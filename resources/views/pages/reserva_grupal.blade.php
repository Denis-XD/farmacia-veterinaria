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
                <li class="breadcrumb-item"><a href="{{ route('ambientes.indexAmbientes') }}">Reservar</a></li>
                <li class="breadcrumb-item active" aria-current="page">Reserva grupal</li>
            </ol>
        </nav>
        <h2 class="text-center mb-4">Reserva Grupal</h2>
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
        <div class="text-end mb-5 mt-5">
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#confirmModal">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-save" viewBox="0 0 16 16">
                    <path d="M8.5 1.5a.5.5 0 0 1 .5.5v3a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5v-3a.5.5 0 0 1 .5-.5h1zM8 0a2 2 0 0 0-2 2v3a2 2 0 0 0 2 2h1a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2H8zM1 3a2 2 0 0 1 2-2h3a1 1 0 0 1 1 1v4H2a1 1 0 0 1-1-1V3z"/>
                    <path d="M2 8.5v-3a.5.5 0 0 1 .5-.5H6a.5.5 0 0 1 .5.5v3a.5.5 0 0 1-.5.5H2.5a.5.5 0 0 1-.5-.5zM1 10.5v.5a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2v-.5H1z"/>
                </svg> Finalizar
            </button>
        </div>
        <form id="reservaGrupalForm" method="POST" action="{{ route('reserva.grupal.store') }}">
            @csrf
            <div class="row">
                <!-- Sección de Solicitud de Reserva -->
                <div class="col-md-6">
                    <div class="card mb-3">
                        <div class="card-body">
                            <h4 class="text-center">Solicitud de Reserva</h4>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="fecha_reserva">Fecha de reserva:</label>
                                    <input type="date" id="fecha_reserva" name="fecha_reserva" class="form-control"
                                        required min="{{ date('Y-m-d') }}">
                                </div>
                                <div class="col-md-6">
                                    <label for="id_tipo">Tipo de ambiente:</label>
                                    <select class="form-select" id="id_tipo" name="id_tipo" required>
                                        @foreach ($tipos as $tipo)
                                            <option value="{{ $tipo->id_tipo }}">{{ $tipo->nombre }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="capacidad">Capacidad:</label>
                                    <input type="number" class="form-control" id="capacidad" name="capacidad"
                                        min="50" max="500" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="periodo">Periodo:</label>
                                    <select class="form-select" id="periodo" name="id_periodo[]" multiple required>
                                        @foreach ($periodos as $periodo)
                                            <option value="{{ $periodo->id_periodo }}">{{ $periodo->inicio }} -
                                                {{ $periodo->fin }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="id_materia">Materias y Grupos:</label>
                                @php
                                    $materiasAgrupadas = [];
                                    foreach ($user->impartes as $imparte) {
                                        $materiasAgrupadas[$imparte->materia->nombre_materia][] = $imparte;
                                    }
                                @endphp

                                @foreach ($materiasAgrupadas as $nombreMateria => $impartes)
                                    <div class="mb-2">
                                        <strong>{{ $nombreMateria }}</strong>
                                        @foreach ($impartes as $imparte)
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox"
                                                    value="{{ $imparte->id_imparte }}" name="id_materia[]">
                                                <label class="form-check-label">{{ $imparte->grupo->nombre }}</label>
                                            </div>
                                        @endforeach
                                    </div>
                                @endforeach
                            </div>
                            <div class="mb-3">
                                <label for="descripcion">Descripción:</label>
                                <textarea id="descripcion" name="descripcion" class="form-control" rows="3" maxlength="100"></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sección de Colaboradores -->
                <div class="col-md-6">
                    <div class="card mb-3">
                        <div class="card-body">
                            <h4 class="text-center">Colaboradores</h4>
                            <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Nombre</th>
                                            <th>Materia</th>
                                            <th>Grupo</th>
                                            <th>Seleccionar</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($compartenMaterias as $colaborador)
                                            @foreach ($colaborador->impartes as $imparte)
                                                @if (in_array($imparte->materia->id_materia, $user->impartes->pluck('id_materia')->toArray()))
                                                    <tr>
                                                        <td>{{ $colaborador->nombre }} {{ $colaborador->apellido }}</td>
                                                        <td>{{ $imparte->materia->nombre_materia }}</td>
                                                        <td>{{ $imparte->grupo->nombre }}</td>
                                                        <td>
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="checkbox"
                                                                    value="{{ $colaborador->id }}" name="colaboradores[]">
                                                                <input type="hidden"
                                                                    name="colaborador_materias[{{ $colaborador->id }}][]"
                                                                    value="{{ $imparte->id_imparte }}">
                                                                <label class="form-check-label">Seleccionar</label>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endif
                                            @endforeach
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal de Confirmación -->
            <div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel"
                aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="confirmModalLabel">Confirmar Solicitud de Reserva</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            ¿Está seguro de que desea realizar esta solicitud de reserva grupal?
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="button" class="btn btn-success" id="confirmReserva">Confirmar Reserva</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        <script>
            document.getElementById('confirmReserva').addEventListener('click', function() {
                document.getElementById('reservaGrupalForm').submit();
            });
        </script>
    </div>
@endsection
