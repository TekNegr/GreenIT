<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Jobs\ImportDpeData;
use App\Http\Controllers\DPEController;
use App\Http\Controllers\AppartementController;
use Livewire\Livewire;
use App\Http\Livewire\Sidebar;
use App\Livewire\Sidebar as LivewireSidebar;
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
        Log::info('GeneralController - Dispatched ImportDpeData job with bbox: ' . implode(',', $bbox) . 'raw' . json_encode($bbox));

        // Call DPEController method to create GeoJSON points for color rendering
        $dpeController = app(DPEController::class);
        $geojson = $dpeController->createGeoJsonForBBox($bbox);

        // Call AppartementController method to fetch appartements within bbox
        // \Livewire\Livewire::dispatch('updateBBoxStatus', ['bbox' => $bbox], false)->to(LivewireSidebar::class);

        return response()->json([
            'message' => 'BBox processed',
            'geojson' => $geojson,
            // 'appartements' => $appartements,
        ]);
    }
}
