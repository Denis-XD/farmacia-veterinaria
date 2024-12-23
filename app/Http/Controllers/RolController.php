<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        abort_if(Gate::denies('rol_listar'), 403);
        $roles = Role::orderBy('id', 'asc')->paginate(10);
        $permisos = Permission::all()->pluck('name', 'id');
        return view('pages.roles', compact('roles', 'permisos'));
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
        abort_if(Gate::denies('rol_crear'), 403);
        $messages = require_once app_path('config/validation.php');
        $customAttributes = require_once app_path('config/customAttributes.php');
        $validatedData = $request->validate([
            'name' => 'required|string|max:30|min:4|unique:roles,name',
        ], $messages, $customAttributes);

        $role = Role::create($validatedData);
        $role->syncPermissions($request->input('permissions', []));


        return redirect()->route('roles.index')->with('success', 'Rol creado correctamente.');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Rol  $rol
     * @return \Illuminate\Http\Response
     */
    public function show()
    {
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Rol  $rol
     * @return \Illuminate\Http\Response
     */
    public function edit()
    {
    }

    public function update(Request $request, $id_rol)
    {
        abort_if(Gate::denies('rol_actualizar'), 403);
        $messages = require_once app_path('config/validation.php');
        $rules = [
            'name' => 'required|string|max:30|min:4|unique:roles,name,' . $id_rol,
        ];
        $customAttributes = require_once app_path('config/customAttributes.php');
        $validatedData = $request->validate($rules, $messages, $customAttributes);

        $rol = Role::find($id_rol);

        if (!$rol) {
            return redirect()->back()->with('error', 'Rol no encontrado.');
        }

        $success = $rol->update($validatedData);
        $rol->syncPermissions($request->input('permissions', []));

        if ($success) {
            return redirect()->route('roles.index')->with('success', 'Rol actualizado correctamente.');
        }

        return redirect()->back()->with('error', 'No se pudo actualizar el rol.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Rol  $rol
     * @return \Illuminate\Http\Response
     */

    public function destroy($id_rol)
    {
        abort_if(Gate::denies('rol_eliminar'), 403);
        $rol = Role::find($id_rol);
        if ($rol && $rol->delete()) {
            return redirect()->route('roles.index')->with('success', 'Rol eliminado correctamente.');
        }
        return redirect()->route('roles.index')->with('error', 'No se pudo eliminar el rol.');
    }
}
