<?php

namespace App\Http\Controllers;

use App\Models\EstadoMensaje;
use App\Models\Mensaje;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class MensajeController extends Controller
{
    public function index(Request $request)
    {
        abort_if(Gate::denies('mensaje_listar'), 403);
        $estados_mensaje = EstadoMensaje::all();
        $fecha = $request->get('fecha');
        $inicio = $request->get('desde') . ' 00:00:00';;
        $fin = $request->get('hasta') . ' 23:59:59';
        $estado = $request->get('estado');

        //$notificaciones = Notificacion::orderBy('id_notificacion', 'asc')->with('destinatarios')->paginate(10);
        // Comienza la consulta base
        // Check if current user has 'mensaje_todos' permission
        if (Gate::allows('mensaje_todos')) {
            $query = Mensaje::query();
        } else {
            $query = Mensaje::where('id_usuario', auth()->id());
        }

        // Filtro por tipo de notificación
        if (!is_null($estado) && $estado != 0) {
            $query->where('id_estado_mensaje', $estado);
        }

        // Filtro por rango de fechas
        if ($fecha === 'rango') {
            $query->whereBetween('created_at', [$inicio, $fin]);
        }

        // Agregar paginación
        $mensajes = $query->orderBy('id_mensaje', 'asc')->paginate(10); // Ajusta el número 10 al número de elementos por página que desees

        return view('pages.mensajes.index_mensaje', compact('estados_mensaje', 'mensajes'));
    }

    public function create()
    {
        abort_if(Gate::denies('mensaje_crear'), 403);
        $estados = EstadoMensaje::all();
        return view('pages.mensajes.create_mensaje', compact('estados'));
    }

    public function store(Request $request)
    {
        abort_if(Gate::denies('mensaje_crear'), 403);
        try {
            $validatedData = $request->validate([
                'asunto' => 'required|string|max:255',
                'contenido' => 'required|string',
            ]);

            DB::beginTransaction();
            // Crear un nuevo mensaje
            $mensaje = Mensaje::create([
                'asunto' => $validatedData['asunto'],
                'contenido' => $validatedData['contenido'],
                'id_estado_mensaje' => 1,
                'id_usuario' => auth()->id(),
            ]);

            DB::commit();
            return redirect()->back()->with('success', 'Mensaje enviado correctamente.');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['Error al enviar el mensaje.', $e->getMessage()]);
        }
    }

    public function show($id)
    {
        abort_if(Gate::denies('mensaje_detalles'), 403);
        $mensaje = Mensaje::find($id);
        if (!$mensaje) {
            return redirect()->route('mensajes.index')->withErrors(['Mensaje no encontrado.']);
        }

        return view('pages.mensajes.show_mensaje', compact('mensaje'));
    }

    public function updateEstado($id, $nuevoEstado)
    {
        abort_if(Gate::denies('mensaje_editar'), 403);

        $mensaje = Mensaje::find($id);

        if (!$mensaje) {
            return redirect()->route('mensajes.index')->withErrors(['Mensaje no encontrado.']);
        }

        $mensaje->id_estado_mensaje = $nuevoEstado;
        $mensaje->save();

        switch ($nuevoEstado) {
            case 1:
                $mensajeEstado = 'pendiente';
                break;
            case 2:
                $mensajeEstado = 'resuelto';
                break;
            case 3:
                $mensajeEstado = 'rechazado';
                break;
            default:
                $mensajeEstado = 'desconocido';
                break;
        }

        return redirect()->back()->with('success', "Mensaje marcado como $mensajeEstado.");
    }
}
