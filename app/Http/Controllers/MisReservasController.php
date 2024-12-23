<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Reserva;
use App\Models\Estado;
use App\Models\TipoAmbiente;
use Carbon\Carbon;

class MisReservasController extends Controller
{
    public function index(Request $request)
    {
        $tipos = TipoAmbiente::all();
        $order = $request->get('order', 'asc');
        $all = $request->get('all', 'all');
        $order_by = $request->get('order_by', 'fecha_reserva');
        $status = $request->get('status', 'all');
        $tipo = $request->get('tipo', 'all');
        $tipo_reserva = $request->get('tipo_reserva', 'all');
        $user_id = Auth::id();

        $query = Reserva::with([
            'ambientes.tipo',
            'tipoAmbiente',
            'reservasPeriodos.periodo',
            'reservasImparte.imparte.materia',
            'reservasImparte.imparte.grupo',
            'reservasImparte.imparte.impartesCarreras.carrera',
            'estado'
        ])
            ->whereHas('usuarios', function ($query) use ($user_id) {
                $query->where('id_usuario', $user_id);
            })
            ->orderBy($order_by, $order);

        if ($status != 'all') {
            $query->whereHas('estado', function ($q) use ($status) {
                $q->whereRaw('LOWER(nombre) = ?', [strtolower($status)]);
            });
        }

        if ($tipo != 'all') {
            $query->whereHas('tipoAmbiente', function ($q) use ($tipo) {
                $q->where('id_tipo', $tipo);
            });
        }

        if ($tipo_reserva != 'all') {
            $query->where('generico', $tipo_reserva == 'generico');
        }

        if ($all == 'range') {
            $date_from = $request->get('date_from', date('Y-m-d')) . ' 00:00:00';
            $date_to = $request->get('date_to', date('Y-m-d')) . ' 23:59:59';
            $query->whereBetween($order_by, [$date_from, $date_to]);
        }

        $reservas = $query->paginate(10)->appends($request->query());
        $currentDate = Carbon::now()->toDateString();

        return view('pages.mis_reservas', compact('reservas', 'currentDate', 'tipos'));
    }

    public function cancel($id)
    {
        DB::beginTransaction();

        try {
            $reserva = Reserva::findOrFail($id);
            $estadoCancelado = Estado::where('nombre', 'CANCELADO')->first();

            $reserva->update([
                'id_estado' => $estadoCancelado->id_estado,
                'fecha_cambio' => now(),
            ]);

            DB::commit();

            return redirect()->route('mis_reservas.index')->with('success', 'Reserva cancelada correctamente.');
        } catch (\Exception $e) {
            DB::rollback();

            return redirect()->route('mis_reservas.index')->with('error', 'Ocurrió un error al cancelar la reserva.');
        }
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        //
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
