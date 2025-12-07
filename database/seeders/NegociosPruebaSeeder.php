<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class NegociosPruebaSeeder extends Seeder
{
    public function run()
    {
        // Primero crear usuarios para los negocios
        $usuarios = [
            [
                'email' => 'pizzeria@test.com',
                'password' => Hash::make('password123'),
                'id_estado_usuario' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'email' => 'cafeteria@test.com',
                'password' => Hash::make('password123'),
                'id_estado_usuario' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'email' => 'restaurante@test.com',
                'password' => Hash::make('password123'),
                'id_estado_usuario' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'email' => 'panaderia@test.com',
                'password' => Hash::make('password123'),
                'id_estado_usuario' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'email' => 'tienda@test.com',
                'password' => Hash::make('password123'),
                'id_estado_usuario' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($usuarios as $usuario) {
            DB::table('usuarios')->insert($usuario);
        }

        // Obtener IDs de usuarios recién creados
        $pizzeriaUser = DB::table('usuarios')->where('email', 'pizzeria@test.com')->first();
        $cafeteriaUser = DB::table('usuarios')->where('email', 'cafeteria@test.com')->first();
        $restauranteUser = DB::table('usuarios')->where('email', 'restaurante@test.com')->first();
        $panaderiaUser = DB::table('usuarios')->where('email', 'panaderia@test.com')->first();
        $tiendaUser = DB::table('usuarios')->where('email', 'tienda@test.com')->first();

        // Obtener algunos municipios (San Salvador, Santa Tecla, Antiguo Cuscatlán, Soyapango, Mejicanos)
        $sanSalvador = DB::table('municipios')->where('nombre', 'San Salvador')->first();
        $santaTecla = DB::table('municipios')->where('nombre', 'Santa Tecla')->first();
        $antiguoCuscatlan = DB::table('municipios')->where('nombre', 'Antiguo Cuscatlán')->first();
        $soyapango = DB::table('municipios')->where('nombre', 'Soyapango')->first();
        $mejicanos = DB::table('municipios')->where('nombre', 'Mejicanos')->first();

        // Crear negocios de prueba con coordenadas
        $negocios = [
            [
                'id_usuario' => $pizzeriaUser->id_usuario,
                'id_municipio' => $sanSalvador->id_municipio,
                'nombre' => 'Pizzería Don Giovanni',
                'descripcion' => 'Las mejores pizzas artesanales de San Salvador. Masa fresca hecha diariamente con ingredientes importados de Italia.',
                'direccion' => 'Colonia Escalón, Calle La Mascota #123',
                'telefono' => '2222-3344',
                'email_contacto' => 'pizzeria@test.com',
                'latitud' => 13.7042,
                'longitud' => -89.2344,
                'estado_verificacion' => 'verificado',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id_usuario' => $cafeteriaUser->id_usuario,
                'id_municipio' => $santaTecla->id_municipio,
                'nombre' => 'Café Aroma',
                'descripcion' => 'Cafetería especializada en café salvadoreño de altura. Ambiente acogedor perfecto para trabajar o reunirse.',
                'direccion' => 'Centro de Santa Tecla, frente al parque',
                'telefono' => '2223-4455',
                'email_contacto' => 'cafeteria@test.com',
                'latitud' => 13.6769,
                'longitud' => -89.2797,
                'estado_verificacion' => 'verificado',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id_usuario' => $restauranteUser->id_usuario,
                'id_municipio' => $antiguoCuscatlan->id_municipio,
                'nombre' => 'Restaurante El Buen Sabor',
                'descripcion' => 'Comida típica salvadoreña con un toque gourmet. Pupusas, yuca frita y más delicias locales.',
                'direccion' => 'Carretera a Santa Tecla, km 10',
                'telefono' => '2224-5566',
                'email_contacto' => 'restaurante@test.com',
                'latitud' => 13.6894,
                'longitud' => -89.2506,
                'estado_verificacion' => 'verificado',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id_usuario' => $panaderiaUser->id_usuario,
                'id_municipio' => $soyapango->id_municipio,
                'nombre' => 'Panadería La Espiga Dorada',
                'descripcion' => 'Pan fresco todos los días. Especialidad en pan francés, semita y quesadilla salvadoreña.',
                'direccion' => 'Mercado Municipal de Soyapango',
                'telefono' => '2225-6677',
                'email_contacto' => 'panaderia@test.com',
                'latitud' => 13.7108,
                'longitud' => -89.1397,
                'estado_verificacion' => 'verificado',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id_usuario' => $tiendaUser->id_usuario,
                'id_municipio' => $mejicanos->id_municipio,
                'nombre' => 'Tienda La Económica',
                'descripcion' => 'Abarrotes, productos de primera necesidad y más. Precios accesibles y atención personalizada.',
                'direccion' => 'Colonia Zacamil, Calle Principal',
                'telefono' => '2226-7788',
                'email_contacto' => 'tienda@test.com',
                'latitud' => 13.7297,
                'longitud' => -89.2119,
                'estado_verificacion' => 'verificado',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($negocios as $negocio) {
            $negocioId = DB::table('negocios')->insertGetId($negocio);

            // Asignar categorías a cada negocio
            $categorias = [
                'Pizzería Don Giovanni' => [1], // Restaurantes
                'Café Aroma' => [1, 2], // Restaurantes, Cafeterías
                'Restaurante El Buen Sabor' => [1], // Restaurantes
                'Panadería La Espiga Dorada' => [3], // Panaderías
                'Tienda La Económica' => [4], // Tiendas
            ];

            if (isset($categorias[$negocio['nombre']])) {
                foreach ($categorias[$negocio['nombre']] as $categoriaId) {
                    DB::table('negocio_categoria')->insert([
                        'id_negocio' => $negocioId,
                        'id_categoria' => $categoriaId,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }

        $this->command->info('✅ 5 negocios de prueba creados exitosamente!');
        $this->command->info('📍 Ubicaciones: San Salvador, Santa Tecla, Antiguo Cuscatlán, Soyapango, Mejicanos');
    }
}
