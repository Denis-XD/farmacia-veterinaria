<?php

namespace App\Http\Controllers;

use App\Models\Ambiente;
use App\Models\TipoAmbiente;
use App\Models\Ubicacion;
use App\Models\Periodo;
use App\Models\Imparte;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Imports\AmbientesImport;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Validators\ValidationException;
use Illuminate\Support\Facades\Auth;

use App\Models\Reserva;

class AmbienteController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        abort_if(Gate::denies('ambiente_listar'), 403);
        $tipos = TipoAmbiente::all();
        $ubicaciones = Ubicacion::all();
        $ambientes = Ambiente::orderBy('id_ambiente', 'asc')->paginate(10);
        return view('pages.ambientes', compact('ambientes', 'tipos', 'ubicaciones'));
    }
    
    public function indexAmbientes(Request $request)
    {
        abort_if(Gate::denies('reservar_listar'), 403);
        $lado = $request->input('lado');
        $aula = $request->input('buscar');
        $idTipo = $request->input('id_tipo');
        $tipos = TipoAmbiente::all();
        $ubicaciones = Ubicacion::all();
        $periodos = Periodo::all();
    
        $horarioform = $request->input('horario_from');
        $fechaform1 = $request->input('fecha_from');
        $fechaform = empty($fechaform1) ? date('Y-m-d') : $fechaform1;
    
        $usuario = Auth::user();
        $id_docente = $usuario->id;
        $asignaciones = Imparte::with(['materia', 'grupo'])
            ->where('id_docente', $id_docente)
            ->get();
    
        // Agrupar materias con sus grupos
        $agrupadasMaterias = [];
        foreach ($asignaciones as $asignacion) {
            $materiaNombre = $asignacion->materia->nombre_materia;
            if (!isset($agrupadasMaterias[$materiaNombre])) {
                $agrupadasMaterias[$materiaNombre] = [];
            }
            $agrupadasMaterias[$materiaNombre][] = $asignacion;
        }
    
        $ambientes = Ambiente::where('habilitado', true)->orderBy('capacidad', 'asc');
        if ($horarioform != 0) {
            $ambientes = $ambientes->whereNotExists(function ($query) use ($fechaform, $horarioform) {
                $query->select('reserva.id_ambiente')
                    ->from('reserva')
                    ->leftJoin('reserva_periodo', 'reserva.id_reserva', '=', 'reserva_periodo.id_reserva')
                    ->leftJoin('periodo', 'reserva_periodo.id_periodo', '=', 'periodo.id_periodo')
                    ->whereColumn('reserva.id_ambiente', 'ambiente.id_ambiente')
                    ->where('reserva.fecha_reserva', $fechaform)
                    ->where('periodo.id_periodo', $horarioform);
            });
        }
    
        if ($lado === 'capacidad') {
            if ($aula) {
                $aula = intval($aula);
                $ambientes = $ambientes->where('capacidad', '>=', $aula);
            }
        } else {
            if ($aula) {
                $ambientes = $ambientes->where('nombre', 'LIKE', '%' . $aula . '%');
            }
        }
    
        if ($idTipo != 0) {
            $ambientes = $ambientes->where('id_tipo', $idTipo);
        }
    
        $ambientes = $ambientes->paginate(12)->appends($request->query());
    
        return view('pages.reservarAmbiente', compact('ambientes', 'tipos', 'ubicaciones', 'periodos', 'agrupadasMaterias'));
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
        abort_if(Gate::denies('ambiente_crear'), 403);
        $request->merge(['habilitado' => $request->has('habilitado') ? 1 : 0]);
        $messages = require_once app_path('config/validation.php');
        $rules = [
            'nombre' => 'required|string|max:40|min:4|unique:ambiente,nombre',
            'capacidad' => 'required|integer|min:10|max:300',
            'descripcion' => 'max:200|min:0',
            'habilitado' => 'required|boolean',
            'id_ubicacion' => 'required|exists:ubicacion,id_ubicacion',
            'id_tipo' => 'required|exists:tipo_ambiente,id_tipo',
        ];

        $validatedData = $request->validate($rules, $messages);
        Ambiente::create($validatedData);
        return redirect()->route('ambientes.index')->with('success', 'Ambiente creado correctamente.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Ambiente  $ambiente
     * @return \Illuminate\Http\Response
     */
    public function show(Ambiente $ambiente)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Ambiente  $ambiente
     * @return \Illuminate\Http\Response
     */
    public function edit(Ambiente $ambiente)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Ambiente  $ambiente
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        abort_if(Gate::denies('ambiente_actualizar'), 403);
        $request->merge(['habilitado' => $request->has('habilitado') ? 1 : 0]);
        $messages = require_once app_path('config/validation.php');
        $rules = [
            'nombre' => 'required|string|max:40|min:4|unique:ambiente,nombre,' . $id . ',id_ambiente',
            'capacidad' => 'required|integer|min:10|max:300',
            'descripcion' => 'max:200|min:0',
            'habilitado' => 'required|boolean',
            'id_ubicacion' => 'required|exists:ubicacion,id_ubicacion',
            'id_tipo' => 'required|exists:tipo_ambiente,id_tipo',
        ];

        $validatedData = $request->validate($rules, $messages);
        $ambiente = Ambiente::findOrFail($id);
        $ambiente->update($validatedData);

        return redirect()->route('ambientes.index')->with('success', 'Ambiente actualizado correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Ambiente  $ambiente
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        abort_if(Gate::denies('ambiente_eliminar'), 403);
        try {
            $ambiente = Ambiente::findOrFail($id);
            $ambiente->delete();
            return redirect()->route('ambientes.index')->with('success', 'Ambiente eliminado correctamente.');
        } catch (\Exception $e) {
            return redirect()->route('ambientes.index')->with('error', 'Error al eliminar el ambiente.');
        }
    }

    public function import(Request $request)
    {
        $this->validate($request, [
            'file' => 'required|file|mimes:csv,txt,xlsx,xls'
        ]);

        $import = new AmbientesImport;

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
        return back()->with('success', 'Ambientes importados correctamente.');
    }
}
