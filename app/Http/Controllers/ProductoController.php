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
        abort_if(Gate::denies('usuario_listar'), 403);

        $buscar = $request->get('buscar');

        $productos = Producto::with(['precioActual', 'historialPrecios'])
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
        abort_if(Gate::denies('usuario_crear'), 403);

        $messages = require_once app_path('config/validation.php');
        $request->validate([
            'codigo_barra' => 'nullable|string|max:13|min:10|unique:producto,codigo_barra',
            'nombre_producto' => 'required|string|max:50|min:4|unique:producto,nombre_producto',
            'unidad' => 'required|string|max:20|min:2',
            'fecha_vencimiento' => 'nullable|date',
            'porcentaje_utilidad' => 'required|numeric|min:0|max:100',
            'precio_compra' => 'required|numeric|min:0',
            'precio_venta_actual' => 'required|numeric|min:0|gte:precio_compra',
            'stock' => 'required|integer|min:0',
            'stock_minimo' => 'required|integer|min:0',
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

            DB::commit();

            return redirect()->route('productos.index')->with('success', 'Producto creado correctamente.');
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
        abort_if(Gate::denies('usuario_actualizar'), 403);

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
        abort_if(Gate::denies('usuario_actualizar'), 403);

        $messages = require_once app_path('config/validation.php');
        $request->validate([
            'codigo_barra' => "nullable|string|max:13|min:10|unique:producto,codigo_barra,{$id},id_producto",
            'nombre_producto' => "required|string|max:50|min:4|unique:producto,nombre_producto,{$id},id_producto",
            'unidad' => 'required|string|max:20|min:2',
            'fecha_vencimiento' => 'nullable|date',
            'porcentaje_utilidad' => 'required|numeric|min:0|max:100',
            'precio_compra' => 'required|numeric|min:0',
            'precio_venta_actual' => 'required|numeric|min:0|gte:precio_compra',
            'stock' => 'required|integer|min:0',
            'stock_minimo' => 'required|integer|min:0',
        ], $messages);

        try {
            DB::beginTransaction();

            $producto = Producto::findOrFail($id);
            $precioAnterior = $producto->precio_venta_actual;

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
        abort_if(Gate::denies('materia_eliminar'), 403);
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

        // Guardar temporalmente el PDF
        $pdfPath = storage_path("app/public/$nombre-barcode.pdf");
        $pdf->save($pdfPath);

        // Retornar el código de barras y la URL del PDF como JSON
        return response()->json([
            'barcode' => $barcode,
            'pdf_url' => asset("storage/$nombre-barcode.pdf"),
        ]);

        // Eliminar el archivo PDF después de enviarlo al cliente
        register_shutdown_function(function () use ($pdfPath) {
            if (file_exists($pdfPath)) {
                unlink($pdfPath);
            }
        });
    }

    public function productosMinimoStock(Request $request)
    {
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

    public function inventario(Request $request)
    {
        // Obtener la fecha seleccionada o la fecha actual
        $fecha = $request->input('fecha', Carbon::now()->toDateString());

        // Obtener los productos con su historial y paginación
        $productosPaginados = Producto::with(['historialPrecios', 'historialInventario'])
            ->paginate(10);

        // Procesar los datos para cada producto dentro de la paginación
        $inventario = collect($productosPaginados->items())->map(function ($producto) use ($fecha) {
            // Calcular el stock en la fecha seleccionada
            $stockHistorial = $producto->historialInventario()
                ->whereDate('fecha', '<=', $fecha)
                ->orderBy('fecha', 'desc')
                ->first();

            $stock = $stockHistorial ? $stockHistorial->stock : 0;

            // Obtener el precio en la fecha seleccionada
            $precioHistorial = $producto->historialPrecios()
                ->whereDate('fecha_inicio', '<=', $fecha)
                ->where(function ($query) use ($fecha) {
                    $query->whereNull('fecha_fin')
                        ->orWhereDate('fecha_fin', '>=', $fecha);
                })
                ->orderBy('fecha_inicio', 'desc')
                ->first();

            $precio = $precioHistorial ? $precioHistorial->precio_venta : 0;

            return [
                'descripcion' => $producto->nombre_producto,
                'unidad' => $producto->unidad,
                'cantidad' => $stock,
                'precio_unitario' => $precio,
                'valor' => $stock * $precio,
            ];
        });

        // Calcular el valor total
        $totalValor = $inventario->sum('valor');

        return view('pages.productos_inventario', [
            'inventario' => $inventario,
            'productos' => $productosPaginados,
            'fecha' => $fecha,
            'totalValor' => $totalValor,
        ]);
    }

    public function descargarInventarioPdf(Request $request)
    {
        // Obtener la fecha seleccionada o la fecha actual
        $fecha = $request->input('fecha', Carbon::now()->toDateString());

        // Obtener los datos para el inventario
        $productos = Producto::with(['historialPrecios', 'historialInventario'])->get();

        $inventario = $productos->map(function ($producto) use ($fecha) {
            $stockHistorial = $producto->historialInventario()
                ->whereDate('fecha', '<=', $fecha)
                ->orderBy('fecha', 'desc')
                ->first();

            $stock = $stockHistorial ? $stockHistorial->stock : 0;

            $precioHistorial = $producto->historialPrecios()
                ->whereDate('fecha_inicio', '<=', $fecha)
                ->where(function ($query) use ($fecha) {
                    $query->whereNull('fecha_fin')
                        ->orWhereDate('fecha_fin', '>=', $fecha);
                })
                ->orderBy('fecha_inicio', 'desc')
                ->first();

            $precio = $precioHistorial ? $precioHistorial->precio_venta : 0;

            return [
                'descripcion' => $producto->nombre_producto,
                'unidad' => $producto->unidad,
                'cantidad' => $stock,
                'precio_unitario' => $precio,
                'valor' => $stock * $precio,
            ];
        });

        $totalValor = $inventario->sum('valor');

        // Generar PDF
        $pdf = PDF::loadView('pdf.inventario_pdf', compact('inventario', 'fecha', 'totalValor'));
        return $pdf->download('inventario_' . $fecha . '.pdf');
    }
}
