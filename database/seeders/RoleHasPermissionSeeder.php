<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleHasPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Admin
        $admin_permissions = Permission::all();
        Role::findOrFail(1)->permissions()->sync($admin_permissions->pluck('id'));

        // Docente
        /* $docente_permissions = $admin_permissions->filter(function ($permiso) {
            return substr($permiso->name, 0, 8) != 'permiso_' &&
                substr($permiso->name, 0, 5) != 'role_' &&
                substr($permiso->name, 0, 8) != 'usuario_';
        });
        Role::findOrFail(2)->permissions()->sync($docente_permissions->pluck('id'));*/
        /*$docente_permissions = Permission::whereIn('name', ['ambiente_listar'])->get();
        Role::findOrFail(2)->permissions()->sync($docente_permissions->pluck('id'));

        $visitante_permissions = $admin_permissions->filter(function ($permiso) {
            return strpos($permiso->name, '_listar') !== false;
        });
        Role::findOrFail(3)->permissions()->sync($visitante_permissions->pluck('id'));*/
    }
}
