<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        // Tabla Catálogo: METODOS DE PAGO 
        Schema::create('metodos_pago', function (Blueprint $table) {
            $table->id('id_metodo_pago');
            $table->string('nombre', 50); // Ej: Efectivo, Tarjeta, Bitcoin
            $table->timestamps();
        });

        // Tabla Principal: NEGOCIOS
        Schema::create('negocios', function (Blueprint $table) {
            $table->id('id_negocio');
            
            // Relación con USUARIOS (el dueño del negocio)
            $table->unsignedBigInteger('id_usuario'); 
            $table->foreign('id_usuario')->references('id_usuario')->on('usuarios')->onDelete('cascade');

            // Relación con MUNICIPIOS (Ubicación)
            $table->unsignedBigInteger('id_municipio');
            $table->foreign('id_municipio')->references('id_municipio')->on('municipios');

            // Datos del Negocio desde frontend
            $table->string('nombre', 100);
            $table->text('descripcion');
            $table->string('direccion', 200);
            $table->string('telefono', 20);
            $table->string('email_contacto', 100);
            $table->string('logo')->nullable();
            
            // Estado (Activo/Inactivo) y Verificación
            $table->boolean('estado_verificacion')->default(false);
            
            $table->timestamps();
        });

        // Tabla Pivote: NEGOCIO_CATEGORIA
        // Conecta 'negocios' con 'categorias'
        Schema::create('negocio_categoria', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_negocio');
            $table->unsignedBigInteger('id_categoria');

            $table->foreign('id_negocio')->references('id_negocio')->on('negocios')->onDelete('cascade');
            $table->foreign('id_categoria')->references('id_categoria')->on('categorias')->onDelete('cascade');
        });

        // Tabla Pivote: NEGOCIO_METODO_PAGO
        Schema::create('negocio_metodo_pago', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_negocio');
            $table->unsignedBigInteger('id_metodo_pago');

            $table->foreign('id_negocio')->references('id_negocio')->on('negocios')->onDelete('cascade');
            $table->foreign('id_metodo_pago')->references('id_metodo_pago')->on('metodos_pago')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('negocio_schema');
    }
};
