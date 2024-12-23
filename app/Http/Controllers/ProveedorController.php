<?php

namespace App\Http\Controllers;

use App\Models\Proveedor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Imports\ProveedoresImport;
use App\Imports\ProveedorImport;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Validators\ValidationException;

class ProveedorController extends Controller
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

        $proveedores = Proveedor::orderBy('id_proveedor', 'asc')
            ->where(function ($query) use ($buscar) {
                // Dividimos la cadena de búsqueda en palabras individuales
                $terminosBusqueda = explode(' ', $buscar);

                foreach ($terminosBusqueda as $termino) {
                    // Buscamos en el campo de nombre y apellido
                    $query->where('nombre_proveedor', 'like', '%' . $termino . '%')
                        ->orWhere('direccion', 'like', '%' . $termino . '%')
                        ->orWhere('celular_proveedor', 'like', '%' . $termino . '%');
                }
            })
            ->paginate(10)->appends($request->query());

        return view('pages.proveedores', compact('proveedores', 'buscar'));
    }

    public function buscar(Request $request)
    {
        $query = $request->get('query'); // Obtener el término de búsqueda del formulario
        echo $request;
        // Realizar la búsqueda en la base de datos
        $proveedores = Proveedor::where('nombre_proveedor', 'like', "%$query%")
            ->orWhere('direccion', 'like', "%$query%")
            ->orWhere('celular_proveedor', 'like', "%$query%")
            ->paginate(10);

        // Devolver los resultados de la búsqueda a la vista
        return view('pages.proveedores', compact('proveedores'));
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
        abort_if(Gate::denies('materia_crear'), 403);
        $messages = require_once app_path('config/validation.php');
        $request->validate([
            'nombre_proveedor' => 'required|string|max:50|min:4|unique:proveedor,nombre_proveedor',
            'direccion' => 'nullable|max:50',
            'celular_proveedor' => 'nullable|max:8|min:0',
        ], $messages);

        Proveedor::create([
            'nombre_proveedor' => $request->input('nombre_proveedor'),
            'direccion' => $request->input('direccion'),
            'celular_proveedor' => $request->input('celular_proveedor'),
        ]);

        return redirect()->route('proveedores.index')->with('success', 'Proveedor creado correctamente.');
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
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id_proveedor)
    {
        abort_if(Gate::denies('materia_actualizar'), 403);
        $messages = require_once app_path('config/validation.php');
        $rules = [
            'nombre_proveedor' => 'required|string|max:50|min:4|unique:proveedor,nombre_proveedor,' . $id_proveedor . ',id_proveedor',
            'direccion' => 'nullable|max:50',
            'celular_proveedor' => 'nullable|max:8|min:0',
        ];
        $validatedData = $request->validate($rules, $messages);

        $proveedor = Proveedor::find($id_proveedor);

        if (!$proveedor) {
            return redirect()->back()->with('error', 'Proveedor no encontrado.');
        }

        $success = $proveedor->update($validatedData);

        if ($success) {
            return redirect()->route('proveedores.index')->with('success', 'Proveedor actualizado correctamente.');
        }

        return redirect()->back()->with('error', 'No se pudo actualizar al proveedor.');
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
        $proveedor = Proveedor::findOrFail($id);
        $proveedor->delete();

        return redirect()->route('proveedores.index')->with('success', 'Proveedor eliminado correctamente.');
    }

    public function import(Request $request)
    {
        $this->validate($request, [
            'file' => 'required|file|mimes:csv,txt,xlsx,xls'
        ]);

        $import = new ProveedorImport;

        try {
            Excel::import($import, $request->file('file'));

            if ($import->rowsProcessed === 0) {
                return back()->with('error', 'No se encontraron registros en el archivo o el archivo está vacío.');
            }
        } catch (ValidationException $e) {
            $failures = $e->failures();

            $erroresPorFila = [];

            foreach ($failures as $failure) {
                $fila = $failure->row();
                if (!isset($erroresPorFila[$fila])) {
                    $erroresPorFila[$fila] = ["Hubo un error en la fila {$fila}:"];
                }

                foreach ($failure->errors() as $error) {
                    $erroresPorFila[$fila][] = $error;
                }
            }

            $erroresTraducidos = [];

            foreach ($erroresPorFila as $fila => $errores) {
                $erroresTraducidos[] = implode("\n", $errores);
            }

            $erroresFinales = nl2br(implode("\n", $erroresTraducidos));

            return back()->with('error', $erroresFinales);
        }

        return back()->with('success', 'Proveedores importados correctamente.');
    }

    public function buscar2(Request $request)
    {
        $query = $request->get('query');
        $proveedor = Proveedor::where('id_proveedor', $query)
            ->orWhere('nombre_proveedor', 'LIKE', "%$query%")
            ->first();

        return response()->json([
            'proveedor' => $proveedor
        ]);
    }
}
