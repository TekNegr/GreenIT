<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ZonesHiver extends Model
{
    protected $table = 'zones_hiver';

    protected $fillable = [
        'code',
        't_ext_moyen',
        'peta_cw',
        'dh14',
        'prs1'
    ];

    public function departements(): HasMany
    {
        return $this->hasMany(Departement::class, 'zone_hiver_id');
    }
}
