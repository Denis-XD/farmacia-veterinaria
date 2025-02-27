@extends('layout')

@section('content')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a disabled><svg xmlns="http://www.w3.org/2000/svg" width="18" height="18"
                        viewBox="0 0 24 24" fill="currentColor" class="icon icon-tabler icons-tabler-filled icon-tabler-home">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path
                            d="M12.707 2.293l9 9c.63 .63 .184 1.707 -.707 1.707h-1v6a3 3 0 0 1 -3 3h-1v-7a3 3 0 0 0 -2.824 -2.995l-.176 -.005h-2a3 3 0 0 0 -3 3v7h-1a3 3 0 0 1 -3 -3v-6h-1c-.89 0 -1.337 -1.077 -.707 -1.707l9 -9a1 1 0 0 1 1.414 0m.293 11.707a1 1 0 0 1 1 1v7h-4v-7a1 1 0 0 1 .883 -.993l.117 -.007z" />
                    </svg></a></li>
            <li class="breadcrumb-item"><a href="{{ route('reservas') }}">Inicio</a></li>
            <li class="breadcrumb-item"><a href="{{ route('ventas.index') }}">Ventas</a></li>
            <li class="breadcrumb-item active" aria-current="page">Reporte de utilidad</li>
        </ol>
    </nav>
    <h2 class="text-center">Reporte de Utilidad</h2>
    <div class="container mt-4">
        <div class="mb-3">
            <h5>Filtros Aplicados:</h5>
            @if (count(array_filter($filtros, fn($valor) => $valor !== 'all' && $valor !== null)))
                <ul>
                    @foreach ($filtros as $filtro => $valor)
                        @if ($filtro === 'credito' || $filtro === 'servicio' || $filtro === 'finalizada')
                            <li><strong>{{ ucfirst(str_replace('_', ' ', $filtro)) }}:</strong>
                                @if ($valor === 'all')
                                    Todas
                                @else
                                    {{ $valor == 1 ? 'Sí' : 'No' }}
                                @endif
                            </li>
                        @elseif ($filtro === 'orden')
                            <li><strong>Orden:</strong> {{ $valor === 'asc' ? 'Más antigua' : 'Más reciente' }}</li>
                        @elseif (!empty($valor) && $valor !== 'all')
                            <li><strong>{{ ucfirst(str_replace('_', ' ', $filtro)) }}:</strong> {{ $valor }}</li>
                        @endif
                    @endforeach
                </ul>
            @else
                <p>No se aplicaron filtros.</p>
            @endif
        </div>
        <!-- Totales -->
        <div class="row mb-3">
            <!-- Columna 1 -->
            <div class="col-md-6 mb-3">
                <h5>Total de Ventas: <span class="text-success">Bs
                        {{ number_format($totalesGlobales['totalVentas'], 2) }}</span></h5>
                <h5>Total Costo de Ventas: <span class="text-danger">Bs
                        {{ number_format($totalesGlobales['totalCosto'], 2) }}</span></h5>
                <h5>Total de Utilidad Bruta: <span class="text-primary">Bs
                        {{ number_format($totalesGlobales['totalUtilidad'], 2) }}</span></h5>
            </div>

            <!-- Columna 2 -->
            <div class="col-md-6 mb-3">
                <h5>Total en Crédito: <span class="text-warning">Bs
                        {{ number_format($totalesGlobales['totalCredito'], 2) }}</span></h5>
                <h5>Total en Efectivo: <span class="text-info">Bs
                        {{ number_format($totalesGlobales['totalEfectivo'], 2) }}</span></h5>
            </div>
        </div>
        <div class="d-flex justify-content-end mb-3">
            <a href="{{ route('ventas.descargarReportePdf', request()->all()) }}" class="btn btn-danger">
                Descargar PDF
            </a>
        </div>
        <!-- Tabla -->
        <h5 class="text-center mb-3">Utilidad Bruta por Producto</h5>
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Cantidad</th>
                        <th>Unidad</th>
                        <th>Descripción</th>
                        <th>Efectivo</th>
                        <th>Crédito</th>
                        <th>Total Ventas</th>
                        <th>Costo de Ventas</th>
                        <th>Utilidad Bruta</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($ventas as $venta)
                        @foreach ($venta->detalles as $detalle)
                            <tr>
                                <td>{{ $detalle->producto->id_producto }}</td>
                                <td>{{ $detalle->cantidad_venta }}</td>
                                <td>{{ $detalle->producto->unidad }}</td>
                                <td>{{ $detalle->producto->nombre_producto }}</td>
                                <td>Bs {{ number_format($detalle->efectivo, 2) }}</td>
                                <td>Bs {{ number_format($detalle->credito, 2) }}</td>
                                <td>Bs {{ number_format($detalle->subtotal_venta, 2) }}</td>
                                <td>Bs {{ number_format($detalle->subtotal_costo, 2) }}</td>
                                <td>Bs {{ number_format($detalle->subtotal_utilidad, 2) }}</td>
                            </tr>
                        @endforeach
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
