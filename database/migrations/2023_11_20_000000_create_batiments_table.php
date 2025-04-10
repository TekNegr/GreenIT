<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('batiments')) {
            Schema::create('batiments', function (Blueprint $table) {
                $table->id();
                $table->string('numero_dpe')->unique();
                $table->unsignedBigInteger('tr002_type_batiment_id');
                $table->string('partie_batiment')->nullable(); // For apartment/unit identification
                $table->integer('consommation_energie');
                $table->char('classe_consommation_energie', 1); // A-G
                $table->integer('estimation_ges');
                $table->char('classe_estimation_ges', 1); // A-G
                $table->integer('annee_construction');
                $table->decimal('surface_habitable', 10, 2);
                $table->unsignedBigInteger('tv016_departement_id');
                $table->string('commune');
                $table->string('code_postal');
                $table->string('nom_rue')->after('code_postal');
                $table->string('numero_rue')->after('nom_rue');
                $table->string('batiment')->nullable()->after('numero_rue');
                $table->geometry('geometry', 'POINT', 4326)->nullable();

                // Other relevant fields from CSV
                $table->timestamps();
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('batiments');
    }
};
