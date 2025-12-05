<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\Usuario;
use App\Models\Perfil;
use App\Models\Negocio;
use App\Models\Interes;

class UsuariosPruebaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // ========================================
        // 1. USUARIO PERSONA DE PRUEBA
        // ========================================
        
        $usuarioPersona = Usuario::create([
            'email' => 'persona@test.com',
            'password' => Hash::make('password123'),
            'id_estado_usuario' => 1,
        ]);

        Perfil::create([
            'id_usuario' => $usuarioPersona->id_usuario,
            'nombres' => 'Juan Carlos',
            'apellidos' => 'Pérez García',
            'fecha_nacimiento' => '1990-05-15',
            'genero' => 'M',
            'telefono' => '7000-1234',
            'foto' => null,
            'id_municipio' => 1, // San Salvador
            'descripcion' => 'Usuario de prueba para cuenta persona',
            'ubicacion_activa' => true,
        ]);

        // Agregar intereses
        $interesesPersona = [1, 2, 3]; // Restaurantes, Cafeterías, Tecnología
        foreach ($interesesPersona as $categoriaId) {
            Interes::create([
                'id_usuario' => $usuarioPersona->id_usuario,
                'id_categoria' => $categoriaId,
            ]);
        }

        // ========================================
        // 2. USUARIO NEGOCIO DE PRUEBA
        // ========================================
        
        $usuarioNegocio = Usuario::create([
            'email' => 'negocio@test.com',
            'password' => Hash::make('password123'),
            'id_estado_usuario' => 1,
        ]);

        $negocio = Negocio::create([
            'id_usuario' => $usuarioNegocio->id_usuario,
            'id_municipio' => 1, // San Salvador
            'nombre' => 'Café La Esquina',
            'descripcion' => 'Cafetería especializada en café de altura con ambiente acogedor',
            'direccion' => 'Calle La Mascota #24, Colonia San Benito, San Salvador',
            'telefono' => '7000-5678',
            'email_contacto' => 'contacto@cafelaesquina.com',
            'logo' => null,
            'estado_verificacion' => true,
        ]);

        // Agregar categorías al negocio
        $negocio->categorias()->attach([2]); // Cafetería

        // Agregar métodos de pago
        $negocio->metodosPago()->attach([1, 2, 3]); // Efectivo, Tarjeta, Transferencia

        $this->command->info('✅ Usuarios de prueba creados exitosamente!');
        $this->command->info('');
        $this->command->info('📋 CREDENCIALES DE ACCESO:');
        $this->command->info('');
        $this->command->info('👤 USUARIO PERSONA:');
        $this->command->info('   Email: persona@test.com');
        $this->command->info('   Password: password123');
        $this->command->info('');
        $this->command->info('🏢 USUARIO NEGOCIO:');
        $this->command->info('   Email: negocio@test.com');
        $this->command->info('   Password: password123');
        $this->command->info('   Negocio: Café La Esquina');
        $this->command->info('');
    }
}
