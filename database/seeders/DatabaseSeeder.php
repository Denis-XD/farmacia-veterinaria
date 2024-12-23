<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            PermissionSeeder::class,
            RoleSeeder::class,
            RoleHasPermissionSeeder::class,
            UserSeeder::class,
            TipoAmbienteSeeder::class,
            UbicacionesSeeder::class,
            PeriodoSeeder::class,
            EstadosSeeder::class,
            TipoNotificacionSeeder::class,
            EstadoMensajeSeeder::class,
        ]);
    }
}
