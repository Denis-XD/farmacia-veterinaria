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

        $orden = $request->get('orden', 'desc');
        $socio = $request->get('socio', null);
        $fechaEspecifica = $request->get('fecha', null);
        $fechaDesde = $request->get('fecha_desde', null);
        $fechaHasta = $request->get('fecha_hasta', null);
        $credito = $request->get('credito', 'all');
        $servicio = $request->get('servicio', 'all');
        $finalizada = $request->get('finalizada', 'all');

        $query = Venta::with(['socio', 'detalles.producto', 'pagos']);

        if (!empty($socio)) {
            $query->whereHas('socio', function ($q) use ($socio) {
                $q->where('nombre_socio', 'like', '%' . $socio . '%');
            });
        }

        if ($fechaEspecifica) {
            $query->whereDate('fecha_venta', '=', $fechaEspecifica);
        }

        if ($fechaDesde && $fechaHasta) {
            $query->whereBetween('fecha_venta', [$fechaDesde . ' 00:00:00', $fechaHasta . ' 23:59:59']);
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

        $query->orderBy('fecha_venta', $orden);

        // Calcular el total de ventas
        $totalVenta = $query->sum('total_venta');

        // Calcular la cantidad de productos vendidos
        $cantidadProductos = $query->withSum('detalles', 'cantidad_venta')->get()->sum('detalles_sum_cantidad_venta');

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

                    if ($producto['modificado']) {
                        // Calcular la diferencia de stock
                        $diferencia = $producto['cantidad'] - $detalle->cantidad_venta;

                        if ($diferencia != 0) {
                            // Actualizar el stock del producto
                            Producto::where('id_producto', $producto['id'])
                                ->decrement('stock', $diferencia);

                            // Actualizar el historial de inventario
                            $historial = HistorialInventario::where('id_producto', $producto['id'])
                                ->where('id_transaccion', $venta->id_venta)
                                ->where('tipo_transaccion', 'Venta')
                                ->first();

                            if ($historial) {
                                $historial->update([
                                    'stock' => $producto['cantidad'],
                                    'fecha' => $fechaVenta,
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

        $filtros = $request->all();

        // Valores por defecto para filtros booleanos y orden
        $filtros['credito'] = $filtros['credito'] ?? 'all';
        $filtros['servicio'] = $filtros['servicio'] ?? 'all';
        $filtros['finalizada'] = $filtros['finalizada'] ?? 'all';
        $filtros['orden'] = $filtros['orden'] ?? 'desc';

        $totalesGlobales = $this->calcularTotalesGlobales($filtros);

        // Consulta base de las ventas con detalles y productos
        $ventas = Venta::with(['detalles.producto.historialPrecios', 'detalles.producto.historialPreciosCompra'])
            ->when(!empty($filtros['fecha']), function ($query) use ($filtros) {
                $query->whereDate('fecha_venta', $filtros['fecha']);
            })
            ->when(!empty($filtros['fecha_desde']), function ($query) use ($filtros) {
                $query->whereDate('fecha_venta', '>=', $filtros['fecha_desde']);
            })
            ->when(!empty($filtros['fecha_hasta']), function ($query) use ($filtros) {
                $query->whereDate('fecha_venta', '<=', $filtros['fecha_hasta']);
            })
            ->when(!empty($filtros['socio']), function ($query) use ($filtros) {
                $query->whereHas('socio', function ($q) use ($filtros) {
                    $q->where('nombre_socio', 'LIKE', '%' . $filtros['socio'] . '%');
                });
            })
            ->when($filtros['credito'] !== 'all', function ($query) use ($filtros) {
                $query->where('credito', $filtros['credito']);
            })
            ->when($filtros['servicio'] !== 'all', function ($query) use ($filtros) {
                $query->where('servicio', $filtros['servicio']);
            })
            ->when($filtros['finalizada'] !== 'all', function ($query) use ($filtros) {
                $query->where('finalizada', $filtros['finalizada']);
            })
            ->orderBy('fecha_venta', $filtros['orden'] === 'asc' ? 'asc' : 'desc')
            ->paginate(10);


        // Procesar cada venta
        $ventas->getCollection()->transform(function ($venta) {
            $venta->detalles->transform(function ($detalle) use ($venta) {
                // Obtener precio de venta y compra según fecha de la venta
                $precioVenta = $detalle->producto->historialPrecios()
                    ->where('fecha_inicio', '<=', $venta->fecha_venta)
                    ->where(function ($query) use ($venta) {
                        $query->whereNull('fecha_fin')
                            ->orWhere('fecha_fin', '>=', $venta->fecha_venta);
                    })
                    ->orderBy('fecha_inicio', 'desc')
                    ->value('precio_venta') ?? 0;

                $precioCompra = $detalle->producto->historialPreciosCompra()
                    ->where('fecha_inicio', '<=', $venta->fecha_venta)
                    ->where(function ($query) use ($venta) {
                        $query->whereNull('fecha_fin')
                            ->orWhere('fecha_fin', '>=', $venta->fecha_venta);
                    })
                    ->orderBy('fecha_inicio', 'desc')
                    ->value('precio_compra') ?? 0;

                // Calcular valores
                //$detalle->subtotal_venta = $detalle->cantidad_venta * $precioVenta;
                $detalle->subtotal_costo = $detalle->cantidad_venta * $precioCompra;
                $detalle->subtotal_utilidad = $detalle->subtotal_venta - $detalle->subtotal_costo;

                // Ajustar efectivo y crédito según el campo "credito" de la venta
                if ($venta->credito) {
                    $detalle->efectivo = 0;
                    $detalle->credito = $detalle->subtotal_venta;
                } else {
                    $detalle->efectivo = $detalle->subtotal_venta;
                    $detalle->credito = 0;
                }

                return $detalle;
            });

            return $venta;
        });

        return view('pages.venta_reporte_utilidad', compact('ventas', 'filtros', 'totalesGlobales'));
    }

    private function calcularTotalesGlobales(array $filtros)
    {
        $totales = [
            'totalEfectivo' => 0,
            'totalCredito' => 0,
            'totalVentas' => 0,
            'totalCosto' => 0,
            'totalUtilidad' => 0,
        ];

        $ventasGlobales = Venta::with(['detalles.producto.historialPrecios', 'detalles.producto.historialPreciosCompra'])
            ->when(!empty($filtros['fecha']), function ($query) use ($filtros) {
                $query->whereDate('fecha_venta', $filtros['fecha']);
            })
            ->when(!empty($filtros['fecha_desde']), function ($query) use ($filtros) {
                $query->whereDate('fecha_venta', '>=', $filtros['fecha_desde']);
            })
            ->when(!empty($filtros['fecha_hasta']), function ($query) use ($filtros) {
                $query->whereDate('fecha_venta', '<=', $filtros['fecha_hasta']);
            })
            ->when(!empty($filtros['socio']), function ($query) use ($filtros) {
                $query->whereHas('socio', function ($q) use ($filtros) {
                    $q->where('nombre_socio', 'LIKE', '%' . $filtros['socio'] . '%');
                });
            })
            ->when($filtros['credito'] !== 'all', function ($query) use ($filtros) {
                $query->where('credito', $filtros['credito']);
            })
            ->when($filtros['servicio'] !== 'all', function ($query) use ($filtros) {
                $query->where('servicio', $filtros['servicio']);
            })
            ->when($filtros['finalizada'] !== 'all', function ($query) use ($filtros) {
                $query->where('finalizada', $filtros['finalizada']);
            })
            ->get();

        foreach ($ventasGlobales as $venta) {
            foreach ($venta->detalles as $detalle) {
                $precioCompra = $detalle->producto->historialPreciosCompra()
                    ->where('fecha_inicio', '<=', $venta->fecha_venta)
                    ->where(function ($query) use ($venta) {
                        $query->whereNull('fecha_fin')
                            ->orWhere('fecha_fin', '>=', $venta->fecha_venta);
                    })
                    ->orderBy('fecha_inicio', 'desc')
                    ->value('precio_compra') ?? 0;

                $subtotalCosto = $detalle->cantidad_venta * $precioCompra;
                $subtotalUtilidad = $detalle->subtotal_venta - $subtotalCosto;

                if ($venta->credito) {
                    $totales['totalCredito'] += $detalle->subtotal_venta;
                } else {
                    $totales['totalEfectivo'] += $detalle->subtotal_venta;
                }

                $totales['totalVentas'] += $detalle->subtotal_venta;
                $totales['totalCosto'] += $subtotalCosto;
                $totales['totalUtilidad'] += $subtotalUtilidad;
            }
        }

        return $totales;
    }

    public function descargarReportePdf(Request $request)
    {
        // Obtener filtros aplicados y establecer valores predeterminados
        $filtros = $request->all();
        $filtros['credito'] = $filtros['credito'] ?? 'all';
        $filtros['servicio'] = $filtros['servicio'] ?? 'all';
        $filtros['finalizada'] = $filtros['finalizada'] ?? 'all';
        $filtros['orden'] = $filtros['orden'] ?? 'desc';

        // Traducción de valores para filtros booleanos
        if ($filtros['credito'] !== 'all') {
            $filtros['credito'] = $filtros['credito'] == 1 ? 'Sí' : 'No';
        } else {
            $filtros['credito'] = 'Todas';
        }

        if ($filtros['servicio'] !== 'all') {
            $filtros['servicio'] = $filtros['servicio'] == 1 ? 'Sí' : 'No';
        } else {
            $filtros['servicio'] = 'Todas';
        }

        if ($filtros['finalizada'] !== 'all') {
            $filtros['finalizada'] = $filtros['finalizada'] == 1 ? 'Sí' : 'No';
        } else {
            $filtros['finalizada'] = 'Todas';
        }

        if ($filtros['orden'] === 'asc') {
            $filtros['orden'] = 'Más antigua';
        } else {
            $filtros['orden'] = 'Más reciente';
        }

        $totalEfectivo = 0;
        $totalCredito = 0;
        $totalVentas = 0;
        $totalCosto = 0;
        $totalUtilidad = 0;

        // Consulta base de las ventas con detalles y productos
        $ventas = Venta::with(['detalles.producto.historialPrecios', 'detalles.producto.historialPreciosCompra'])
            ->when(!empty($filtros['fecha']), function ($query) use ($filtros) {
                $query->whereDate('fecha_venta', $filtros['fecha']);
            })
            ->when(!empty($filtros['fecha_desde']), function ($query) use ($filtros) {
                $query->whereDate('fecha_venta', '>=', $filtros['fecha_desde']);
            })
            ->when(!empty($filtros['fecha_hasta']), function ($query) use ($filtros) {
                $query->whereDate('fecha_venta', '<=', $filtros['fecha_hasta']);
            })
            ->when(!empty($filtros['socio']), function ($query) use ($filtros) {
                $query->whereHas('socio', function ($q) use ($filtros) {
                    $q->where('nombre_socio', 'LIKE', '%' . $filtros['socio'] . '%');
                });
            })
            ->when($filtros['credito'] !== 'Todas', function ($query) use ($filtros) {
                $query->where('credito', $filtros['credito'] === 'Sí' ? 1 : 0);
            })
            ->when($filtros['servicio'] !== 'Todas', function ($query) use ($filtros) {
                $query->where('servicio', $filtros['servicio'] === 'Sí' ? 1 : 0);
            })
            ->when($filtros['finalizada'] !== 'Todas', function ($query) use ($filtros) {
                $query->where('finalizada', $filtros['finalizada'] === 'Sí' ? 1 : 0);
            })
            ->orderBy('fecha_venta', $filtros['orden'] === 'asc' ? 'asc' : 'desc')
            ->get();

        $ventas->transform(function ($venta) use (&$totalEfectivo, &$totalCredito, &$totalVentas, &$totalCosto, &$totalUtilidad) {
            $venta->detalles->transform(function ($detalle) use ($venta, &$totalEfectivo, &$totalCredito, &$totalVentas, &$totalCosto, &$totalUtilidad) {
                // Obtener precio de venta y compra según fecha de la venta
                $precioVenta = $detalle->producto->historialPrecios()
                    ->where('fecha_inicio', '<=', $venta->fecha_venta)
                    ->where(function ($query) use ($venta) {
                        $query->whereNull('fecha_fin')
                            ->orWhere('fecha_fin', '>=', $venta->fecha_venta);
                    })
                    ->orderBy('fecha_inicio', 'desc')
                    ->value('precio_venta') ?? 0;

                $precioCompra = $detalle->producto->historialPreciosCompra()
                    ->where('fecha_inicio', '<=', $venta->fecha_venta)
                    ->where(function ($query) use ($venta) {
                        $query->whereNull('fecha_fin')
                            ->orWhere('fecha_fin', '>=', $venta->fecha_venta);
                    })
                    ->orderBy('fecha_inicio', 'desc')
                    ->value('precio_compra') ?? 0;

                // Calcular valores
                //$detalle->subtotal_venta = $detalle->cantidad_venta * $precioVenta;
                $detalle->subtotal_costo = $detalle->cantidad_venta * $precioCompra;
                $detalle->subtotal_utilidad = $detalle->subtotal_venta - $detalle->subtotal_costo;

                if ($venta->credito) {
                    $detalle->efectivo = 0;
                    $detalle->credito = $detalle->subtotal_venta;
                    $totalCredito += $detalle->subtotal_venta;
                } else {
                    $detalle->efectivo = $detalle->subtotal_venta;
                    $detalle->credito = 0;
                    $totalEfectivo += $detalle->subtotal_venta;
                }

                $totalVentas += $detalle->subtotal_venta;
                $totalCosto += $detalle->subtotal_costo;
                $totalUtilidad += $detalle->subtotal_utilidad;

                return $detalle;
            });

            return $venta;
        });

        // Generar PDF
        $pdf = Pdf::loadView('pdf.venta_reporte_utilidad_pdf', compact('ventas', 'filtros', 'totalEfectivo', 'totalCredito', 'totalVentas', 'totalCosto', 'totalUtilidad'));

        return $pdf->download('reporte_utilidad.pdf');
    }

    public function dashboard(Request $request)
    {
        abort_if(Gate::denies('venta_dashboard'), 403);

        $fechaInicio = $request->input('fecha_inicio', now()->subMonth()->toDateString());
        $fechaFin = $request->input('fecha_fin', now()->toDateString());

        // Ventas por fecha
        $ventasPorFecha = Venta::selectRaw('DATE(fecha_venta) as fecha, SUM(total_venta) as total')
            ->whereBetween('fecha_venta', [$fechaInicio, $fechaFin])
            ->groupBy('fecha')
            ->orderBy('fecha')
            ->get();

        $ventasLabels = $ventasPorFecha->pluck('fecha')->toArray();
        $ventasValues = $ventasPorFecha->pluck('total')->toArray();

        // Productos más vendidos
        $productosMasVendidos = DetalleVenta::selectRaw('producto.nombre_producto, SUM(detalle_venta.cantidad_venta) as cantidad')
            ->join('producto', 'detalle_venta.id_producto', '=', 'producto.id_producto')
            ->whereBetween('detalle_venta.created_at', [$fechaInicio, $fechaFin])
            ->groupBy('producto.nombre_producto')
            ->orderByDesc('cantidad')
            ->limit(5)
            ->get();

        $productosLabels = $productosMasVendidos->pluck('nombre_producto')->toArray();
        $productosValues = $productosMasVendidos->pluck('cantidad')->toArray();

        // Ventas vs Compras (Ingresos vs Egresos)
        $ventasTotales = Venta::whereBetween('fecha_venta', [$fechaInicio, $fechaFin])->sum('total_venta');
        $comprasTotales = Compra::whereBetween('fecha_compra', [$fechaInicio, $fechaFin])->sum('total_compra');

        return view('pages.ventas_dashboard', [
            'ventasLabels' => $ventasLabels,
            'ventasValues' => $ventasValues,
            'productosLabels' => $productosLabels,
            'productosValues' => $productosValues,
            'ventasTotales' => $ventasTotales,
            'comprasTotales' => $comprasTotales,
            'fechaInicio' => $fechaInicio,
            'fechaFin' => $fechaFin,
        ]);
    }
}
