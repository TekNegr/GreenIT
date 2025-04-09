<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Batiment extends Model
{
    use HasFactory;


    protected $fillable = [
        'rue',
        'code_postal',
        'ville',
        'secteur_id',
        'quartier_id',
    ];


    public function secteur()
    {
        return $this->belongsTo(Secteur::class);
    }

    
    public function quartier()
    {
        return $this->belongsTo(Quartier::class);
    }

    
    public function appartements()
    {
        return $this->hasMany(Appartement::class);
    }
}
