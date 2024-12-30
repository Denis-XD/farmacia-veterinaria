<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

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
            'unidad' => 'required|string|max:15|min:5',
            'fecha_vencimiento' => 'nullable|date',
            'porcentaje_utilidad' => 'required|numeric|min:0|max:100',
            'precio_compra' => 'required|numeric|min:0',
            'precio_venta_actual' => 'required|numeric|min:0|gte:precio_compra',
            'stock' => 'required|integer|min:0',
            'stock_minimo' => 'required|integer|min:0',
        ], $messages);

        try {
            DB::beginTransaction();

            // Crear producto
            $producto = Producto::create($request->all());

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
            'unidad' => 'required|string|max:15|min:5',
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

            // Actualizar los datos del producto
            $producto->update($request->all());

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
}
