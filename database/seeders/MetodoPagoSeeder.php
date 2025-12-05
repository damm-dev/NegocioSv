<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MetodoPago;

class MetodoPagoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $metodos = [
            ['nombre' => 'Efectivo'],
            ['nombre' => 'Tarjeta de Crédito'],
            ['nombre' => 'Tarjeta de Débito'],
            ['nombre' => 'Transferencia Bancaria'],
            ['nombre' => 'Bitcoin'],
            ['nombre' => 'Chivo Wallet'],
            ['nombre' => 'PayPal'],
        ];

        foreach ($metodos as $metodo) {
            MetodoPago::create($metodo);
        }
    }
}
