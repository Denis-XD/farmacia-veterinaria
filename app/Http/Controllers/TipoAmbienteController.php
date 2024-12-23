<?php

namespace App\Http\Controllers;

use App\Models\TipoAmbiente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class TipoAmbienteController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        abort_if(Gate::denies('tipo_ambiente_listar'), 403);
        $tipos = TipoAmbiente::orderBy('id_tipo', 'asc')->paginate(10);
        return view('pages.tipos_ambiente', compact('tipos'));
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
        abort_if(Gate::denies('tipo_ambiente_crear'), 403);
        $messages = require_once app_path('config/validation.php');
        $rules = [
            'nombre' => 'required|string|max:50|min:4|unique:tipo_ambiente,nombre',
            'color' => 'required|string|max:20',
        ];

        $validatedData = $request->validate($rules, $messages);

        TipoAmbiente::create($validatedData);
        return redirect()->route('tipos_ambiente.index')->with('success', 'Tipo de ambiente creado correctamente.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\TipoAmbiente  $tipoAmbiente
     * @return \Illuminate\Http\Response
     */
    public function show(TipoAmbiente $tipoAmbiente)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\TipoAmbiente  $tipoAmbiente
     * @return \Illuminate\Http\Response
     */
    public function edit(TipoAmbiente $tipoAmbiente)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\TipoAmbiente  $tipoAmbiente
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        abort_if(Gate::denies('tipo_ambiente_actualizar'), 403);
        $messages = require_once app_path('config/validation.php');
        $rules = [
            'nombre' => 'required|string|max:50|min:4|unique:tipo_ambiente,nombre,' . $id . ',id_tipo',
            'color' => 'required|string|max:20',
        ];
        $validatedData = $request->validate($rules, $messages);

        $tipo = TipoAmbiente::find($id);

        if (!$tipo) {
            return redirect()->back()->with('error', 'Tipo de ambiente no encontrado.');
        }

        $success = $tipo->update($validatedData);

        if ($success) {
            return redirect()->route('tipos_ambiente.index')->with('success', 'Tipo de ambiente actualizado correctamente.');
        }

        return redirect()->back()->with('error', 'No se pudo actualizar el tipo de ambiente.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\TipoAmbiente  $tipoAmbiente
     * @return \Illuminate\Http\Response
     */
    public function destroy(TipoAmbiente $tipoAmbiente, $id)
    {
        abort_if(Gate::denies('tipo_ambiente_eliminar'), 403);
        $tipo = TipoAmbiente::find($id);
        if ($tipo->delete()) {
            return redirect()->route('tipos_ambiente.index')->with('success', 'Tipo de ambiente eliminado correctamente.');
        }
        return redirect()->route('tipos_ambiente.index')->with('error', 'No se pudo eliminar el tipo de ambiente.');
    }
}
