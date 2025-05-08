<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Jobs\ImportDpeData;
use App\Services\GeoJsonBuilder;
use App\Models\Batiment;
use Illuminate\Support\Facades\Log;

class GeneralController extends Controller
{
    public $mode = 'dpe';
    public function handleBBoxUpdate(Request $request)
    {
        $bbox = $request->input('bbox');
        $bbox = explode(',', $bbox);
        Log::info('GeneralController - Received bbox: ' . $request->input('bbox'));

        if (!$bbox || count($bbox) !== 4) {
            return response()->json(['error' => 'Invalid bbox'], 400);
        }

        // Dispatch ImportDpeData job with bbox
        ImportDpeData::dispatch($bbox);
        Log::info('GeneralController - Dispatched ImportDpeData job with bbox: ' . implode(',', $bbox) . ' raw ' . json_encode($bbox));

        // Query Batiment models within bbox
        $batiments = Batiment::whereBetween('latitude', [$bbox[1], $bbox[3]])
            ->whereBetween('longitude', [$bbox[0], $bbox[2]])
            ->get();

        // Generate GeoJson using GeoJsonBuilder service
        $geojson = GeoJsonBuilder::fromCollection($batiments);

        return response()->json([
            'message' => 'BBox processed',
            'geojson' => $geojson,
        ]);
    }
}
