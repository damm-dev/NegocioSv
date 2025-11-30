<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('perfiles', function (Blueprint $table) {
            $table->id('id_perfil');
            $table->unsignedBigInteger('id_usuario')->unique();
            $table->string('nombres', 120);
            $table->string('apellidos', 120);
            $table->date('fecha_nacimiento')->nullable();
            $table->string('genero', 20)->nullable();
            $table->string('telefono', 20)->nullable();
            $table->string('foto')->nullable();
            $table->unsignedBigInteger('id_municipio')->nullable();
            $table->text('descripcion')->nullable();
            $table->boolean('ubicacion_activa')->default(false);
            $table->timestamps();

            $table->foreign('id_usuario')->references('id_usuario')->on('usuarios')->onDelete('cascade');
            $table->foreign('id_municipio')->references('id_municipio')->on('municipios');
        });
    }

    public function down()
    {
        Schema::dropIfExists('perfiles');
    }
};
