<?php

namespace App\Http\Controllers;

use App\Jobs\ImportDpeData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

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

    public function addLogementToDb(Request $request)
    {
        // This function is a placeholder for the actual implementation
        // that would save the appartement data to the database. With it's correct batiment
    }
}
