<?php

namespace App\Http\Controllers;

use App\Jobs\ImportDpeData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Batiment;
use App\Models\Appartement;
use App\Models\GeoTile;

class AppartementController extends Controller
{
    public function fetchAppartements(Request $request)
    {
        $bbox = $request->input('bbox');

        if (!$bbox) {
            return response()->json(['error' => 'Bounding box (bbox) parameter is required'], 400);
        }

        $bboxArray = explode(',', $bbox);
        if (count($bboxArray) !== 4) {
            return response()->json(['error' => 'Bounding box (bbox) must have 4 comma-separated values'], 400);
        }

        Log::info('AptController - Received bbox for fetching appartements: ' . $bbox);

        // Dispatch the ImportDpeData job with bbox array
        ImportDpeData::dispatch($bboxArray);

        Log::info('AptController - Dispatched ImportDpeData job with bbox: ' . implode(',', $bboxArray) . 'raw:'. json_encode($bboxArray));
        return response()->json(['message' => 'ImportDpeData job dispatched', 'bbox' => $bboxArray]);
    }

    

    function addLogementToDB($json)
    {
        // Extraire l'adresse depuis le JSON
        $adresse = $json['address'] ?? $json['geo_adresse'] ?? null;

        if (!$adresse) {
            Log::error("addLogementToDB: Missing address in data: " . json_encode($json));
            return 0;
        }

        // Vérifier si le bâtiment existe déjà
        if (!Batiment::where('address_text', $adresse)->exists()) {
            // Créer un nouveau bâtiment si l'adresse n'existe pas
            $batiment = Batiment::create([
                'address_text' => $adresse,
                'latitude' => $json['latitude'] ?? null,
                'longitude' => $json['longitude'] ?? null,
                'type' => $json['building_type'] ?? null,
                'avg_dpe_grade' => null,
                'avg_ges_grade' => null,
                'avg_energy_consumption' => null,
                'avg_carbon_emission' => null,
                'apartments_count' => 0,
            ]);
        } else {
            // Récupérer le bâtiment existant
            $batiment = Batiment::where('address_text', $adresse)->first();
        }

        // Vérifier si l'appartement existe déjà avec le dpe_code
        if (isset($json['dpe_code']) && Appartement::where('dpe_code', $json['dpe_code'])->exists()) {
            Log::info("addLogementToDB: Appartement with dpe_code {$json['dpe_code']} already exists for address: $adresse");
            return 0; // L'appartement existe déjà, ne rien faire
        }

        // Créer un nouvel appartement
        Appartement::create([
            'dpe_code' => $json['dpe_code'] ?? null,
            'latitude' => $json['latitude'] ?? null,
            'longitude' => $json['longitude'] ?? null,
            'address' => $adresse,
            'surface_area' => $json['surface_area'] ?? null,
            'year_built' => $json['year_built'] ?? null,
            'dpe_grade' => $json['dpe_grade'] ?? null,
            'ges_grade' => $json['ges_grade'] ?? null,
            'energy_consumption' => $json['energy_consumption'] ?? null,
            'carbon_emission' => $json['carbon_emission'] ?? null,
            'batiment_id' => $batiment->id,
        ]);

        return 1; // Succès
    }
}
