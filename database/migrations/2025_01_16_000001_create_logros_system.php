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
        // Tabla de logros disponibles
        Schema::create('logros', function (Blueprint $table) {
            $table->id('id_logro');
            $table->string('nombre', 100);
            $table->text('descripcion');
            $table->string('icono', 50); // Nombre del icono o emoji
            $table->integer('meta'); // Cantidad necesaria para completar
            $table->string('tipo'); // explorador, fiel, apoyo_local, social, critico, influencer
            $table->timestamps();
        });

        // Tabla pivote: logros desbloqueados por usuarios
        Schema::create('usuario_logros', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_usuario');
            $table->unsignedBigInteger('id_logro');
            $table->integer('progreso')->default(0);
            $table->boolean('completado')->default(false);
            $table->timestamp('fecha_completado')->nullable();
            $table->timestamps();

            $table->foreign('id_usuario')->references('id_usuario')->on('usuarios')->onDelete('cascade');
            $table->foreign('id_logro')->references('id_logro')->on('logros')->onDelete('cascade');
            
            // Un usuario solo puede tener un registro por logro
            $table->unique(['id_usuario', 'id_logro']);
        });

        // Tabla de favoritos
        Schema::create('favoritos', function (Blueprint $table) {
            $table->id('id_favorito');
            $table->unsignedBigInteger('id_usuario');
            $table->unsignedBigInteger('id_negocio');
            $table->timestamps();

            $table->foreign('id_usuario')->references('id_usuario')->on('usuarios')->onDelete('cascade');
            $table->foreign('id_negocio')->references('id_negocio')->on('negocios')->onDelete('cascade');
            
            // Un usuario no puede marcar el mismo negocio como favorito dos veces
            $table->unique(['id_usuario', 'id_negocio']);
        });

        // Tabla de seguimientos
        Schema::create('seguimientos', function (Blueprint $table) {
            $table->id('id_seguimiento');
            $table->unsignedBigInteger('id_usuario');
            $table->unsignedBigInteger('id_negocio');
            $table->timestamps();

            $table->foreign('id_usuario')->references('id_usuario')->on('usuarios')->onDelete('cascade');
            $table->foreign('id_negocio')->references('id_negocio')->on('negocios')->onDelete('cascade');
            
            // Un usuario no puede seguir el mismo negocio dos veces
            $table->unique(['id_usuario', 'id_negocio']);
        });

        // Tabla de recomendaciones (para logro de apoyo local)
        Schema::create('recomendaciones', function (Blueprint $table) {
            $table->id('id_recomendacion');
            $table->unsignedBigInteger('id_usuario'); // Quien recomienda
            $table->unsignedBigInteger('id_negocio'); // Negocio recomendado
            $table->string('medio')->nullable(); // email, whatsapp, facebook, etc.
            $table->timestamps();

            $table->foreign('id_usuario')->references('id_usuario')->on('usuarios')->onDelete('cascade');
            $table->foreign('id_negocio')->references('id_negocio')->on('negocios')->onDelete('cascade');
        });

        // Tabla de visitas influenciadas (para logro de influencer)
        Schema::create('visitas_influenciadas', function (Blueprint $table) {
            $table->id('id_visita');
            $table->unsignedBigInteger('id_usuario_influencer'); // Quien influyó
            $table->unsignedBigInteger('id_negocio'); // Negocio visitado
            $table->unsignedBigInteger('id_usuario_visitante')->nullable(); // Quien visitó (puede ser anónimo)
            $table->string('origen')->nullable(); // link_compartido, recomendacion, etc.
            $table->timestamps();

            $table->foreign('id_usuario_influencer')->references('id_usuario')->on('usuarios')->onDelete('cascade');
            $table->foreign('id_negocio')->references('id_negocio')->on('negocios')->onDelete('cascade');
            $table->foreign('id_usuario_visitante')->references('id_usuario')->on('usuarios')->onDelete('set null');
        });

        // Tabla de fotos adicionales del negocio (4 fotos)
        Schema::create('fotos_negocio', function (Blueprint $table) {
            $table->id('id_foto');
            $table->unsignedBigInteger('id_negocio');
            $table->string('ruta_foto');
            $table->integer('orden')->default(1); // 1, 2, 3, 4
            $table->string('descripcion')->nullable();
            $table->timestamps();

            $table->foreign('id_negocio')->references('id_negocio')->on('negocios')->onDelete('cascade');
        });

        // Tabla de promociones/descuentos
        Schema::create('promociones', function (Blueprint $table) {
            $table->id('id_promocion');
            $table->unsignedBigInteger('id_negocio');
            $table->string('titulo', 100);
            $table->text('descripcion');
            $table->integer('descuento_porcentaje')->nullable(); // Ej: 20 (para 20%)
            $table->string('codigo_promocional')->nullable();
            $table->date('fecha_inicio');
            $table->date('fecha_fin');
            $table->boolean('activa')->default(true);
            $table->timestamps();

            $table->foreign('id_negocio')->references('id_negocio')->on('negocios')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('promociones');
        Schema::dropIfExists('fotos_negocio');
        Schema::dropIfExists('visitas_influenciadas');
        Schema::dropIfExists('recomendaciones');
        Schema::dropIfExists('seguimientos');
        Schema::dropIfExists('favoritos');
        Schema::dropIfExists('usuario_logros');
        Schema::dropIfExists('logros');
    }
};
