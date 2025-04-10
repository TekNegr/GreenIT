<?php

namespace App\Jobs;

use App\Models\Batiment;
use App\Models\Departement;
use App\Models\TypeBatiment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class ImportDpeData implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $csvPath;

    public function __construct($csvPath)
    {
        $this->csvPath = $csvPath;
    }

    public function handle()
    {
        Log::info("Starting DPE import job for file: " . $this->csvPath);
        
        if (!file_exists($this->csvPath)) {
            Log::error("File not found: " . $this->csvPath);
            return;
        }

        $handle = fopen($this->csvPath, 'r');
        $header = fgetcsv($handle);
        $batch = [];
        $batchSize = 1000;
        $processed = 0;

        while (($row = fgetcsv($handle)) !== false) {
            try {
                $data = array_combine($header, $row);

                // Skip empty or invalid rows
                if (empty(array_filter($row)) || !is_numeric(trim($row[0]))) {
                    continue;
                }

                // Process data with same validation as controller
                $data = $this->processRow($data);
                $batch[] = $data;

                // Insert batch when size is reached
                if (count($batch) >= $batchSize) {
                    $this->insertBatch($batch);
                    $batch = [];
                }

                $processed++;
                if ($processed % 10000 === 0) {
                    Log::info("Processed $processed records");
                }
            } catch (\Exception $e) {
                Log::error("Error processing row: " . $e->getMessage());
            }
        }

        // Insert remaining records
        if (!empty($batch)) {
            $this->insertBatch($batch);
        }

        fclose($handle);
        Log::info("Completed DPE import job. Processed $processed records");
    }

    protected function processRow($data)
    {
        // Apply same validation/fallbacks as controller
        if (empty($data['numero_dpe'])) {
            $data['numero_dpe'] = 'TEMP_' . uniqid();
        }

        if (empty($data['departement'])) {
            $data['departement'] = '75';
        }

        if (empty($data['type_batiment'])) {
            $data['type_batiment'] = 'TR002_002';
        }

        // Calculate or validate energy consumption class
        $validClasses = ['A', 'B', 'C', 'D', 'E', 'F', 'G'];
        $data['classe_consommation_energie'] = strtoupper($data['classe_consommation_energie'] ?? '');
        
        // If class is missing or invalid, calculate from consommation_energie
        if (empty($data['classe_consommation_energie']) || !in_array($data['classe_consommation_energie'], $validClasses)) {
            $consommation = $data['consommation_energie'] ?? 0;
            
            if ( $consommation <= 50) $data['classe_consommation_energie'] = 'A';
            elseif ($consommation <= 90) $data['classe_consommation_energie'] = 'B';
            elseif ($consommation <= 150) $data['classe_consommation_energie'] = 'C';
            elseif ($consommation <= 230) $data['classe_consommation_energie'] = 'D';
            elseif ($consommation <= 330) $data['classe_consommation_energie'] = 'E';
            elseif ($consommation <= 450) $data['classe_consommation_energie'] = 'F';
            else $data['classe_consommation_energie'] = 'G';
            
            // Log::info("Calculated energy class {$data['classe_consommation_energie']} for DPE {$data['numero_dpe']} based on consommation: {$consommation}");
        }

        // Set default values for new address fields
        $data['nom_rue'] = $data['nom_rue'] ?? '';
        $data['numero_rue'] = $data['numero_rue'] ?? '';
        $data['batiment'] = $data['batiment'] ?? '';

        return $data;
    }

    protected function insertBatch($batch)
    {
        try {
            $typeBatiments = [];
            $departements = [];
            $batiments = [];
            $insertedCount = 0;

            Log::info("Starting batch insert with ".count($batch)." records");

            foreach ($batch as $data) {
                // Get or create type batiment
                $typeBatimentCode = $data['type_batiment'];
                if (!isset($typeBatiments[$typeBatimentCode])) {
                    $typeBatiments[$typeBatimentCode] = TypeBatiment::firstOrCreate(
                        ['code' => $typeBatimentCode],
                        ['libelle' => $typeBatimentCode, 'ordre' => 99]
                    );
                }

                // Use Paris departement (code 75)
                $departementCode = '75';
                if (!isset($departements[$departementCode])) {
                    $departements[$departementCode] = Departement::firstOrCreate(
                        ['code' => $departementCode]
                    );
                }

                try {
                    // Prepare batiment data with all fields
                    $batimentData = [
                        'numero_dpe' => $data['numero_dpe'],
                    'tr002_type_batiment_id' => $typeBatiments[$typeBatimentCode]->id,
                    'partie_batiment' => $data['partie_batiment'] ?? null,
                    'consommation_energie' => $data['consommation_energie'] ?? 0,
                        'classe_consommation_energie' => isset($data['classe_consommation_energie']) 
                            ? strtoupper($data['classe_consommation_energie']) 
                            : 'G',
                    'estimation_ges' => $data['estimation_ges'] ?? 0,
                    'classe_estimation_ges' => strtoupper($data['classe_estimation_ges'] ?? 'G'),
                    'annee_construction' => $data['annee_construction'] ?? 1900,
                    'surface_habitable' => $data['surface_habitable'] ?? 0,
                    'tv016_departement_id' => $departementCode,
                    'commune' => $data['commune'] ?? '',
                    'code_postal' => $data['code_postal'] ?? '',
                    'nom_rue' => $data['nom_rue'] ?? '',
                    'numero_rue' => $data['numero_rue'] ?? '',
                    'batiment' => $data['batiment'] ?? '',
                        'created_at' => now(),
                        'updated_at' => now()
                    ];

                    // Only include fields that exist in the database
                    $columns = Schema::getColumnListing('batiments');
                    $filteredData = array_intersect_key($batimentData, array_flip($columns));
                    
                    $batiments[] = $filteredData;
                } catch (\Exception $e) {
                    Log::error("Error preparing batiment data: " . $e->getMessage());
                    continue;
                }
            }

            // Bulk insert batiments
            $result = Batiment::insert($batiments);
            $insertedCount += count($batiments);
            
            Log::info("Successfully inserted {$insertedCount} records in this batch");
            return $insertedCount;
            
        } catch (\Exception $e) {
            Log::error("Error inserting batch: ".$e->getMessage());
            Log::error("Failed batch data: ".json_encode($batiments));
            return 0;
        }
    }

    public function failed(\Exception $exception)
    {
        Log::error("DPE Import Job Failed: ".$exception->getMessage());
    }
}
