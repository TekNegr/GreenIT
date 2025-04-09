<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Appartement extends Model
{
    public function up()
{
    Schema::create('batiments', function (Blueprint $table) {
        $table->id();
        $table->string('rue');
        $table->string('code_postal');
        $table->string('ville');
        $table->unsignedBigInteger('secteur_id')->nullable();  // Pour associer un secteur
        $table->unsignedBigInteger('quartier_id')->nullable(); // Pour associer un quartier

        // Clés étrangères (si les tables existent)
        $table->foreign('secteur_id')->references('id')->on('secteurs')->onDelete('set null');
        $table->foreign('quartier_id')->references('id')->on('quartiers')->onDelete('set null');

        $table->timestamps();
    });
}

}
