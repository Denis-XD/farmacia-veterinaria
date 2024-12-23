<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Periodo;
class PeriodoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $periodos = [
            
            '6:45 - 8:15',
            '8:15 - 9:45',
            '9:45 - 11:15',
            '11:15 - 12:45',
            '12:45 - 14:15',
            '14:15 - 15:45',
            '15:45 - 17:15',
            '17:15 - 18:45',
            '18:45 - 20:15',
            '20:15 - 21:45',
        ];

        foreach ($periodos as $periodo) {
            list($inicio, $fin) = explode(' - ', $periodo);

            // Crear el registro en la base de datos
            Periodo::create([
                'inicio' => $inicio,
                'fin' => $fin,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
    }
}
