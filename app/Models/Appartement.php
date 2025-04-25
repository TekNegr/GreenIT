<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Appartement extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'dpe_code',
        'latitude',
        'longitude',
        'surface_area',
        'year_built',
        'dpe_grade',
        'ges_grade',
        'energy_consumption',
        'carbon_emission',
        'raw_ademe_data',
        'batiment_id', //Foreign Key
    ];

    protected $casts = [
        'latitude' => 'decimal:6',
        'longitude' => 'decimal:6',
        'surface_area' => 'float',
        'year_built' => 'integer',
        'energy_consumption' => 'float',
        'carbon_emission' => 'float',
        'raw_ademe_data' => 'array',
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

    public function batiment(): BelongsTo
    {
        return $this->belongsTo(Batiment::class, 'batiment_id');
    }

    /**
     * Temporary placeholder for AddLogementToDb function.
     * Logs the data received.
     *
     * @param array $data
     * @return void
     */
    public static function AddLogementToDb(array $data)
    {
        \Illuminate\Support\Facades\Log::info('AddLogementToDb called with data: ' . json_encode($data));
        // TODO: Implement actual logic to assign appartement to building and save to DB
    }
}
