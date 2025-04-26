<?php

namespace App\Http\Controllers;

use App\Jobs\ImportDpeData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;

class ApiController extends Controller
{
    public function testApiPage()
    {
        $logPath = storage_path('logs/laravel.log');
        $logs = [];

        if (File::exists($logPath)) {
            $lines = file($logPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            $logs = array_slice($lines, -100);
        }

        return view('TestAPI', ['logs' => $logs]);
    }

    
    public function fetchData(Request $request)
    {
        Log::info('APIController - Received request to fetch data with bbox: ' . $request->input('bbox'));
        $bbox = $request->input('bbox');
        $bboxArray = explode(',', $bbox);
        if (count($bboxArray) !== 4) {
            return response()->json(['error' => 'Bounding box (bbox) must have 4 comma-separated values'], 400);
        }
        $lonMin = $bboxArray[0];
        $latMin = $bboxArray[1];
        $lonMax = $bboxArray[2];
        $latMax = $bboxArray[3];

        $response = Http::withOptions(['verify' => false])
            ->get('https://data.ademe.fr/data-fair/api/v1/datasets/dpe-france/lines', [
                'bbox' => " $lonMin,$latMin,$lonMax,$latMax",
                'rows' => 500,
            ]);
        Log::info('APIController - API response status: ' . $response->status());
        if ($response->failed()) {
            Log::error('APIController - API request failed: ' . $response->body());
            return response()->json(['error' => 'Failed to fetch data from API'], 500);
        }
        // Log::info('APIController - API response data: ' . $response->body());
        return $response->json();
    }

    public function extractApartments(array $data): array
    {
        $apartments = [];

        if (isset($data['results']) && is_array($data['results'])) {
            foreach ($data['results'] as $result) {
                $apartments[] = [
                    'dpe_code' => $result['numero_dpe'] ?? null,
                    'latitude' => $result['latitude'] ?? null,
                    'longitude' => $result['longitude'] ?? null,
                    'address' => $result['geo_adresse'] ?? null,
                    'surface_area' => $result['surface_thermique_lot'] ?? null,
                    'year_built' => $result['annee_construction'] ?? null,
                    'dpe_grade' => $result['classe_consommation_energie'] ?? null,
                    'ges_grade' => $result['classe_estimationion_ges'] ?? null,
                    'energy_consumption' => $result['consommation_energie'] ?? null,
                    'carbon_emission' => $result['estimation_ges'] ?? null,
                    'building_type' => $result['tr002_type_batiment_description'] ?? null,
                ];
            }
        }

        return $apartments;
    }

    
}
