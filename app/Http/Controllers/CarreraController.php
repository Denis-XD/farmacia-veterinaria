<?php

namespace App\Http\Controllers;

use App\Models\Carrera;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class CarreraController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        abort_if(Gate::denies('carrera_listar'), 403);
        $carreras = Carrera::orderBy('id_carrera', 'asc')->paginate(10);
        return view('pages.carreras', compact('carreras'));
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
        abort_if(Gate::denies('carrera_crear'), 403);
        $messages = require_once app_path('config/validation.php');
        $request->validate([
            'codigo' => 'required|string|max:10|min:1|unique:carrera,codigo',
            'nombre' => 'required|string|max:40|min:5|unique:carrera,nombre',
        ], $messages);

        Carrera::create([
            'codigo' => $request->input('codigo'),
            'nombre' => $request->input('nombre'),
        ]);

        return redirect()->route('carreras.index')->with('success', 'Carrera creada correctamente.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Carrera  $carrera
     * @return \Illuminate\Http\Response
     */
    public function show(Carrera $carrera)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Carrera  $carrera
     * @return \Illuminate\Http\Response
     */
    public function edit(Carrera $carrera)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Carrera  $carrera
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id_carrera)
    {
        abort_if(Gate::denies('carrera_actualizar'), 403);
        $messages = require_once app_path('config/validation.php');
        $rules = [
            'codigo' => 'required|string|max:10|min:1|unique:carrera,codigo,' . $id_carrera . ',id_carrera',
            'nombre' => 'required|string|max:40|min:5|unique:carrera,nombre,' . $id_carrera . ',id_carrera',
        ];
        $validatedData = $request->validate($rules, $messages);

        $carrera = Carrera::find($id_carrera);

        if (!$carrera) {
            return redirect()->back()->with('error', 'Carrera no encontrada.');
        }

        $success = $carrera->update($validatedData);

        if ($success) {
            return redirect()->route('carreras.index')->with('success', 'Carrera actualizada correctamente.');
        }

        return redirect()->back()->with('error', 'No se pudo actualizar la carrera.');
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Carrera  $carrera
     * @return \Illuminate\Http\Response
     */
    public function destroy($id_carrera)
    {
        abort_if(Gate::denies('carrera_eliminar'), 403);
        $carrera = Carrera::findOrFail($id_carrera);
        $carrera->delete();

        return redirect()->route('carreras.index')->with('success', 'Carrera eliminada correctamente.');
    }
}
