<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Batiment extends Model
{
    protected $fillable = [
        'numero_dpe',
        'tr002_type_batiment_id',
        'partie_batiment',
        'consommation_energie',
        'classe_consommation_energie',
        'estimation_ges',
        'classe_estimation_ges',
        'annee_construction',
        'surface_habitable',
        'tv016_departement_id',
        'commune',
        'code_postal',
        'geometry'
    ];

    public function scopeWithCoordinates($query)
    {
        return $query->whereNotNull('geometry')
            ->whereRaw("ST_X(geometry) != 0 AND ST_Y(geometry) != 0");
    }

    public function scopeWithinBoundingBox($query, $minLng, $minLat, $maxLng, $maxLat)
    {
        return $query->whereRaw("MBRContains(
            ST_GeomFromText('Polygon((
                $minLng $minLat,
                $maxLng $minLat,
                $maxLng $maxLat,
                $minLng $maxLat,
                $minLng $minLat
            ))'),
            geometry
        )");
    }

    protected $appends = ['dpe_color', 'is_apartment'];

    public function typeBatiment(): BelongsTo
    {
        return $this->belongsTo(TypeBatiment::class, 'tr002_type_batiment_id');
    }

    public function departement(): BelongsTo
    {
        return $this->belongsTo(Departement::class, 'tv016_departement_id');
    }

    public function getDpeColorAttribute(): string
    {
        return match($this->classe_consommation_energie) {
            'A' => 'bg-green-100 text-green-800',
            'B' => 'bg-green-200 text-green-800',
            'C' => 'bg-yellow-100 text-yellow-800',
            'D' => 'bg-yellow-200 text-yellow-800',
            'E' => 'bg-orange-100 text-orange-800',
            'F' => 'bg-red-100 text-red-800',
            'G' => 'bg-red-200 text-red-800',
            default => 'bg-gray-100 text-gray-800'
        };
    }

    public function getIsApartmentAttribute(): bool
    {
        return !empty($this->partie_batiment);
    }
}
