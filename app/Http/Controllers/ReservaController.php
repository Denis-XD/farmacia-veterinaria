<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Reserva;
use App\Models\Estado;
use App\Models\ReservaUsuario;
use App\Models\ReservaUsuarioImparte;
use App\Models\Imparte;
use App\Models\Ambiente;
use App\Models\Periodo;
use App\Models\TipoAmbiente;
use App\Models\User;
use App\Models\Reglamento;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReservaController extends Controller
{
    public function index()
    {
        //
    }

    public function create()
    {
        //
    }
    public function store(Request $request)
    {
        $messages = require_once app_path('config/validation.php');

        $request->validate([
            'fecha_reserva' => 'required',
            'descripcion' => 'nullable|string|max:200',
            'id_ambiente' => 'required',
            'id_periodo' => 'required',
        ], $messages);

        $usuario = $request->user();
        $asignaciones = Imparte::where('id_docente', $usuario->id)->count();
        $ultimasReglas = Reglamento::latest()->first();

        if ($ultimasReglas) {
            $fecha_inicio_reservas = Carbon::parse($ultimasReglas->fecha_inicio)->toDateString();
            $fecha_final_reservas = Carbon::parse($ultimasReglas->fecha_final)->toDateString();
            if (Carbon::now() < $fecha_inicio_reservas || Carbon::now() > $fecha_final_reservas) {
                return redirect()->back()->with('error', 'Esta fuera del plazo de reservas. Por favor revise las reglas o contacte con un administrador');
            }
        } else {
            return redirect()->back()->with('error', 'En este momento, las reglas aún están siendo configuradas. Por favor, tenga paciencia o contacte con un administrador para obtener más información.');
        }

        if ($asignaciones == 0) {
            return redirect()->back()->with('error', 'No tiene materias para solicitar la reserva.');
        }

        if (!$request->has('id_materia')) {
            return redirect()->back()->with('error', 'No seleccionó materias para solicitar la reserva.');
        }

        $currentDateTime = Carbon::now();
        $ambiente = Ambiente::find($request->id_ambiente);

        // Verificar si el ambiente es de tipo "auditorio"
        if ($ambiente->tipo->nombre === 'AUDITORIO') {
            $periodosSolicitados = $request->id_periodo;
            $reservasPeriodosUsuario = Reserva::whereHas('usuarios', function ($q) use ($usuario) {
                $q->where('id', $usuario->id);
            })
                ->whereHas('ambientes', function ($q) use ($ambiente) {
                    $q->where('ambiente.id_ambiente', $ambiente->id_ambiente);
                })
                ->whereHas('estado', function ($q) {
                    $q->whereIn('nombre', ['PENDIENTE', 'ACEPTADO']);
                })
                ->whereDate('fecha_reserva', $request->fecha_reserva)
                ->withCount('reservasPeriodos')
                ->get()
                ->sum('reservas_periodos_count');

            $totalPeriodos = $reservasPeriodosUsuario + count($periodosSolicitados);

            if ($totalPeriodos > $ultimasReglas->reservas_auditorio) {
                return redirect()->back()->with('error', 'Ha superado el límite de ' . $ultimasReglas->reservas_auditorio . ' periodos de reservas para este auditorio.');
            }
        }

        try {
            $reserva = Reserva::create([
                'fecha_solicitud' => $currentDateTime,
                'fecha_reserva' => $request->input('fecha_reserva'),
                'descripcion' => $request->input('descripcion'),
                'id_estado' => 1,
                'capacidad_solicitada' => $ambiente->capacidad,
                'capacidad_total' => $ambiente->capacidad,
                'id_tipo' => $ambiente->tipo->id_tipo,
            ]);

            foreach ($request->id_periodo as $id_periodo) {
                $reserva->reservasPeriodos()->create(['id_periodo' => $id_periodo]);
            }

            $reservaUsuario = ReservaUsuario::create([
                'id_reserva' => $reserva->id_reserva,
                'id_usuario' => $usuario->id,
            ]);

            foreach ($request->id_materia as $id_imparte) {
                ReservaUsuarioImparte::create([
                    'id_reserva_usuario' => $reservaUsuario->id_reserva_usuario,
                    'id_imparte' => $id_imparte,
                ]);
            }

            $reserva->ambientes()->attach($request->id_ambiente);
        } catch (\Exception $e) {
            if (isset($reserva)) {
                $reserva->delete();
            }
            return redirect()->back()->with('error', 'Error al hacer reserva: ' . $e->getMessage());
        }

        return redirect()->route('ambientes.indexAmbientes')->with('success', 'Solicitud de reserva registrada correctamente.');
    }

    public function storeGenerica(Request $request)
    {
        $messages = require_once app_path('config/validation.php');

        $request->validate([
            'fecha_reserva' => 'required|date|after_or_equal:today',
            'id_tipo' => 'required|exists:tipo_ambiente,id_tipo',
            'capacidad' => 'required|integer|min:50|max:500',
            'id_periodo' => 'required|array',
            'id_periodo.*' => 'exists:periodo,id_periodo',
            'descripcion' => 'nullable|string|max:200',
        ], $messages);

        $ultimasReglas = Reglamento::latest()->first();
        if ($ultimasReglas) {
            $fecha_inicio_reservas = Carbon::parse($ultimasReglas->fecha_inicio)->toDateString();
            $fecha_final_reservas = Carbon::parse($ultimasReglas->fecha_final)->toDateString();
            if (Carbon::now() < $fecha_inicio_reservas || Carbon::now() > $fecha_final_reservas) {
                return redirect()->back()->with('error', 'Esta fuera del plazo de reservas. Por favor revise las reglas o contacte con un administrador');
            }
        } else {
            return redirect()->back()->with('error', 'En este momento, las reglas aún están siendo configuradas. Por favor, tenga paciencia o contacte con un administrador para obtener más información.');
        }

        $usuario = $request->user();
        $asignaciones = Imparte::where('id_docente', $usuario->id)->count();
        if ($asignaciones == 0) {
            return redirect()->back()->with('error', 'No tiene materias para solicitar la reserva.');
        }

        if (!$request->has('id_materia')) {
            return redirect()->back()->with('error', 'No seleccionó materias para solicitar la reserva.');
        }

        try {
            $currentDateTime = Carbon::now();
            $reserva = Reserva::create([
                'fecha_solicitud' => $currentDateTime,
                'fecha_reserva' => $request->input('fecha_reserva'),
                'descripcion' => $request->input('descripcion'),
                'id_estado' => 1,
                'generico' => true,
                'capacidad_total' => 0,
                'capacidad_solicitada' => $request->input('capacidad'),
                'id_tipo' => $request->input('id_tipo'),
            ]);

            foreach ($request->id_periodo as $id_periodo) {
                $reserva->reservasPeriodos()->create(['id_periodo' => $id_periodo]);
            }

            $reservaUsuario = ReservaUsuario::create([
                'id_reserva' => $reserva->id_reserva,
                'id_usuario' => $usuario->id,
            ]);

            foreach ($request->id_materia as $id_imparte) {
                ReservaUsuarioImparte::create([
                    'id_reserva_usuario' => $reservaUsuario->id_reserva_usuario,
                    'id_imparte' => $id_imparte,
                ]);
            }
        } catch (\Exception $e) {
            if (isset($reserva)) {
                $reserva->delete();
            }

            return redirect()->back()->with('error', 'Error al hacer reserva: ' . $e->getMessage());
        }

        return redirect()->route('ambientes.indexAmbientes')->with('success', 'Solicitud de reserva registrada correctamente.');
    }

    public function reservaGrupal(Request $request, $user_id)
    {
        $user = User::with('impartes.materia', 'impartes.grupo')->findOrFail($user_id);
        $tipos = TipoAmbiente::all();
        $periodos = Periodo::all();

        // Obtener los usuarios que comparten materias con el usuario actual
        $compartenMaterias = User::whereHas('impartes', function ($q) use ($user) {
            $q->whereIn('id_materia', $user->impartes->pluck('id_materia'));
        })->where('id', '<>', $user->id)->get();

        return view('pages.reserva_grupal', compact('user', 'tipos', 'periodos', 'compartenMaterias'));
    }

    public function storeReservaGrupal(Request $request)
    {
        $request->validate([
            'fecha_reserva' => 'required|date',
            'id_tipo' => 'required|integer',
            'capacidad' => 'required|integer|min:50|max:500',
            'id_periodo' => 'required|array|min:1',
            'id_materia' => 'required|array|min:1',
            'colaboradores' => 'required|array|min:1'
        ], [
            'id_periodo.required' => 'Debe seleccionar al menos un período.',
            'id_materia.required' => 'Debe seleccionar al menos una materia.',
            'colaboradores.required' => 'Debe seleccionar al menos un colaborador.'
        ]);
    
        $selectedMaterias = $request->id_materia;
        $selectedColaboradores = $request->colaboradores;
    
        // Obtener el estado "PENDIENTE" o cualquier otro estado deseado
        $estadoPendiente = Estado::where('nombre', 'PENDIENTE')->firstOrFail();
    
        DB::beginTransaction();
    
        try {
            // Crear la reserva principal
            $reserva = Reserva::create([
                'fecha_solicitud' => now(),
                'fecha_reserva' => $request->fecha_reserva,
                'id_tipo' => $request->id_tipo,
                'capacidad_solicitada' => $request->capacidad,
                'descripcion' => $request->descripcion ?? '',
                'id_estado' => $estadoPendiente->id_estado,
                'generico' => true,
                'grupal' => true,
            ]);
    
            // Añadir periodos a la reserva
            foreach ($request->id_periodo as $periodoId) {
                $reserva->reservasPeriodos()->create(['id_periodo' => $periodoId]);
            }
    
            // Añadir materias del usuario principal
            $reservaUsuario = $reserva->usuarios()->create(['id_usuario' => auth()->user()->id]);
            foreach ($selectedMaterias as $materiaId) {
                ReservaUsuarioImparte::create([
                    'id_reserva_usuario' => $reservaUsuario->id_reserva_usuario,
                    'id_imparte' => $materiaId,
                ]);
            }
    
            // Añadir colaboradores y sus materias
            foreach ($selectedColaboradores as $colaborador_id) {
                $colaborador = User::findOrFail($colaborador_id);
                $reservaUsuario = $reserva->usuarios()->create(['id_usuario' => $colaborador->id]);
    
                if (isset($request->colaborador_materias[$colaborador_id])) {
                    foreach ($request->colaborador_materias[$colaborador_id] as $materiaId) {
                        ReservaUsuarioImparte::create([
                            'id_reserva_usuario' => $reservaUsuario->id_reserva_usuario,
                            'id_imparte' => $materiaId,
                        ]);
                    }
                }
            }
    
            DB::commit();
    
            return redirect()->route('ambientes.indexAmbientes')->with('success', 'Reserva grupal creada correctamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('ambientes.indexAmbientes')->with('error', 'Hubo un error al crear la reserva grupal: ' . $e->getMessage());
        }
    }
    
    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        //
    }

    public function destroy($id)
    {
        //
    }
}
