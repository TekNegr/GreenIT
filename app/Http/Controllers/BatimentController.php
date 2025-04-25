<?php

namespace App\Http\Controllers;

use App\Jobs\ImportDpeData;
use App\Models\Batiment;
use App\Models\Departement;
use App\Models\TypeBatiment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

        
class BatimentController extends Controller
{
    public function importDpe()
    {
        $csvPath = storage_path('geo_dep_75.csv');
        Log::info("Dispatching DPE import job for file: " . $csvPath);
        
        if (!file_exists($csvPath)) {
            return back()->withErrors(['File not found at: ' . $csvPath]);
        }

        ImportDpeData::dispatch($csvPath);

        return back()->with([
            'success' => "DPE import job has been queued. The import will process in the background."
        ]);
    }

    /**
     * API endpoint to receive BBox and dispatch ImportDpeData job.
     */
    public function fetchAppartementsByBBox(\Illuminate\Http\Request $request)
    {
        $validated = $request->validate([
            'bbox' => 'required|string', // expecting "lonMin,latMin,lonMax,latMax"
        ]);

        $bboxString = $validated['bbox'];
        $bboxParts = explode(',', $bboxString);

        if (count($bboxParts) !== 4) {
            return response()->json(['error' => 'Invalid bbox format'], 400);
        }

        $bbox = array_map('floatval', $bboxParts);

        $apiUri = 'https://data.ademe.fr/data-fair/api/v1/datasets/dpe-france/lines?bbox=$lonMin,$latMin,$lonMax,$latMax&rows=$MaxRows';

        \App\Jobs\ImportDpeData::dispatch($bbox, $apiUri);

        return response()->json(['message' => 'ImportDpeData job dispatched for bbox: ' . $bboxString]);
    }
    

    public function showDpeData()
    {
        $batiments = Batiment::with(['typeBatiment', 'departement'])
            ->orderBy('classe_consommation_energie')
            ->paginate(20);

        return view('dpe.index', compact('batiments'));
    }

    public function getBuildingsGeoJson(Request $request)
    {
        try {
            $query = Batiment::selectRaw('
                id,
                avg_dpe_grade as classe_consommation_energie,
                address_text as numero_rue,
                "" as nom_rue,
                "" as code_postal,
                "" as commune,
                latitude as latitude,
                longitude as longitude
            ')
            ->whereNotNull('latitude')
            ->whereNotNull('longitude');
    
            if ($request->has('bbox')) {
                $bbox = explode(',', $request->input('bbox'));
                if (count($bbox) == 4) {
                    $query->whereRaw("latitude BETWEEN ? AND ? AND longitude BETWEEN ? AND ?", [
                        $bbox[1], $bbox[3], $bbox[0], $bbox[2]
                    ]);
                }
            }
    
            $limit = $request->input('limit', 100); // default limit
            $buildings = $query->limit($limit)->get();
    
            $features = $buildings->map(function ($building) {
                return [
                    'type' => 'Feature',
                    'geometry' => [
                        'type' => 'Point',
                        'coordinates' => [(float)$building->longitude, (float)$building->latitude],
                    ],
                    'properties' => [
                        'id' => $building->id,
                        'dpe_class' => $building->classe_consommation_energie,
                        'address' => trim("{$building->numero_rue} {$building->nom_rue}, {$building->code_postal} {$building->commune}")
                    ]
                ];
            });
    
            return response()->json([
                'type' => 'FeatureCollection',
                'features' => $features,
            ]);
    
        } catch (\Exception $e) {
            Log::error('GeoJSON Error: '.$e->getMessage());
            return response()->json([
                'error' => 'Failed to load building data',
                'details' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }
}
