<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('terminos', function (Blueprint $table) {
            $table->id('id_termino');
            $table->unsignedBigInteger('id_usuario');
            $table->boolean('acepta_terminos')->default(false);
            $table->boolean('acepta_politicas')->default(false);
            $table->timestamps();

            $table->foreign('id_usuario')->references('id_usuario')->on('usuarios')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('terminos');
    }
};
