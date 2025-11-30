<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Usuario;
use App\Models\Perfil;

class UsuarioSeeder extends Seeder
{
    public function run()
    {
        $usuario = Usuario::create([
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
            'id_estado_usuario' => 1
        ]);

        Perfil::create([
            'id_usuario' => $usuario->id_usuario,
            'nombres' => 'Usuario',
            'apellidos' => 'Prueba',
            'fecha_nacimiento' => '2000-01-01',
            'genero' => 'no declarado',
            'telefono' => '0000-0000',
            'id_municipio' => 1,
            'descripcion' => 'Perfil de prueba',
            'ubicacion_activa' => false
        ]);
    }
}
