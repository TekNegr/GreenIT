<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\DpeCalculator;

class DpeCalculatorTest extends TestCase
{
    public function test_calcul_moyenne_dpe_gpe()
    {
        $batiments = [
            ['dpe' => 180, 'gpe' => 35],
            ['dpe' => 220, 'gpe' => 50],
            ['dpe' => 200, 'gpe' => 45],
        ];

        $resultats = DpeCalculator::moyenne($batiments);

        $this->assertEquals(200, $resultats['dpe_moyen']);
        $this->assertEquals(43.333333333333, $resultats['gpe_moyen']);
    }

    public function test_aucun_batiment()
    {
        $resultats = DpeCalculator::moyenne([]);

        $this->assertEquals(0, $resultats['dpe_moyen']);
        $this->assertEquals(0, $resultats['gpe_moyen']);
    }
}
