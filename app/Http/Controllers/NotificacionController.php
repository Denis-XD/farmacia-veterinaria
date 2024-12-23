<?php

namespace App\Http\Controllers;

use App\Mail\NotificacionMail;
use App\Models\Destinatario;
use App\Models\Notificacion;
use App\Models\TipoNotificacion;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Spatie\Permission\Models\Role;

class NotificacionController extends Controller
{
    public function index(Request $request)
    {
        abort_if(Gate::denies('notificacion_listar'), 403);
        $usuarios = User::all();
        $tipos = TipoNotificacion::all();
        $correo = $request->get('correo');
        //$notificaciones = Notificacion::orderBy('id_notificacion', 'asc')->with('destinatarios')->paginate(10);
        $tipo = $request->get('tipo');
        $fecha = $request->get('fecha');
        $inicio = $request->get('desde') . ' 00:00:00';;
        $fin = $request->get('hasta') . ' 23:59:59';

        //$notificaciones = Notificacion::orderBy('id_notificacion', 'asc')->with('destinatarios')->paginate(10);
        // Comienza la consulta base
        $query = Notificacion::query();

        // Filtro por correo
        if (!is_null($correo)) {
            $query->whereHas('destinatarios.usuario', function ($q) use ($correo) {
                $q->where('email', $correo);
            });
        }

        // Filtro por tipo de notificación
        if (!is_null($tipo) && $tipo != 0) {
            $query->where('id_tipo_notificacion', $tipo);
        }

        // Filtro por rango de fechas
        if ($fecha === 'rango') {
            $query->whereBetween('created_at', [$inicio, $fin]);
        }

        // Agregar paginación
        $notificaciones = $query->orderBy('id_notificacion', 'asc')->with('destinatarios')->paginate(10); // Ajusta el número 10 al número de elementos por página que desees

        return view('pages.notificaciones.notificaciones', compact('tipos', 'notificaciones', 'usuarios'));
    }

    public function create()
    {
        abort_if(Gate::denies('notificacion_crear'), 403);
        $tipos = TipoNotificacion::all();
        $roles = Role::all();
        $usuarios = User::all();
        return view('pages.notificaciones.create_notificacion', compact('tipos', 'roles', 'usuarios'));
    }

    public function store(Request $request)
    {
        abort_if(Gate::denies('notificacion_crear'), 403);
        try {
            $validatedData = $request->validate([
                'asunto' => 'required|string|max:255',
                'contenido' => 'required|string',
                'tipo_notif' => 'required|exists:tipo_notificacion,id_tipo_notificacion',
                'send_to' => 'required',
                'selected_users' => 'required|string'
            ]);

            $selectedUsers = [];
            if ($validatedData['send_to'] == 0) {
                $selectedUsers = json_decode($validatedData['selected_users']);
                if (count($selectedUsers) == 0) {
                    return redirect()->back()->withErrors(['Debe seleccionar al menos un usuario.']);
                }
            } else if ($validatedData['send_to'] == 999) {
                $selectedUsers = User::all()->pluck('id')->toArray();
            } else {
                $selectedUsersFromRole = User::role($validatedData['send_to'])->pluck('id')->toArray();
                $selectedUsers = array_merge($selectedUsers, $selectedUsersFromRole);
            }

            $selectedUsers = array_unique($selectedUsers);

            DB::beginTransaction();
            // Crear una nueva notificación
            $notificacion = Notificacion::create([
                'asunto' => $validatedData['asunto'],
                'contenido' => $validatedData['contenido'],
                'id_tipo_notificacion' => $validatedData['tipo_notif']
            ]);

            foreach ($selectedUsers as $userId) {
                Destinatario::create([
                    'id_notificacion' => $notificacion->id_notificacion,
                    'id_usuario' => $userId
                ]);
            }

            // Enviar correos electrónicos
            $destinatarios = User::whereIn('id', $selectedUsers)->get();
            $subject = $validatedData['asunto'];
            $content = $validatedData['contenido'];

            foreach ($destinatarios as $destinatario) {
                Mail::to($destinatario->email)->send(new NotificacionMail($subject, $content));
            }

            DB::commit();
            return redirect()->back()->with('success', 'Notificación creada correctamente.');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['Error al enviar la notificación.', $e->getMessage()]);
        }
    }

    public function show($id)
    {
        abort_if(Gate::denies('notificacion_detalles'), 403);
        $notificacion = Notificacion::find($id);
        if (!$notificacion) {
            return redirect()->route('notificaciones.index')->withErrors(['Notificación no encontrada.']);
        }

        $destinatarios = $notificacion->destinatarios()->with('usuario')->get();
        return view('pages.notificaciones.show_notificacion', compact('notificacion', 'destinatarios'));
    }
}
