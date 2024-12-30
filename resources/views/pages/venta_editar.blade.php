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
            <li class="breadcrumb-item active" aria-current="page">Editar Venta</li>
        </ol>
    </nav>
    <h2 class="text-center">Editar Venta</h2>
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
    <div class="container mt-4">
        <form id="form-editar-venta" action="{{ route('ventas.update', $venta->id_venta) }}" method="POST">
            @csrf
            @method('PUT')

            <!-- Información de la Venta -->
            <div class="row mb-4">
                <!-- Información General -->
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Información de la Venta</h5>
                            <p><strong>ID Venta:</strong> {{ $venta->id_venta }}</p>
                            <p><strong>Socio:</strong> {{ $venta->socio->nombre_socio ?? 'Sin Socio' }}</p>
                            <p><strong>Fecha:</strong> {{ $venta->fecha_venta }}</p>
                            <p><strong>Total Venta:</strong> Bs {{ number_format($venta->total_venta, 2) }}</p>
                            <p><strong>Estado:</strong> {{ $venta->finalizada ? 'Finalizada' : 'Pendiente' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Detalles de Productos -->
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Detalles de Productos</h5>
                            <ul>
                                @foreach ($venta->detalles as $detalle)
                                    <li>
                                        {{ $detalle->producto->nombre_producto }} - Cantidad:
                                        {{ $detalle->cantidad_venta }}
                                        - Subtotal: Bs {{ number_format($detalle->subtotal_venta, 2) }}
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Pagos -->
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Pagos</h5>
                            <ul>
                                @foreach ($venta->pagos as $pago)
                                    <li>
                                        Fecha: {{ \Carbon\Carbon::parse($pago->fecha_pago)->format('d/m/Y') }} |
                                        Monto: Bs {{ number_format($pago->monto_pagado, 2) }} |
                                        Saldo Pendiente: Bs {{ number_format($pago->saldo_pendiente, 2) }}
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Edición y Nuevo Pago -->
            <div class="row mb-4">
                <!-- Editar Información -->
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Editar Información</h5>
                            <div class="mb-3">
                                <label for="credito" class="form-label">¿Venta a Crédito?</label>
                                <select class="form-select" id="credito" name="credito">
                                    <option value="1" {{ $venta->credito ? 'selected' : '' }}>Sí</option>
                                    <option value="0" {{ !$venta->credito ? 'selected' : '' }}>No</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="servicio" class="form-label">¿Incluye Servicio?</label>
                                <select class="form-select" id="servicio" name="servicio">
                                    <option value="1" {{ $venta->servicio ? 'selected' : '' }}>Sí</option>
                                    <option value="0" {{ !$venta->servicio ? 'selected' : '' }}>No</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="finalizada" class="form-label">¿Venta Finalizada?</label>
                                <select class="form-select" id="finalizada" name="finalizada">
                                    <option value="1" {{ $venta->finalizada ? 'selected' : '' }}>Sí</option>
                                    <option value="0" {{ !$venta->finalizada ? 'selected' : '' }}>No</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Añadir Nuevo Pago -->
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Añadir Nuevo Pago</h5>
                            <div class="mb-3">
                                <label for="nuevo_pago" class="form-label">Monto del Pago</label>
                                <input type="number" class="form-control" id="nuevo_pago" name="nuevo_pago" min="0"
                                    step="0.01">
                            </div>
                            <div class="mb-3">
                                <label for="saldo_pendiente" class="form-label">Saldo Pendiente</label>
                                <input type="number" class="form-control" id="saldo_pendiente" name="saldo_pendiente"
                                    min="0" step="0.01" readonly
                                    value="{{ $venta->total_venta - $venta->pagos->sum('monto_pagado') }}">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Servicio Veterinario -->
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title">Incluir Servicio Veterinario</h5>
                    <div class="mb-3">
                        <label for="tratamiento" class="form-label">Tratamiento</label>
                        <input type="text" class="form-control" id="tratamiento" name="tratamiento"
                            value="{{ optional($venta->servicioVeterinario)->tratamiento }}">
                    </div>
                    <div class="mb-3">
                        <label for="fecha_servicio" class="form-label">Fecha del Servicio</label>
                        <input type="datetime-local" class="form-control" id="fecha_servicio" name="fecha_servicio"
                            value="{{ optional($venta->servicioVeterinario)->fecha_servicio }}">
                    </div>
                    <div class="mb-3">
                        <label for="costo_servicio" class="form-label">Costo del Servicio</label>
                        <input type="number" class="form-control" id="costo_servicio" name="costo_servicio"
                            min="0" step="0.01"
                            value="{{ optional($venta->servicioVeterinario)->costo_servicio }}">
                    </div>
                    <div class="mb-3">
                        <label for="costo_combustible" class="form-label">Costo del Combustible</label>
                        <input type="number" class="form-control" id="costo_combustible" name="costo_combustible"
                            min="0" step="0.01"
                            value="{{ optional($venta->servicioVeterinario)->costo_combustible }}">
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-end mt-4">
                <button type="button" id="btn-confirmar" class="btn btn-primary me-2">Guardar Cambios</button>
                <button type="button" id="btn-cancelar" class="btn btn-secondary">Cancelar</button>
            </div>
        </form>
    </div>

    <!-- Modal de confirmación -->
    <div class="modal fade" id="modalConfirmacion" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalLabel">Confirmar Cambios</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    ¿Estás seguro de que deseas guardar los cambios de la venta con ID:
                    <strong>{{ $venta->id_venta }}</strong>?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" id="btn-confirmar-guardar" class="btn btn-success">Confirmar</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        const totalVenta = {{ $venta->total_venta }};
        const pagosAnteriores = {{ $venta->pagos->sum('monto_pagado') }};
        const nuevoPagoInput = document.getElementById('nuevo_pago');
        const saldoPendienteInput = document.getElementById('saldo_pendiente');

        nuevoPagoInput.addEventListener('input', () => {
            const nuevoPago = parseFloat(nuevoPagoInput.value) || 0;
            const saldoPendiente = totalVenta - (pagosAnteriores + nuevoPago);
            saldoPendienteInput.value = saldoPendiente.toFixed(2);
        });

        document.getElementById('btn-cancelar').addEventListener('click', () => {
            window.location.reload();
        });

        document.getElementById('btn-confirmar').addEventListener('click', () => {
            const modal = new bootstrap.Modal(document.getElementById('modalConfirmacion'));
            modal.show();
        });

        document.getElementById('btn-confirmar-guardar').addEventListener('click', () => {
            document.getElementById('form-editar-venta').submit();
        });
    </script>
@endsection
