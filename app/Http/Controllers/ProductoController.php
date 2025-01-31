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
        $kardex = $movimientos->map(function ($movimiento) use (&$saldo) {
            $detalle = $movimiento->motivo;

            // Obtener detalles específicos según el tipo de transacción
            if ($movimiento->tipo_transaccion === 'Compra' && $movimiento->id_transaccion) {
                $compra = Compra::find($movimiento->id_transaccion);
                $detalle = $compra && $compra->proveedor
                    ? 'Proveedor: ' . $compra->proveedor->nombre_proveedor
                    : 'Proveedor desconocido';
            } elseif ($movimiento->tipo_transaccion === 'Venta' && $movimiento->id_transaccion) {
                $venta = Venta::find($movimiento->id_transaccion);
                $detalle = $venta
                    ? ($venta->socio
                        ? 'Socio: ' . $venta->socio->nombre_socio
                        : 'Venta sin socio')
                    : 'Venta desconocida';
            }

            // Actualizar el saldo acumulativo
            if ($movimiento->motivo === 'Compra' || $movimiento->motivo === 'Ajuste Positivo') {
                $saldo += $movimiento->stock; // Entrada
            } elseif ($movimiento->motivo === 'Venta' || $movimiento->motivo === 'Ajuste Negativo') {
                $saldo -= $movimiento->stock; // Salida
            }

            return [
                'fecha' => $movimiento->fecha->toDateTimeString(),
                'tipo' => $movimiento->motivo,
                'detalle' => $detalle,
                'entrada' => in_array($movimiento->motivo, ['Compra', 'Ajuste Positivo']) ? $movimiento->stock : 0,
                'salida' => in_array($movimiento->motivo, ['Venta', 'Ajuste Negativo']) ? $movimiento->stock : 0,
                'saldo' => $saldo,
            ];
        });

        return view('pages.producto_kardex', compact('producto', 'kardex'));
    }

    public function kardex2($id, $fechaFin)
    {
        // Validar que el producto existe
        $producto = Producto::findOrFail($id);

        // Obtener movimientos hasta la fecha fin, ordenados por fecha ascendente
        $movimientos = HistorialInventario::where('id_producto', $id)
            ->whereDate('fecha', '<=', $fechaFin) // Solo considerar movimientos hasta la fecha fin
            ->orderBy('fecha', 'asc')
            ->get();

        // Calcular el saldo acumulativo hasta la fecha más cercana a la fecha fin
        $saldo = 0;
        foreach ($movimientos as $movimiento) {
            if ($movimiento->motivo === 'Compra' || $movimiento->motivo === 'Ajuste Positivo') {
                $saldo += $movimiento->stock; // Entrada
            } elseif ($movimiento->motivo === 'Venta' || $movimiento->motivo === 'Ajuste Negativo') {
                $saldo -= $movimiento->stock; // Salida
            }
        }

        return $saldo; // Devolver el saldo final hasta la fecha fin
    }

    public function inventario(Request $request)
    {
        abort_if(Gate::denies('producto_inventario'), 403);

        // Validar las fechas
        $fechaInicio = $request->input('fecha_inicio', Carbon::now()->subMonth()->toDateString());
        $fechaFin = $request->input('fecha_fin', Carbon::now()->toDateString());

        if (Carbon::parse($fechaInicio)->gt(Carbon::parse($fechaFin))) {
            return redirect()->back()->with('error', 'Las fechas no son válidas. La fecha de inicio debe ser menor o igual a la fecha de fin.');
        }

        // Obtener los productos paginados
        $productosPaginados = Producto::with(['historialPrecios'])->paginate(10);

        // Calcular los datos para la página actual
        $inventario = collect($productosPaginados->items())->map(function ($producto) use ($fechaInicio, $fechaFin) {
            // Obtener el saldo del producto desde el método kardex2
            $stock = $this->kardex2($producto->id_producto, $fechaFin);

            // Obtener precios en el rango de fechas
            $precio = $this->obtenerPrecioProducto($producto, $fechaInicio, $fechaFin);

            return [
                'descripcion' => $producto->nombre_producto,
                'unidad' => $producto->unidad,
                'cantidad' => $stock, // Ahora usamos el stock obtenido desde el kardex2
                'precio_unitario' => $precio,
                'valor' => $stock * $precio,
            ];
        });

        // Calcular el valor total de la página actual
        $totalValorPagina = $inventario->sum('valor');

        // Calcular el valor total global usando la misma lógica
        $totalValorGlobal = Producto::with(['historialPrecios'])->get()->reduce(function ($carry, $producto) use ($fechaInicio, $fechaFin) {
            $stock = $this->kardex2($producto->id_producto, $fechaFin);
            $precio = $this->obtenerPrecioProducto($producto, $fechaInicio, $fechaFin);

            return $carry + ($stock * $precio);
        }, 0);

        return view('pages.productos_inventario', [
            'inventario' => $inventario,
            'productos' => $productosPaginados,
            'fechaInicio' => $fechaInicio,
            'fechaFin' => $fechaFin,
            'totalValor' => $totalValorPagina,
            'totalValorGlobal' => $totalValorGlobal,
        ]);
    }

    private function obtenerPrecioProducto($producto, $fechaInicio, $fechaFin)
    {
        // Obtener precios en el rango de fechas
        $preciosHistorial = $producto->historialPrecios()
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
            $precio = $preciosHistorial->take(2)->avg('precio_venta');
        } elseif ($preciosHistorial->count() == 1) {
            // Si solo hay un precio en el rango, usar ese
            $precio = $preciosHistorial->first()->precio_venta;
        } else {
            // Si no hay registros en el rango, buscar el más cercano antes de la fechaInicio o después de fechaFin
            $precioAnterior = $producto->historialPrecios()
                ->whereDate('fecha_inicio', '<', $fechaInicio)
                ->orderBy('fecha_inicio', 'desc')
                ->first();

            $precioPosterior = $producto->historialPrecios()
                ->whereDate('fecha_inicio', '>', $fechaFin)
                ->orderBy('fecha_inicio', 'asc')
                ->first();

            if ($precioAnterior && $precioPosterior) {
                // Tomar el más cercano en tiempo
                $precio = abs(strtotime($precioAnterior->fecha_inicio) - strtotime($fechaInicio)) <
                    abs(strtotime($precioPosterior->fecha_inicio) - strtotime($fechaFin))
                    ? $precioAnterior->precio_venta
                    : $precioPosterior->precio_venta;
            } elseif ($precioAnterior) {
                // Si solo hay un precio anterior, tomar ese
                $precio = $precioAnterior->precio_venta;
            } elseif ($precioPosterior) {
                // Si solo hay un precio posterior, tomar ese
                $precio = $precioPosterior->precio_venta;
            }
        }

        return $precio;
    }

    public function descargarInventarioPdf(Request $request)
    {
        // Obtener las fechas seleccionadas o valores predeterminados
        $fechaInicio = $request->input('fechaInicio', Carbon::now()->subMonth()->toDateString());
        $fechaFin = $request->input('fechaFin', Carbon::now()->toDateString());

        // Obtener todos los productos
        $productos = Producto::with(['historialPrecios'])->get();

        // Procesar cada producto
        $inventario = $productos->map(function ($producto) use ($fechaInicio, $fechaFin) {
            // Obtener el saldo del producto desde el método kardex2
            $stock = $this->kardex2($producto->id_producto, $fechaFin);

            // Obtener precios en el rango de fechas
            $preciosHistorial = $producto->historialPrecios()
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
                // Si hay al menos dos registros dentro del rango, promediar los dos últimos
                $precio = $preciosHistorial->take(2)->avg('precio_venta');
            } elseif ($preciosHistorial->count() == 1) {
                // Si solo hay un precio en el rango, usar ese
                $precio = $preciosHistorial->first()->precio_venta;
            } else {
                // Buscar el precio más cercano fuera del rango si no hay registros
                $precioAnterior = $producto->historialPrecios()
                    ->whereDate('fecha_inicio', '<', $fechaInicio)
                    ->orderBy('fecha_inicio', 'desc')
                    ->first();

                $precioPosterior = $producto->historialPrecios()
                    ->whereDate('fecha_inicio', '>', $fechaFin)
                    ->orderBy('fecha_inicio', 'asc')
                    ->first();

                if ($precioAnterior && $precioPosterior) {
                    // Seleccionar el precio más cercano a la fecha de consulta
                    $precio = abs(strtotime($precioAnterior->fecha_inicio) - strtotime($fechaInicio)) <
                        abs(strtotime($precioPosterior->fecha_inicio) - strtotime($fechaFin))
                        ? $precioAnterior->precio_venta
                        : $precioPosterior->precio_venta;
                } elseif ($precioAnterior) {
                    $precio = $precioAnterior->precio_venta;
                } elseif ($precioPosterior) {
                    $precio = $precioPosterior->precio_venta;
                }
            }

            return [
                'descripcion' => $producto->nombre_producto,
                'unidad' => $producto->unidad,
                'cantidad' => $stock, // Ahora usamos el stock obtenido desde el kardex2
                'precio_unitario' => $precio,
                'valor' => $stock * $precio,
            ];
        });

        // Calcular el valor total del inventario global
        $totalValor = $inventario->sum('valor');

        // Generar PDF con los datos del inventario
        $pdf = PDF::loadView('pdf.inventario_pdf', compact('inventario', 'fechaInicio', 'fechaFin', 'totalValor'));

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
}
