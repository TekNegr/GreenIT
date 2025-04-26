<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Jobs\ImportDpeData;
use App\Http\Controllers\DPEController;
use App\Http\Controllers\AppartementController;
use Livewire\Livewire;

class GeneralController extends Controller
{

    protected $mode = 'dpe-apts'; // Default mode
    public function handleBBoxUpdate(Request $request)
    {
        $bbox = $request->input('bbox');

        if (!$bbox || count($bbox) !== 4) {
            return response()->json(['error' => 'Invalid bbox'], 400);
        }

        // Dispatch ImportDpeData job with bbox
        ImportDpeData::dispatch($bbox);

        // Call DPEController method to create GeoJSON points for color rendering
        $dpeController = app(DPEController::class);
        $geojson = $dpeController->createGeoJsonForBBox($bbox);

        
        \Livewire\Livewire::emitTo('sidebar', 'updateBBoxStatus', $bbox, false);

        return response()->json([
            'message' => 'BBox processed',
            'geojson' => $geojson,
            'appartements' => $appartements,
        ]);
    }
}
