<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LogrosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $logros = [
            [
                'nombre' => 'Explorador',
                'descripcion' => 'Visita 10 negocios distintos',
                'icono' => 'explorer',
                'meta' => 10,
                'tipo' => 'explorador',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Cliente Fiel',
                'descripcion' => 'Guarda 5 negocios como favoritos',
                'icono' => 'loyal',
                'meta' => 5,
                'tipo' => 'fiel',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Apoyo Local',
                'descripcion' => 'Recomienda 3 negocios a otras personas',
                'icono' => 'support',
                'meta' => 3,
                'tipo' => 'apoyo_local',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Social',
                'descripcion' => 'Sigue a 10 negocios en la plataforma',
                'icono' => 'social',
                'meta' => 10,
                'tipo' => 'social',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Crítico',
                'descripcion' => 'Deja 5 reseñas en diferentes negocios',
                'icono' => 'reviewer',
                'meta' => 5,
                'tipo' => 'critico',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Influencer',
                'descripcion' => 'Consigue que 20 personas visiten negocios por tu recomendación',
                'icono' => 'influencer',
                'meta' => 20,
                'tipo' => 'influencer',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('logros')->insert($logros);
    }
}
