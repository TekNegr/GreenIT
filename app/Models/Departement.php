<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Departement extends Model
{
    protected $fillable = [
        'code',
        'departement',
        'zone_hiver_id',
        'zone_ete_id',
        'altmin',
        'altmax'
    ];

    public function batiments(): HasMany
    {
        return $this->hasMany(Batiment::class, 'tv016_departement_id');
    }
}
