<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Municipio;

class MunicipioSeeder extends Seeder
{
    public function run()
    {
        Municipio::insert([
            ['nombre' => 'San Salvador', 'id_departamento' => 1],
            ['nombre' => 'Mejicanos', 'id_departamento' => 1],
            ['nombre' => 'Santa Tecla', 'id_departamento' => 2],
            ['nombre' => 'Antiguo Cuscatlán', 'id_departamento' => 2]
        ]);
    }
}
