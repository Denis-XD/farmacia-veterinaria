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

            'permiso_listar',
            'permiso_crear',
            'permiso_actualizar',
            'permiso_eliminar',

            'rol_listar',
            'rol_crear',
            'rol_actualizar',
            'rol_eliminar',

            'usuario_listar',
            'usuario_crear',
            'usuario_actualizar',
            'usuario_eliminar',

            'notificacion_listar',
            'notificacion_crear',
            'notificacion_detalles',

            'mensaje_listar',
            'mensaje_crear',
            'mensaje_detalles',
            'mensaje_editar',
            'mensaje_todos',
            'enviar_mensaje_correo',

            'proveedor_listar',
            'proveedor_crear',
            'proveedor_actualizar',
            'proveedor_eliminar',

            'socio_listar',
            'socio_crear',
            'socio_actualizar',
            'socio_eliminar',

            'producto_listar',
            'producto_crear',
            'producto_verifi_stock',
            'producto_inventario',
            'producto_actualizar',
            'producto_eliminar',

            'compra_listar',
            'compra_registrar',
            'compra_actualizar',
            'compra_eliminar',
            'compra_dashboard',

            'venta_listar',
            'venta_registrar',
            'venta_actualizar',
            'venta_eliminar',
            'venta_dashboard',
            'venta_reporte_utilidad',
        ];

        foreach ($permissions as $permission) {
            \Spatie\Permission\Models\Permission::create(['name' => $permission]);
        }
    }
}
