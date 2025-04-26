<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\GeoTile;
use App\Jobs\ImportDpeData;

class GeotileController extends Controller
{
    public function checkGeoTile(Request $request)
    {
        $bbox = $request->input('bbox');

        if (!$bbox) {
            return response()->json(['error' => 'Bounding box (bbox) parameter is required'], 400);
        }

        $bboxArray = explode(',', $bbox);
        if (count($bboxArray) !== 4) {
            return response()->json(['error' => 'Bounding box (bbox) must have 4 comma-separated values'], 400);
        }

        $tileKey = implode('_', $bboxArray);

        // Check if GeoTile exists and is fresh
        $geoTile = GeoTile::where('tile_key', $tileKey)->first();
        if ($geoTile && !$geoTile->isExpired(60)) {
            return response()->json(['message' => 'GeoTile is cached and fresh', 'cached' => true]);
        }

        // Dispatch ImportDpeData job if not cached
        ImportDpeData::dispatch($bboxArray, 'https://data.ademe.fr/data-fair/api/v1/datasets/dpe-france/lines');

        return response()->json(['message' => 'GeoTile is not cached. ImportDpeData job dispatched.', 'cached' => false]);
    }
}
