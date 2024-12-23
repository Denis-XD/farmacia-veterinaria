<?php

namespace App\Http\Controllers;

use App\Models\Grupo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class GrupoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        abort_if(Gate::denies('grupo_listar'), 403);
        $grupos = Grupo::orderBy('id_grupo', 'asc')->paginate(10);
        return view('pages.grupos', compact('grupos'));
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
        abort_if(Gate::denies('grupo_crear'), 403);
        $messages = require_once app_path('config/validation.php');
        $validatedData = $request->validate([
            'nombre' => 'required|string|max:20|min:2|unique:grupo,nombre',
        ], $messages);

        Grupo::create($validatedData);

        return redirect()->route('grupos.index')->with('success', 'Grupo creado correctamente.');
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
    public function update(Request $request, $id_grupo)
    {
        abort_if(Gate::denies('grupo_actualizar'), 403);
        $messages = require_once app_path('config/validation.php');
        $rules = [
            'nombre' => 'required|string|max:20|min:2|unique:grupo,nombre,' . $id_grupo . ',id_grupo',
        ];
        $validatedData = $request->validate($rules, $messages);

        $grupo = Grupo::find($id_grupo);

        if (!$grupo) {
            return redirect()->back()->with('error', 'Grupo no encontrado.');
        }

        $success = $grupo->update($validatedData);

        if ($success) {
            return redirect()->route('grupos.index')->with('success', 'Grupo actualizado correctamente.');
        }

        return redirect()->back()->with('error', 'No se pudo actualizar el grupo.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id_grupo)
    {
        abort_if(Gate::denies('grupo_eliminar'), 403);
        $grupo = Grupo::find($id_grupo);
        if ($grupo && $grupo->delete()) {
            return redirect()->route('grupos.index')->with('success', 'Grupo eliminado correctamente.');
        }
        return redirect()->route('grupos.index')->with('error', 'No se pudo eliminar el grupo.');
    }
}
