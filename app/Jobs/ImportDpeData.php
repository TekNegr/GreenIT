<?php

namespace App\Jobs;

use App\Models\Batiment;
use App\Models\GeoTile;
use App\Models\Appartement;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;


class ImportDpeData implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $bbox;
    protected $apiUri;
    protected $baseUri = 'https://data.ademe.fr/data-fair/api/v1/datasets/dpe-france/lines';
    protected $batchSize = 500;

    public function __construct(array $bbox, string $apiUri)
    {
        $this->bbox = $bbox;
        $this->apiUri = $apiUri;
    }

    public function handle()
    {
        Log::info("Starting DPE import job for BBox: " . implode(',', $this->bbox));

        // Divide BBox into GeoTiles (for simplicity, assume 1 tile = BBox here)
        $tileKey = $this->generateTileKey($this->bbox);

        // Check if tile is cached and not expired
        $geoTile = GeoTile::where('tile_key', $tileKey)->first();
        if ($geoTile && !$geoTile->isExpired(60)) {
            Log::info("GeoTile {$tileKey} is already cached and fresh. Skipping fetch.");
            // Emit event to Livewire sidebar for logging
            \Livewire\Livewire::emit('logMessage', "GeoTile {$tileKey} is cached. Skipping fetch.");
            // Emit event to update BBox status in sidebar
            Log::info('Emitting updateBBoxStatus with bbox: ' . json_encode($this->bbox) . ' cached: true');
            \Livewire\Livewire::emit('updateBBoxStatus', $this->bbox, true);
            return;
        }

        // Fetch appartements from API in batches
        $offset = 0;
        $totalFetched = 0;
        do {
            $url = $this->buildApiUrl($this->bbox, $this->batchSize, $offset);
            Log::info("Fetching data from API: $url");
            $response = Http::get($url);

            if (!$response->ok()) {
                Log::error("API request failed with status: " . $response->status());
                break;
            }

            $data = $response->json();
            $appartements = $data['records'] ?? [];

            if (empty($appartements)) {
                Log::info("No more appartements to fetch.");
                break;
            }

            foreach ($appartements as $appartementData) {
                try {
                    // Call AddLogementToDb function (assumed to be a static method in Appartement model)
                    Appartement::AddLogementToDb($appartementData);
                    // Emit event to Livewire sidebar for logging
                    \Livewire\Livewire::emit('logMessage', 'Appartement added: ' . json_encode($appartementData));
                } catch (\Exception $e) {
                    Log::error("Error adding logement: " . $e->getMessage());
                }
            }

            $fetchedCount = count($appartements);
            $totalFetched += $fetchedCount;
            $offset += $fetchedCount;

            // Respect API rate limit: 600 requests/min => 10 requests/sec, so sleep 0.1 sec
            usleep(100000);

        } while ($fetchedCount === $this->batchSize);

        // Update or create GeoTile cache record
        if (!$geoTile) {
            $geoTile = new GeoTile();
            $geoTile->tile_key = $tileKey;
            $geoTile->bbox = $this->bbox;
        }
        $geoTile->cached_at = now();
        $geoTile->save();

        // Emit event to update BBox status in sidebar
        \Livewire\Livewire::emit('updateBBoxStatus', $this->bbox, false);

        Log::info("Completed DPE import job. Total appartements fetched: $totalFetched");
    }

    protected function generateTileKey(array $bbox)
    {
        return implode('_', $bbox);
    }

    protected function buildApiUrl(array $bbox, int $rows, int $offset)
    {
        // API URI template: https://data.ademe.fr/data-fair/api/v1/datasets/dpe-france/lines?bbox=$lonMin,$latMin,$lonMax,l$atMax&rows=$MaxRows
        // Replace placeholders with actual values
        $lonMin = $bbox[0];
        $latMin = $bbox[1];
        $lonMax = $bbox[2];
        $latMax = $bbox[3];

        $url = str_replace(
            ['$lonMin', '$latMin', '$lonMax', '$latMax', '$MaxRows'],
            [$lonMin, $latMin, $lonMax, $latMax, $rows],
            $this->apiUri
        );

        // Add offset parameter if API supports pagination by offset (not specified, so omitted here)
        return $url;
    }
}
