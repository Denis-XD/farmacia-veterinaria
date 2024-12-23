<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class EstadosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $estados = [
            'PENDIENTE',
            'ACEPTADO',
            'RECHAZADO',
            'CANCELADO',
        ];

        foreach ($estados as $estado) {
            \App\Models\Estado::create(['nombre' => $estado]);
        }
    }
}
