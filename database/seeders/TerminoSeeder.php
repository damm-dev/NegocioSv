<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Termino;

class TerminoSeeder extends Seeder
{
    public function run()
    {
        Termino::create([
            'id_usuario' => 1,
            'acepta_terminos' => true,
            'acepta_politicas' => true
        ]);
    }
}
