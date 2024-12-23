@extends('layout')

@section('content')
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a disabled><svg xmlns="http://www.w3.org/2000/svg" width="18" height="18"
                            viewBox="0 0 24 24" fill="currentColor"
                            class="icon icon-tabler icons-tabler-filled icon-tabler-home">
                            <path stroke="none" d="M0 0h24Vh24H0z" fill="none" />
                            <path
                                d="M12.707 2.293l9 9c.63 .63 .184 1.707 -.707 1.707h-1v6a3 3 0 0 1 -3 3h-1v-7a3 3 0 0 0 -2.824 -2.995l-.176 -.005h-2a3 3 0 0 0 -3 3v7h-1a3 3 0 0 1 -3 -3v-6h-1c-.89 0 -1.337 -1.077 -.707 -1.707l9 -9a1 1 0 0 1 1.414 0m.293 11.707a1 1 0 0 1 1 1v7h-4v-7a1 1 0 0 1 .883 -.993l.117 -.007z" />
                        </svg></a></li>
                <li class="breadcrumb-item"><a href="{{ route('reservas') }}">Inicio</a></li>
                <li class="breadcrumb-item"><a href="{{ route('solicitudes.index') }}">Solicitudes</a></li>
                <li class="breadcrumb-item active" aria-current="page">Asignar ambiente</li>
            </ol>
        </nav>
        <h2 class="text-center mb-4">Asignación de Ambiente</h2>
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
        <div class="row">
            <div class="col-md-6">
                <div class="card mb-3">
                    <div class="card-body">
                        <h4 class="text-center">Datos de los Usuarios</h4>
                        @foreach ($users as $user)
                            <p><strong>Nombre:</strong> {{ $user->usuario->nombre }} {{ $user->usuario->apellido }}</p>
                            <p><strong>Correo:</strong> {{ $user->usuario->email }}</p>
                            <p><strong>Asignaturas:</strong>
                                @foreach ($user->reservasImparte as $imparte)
                                    {{ $imparte->imparte->materia->nombre_materia }} - {{ $imparte->imparte->grupo->nombre }}@if(!$loop->last),@endif
                                @endforeach
                            </p>
                            <hr>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card mb-3">
                    <div class="card-body">
                        <h4 class="text-center">Detalles de la Reserva</h4>
                        <p><strong>Fecha de Reserva:</strong> {{ $reserva->fecha_reserva }}</p>
                        <p><strong>Hora de Reserva:</strong>
                            @foreach ($reserva->reservasPeriodos as $reservaPeriodo)
                                {{ $loop->first ? '' : ', ' }}
                                {{ $reservaPeriodo->periodo->inicio }} - {{ $reservaPeriodo->periodo->fin }}
                            @endforeach
                        </p>
                        <p><strong>Capacidad Solicitada:</strong> {{ $reserva->capacidad_solicitada }}</p>
                        <p><strong>Asignatura:</strong>
                            @foreach ($reserva->reservasImparte as $reservaImparte)
                                {{ $loop->first ? '' : ', ' }}
                                {{ $reservaImparte->imparte->materia->nombre_materia }}
                                {{ $reservaImparte->imparte->grupo->nombre }}
                            @endforeach
                        </p>
                        <p><strong>Carrera:</strong>
                            @php
                                $carreras = [];
                            @endphp
                            @foreach ($reserva->reservasImparte as $reservaImparte)
                                @foreach ($reservaImparte->imparte->impartesCarreras as $imparteCarrera)
                                    @if (!in_array($imparteCarrera->carrera->nombre, $carreras))
                                        @php
                                            $carreras[] = $imparteCarrera->carrera->nombre;
                                        @endphp
                                    @endif
                                @endforeach
                            @endforeach
                            {{ implode(', ', $carreras) }}
                        </p>
                        <p><strong>Descripción:</strong> {{ $reserva->descripcion }}</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="d-flex justify-content-end align-items-center mb-3">
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#confirmModal">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                    class="bi bi-pencil" viewBox="0 0 16 16">
                    <path
                        d="M12.854.146a.5.5 0 0 1 .708 0L15.5 2.086a.5.5 0 0 1 0 .708l-9.5 9.5a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1 .11-.168l9.5-9.5zM11.207 2L3 10.207V11h.793L13 2.793 11.207 2zM1 13.5V15h1.5l.5-.5H2a.5.5 0 0 1-.5-.5v-.5l-.5.5z" />
                </svg>
                Asignar ambientes
            </button>

        </div>
        <h4 class="mb-3">Ambientes Disponibles</h4>
        <form id="assignForm" action="{{ route('solicitudes.confirmarAsignacion', $reserva->id_reserva) }}" method="POST">
            @csrf
            <div class="row">
                @forelse ($ambientesDisponibles as $ambiente)
                    <div class="col-md-4 mb-4">
                        <div class="card rounded card-custom" style="background-color: {{ $ambiente->tipo->color }};">
                            <div class="card-body">
                                <h5 class="card-text">{{ $ambiente->tipo->nombre }}</h5>
                                <h2 class="card-title">{{ $ambiente->nombre }}</h2>
                                <p class="card-text">Ubicación: {{ $ambiente->ubicacion->nombre }}</p>
                                <p class="card-text">Capacidad: {{ $ambiente->capacidad }}</p>
                                <div class="d-flex justify-content-between">
                                    <button class="btn btn-outline-custom" type="button" data-bs-toggle="modal"
                                        data-bs-target="#descriptionModal-{{ $ambiente->id_ambiente }}">Descripción</button>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox"
                                            value="{{ $ambiente->id_ambiente }}"
                                            id="ambiente-{{ $ambiente->id_ambiente }}" name="ambientes[]">
                                        <label class="form-check-label"
                                            for="ambiente-{{ $ambiente->id_ambiente }}">Seleccionar</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center my-2">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" style="width: 16px; height: 16px;">
                            <path
                                d="M256 512A256 256 0 1 0 256 0a256 256 0 1 0 0 512zm0-384c13.3 0 24 10.7 24 24V264c0 13.3-10.7 24-24 24s-24-10.7-24-24V152c0-13.3 10.7-24 24-24zM224 352a32 32 0 1 1 64 0 32 32 0 1 1 -64 0z" />
                        </svg>
                        No hay ambientes disponibles para la reserva.
                    </div>
                @endforelse
            </div>

            <div class="d-flex justify-content-center mt-4">
                <nav aria-label="Page navigation example">
                    <ul class="pagination justify-content-center mt-4">
                        <!-- Enlace "Anterior" -->
                        @if ($ambientesDisponibles->previousPageUrl())
                            <li class="page-item">
                                <a class="page-link"
                                    href="{{ $ambientesDisponibles->previousPageUrl() . '&' . http_build_query(request()->except('page')) }}"
                                    tabindex="-1" aria-disabled="true">Anterior</a>
                            </li>
                        @else
                            <li class="page-item disabled">
                                <span class="page-link">Anterior</span>
                            </li>
                        @endif

                        <!-- Enlaces de páginas -->
                        @foreach ($ambientesDisponibles->getUrlRange(1, $ambientesDisponibles->lastPage()) as $pagina => $url)
                            <li class="page-item {{ $ambientesDisponibles->currentPage() == $pagina ? 'active' : '' }}">
                                <a class="page-link"
                                    href="{{ $url . '&' . http_build_query(request()->except('page')) }}">{{ $pagina }}</a>
                            </li>
                        @endforeach

                        <!-- Enlace "Siguiente" -->
                        @if ($ambientesDisponibles->nextPageUrl())
                            <li class="page-item">
                                <a class="page-link"
                                    href="{{ $ambientesDisponibles->nextPageUrl() . '&' . http_build_query(request()->except('page')) }}">Siguiente</a>
                            </li>
                        @else
                            <li class="page-item disabled">
                                <span class="page-link">Siguiente</span>
                            </li>
                        @endif
                    </ul>
                </nav>
            </div>
        </form>

        <!-- Modal de Confirmación -->
        <div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="confirmModalLabel">Confirmar Asignación</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        ¿Está seguro de que desea asignar los ambientes seleccionados a esta reserva?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="button" class="btn btn-success" id="confirmAssign">Confirmar Asignación</button>
                    </div>
                </div>
            </div>
        </div>

        <style>
            .card-custom {
                border: none;
                color: white;
                padding: 15px;
            }

            .card-title {
                display: block;
                margin-bottom: 0.5rem;
                padding-bottom: 0.5rem;
                border-bottom: 1px solid white;
            }

            .btn-outline-custom {
                color: white;
                border: 1px solid white;
                background-color: transparent;
                margin-top: 0.5rem;
            }

            .btn-outline-custom:hover {
                background-color: white;
                color: black;
            }

            .modal-footer .btn-danger {
                display: none;
            }
        </style>

        @foreach ($ambientesDisponibles as $ambiente)
            <div class="modal fade" id="descriptionModal-{{ $ambiente->id_ambiente }}" tabindex="-1"
                aria-labelledby="descriptionModalLabel-{{ $ambiente->id_ambiente }}" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5" id="descriptionModalLabel-{{ $ambiente->id_ambiente }}">
                                Descripción del ambiente</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <h3><strong>Ambiente: </strong> {{ $ambiente->nombre }}</h3>
                            <p><strong>Tipo de ambiente: </strong> {{ $ambiente->tipo->nombre }}</p>
                            <p><strong>Ubicación: </strong> {{ $ambiente->ubicacion->nombre }}</p>
                            <p><strong>Capacidad: </strong> {{ $ambiente->capacidad }}</p>
                            <p><strong>Descripción: </strong> {{ $ambiente->descripcion }}</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <script>
        document.getElementById('confirmAssign').addEventListener('click', function() {
            document.getElementById('assignForm').submit();
        });
    </script>
@endsection
