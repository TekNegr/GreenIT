<?php

namespace App\Console\Commands;

use App\Models\Batiment;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GeocodeBatiments extends Command
{
    protected $signature = 'batiments:geocode {--limit=100}';
    protected $description = 'Geocode batiment addresses and store coordinates';

    public function handle()
    {
        $limit = (int)$this->option('limit');
        $count = 0;
        $failed = 0;
        $processed = 0;
        $batchSize = 50;
        $delayBetweenRequests = 0.5; // seconds

        $query = Batiment::where(function($q) {
                $q->whereRaw("ST_Equals(geometry, ST_GeomFromText('POINT(0 0)', 4326))")
                  ->orWhereNull('geometry');
            })
            ->limit($limit);

        $total = $query->count();
        $this->info("Found {$total} batiments needing geocoding");
        
        if ($total === 0) {
            $sample = DB::selectOne("SELECT id, CONCAT(numero_rue, ' ', nom_rue, ' ', code_postal, ' ', commune) as address FROM batiments LIMIT 1");
            $this->error("Sample batiment address format: " . ($sample->address ?? 'No records found'));
            return 1;
        }

        $progressBar = $this->output->createProgressBar($total);
        $progressBar->start();

        $query->chunk($batchSize, function ($batiments) use (&$count, &$failed, &$processed, $progressBar, $delayBetweenRequests, $total) {
            foreach ($batiments as $batiment) {
                $processed++;
                $address = implode(' ', array_filter([
                    $batiment->numero_rue,
                    $batiment->nom_rue,
                    $batiment->code_postal,
                    $batiment->commune,
                    'France'
                ]));

                $this->line("Processing {$processed} of {$total}: {$address}");

                $maxRetries = 3;
                $retryCount = 0;
                $success = false;

                while ($retryCount < $maxRetries && !$success) {
                    try {
                        $response = Http::withOptions([
                            'verify' => false,
                            'timeout' => 5
                        ])->get('https://api-adresse.data.gouv.fr/search/', [
                            'q' => $address,
                            'limit' => 1
                        ]);

                        if ($response->successful() && !empty($response->json()['features'])) {
                            $coordinates = $response->json()['features'][0]['geometry']['coordinates'];
                            
                            DB::table('batiments')
                                ->where('id', $batiment->id)
                                ->update([
                                    'geometry' => DB::raw("ST_GeomFromText('POINT({$coordinates[0]} {$coordinates[1]})', 4326)")
                                ]);

                            $this->info("Geocoded: {$coordinates[0]}, {$coordinates[1]}");
                            $count++;
                            $success = true;
                        } else {
                            $this->warn("No results for address: {$address}");
                            $failed++;
                            break;
                        }
                    } catch (\Exception $e) {
                        $retryCount++;
                        $this->warn("Attempt {$retryCount}/{$maxRetries} failed: " . $e->getMessage());
                        if ($retryCount < $maxRetries) {
                            sleep($delayBetweenRequests);
                        } else {
                            $this->error("Failed after {$maxRetries} attempts: {$address}");
                            Log::error("Geocoding failed after retries", [
                                'error' => $e->getMessage(), 
                                'address' => $address,
                                'batiment_id' => $batiment->id
                            ]);
                            $failed++;
                        }
                    }
                }

                $progressBar->advance();
                sleep($delayBetweenRequests);
            }
        });

        $progressBar->finish();
        $this->newLine();
        $this->info("Geocoding completed");
        $this->info("Successfully geocoded: {$count}");
        $this->warn("Failed to geocode: {$failed}");
        $this->info("Total processed: {$processed}");

        return $failed > 0 ? 1 : 0;
    }
}
