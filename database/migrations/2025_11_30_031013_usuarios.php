<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
     public function up()
    {
        Schema::create('usuarios', function (Blueprint $table) {
            $table->id('id_usuario');
            $table->string('email', 150)->unique();
            $table->string('password');
            $table->unsignedBigInteger('id_estado_usuario')->default(1);
            $table->timestamps();

            $table->foreign('id_estado_usuario')->references('id_estado_usuario')->on('estados_usuario');
        });
    }

    public function down()
    {
        Schema::dropIfExists('usuarios');
    }
};
