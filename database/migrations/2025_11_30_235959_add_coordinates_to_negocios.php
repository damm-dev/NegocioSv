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
        Schema::table('negocios', function (Blueprint $table) {
            $table->decimal('latitud', 10, 8)->nullable()->after('direccion');
            $table->decimal('longitud', 11, 8)->nullable()->after('latitud');
            
            // Índice para mejorar el rendimiento de búsquedas geográficas
            $table->index(['latitud', 'longitud']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('negocios', function (Blueprint $table) {
            $table->dropIndex(['latitud', 'longitud']);
            $table->dropColumn(['latitud', 'longitud']);
        });
    }
};
