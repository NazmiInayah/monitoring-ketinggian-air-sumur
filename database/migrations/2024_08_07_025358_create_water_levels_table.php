<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWaterLevelsTable extends Migration
{
    public function up()
    {
        Schema::create('water_levels', function (Blueprint $table) {
            $table->id();
            $table->float('level'); // To store water level
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('water_levels');
    }
}
