<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Building; 

class GeoController extends Controller
{
    public function index(Request $request)
    {
        $query = Building::query(); 

        //  Filtre type : "building" ou "appartement"
        if ($request->has('type') && in_array($request->input('type'), ['building', 'appartement'])) {
            $query->where('type', $request->input('type'));
        }

        //  Filtre DPE : "A", "B", ..., "G"
        if ($request->has('dpe')) {
            $query->where('dpe_class', $request->input('dpe')); // ou 'dpe_note' selon ta base
        }

        $results = $query->get();

        // Retour GeoJSON simplifié
        $geojson = [
            'type' => 'FeatureCollection',
            'features' => $results->map(function ($item) {
                return [
                    'type' => 'Feature',
                    'geometry' => json_decode($item->geometry), // ou adapte selon ton champ
                    'properties' => [
                        'id' => $item->id,
                        'name' => $item->name ?? '',
                        'type' => $item->type,
                        'dpe' => $item->dpe_class ?? null,
                        'gpe' => $item->gpe_class ?? null,
                    ],
                ];
            }),
        ];

        return response()->json($geojson);
    }
}
