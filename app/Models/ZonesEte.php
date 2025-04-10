<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ZonesEte extends Model
{
    protected $table = 'zones_ete';

    protected $fillable = [
        'code',
        'sclim_inf_150',
        'sclim_sup_150',
        'rclim_autres_etages',
        'rclim_dernier_etage'
    ];

    public function departements(): HasMany
    {
        return $this->hasMany(Departement::class, 'zone_ete_id');
    }
}
