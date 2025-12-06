<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Administrador;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Administrador::create([
            'nombre' => 'Administrador Principal',
            'email' => 'admin@negociosv.com',
            'password' => Hash::make('Admin2025!NegocioSV'),
            'activo' => true
        ]);
    }
}
