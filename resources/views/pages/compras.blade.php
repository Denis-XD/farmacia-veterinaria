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
                                <div class="d-flex justify-content-start align-items-center">
                                    <!-- Botón Detalles -->
                                    <button class="btn btn-info text-white me-2 text-nowrap" data-bs-toggle="modal"
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

                                    <!-- Botón Descargar PDF -->
                                    <a href="{{ route('compras.descargar', $compra->id_compra) }}"
                                        class="btn btn-primary btn-sm text-nowrap">
                                        Descargar PDF
                                    </a>
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
            @foreach ($compras->getUrlRange(1, $compras->lastPage()) as $pagina => $url)
                <li class="page-item {{ $compras->currentPage() == $pagina ? 'active' : '' }}">
                    <a class="page-link"
                        href="{{ $url . '&' . http_build_query(request()->except('page')) }}">{{ $pagina }}</a>
                </li>
            @endforeach

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
