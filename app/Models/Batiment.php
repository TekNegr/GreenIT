<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use App\Models\Appartement;

class Batiment extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'latitude',
        'longitude',
        'address_text',
        'avg_dpe_grade',
        'avg_ges_grade',
        'avg_energy_consumption',
        'avg_carbon_emission',
        'apartments_count',
    ];

    protected $casts = [
        'latitude' => 'decimal:6',
        'longitude' => 'decimal:6',
        'avg_energy_consumption' => 'float',
        'avg_carbon_emission' => 'float',
        'apartments_count' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid();
            }
        });
    }

    public function apartments(): HasMany
    {
        return $this->hasMany(Appartement::class, 'building_id');
    }
}
