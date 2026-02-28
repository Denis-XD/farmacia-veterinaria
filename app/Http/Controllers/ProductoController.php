<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\HistorialInventario;
use App\Models\Venta;
use App\Models\Compra;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class ProductoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        abort_if(Gate::denies('producto_listar'), 403);

        $buscar = $request->get('buscar');

        $productos = Producto::with(['precioActual', 'historialPrecios', 'historialPreciosCompra'])
            ->where(function ($query) use ($buscar) {
                $terminosBusqueda = explode(' ', $buscar);

                foreach ($terminosBusqueda as $termino) {
                    $query->where('codigo_barra', 'like', '%' . $termino . '%')
                        ->orWhere('nombre_producto', 'like', '%' . $termino . '%')
                        ->orWhere('unidad', 'like', '%' . $termino . '%')
                        ->orWhere('precio_venta_actual', 'like', '%' . $termino . '%');
                }
            })
            ->orderBy('id_producto', 'asc')
            ->paginate(10)
            ->appends($request->query());

        return view('pages.productos', compact('productos', 'buscar'));
    }

    public function buscar(Request $request)
    {
        $query = $request->get('query'); // Obtener el término de búsqueda del formulario
        echo $request;
        // Realizar la búsqueda en la base de datos
        $productos = Producto::where('codigo_barra', 'like', "%$query%")
            ->orWhere('nombre_producto', 'like', "%$query%")
            ->orWhere('unidad', 'like', "%$query%")
            ->orWhere('precio_venta_actual', 'like', "%$query%")
            ->paginate(10);

        // Devolver los resultados de la búsqueda a la vista
        return view('pages.productos', compact('productos'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('pages.crear_producto');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        abort_if(Gate::denies('producto_crear'), 403);

        $messages = require_once app_path('config/validation.php');
        $request->validate([
            'codigo_barra' => 'nullable|string|max:15|min:5|unique:producto,codigo_barra',
            'nombre_producto' => 'required|string|max:50|min:4|unique:producto,nombre_producto',
            'unidad' => 'required|string|max:20|min:2',
            'fecha_vencimiento' => 'nullable|date',
            'porcentaje_utilidad' => 'required|numeric|min:0|max:100',
            'precio_compra_actual' => 'required|numeric|min:0',
            'precio_venta_actual' => 'required|numeric|min:0|gte:precio_compra_actual',
            'stock' => 'required|numeric|min:0',
            'stock_minimo' => 'required|numeric|min:0',
        ], $messages);

        try {
            DB::beginTransaction();

            // Ajustar formato de unidad
            $data = $request->all();
            $data['unidad'] = ucfirst(strtolower($request->unidad)); // Primera letra mayúscula, resto minúscula

            // Crear producto
            $producto = Producto::create($data);

            // Crear historial de precios
            $producto->historialPrecios()->create([
                'precio_venta' => $request->precio_venta_actual,
                'fecha_inicio' => now(),
                'fecha_fin' => null,
            ]);

            // Crear historial de precio de compra
            $producto->historialPreciosCompra()->create([
                'precio_compra' => $request->precio_compra_actual,
                'fecha_inicio' => now(),
                'fecha_fin' => null,
            ]);

            DB::commit();

            return redirect()->route('productos.create')->with('success', 'Producto creado correctamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error al crear el producto: ' . $e->getMessage());
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
        abort_if(Gate::denies('producto_actualizar'), 403);

        $producto = Producto::with('historialPrecios')->findOrFail($id);

        return view('pages.producto_editar', compact('producto'));
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
        abort_if(Gate::denies('producto_actualizar'), 403);

        $messages = require_once app_path('config/validation.php');
        $request->validate([
            'codigo_barra' => "nullable|string|max:15|min:5|unique:producto,codigo_barra,{$id},id_producto",
            'nombre_producto' => "required|string|max:50|min:4|unique:producto,nombre_producto,{$id},id_producto",
            'unidad' => 'required|string|max:20|min:2',
            'fecha_vencimiento' => 'nullable|date',
            'porcentaje_utilidad' => 'required|numeric|min:0|max:100',
            'precio_compra_actual' => 'required|numeric|min:0',
            'precio_venta_actual' => 'required|numeric|min:0|gte:precio_compra_actual',
            'stock' => 'required|numeric|min:0',
            'stock_minimo' => 'required|numeric|min:0',
        ], $messages);

        try {
            DB::beginTransaction();

            $producto = Producto::findOrFail($id);
            $precioAnterior = $producto->precio_venta_actual;
            $precioCompraAnterior = $producto->precio_compra_actual;

            // Formatear la unidad
            $data = $request->all();
            $data['unidad'] = ucfirst(strtolower($request->unidad)); // Convertir primera letra en mayúscula, resto en minúscula

            // Actualizar los datos del producto
            $producto->update($data);

            // Verificar si el precio de venta ha cambiado
            if ($request->precio_venta_actual != $precioAnterior) {
                // Actualizar el historial actual
                $historialActual = $producto->historialPrecios()->whereNull('fecha_fin')->first();
                if ($historialActual) {
                    $historialActual->update(['fecha_fin' => now()]);
                }

                // Crear un nuevo registro en el historial de precios
                $producto->historialPrecios()->create([
                    'precio_venta' => $request->precio_venta_actual,
                    'fecha_inicio' => now(),
                    'fecha_fin' => null,
                ]);
            }

            // Verificar si el precio de compra ha cambiado
            if ($request->precio_compra_actual != $precioCompraAnterior) {
                // Actualizar el historial actual
                $historialCompraActual = $producto->historialPreciosCompra()->whereNull('fecha_fin')->first();
                if ($historialCompraActual) {
                    $historialCompraActual->update(['fecha_fin' => now()]);
                }

                // Crear un nuevo registro en el historial de precios
                $producto->historialPreciosCompra()->create([
                    'precio_compra' => $request->precio_compra_actual,
                    'fecha_inicio' => now(),
                    'fecha_fin' => null,
                ]);
            }

            DB::commit();

            return redirect()->route('productos.edit', $id)->with('success', 'Producto actualizado correctamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error al actualizar el producto: ' . $e->getMessage());
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
        abort_if(Gate::denies('producto_eliminar'), 403);
        $producto = Producto::findOrFail($id);
        $producto->delete();

        return redirect()->route('productos.index')->with('success', 'Producto eliminado correctamente.');
    }

    public function generateBarcode(Request $request)
    {
        $nombre = $request->get('name');

        // Generar un código de barras único
        do {
            $barcode = mt_rand(100000000000, 999999999999); // Genera un número de 12 dígitos
        } while (Producto::where('codigo_barra', $barcode)->exists());

        // Generar el PDF del código de barras
        $pdf = Pdf::loadView('pdf.barcode_pdf', ['barcode' => $barcode, 'nombre' => $nombre]);

        // Codificar el PDF como base64 para enviar en la respuesta JSON
        $pdfContent = $pdf->output();
        $pdfBase64 = base64_encode($pdfContent);

        // Retornar el código de barras y el PDF como JSON
        return response()->json([
            'barcode' => $barcode,
            'pdf_base64' => $pdfBase64,
        ]);
    }

    public function productosMinimoStock(Request $request)
    {
        abort_if(Gate::denies('producto_verifi_stock'), 403);
        $productosMinimoStock = DB::table('VistaAlertasStock')
            ->paginate(10)
            ->appends($request->query());

        return view('pages.productos_minimo_stock', compact('productosMinimoStock'));
    }

    public function buscar2(Request $request)
    {
        $query = $request->get('query');
        $producto = Producto::where('id_producto', $query)
            ->orWhere('codigo_barra', $query)
            ->orWhere('nombre_producto', 'LIKE', "%$query%")
            ->first();

        return response()->json([
            'producto' => $producto
        ]);
    }

    public function kardex($id)
    {
        // Validar que el producto existe
        $producto = Producto::findOrFail($id);

        // Obtener movimientos ordenados por fecha
        $movimientos = HistorialInventario::where('id_producto', $id)
            ->orderBy('fecha', 'asc')
            ->get();

        // Calcular el saldo acumulativo
        $saldo = 0;
        // DESPUÉS
        $kardex = $movimientos->map(function ($movimiento) use (&$saldo) {
            $detalle = $movimiento->motivo;

            // Detalles específicos por tipo de transacción
            if ($movimiento->tipo_transaccion === 'Compra' && $movimiento->id_transaccion) {
                $compra  = Compra::find($movimiento->id_transaccion);
                $detalle = $compra && $compra->proveedor
                    ? 'Proveedor: ' . $compra->proveedor->nombre_proveedor
                    : 'Proveedor desconocido';
            } elseif ($movimiento->tipo_transaccion === 'Venta' && $movimiento->id_transaccion) {
                $venta   = Venta::find($movimiento->id_transaccion);
                $detalle = $venta
                    ? ($venta->socio
                        ? 'Socio: ' . $venta->socio->nombre_socio
                        : 'Venta sin socio')
                    : 'Venta desconocida';
            } elseif ($movimiento->tipo_transaccion === 'Ajuste') {
                // El detalle es el motivo libre del usuario;
                // el tipo visible en la tabla será "Ajuste Positivo" o "Ajuste Negativo"
                $detalle = $movimiento->motivo;
            }

            // ✅ Usar tipo_transaccion + motivo default para determinar si es entrada o salida
            // — Compra o ajuste positivo (el motivo default sigue siendo 'Ajuste Positivo')
            $esEntrada = $movimiento->tipo_transaccion === 'Compra'
                || ($movimiento->tipo_transaccion === 'Ajuste' && $movimiento->motivo === 'Ajuste Positivo');

            // — Venta o ajuste negativo (el motivo default sigue siendo 'Ajuste Negativo')
            $esSalida  = $movimiento->tipo_transaccion === 'Venta'
                || ($movimiento->tipo_transaccion === 'Ajuste' && $movimiento->motivo === 'Ajuste Negativo');

            // ✅ Pero si el usuario ingresó un motivo personalizado, leer el motivo guardado
            //    en storeAjuste() guardamos: si positivo => motivo del usuario (o 'Ajuste Positivo')
            //    necesitamos saber la dirección — la guardamos en tipo_transaccion como 'Ajuste'
            //    pero no sabemos si fue + o - solo con eso.
            //    Solución: comparar con los defaults; si no coincide, buscar por eliminación
            //    usando que storeAjuste guarda 'Ajuste' en tipo_transaccion siempre,
            //    y el motivo puede ser cualquier texto.
            //    → La dirección real la detectamos porque en storeAjuste SOLO llamamos
            //      increment (positivo) o decrement (negativo). Necesitamos un campo extra
            //      OR reutilizar el campo motivo con prefijo. Ver nota abajo.
            //
            //    SOLUCIÓN LIMPIA: en storeAjuste guardar el tipo de ajuste en tipo_transaccion:
            //    'Ajuste Positivo' o 'Ajuste Negativo' — así kardex() siempre puede leerlo.

            $esEntrada = in_array($movimiento->tipo_transaccion, ['Compra', 'Ajuste Positivo']);
            $esSalida  = in_array($movimiento->tipo_transaccion, ['Venta',  'Ajuste Negativo']);

            if ($esEntrada) {
                $saldo += $movimiento->stock;
            } elseif ($esSalida) {
                $saldo -= $movimiento->stock;
            }

            // Etiqueta legible para la columna "Tipo"
            $tipoLabel = match ($movimiento->tipo_transaccion) {
                'Compra'          => 'Compra',
                'Venta'           => 'Venta',
                'Ajuste Positivo' => 'Ajuste Positivo',
                'Ajuste Negativo' => 'Ajuste Negativo',
                default           => $movimiento->tipo_transaccion,
            };

            return [
                'fecha'   => $movimiento->fecha->toDateTimeString(),
                'tipo'    => $tipoLabel,
                'detalle' => $detalle,
                'entrada' => $esEntrada ? $movimiento->stock : 0,
                'salida'  => $esSalida  ? $movimiento->stock : 0,
                'saldo'   => $saldo,
            ];
        });

        return view('pages.producto_kardex', compact('producto', 'kardex'));
    }

    public function kardex2($id, $fechaFin)
    {
        $movimientos = HistorialInventario::where('id_producto', $id)
            ->where('fecha', '<=', Carbon::parse($fechaFin)->endOfDay()) // ✅ cubre todo el día
            ->orderBy('fecha', 'asc')
            ->get();

        $saldo = 0;
        foreach ($movimientos as $movimiento) {
            // ✅ Usa tipo_transaccion igual que kardex() corregido
            if (in_array($movimiento->tipo_transaccion, ['Compra', 'Ajuste Positivo'])) {
                $saldo += $movimiento->stock;
            } elseif (in_array($movimiento->tipo_transaccion, ['Venta', 'Ajuste Negativo'])) {
                $saldo -= $movimiento->stock;
            }
        }

        return $saldo;
    }

    public function inventario(Request $request)
    {
        abort_if(Gate::denies('producto_inventario'), 403);

        $fechaInicio = $request->input('fecha_inicio', Carbon::now()->subMonth()->toDateString());
        $fechaFin    = $request->input('fecha_fin',    Carbon::now()->toDateString());

        if (Carbon::parse($fechaInicio)->gt(Carbon::parse($fechaFin))) {
            return redirect()->back()->with(
                'error',
                'La fecha de inicio debe ser menor o igual a la fecha de fin.'
            );
        }

        $fechaFinCarbon = Carbon::parse($fechaFin)->endOfDay();

        // Una sola query para todos los movimientos hasta fechaFin
        $stockPorProducto = HistorialInventario::where('fecha', '<=', $fechaFinCarbon)
            ->get()
            ->groupBy('id_producto')
            ->map(function ($movimientos) {
                $saldo = 0;
                foreach ($movimientos as $m) {
                    if (in_array($m->tipo_transaccion, ['Compra', 'Ajuste Positivo'])) {
                        $saldo += $m->stock;
                    } elseif (in_array($m->tipo_transaccion, ['Venta', 'Ajuste Negativo'])) {
                        $saldo -= $m->stock;
                    }
                }
                return $saldo;
            });

        // Página actual — eager load para evitar N+1 en obtenerPrecioProducto
        $productosPaginados = Producto::with(['historialPrecios', 'historialPreciosCompra'])
            ->paginate(10);

        // Inyectar datos calculados directamente en cada objeto producto
        foreach ($productosPaginados->items() as $producto) {
            $stock  = $stockPorProducto->get($producto->id_producto, 0);
            $precio = $this->obtenerPrecioProducto($producto, $fechaInicio, $fechaFin);

            $producto->stock_kardex  = $stock;
            $producto->precio_kardex = $precio;
            $producto->valor_kardex  = $stock * $precio;
        }

        $totalValorPagina = collect($productosPaginados->items())->sum('valor_kardex');

        // Total global — todos los productos con sus precios, reutiliza $stockPorProducto
        $totalValorGlobal = Producto::with(['historialPreciosCompra'])
            ->get()
            ->sum(function ($producto) use ($fechaInicio, $fechaFin, $stockPorProducto) {
                $stock  = $stockPorProducto->get($producto->id_producto, 0);
                $precio = $this->obtenerPrecioProducto($producto, $fechaInicio, $fechaFin);
                return $stock * $precio;
            });

        return view('pages.productos_inventario', [
            'productos'        => $productosPaginados,
            'fechaInicio'      => $fechaInicio,
            'fechaFin'         => $fechaFin,
            'totalValor'       => $totalValorPagina,
            'totalValorGlobal' => $totalValorGlobal,
        ]);
    }

    private function obtenerPrecioProducto($producto, $fechaInicio, $fechaFin)
    {
        // Obtener precios en el rango de fechas
        $preciosHistorial = $producto->historialPreciosCompra()
            ->whereDate('fecha_inicio', '>=', $fechaInicio)
            ->whereDate('fecha_inicio', '<=', $fechaFin)
            ->where(function ($query) use ($fechaInicio, $fechaFin) {
                $query->whereNull('fecha_fin')
                    ->orWhereDate('fecha_fin', '<=', $fechaFin)
                    ->orWhereDate('fecha_fin', '>=', $fechaInicio);
            })
            ->orderBy('fecha_inicio', 'desc')
            ->get();

        $precio = 0;

        if ($preciosHistorial->count() >= 2) {
            // Si hay al menos dos registros dentro del rango, sacar el promedio de los dos últimos
            $precio = $preciosHistorial->take(2)->avg('precio_compra');
        } elseif ($preciosHistorial->count() == 1) {
            // Si solo hay un precio en el rango, usar ese
            $precio = $preciosHistorial->first()->precio_compra;
        } else {
            // Si no hay registros en el rango, buscar el más cercano antes de la fechaInicio o después de fechaFin
            $precioAnterior = $producto->historialPreciosCompra()
                ->whereDate('fecha_inicio', '<', $fechaInicio)
                ->orderBy('fecha_inicio', 'desc')
                ->first();

            $precioPosterior = $producto->historialPreciosCompra()
                ->whereDate('fecha_inicio', '>', $fechaFin)
                ->orderBy('fecha_inicio', 'asc')
                ->first();

            if ($precioAnterior && $precioPosterior) {
                // Tomar el más cercano en tiempo
                $precio = abs(strtotime($precioAnterior->fecha_inicio) - strtotime($fechaInicio)) <
                    abs(strtotime($precioPosterior->fecha_inicio) - strtotime($fechaFin))
                    ? $precioAnterior->precio_compra
                    : $precioPosterior->precio_compra;
            } elseif ($precioAnterior) {
                // Si solo hay un precio anterior, tomar ese
                $precio = $precioAnterior->precio_compra;
            } elseif ($precioPosterior) {
                // Si solo hay un precio posterior, tomar ese
                $precio = $precioPosterior->precio_compra;
            }
        }

        return $precio;
    }

    // DESPUÉS — reutiliza kardex2() corregido y obtenerPrecioProducto()
    public function descargarInventarioPdf(Request $request)
    {
        $fechaInicio = $request->input('fechaInicio', Carbon::now()->subMonth()->toDateString());
        $fechaFin    = $request->input('fechaFin',    Carbon::now()->toDateString());

        // ✅ Una sola query para todos los movimientos hasta fechaFin
        $fechaFinCarbon = Carbon::parse($fechaFin)->endOfDay();

        $stockPorProducto = HistorialInventario::where('fecha', '<=', $fechaFinCarbon)
            ->get()
            ->groupBy('id_producto')
            ->map(function ($movimientos) {
                $saldo = 0;
                foreach ($movimientos as $m) {
                    if (in_array($m->tipo_transaccion, ['Compra', 'Ajuste Positivo'])) {
                        $saldo += $m->stock;
                    } elseif (in_array($m->tipo_transaccion, ['Venta', 'Ajuste Negativo'])) {
                        $saldo -= $m->stock;
                    }
                }
                return $saldo;
            });

        // ✅ Eager load para evitar N+1 en obtenerPrecioProducto
        $productos = Producto::with(['historialPreciosCompra'])->get();

        $inventario = $productos->map(function ($producto) use ($fechaInicio, $fechaFin, $stockPorProducto) {
            $stock  = $stockPorProducto->get($producto->id_producto, 0);
            $precio = $this->obtenerPrecioProducto($producto, $fechaInicio, $fechaFin);

            return [
                'descripcion'     => $producto->nombre_producto,
                'unidad'          => $producto->unidad,
                'cantidad'        => $stock,
                'precio_unitario' => $precio,
                'valor'           => $stock * $precio,
            ];
        });

        $totalValor = $inventario->sum('valor');

        $pdf = PDF::loadView(
            'pdf.inventario_pdf',
            compact('inventario', 'fechaInicio', 'fechaFin', 'totalValor')
        );

        return $pdf->download('inventario_' . $fechaInicio . '_al_' . $fechaFin . '.pdf');
    }

    public function generarCodigosBarraPdf()
    {
        // Obtener productos que no tienen código de barra
        $productosSinCodigo = Producto::whereNull('codigo_barra')->get();

        // Generar códigos de barra para esos productos
        foreach ($productosSinCodigo as $producto) {
            do {
                $barcode = mt_rand(100000000000, 999999999999); // Genera un número de 12 dígitos
            } while (Producto::where('codigo_barra', $barcode)->exists());

            $producto->update(['codigo_barra' => $barcode]);
        }

        // Volver a obtener los productos actualizados con su código de barra
        $productosActualizados = Producto::whereIn('id_producto', $productosSinCodigo->pluck('id_producto'))->get();

        // Generar el PDF con los códigos de barra
        $pdf = Pdf::loadView('pdf.barcode_productos_pdf', compact('productosActualizados'));

        // Descargar el PDF con la fecha actual en el nombre
        $nombrePdf = 'codigos_barra_productos_' . now()->format('Y_m_d') . '.pdf';

        return $pdf->download($nombrePdf);
    }

    public function ajustarKardex()
    {
        abort_if(Gate::denies('producto_ajustar'), 403);

        $productos = Producto::orderBy('nombre_producto')->get();

        return view('pages.producto_ajustar_kardex', compact('productos'));
    }

    public function storeAjuste(Request $request, $id)
    {
        abort_if(Gate::denies('producto_ajustar'), 403);

        $request->validate([
            'tipo'     => 'required|in:positivo,negativo',
            'cantidad' => 'required|numeric|min:0.01|max:50000',
            'motivo'   => 'nullable|string|max:50',
        ]);

        $producto = Producto::findOrFail($id);
        $cantidad = $request->cantidad;
        $tipo     = $request->tipo;

        // Validar que un ajuste negativo no deje stock negativo
        if ($tipo === 'negativo' && $cantidad > $producto->stock) {
            return response()->json([
                'success' => false,
                'message' => 'La cantidad a restar (' . $cantidad . ') supera el stock actual (' . $producto->stock . ').',
            ], 422);
        }

        DB::beginTransaction();

        try {
            // DESPUÉS
            if ($tipo === 'positivo') {
                $producto->increment('stock', $cantidad);
                $motivoDefault    = 'Ajuste Positivo';
                $tipoTransaccion  = 'Ajuste Positivo'; // ✅ guarda la dirección
            } else {
                $producto->decrement('stock', $cantidad);
                $motivoDefault    = 'Ajuste Negativo';
                $tipoTransaccion  = 'Ajuste Negativo'; // ✅ guarda la dirección
            }

            // El motivo visible es lo que escribió el usuario, o el default si no escribió nada
            $motivo = !empty($request->motivo) ? $request->motivo : $motivoDefault;

            HistorialInventario::create([
                'id_producto'      => $producto->id_producto,
                'stock'            => $cantidad,
                'fecha'            => now(),
                'motivo'           => $motivo,           // texto libre del usuario
                'id_transaccion'   => null,
                'tipo_transaccion' => $tipoTransaccion,  // 'Ajuste Positivo' o 'Ajuste Negativo'
            ]);

            DB::commit();

            return response()->json([
                'success'        => true,
                'message'        => 'Ajuste registrado correctamente.',
                'nuevo_stock'    => $producto->fresh()->stock,
                'nombre_producto' => $producto->nombre_producto,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Error al registrar el ajuste: ' . $e->getMessage(),
            ], 500);
        }
    }
}
