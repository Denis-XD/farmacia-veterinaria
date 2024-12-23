<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link href="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.snow.css" rel="stylesheet" />
    <title>Notificación</title>
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
                {{-- <div class="d-flex justify-content-start">
                    <a href="{{ route('notificaciones.index') }}" class="btn btn-primary d-flex align-items-center gap-1"
                        style="max-width: max-content;"><svg xmlns="http://www.w3.org/2000/svg" width="18"
                            height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round"
                            class="icon icon-tabler icons-tabler-outline icon-tabler-chevron-left">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M15 6l-6 6l6 6" />
                        </svg> Volver</a>
                </div> --}}
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
                        <li class="breadcrumb-item"><a href="{{ route('notificaciones.index') }}">Notificaciones</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Detalles notificación</li>
                    </ol>
                </nav>
                <h2 class="text-center">Detalles notificación</h2>
            </div>
            <p><strong class="text-primary">Asunto: </strong>{{ $notificacion->asunto }}</p>
            <p><strong class="text-primary">Fecha:
                </strong>{{ date('d/m/Y - H:i:s', strtotime($notificacion->created_at)) }}</p>
            <p><strong class="text-primary">Contenido: </strong></p>
            <div id="contenido" class="px-4">{!! $notificacion->contenido !!}</div>
            <p><strong class="text-primary">Destinatarios: </strong></p>
            <ul class="list-group">
                @foreach ($notificacion->destinatarios as $destinatario)
                    <li class="list-group-item d-flex align-items-center gap-2">
                        @forelse ($destinatario->usuario->roles as $rol)
                            <span class="badge bg-primary">{{ $rol->name }}</span>
                        @empty
                            <span class="badge bg-danger">Sin rol</span>
                        @endforelse{{ $destinatario->usuario->nombre }}
                        {{ $destinatario->usuario->apellido }}
                        ({{ $destinatario->usuario->email }})
                    </li>
                @endforeach
            </ul>

        </div>
    @endsection
</body>

</html>
