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
                <li class="breadcrumb-item"><a href="{{ route('compras.index') }}">Compras</a></li>
                <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
            </ol>
        </nav>
        <h2 class="text-center mt-3">Dashboard de Compras</h2>
    </div>
    <div class="container mt-4">
        <!-- Filtros -->
        <form method="GET" action="{{ route('compras.dashboard') }}" class="row g-3 mb-4">
            <div class="col-md-6">
                <label for="fecha_inicio" class="form-label">Fecha Inicio</label>
                <input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio"
                    value="{{ $fechaInicio }}">
            </div>
            <div class="col-md-6">
                <label for="fecha_fin" class="form-label">Fecha Fin</label>
                <input type="date" class="form-control" id="fecha_fin" name="fecha_fin" value="{{ $fechaFin }}">
            </div>
            <div class="col-md-12 text-end mt-3">
                <button type="submit" class="btn btn-primary">Aplicar Filtros</button>
            </div>
        </form>

        <!-- Gráficos -->
        <div class="row gy-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">Compras por Fecha</div>
                    <div class="card-body">
                        <canvas id="graficoComprasPorFecha"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">Productos Más Comprados</div>
                    <div class="card-body">
                        <canvas id="graficoProductosMasComprados"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const comprasLabels = @json($comprasLabels);
        const comprasValues = @json($comprasValues);

        const productosLabels = @json($productosLabels);
        const productosValues = @json($productosValues);

        // Gráfico de Compras por Fecha (Gráfico de área)
        const ctxCompras = document.getElementById('graficoComprasPorFecha').getContext('2d');
        new Chart(ctxCompras, {
            type: 'line',
            data: {
                labels: comprasLabels,
                datasets: [{
                    label: 'Compras por Fecha',
                    data: comprasValues,
                    backgroundColor: 'rgba(75, 192, 192, 0.5)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 2,
                    fill: true // Activar el área debajo de la línea
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: {
                        beginAtZero: true
                    },
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Gráfico de Productos Más Comprados (Barras)
        const ctxProductos = document.getElementById('graficoProductosMasComprados').getContext('2d');
        new Chart(ctxProductos, {
            type: 'bar',
            data: {
                labels: productosLabels,
                datasets: [{
                    label: 'Productos Más Comprados',
                    data: productosValues,
                    backgroundColor: 'rgba(153, 102, 255, 0.5)',
                    borderColor: 'rgba(153, 102, 255, 1)',
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: {
                        beginAtZero: true
                    },
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
@endsection
