<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  
    public function up()
    {
        Schema::create('municipios', function (Blueprint $table) {
            $table->id('id_municipio');
            $table->string('nombre', 120);
            $table->unsignedBigInteger('id_departamento');
            $table->timestamps();

            $table->foreign('id_departamento')->references('id_departamento')->on('departamentos');
        });
    }

    public function down()
    {
        Schema::dropIfExists('municipios');
    }
};
