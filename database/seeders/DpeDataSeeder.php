<?php

namespace Database\Seeders;

use App\Models\Departement;
use App\Models\TypeBatiment;
use App\Models\ZonesHiver;
use App\Models\ZonesEte;
use Illuminate\Database\Seeder;

class DpeDataSeeder extends Seeder
{
    public function run()
    {
        // Seed winter zones
        $winterZones = [
            ['code' => 'H1', 't_ext_moyen' => 6, 'peta_cw' => 0.9, 'dh14' => 14, 'prs1' => 1.1],
            ['code' => 'H2', 't_ext_moyen' => 5, 'peta_cw' => 0.8, 'dh14' => 16, 'prs1' => 1.2],
            ['code' => 'H3', 't_ext_moyen' => 4, 'peta_cw' => 0.7, 'dh14' => 18, 'prs1' => 1.3]
        ];
        ZonesHiver::insert($winterZones);

        // Seed summer zones
        $summerZones = [
            ['code' => 'E1', 'sclim_inf_150' => 0.5, 'sclim_sup_150' => 0.6, 'rclim_autres_etages' => 0.7, 'rclim_dernier_etage' => 0.8],
            ['code' => 'E2', 'sclim_inf_150' => 0.6, 'sclim_sup_150' => 0.7, 'rclim_autres_etages' => 0.8, 'rclim_dernier_etage' => 0.9],
            ['code' => 'E3', 'sclim_inf_150' => 0.7, 'sclim_sup_150' => 0.8, 'rclim_autres_etages' => 0.9, 'rclim_dernier_etage' => 1.0]
        ];
        ZonesEte::insert($summerZones);

        // Seed building types
        $buildingTypes = [
            ['code' => 'MAI', 'libelle' => 'Maison individuelle', 'description' => 'Maison individuelle', 'ordre' => 1],
            ['code' => 'APP', 'libelle' => 'Appartement', 'description' => 'Appartement en immeuble collectif', 'ordre' => 2],
            ['code' => 'COM', 'libelle' => 'Commerce', 'description' => 'Bâtiment commercial', 'ordre' => 3]
        ];
        TypeBatiment::insert($buildingTypes);

        // Seed sample departments
        $departements = [
            ['code' => '75', 'departement' => 'Paris', 'zone_hiver_id' => 1, 'zone_ete_id' => 1],
            ['code' => '13', 'departement' => 'Bouches-du-Rhône', 'zone_hiver_id' => 2, 'zone_ete_id' => 2],
            ['code' => '69', 'departement' => 'Rhône', 'zone_hiver_id' => 3, 'zone_ete_id' => 3]
        ];
        Departement::insert($departements);
    }
}
