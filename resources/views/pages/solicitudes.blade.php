@extends('layout')

@section('content')
    <div class="">
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
                <li class="breadcrumb-item active" aria-current="page">Reservas</li>
                <li class="breadcrumb-item active" aria-current="page">Solicitudes</li>
            </ol>
        </nav>
        <h2 class="text-center">Solicitudes</h2>
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
            <form action="{{ route('solicitudes.index') }}" id="filter_form" method="GET">
                @csrf
                <div class="d-flex flex-wrap gap-2 align-items-center">
                    <div class="align-items-center">
                        <small><label class="form-label" for="order_by">Ordenar por</label></small>
                        <select class="form-select form-select-sm" name="order_by" id="order_by">
                            <option value="fecha_solicitud"
                                {{ request('order_by') == 'fecha_solicitud' ? 'selected' : '' }}>Fecha de solicitud</option>
                            <option value="fecha_reserva" {{ request('order_by') == 'fecha_reserva' ? 'selected' : '' }}>
                                Fecha de reserva</option>
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
                            <option value="all" {{ request('status') == 'todos' ? 'selected' : '' }}>Todos</option>
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
                    <a href="{{ route('solicitudes.index') }}" class="btn btn-secondary d-flex align-items-center gap-1">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"
                            stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-filter-off">
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
                        <th scope="col">Nombre</th>
                        <th scope="col">Ambiente</th>
                        <th scope="col">Fecha de solicitud</th>
                        <th scope="col">Fecha de reserva</th>
                        <th scope="col">Hora de reserva</th>
                        <th scope="col">Estado</th>
                        <th scope="col">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $index = 1; ?>
                    @forelse ($solicitudes as $solicitud)
                        <tr>
                            <th class="align-middle" scope="row">{{ $index }}</th>
                            <th class="align-middle" scope="row">{{ $solicitud->id_reserva }}</th>
                            <td class="text-nowrap align-middle">
                                @if ($solicitud->usuarios->count() > 1)
                                    {{ $solicitud->usuarios->first()->usuario->apellido }}
                                    {{ $solicitud->usuarios->first()->usuario->nombre }}, ...
                                @else
                                    {{ $solicitud->usuarios->first()->usuario->apellido }}
                                    {{ $solicitud->usuarios->first()->usuario->nombre }}
                                @endif
                            </td>
                            <td class="text-nowrap align-middle">
                                @if ($solicitud->generico)
                                    <span class="badge rounded-pill bg-warning text-dark">
                                        SIN ASIGNAR
                                    </span>
                                @else
                                    @if ($solicitud->ambientes->count() > 0)
                                        {{ $solicitud->ambientes->first()->nombre }}
                                        @if ($solicitud->ambientes->count() > 1)
                                            , ...
                                        @endif
                                    @endif
                                @endif
                            </td>
                            <td class="text-nowrap align-middle">{{ $solicitud->fecha_solicitud }}</td>
                            <td class="text-nowrap align-middle">{{ $solicitud->fecha_reserva }}</td>
                            <td class="text-nowrap align-middle">
                                @if ($solicitud->reservasPeriodos->count() > 0)
                                    {{ $solicitud->reservasPeriodos->first()->periodo->inicio }} -
                                    {{ $solicitud->reservasPeriodos->first()->periodo->fin }}
                                    @if ($solicitud->reservasPeriodos->count() > 1)
                                        , ...
                                    @endif
                                @endif
                            </td>
                            <td class="text-nowrap align-middle">
                                <span
                                    class="badge rounded-pill
                                    @if ($solicitud->estado->nombre == 'ACEPTADO') bg-success
                                    @elseif($solicitud->estado->nombre == 'PENDIENTE') bg-warning text-dark
                                    @elseif($solicitud->estado->nombre == 'RECHAZADO' || $solicitud->estado->nombre == 'CANCELADO') bg-danger @endif">
                                    {{ strtoupper($solicitud->estado->nombre) }}
                                </span>
                            </td>
                            <td class="d-flex gap-1">
                                <button class="btn btn-outline-primary" data-bs-placement="left" title="Detalles"
                                    data-bs-toggle="modal" data-bs-target="#detailsModal{{ $solicitud->id_reserva }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round"
                                        class="icon icon-tabler icons-tabler-outline icon-tabler-list-details">
                                        <path stroke="none" d="M0 0h24h24" fill="none" />
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
                                @if ($solicitud->estado->nombre == 'PENDIENTE' || $solicitud->estado->nombre == 'RECHAZADO')
                                    @can('solicitud_aceptar')
                                        @if ($solicitud->generico)
                                            <a href="{{ route('solicitudes.asignarAmbiente', ['reserva_id' => $solicitud->id_reserva]) }}"
                                                class="btn btn-outline-success" title="Aceptar">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                    viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                    class="icon icon-tabler icons-tabler-outline icon-tabler-circle-check">
                                                    <path stroke="none" d="M0 0h24h24" fill="none" />
                                                    <path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" />
                                                    <path d="M9 12l2 2l4 -4" />
                                                </svg>
                                            </a>
                                        @else
                                            <button class="btn btn-outline-success" data-bs-toggle="modal"
                                                data-bs-target="#acceptModal{{ $solicitud->id_reserva }}" title="Aceptar">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                    viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                    class="icon icon-tabler icons-tabler-outline icon-tabler-circle-check">
                                                    <path stroke="none" d="M0 0h24h24" fill="none" />
                                                    <path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" />
                                                    <path d="M9 12l2 2l4 -4" />
                                                </svg>
                                            </button>
                                        @endif
                                    @endcan
                                @endif
                                @if ($solicitud->estado->nombre == 'PENDIENTE' || $solicitud->estado->nombre == 'ACEPTADO')
                                    @can('solicitud_rechazar')
                                        <button class="btn btn-outline-danger" data-bs-toggle="modal"
                                            data-bs-target="#rejectModal{{ $solicitud->id_reserva }}" title="Rechazar">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                                stroke-linecap="round" stroke-linejoin="round"
                                                class="icon icon-tabler icons-tabler-outline icon-tabler-ban">
                                                <path stroke="none" d="M0 0h24h24" fill="none" />
                                                <path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" />
                                                <path d="M5.7 5.7l12.6 12.6" />
                                            </svg>
                                        </button>
                                    @endcan
                                @endif
                            </td>
                           
                        </tr>
                        
                        <!-- Modal Detalles -->
                        <div class="modal fade" id="detailsModal{{ $solicitud->id_reserva }}" tabindex="-1"
                            aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div
                                        class="modal-header text-white
                                        @if ($solicitud->estado->nombre == 'ACEPTADO') bg-success
                                        @elseif($solicitud->estado->nombre == 'PENDIENTE') bg-warning text-dark
                                        @elseif($solicitud->estado->nombre == 'RECHAZADO' || $solicitud->estado->nombre == 'CANCELADO') bg-danger @endif">
                                        <h1 class="modal-title fs-5" id="exampleModalLabel">Detalles de reserva</h1>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div>
                                            <p><strong>Fecha de solicitud:</strong> {{ $solicitud->fecha_solicitud }}</p>
                                            <p><strong>Fecha de reserva:</strong> {{ $solicitud->fecha_reserva }}</p>
                                            <p><strong>Hora de reserva:</strong>
                                                @foreach ($solicitud->reservasPeriodos as $reservaPeriodo)
                                                    {{ $loop->first ? '' : ', ' }}
                                                    {{ $reservaPeriodo->periodo->inicio }} -
                                                    {{ $reservaPeriodo->periodo->fin }}
                                                @endforeach
                                            </p>
                                            <p><strong>Estado:</strong>
                                                <span
                                                    class="badge rounded-pill
                                                    @if ($solicitud->estado->nombre == 'ACEPTADO') bg-success
                                                    @elseif($solicitud->estado->nombre == 'PENDIENTE') bg-warning text-dark
                                                    @elseif($solicitud->estado->nombre == 'RECHAZADO' || $solicitud->estado->nombre == 'CANCELADO') bg-danger @endif">
                                                    {{ strtoupper($solicitud->estado->nombre) }}
                                                </span>
                                            </p>
                                            <p><strong>Nombre:</strong>
                                                @foreach ($solicitud->usuarios as $index => $usuario)
                                                    {{ $usuario->usuario->apellido }} {{ $usuario->usuario->nombre }}
                                                    @if ($index < $solicitud->usuarios->count() - 1)
                                                        ,
                                                    @endif
                                                @endforeach
                                            </p>
                                            <p><strong>Correo:</strong>
                                                @foreach ($solicitud->usuarios as $index => $usuario)
                                                    {{ $usuario->usuario->email }}@if ($index < $solicitud->usuarios->count() - 1)
                                                        ,
                                                    @endif
                                                @endforeach
                                            </p>
                                            <p><strong>Ambiente(s):</strong>
                                                @if ($solicitud->generico)
                                                    <span class="badge rounded-pill bg-warning text-dark">
                                                        SIN ASIGNAR
                                                    </span>
                                                    ({{ $solicitud->tipoAmbiente->nombre }})
                                                @else
                                                    @foreach ($solicitud->ambientes as $ambiente)
                                                        {{ $loop->first ? '' : ', ' }}
                                                        {{ $ambiente->nombre }} ({{ $ambiente->tipo->nombre }})
                                                    @endforeach
                                                @endif
                                            </p>
                                            <p><strong>Capacidad Total:</strong> {{ $solicitud->capacidad_total }}</p>
                                            <p><strong>Capacidad Solicitada:</strong>
                                                {{ $solicitud->capacidad_solicitada }}
                                            <p><strong>Ubicación:</strong>
                                                @if ($solicitud->generico)
                                                    -
                                                @else
                                                    {{ $solicitud->ambientes->first()->ubicacion->nombre }}
                                                @endif
                                            </p>
                                            <p><strong>Grupal:</strong> {{ $solicitud->grupal ? 'Sí' : 'No' }}</p>
                                            <p><strong>Descripción:</strong> {{ $solicitud->descripcion }}</p>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        @if ($solicitud->estado->nombre == 'PENDIENTE' || $solicitud->estado->nombre == 'RECHAZADO')
                                            @can('solicitud_aceptar')
                                                @if ($solicitud->generico)
                                                    <a href="{{ route('solicitudes.asignarAmbiente', ['reserva_id' => $solicitud->id_reserva]) }}"
                                                        class="btn btn-success">
                                                        Aceptar solicitud
                                                    </a>
                                                @else
                                                    <button type="button" class="btn btn-success" data-bs-toggle="modal"
                                                        data-bs-target="#acceptModal{{ $solicitud->id_reserva }}">
                                                        Aceptar solicitud
                                                    </button>
                                                @endif
                                            @endcan
                                        @endif

                                        @if ($solicitud->estado->nombre == 'PENDIENTE' || $solicitud->estado->nombre == 'ACEPTADO')
                                            @can('solicitud_rechazar')
                                                <button type="button" class="btn btn-danger" data-bs-toggle="modal"
                                                    data-bs-target="#rejectModal{{ $solicitud->id_reserva }}">
                                                    Rechazar solicitud
                                                </button>
                                            @endcan
                                        @endif
                                        <button type="button" class="btn btn-secondary"
                                            data-bs-dismiss="modal">Cerrar</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Modal Aceptar -->
                        <div class="modal fade" id="acceptModal{{ $solicitud->id_reserva }}" tabindex="-1"
                            aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header bg-success text-white">
                                        <h5 class="modal-title" id="exampleModalLabel">Confirmar aceptación</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        ¿Está seguro de que desea <strong>aceptar</strong> esta solicitud?
                                    </div>
                                    <div class="modal-footer">
                                        <form action="{{ route('solicitudes.aceptar', $solicitud->id_reserva) }}"
                                            method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="user_id"
                                                value="{{ $solicitud->usuarios->first()->id }}">
                                            <button type="button" class="btn btn-secondary"
                                                data-bs-dismiss="modal">Cerrar</button>
                                            <button type="submit" class="btn btn-success">Aceptar solicitud</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Modal Rechazar -->
                        <div class="modal fade" id="rejectModal{{ $solicitud->id_reserva }}" tabindex="-1"
                            aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header bg-danger text-white">
                                        <h1 class="modal-title fs-5" id="exampleModalLabel">Confirmar rechazo</h1>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <form method="POST"
                                        action="{{ route('solicitudes.rechazar', $solicitud->id_reserva) }}">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="usuario_id"
                                            value="{{ $solicitud->usuarios->first()->id }}">
                                        <div class="modal-body">
                                            <p>¿Estás seguro de que deseas <strong>rechazar</strong> esta solicitud?</p>
                                            <div class="mb-3">
                                                <label for="motivo_rechazo"><strong>Motivo del rechazo:</strong></label>
                                                <textarea id="motivo_rechazo" name="motivo_rechazo" class="form-control" maxlength="100" required></textarea>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary"
                                                data-bs-dismiss="modal">Cerrar</button>
                                            <button type="submit" class="btn btn-danger">Rechazar solicitud</button>
                                    </form>
                                    
                                </div>
                               
                            </div>
                            <?php $index++; ?>
                        </div>
        </div>
        @empty
            <tr>
                <td colspan="9" class="text-center">No hay solicitudes</td>
            </tr>
            @endforelse
            </tbody>
            </table>
        </div>
        </div>

        <!-- Paginación -->
        <nav aria-label="Page navigation example">
            <ul class="pagination justify-content-center mt-4">
                <!-- Enlace "Anterior" -->
                @if ($solicitudes->previousPageUrl())
                    <li class="page-item">
                        <a class="page-link"
                            href="{{ $solicitudes->previousPageUrl() . '&' . http_build_query(request()->except('page')) }}"
                            tabindex="-1" aria-disabled="true">Anterior</a>
                    </li>
                @else
                    <li class="page-item disabled">
                        <span class="page-link">Anterior</span>
                    </li>
                @endif

                <!-- Enlaces de páginas -->
                @foreach ($solicitudes->getUrlRange(1, $solicitudes->lastPage()) as $pagina => $url)
                    <li class="page-item {{ $solicitudes->currentPage() == $pagina ? 'active' : '' }}">
                        <a class="page-link"
                            href="{{ $url . '&' . http_build_query(request()->except('page')) }}">{{ $pagina }}</a>
                    </li>
                @endforeach

                <!-- Enlace "Siguiente" -->
                @if ($solicitudes->nextPageUrl())
                    <li class="page-item">
                        <a class="page-link"
                            href="{{ $solicitudes->nextPageUrl() . '&' . http_build_query(request()->except('page')) }}">Siguiente</a>
                    </li>
                @else
                    <li class="page-item disabled">
                        <span class="page-link">Siguiente</span>
                    </li>
                @endif
            </ul>
        </nav>
    @endsection

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
