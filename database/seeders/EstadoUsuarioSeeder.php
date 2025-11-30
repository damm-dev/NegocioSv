<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\EstadoUsuario;

class EstadoUsuarioSeeder extends Seeder
{
    public function run()
    {
        EstadoUsuario::insert([
            ['nombre' => 'activo'],
            ['nombre' => 'inactivo'],
            ['nombre' => 'suspendido']
        ]);
    }
}
