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
                classe_consommation_energie,
                numero_rue,
                nom_rue,
                code_postal,
                commune,
                ST_X(geometry) AS longitude,
                ST_Y(geometry) AS latitude
            ')
            ->whereNotNull('geometry');
    
            if ($request->has('bbox')) {
                $bbox = explode(',', $request->input('bbox'));
                if (count($bbox) == 4) {
                    $query->whereRaw("ST_Within(geometry, ST_MakeEnvelope(?, ?, ?, ?, 4326))", [
                        $bbox[0], $bbox[1], $bbox[2], $bbox[3]
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
