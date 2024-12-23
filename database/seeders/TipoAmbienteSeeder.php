<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class TipoAmbienteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $tiposAmbiente = [
            [
                'nombre' => 'AUDITORIO',
                'color' => '#7749F8'
            ],
            [
                'nombre' => 'AULA',
                'color' => '#087990'
            ],
            [
                'nombre' => 'LABORATORIO',
                'color' => '#6C757D'
            ],
        ];

        foreach ($tiposAmbiente as $tipoAmbiente) {
            \App\Models\TipoAmbiente::create([
                'nombre' => $tipoAmbiente['nombre'], // Acceder directamente al valor 'nombre'
                'color' => $tipoAmbiente['color'] // Acceder directamente al valor 'color'
            ]);
        }
    }
}
