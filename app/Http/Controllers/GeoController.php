<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Appartement;
use App\Models\Batiment;
use App\Services\GeoJsonBuilder; //  import du builder

class GeoController extends Controller
{
    public function index(Request $request)
    {
        $type = $request->input('type', 'building'); // défaut = building

        // Sélection dynamique du modèle
        $query = match ($type) {
            'appartement' => Appartement::query(),
            default       => Batiment::query(),
        };

        // Filtre DPE si présent
        if ($request->has('dpe')) {
            $query->where('dpe_class', $request->input('dpe'));
        }

        $results = $query->get();

        // Construction GeoJSON déléguée au builder
        $geojson = GeoJsonBuilder::fromCollection($results);

        return response()->json($geojson);
    }
}
