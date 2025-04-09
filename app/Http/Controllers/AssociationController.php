<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Appartement;
use App\Models\Batiment;
use App\Models\Quartier;
use Illuminate\Http\Request;





class AssociationController extends Controller
{
    public function associerAppartements()
    {
        $appartements = Appartement::whereNull('batiment_id')->get();

        foreach ($appartements as $appartement) {
            $batiment = Batiment::firstOrCreate([
                'rue'         => $appartement->rue,
                'code_postal'=> $appartement->code_postal,
                'ville'      => $appartement->ville,
                'secteur_id' => $appartement->secteur_id,
            ]);

            $appartement->batiment_id = $batiment->id;
            $appartement->save();

            // Association automatique du bâtiment à un quartier
            $quartier = Quartier::firstOrCreate([
                'secteur_id' => $batiment->secteur_id,
                'ville'      => $batiment->ville,
            ], [
                'nom' => 'Quartier auto ' . $batiment->secteur_id // ou autre logique
            ]);

            $batiment->quartier_id = $quartier->id;
            $batiment->save();
        }

        return response()->json(['message' => 'Appartements associés avec succès.']);
    }
}
