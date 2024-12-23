@extends('layout')

@section('content')
    <div class="">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a disabled><svg xmlns="http://www.w3.org/2000/svg" width="18" height="18"
                            viewBox="0 0 24 24" fill="currentColor"
                            class="icon icon-tabler icons-tabler-filled icon-tabler-home">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path
                                d="M12.707 2.293l9 9c.63 .63 .184 1.707 -.707 1.707h-1v6a3 3 0 0 1 -3 3h-1v-7a3 3 0 0 0 -2.824 -2.995l-.176 -.005h-2a3 3 0 0 0 -3 3v7h-1a3 3 0 0 1 -3 -3v-6h-1c-.89 0 -1.337 -1.077 -.707 -1.707l9 -9a1 1 0 0 1 1.414 0m.293 11.707a1 1 0 0 1 1 1v7h-4v-7a1 1 0 0 1 .883 -.993l.117 -.007z" />
                        </svg></a></li>
                <li class="breadcrumb-item"><a href="{{ route('reservas') }}">Inicio</a></li>
                <li class="breadcrumb-item active" aria-current="page">Reservas</li>
                <li class="breadcrumb-item active" aria-current="page">Mis reservas</li>
            </ol>
        </nav>
        <h2 class="text-center">Mis Reservas</h2>
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
        <div class="d-flex align-items-start gap-2 justify-content-start my-3">
            <form action="{{ route('mis_reservas.index') }}" id="filter_form" method="GET">
                @csrf
                <div class="d-flex flex-wrap gap-2 align-items-center">
                    <div class="align-items-center">
                        <small><label class="form-label" for="order_by">Ordenar por</label></small>
                        <select class="form-select form-select-sm" name="order_by" id="order_by">
                            <option value="fecha_reserva" {{ request('order_by') == 'fecha_reserva' ? 'selected' : '' }}>
                                Fecha de reserva</option>
                            <option value="fecha_solicitud"
                                {{ request('order_by') == 'fecha_solicitud' ? 'selected' : '' }}>
                                Fecha de solicitud</option>
                        </select>
                    </div>
                    <div class="align-items-center">
                        <small><label class="form-label" for="order">Orden</label></small>
                        <select class="form-select form-select-sm" name="order" id="order">
                            <option value="asc" {{ request('order') == 'asc' ? 'selected' : '' }}>Ascendente</option>
                            <option value="desc" {{ request('order') == 'desc' ? 'selected' : '' }}>Descendente</option>
                        </select>
                    </div>
                    <div class="align-items-center">
                        <small><label class="form-label" for="status">Estado</label></small>
                        <select class="form-select form-select-sm" name="status" id="status">
                            <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>Todos</option>
                            <option value="pendiente" {{ request('status') == 'pendiente' ? 'selected' : '' }}>Pendiente
                            </option>
                            <option value="aceptado" {{ request('status') == 'aceptado' ? 'selected' : '' }}>Aceptado
                            </option>
                            <option value="rechazado" {{ request('status') == 'rechazado' ? 'selected' : '' }}>Rechazado
                            </option>
                            <option value="cancelado" {{ request('status') == 'cancelado' ? 'selected' : '' }}>Cancelado
                            </option>
                        </select>
                    </div>
                    <div class="align-items-center">
                        <small><label class="form-label" for="tipo">Tipos</label></small>
                        <select class="form-select form-select-sm" name="tipo" id="tipo">
                            <option value="all" {{ request('tipo') == 'all' ? 'selected' : '' }}>Todos</option>
                            @foreach ($tipos as $tipo)
                                <option value="{{ $tipo->id_tipo }}"
                                    {{ request('tipo') == $tipo->id_tipo ? 'selected' : '' }}>
                                    {{ $tipo->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="align-items-center">
                        <small><label class="form-label" for="tipo_reserva">Tipo de reserva</label></small>
                        <select class="form-select form-select-sm" name="tipo_reserva" id="tipo_reserva">
                            <option value="all" {{ request('tipo_reserva') == 'all' ? 'selected' : '' }}>Todas</option>
                            <option value="especifico" {{ request('tipo_reserva') == 'especifico' ? 'selected' : '' }}>
                                Específico</option>
                            <option value="generico" {{ request('tipo_reserva') == 'generico' ? 'selected' : '' }}>
                                Genérico</option>
                        </select>
                    </div>
                    <div class="align-items-center">
                        <small><label class="form-label" for="sort_all">Ver</label></small>
                        <select class="form-select form-select-sm" name="all" id="sort_all">
                            <option value="all" {{ request('all') == 'all' ? 'selected' : '' }}>Todas</option>
                            <option value="range" {{ request('all') == 'range' ? 'selected' : '' }}>Filtrar por rango
                            </option>
                        </select>
                    </div>
                    <div class="gap-2" id="date_container" style="display: none">
                        <div class="align-items-center">
                            <small><label class="form-label" for="date_from">Inicio</label></small>
                            <input class="form-control form-control-sm" type="date" name="date_from" id="date_from"
                                value="{{ request('date_from') ? request('date_from') : date('Y-m-d') }}"
                                max="{{ date('Y-m-d') }}">
                        </div>
                        <div class="align-items-center">
                            <small><label class="form-label" for="date_to">Fin</label></small>
                            <input class="form-control form-control-sm" type="date" name="date_to" id="date_to"
                                value="{{ request('date_to') ? request('date_to') : date('Y-m-d') }}">
                        </div>
                    </div>
                </div>
                <div class="d-flex align-items-end mt-2 gap-1">
                    <button type="submit" class="btn btn-primary d-flex align-items-center gap-1" id="btn_submit">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"
                            stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-filter">
                            <path stroke="none" d="M0 0h24Vh24H0z" fill="none" />
                            <path
                                d="M4 4h16v2.172a2 2 0 0 1 -.586 1.414l-4.414 4.414v7l-6 2v-8.5l-4.48 -4.928a2 2 0 0 1 -.52 -1.345v-2.227z" />
                        </svg> Filtrar
                    </button>
                    <a href="{{ route('mis_reservas.index') }}"
                        class="btn btn-secondary d-flex align-items-center gap-1"><svg xmlns="http://www.w3.org/2000/svg"
                            width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"
                            class="icon icon-tabler icons-tabler-outline icon-tabler-filter-off">
                            <path stroke="none" d="M0 0h24Vh24H0z" fill="none" />
                            <path
                                d="M8 4h12v2.172a2 2 0 0 1 -.586 1.414l-3.914 3.914m-.5 3.5v4l-6 2v-8.5l-4.48 -4.928a2 2 0 0 1 -.52 -1.345v-2.227" />
                            <path d="M3 3l18 18" />
                        </svg>Limpiar</a>
                </div>
            </form>
        </div>
        <div class="table-responsive">
            <table class="table table-hover table-striped">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">ID</th>
                        <th scope="col">Tipo</th>
                        <th scope="col">Ambiente</th>
                        <th scope="col">Fecha de reserva</th>
                        <th scope="col">Hora de reserva</th>
                        <th scope="col">Materia</th>
                        <th scope="col">Estado</th>
                        <th scope="col">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $index = 1; ?>
                    @forelse ($reservas as $reserva)
                        <tr>
                            <th class="align-middle" scope="row">{{ $index }}</th>
                            <th class="align-middle" scope="row">{{ $reserva->id_reserva }}</th>
                            <td class="text-nowrap align-middle">
                              {{ $reserva->tipoAmbiente->nombre }}
                            </td>
                            <td class="text-nowrap align-middle">
                                @if ($reserva->generico)
                                    <span class="badge rounded-pill bg-warning text-dark">
                                        SIN ASIGNAR
                                    </span>
                                @else
                                    @if ($reserva->ambientes->count() > 1)
                                        {{ $reserva->ambientes->first()->nombre }}, ...
                                    @else
                                        {{ $reserva->ambientes->first()->nombre }}
                                    @endif
                                @endif
                            </td>
                            <td class="text-nowrap align-middle">{{ $reserva->fecha_reserva }}</td>
                            <td class="text-nowrap align-middle">
                                @if ($reserva->reservasPeriodos->count() > 0)
                                    {{ $reserva->reservasPeriodos->first()->periodo->inicio }} -
                                    {{ $reserva->reservasPeriodos->first()->periodo->fin }}
                                    @if ($reserva->reservasPeriodos->count() > 1)
                                        , ...
                                    @endif
                                @endif
                            </td>
                            <td class="text-nowrap align-middle">
                                @if ($reserva->reservasImparte->count() > 0)
                                    {{ $reserva->reservasImparte->first()->imparte->materia->nombre_materia }}
                                    {{ $reserva->reservasImparte->first()->imparte->grupo->nombre }}
                                    @if ($reserva->reservasImparte->count() > 1)
                                        , ...
                                    @endif
                                @endif
                            </td>
                            <td class="text-nowrap align-middle">
                                <span
                                    class="badge rounded-pill @if ($reserva->estado->nombre == 'ACEPTADO') bg-success @elseif($reserva->estado->nombre == 'PENDIENTE') bg-warning text-dark @elseif($reserva->estado->nombre == 'RECHAZADO' || $reserva->estado->nombre == 'CANCELADO') bg-danger @endif">
                                    {{ strtoupper($reserva->estado->nombre) }}
                                </span>
                            </td>
                            <td class="d-flex gap-1">
                                <button class="btn btn-outline-primary" data-bs-placement="left" title="Detalles"
                                    data-bs-toggle="modal" data-bs-target="#detailsModal{{ $reserva->id_reserva }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round"
                                        class="icon icon-tabler icons-tabler-outline icon-tabler-list-details">
                                        <path stroke="none" d="M0 0h24Vh24H0z" fill="none" />
                                        <path d="M13 5h8" />
                                        <path d="M13 9h5" />
                                        <path d="M13 15h8" />
                                        <path d="M13 19h5" />
                                        <path
                                            d="M3 4m0 1a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v4a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z" />
                                        <path
                                            d="M3 14m0 1a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v4a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z" />
                                    </svg>
                                </button>
                                @if (in_array($reserva->estado->nombre, ['PENDIENTE', 'ACEPTADO']) && $currentDate <= $reserva->fecha_reserva)
                                    <button class="btn btn-outline-danger" data-bs-toggle="modal"
                                        title="Cancelar reserva" data-bs-target="#cancelModal{{ $reserva->id_reserva }}">
                                        Cancelar
                                    </button>
                                @endif
                            </td>
                            <?php $index++; ?>
                        </tr>

                        <div class="modal fade" id="detailsModal{{ $reserva->id_reserva }}" tabindex="-1"
                            aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div
                                        class="modal-header text-white @if ($reserva->estado->nombre == 'ACEPTADO') bg-success @elseif($reserva->estado->nombre == 'PENDIENTE') bg-warning text-dark @elseif($reserva->estado->nombre == 'RECHAZADO' || $reserva->estado->nombre == 'CANCELADO') bg-danger @endif">
                                        <h1 class="modal-title fs-5" id="exampleModalLabel">Detalles de reserva</h1>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div>
                                            <p><strong>Ambiente(s):</strong>
                                                @if ($reserva->generico)
                                                    <span class="badge rounded-pill bg-warning text-dark">
                                                        SIN ASIGNAR
                                                    </span>
                                                    ({{ $reserva->tipoAmbiente->nombre }})
                                                @else
                                                    @foreach ($reserva->ambientes as $ambiente)
                                                        {{ $loop->first ? '' : ', ' }}
                                                        {{ $ambiente->nombre }} ({{ $ambiente->tipo->nombre }})
                                                    @endforeach
                                                @endif
                                            </p>
                                            <p><strong>Capacidad Total:</strong> {{ $reserva->capacidad_total }}</p>
                                            <p><strong>Capacidad Solicitada:</strong> {{ $reserva->capacidad_solicitada }}
                                            </p>
                                            <p><strong>Ubicación:</strong>
                                                @if ($reserva->generico)
                                                    -
                                                @else
                                                    @foreach ($reserva->ambientes as $ambiente)
                                                        {{ $loop->first ? '' : ', ' }}
                                                        {{ $ambiente->ubicacion->nombre }}
                                                    @endforeach
                                                @endif
                                            </p>
                                            <p><strong>Fecha de reserva:</strong> {{ $reserva->fecha_reserva }}</p>
                                            <p><strong>Fecha de solicitud:</strong> {{ $reserva->fecha_solicitud }}</p>
                                            <p><strong>Hora de reserva:</strong>
                                                @foreach ($reserva->reservasPeriodos as $reservaPeriodo)
                                                    {{ $loop->first ? '' : ', ' }}
                                                    {{ $reservaPeriodo->periodo->inicio }} -
                                                    {{ $reservaPeriodo->periodo->fin }}
                                                @endforeach
                                            </p>
                                            <p><strong>Estado:</strong>
                                                <span
                                                    class="badge rounded-pill @if ($reserva->estado->nombre == 'ACEPTADO') bg-success @elseif($reserva->estado->nombre == 'PENDIENTE') bg-warning text-dark @elseif($reserva->estado->nombre == 'RECHAZADO' || $reserva->estado->nombre == 'CANCELADO') bg-danger @endif">
                                                    {{ strtoupper($reserva->estado->nombre) }}
                                                </span>
                                            </p>
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
                                            <p><strong>Grupal:</strong> {{ $reserva->grupal ? 'Sí' : 'No' }}</p>
                                            <p><strong>Descripción:</strong> {{ $reserva->descripcion }}</p>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        @if (in_array($reserva->estado->nombre, ['PENDIENTE', 'ACEPTADO']) && $currentDate <= $reserva->fecha_reserva)
                                            <button class="btn btn-danger" data-bs-toggle="modal"
                                                data-bs-target="#cancelModal{{ $reserva->id_reserva }}">
                                                Cancelar reserva
                                            </button>
                                        @endif
                                        <button type="button" class="btn btn-secondary"
                                            data-bs-dismiss="modal">Cerrar</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal fade" id="cancelModal{{ $reserva->id_reserva }}" tabindex="-1"
                            aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <diva class="modal-content">
                                    <div class="modal-header text-white bg-danger">
                                        <svg width="24px" height="24px" viewBox="0 0 24 24" fill="none"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path
                                                d="M21.7605 15.92L15.3605 4.4C14.5005 2.85 13.3105 2 12.0005 2C10.6905 2 9.50047 2.85 8.64047 4.4L2.24047 15.92C1.43047 17.39 1.34047 18.8 1.99047 19.91C2.64047 21.02 3.92047 21.63 5.60047 21.63H18.4005C20.0805 21.63 21.3605 21.02 22.0105 19.91C22.6605 18.8 22.5705 17.38 21.7605 15.92ZM11.2505 9C11.2505 8.59 11.5905 8.25 12.0005 8.25C12.4105 8.25 12.7505 8.59 12.7505 9V14C12.7505 14.41 12.4105 14.75 12.0005 14.75C11.5905 14.75 11.2505 14.41 11.2505 14V9ZM12.7105 17.71C12.6605 17.75 12.6105 17.79 12.5605 17.83C12.5005 17.87 12.4405 17.9 12.3805 17.92C12.3205 17.95 12.2605 17.97 12.1905 17.98C12.1305 17.99 12.0605 18 12.0005 18C11.9405 18 11.8705 17.99 11.8005 17.98C11.7405 17.97 11.6805 17.95 11.6205 17.92C11.5605 17.9 11.5005 17.87 11.4405 17.83C11.3905 17.79 11.3405 17.75 11.2905 17.71C11.1105 17.52 11.0005 17.26 11.0005 17C11.0005 16.74 11.1105 16.48 11.2905 16.29C11.3405 16.25 11.3905 16.21 11.4405 16.17C11.5005 16.13 11.5605 16.1 11.6205 16.08C11.6805 16.05 11.7405 16.03 11.8005 16.02C11.9305 15.99 12.0705 15.99 12.1905 16.02C12.2605 16.03 12.3205 16.05 12.3805 16.08C12.4405 16.1 12.5005 16.13 12.5605 16.17C12.6105 16.21 12.6605 16.25 12.7105 16.29Z"
                                                fill="#FFFFFF" />
                                        </svg>
                                        <h1 class="modal-title fs-5" id="exampleModalLabel">Confirmar cancelación
                                        </h1>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <p><strong>¡Cuidado!</strong> ¿Estás seguro de que deseas
                                            <strong>cancelar</strong> esta reserva?
                                            <br><br><strong>Nota:</strong> Esta acción no se puede revertir.
                                        </p>
                                    </div>
                                    <div class="modal-footer">
                                        <form method="POST"
                                            action="{{ route('mis_reservas.cancel', $reserva->id_reserva) }}">
                                            @csrf
                                            @method('PATCH')
                                            <button type="button" class="btn btn-secondary"
                                                data-bs-dismiss="modal">Cerrar</button>
                                            <button type="submit" class="btn btn-danger">Cancelar reserva</button>
                                        </form>
                                    </div>
                                </diva>
                            </div>
                        </div>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center">No hay reservas</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <nav aria-label="Page navigation example">
            <ul class="pagination justify-content-center mt-4">
                <!-- Enlace "Anterior" -->
                @if ($reservas->previousPageUrl())
                    <li class="page-item">
                        <a class="page-link"
                            href="{{ $reservas->previousPageUrl() . '&' . http_build_query(request()->except('page')) }}"
                            tabindex="-1" aria-disabled="true">Anterior</a>
                    </li>
                @else
                    <li class="page-item disabled">
                        <span class="page-link">Anterior</span>
                    </li>
                @endif

                <!-- Enlaces de páginas -->
                @foreach ($reservas->getUrlRange(1, $reservas->lastPage()) as $pagina => $url)
                    <li class="page-item {{ $reservas->currentPage() == $pagina ? 'active' : '' }}">
                        <a class="page-link"
                            href="{{ $url . '&' . http_build_query(request()->except('page')) }}">{{ $pagina }}</a>
                    </li>
                @endforeach

                <!-- Enlace "Siguiente" -->
                @if ($reservas->nextPageUrl())
                    <li class="page-item">
                        <a class="page-link"
                            href="{{ $reservas->nextPageUrl() . '&' . http_build_query(request()->except('page')) }}">Siguiente</a>
                    </li>
                @else
                    <li class="page-item disabled">
                        <span class="page-link">Siguiente</span>
                    </li>
                @endif
            </ul>
        </nav>
    </div>

    <script>
        window.onload = function() {
            const dateContainer = document.getElementById('date_container');
            const sortAll = document.getElementById('sort_all');
            if (sortAll.value === 'all') {
                dateContainer.style.display = 'none';
            } else {
                dateContainer.style.display = 'flex';
            }
            sortAll.addEventListener('change', function() {
                if (this.value === 'all') {
                    dateContainer.style.display = 'none';
                } else {
                    dateContainer.style.display = 'flex';
                }
            });
            document.getElementById('filter_form').addEventListener('submit', function(e) {
                document.getElementById('btn_submit').style.disabled = true;
            });
        }
    </script>
@endsection
