<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\DpeCalculator;

class DpeController extends Controller
{
    public function moyenne(Request $request)
    {
        $donnees = $request->input('batiments', []);
        $resultats = DpeCalculator::moyenne($donnees);

        return response()->json($resultats);
    }
}
