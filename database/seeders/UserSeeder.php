<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Admin
        // Create admin user
        $admin = User::create([
            'nombre' => 'ADMIN',
            'celular_usuario' => '72760930',
            'email' => 'alvaplanta1@hotmail.com',
            'password' => bcrypt('admin'),
        ]);


        // Assign admin role to the user
        $adminRole = Role::where('name', 'ADMIN')->first();
        $admin->roles()->attach($adminRole);


        /*

        $docente = User::create([
            'nombre' => 'JUAN',
            'apellido' => 'PEREZ',
            'email' => 'juanperez@gmail.com',
            'password' => bcrypt('juanperez'),
        ]);

        // Assign admin role to the user
        $docenteRole = Role::where('name', 'DOCENTE')->first();
        $docente->roles()->attach($docenteRole);

        $visitante = User::create([
            'nombre' => 'MARIA',
            'apellido' => 'LOPEZ',
            'email' => 'marialopez@gmail.com',
            'password' => bcrypt('marialopez'),
        ]);

        // Assign admin role to the user
        $visitanteRole = Role::where('name', 'VISITANTE')->first();
        $visitante->roles()->attach($visitanteRole);*/
    }
}
