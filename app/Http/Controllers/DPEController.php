<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\DpeCalculator;
use App\Models\Batiment;
use App\Models\Appartement;
use Illuminate\Support\Facades\Log;


class DPEController extends Controller
{
    public function moyenne(Request $request)
    {
        $donnees = $request->input('batiments', []);
        $resultats = DpeCalculator::moyenne($donnees);

        return response()->json($resultats);
    }

    public function createGeoJsonForBBox()
    {
        // This method should create GeoJSON points for color rendering
        // based on the bounding box provided in the request.
        // Implement your logic here to generate the GeoJSON.
        
        return response()->json([
            'type' => 'FeatureCollection',
            'features' => []
        ]);
    }


    public function getBuildingsGeoJson(Request $request)
    {
        return $this->generateGeoJson('building', $request);
    }

    public function getApartmentsGeoJson(Request $request)
    {
        return $this->generateGeoJson('apartment', $request);
    }

    private function generateGeoJson($type, Request $request)
    {
        try {
            $query = $type === 'building' ? Batiment::query() : Appartement::query();

            if ($request->has('bbox')) {
                $bbox = explode(',', $request->input('bbox'));
                if (count($bbox) === 4) {
                    $query->whereBetween('latitude', [$bbox[1], $bbox[3]])
                        ->whereBetween('longitude', [$bbox[0], $bbox[2]]);
                }
            }

            $limit = $request->input('limit', 100);
            $data = $query->limit($limit)->get();

            $features = $data->map(function ($item) use ($type) {
                return [
                    'type' => 'Feature',
                    'geometry' => [
                        'type' => 'Point',
                        'coordinates' => [(float)$item->longitude, (float)$item->latitude],
                    ],
                    'properties' => [
                        'id' => $item->id,
                        'dpe_class' => $type === 'building' ? $item->avg_dpe_grade : $item->dpe_grade,
                        'address' => $item->address_text ?? $item->address,
                    ],
                ];
            });

            return response()->json([
                'type' => 'FeatureCollection',
                'features' => $features,
            ]);
        } catch (\Exception $e) {
            Log::error('GeoJSON Error: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to load data',
                'details' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }
}
