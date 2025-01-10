<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Proveedor;
use App\Models\Producto;
use App\Models\Compra;
use App\Models\HistorialInventario;
use App\Models\DetalleCompra;
use Carbon\Carbon;
use Illuminate\Support\Facades\Gate;
use Barryvdh\DomPDF\Facade\Pdf;

class CompraController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        abort_if(Gate::denies('compra_listar'), 403);

        $orden = $request->get('orden', 'desc'); // Orden por defecto descendente
        $proveedor = $request->get('proveedor', 'all'); // Filtro de proveedor
        $tipo = $request->get('tipo', 'all'); // Filtro por tipo (con o sin factura)
        $fechaDesde = $request->get('fecha_desde', null);
        $fechaHasta = $request->get('fecha_hasta', null);
        $fechaEspecifica = $request->get('fecha', null);

        // Construir la consulta base
        $query = Compra::with(['proveedor', 'detalles.producto']);

        // Filtro por proveedor
        if ($proveedor != 'all') {
            $query->whereHas('proveedor', function ($q) use ($proveedor) {
                $q->where('nombre_proveedor', 'like', '%' . $proveedor . '%');
            });
        }

        // Filtro por fecha específica
        if ($fechaEspecifica) {
            $query->whereDate('fecha_compra', '=', $fechaEspecifica);
        }

        // Filtro por rango de fechas
        if ($fechaDesde && $fechaHasta) {
            $query->whereBetween('fecha_compra', [$fechaDesde . ' 00:00:00', $fechaHasta . ' 23:59:59']);
        }

        // Filtro por tipo (con factura o sin factura)
        if ($tipo != 'all') {
            $query->where('factura_compra', $tipo);
        }

        // Ordenar resultados
        $query->orderBy('fecha_compra', $orden);

        // Calcular el total de las compras después de aplicar filtros
        $totalCompra = $query->sum('total_compra');

        // Calcular la cantidad de productos
        $cantidadProductos = $query->withSum('detalles', 'cantidad_compra')->get()->sum('detalles_sum_cantidad_compra');

        // Paginación con preservación de filtros
        $compras = $query->paginate(10)->appends($request->query());

        return view('pages.compras', compact('compras', 'totalCompra', 'cantidadProductos'));
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function create(Request $request) {}

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        abort_if(Gate::denies('compra_registrar'), 403);

        $request->validate([
            'proveedor_id' => 'required|exists:proveedor,id_proveedor',
            'productos' => 'required|array|min:1',
            'productos.*.id' => 'required|exists:producto,id_producto',
            'productos.*.cantidad' => 'required|integer|min:1',
            'productos.*.subtotal' => 'required|numeric|min:0',
            'factura_compra' => 'nullable|boolean',
            'descuento_compra' =>  'required|integer|min:0|max:100',
        ]);

        try {
            DB::beginTransaction();

            // Calcular el total de la compra
            $totalCompra = array_sum(array_column($request->productos, 'subtotal'));

            // Aplicar el porcentaje de descuento si existe
            if ($request->filled('descuento_compra')) {
                $descuento = ($totalCompra * $request->descuento_compra) / 100;
                $totalCompra -= $descuento;
            }

            // Crear la compra
            $compra = Compra::create([
                'id_proveedor' => $request->proveedor_id,
                'fecha_compra' => Carbon::now(), // Guardar la fecha de la compra
                'total_compra' => $totalCompra,
                'descuento_compra' => $request->descuento_compra ?? 0, // Guardar el porcentaje de descuento
                'factura_compra' => $request->factura_compra ?? 0, // Manejar nulos
            ]);

            // Registrar detalles de la compra y actualizar stock
            foreach ($request->productos as $producto) {
                // Crear el detalle de la compra
                $compra->detalles()->create([
                    'id_producto' => $producto['id'],
                    'descripcion' => $producto['descripcion'] ?? null,
                    'cantidad_compra' => $producto['cantidad'],
                    'subtotal_compra' => $producto['subtotal'],
                ]);

                // Actualizar stock del producto
                Producto::where('id_producto', $producto['id'])
                    ->increment('stock', $producto['cantidad']);

                // Registrar en el historial de inventario
                HistorialInventario::create([
                    'id_producto' => $producto['id'],
                    'stock' => $producto['cantidad'],
                    'fecha' => now(),
                    'motivo' => 'Compra',
                    'id_transaccion' => $compra->id_compra,
                    'tipo_transaccion' => 'Compra',
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Compra registrada correctamente.',
                'redirect' => route('compras.registrar'),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al registrar la compra: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        abort_if(Gate::denies('compra_actualizar'), 403);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        abort_if(Gate::denies('compra_actualizar'), 403);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        abort_if(Gate::denies('compra_eliminar'), 403);

        DB::beginTransaction(); // Iniciar la transacción

        try {
            // Obtener la compra con sus detalles y productos asociados
            $compra = Compra::with('detalles.producto')->findOrFail($id);

            foreach ($compra->detalles as $detalle) {
                $producto = $detalle->producto;

                if ($producto) {
                    // Reducir el stock del producto basado en la cantidad comprada
                    $nuevoStock = max(0, $producto->stock - $detalle->cantidad_compra);
                    $producto->update(['stock' => $nuevoStock]);

                    // Eliminar registros de historial de inventario asociados a esta transacción
                    HistorialInventario::where('id_producto', $producto->id_producto)
                        ->where('id_transaccion', $compra->id_compra)
                        ->where('tipo_transaccion', 'Compra')
                        ->delete();
                }
            }

            // Eliminar los detalles de la compra
            $compra->detalles()->delete();

            // Eliminar la compra
            $compra->delete();

            DB::commit(); // Confirmar la transacción

            return redirect()->route('compras.index')->with('success', 'Compra eliminada correctamente. El stock de los productos ha sido ajustado y el historial de inventario actualizado.');
        } catch (\Exception $e) {
            DB::rollBack(); // Revertir la transacción en caso de error

            return redirect()->route('compras.index')->with('error', 'Hubo un problema al eliminar la compra: ' . $e->getMessage());
        }
    }

    public function registrar()
    {
        abort_if(Gate::denies('compra_registrar'), 403);

        $proveedores = Proveedor::all();
        $productos = Producto::all();

        // Retornar la vista con los datos
        return view('pages.registrar_compra', compact('proveedores', 'productos'));
    }

    public function descargarPdf($id)
    {
        // Obtener la compra con sus detalles
        $compra = Compra::with(['proveedor', 'detalles.producto'])->findOrFail($id);

        // Generar el PDF
        $pdf = PDF::loadView('pdf.compras', compact('compra'));

        // Descargar el PDF con un nombre
        return $pdf->download("compra_{$compra->id_compra}.pdf");
    }

    public function dashboard(Request $request)
    {
        abort_if(Gate::denies('compra_dashboard'), 403);

        $fechaInicio = $request->input('fecha_inicio', now()->subMonth()->toDateString());
        $fechaFin = $request->input('fecha_fin', now()->toDateString());

        // Compras por fecha
        $comprasPorFecha = Compra::selectRaw('DATE(fecha_compra) as fecha, SUM(total_compra) as total')
            ->whereBetween('fecha_compra', [$fechaInicio, $fechaFin])
            ->groupBy('fecha')
            ->orderBy('fecha')
            ->get();

        $comprasLabels = $comprasPorFecha->pluck('fecha')->toArray();
        $comprasValues = $comprasPorFecha->pluck('total')->toArray();

        // Productos más comprados
        $productosMasComprados = DetalleCompra::selectRaw('producto.nombre_producto, SUM(detalle_compra.cantidad_compra) as cantidad')
            ->join('producto', 'detalle_compra.id_producto', '=', 'producto.id_producto')
            ->whereBetween('detalle_compra.created_at', [$fechaInicio, $fechaFin])
            ->groupBy('producto.nombre_producto')
            ->orderByDesc('cantidad')
            ->limit(5)
            ->get(); // Devuelve una colección

        $productosLabels = $productosMasComprados->pluck('nombre_producto')->toArray();
        $productosValues = $productosMasComprados->pluck('cantidad')->toArray();

        return view('pages.compras_dashboard', [
            'comprasLabels' => $comprasLabels,
            'comprasValues' => $comprasValues,
            'productosLabels' => $productosLabels,
            'productosValues' => $productosValues,
            'fechaInicio' => $fechaInicio,
            'fechaFin' => $fechaFin
        ]);
    }
}
