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
            $table->uuid('id')->primary();
            $table->string('dpe_code')->nullable();
            $table->decimal('latitude', 10, 6)->nullable();
            $table->decimal('longitude', 10, 6)->nullable();
            $table->string('address')->nullable();
            $table->float('surface_area')->nullable();
            $table->integer('year_built')->nullable();
            $table->string('dpe_grade', 2)->nullable();
            $table->string('ges_grade', 2)->nullable();
            $table->float('energy_consumption')->nullable();
            $table->float('carbon_emission')->nullable();
            $table->uuid('batiment_id')->nullable();
            $table->timestamps();

            $table->foreign('batiment_id')->references('id')->on('batiments')->onDelete('set null');
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
