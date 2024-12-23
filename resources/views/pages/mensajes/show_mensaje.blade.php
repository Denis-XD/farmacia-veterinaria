<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link href="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.snow.css" rel="stylesheet" />
    <title>Mensaje</title>
</head>

<body>
    @extends('layout')

    @section('content')

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {!! session('error') !!}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <ul class="m-0">
                    @foreach ($errors->all() as $error)
                        <li class="m-0">{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        <div>
            <div class="">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a disabled><svg xmlns="http://www.w3.org/2000/svg" width="18"
                                    height="18" viewBox="0 0 24 24" fill="currentColor"
                                    class="icon icon-tabler icons-tabler-filled icon-tabler-home">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path
                                        d="M12.707 2.293l9 9c.63 .63 .184 1.707 -.707 1.707h-1v6a3 3 0 0 1 -3 3h-1v-7a3 3 0 0 0 -2.824 -2.995l-.176 -.005h-2a3 3 0 0 0 -3 3v7h-1a3 3 0 0 1 -3 -3v-6h-1c-.89 0 -1.337 -1.077 -.707 -1.707l9 -9a1 1 0 0 1 1.414 0m.293 11.707a1 1 0 0 1 1 1v7h-4v-7a1 1 0 0 1 .883 -.993l.117 -.007z" />
                                </svg></a></li>
                        <li class="breadcrumb-item"><a href="{{ route('reservas') }}">Inicio</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('mensajes.index') }}">Mensajes</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Detalles mensaje</li>
                    </ol>
                </nav>
                <h2 class="text-center">Detalles mensaje</h2>
            </div>
            <p><strong class="text-primary">Asunto: </strong>{{ $mensaje->asunto }}</p>
            <p><strong class="text-primary">Fecha:
                </strong>{{ date('d/m/Y - H:i:s', strtotime($mensaje->created_at)) }}</p>
            <p><strong class="text-primary">Estado</strong>
                <span
                    class="badge rounded-pill @if ($mensaje->estado->id_estado_mensaje == 1) text-bg-warning @elseif ($mensaje->estado->id_estado_mensaje == 2) text-bg-success @elseif ($mensaje->estado->id_estado_mensaje == 3) text-bg-danger
                                        @else
                                            text-bg-primary @endif"><small>{{ $mensaje->estado->nombre }}</small></span>
            </p>
            <p><strong class="text-primary">Contenido: </strong></p>
            <div id="contenido" class="px-4">{!! $mensaje->contenido !!}</div>

            @can('mensaje_editar')
                <span class="bg-primary mt-5 mb-3"
                    style="width: 100%; height: 2px; border-radius: 1px; display: inline-block"></span>
                <h4>Acciones</h4>
                <div>
                    @if ($mensaje->estado->id_estado_mensaje != 1)
                        <form
                            action="{{ route('mensajes.actualizar_estado', ['mensaje' => $mensaje->id_mensaje, 'nuevoEstado' => 1]) }}"
                            method="POST" style="display: inline;">
                            @csrf
                            <button type="submit" class="btn btn-warning">Marcar como pendiente</button>
                        </form>
                    @endif
                    @if ($mensaje->estado->id_estado_mensaje != 2)
                        <form
                            action="{{ route('mensajes.actualizar_estado', ['mensaje' => $mensaje->id_mensaje, 'nuevoEstado' => 2]) }}"
                            method="POST" style="display: inline;">
                            @csrf
                            <button type="submit" class="btn btn-success">Marcar como solucionado</button>
                        </form>
                    @endif
                    @if ($mensaje->estado->id_estado_mensaje != 3)
                        <form
                            action="{{ route('mensajes.actualizar_estado', ['mensaje' => $mensaje->id_mensaje, 'nuevoEstado' => 3]) }}"
                            method="POST" style="display: inline;">
                            @csrf
                            <button type="submit" class="btn btn-danger">Marcar como rechazado</button>
                        </form>
                    @endif
                </div>
            @endcan

        </div>
    @endsection
</body>

</html>
