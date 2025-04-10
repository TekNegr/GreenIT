<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('zones_ete', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->decimal('sclim_inf_150', 5, 2);
            $table->decimal('sclim_sup_150', 5, 2);
            $table->decimal('rclim_autres_etages', 5, 2);
            $table->decimal('rclim_dernier_etage', 5, 2);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('zones_ete');
    }
};
