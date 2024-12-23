<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        abort_if(Gate::denies('permiso_listar'), 403);
        $permisos = Permission::orderBy('id', 'asc')->paginate(10);
        return view('pages.permisos', compact('permisos'));
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
        abort_if(Gate::denies('permiso_crear'), 403);
        $messages = require_once app_path('config/validation.php');
        $rules = [
            'name' => 'required|string|max:50|min:4|unique:permissions,name',
        ];
        $customAttributes = require_once app_path('config/customAttributes.php');

        $validatedData = $request->validate($rules, $messages, $customAttributes);

        Permission::create($validatedData);
        return redirect()->route('permisos.index')->with('success', 'Permiso creado correctamente.');
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
    public function update(Request $request, $id)
    {
        abort_if(Gate::denies('permiso_actualizar'), 403);
        $messages = require_once app_path('config/validation.php');
        $rules = [
            'name' => 'required|string|max:50|min:4|unique:permissions,name',
        ];
        $validatedData = $request->validate($rules, $messages);

        $permiso = Permission::find($id);

        if (!$permiso) {
            return redirect()->back()->with('error', 'Permiso no encontrada.');
        }

        $success = $permiso->update($validatedData);

        if ($success) {
            return redirect()->route('permisos.index')->with('success', 'Permiso actualizado correctamente.');
        }

        return redirect()->back()->with('error', 'No se pudo actualizar el permiso.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        abort_if(Gate::denies('permiso_eliminar'), 403);
        $permiso = Permission::find($id);
        if ($permiso && $permiso->delete()) {
            return redirect()->route('permisos.index')->with('success', 'Permiso eliminado correctamente.');
        }
        return redirect()->route('permisos.index')->with('error', 'No se pudo eliminar el permiso.');
    }
}
