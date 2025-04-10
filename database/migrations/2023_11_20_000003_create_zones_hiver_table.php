<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('zones_hiver', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->decimal('t_ext_moyen', 5, 2);
            $table->decimal('peta_cw', 5, 2);
            $table->integer('dh14');
            $table->decimal('prs1', 5, 2);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('zones_hiver');
    }
};
