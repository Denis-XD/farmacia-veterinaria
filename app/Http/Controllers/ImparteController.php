<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Materia;
use App\Models\Carrera;
use App\Models\Grupo;
use App\Models\Imparte;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ImparteController extends Controller
{
    public function store(Request $request)
    {
        abort_if(Gate::denies('asignacion_crear'), 403);
        $messages = require_once app_path('config/validation.php');
        $rules = [
            'id_docente' => 'required',
            'id_materia' => 'required',
            'id_grupo' => 'required',
        ];
        $validatedData = $request->validate($rules, $messages);

        $exiteConDocente = Imparte::where([
            ['id_docente', '=', $request->id_docente],
            ['id_materia', '=', $request->id_materia],
            ['id_grupo', '=', $request->id_grupo],
        ])->first();

        if ($exiteConDocente) {
            return redirect()->back()->withErrors(['error' => 'El docente ya tiene una asignación con esta materia y grupo.']);
        }
        $existeSinDocente = Imparte::where([
            ['id_materia', '=', $request->id_materia],
            ['id_grupo', '=', $request->id_grupo],
        ])->first();

        if ($existeSinDocente) {
            $docenteAsignado = $existeSinDocente->docente;
            $nombreDocente = $docenteAsignado->nombre;
            $apellidoDocente = $docenteAsignado->apellido;
            return redirect()->back()->withErrors(['error' => 'Materia y grupo ya está asignada a ' . $nombreDocente . ' ' . $apellidoDocente]);
        }
        $imparte = Imparte::create($validatedData);

        if ($request->has('id_carrera')) {
            $id_carreras = $request->id_carrera;
            $niveles = $request->nivel;
            foreach ($id_carreras as $key => $id_carrera) {
                if (!empty($id_carrera) && !empty($niveles[$key])) {
                    $imparte->impartesCarreras()->create([
                        'id_carrera' => $id_carrera,
                        'nivel' => $niveles[$key],
                    ]);
                }
            }
        }
        return redirect()->route('asignaciones.show', $request->id_docente)->with('success', 'Asignación creada correctamente.');
    }

    public function show($id)
    {
        abort_if(Gate::denies('asignacion_listar'), 403);
        $docente = User::find($id);
        $materias = Materia::all();
        $carreras = Carrera::all();
        $grupos = Grupo::all();
        if (!$docente) {
            return redirect()->back()->with('error', 'Docente no encontrado');
        }
        $imparte = Imparte::where('id_docente', $id)->orderBy('id_imparte', 'asc')->paginate(10);
        return view('pages.asignaciones', compact('docente', 'imparte', 'materias', 'grupos', 'carreras'));
    }

    public function update(Request $request, $id_imparte)
    {
        abort_if(Gate::denies('asignacion_actualizar'), 403);
        $messages = require_once app_path('config/validation.php');
        $rules = [
            'id_docente' => 'required',
            'id_materia' => 'required',
            'id_grupo' => 'required',
        ];
        $validatedData = $request->validate($rules, $messages);

        $asignacion = Imparte::find($id_imparte);
        $id_docente = $asignacion->id_docente;

        if (!$asignacion) {
            return redirect()->back()->with('error', 'Asignación no encontrada.');
        }

        $exiteConDocente = Imparte::where([
            ['id_docente', '=', $request->id_docente],
            ['id_materia', '=', $request->id_materia],
            ['id_grupo', '=', $request->id_grupo],
        ])->where('id_imparte', '!=', $id_imparte)->first();

        if ($exiteConDocente) {
            return redirect()->back()->withErrors(['error' => 'El docente ya tiene una asignación con esta materia y grupo.']);
        }

        $existeSinDocente = Imparte::where([
            ['id_materia', '=', $request->id_materia],
            ['id_grupo', '=', $request->id_grupo],
        ])->where('id_imparte', '!=', $id_imparte)->first();

        if ($existeSinDocente) {
            $docenteAsignado = $existeSinDocente->docente;
            $nombreDocente = $docenteAsignado->nombre;
            $apellidoDocente = $docenteAsignado->apellido;
            return redirect()->back()->withErrors(['error' => 'Materia y grupo ya está asignada a ' . $nombreDocente . ' ' . $apellidoDocente]);
        }

        $success = $asignacion->update($validatedData);
        if ($success) {
            $asignacion->impartesCarreras()->delete();
            if ($request->has('id_carrera') && !empty($request->id_carrera)) {
                $id_carreras = $request->id_carrera;
                $niveles = $request->nivel;
                foreach ($id_carreras as $key => $id_carrera) {
                    if (!empty($id_carrera) && !empty($niveles[$key])) {
                        $asignacion->impartesCarreras()->create([
                            'id_carrera' => $id_carrera,
                            'nivel' => $niveles[$key],
                        ]);
                    }
                }
            }
            return redirect()->route('asignaciones.show', $id_docente)->with('success', 'Asignación actualizada correctamente.');
        }

        return redirect()->back()->with('error', 'No se pudo actualizar la asignación.');
    }

    public function destroy($id)
    {
        abort_if(Gate::denies('asignacion_eliminar'), 403);
        $asignacion = Imparte::findOrFail($id);
        $id_docente = $asignacion->id_docente;
        $asignacion->delete();

        return redirect()->route('asignaciones.show', $id_docente)->with('success', 'Asignación eliminada correctamente.');
    }
}
