<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EstadoMensajeSeeder extends Seeder
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
            'SOLUCIONADO',
            'RECHAZADO'
        ];

        foreach ($estados as $estado) {
            \App\Models\EstadoMensaje::create(['nombre' => $estado]);
        }
    }
}
