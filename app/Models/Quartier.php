<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Quartier extends Model
{
    public function up()
{
    Schema::create('quartiers', function (Blueprint $table) {
        $table->id();
        $table->string('nom');  // Le nom du quartier
        $table->unsignedBigInteger('secteur_id')->nullable(); // Lien vers le secteur (optionnel)
        $table->string('ville'); // Ville associée au quartier

        // Clé étrangère vers le secteur
        $table->foreign('secteur_id')->references('id')->on('secteurs')->onDelete('set null');

        $table->timestamps();
    });
}
}
