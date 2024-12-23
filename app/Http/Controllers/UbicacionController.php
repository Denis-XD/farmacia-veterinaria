<?php

namespace App\Http\Controllers;

use App\Models\Ubicacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class UbicacionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        abort_if(Gate::denies('ubicacion_listar'), 403);
        $ubicaciones = Ubicacion::orderBy('id_ubicacion', 'asc')->paginate(10);
        return view('pages.ubicaciones', compact('ubicaciones'));
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
        abort_if(Gate::denies('ubicacion_crear'), 403);
        $messages = require_once app_path('config/validation.php');
        $rules = [
            'nombre' => 'required|string|max:50|min:4|unique:ubicacion,nombre',
        ];

        $validatedData = $request->validate($rules, $messages);

        Ubicacion::create($validatedData);
        return redirect()->route('ubicaciones.index')->with('success', 'Ubicación creada correctamente.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Ubicacion  $ubicacion
     * @return \Illuminate\Http\Response
     */
    public function show(Ubicacion $ubicacion)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Ubicacion  $ubicacion
     * @return \Illuminate\Http\Response
     */
    public function edit(Ubicacion $ubicacion)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Ubicacion  $ubicacion
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        abort_if(Gate::denies('ubicacion_actualizar'), 403);
        $messages = require_once app_path('config/validation.php');
        $rules = [
            'nombre' => 'required|string|max:50|min:4|unique:ubicacion,nombre,' . $id . ',id_ubicacion',
        ];
        $validatedData = $request->validate($rules, $messages);

        $ubicacion = Ubicacion::find($id);

        if (!$ubicacion) {
            return redirect()->back()->with('error', 'Ubicación no encontrada.');
        }

        $success = $ubicacion->update($validatedData);

        if ($success) {
            return redirect()->route('ubicaciones.index')->with('success', 'Ubicación actualizada correctamente.');
        }

        return redirect()->back()->with('error', 'No se pudo actualizar la ubicación.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Ubicacion  $ubicacion
     * @return \Illuminate\Http\Response
     */
    public function destroy(Ubicacion $ubicacion, $id)
    {
        abort_if(Gate::denies('ubicacion_eliminar'), 403);
        $ubicacion = Ubicacion::find($id);
        if ($ubicacion->delete()) {
            return redirect()->route('ubicaciones.index')->with('success', 'Ubicación eliminada correctamente.');
        }
        return redirect()->route('ubicaciones.index')->with('error', 'No se pudo eliminar la ubicación.');
    }
}
