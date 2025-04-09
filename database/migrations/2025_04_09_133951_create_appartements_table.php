<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('appartements', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('rue');
        $table->string('code_postal');
        $table->string('ville');
        $table->unsignedBigInteger('secteur_id')->nullable();  // Pour associer un secteur
        $table->unsignedBigInteger('quartier_id')->nullable(); // Pour associer un quartier

        // Clés étrangères (si les tables existent)
        $table->foreign('secteur_id')->references('id')->on('secteurs')->onDelete('set null');
        $table->foreign('quartier_id')->references('id')->on('quartiers')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appartements');
    }
};
