<?php

namespace App\Http\Controllers;

use App\Models\Reserva;
use App\Models\Estado;
use App\Models\Ambiente;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\SolicitudAceptadaMail;
use App\Mail\SolicitudRechazadaMail;
use App\Models\TipoAmbiente;
use Illuminate\Support\Facades\Gate;
use App\Models\Ubicacion;
use App\Models\Reglamento;


class SolicitudesController extends Controller
{
    public function index(Request $request)
    {
        abort_if(Gate::denies('solicitud_listar'), 403);
        $tipos = TipoAmbiente::all();
        $tipo = $request->get('tipo', 'all');
        $order = $request->get('order', 'asc');
        $all = $request->get('all', 'all');
        $order_by = $request->get('order_by', 'fecha_solicitud');
        $status = $request->get('status', 'all');
        $tipo_reserva = $request->get('tipo_reserva', 'all');

        $query = Reserva::with([
            'usuarios.usuario', // Asegúrate de que esta relación esté cargando correctamente el usuario.
            'ambientes.tipo',
            'estado',
            'reservasPeriodos.periodo',
            'tipoAmbiente'
        ])
            ->orderBy($order_by, $order);

        if ($status != 'all') {
            $query->whereHas('estado', function ($q) use ($status) {
                $q->whereRaw('LOWER(nombre) = ?', [strtolower($status)]);
            });
        }

        if ($tipo != 'all') {
            $query->where('id_tipo', $tipo);
        }

        if ($tipo_reserva != 'all') {
            $query->where('generico', $tipo_reserva == 'generico');
        }

        if ($all == 'range') {
            $date_from = $request->get('date_from', date('Y-m-d')) . ' 00:00:00';
            $date_to = $request->get('date_to', date('Y-m-d')) . ' 23:59:59';
            $query->whereBetween($order_by, [$date_from, $date_to]);
        }

        $solicitudes = $query->paginate(10)->appends($request->query());

        return view('pages.solicitudes', compact('solicitudes', 'tipos'));
    }

    public function aceptar(Request $request, $id)
    {
        DB::beginTransaction();

        try {
            $reserva = Reserva::with('usuarios.usuario')->findOrFail($id);
            $usuarioReserva = $reserva->usuarios->first(); // Obtener el primer usuario relacionado
            $user = $usuarioReserva->usuario; // Obtener la instancia de User
            $estadoAceptado = Estado::where('nombre', 'ACEPTADO')->first();

            $reserva->update([
                'id_estado' => $estadoAceptado->id_estado,
                'fecha_cambio' => now(),
            ]);

            Mail::to($user->email)->send(new SolicitudAceptadaMail($user, $reserva, false));

            DB::commit();

            return redirect()->route('solicitudes.index')->with('success', 'Solicitud aceptada correctamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('solicitudes.index')->with('error', 'Hubo un error al aceptar la solicitud.');
        }
    }

    public function rechazar(Request $request, $id)
    {
        $request->validate([
            'motivo_rechazo' => 'required|string|max:100',
        ]);

        DB::beginTransaction();

        try {
            $reserva = Reserva::findOrFail($id);
            $motivo = $request->input('motivo_rechazo');
            $estadoRechazado = Estado::where('nombre', 'RECHAZADO')->first();

            $reserva->update([
                'id_estado' => $estadoRechazado->id_estado,
                'fecha_cambio' => now(),
            ]);

            foreach ($reserva->usuarios as $reservaUsuario) {
                $user = $reservaUsuario->usuario;
                Mail::to($user->email)->send(new SolicitudRechazadaMail($user, $reserva, $motivo));
            }

            DB::commit();

            return redirect()->route('solicitudes.index')->with('success', 'Solicitud rechazada correctamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('solicitudes.index')->with('error', 'Hubo un error al rechazar la solicitud.');
        }
    }

    public function asignarAmbiente(Request $request, $reserva_id)
    {
        $reserva = Reserva::with([
            'reservasPeriodos.periodo',
            'tipoAmbiente',
            'usuarios.usuario.impartes.materia',
            'usuarios.usuario.impartes.grupo'
        ])->findOrFail($reserva_id);

        $users = $reserva->usuarios;

        $ambientesDisponibles = Ambiente::where('id_tipo', $reserva->id_tipo)
            ->where('habilitado', true)
            ->whereDoesntHave('reservas', function ($query) use ($reserva) {
                $query->where('fecha_reserva', $reserva->fecha_reserva)
                    ->whereHas('reservasPeriodos', function ($q) use ($reserva) {
                        $q->whereIn('id_periodo', $reserva->reservasPeriodos->pluck('id_periodo'));
                    })
                    ->where('id_estado', Estado::where('nombre', 'ACEPTADO')->first()->id_estado);
            })
            ->paginate(6)->appends($request->query());

        return view('pages.asignacion_ambiente', compact('reserva', 'users', 'ambientesDisponibles'));
    }
    public function confirmarAsignacion(Request $request, $reserva_id)
    {
        if (!$request->ambientes) {
            return redirect()->back()->with('error', 'Debe seleccionar al menos un ambiente para asignar.');
        }
    
        DB::beginTransaction();
    
        try {
            $reserva = Reserva::with(['usuarios.usuario', 'reservasPeriodos'])->findOrFail($reserva_id);
            $estadoAceptado = Estado::where('nombre', 'ACEPTADO')->first();
            $ultimasReglas = Reglamento::latest()->first();
    
            // Verificar cada ambiente seleccionado
            foreach ($request->ambientes as $ambiente_id) {
                $ambiente = Ambiente::with('tipo')->findOrFail($ambiente_id);
    
                // Verificar si el ambiente es de tipo "auditorio"
                if ($ambiente->tipo->nombre === 'AUDITORIO') {
                    $periodosSolicitados = $reserva->reservasPeriodos->pluck('id_periodo');
                    $reservasPeriodosUsuario = Reserva::whereHas('usuarios', function ($q) use ($reserva) {
                        $q->where('id_usuario', $reserva->usuarios->first()->id_usuario);
                    })
                    ->whereHas('ambientes', function ($q) use ($ambiente) {
                        $q->where('ambiente.id_ambiente', $ambiente->id_ambiente);
                    })
                    ->whereHas('estado', function ($q) {
                        $q->whereIn('nombre', ['PENDIENTE', 'ACEPTADO']);
                    })
                    ->whereDate('fecha_reserva', $reserva->fecha_reserva)
                    ->withCount('reservasPeriodos')
                    ->get()
                    ->sum('reservas_periodos_count');
    
                    $totalPeriodos = $reservasPeriodosUsuario + $periodosSolicitados->count();
    
                    if ($totalPeriodos > $ultimasReglas->reservas_auditorio) {
                        return redirect()->back()->with('error', 'Ha superado el límite de ' . $ultimasReglas->reservas_auditorio . ' periodos de reservas para el auditorio ' . $ambiente->nombre);
                    }
                }
            }
    
            // Asignar ambientes a la reserva
            $reserva->ambientes()->sync($request->ambientes);
    
            // Calcular capacidad total
            $capacidadTotal = Ambiente::whereIn('id_ambiente', $request->ambientes)->sum('capacidad');
    
            // Actualizar el estado de la reserva y capacidad total
            $reserva->update([
                'generico' => false,
                'id_estado' => $estadoAceptado->id_estado,
                'fecha_cambio' => now(),
                'capacidad_total' => $capacidadTotal,
            ]);
    
            // Enviar notificaciones a todos los usuarios de la reserva
            $usuarios = $reserva->usuarios;
            foreach ($usuarios as $usuarioReserva) {
                $user = $usuarioReserva->usuario; // Obtener la instancia de User
                Mail::to($user->email)->send(new SolicitudAceptadaMail($user, $reserva, true));
            }
    
            DB::commit();
    
            return redirect()->route('solicitudes.index')->with('success', 'Ambiente(s) asignado(s) correctamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('solicitudes.index')->with('error', 'Hubo un error al asignar los ambientes.' . $e->getMessage());
        }
    }
    
    
}
