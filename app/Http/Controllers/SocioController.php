<?php

namespace App\Http\Controllers;

use App\Models\Socio;
use Illuminate\Http\Request;
use App\Imports\SocioImport;
use Illuminate\Support\Facades\Gate;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Validators\ValidationException;


class SocioController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        abort_if(Gate::denies('socio_listar'), 403);

        $buscar = $request->get('buscar');

        $socios = Socio::orderBy('id_socio', 'asc')
            ->where(function ($query) use ($buscar) {
                // Dividimos la cadena de búsqueda en palabras individuales
                $terminosBusqueda = explode(' ', $buscar);

                foreach ($terminosBusqueda as $termino) {
                    // Buscamos en el campo de nombre y apellido
                    $query->where('nombre_socio', 'like', '%' . $termino . '%')
                        ->orWhere('celular_socio', 'like', '%' . $termino . '%');
                }
            })
            ->paginate(10)->appends($request->query());

        return view('pages.socios', compact('socios', 'buscar'));
    }

    public function buscar(Request $request)
    {
        $query = $request->get('query'); // Obtener el término de búsqueda del formulario
        echo $request;
        // Realizar la búsqueda en la base de datos
        $socios = Socio::where('nombre_socio', 'like', "%$query%")
            ->orWhere('celular_socio', 'like', "%$query%")
            ->paginate(10);

        // Devolver los resultados de la búsqueda a la vista
        return view('pages.socios', compact('socios'));
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
        abort_if(Gate::denies('socio_crear'), 403);
        $messages = require_once app_path('config/validation.php');
        $request->validate([
            'nombre_socio' => 'required|string|max:50|min:4|unique:socio,nombre_socio',
            'celular_socio' => 'nullable|max:8|min:0',
        ], $messages);

        Socio::create([
            'nombre_socio' => $request->input('nombre_socio'),
            'celular_socio' => $request->input('celular_socio'),
        ]);

        return redirect()->route('socios.index')->with('success', 'Socio creado correctamente.');
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
    public function update(Request $request, $id_socio)
    {
        abort_if(Gate::denies('socio_actualizar'), 403);
        $messages = require_once app_path('config/validation.php');
        $rules = [
            'nombre_socio' => 'required|string|max:50|min:4|unique:socio,nombre_socio,' . $id_socio . ',id_socio',
            'celular_socio' => 'nullable|max:8|min:0',
        ];
        $validatedData = $request->validate($rules, $messages);

        $socio = Socio::find($id_socio);

        if (!$socio) {
            return redirect()->back()->with('error', 'Socio no encontrado.');
        }

        $success = $socio->update($validatedData);

        if ($success) {
            return redirect()->route('socios.index')->with('success', 'Socio actualizado correctamente.');
        }

        return redirect()->back()->with('error', 'No se pudo actualizar al socio.');
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
        $socio = Socio::findOrFail($id);
        $socio->delete();

        return redirect()->route('socios.index')->with('success', 'Socio eliminado correctamente.');
    }

    public function import(Request $request)
    {
        $this->validate($request, [
            'file' => 'required|file|mimes:csv,txt,xlsx,xls'
        ]);

        $import = new SocioImport;

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

        return back()->with('success', 'Socios importados correctamente.');
    }
}
