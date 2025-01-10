<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\Socio;
use App\Models\Venta;
use App\Models\DetalleVenta;
use App\Models\Pago;
use App\Models\Servicio;
use App\Models\Compra;
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
            'productos.*.cantidad' => 'required|integer|min:1',
            'productos.*.subtotal' => 'required|numeric|min:0',
            'total_venta' => 'required|numeric|min:0',
            'monto_pagado' => 'required|numeric|min:0',
            'saldo_pendiente' => 'nullable|numeric|min:0',
            'credito' => 'required|boolean',
            'servicio' => 'required|boolean',
            'finalizada' => 'required|boolean',
            'descripcion' => 'nullable|string|max:200',
        ]);

        try {
            DB::beginTransaction();

            $venta = Venta::create([
                'id_usuario' => Auth::id(),
                'id_socio' => $request->input('id_socio', null),
                'fecha_venta' => now(),
                'total_venta' => $request->input('total_venta'),
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

        $venta = Venta::with(['detalles.producto', 'pagos', 'servicioVeterinario'])->findOrFail($id);
        //return response()->json(['venta' => $venta], 200);
        return view('pages.venta_editar', compact('venta'));
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

        $request->validate(
            [
                'credito' => 'required|boolean',
                'servicio' => 'required|boolean',
                'finalizada' => 'required|boolean',
                'nuevo_pago' => 'nullable|numeric|min:0',
                'saldo_pendiente' => 'nullable|numeric|min:0',
                'tratamiento' => 'nullable|string|max:200|required_if:servicio,1',
                'fecha_servicio' => 'nullable|date|required_if:servicio,1',
                'costo_servicio' => 'nullable|numeric|min:0|required_if:servicio,1',
                'costo_combustible' => 'nullable|numeric|min:0|required_if:servicio,1',
            ],
            [
                'tratamiento.required_if' => 'El campo tratamiento es obligatorio cuando el servicio es "Sí".',
                'fecha_servicio.required_if' => 'El campo fecha servicio es obligatorio cuando el servicio es "Sí".',
                'costo_servicio.required_if' => 'El campo costo servicio es obligatorio cuando el servicio es "Sí".',
                'costo_combustible.required_if' => 'El campo costo combustible es obligatorio cuando el servicio es "Sí".',
            ]
        );

        try {
            DB::beginTransaction();

            // Actualizar los datos de la venta
            $venta = Venta::findOrFail($id);
            $venta->credito = $request->credito;
            $venta->servicio = $request->servicio;
            $venta->finalizada = $request->finalizada;
            $venta->save();

            // Crear un nuevo pago si se añadió
            if ($request->nuevo_pago && $request->nuevo_pago > 0) {
                Pago::create([
                    'id_venta' => $venta->id_venta,
                    'fecha_pago' => now(),
                    'monto_pagado' => $request->nuevo_pago,
                    'saldo_pendiente' => $request->saldo_pendiente,
                ]);
            }

            // Verificar si el servicio es true
            if ($request->servicio) {
                // Si servicio es true, verificar si ya existe un servicio
                $servicio = Servicio::where('id_venta', $venta->id_venta)->first();

                if ($servicio) {
                    // Actualizar el servicio existente
                    $servicio->update([
                        'tratamiento' => $request->tratamiento,
                        'fecha_servicio' => $request->fecha_servicio,
                        'costo_servicio' => $request->costo_servicio,
                        'costo_combustible' => $request->costo_combustible,
                        'total_servicio' => $request->costo_servicio + $request->costo_combustible + $venta->total_venta,
                    ]);
                } else {
                    // Crear un nuevo servicio
                    Servicio::create([
                        'id_venta' => $venta->id_venta,
                        'tratamiento' => $request->tratamiento,
                        'fecha_servicio' => $request->fecha_servicio,
                        'costo_servicio' => $request->costo_servicio,
                        'costo_combustible' => $request->costo_combustible,
                        'total_servicio' => $request->costo_servicio + $request->costo_combustible + $venta->total_venta,
                    ]);
                }
            } else {
                // Si servicio es false, eliminar el servicio si existe
                Servicio::where('id_venta', $venta->id_venta)->delete();
            }

            DB::commit();

            return redirect()->route('ventas.edit', $id)->with('success', 'Venta actualizada correctamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error al actualizar la venta: ' . $e->getMessage()]);
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

        $totalEfectivo = 0;
        $totalCredito = 0;
        $totalVentas = 0;
        $totalCosto = 0;
        $totalUtilidad = 0;

        // Procesar cada venta
        $ventas->getCollection()->transform(function ($venta) use (&$totalEfectivo, &$totalCredito,  &$totalVentas, &$totalCosto, &$totalUtilidad) {
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
                $detalle->subtotal_venta = $detalle->cantidad_venta * $precioVenta;
                $detalle->subtotal_costo = $detalle->cantidad_venta * $precioCompra;
                $detalle->subtotal_utilidad = $detalle->subtotal_venta - $detalle->subtotal_costo;

                // Ajustar efectivo y crédito según el campo "credito" de la venta
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

        return view('pages.venta_reporte_utilidad', compact('ventas', 'filtros', 'totalEfectivo', 'totalCredito', 'totalVentas', 'totalCosto', 'totalUtilidad'));
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
                $detalle->subtotal_venta = $detalle->cantidad_venta * $precioVenta;
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
