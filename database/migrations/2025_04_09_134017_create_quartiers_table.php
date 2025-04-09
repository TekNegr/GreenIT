<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**GIT 
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('quartiers', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('nom');  // Le nom du quartier
            $table->unsignedBigInteger('secteur_id')->nullable(); // Lien vers le secteur (optionnel)
            $table->string('ville'); // Ville associée au quartier
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quartiers');
    }
};
