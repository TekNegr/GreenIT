<?php

namespace App\Services;

class DpeCalculator
{
    public static function moyenne(array $batiments): array
    {
        $totalDpe = 0;
        $totalGpe = 0;
        $count = count($batiments);

        foreach ($batiments as $b) {
            $totalDpe += $b['dpe'] ?? 0;
            $totalGpe += $b['gpe'] ?? 0;
        }

        return [
            'dpe_moyen' => $count ? $totalDpe / $count : 0,
            'gpe_moyen' => $count ? $totalGpe / $count : 0,
        ];
    }
}
