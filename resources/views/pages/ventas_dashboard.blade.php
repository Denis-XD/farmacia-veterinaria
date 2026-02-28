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
                <li class="breadcrumb-item"><a href="{{ route('ventas.index') }}">Ventas</a></li>
                <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
            </ol>
        </nav>
        <h2 class="text-center mt-3">Dashboard de Ventas</h2>

        {{-- ✅ Mensaje de error si fechas inválidas --}}
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
    </div>

    <div class="container mt-4">
        <!-- Filtros -->
        <form method="GET" action="{{ route('ventas.dashboard') }}" class="row g-3 mb-4">
            <div class="col-md-5">
                <label for="fecha_inicio" class="form-label">Fecha Inicio</label>
                <input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio" value="{{ $fechaInicio }}"
                    required>
            </div>
            <div class="col-md-5">
                <label for="fecha_fin" class="form-label">Fecha Fin</label>
                <input type="date" class="form-control" id="fecha_fin" name="fecha_fin" value="{{ $fechaFin }}"
                    required>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">Aplicar</button>
            </div>
        </form>

        <!-- Gráficos -->
        <div class="row gy-4">
            <!-- Ventas por Fecha -->
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">Ventas por Fecha</div>
                    <div class="card-body">
                        {{-- ✅ Altura fija para que el gráfico no colapse --}}
                        <div style="position: relative; height: 300px;">
                            <canvas id="graficoVentasPorFecha"></canvas>
                        </div>
                        @if (count($ventasLabels) === 0)
                            <p class="text-center text-muted mt-3">No hay ventas en el rango seleccionado.</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Ventas vs Compras -->
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">Ingresos vs Egresos</div>
                    <div class="card-body">
                        <div style="position: relative; height: 300px;">
                            <canvas id="graficoVentasCompras"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Productos más vendidos -->
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">Top 5 Productos Más Vendidos</div>
                    <div class="card-body">
                        <div style="position: relative; height: 300px;">
                            <canvas id="graficoProductosMasVendidos"></canvas>
                        </div>
                        @if (count($productosLabels) === 0)
                            <p class="text-center text-muted mt-3">No hay productos vendidos en el rango seleccionado.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ventasLabels = @json($ventasLabels);
        const ventasValues = @json($ventasValues);
        const productosLabels = @json($productosLabels);
        const productosValues = @json($productosValues);
        const ingresos = @json($ventasTotales);
        const egresos = @json($comprasTotales);

        // ✅ Configuración común responsive
        const commonOptions = {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true,
                    position: 'top'
                }
            }
        };

        // Gráfico de Ventas por Fecha
        if (ventasLabels.length > 0) {
            const ctxVentas = document.getElementById('graficoVentasPorFecha').getContext('2d');
            new Chart(ctxVentas, {
                type: 'line',
                data: {
                    labels: ventasLabels,
                    datasets: [{
                        label: 'Ventas (Bs)',
                        data: ventasValues,
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.3
                    }]
                },
                options: {
                    ...commonOptions,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        }

        // Gráfico de Ingresos vs Egresos
        const ctxIngresosEgresos = document.getElementById('graficoVentasCompras').getContext('2d');
        new Chart(ctxIngresosEgresos, {
            type: 'bar',
            data: {
                labels: ['Ingresos (Ventas)', 'Egresos (Compras)'],
                datasets: [{
                    label: 'Monto (Bs)',
                    data: [ingresos, egresos],
                    backgroundColor: ['rgba(54, 162, 235, 0.5)', 'rgba(255, 99, 132, 0.5)'],
                    borderColor: ['rgba(54, 162, 235, 1)', 'rgba(255, 99, 132, 1)'],
                    borderWidth: 2
                }]
            },
            options: {
                ...commonOptions,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Gráfico de Productos Más Vendidos
        if (productosLabels.length > 0) {
            const ctxProductos = document.getElementById('graficoProductosMasVendidos').getContext('2d');
            new Chart(ctxProductos, {
                type: 'bar',
                data: {
                    labels: productosLabels,
                    datasets: [{
                        label: 'Cantidad Vendida',
                        data: productosValues,
                        backgroundColor: 'rgba(153, 102, 255, 0.5)',
                        borderColor: 'rgba(153, 102, 255, 1)',
                        borderWidth: 2
                    }]
                },
                options: {
                    ...commonOptions,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        }
    </script>
@endsection
