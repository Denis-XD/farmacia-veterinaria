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
                <li class="breadcrumb-item"><a href="{{ route('productos.index') }}">Productos</a></li>
                <li class="breadcrumb-item active" aria-current="page">Inventario</li>
            </ol>
        </nav>
        <h2 class="text-center mt-3">Inventario de Productos</h2>
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert" id='cajaOkey'>
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
        <form method="GET" action="{{ route('productos.inventario') }}" class="row g-2 mb-3 align-items-end">
            <div class="col-12 col-md-auto">
                <label for="fecha_inicio" class="form-label">Fecha Inicio:</label>
                <input type="date" name="fecha_inicio" id="fecha_inicio" value="{{ $fechaInicio }}"
                    class="form-control">
            </div>
            <div class="col-12 col-md-auto">
                <label for="fecha_fin" class="form-label">Fecha Fin:</label>
                <input type="date" name="fecha_fin" id="fecha_fin" value="{{ $fechaFin }}" class="form-control">
            </div>
            <div class="col-12 col-md-auto">
                <button type="submit" class="btn btn-primary w-100">Buscar</button>
            </div>
        </form>

        <div class="d-flex flex-column flex-md-row align-items-md-center align-items-start mb-3">
            <h5 class="mb-2 mb-md-0">Total valor Global: Bs {{ number_format($totalValorGlobal, 2) }}</h5>
            <h5 class="mb-2 mb-md-0 ms-md-3">Total valor Actual: Bs {{ number_format($totalValor, 2) }}</h5>
            <a href="{{ route('productos.descargarInventarioPdf', ['fechaInicio' => $fechaInicio, 'fechaFin' => $fechaFin]) }}"
                class="btn btn-danger ms-auto">
                Descargar en PDF
            </a>
        </div>

        <div style="max-height: 500px; overflow-y: auto;">
            <table class="table table-bordered mt-3">
                <thead>
                    <tr>
                        <th>Nº</th>
                        <th>Descripción</th>
                        <th>Unidad</th>
                        <th>Cantidad</th>
                        <th>C/U</th>
                        <th>Valor</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($inventario as $index => $item)
                        <tr>
                            <td>{{ ($productos->currentPage() - 1) * $productos->perPage() + $loop->iteration }}</td>
                            <td>{{ $item['descripcion'] }}</td>
                            <td>{{ $item['unidad'] }}</td>
                            <td>{{ $item['cantidad'] }}</td>
                            <td>Bs {{ number_format($item['precio_unitario'], 2) }}</td>
                            <td>Bs {{ number_format($item['valor'], 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">No se encontraron datos para esta fecha.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <nav aria-label="Page navigation example">
        <ul class="pagination justify-content-center mt-4">
            <!-- Enlace "Anterior" -->
            @if ($productos->previousPageUrl())
                <li class="page-item">
                    <a class="page-link"
                        href="{{ $productos->previousPageUrl() . '&' . http_build_query(request()->except('page')) }}"
                        tabindex="-1" aria-disabled="true">Anterior</a>
                </li>
            @else
                <li class="page-item disabled">
                    <span class="page-link">Anterior</span>
                </li>
            @endif

            <!-- Enlaces de páginas -->
            @php
                $totalPages = $productos->lastPage();
                $currentPage = $productos->currentPage();
                $range = 1; // Número de páginas visibles a izquierda y derecha de la actual
                $start = max(1, $currentPage - $range);
                $end = min($totalPages, $currentPage + $range);
            @endphp

            @if ($start > 1)
                <li class="page-item">
                    <a class="page-link"
                        href="{{ $productos->url(1) . '&' . http_build_query(request()->except('page')) }}">1</a>
                </li>
                @if ($start > 2)
                    <li class="page-item disabled d-none d-md-block">
                        <span class="page-link">...</span>
                    </li>
                @endif
            @endif

            @for ($i = $start; $i <= $end; $i++)
                <li class="page-item {{ $productos->currentPage() == $i ? 'active' : '' }}">
                    <a class="page-link"
                        href="{{ $productos->url($i) . '&' . http_build_query(request()->except('page')) }}">{{ $i }}</a>
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
                        href="{{ $productos->url($totalPages) . '&' . http_build_query(request()->except('page')) }}">{{ $totalPages }}</a>
                </li>
            @endif

            <!-- Enlace "Siguiente" -->
            @if ($productos->nextPageUrl())
                <li class="page-item">
                    <a class="page-link"
                        href="{{ $productos->nextPageUrl() . '&' . http_build_query(request()->except('page')) }}">Siguiente</a>
                </li>
            @else
                <li class="page-item disabled">
                    <span class="page-link">Siguiente</span>
                </li>
            @endif
        </ul>
    </nav>
@endsection
