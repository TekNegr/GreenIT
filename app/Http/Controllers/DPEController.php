<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DPEController extends Controller
{
    public function moyenne(Request $request)
    {
        $donnees = $request->input('batiments', []);
        $resultats = DpeCalculator::moyenne($donnees);

        return response()->json($resultats);
    }
}
