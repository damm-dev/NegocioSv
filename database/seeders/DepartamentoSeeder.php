<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Departamento;

class DepartamentoSeeder extends Seeder
{
    public function run()
    {
        $departamentos = [
            'San Salvador', 'La Libertad', 'Santa Ana',
            'San Miguel', 'La Unión', 'Sonsonate'
        ];

        foreach ($departamentos as $nombre) {
            Departamento::create(['nombre' => $nombre]);
        }
    }
}
