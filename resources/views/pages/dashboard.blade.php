@extends('layout')

@section('content')
    <!-- Contenedor principal con fondo blanco -->
    <div class="container mt-4 bg-white p-5 rounded shadow">
        <!-- Encabezado con logo y título centrado -->
        <div class="text-center mb-4">
            <img src="{{ asset('assets/logo.jpeg') }}" alt="Logo" style="max-height: 150px;" class="mb-3">
            <h2 class="mb-0 text-success">Farmacia Veterinaria ALVA</h2>
        </div>

        <!-- Título principal centrado -->
        <div class="text-center mb-5">
            <h1 class="text-primary fw-bold">Bienvenido al Sistema de Gestión</h1>
            <p class="text-muted fs-5">Hola, <strong>{{ $usuario->nombre }}</strong>. ¡Qué bueno tenerte de vuelta!</p>
        </div>

        <!-- Tarjetas informativas centradas -->
        <div class="row justify-content-center g-4">
            <div class="col-md-3">
                <div class="card text-center shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title text-success">Ventas del Día</h5>
                        <p class="card-text fs-3 fw-bold">{{ $ventasHoy }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title text-danger">Compras del Día</h5>
                        <p class="card-text fs-3 fw-bold">{{ $comprasHoy }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title text-info">Total de Productos</h5>
                        <p class="card-text fs-3 fw-bold">{{ $totalProductos }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
