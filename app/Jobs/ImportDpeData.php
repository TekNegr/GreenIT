<?php

namespace App\Jobs;

use App\Http\Controllers\GeoTileController;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\AppartementController;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ImportDpeData implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $bbox;

    public function __construct(array $bbox)
    {
        Log::info("DPEJob - Initializing DPE import job with BBox: " . implode(',', $bbox));
        $this->bbox = $bbox;
    }

    public function handle()
    {
        Log::info("DPEJob - Starting DPE import job for BBox: " . implode(',', $this->bbox));

        // Step 1: Check GeoTile cache directly
        Log::info("step 1 of importDpeData");
        Log::info("Checking GeoTile cache for BBox: " . implode(',', $this->bbox));
        $tileKey = implode('_', $this->bbox);
        $geoTile = \App\Models\GeoTile::where('tile_key', $tileKey)->first();

        if ($geoTile && !$geoTile->isExpired(60)) {
            Log::info("GeoTile is cached. Skipping API call and database insertion.");
            return;
        }

        // Step 2: Call the API directly with bbox
        Log::info("step 2 of importDpeData");
        Log::info("Fetching data from API for BBox: " . implode(',', $this->bbox));
        $apiController = new ApiController();
        $request = new \Illuminate\Http\Request();
        $request->merge(['bbox' => implode(',', $this->bbox)]);
        $apiResponse = $apiController->fetchData($request);

        if (!$apiResponse || empty($apiResponse)) {
            Log::info("No data returned from the API for BBox: " . implode(',', $this->bbox));
            return;
        }

        // Step 3: Feed Data into the Database
        Log::info("step 3 of importDpeData");
        Log::info("Feeding data into the database for BBox: " . implode(',', $this->bbox));
        if (!isset($apiResponse['results']) || !is_array($apiResponse['results'])) {
            Log::info("Invalid API response format for BBox: " . implode(',', $this->bbox));
            return;
        }
        if (empty($apiResponse['results'])) {
            Log::info("No results found in the API response for BBox: " . implode(',', $this->bbox));
            return;
        }
        // Extract apartments data using ApiController's extractApartments method
        $apiController = new \App\Http\Controllers\ApiController();
        $apartments = $apiController->extractApartments($apiResponse);

        // Loop through the extracted apartments and insert them into the database
        $appartementController = new AppartementController();
        foreach ($apartments as $appartementData) {
            try {
                $appartementController->addLogementToDB($appartementData);
                Log::info("Appartement addition to the database successful: " . json_encode($appartementData));
            } catch (\Exception $e) {
                Log::error("Error adding appartement to the database: " . $e->getMessage());
            }
        }

        Log::info("Completed DPE import job for BBox: " . implode(',', $this->bbox));
    }
}