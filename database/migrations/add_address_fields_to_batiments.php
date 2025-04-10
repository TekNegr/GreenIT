<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('batiments', function (Blueprint $table) {
            $table->string('nom_rue')->after('code_postal');
            $table->string('numero_rue')->after('nom_rue');
            $table->string('batiment')->nullable()->after('numero_rue');
        });
    }

    public function down()
    {
        Schema::table('batiments', function (Blueprint $table) {
            $table->dropColumn(['nom_rue', 'numero_rue', 'batiment']);
        });
    }
};
