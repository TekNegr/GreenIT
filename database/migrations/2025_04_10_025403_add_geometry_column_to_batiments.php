<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('batiments', function (Blueprint $table) {
            $table->geometry('geometry')->nullable()->after('batiment');
        });
    }

    public function down()
    {
        Schema::table('batiments', function (Blueprint $table) {
            $table->dropColumn('geometry');
        });
    }
};
