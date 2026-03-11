<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\Socio;
use App\Models\Venta;
use App\Models\DetalleVenta;
use App\Models\Pago;
use App\Models\Servicio;
use App\Models\Compra;
use Carbon\Carbon;
use App\Models\HistorialInventario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Barryvdh\DomPDF\Facade\Pdf;

class VentaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        abort_if(Gate::denies('venta_listar'), 403);

        $orden          = $request->get('orden', 'desc');
        $socio          = $request->get('socio');
        $fechaEspecifica = $request->get('fecha');
        $fechaDesde     = $request->get('fecha_desde');
        $fechaHasta     = $request->get('fecha_hasta');
        $credito        = $request->get('credito', 'all');
        $servicio       = $request->get('servicio', 'all');
        $finalizada     = $request->get('finalizada', 'all');

        // ✅ Construir la query una sola vez
        $query = Venta::with(['socio', 'detalles.producto', 'pagos']);

        if (!empty($socio)) {
            $query->whereHas(
                'socio',
                fn($q) =>
                $q->where('nombre_socio', 'like', '%' . $socio . '%')
            );
        }

        if (!empty($fechaEspecifica)) {
            $query->whereDate('fecha_venta', $fechaEspecifica);
        } else {
            // ✅ Aplicar cada fecha independientemente, no exigir ambas
            if (!empty($fechaDesde)) {
                $query->where('fecha_venta', '>=', $fechaDesde . ' 00:00:00');
            }
            if (!empty($fechaHasta)) {
                $query->where('fecha_venta', '<=', $fechaHasta . ' 23:59:59');
            }
        }

        if ($credito !== 'all') {
            $query->where('credito', $credito);
        }
        if ($servicio !== 'all') {
            $query->where('servicio', $servicio);
        }
        if ($finalizada !== 'all') {
            $query->where('finalizada', $finalizada);
        }

        $query->orderBy('fecha_venta', $orden === 'asc' ? 'asc' : 'desc');

        // ✅ Clonar la query para los totales ANTES de paginar
        $queryTotales = clone $query;

        $totalVenta       = $queryTotales->sum('total_venta');
        $cantidadProductos = $queryTotales->withSum('detalles', 'cantidad_venta')
            ->get()
            ->sum('detalles_sum_cantidad_venta');

        $ventas = $query->paginate(10)->appends($request->query());

        return view('pages.ventas', compact('ventas', 'totalVenta', 'cantidadProductos'));
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        abort_if(Gate::denies('venta_registrar'), 403);

        $request->validate([
            'productos' => 'required|array|min:1',
            'productos.*.id' => 'required|exists:producto,id_producto',
            'productos.*.cantidad' => 'required|numeric|min:0',
            'productos.*.subtotal' => 'required|numeric|min:0',
            'total_venta' => 'required|numeric|min:0',
            'monto_pagado' => 'required|numeric|min:0',
            'saldo_pendiente' => 'nullable|numeric|min:0',
            'descuento_venta' => 'nullable|integer|min:0|max:100',
            'fecha_venta' => 'nullable|date',
            'credito' => 'required|boolean',
            'servicio' => 'required|boolean',
            'finalizada' => 'required|boolean',
            'descripcion' => 'nullable|string|max:200',
        ]);

        try {
            DB::beginTransaction();

            $fechaVenta = $request->input('fecha_venta') ?? now();

            $venta = Venta::create([
                'id_usuario' => Auth::id(),
                'id_socio' => $request->input('id_socio', null),
                'fecha_venta' => $fechaVenta,
                'total_venta' => $request->input('total_venta'),
                'descuento_venta' => $request->input('descuento_venta'),
                'credito' => $request->input('credito'),
                'servicio' => $request->input('servicio'),
                'finalizada' => $request->input('finalizada'),
                'descripcion' => $request->input('descripcion', null),
            ]);

            foreach ($request->productos as $producto) {
                DetalleVenta::create([
                    'id_venta' => $venta->id_venta,
                    'id_producto' => $producto['id'],
                    'cantidad_venta' => $producto['cantidad'],
                    'subtotal_venta' => $producto['subtotal'],
                ]);

                Producto::where('id_producto', $producto['id'])
                    ->decrement('stock', $producto['cantidad']);

                HistorialInventario::create([
                    'id_producto' => $producto['id'],
                    'stock' => $producto['cantidad'],
                    'fecha' => now(),
                    'motivo' => 'Venta',
                    'id_transaccion' => $venta->id_venta,
                    'tipo_transaccion' => 'Venta',
                ]);
            }

            Pago::create([
                'id_venta' => $venta->id_venta,
                'fecha_pago' => now(),
                'monto_pagado' => $request->input('monto_pagado'),
                'saldo_pendiente' => $request->input('saldo_pendiente', 0),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Venta registrada correctamente.',
                'redirect' => route('ventas.registrar'),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Error al registrar la venta: ' . $e->getMessage(),
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
        abort_if(Gate::denies('venta_actualizar'), 403);

        $venta = Venta::with(['detalles.producto', 'pagos', 'socio', 'usuario'])
            ->findOrFail($id);

        $productos = Producto::where('stock', '>', 0)->get();

        $socios = Socio::all();

        return view('pages.venta_editar', compact('venta', 'productos', 'socios'));
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
        abort_if(Gate::denies('venta_actualizar'), 403);

        $request->validate([
            'id_socio' => 'nullable|exists:socio,id_socio',
            'productosEliminados' => 'nullable|array',
            'productosEliminados.*' => 'exists:detalle_venta,id_detalle_venta',
            'total_venta' => 'required|numeric|min:0',
            'descuento_venta' => 'nullable|numeric|min:0|max:100',
            'fecha_venta' => 'nullable|date',
            'monto_pagado' => 'nullable|numeric|min:0',
            'saldo_pendiente' => 'nullable|numeric|min:0',
            'descripcion' => 'nullable|string|max:200',
            'credito' => 'required|boolean',
            'servicio' => 'required|boolean',
            'finalizada' => 'required|boolean',
            'productos' => 'required|array|min:1',
            'productos.*.id' => 'required|exists:producto,id_producto',
            'productos.*.cantidad' => 'required|numeric|min:0',
            'productos.*.subtotal' => 'required|numeric|min:0',
            'productos.*.esExistente' => 'required|boolean',
            'productos.*.modificado' => 'required|boolean',
            'pagos' => 'nullable|array',
            'pagos.*.id_pago' => 'required|exists:pago,id_pago',
            'pagos.*.fecha_pago' => 'required|date',
            'pagos.*.monto_pagado' => 'required|numeric|min:0',
            'pagos.*.saldo_pendiente' => 'required|numeric|min:0',
            'pagosEliminados' => 'nullable|array',
            'pagosEliminados.*' => 'exists:pago,id_pago',
        ]);

        try {
            DB::beginTransaction();

            $venta = Venta::findOrFail($id);

            $fechaVenta = $request->fecha_venta ?? Carbon::now();

            // Actualizar información de la venta
            $venta->update([
                'id_socio' => $request->id_socio,
                'total_venta' => $request->total_venta,
                'descuento_venta' => $request->descuento_venta,
                'fecha_venta' => $fechaVenta,
                'credito' => $request->credito,
                'servicio' => $request->servicio,
                'finalizada' => $request->finalizada,
                'descripcion' => $request->descripcion,
            ]);

            // Eliminar pagos
            if (!empty($request->pagosEliminados)) {
                Pago::whereIn('id_pago', $request->pagosEliminados)->delete();
            }

            // Actualizar pagos existentes
            if (!empty($request->pagos)) {
                foreach ($request->pagos as $pago) {
                    Pago::findOrFail($pago['id_pago'])->update([
                        'fecha_pago' => $pago['fecha_pago'],
                        'monto_pagado' => $pago['monto_pagado'],
                        'saldo_pendiente' => $pago['saldo_pendiente'],
                    ]);
                }
            }

            // Crear un nuevo pago si el monto_pagado es mayor a 0
            if ($request->monto_pagado > 0) {
                Pago::create([
                    'id_venta' => $venta->id_venta,
                    'fecha_pago' => $fechaVenta,
                    'monto_pagado' => $request->monto_pagado,
                    'saldo_pendiente' => $request->saldo_pendiente,
                ]);
            }

            // Procesar productos eliminados
            if (!empty($request->productosEliminados)) {
                foreach ($request->productosEliminados as $idDetalle) {
                    $detalle = DetalleVenta::findOrFail($idDetalle);

                    // Reducir el stock del producto
                    Producto::where('id_producto', $detalle->id_producto)
                        ->increment('stock', $detalle->cantidad_venta);

                    // Eliminar el registro del historial de inventario
                    HistorialInventario::where('id_producto', $detalle->id_producto)
                        ->where('id_transaccion', $venta->id_venta)
                        ->where('tipo_transaccion', 'Venta')
                        ->delete();

                    // Eliminar el detalle de la venta
                    $detalle->delete();
                }
            }

            // Procesar productos existentes y nuevos
            foreach ($request->productos as $producto) {
                if ($producto['esExistente']) {
                    // Producto existente
                    $detalle = DetalleVenta::where('id_venta', $venta->id_venta)
                        ->where('id_producto', $producto['id'])
                        ->firstOrFail();

                    // Actualizar el historial de inventario
                    $historial = HistorialInventario::where('id_producto', $producto['id'])
                        ->where('id_transaccion', $venta->id_venta)
                        ->where('tipo_transaccion', 'Venta')
                        ->first();

                    if ($historial) {
                        $historial->update([
                            'fecha' => $fechaVenta,
                        ]);
                    }

                    if ($producto['modificado']) {
                        // Calcular la diferencia de stock
                        $diferencia = $producto['cantidad'] - $detalle->cantidad_venta;

                        if ($diferencia != 0) {
                            // Actualizar el stock del producto
                            Producto::where('id_producto', $producto['id'])
                                ->decrement('stock', $diferencia);

                            if ($historial) {
                                $historial->update([
                                    'stock' => $producto['cantidad'],
                                ]);
                            }
                        }

                        // Actualizar el detalle de la venta
                        $detalle->update([
                            'cantidad_venta' => $producto['cantidad'],
                            'subtotal_venta' => $producto['subtotal'],
                        ]);
                    }
                } else {
                    // Producto nuevo
                    $venta->detalles()->create([
                        'id_producto' => $producto['id'],
                        'cantidad_venta' => $producto['cantidad'],
                        'subtotal_venta' => $producto['subtotal'],
                    ]);

                    // Reducir el stock del producto
                    Producto::where('id_producto', $producto['id'])
                        ->decrement('stock', $producto['cantidad']);

                    // Registrar en el historial de inventario
                    HistorialInventario::create([
                        'id_producto' => $producto['id'],
                        'stock' => $producto['cantidad'],
                        'fecha' => $fechaVenta,
                        'motivo' => 'Venta',
                        'id_transaccion' => $venta->id_venta,
                        'tipo_transaccion' => 'Venta',
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Venta actualizada correctamente.',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar la venta: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        abort_if(Gate::denies('venta_eliminar'), 403);

        DB::beginTransaction(); // Iniciar la transacción

        try {
            // Obtener la venta con sus detalles y productos asociados
            $venta = Venta::with('detalles.producto')->findOrFail($id);

            foreach ($venta->detalles as $detalle) {
                $producto = $detalle->producto;

                if ($producto) {
                    // Incrementar el stock del producto basado en la cantidad vendida
                    $producto->increment('stock', $detalle->cantidad_venta);

                    // Eliminar registros de historial de inventario asociados a esta transacción
                    HistorialInventario::where('id_producto', $producto->id_producto)
                        ->where('id_transaccion', $venta->id_venta)
                        ->where('tipo_transaccion', 'Venta')
                        ->delete();
                }
            }

            // Eliminar los detalles de la venta
            $venta->detalles()->delete();

            // Eliminar los pagos asociados a la venta
            $venta->pagos()->delete();

            // Eliminar la venta
            $venta->delete();

            DB::commit(); // Confirmar la transacción

            return redirect()->route('ventas.index')->with('success', 'Venta eliminada correctamente. El stock de los productos ha sido ajustado y el historial de inventario actualizado.');
        } catch (\Exception $e) {
            DB::rollBack(); // Revertir la transacción en caso de error

            return redirect()->route('ventas.index')->with('error', 'Hubo un problema al eliminar la venta: ' . $e->getMessage());
        }
    }

    public function registrar()
    {
        abort_if(Gate::denies('venta_registrar'), 403);

        $productos = Producto::where('stock', '>', 0)->get();
        $socios = Socio::all();
        $usuario = Auth::user(); // Obtenemos el usuario autenticado

        return view('pages.registrar_venta', compact('productos', 'socios', 'usuario'));
    }

    public function descargarPdf($id)
    {
        $venta = Venta::with(['socio', 'detalles.producto', 'pagos'])->findOrFail($id);

        // Generar el PDF
        $pdf = PDF::loadView('pdf.ventas', compact('venta'));

        return $pdf->download("venta_{$venta->id_venta}.pdf");
    }

    public function generarReporteUtilidad(Request $request)
    {
        abort_if(Gate::denies('venta_reporte_utilidad'), 403);

        $filtros = [
            'fecha'       => $request->get('fecha'),
            'fecha_desde' => $request->get('fecha_desde'),
            'fecha_hasta' => $request->get('fecha_hasta'),
            'socio'       => $request->get('socio'),
            'credito'     => $request->get('credito', 'all'),
            'servicio'    => $request->get('servicio', 'all'),
            'finalizada'  => $request->get('finalizada', 'all'),
            'orden'       => $request->get('orden', 'desc'),
        ];

        $queryBase = $this->buildVentasQuery($filtros);

        $ventasGlobales = (clone $queryBase)
            ->with(['detalles.producto.historialPreciosCompra'])
            ->get();

        $totalesGlobales = $this->calcularTotalesDeColeccion($ventasGlobales);

        $ventas = (clone $queryBase)
            ->with(['detalles.producto.historialPrecios', 'detalles.producto.historialPreciosCompra'])
            ->paginate(10);

        $ventas->getCollection()->transform(function ($venta) {
            $sumaSubtotales = $venta->detalles->sum('subtotal_venta');

            $factorDescuento = $sumaSubtotales > 0
                ? $venta->total_venta / $sumaSubtotales
                : 1;

            $venta->detalles->transform(function ($detalle) use ($venta, $factorDescuento) {
                // ✅ PHP 7.4 compatible - Reemplazar ?-> con comprobación tradicional
                $historialFiltrado = $detalle->producto->historialPreciosCompra
                    ->filter(function ($h) use ($venta) {
                        return $h->fecha_inicio <= $venta->fecha_venta
                            && (is_null($h->fecha_fin) || $h->fecha_fin >= $venta->fecha_venta);
                    })
                    ->sortByDesc('fecha_inicio')
                    ->first();

                $precioCompra = $historialFiltrado ? $historialFiltrado->precio_compra : 0;

                $subtotalConDescuento = $detalle->subtotal_venta * $factorDescuento;

                $detalle->subtotal_venta_original = $detalle->subtotal_venta;
                $detalle->subtotal_venta = $subtotalConDescuento;

                $detalle->subtotal_costo    = $detalle->cantidad_venta * $precioCompra;
                $detalle->subtotal_utilidad = $subtotalConDescuento - $detalle->subtotal_costo;

                if ($venta->credito) {
                    $detalle->efectivo      = 0;
                    $detalle->monto_credito = $subtotalConDescuento;
                } else {
                    $detalle->efectivo      = $subtotalConDescuento;
                    $detalle->monto_credito = 0;
                }

                return $detalle;
            });
            return $venta;
        });

        $filtrosLegibles = $this->filtrosATexto($filtros);

        return view(
            'pages.venta_reporte_utilidad',
            compact('ventas', 'filtros', 'filtrosLegibles', 'totalesGlobales')
        );
    }

    // ✅ Query base compartida entre index, reporte y PDF — sin duplicar lógica
    private function buildVentasQuery(array $filtros)
    {
        return Venta::query()
            ->when(
                !empty($filtros['fecha']),
                fn($q) =>
                $q->whereDate('fecha_venta', $filtros['fecha'])
            )
            ->when(
                empty($filtros['fecha']) && !empty($filtros['fecha_desde']),
                fn($q) =>
                $q->where('fecha_venta', '>=', $filtros['fecha_desde'] . ' 00:00:00')
            )
            ->when(
                empty($filtros['fecha']) && !empty($filtros['fecha_hasta']),
                fn($q) =>
                $q->where('fecha_venta', '<=', $filtros['fecha_hasta'] . ' 23:59:59')
            )
            ->when(
                !empty($filtros['socio']),
                fn($q) =>
                $q->whereHas(
                    'socio',
                    fn($q2) =>
                    $q2->where('nombre_socio', 'LIKE', '%' . $filtros['socio'] . '%')
                )
            )
            ->when(
                $filtros['credito'] !== 'all',
                fn($q) =>
                $q->where('credito', $filtros['credito'])
            )
            ->when(
                $filtros['servicio'] !== 'all',
                fn($q) =>
                $q->where('servicio', $filtros['servicio'])
            )
            ->when(
                $filtros['finalizada'] !== 'all',
                fn($q) =>
                $q->where('finalizada', $filtros['finalizada'])
            )
            ->orderBy('fecha_venta', $filtros['orden'] === 'asc' ? 'asc' : 'desc');
    }

    private function calcularTotalesDeColeccion($ventas): array
    {
        $totales = [
            'totalEfectivo' => 0,
            'totalCredito'  => 0,
            'totalVentas'   => 0,
            'totalCosto'    => 0,
            'totalUtilidad' => 0,
            'totalUtilidadEfectivo' => 0, // ✅ Nuevo campo
        ];

        foreach ($ventas as $venta) {
            $sumaSubtotales = $venta->detalles->sum('subtotal_venta');
            $factorDescuento = $sumaSubtotales > 0
                ? $venta->total_venta / $sumaSubtotales
                : 1;

            foreach ($venta->detalles as $detalle) {
                $historialFiltrado = $detalle->producto->historialPreciosCompra
                    ->filter(function ($h) use ($venta) {
                        return $h->fecha_inicio <= $venta->fecha_venta
                            && (is_null($h->fecha_fin) || $h->fecha_fin >= $venta->fecha_venta);
                    })
                    ->sortByDesc('fecha_inicio')
                    ->first();

                $precioCompra = $historialFiltrado ? $historialFiltrado->precio_compra : 0;

                $subtotalConDescuento = $detalle->subtotal_venta * $factorDescuento;

                $costo    = $detalle->cantidad_venta * $precioCompra;
                $utilidad = $subtotalConDescuento - $costo;

                $totales['totalVentas']   += $subtotalConDescuento;
                $totales['totalCosto']    += $costo;
                $totales['totalUtilidad'] += $utilidad;

                if ($venta->credito) {
                    $totales['totalCredito'] += $subtotalConDescuento;
                } else {
                    $totales['totalEfectivo'] += $subtotalConDescuento;
                    // ✅ Sumar utilidad solo si es venta en efectivo
                    $totales['totalUtilidadEfectivo'] += $utilidad;
                }
            }
        }

        return $totales;
    }

    private function filtrosATexto(array $filtros)
    {
        $legibles = [];

        if (!empty($filtros['fecha'])) {
            $legibles['Fecha específica'] = $filtros['fecha'];
        }
        if (!empty($filtros['fecha_desde'])) {
            $legibles['Fecha desde'] = $filtros['fecha_desde'];
        }
        if (!empty($filtros['fecha_hasta'])) {
            $legibles['Fecha hasta'] = $filtros['fecha_hasta'];
        }
        if (!empty($filtros['socio'])) {
            $legibles['Socio'] = $filtros['socio'];
        }
        if ($filtros['credito'] !== 'all') {
            $legibles['Crédito'] = $filtros['credito'] == 1 ? 'Sí' : 'No';
        }
        if ($filtros['servicio'] !== 'all') {
            $legibles['Servicio'] = $filtros['servicio'] == 1 ? 'Sí' : 'No';
        }
        if ($filtros['finalizada'] !== 'all') {
            $legibles['Finalizada'] = $filtros['finalizada'] == 1 ? 'Sí' : 'No';
        }
        $legibles['Orden'] = $filtros['orden'] === 'asc' ? 'Más antigua' : 'Más reciente';

        return collect($legibles);
    }

    public function descargarReportePdf(Request $request)
    {
        $filtros = [
            'fecha'       => $request->get('fecha'),
            'fecha_desde' => $request->get('fecha_desde'),
            'fecha_hasta' => $request->get('fecha_hasta'),
            'socio'       => $request->get('socio'),
            'credito'     => $request->get('credito', 'all'),
            'servicio'    => $request->get('servicio', 'all'),
            'finalizada'  => $request->get('finalizada', 'all'),
            'orden'       => $request->get('orden', 'desc'),
        ];

        $ventas = $this->buildVentasQuery($filtros)
            ->with(['detalles.producto.historialPrecios', 'detalles.producto.historialPreciosCompra'])
            ->get();

        $totalEfectivo = 0;
        $totalCredito  = 0;
        $totalVentas   = 0;
        $totalCosto    = 0;
        $totalUtilidad = 0;
        $totalUtilidadEfectivo = 0; // ✅ Nuevo acumulador

        $ventas->transform(function ($venta) use (&$totalEfectivo, &$totalCredito, &$totalVentas, &$totalCosto, &$totalUtilidad, &$totalUtilidadEfectivo) {
            $sumaSubtotales = $venta->detalles->sum('subtotal_venta');
            $factorDescuento = $sumaSubtotales > 0
                ? $venta->total_venta / $sumaSubtotales
                : 1;

            $venta->detalles->transform(function ($detalle) use ($venta, $factorDescuento, &$totalEfectivo, &$totalCredito, &$totalVentas, &$totalCosto, &$totalUtilidad, &$totalUtilidadEfectivo) {
                $historialFiltrado = $detalle->producto->historialPreciosCompra
                    ->filter(function ($h) use ($venta) {
                        return $h->fecha_inicio <= $venta->fecha_venta
                            && (is_null($h->fecha_fin) || $h->fecha_fin >= $venta->fecha_venta);
                    })
                    ->sortByDesc('fecha_inicio')
                    ->first();

                $precioCompra = $historialFiltrado ? $historialFiltrado->precio_compra : 0;

                $subtotalConDescuento = $detalle->subtotal_venta * $factorDescuento;

                $detalle->subtotal_venta = $subtotalConDescuento;
                $detalle->subtotal_costo    = $detalle->cantidad_venta * $precioCompra;
                $detalle->subtotal_utilidad = $subtotalConDescuento - $detalle->subtotal_costo;

                if ($venta->credito) {
                    $detalle->efectivo      = 0;
                    $detalle->monto_credito = $subtotalConDescuento;
                    $totalCredito          += $subtotalConDescuento;
                } else {
                    $detalle->efectivo      = $subtotalConDescuento;
                    $detalle->monto_credito = 0;
                    $totalEfectivo         += $subtotalConDescuento;
                    // ✅ Sumar utilidad solo si es efectivo
                    $totalUtilidadEfectivo += $detalle->subtotal_utilidad;
                }

                $totalVentas   += $subtotalConDescuento;
                $totalCosto    += $detalle->subtotal_costo;
                $totalUtilidad += $detalle->subtotal_utilidad;

                return $detalle;
            });
            return $venta;
        });

        $filtrosLegibles = $this->filtrosATexto($filtros);

        $pdf = Pdf::loadView(
            'pdf.venta_reporte_utilidad_pdf',
            compact(
                'ventas',
                'filtrosLegibles',
                'totalEfectivo',
                'totalCredito',
                'totalVentas',
                'totalCosto',
                'totalUtilidad',
                'totalUtilidadEfectivo'
            )
        );

        return $pdf->download('reporte_utilidad.pdf');
    }

    public function dashboard(Request $request)
    {
        abort_if(Gate::denies('venta_dashboard'), 403);

        $fechaInicio = $request->input('fecha_inicio', now()->subMonth()->toDateString());
        $fechaFin    = $request->input('fecha_fin',    now()->toDateString());

        // ✅ Validar que fecha_inicio <= fecha_fin
        if (Carbon::parse($fechaInicio)->gt(Carbon::parse($fechaFin))) {
            return redirect()->back()->with(
                'error',
                'La fecha de inicio debe ser menor o igual a la fecha de fin.'
            );
        }

        // ✅ Incluir todo el día final con endOfDay
        $fechaFinCompleta = Carbon::parse($fechaFin)->endOfDay();

        // ── Ventas por fecha ─────────────────────────────────────────────────
        $ventasPorFecha = Venta::selectRaw('DATE(fecha_venta) as fecha, SUM(total_venta) as total')
            ->where('fecha_venta', '>=', $fechaInicio)
            ->where('fecha_venta', '<=', $fechaFinCompleta)
            ->groupBy('fecha')
            ->orderBy('fecha')
            ->get();

        $ventasLabels = $ventasPorFecha->pluck('fecha')->toArray();
        $ventasValues = $ventasPorFecha->pluck('total')->toArray();

        // ── Productos más vendidos ───────────────────────────────────────────
        // ✅ Filtrar por fecha_venta de la venta, no por created_at del detalle
        $productosMasVendidos = DetalleVenta::selectRaw('
            producto.id_producto,
            producto.nombre_producto,
            SUM(detalle_venta.cantidad_venta) as cantidad
        ')
            ->join('venta', 'detalle_venta.id_venta', '=', 'venta.id_venta')
            ->join('producto', 'detalle_venta.id_producto', '=', 'producto.id_producto')
            ->where('venta.fecha_venta', '>=', $fechaInicio)
            ->where('venta.fecha_venta', '<=', $fechaFinCompleta)
            ->groupBy('producto.id_producto', 'producto.nombre_producto')
            ->orderByDesc('cantidad')
            ->limit(5) // ✅ Top 5 productos — configurable si se desea
            ->get();

        $productosLabels = $productosMasVendidos->pluck('nombre_producto')->toArray();
        $productosValues = $productosMasVendidos->pluck('cantidad')->toArray();

        // ── Ventas vs Compras (Ingresos vs Egresos) ──────────────────────────
        $ventasTotales  = Venta::where('fecha_venta', '>=', $fechaInicio)
            ->where('fecha_venta', '<=', $fechaFinCompleta)
            ->sum('total_venta');

        $comprasTotales = Compra::where('fecha_compra', '>=', $fechaInicio)
            ->where('fecha_compra', '<=', $fechaFinCompleta)
            ->sum('total_compra');

        return view('pages.ventas_dashboard', [
            'ventasLabels'    => $ventasLabels,
            'ventasValues'    => $ventasValues,
            'productosLabels' => $productosLabels,
            'productosValues' => $productosValues,
            'ventasTotales'   => $ventasTotales,
            'comprasTotales'  => $comprasTotales,
            'fechaInicio'     => $fechaInicio,
            'fechaFin'        => $fechaFin,
        ]);
    }
}
