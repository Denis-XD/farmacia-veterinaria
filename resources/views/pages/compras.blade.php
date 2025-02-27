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
                <li class="breadcrumb-item active" aria-current="page">Compras</li>
                <li class="breadcrumb-item active" aria-current="page">Compras</li>
            </ol>
        </nav>
        <h2 class="text-center mt-3">Gestión de Compras</h2>
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
        <form id="filtrosForm" class="row g-3 mb-4" method="GET" action="{{ route('compras.index') }}">
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
                <label for="proveedor" class="form-label">Proveedor</label>
                <input type="text" class="form-control" id="proveedor" name="proveedor"
                    placeholder="Nombre del proveedor" value="{{ request('proveedor') }}">
            </div>
            <div class="col-md-3">
                <label for="orden" class="form-label">Orden</label>
                <select class="form-select" id="orden" name="orden">
                    <option value="desc" {{ request('orden') == 'desc' ? 'selected' : '' }}>Más reciente</option>
                    <option value="asc" {{ request('orden') == 'asc' ? 'selected' : '' }}>Más antigua</option>
                </select>
            </div>
            <div class="col-md-3">
                <label for="tipo" class="form-label">Tipo</label>
                <select class="form-select" id="tipo" name="tipo">
                    <option value="all" {{ request('tipo') == 'all' ? 'selected' : '' }}>Todas</option>
                    <option value="1" {{ request('tipo') == '1' ? 'selected' : '' }}>Con factura</option>
                    <option value="0" {{ request('tipo') == '0' ? 'selected' : '' }}>Sin factura</option>
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
                <a href="{{ route('compras.index') }}" class="btn btn-secondary d-flex align-items-center gap-1"><svg
                        xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"
                        stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-filter-off">
                        <path stroke="none" d="M0 0h24Vh24H0z" fill="none" />
                        <path
                            d="M8 4h12v2.172a2 2 0 0 1 -.586 1.414l-3.914 3.914m-.5 3.5v4l-6 2v-8.5l-4.48 -4.928a2 2 0 0 1 -.52 -1.345v-2.227" />
                        <path d="M3 3l18 18" />
                    </svg>Limpiar</a>
            </div>
        </form>
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5>Total Compra: <span id="totalCompra">Bs {{ number_format($totalCompra, 2) }}</span></h5>
            <h5>Cantidad de Productos: <span id="cantidadProductos">{{ $cantidadProductos }}</span></h5>
        </div>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th scope="col" class="sticky-id bg-light align-middle">ID</th>
                        <th scope="col" class="bg-light align-middle">Proveedor</th>
                        <th scope="col" class="bg-light">Total</th>
                        <th scope="col" class="bg-light">Descuento</th>
                        <th scope="col" class="bg-light">Fecha</th>
                        <th scope="col" class="bg-light col-sm-2">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($compras as $compra)
                        <tr>
                            <td class="sticky-id bg-light">{{ $compra->id_compra }}</td>
                            <td>{{ $compra->proveedor->nombre_proveedor }}</td>
                            <td>Bs {{ number_format($compra->total_compra, 2) }}</td>
                            <td>{{ $compra->descuento_compra }}%</td>
                            <td>{{ $compra->fecha_compra }}</td>
                            <td>
                                <div class="d-flex justify-content-start align-items-center col">
                                    <!-- Botón Detalles -->
                                    <button class="btn btn-info text-white" data-bs-toggle="modal"
                                        data-bs-target="#detallesModal{{ $compra->id_compra }}">Detalles</button>

                                    <!-- Modal Detalles -->
                                    <div class="modal fade" id="detallesModal{{ $compra->id_compra }}" tabindex="-1"
                                        aria-labelledby="detallesModalLabel{{ $compra->id_compra }}" aria-hidden="true">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content">
                                                <div class="modal-header bg-info text-white">
                                                    <h5 class="modal-title"
                                                        id="detallesModalLabel{{ $compra->id_compra }}">Detalles de Compra
                                                    </h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                        aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <h6>Proveedor: {{ $compra->proveedor->nombre_proveedor }}</h6>
                                                    <h6>Total: Bs {{ number_format($compra->total_compra, 2) }}</h6>
                                                    <h6>Descuento: {{ $compra->descuento_compra }}%</h6>
                                                    <h6>Factura: {{ $compra->factura_compra ? 'Sí' : 'No' }}</h6>
                                                    <h6>Fecha: {{ $compra->fecha_compra }}</h6>
                                                    <hr>
                                                    <h6>Detalles:</h6>
                                                    <ul>
                                                        @foreach ($compra->detalles as $detalle)
                                                            <li>{{ $detalle->producto->nombre_producto }} - Cantidad:
                                                                {{ $detalle->cantidad_compra }} - Subtotal: Bs
                                                                {{ number_format($detalle->subtotal_compra, 2) }}</li>
                                                        @endforeach
                                                    </ul>
                                                    <h6>Total suma: Bs
                                                        {{ number_format($compra->detalles->sum('subtotal_compra'), 2) }}
                                                    </h6>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary"
                                                        data-bs-dismiss="modal">Aceptar</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    @can('compra_actualizar')
                                        <a href="{{ route('compras.edit', $compra->id_compra) }}"
                                            class="btn btn-warning ms-2 me-2">Editar</a>
                                    @endcan
                                    <a href="{{ route('compras.descargar', $compra->id_compra) }}"
                                        class="btn btn-primary text-white me-2">
                                        PDF
                                    </a>
                                    @can('compra_eliminar')
                                        <button type="button" class="btn btn-danger me-2" data-bs-toggle="modal"
                                            data-bs-target="#deleteCompraModal{{ $compra->id_compra }}">
                                            Eliminar
                                        </button>
                                    @endcan
                                    @can('compra_eliminar')
                                        <div class="modal fade" id="deleteCompraModal{{ $compra->id_compra }}"
                                            tabindex="-1" aria-labelledby="deleteCompraModalLabel" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header text-white bg-danger">
                                                        <svg width="24px" height="24px" viewBox="0 0 24 24"
                                                            fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <path
                                                                d="M21.7605 15.92L15.3605 4.4C14.5005 2.85 13.3105 2 12.0005 2C10.6905 2 9.50047 2.85 8.64047 4.4L2.24047 15.92C1.43047 17.39 1.34047 18.8 1.99047 19.91C2.64047 21.02 3.92047 21.63 5.60047 21.63H18.4005C20.0805 21.63 21.3605 21.02 22.0105 19.91C22.6605 18.8 22.5705 17.38 21.7605 15.92ZM11.2505 9C11.2505 8.59 11.5905 8.25 12.0005 8.25C12.4105 8.25 12.7505 8.59 12.7505 9V14C12.7505 14.41 12.4105 14.75 12.0005 14.75C11.5905 14.75 11.2505 14.41 11.2505 14V9ZM12.7105 17.71C12.6605 17.75 12.6105 17.79 12.5605 17.83C12.5005 17.87 12.4405 17.9 12.3805 17.92C12.3205 17.95 12.2605 17.97 12.1905 17.98C12.1305 17.99 12.0605 18 12.0005 18C11.9405 18 11.8705 17.99 11.8005 17.98C11.7405 17.97 11.6805 17.95 11.6205 17.92C11.5605 17.9 11.5005 17.87 11.4405 17.83C11.3905 17.79 11.3405 17.75 11.2905 17.71C11.1105 17.52 11.0005 17.26 11.0005 17C11.0005 16.74 11.1105 16.48 11.2905 16.29C11.3405 16.25 11.3905 16.21 11.4405 16.17C11.5005 16.13 11.5605 16.1 11.6205 16.08C11.6805 16.05 11.7405 16.03 11.8005 16.02C11.9305 15.99 12.0705 15.99 12.1905 16.02C12.2605 16.03 12.3205 16.05 12.3805 16.08C12.4405 16.1 12.5005 16.13 12.5605 16.17C12.6105 16.21 12.6605 16.25 12.7105 16.29C12.8905 16.48 13.0005 16.74 13.0005 17C13.0005 17.26 12.8905 17.52 12.7105 17.71Z"
                                                                fill="#FFFFFF" />
                                                        </svg>
                                                        <h1 class="modal-title fs-5" id="exampleModalLabel">Eliminar compra
                                                        </h1>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                            aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <p><strong>¡Cuidado!</strong> ¿Estás seguro de que deseas
                                                            <strong>eliminar</strong> la
                                                            compra
                                                            <strong>"ID: {{ $compra->id_compra }}"</strong>?
                                                            <br><br><strong>Nota:
                                                            </strong>Esta acción
                                                            no se puede revertir
                                                            y se
                                                            reducira el stock de cada producto de acuerdo a la cantidad de
                                                            compra.
                                                        </p>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary"
                                                            data-bs-dismiss="modal">Cancelar</button>
                                                        <form action="{{ route('compras.destroy', $compra->id_compra) }}"
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
            @if ($compras->previousPageUrl())
                <li class="page-item">
                    <a class="page-link"
                        href="{{ $compras->previousPageUrl() . '&' . http_build_query(request()->except('page')) }}"
                        tabindex="-1" aria-disabled="true">Anterior</a>
                </li>
            @else
                <li class="page-item disabled">
                    <span class="page-link">Anterior</span>
                </li>
            @endif

            <!-- Enlaces de páginas -->
            @php
                $totalPages = $compras->lastPage();
                $currentPage = $compras->currentPage();
                $range = 1; // Número de páginas visibles a izquierda y derecha de la actual
                $start = max(1, $currentPage - $range);
                $end = min($totalPages, $currentPage + $range);
            @endphp

            @if ($start > 1)
                <li class="page-item">
                    <a class="page-link"
                        href="{{ $compras->url(1) . '&' . http_build_query(request()->except('page')) }}">1</a>
                </li>
                @if ($start > 2)
                    <li class="page-item disabled d-none d-md-block">
                        <span class="page-link">...</span>
                    </li>
                @endif
            @endif

            @for ($i = $start; $i <= $end; $i++)
                <li class="page-item {{ $compras->currentPage() == $i ? 'active' : '' }}">
                    <a class="page-link"
                        href="{{ $compras->url($i) . '&' . http_build_query(request()->except('page')) }}">{{ $i }}</a>
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
                        href="{{ $compras->url($totalPages) . '&' . http_build_query(request()->except('page')) }}">{{ $totalPages }}</a>
                </li>
            @endif

            <!-- Enlace "Siguiente" -->
            @if ($compras->nextPageUrl())
                <li class="page-item">
                    <a class="page-link"
                        href="{{ $compras->nextPageUrl() . '&' . http_build_query(request()->except('page')) }}">Siguiente</a>
                </li>
            @else
                <li class="page-item disabled">
                    <span class="page-link">Siguiente</span>
                </li>
            @endif
        </ul>
    </nav>
@endsection
