<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link href="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.snow.css" rel="stylesheet" />
    <title>Nuevo mensaje</title>
    <style>
        .suggestions {
            max-height: 200px;
            overflow-y: auto;
        }

        .icon-btn {
            cursor: pointer;
        }
    </style>
</head>

<body>
    @extends('layout')

    @section('content')
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
                    <li class="breadcrumb-item active" aria-current="page">Nuevo mensaje</li>
                </ol>
            </nav>
            <h2 class="text-center">Crear nuevo mensaje</h2>
        </div>
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
        <div class="px-md-5 px-0">
            <form id="form" action="{{ route('mensajes.store') }}" method="POST">
                @csrf
                <div class="row col col-md-6 mb-3">
                    <div class="" style="width: 100%">
                        <label for="asunto" class="form-label">Asunto</label>
                        <input type="text" class="form-control" id="asunto" name="asunto"
                            pattern="[A-Za-z0-9ñÑáéíóúÁÉÍÓÚ ]+" maxlength="50" minlength="4"
                            oninput="this.value = this.value.replace(/[^A-Za-zñÑáéíóúÁÉÍÓÚ ]/g, '').substring(0, 50)"
                            required>
                    </div>
                </div>
                <div class="mb-3">
                    <label for="descripcion" class="form-label">Contenido</label>
                    <div id="contenido" class="bg-white">

                    </div>
                </div>
                <input type="hidden" name="contenido" id="contenidoInput">
                <button type="submit" id="enviar" class="btn btn-primary">Enviar</button>
            </form>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.js"></script>
        <script>
            const quill = new Quill('#contenido', {
                theme: 'snow'
            });

            document.getElementById('form').addEventListener('submit', function(event) {
                document.getElementById('contenidoInput').value = quill.getSemanticHTML(0, quill.getLength());
            });
        </script>
    @endsection
</body>

</html>
