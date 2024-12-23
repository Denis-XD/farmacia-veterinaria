<?php

namespace App\Http\Controllers;

use App\Models\Materia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Imports\MateriasImport;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Validators\ValidationException;

class MateriaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        abort_if(Gate::denies('materia_listar'), 403);
        $materias = Materia::orderBy('id_materia', 'asc')->paginate(10);
        return view('pages.materias', compact('materias'));
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
            'codigo' => 'required|string|max:10|min:1|unique:materia,codigo',
            'nombre_materia' => 'required|string|max:40|min:4|unique:materia,nombre_materia',
        ], $messages);

        Materia::create([
            'codigo' => $request->input('codigo'),
            'nombre_materia' => $request->input('nombre_materia'),
        ]);

        return redirect()->route('materias.index')->with('success', 'Materia creada correctamente.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Materia  $materia
     * @return \Illuminate\Http\Response
     */
    public function show(Materia $materia)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Materia  $materia
     * @return \Illuminate\Http\Response
     */
    public function edit(Materia $materia)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Materia  $materia
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id_materia)
    {
        abort_if(Gate::denies('materia_actualizar'), 403);
        $messages = require_once app_path('config/validation.php');
        $rules = [
            'codigo' => 'required|string|max:10|min:1|unique:materia,codigo,' . $id_materia . ',id_materia',
            'nombre_materia' => 'required|string|max:40|min:4|unique:materia,nombre_materia,' . $id_materia . ',id_materia',
        ];
        $validatedData = $request->validate($rules, $messages);

        $materia = Materia::find($id_materia);

        if (!$materia) {
            return redirect()->back()->with('error', 'Materia no encontrada.');
        }

        $success = $materia->update($validatedData);

        if ($success) {
            return redirect()->route('materias.index')->with('success', 'Materia actualizada correctamente.');
        }

        return redirect()->back()->with('error', 'No se pudo actualizar la materia.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Materia  $materia
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        abort_if(Gate::denies('materia_eliminar'), 403);
        $materia = Materia::findOrFail($id);
        $materia->delete();

        return redirect()->route('materias.index')->with('success', 'Materia eliminada correctamente.');
    }

    public function import(Request $request)
    {
        $this->validate($request, [
            'file' => 'required|file|mimes:csv,txt,xlsx,xls'
        ]);

        $import = new MateriasImport;

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

        return back()->with('success', 'Materias importadas correctamente.');
    }
}
