<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('type_batiments', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('libelle');
            $table->text('description')->nullable();
            $table->integer('ordre');
            $table->boolean('est_efface')->default(false);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('type_batiments');
    }
};
