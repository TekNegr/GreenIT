<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class GeoTile extends Model
{
    protected $table = 'geotiles';

    protected $fillable = [
        'tile_key',
        'bbox',
        'cached_at',
    ];

    protected $casts = [
        'bbox' => 'array',
        'cached_at' => 'datetime',
    ];

    public $timestamps = false;

    /**
     * Check if the GeoTile cache is expired.
     *
     * @param int $minutes
     * @return bool
     */
    public function isExpired($minutes = 60)
    {
        if (!$this->cached_at) {
            return true;
        }
        return $this->cached_at->lt(Carbon::now()->subMinutes($minutes));
    }
}
