<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run()
{
    $this->call([
        EstadoUsuarioSeeder::class,
        DepartamentoSeeder::class,
        MunicipioSeeder::class,
        CategoriaSeeder::class,
        MetodoPagoSeeder::class,
        UsuarioSeeder::class,
        InteresSeeder::class,
        TerminoSeeder::class,
    ]);
}
}
