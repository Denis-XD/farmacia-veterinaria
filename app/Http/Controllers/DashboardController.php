<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\Compra;
use App\Models\Venta;
use App\Models\Producto;

class DashboardController extends Controller
{
    public function index()
    {
        // Obtener el usuario autenticado
        $usuario = Auth::user();

        // Cantidad de compras del día
        $comprasHoy = Compra::whereDate('fecha_compra', Carbon::today())->count();

        // Cantidad de ventas del día
        $ventasHoy = Venta::whereDate('fecha_venta', Carbon::today())->count();

        // Total de productos
        $totalProductos = Producto::count();

        return view('pages.dashboard', compact('usuario', 'comprasHoy', 'ventasHoy', 'totalProductos'));
    }
}
