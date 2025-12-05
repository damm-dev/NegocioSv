<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Categoria;

class CategoriaSeeder extends Seeder
{
    public function run()
    {
        $categorias = [
            'Restaurante',
            'Cafetería',
            'Barbería',
            'Salón de Belleza',
            'Gimnasio',
            'Tienda',
            'Servicios Profesionales',
            'Entretenimiento',
            'Educación',
            'Salud',
        ];

        foreach ($categorias as $nombre) {
            Categoria::create(['nombre' => $nombre]);
        }
    }
}
