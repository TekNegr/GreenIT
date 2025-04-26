<?php

namespace App\Services;

class BBoxHelper
{
    public static function toArray(string $bbox): array
    {
        return array_map('floatval', explode(',', $bbox));
    }

    public static function toString(array $bbox): string
    {
        return implode(',', $bbox);
    }


}