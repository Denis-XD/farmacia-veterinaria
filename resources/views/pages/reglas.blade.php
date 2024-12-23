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
                <li class="breadcrumb-item active" aria-current="page">Reglas</li>
                <li class="breadcrumb-item active" aria-current="page">Configuracion</li>
            </ol>
        </nav>
        <h2 class="text-center">CONFIGURACION DE REGLAS</h2>
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
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
            <div class="row justify-content-center">
                <div class="col-lg-6 col-md-12 overflow-hidden">
                    <form method="POST" action="{{ route('reglas.store') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="fecha_inicio_reservas">Fecha inicio de reservas:</label>
                            <input type="date" id="fecha_inicio_reservas" name="fecha_inicio" class="form-control"
                                value="{{ $ultimasReglas->fecha_inicio ?? '' }}" required min="{{ date('Y-m-d') }}">
                        </div>
                        <div class="mb-3">
                            <label for="fecha_final_reservas">Fecha final de reservas:</label>
                            <input type="date" id="fecha_final_reservas" name="fecha_final" class="form-control"
                                value="{{ $ultimasReglas->fecha_final ?? '' }}" required min="{{ date('Y-m-d') }}">
                        </div>
                        <div class="mb-3">
                            <label for="dias_atencion" class="form-label mb-0">Numero de dias para la atencion
                                posterior:</label>
                            <input type="number" class="form-control" id="dias_atencion" name="atencion_posterior"
                                min="0" max="99" value="{{ $ultimasReglas->atencion_posterior ?? '' }}"
                                required>
                        </div>

                        <div class="mb-3">
                            <label for="horario_atencion" class="form-label mb-0">Horario de atencion:</label>
                            <div class="row g-3 mb-3">
                                <div class="col-md-5 col-6">
                                    <input type="time" class="form-control " id="horario_atencion" name="atencion_inicio"
                                        value="{{ $ultimasReglas->atencion_inicio ?? '' }}" required>
                                </div>
                                <div class="col-md-2">
                                    A
                                </div>
                                <div class="col-md-5 col-6">
                                    <input type="time" class="form-control" id="horario_atencion" name="atencion_final"
                                        value="{{ $ultimasReglas->atencion_final ?? '' }}" required>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="Limite_auditorios" class="form-label mb-0">Reservas permitidas por dia para
                                auditorios: </label>
                            <input type="number" class="form-control" id="Limite_auditorios" name="reservas_auditorio"
                                min="0" max="10" value="{{ $ultimasReglas->reservas_auditorio ?? '' }}"
                                required>
                        </div>
                        <div class="mb-3">
                            <label for="mas_reglas" class="form-label mb-0">Mas reglas:</label>
                            <textarea class="form-control" id="mas_reglas" name="mas_reglas" rows="4"
                                placeholder="Ingresa mas reglas de ser necesario">{{ $ultimasReglas && $ultimasReglas->mas_reglas ? $ultimasReglas->mas_reglas : '' }}</textarea>
                        </div>

                        <!-- Botón de Envío -->
                        <div>
                            <a class="btn btn-secondary" href="/">Cancelar</a>
                            <button id="aceptar_reglas" type="submit" class="btn btn-primary">Aceptar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
@endsection
