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
                <li class="breadcrumb-item active" aria-current="page">Ventas</li>
                <li class="breadcrumb-item active" aria-current="page">Ventas</li>
            </ol>
        </nav>
        <h2 class="text-center mt-3">Gestión de Ventas</h2>
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
    </div>
    <div class="container mt-4">
        <!-- Filtros -->
        <form id="filtrosForm" class="row g-3 mb-4" method="GET" action="{{ route('ventas.index') }}">
            <div class="col-md-3">
                <label for="fecha" class="form-label">Fecha Específica</label>
                <input type="date" class="form-control" id="fecha" name="fecha" value="{{ request('fecha') }}">
            </div>
            <div class="col-md-3">
                <label for="fecha_desde" class="form-label">Fecha Desde</label>
                <input type="date" class="form-control" id="fecha_desde" name="fecha_desde"
                    value="{{ request('fecha_desde') }}">
            </div>
            <div class="col-md-3">
                <label for="fecha_hasta" class="form-label">Fecha Hasta</label>
                <input type="date" class="form-control" id="fecha_hasta" name="fecha_hasta"
                    value="{{ request('fecha_hasta') }}">
            </div>
            <div class="col-md-3">
                <label for="socio" class="form-label">Socio</label>
                <input type="text" class="form-control" id="socio" name="socio" placeholder="Nombre del socio"
                    value="{{ request('socio') }}">
            </div>
            <div class="col-md-3">
                <label for="credito" class="form-label">Crédito</label>
                <select class="form-select" id="credito" name="credito">
                    <option value="all" {{ request('credito') == 'all' ? 'selected' : '' }}>Todas</option>
                    <option value="1" {{ request('credito') == '1' ? 'selected' : '' }}>Sí</option>
                    <option value="0" {{ request('credito') == '0' ? 'selected' : '' }}>No</option>
                </select>
            </div>
            <div class="col-md-3">
                <label for="servicio" class="form-label">Servicio</label>
                <select class="form-select" id="servicio" name="servicio">
                    <option value="all" {{ request('servicio') == 'all' ? 'selected' : '' }}>Todas</option>
                    <option value="1" {{ request('servicio') == '1' ? 'selected' : '' }}>Sí</option>
                    <option value="0" {{ request('servicio') == '0' ? 'selected' : '' }}>No</option>
                </select>
            </div>
            <div class="col-md-3">
                <label for="finalizada" class="form-label">Finalizada</label>
                <select class="form-select" id="finalizada" name="finalizada">
                    <option value="all" {{ request('finalizada') == 'all' ? 'selected' : '' }}>Todas</option>
                    <option value="1" {{ request('finalizada') == '1' ? 'selected' : '' }}>Sí</option>
                    <option value="0" {{ request('finalizada') == '0' ? 'selected' : '' }}>No</option>
                </select>
            </div>
            <div class="col-md-3">
                <label for="orden" class="form-label">Orden</label>
                <select class="form-select" id="orden" name="orden">
                    <option value="desc" {{ request('orden') == 'desc' ? 'selected' : '' }}>Más reciente</option>
                    <option value="asc" {{ request('orden') == 'asc' ? 'selected' : '' }}>Más antigua</option>
                </select>
            </div>
            <div class="col-md-3 d-flex align-items-end mt-2 gap-1">
                <button type="submit" class="btn btn-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"
                        stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-filter">
                        <path stroke="none" d="M0 0h24Vh24H0z" fill="none" />
                        <path
                            d="M4 4h16v2.172a2 2 0 0 1 -.586 1.414l-4.414 4.414v7l-6 2v-8.5l-4.48 -4.928a2 2 0 0 1 -.52 -1.345v-2.227z" />
                    </svg> Filtrar
                </button>
                <a href="{{ route('ventas.index') }}" class="btn btn-secondary">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"
                        stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-filter-off">
                        <path stroke="none" d="M0 0h24Vh24H0z" fill="none" />
                        <path
                            d="M8 4h12v2.172a2 2 0 0 1 -.586 1.414l-3.914 3.914m-.5 3.5v4l-6 2v-8.5l-4.48 -4.928a2 2 0 0 1 -.52 -1.345v-2.227" />
                        <path d="M3 3l18 18" />
                    </svg>Limpiar
                </a>
            </div>
        </form>

        @can('venta_reporte_utilidad')
            <div class="d-flex justify-content-end mb-3">
                <form method="GET" action="{{ route('ventas.reporteUtilidad') }}">
                    @foreach (request()->all() as $key => $value)
                        @if (!is_null($value))
                            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                        @endif
                    @endforeach
                    <button type="submit" class="btn btn-info text-white">Reporte de Utilidad</button>
                </form>
            </div>
        @endcan

        <!-- Resumen -->
        <div class="d-flex justify-content-between mb-4">
            <h5>Total Venta: <span id="totalVenta">Bs {{ number_format($totalVenta, 2) }}</span></h5>
            <h5>Cantidad de Productos: <span id="cantidadProductos">{{ $cantidadProductos }}</span></h5>
        </div>

        <!-- Tabla -->
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th scope="col" class="sticky-id bg-light align-middle">ID</th>
                        <th scope="col" class="bg-light align-middle">Socio</th>
                        <th scope="col" class="bg-light">Fecha</th>
                        <th scope="col" class="bg-light">Cant. Productos</th>
                        <th scope="col" class="bg-light">Total</th>
                        <th scope="col" class="bg-light col-sm-2">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($ventas as $venta)
                        <tr>
                            <td class="sticky-id bg-light">{{ $venta->id_venta }}</td>
                            <td>{{ $venta->socio->nombre_socio ?? 'Sin Socio' }}</td>
                            <td>{{ $venta->fecha_venta }}</td>
                            <td>{{ $venta->detalles->sum('cantidad_venta') }}</td>
                            <td>Bs {{ number_format($venta->total_venta, 2) }}</td>
                            <td>
                                <div class="d-flex justify-content-start align-items-center col">
                                    <!-- Botón Detalles -->
                                    <button class="btn btn-info text-white" data-bs-toggle="modal"
                                        data-bs-target="#detallesModal{{ $venta->id_venta }}">Detalles</button>

                                    <!-- Modal Detalles -->
                                    <div class="modal fade" id="detallesModal{{ $venta->id_venta }}" tabindex="-1"
                                        aria-labelledby="detallesModalLabel{{ $venta->id_venta }}" aria-hidden="true">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content">
                                                <div class="modal-header bg-info text-white">
                                                    <h5 class="modal-title"
                                                        id="detallesModalLabel{{ $venta->id_venta }}">
                                                        Detalles de Venta
                                                    </h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                        aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <h6>Socio: {{ $venta->socio->nombre_socio ?? 'Sin Socio' }}</h6>
                                                    <h6>Fecha: {{ $venta->fecha_venta }}</h6>
                                                    <h6>Total: Bs {{ number_format($venta->total_venta, 2) }}</h6>
                                                    <h6>Descuento: {{ $venta->descuento_venta }}%</h6>
                                                    <h6>Crédito: {{ $venta->credito ? 'Sí' : 'No' }}</h6>
                                                    <h6>Servicio: {{ $venta->servicio ? 'Sí' : 'No' }}</h6>
                                                    <h6>Finalizada: {{ $venta->finalizada ? 'Sí' : 'No' }}</h6>
                                                    <hr>
                                                    <h6>Detalles de Venta:</h6>
                                                    <ul>
                                                        @foreach ($venta->detalles as $detalle)
                                                            <li>
                                                                Producto: {{ $detalle->producto->nombre_producto }} -
                                                                Cantidad: {{ $detalle->cantidad_venta }} -
                                                                Subtotal: Bs
                                                                {{ number_format($detalle->subtotal_venta, 2) }}
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                    <hr>
                                                    <h6>Pagos:</h6>
                                                    <ul>
                                                        @foreach ($venta->pagos as $pago)
                                                            <li>
                                                                Fecha: {{ $pago->fecha_pago }} -
                                                                Monto: Bs {{ number_format($pago->monto_pagado, 2) }} -
                                                                Saldo pendiente: Bs
                                                                {{ number_format($pago->saldo_pendiente, 2) }}
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary"
                                                        data-bs-dismiss="modal">Aceptar</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @can('venta_actualizar')
                                        <a href="{{ route('ventas.edit', $venta->id_venta) }}"
                                            class="btn btn-warning ms-2 me-2">Editar</a>
                                    @endcan
                                    <a href="{{ route('ventas.descargar', $venta->id_venta) }}"
                                        class="btn btn-primary text-white me-2">PDF</a>
                                    @can('venta_eliminar')
                                        <button type="button" class="btn btn-danger me-2" data-bs-toggle="modal"
                                            data-bs-target="#deleteVentaModal{{ $venta->id_venta }}">
                                            Eliminar
                                        </button>
                                    @endcan
                                    @can('venta_eliminar')
                                        <div class="modal fade" id="deleteVentaModal{{ $venta->id_venta }}" tabindex="-1"
                                            aria-labelledby="deleteVentaModalLabel" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header text-white bg-danger">
                                                        <svg width="24px" height="24px" viewBox="0 0 24 24"
                                                            fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <path
                                                                d="M21.7605 15.92L15.3605 4.4C14.5005 2.85 13.3105 2 12.0005 2C10.6905 2 9.50047 2.85 8.64047 4.4L2.24047 15.92C1.43047 17.39 1.34047 18.8 1.99047 19.91C2.64047 21.02 3.92047 21.63 5.60047 21.63H18.4005C20.0805 21.63 21.3605 21.02 22.0105 19.91C22.6605 18.8 22.5705 17.38 21.7605 15.92ZM11.2505 9C11.2505 8.59 11.5905 8.25 12.0005 8.25C12.4105 8.25 12.7505 8.59 12.7505 9V14C12.7505 14.41 12.4105 14.75 12.0005 14.75C11.5905 14.75 11.2505 14.41 11.2505 14V9ZM12.7105 17.71C12.6605 17.75 12.6105 17.79 12.5605 17.83C12.5005 17.87 12.4405 17.9 12.3805 17.92C12.3205 17.95 12.2605 17.97 12.1905 17.98C12.1305 17.99 12.0605 18 12.0005 18C11.9405 18 11.8705 17.99 11.8005 17.98C11.7405 17.97 11.6805 17.95 11.6205 17.92C11.5605 17.9 11.5005 17.87 11.4405 17.83C11.3905 17.79 11.3405 17.75 11.2905 17.71C11.1105 17.52 11.0005 17.26 11.0005 17C11.0005 16.74 11.1105 16.48 11.2905 16.29C11.3405 16.25 11.3905 16.21 11.4405 16.17C11.5005 16.13 11.5605 16.1 11.6205 16.08C11.6805 16.05 11.7405 16.03 11.8005 16.02C11.9305 15.99 12.0705 15.99 12.1905 16.02C12.2605 16.03 12.3205 16.05 12.3805 16.08C12.4405 16.1 12.5005 16.13 12.5605 16.17C12.6105 16.21 12.6605 16.25 12.7105 16.29C12.8905 16.48 13.0005 16.74 13.0005 17C13.0005 17.26 12.8905 17.52 12.7105 17.71Z"
                                                                fill="#FFFFFF" />
                                                        </svg>
                                                        <h1 class="modal-title fs-5" id="exampleModalLabel">Eliminar venta
                                                        </h1>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                            aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <p><strong>¡Cuidado!</strong> ¿Estás seguro de que deseas
                                                            <strong>eliminar</strong> la
                                                            venta
                                                            <strong>"ID: {{ $venta->id_venta }}"</strong>?
                                                            <br><br><strong>Nota:
                                                            </strong>Esta acción
                                                            no se puede revertir
                                                            y se
                                                            incrementará el stock de cada producto de acuerdo a la cantidad de
                                                            venta.
                                                        </p>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary"
                                                            data-bs-dismiss="modal">Cancelar</button>
                                                        <form action="{{ route('ventas.destroy', $venta->id_venta) }}"
                                                            method="POST">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-danger">Eliminar</button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <nav aria-label="Page navigation example">
        <ul class="pagination justify-content-center mt-4">
            <!-- Enlace "Anterior" -->
            @if ($ventas->previousPageUrl())
                <li class="page-item">
                    <a class="page-link"
                        href="{{ $ventas->previousPageUrl() . '&' . http_build_query(request()->except('page')) }}"
                        tabindex="-1" aria-disabled="true">Anterior</a>
                </li>
            @else
                <li class="page-item disabled">
                    <span class="page-link">Anterior</span>
                </li>
            @endif

            <!-- Enlaces de páginas -->
            @php
                $totalPages = $ventas->lastPage();
                $currentPage = $ventas->currentPage();
                $range = 1; // Número de páginas visibles a izquierda y derecha de la actual
                $start = max(1, $currentPage - $range);
                $end = min($totalPages, $currentPage + $range);
            @endphp

            @if ($start > 1)
                <li class="page-item">
                    <a class="page-link"
                        href="{{ $ventas->url(1) . '&' . http_build_query(request()->except('page')) }}">1</a>
                </li>
                @if ($start > 2)
                    <li class="page-item disabled d-none d-md-block">
                        <span class="page-link">...</span>
                    </li>
                @endif
            @endif

            @for ($i = $start; $i <= $end; $i++)
                <li class="page-item {{ $ventas->currentPage() == $i ? 'active' : '' }}">
                    <a class="page-link"
                        href="{{ $ventas->url($i) . '&' . http_build_query(request()->except('page')) }}">{{ $i }}</a>
                </li>
            @endfor

            @if ($end < $totalPages)
                @if ($end < $totalPages - 1)
                    <li class="page-item disabled d-none d-md-block">
                        <span class="page-link">...</span>
                    </li>
                @endif
                <li class="page-item">
                    <a class="page-link"
                        href="{{ $ventas->url($totalPages) . '&' . http_build_query(request()->except('page')) }}">{{ $totalPages }}</a>
                </li>
            @endif

            <!-- Enlace "Siguiente" -->
            @if ($ventas->nextPageUrl())
                <li class="page-item">
                    <a class="page-link"
                        href="{{ $ventas->nextPageUrl() . '&' . http_build_query(request()->except('page')) }}">Siguiente</a>
                </li>
            @else
                <li class="page-item disabled">
                    <span class="page-link">Siguiente</span>
                </li>
            @endif
        </ul>
    </nav>
@endsection
