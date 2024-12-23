<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class TipoNotificacionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $tiposNotificacion = ['IMPORTANTE', 'INFORMACION', 'RECORDATORIO'];

        foreach ($tiposNotificacion as $tipoNotificacion) {
            \App\Models\TipoNotificacion::create([
                'nombre' => $tipoNotificacion
            ]);
        }
    }
}
