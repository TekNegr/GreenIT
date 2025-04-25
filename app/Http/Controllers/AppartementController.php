<?php

namespace App\Http\Controllers;

use App\Jobs\ImportDpeData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Batiment;
use App\Models\Appartement;

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

        Log::info('Received bbox for fetching appartements: ' . $bbox);

        // Dispatch the ImportDpeData job with bbox array
        ImportDpeData::dispatch($bboxArray);

        return response()->json(['message' => 'ImportDpeData job dispatched', 'bbox' => $bboxArray]);
    }

    function addLogementToDB($json)
    {
        // Extraire l'adresse depuis le JSON
        $adresse = $json['address'];

        // Vérifier si le bâtiment existe déjà
        if (!Batiment::where('adresse', $adresse)->exists()) {
            // Créer un nouveau bâtiment si l'adresse n'existe pas
            $batiment = Batiment::create([
                'id',
                'adresse' => $adresse,
                'latitude' => $json['latitude'],
                'longitude' => $json['longitude'],
                'type' => $json['building_type'],
                'avg_dpe_grade',
                'avg_ges_grade',
                'avg_energy_consumption',
                'avg_carbon_emission',
                'apartments_count',
            ]);
        } else {
            // Récupérer le bâtiment existant
            $batiment = Batiment::where('adresse', $adresse)->first();
        }

        // Vérifier si l'appartement existe déjà avec le dpe_number
        if (Appartement::where('dpe_number', $json['dpe_number'])->exists()) {
            return 0; // L'appartement existe déjà, ne rien faire
        }

        // Créer un nouvel appartement
        Appartement::create([
            
            'dpe_number' => $json['dpe_number'],
            'energy_class' => $json['energy_class'],
            'construction_year' => $json['construction_year'],
            'surface_area' => $json['surface_area'],
            'energy_consumption' => $json['energy_consumption'],
            'ges_estimation' => $json['ges_estimation'],
            'longitude'  => $json['longitude'],
            'latitude' => $json['latitude'],
            'address' => $json['geo_adresse'],
            'year_built' => $json['annee_construction'],
            'ges_grade' => $json['classe_estimationion_ges'],
            'carbon_emission' => $json['carbon_emission'],
            'batiment_id' => $json['batiment_id'],
            'building_id' => $batiment->id,
            
            
        ]);

        return 1; // Succès
    }
}