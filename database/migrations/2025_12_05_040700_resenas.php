<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('resenas', function (Blueprint $table) {
            $table->id('id_resena'); // Tu llave primaria

            // Relaciones
            $table->unsignedBigInteger('id_negocio');
            $table->unsignedBigInteger('id_usuario');

            // Datos de la reseña
            $table->text('comentario');
            $table->integer('calificacion'); // Del 1 al 5

            $table->timestamps();

            // Llaves foráneas
            $table->foreign('id_negocio')->references('id_negocio')->on('negocios')->onDelete('cascade');
            $table->foreign('id_usuario')->references('id_usuario')->on('usuarios')->onDelete('cascade');

            /*Un usuario solo puede dejar 1 reseña por negocio
            esto crea un índice único compuesto. */
            $table->unique(['id_negocio', 'id_usuario']); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('resenas');
    }
};
