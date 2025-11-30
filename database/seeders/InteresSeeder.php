<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Interes;

class InteresSeeder extends Seeder
{
    public function run()
    {
        Interes::insert([
            ['id_usuario' => 1, 'id_categoria' => 1],
            ['id_usuario' => 1, 'id_categoria' => 3]
        ]);
    }
}
