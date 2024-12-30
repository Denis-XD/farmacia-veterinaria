<?php

use App\Http\Controllers\AmbienteController;
use App\Http\Controllers\CambiarContrasenaController;
use App\Http\Controllers\CarreraController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MateriaController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\RolController;
use App\Http\Controllers\UbicacionController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\MensajeController;
use App\Http\Controllers\MisReservasController;
use App\Http\Controllers\NotificacionController;
use App\Http\Controllers\ReglasController;
use App\Http\Controllers\ProveedorController;
use App\Http\Controllers\SocioController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\CompraController;
use App\Http\Controllers\VentaController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::middleware('auth')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('reservas');

    Route::resource('roles', RolController::class)->names([
        'index' => 'roles.index',
        'create' => 'roles.create',
        'store' => 'roles.store',
        'show' => 'roles.show',
        'edit' => 'roles.edit',
        'update' => 'roles.update',
        'destroy' => 'roles.destroy',
    ]);


    Route::resource('ubicaciones', UbicacionController::class)->names([
        'index' => 'ubicaciones.index',
        'create' => 'ubicaciones.create',
        'store' => 'ubicaciones.store',
        'show' => 'ubicaciones.show',
        'edit' => 'ubicaciones.edit',
        'update' => 'ubicaciones.update',
        'destroy' => 'ubicaciones.destroy',
    ]);


    Route::resource('carreras', CarreraController::class)->names([
        'index' => 'carreras.index',
        'create' => 'carreras.create',
        'store' => 'carreras.store',
        'show' => 'carreras.show',
        'edit' => 'carreras.edit',
        'update' => 'carreras.update',
        'destroy' => 'carreras.destroy',
    ]);

    Route::post('materias/import', [MateriaController::class, 'import'])->name('materias.import');

    Route::resource('materias', MateriaController::class)->names([
        'index' => 'materias.index',
        'create' => 'materias.create',
        'store' => 'materias.store',
        'show' => 'materias.show',
        'edit' => 'materias.edit',
        'update' => 'materias.update',
        'destroy' => 'materias.destroy',
    ]);

    Route::post('usuarios/import', [UserController::class, 'import'])->name('usuarios.import');

    Route::resource('usuarios', UserController::class)->names([
        'index' => 'usuarios.index',
        'create' => 'users.create',
        'store' => 'users.store',
        'show' => 'users.show',
        'edit' => 'users.edit',
        'update' => 'users.update',
        'destroy' => 'users.destroy',
    ]);

    Route::post('proveedores/import', [ProveedorController::class, 'import'])->name('proveedores.import');
    Route::get('proveedores/buscar2', [ProveedorController::class, 'buscar2'])->name('proveedores.buscar2');

    Route::resource('proveedores', ProveedorController::class)->names([
        'index' => 'proveedores.index',
        'create' => 'proveedores.create',
        'store' => 'proveedores.store',
        'show' => 'proveedores.show',
        'edit' => 'proveedores.edit',
        'update' => 'proveedores.update',
        'destroy' => 'proveedores.destroy',
    ]);

    Route::post('socios/import', [SocioController::class, 'import'])->name('socios.import');

    Route::resource('socios', SocioController::class)->names([
        'index' => 'socios.index',
        'create' => 'socios.create',
        'store' => 'socios.store',
        'show' => 'socios.show',
        'edit' => 'socios.edit',
        'update' => 'socios.update',
        'destroy' => 'socios.destroy',
    ]);

    Route::resource('permisos', PermissionController::class)->names([
        'index' => 'permisos.index',
        'create' => 'permisos.create',
        'store' => 'permisos.store',
        'show' => 'permisos.show',
        'edit' => 'permisos.edit',
        'update' => 'permisos.update',
        'destroy' => 'permisos.destroy',
    ]);

    Route::post('productos/generate-barcode', [ProductoController::class, 'generateBarcode'])->name('generate.barcode');
    Route::get('/productos/stock-minimo', [ProductoController::class, 'productosMinimoStock'])->name('productos.stock_minimo');
    Route::get('productos/buscar2', [ProductoController::class, 'buscar2'])->name('productos.buscar2');

    Route::resource('productos', ProductoController::class)->names([
        'index' => 'productos.index',
        'create' => 'productos.create',
        'store' => 'productos.store',
        'show' => 'productos.show',
        '{id_producto}/edit' => 'productos.edit',
        'update' => 'productos.update',
        'destroy' => 'productos.destroy',
    ]);

    Route::get('compras/registrar', [CompraController::class, 'registrar'])->name('compras.registrar');
    Route::get('/compras/{id}/descargar', [CompraController::class, 'descargarPdf'])->name('compras.descargar');
    Route::get('/compras/dashboard', [CompraController::class, 'dashboard'])->name('compras.dashboard');

    Route::resource('compras', CompraController::class)->names([
        'index' => 'compras.index',
        'create' => 'compras.create',
        'store' => 'compras.store',
        'show' => 'compras.show',
        'edit' => 'compras.edit',
        'update' => 'compras.update',
        'destroy' => 'compras.destroy',
    ]);

    Route::resource('cambiar_contrasena', CambiarContrasenaController::class)->names([
        'index' => 'cambiar_contrasena.index',
        'create' => 'cambiar_contrasena.create',
        'store' => 'cambiar_contrasena.store',
        'show' => 'cambiar_contrasena.show',
        'edit' => 'cambiar_contrasena.edit',
        'update' => 'cambiar_contrasena.update',
        'destroy' => 'cambiar_contrasena.destroy',
    ]);

    Route::get('ventas/registrar', [VentaController::class, 'registrar'])->name('ventas.registrar');
    Route::get('/ventas/{id}/descargar', [VentaController::class, 'descargarPdf'])->name('ventas.descargar');
    Route::get('/ventas/reporte-utilidad', [VentaController::class, 'generarReporteUtilidad'])->name('ventas.reporteUtilidad');
    Route::get('/ventas/reporte-utilidad/pdf', [VentaController::class, 'descargarReportePdf'])->name('ventas.descargarReportePdf');
    Route::get('/ventas/dashboard', [VentaController::class, 'dashboard'])->name('ventas.dashboard');

    Route::resource('ventas', VentaController::class)->names([
        'index' => 'ventas.index',
        'create' => 'ventas.create',
        'store' => 'ventas.store',
        'show' => 'ventas.show',
        'edit' => 'ventas.edit',
        'update' => 'ventas.update',
        'destroy' => 'ventas.destroy',
    ]);

    Route::patch('mis_reservas/{id}/cancel', [MisReservasController::class, 'cancel'])->name('mis_reservas.cancel');

    Route::resource('mis_reservas', MisReservasController::class)->names([
        'index' => 'mis_reservas.index',
        'create' => 'mis_reservas.create',
        'store' => 'mis_reservas.store',
        'show' => 'mis_reservas.show',
        'edit' => 'mis_reservas.edit',
        'update' => 'mis_reservas.update',
        'destroy' => 'mis_reservas.destroy',
    ]);

    Route::resource('notificaciones', NotificacionController::class)->names([
        'index' => 'notificaciones.index',
        'create' => 'notificaciones.create',
        'store' => 'notificaciones.store',
        'show' => 'notificaciones.show',
        'edit' => 'notificaciones.edit',
        'update' => 'notificaciones.update',
        'destroy' => 'notificaciones.destroy',
    ]);

    Route::resource('notificaciones', NotificacionController::class)->names([
        'index' => 'notificaciones.index',
        'create' => 'notificaciones.create',
        'store' => 'notificaciones.store',
        'show' => 'notificaciones.show',
        'edit' => 'notificaciones.edit',
        'update' => 'notificaciones.update',
        'destroy' => 'notificaciones.destroy',
    ]);
    Route::resource('reglas', ReglasController::class)->names([
        'index' => 'reglas.index',
        'create' => 'reglas.create',
        'store' => 'reglas.store',
    ]);

    Route::resource('mensajes', MensajeController::class)->names([
        'index' => 'mensajes.index',
        'create' => 'mensajes.create',
        'store' => 'mensajes.store',
        'show' => 'mensajes.show',
        'edit' => 'mensajes.edit',
        'update' => 'mensajes.update',
        'destroy' => 'mensajes.destroy',
    ]);
    Route::post('/mensajes/{mensaje}/actualizar-estado/{nuevoEstado}', [MensajeController::class, 'updateEstado'])
        ->name('mensajes.actualizar_estado');


    Route::get('logout', [UserController::class, 'logout'])->name('users.logout');
    Route::match(['get', 'post'], '/reservarAmbiente', [AmbienteController::class, 'indexAmbientes'])->name('ambientes.indexAmbientes');
});
Route::get('login', function () {
    if (Auth::check()) {
        return redirect()->route('reservas');
    }
    return view('auth.login');
})->name('login');

Route::post('login', [UserController::class, 'login'])->name('users.login');
