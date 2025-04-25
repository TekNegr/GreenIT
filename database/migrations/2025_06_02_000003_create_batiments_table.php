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
        Schema::create('batiments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->decimal('latitude', 10, 6)->nullable();
            $table->decimal('longitude', 10, 6)->nullable();
            $table->string('address_text')->nullable();
            $table->string('avg_dpe_grade', 2)->nullable();
            $table->string('avg_ges_grade', 2)->nullable();
            $table->float('avg_energy_consumption')->nullable();
            $table->float('avg_carbon_emission')->nullable();
            $table->integer('apartments_count')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('batiments');
    }
};
