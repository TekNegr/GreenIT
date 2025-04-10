<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TypeBatiment extends Model
{
    protected $fillable = [
        'code',
        'libelle',
        'description',
        'ordre',
        'est_efface'
    ];

    public function batiments(): HasMany
    {
        return $this->hasMany(Batiment::class, 'tr002_type_batiment_id');
    }
}
