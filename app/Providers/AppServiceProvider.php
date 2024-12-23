<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\DB;
use App\Models\Mensaje;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Utilizar View Composer para la plantilla layout.blade.php
        View::composer('layout', function ($view) {
            if (Gate::allows('mensaje_todos')) {
                $cantidadMensajes = Mensaje::where('id_estado_mensaje', 1)->count();
            } else {
                $cantidadMensajes = Mensaje::where('id_estado_mensaje', 1)
                    ->where('id_usuario', auth()->id())
                    ->count();
            }

            // Contar los productos en la vista `VistaAlertasStock`
            $cantidadProductosStock = DB::table('VistaAlertasStock')->count();

            // Compartir variables con la vista
            $view->with([
                'cantidadMensajes' => $cantidadMensajes,
                'cantidadProductosStock' => $cantidadProductosStock,
            ]);
        });
    }
}
