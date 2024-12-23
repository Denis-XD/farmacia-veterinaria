<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $permissions = [
            'ambiente_listar',
            'ambiente_crear',
            'ambiente_actualizar',
            'ambiente_eliminar',

            'carrera_listar',
            'carrera_crear',
            'carrera_actualizar',
            'carrera_eliminar',

            'grupo_listar',
            'grupo_crear',
            'grupo_actualizar',
            'grupo_eliminar',

            'materia_carrera_listar',
            'materia_carrera_crear',
            'materia_carrera_actualizar',
            'materia_carrera_eliminar',

            'materia_listar',
            'materia_crear',
            'materia_actualizar',
            'materia_eliminar',

            'permiso_listar',
            'permiso_crear',
            'permiso_actualizar',
            'permiso_eliminar',

            'rol_listar',
            'rol_crear',
            'rol_actualizar',
            'rol_eliminar',

            'tipo_ambiente_listar',
            'tipo_ambiente_crear',
            'tipo_ambiente_actualizar',
            'tipo_ambiente_eliminar',

            'ubicacion_listar',
            'ubicacion_crear',
            'ubicacion_actualizar',
            'ubicacion_eliminar',

            'usuario_listar',
            'usuario_crear',
            'usuario_actualizar',
            'usuario_eliminar',

            'asignacion_listar',
            'asignacion_crear',
            'asignacion_actualizar',
            'asignacion_eliminar',

            'reservar_listar',

            'solicitud_listar',
            'solicitud_aceptar',
            'solicitud_rechazar',

            'notificacion_listar',
            'notificacion_crear',
            'notificacion_detalles',

            'reglas',

            'mensaje_listar',
            'mensaje_crear',
            'mensaje_detalles',
            'mensaje_editar',
            'mensaje_todos',
            'enviar_mensaje_correo'
        ];

        foreach ($permissions as $permission) {
            \Spatie\Permission\Models\Permission::create(['name' => $permission]);
        }
    }
}
