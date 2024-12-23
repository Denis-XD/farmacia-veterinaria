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
            <li class="breadcrumb-item active" aria-current="page">Ambientes</li>
            <li class="breadcrumb-item active" aria-current="page">Reservar</li>
        </ol>
    </nav>
    <h1 class="text-center">Reservar Ambiente</h1>
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
    <div class="container">
        <div>
            <div class="d-flex justify-content-center mb-3 mt-3">
                <form id="myForm" action="{{ route('ambientes.indexAmbientes') }}" method="POST" class="">
                    @csrf
                    <div class="input-group rounded d-flex justify-content-center" style="max-width: 600px;">
                        <label for="search-input" class="visually-hidden">Buscar</label>
                        <input id="search-input" type="search" name="buscar" class="form-control rounded-start"
                            placeholder="Buscar" aria-label="Buscar" aria-describedby="search-addon" maxlength="40"
                            minlength=""
                            oninput="this.value = this.value.replace(/[^A-Za-z0-9ñÑáéíóúÁÉÍÓÚ ]/g, '').toUpperCase().substring(0, 40)"
                            value="{{ request()->input('buscar') }}" style="max-width: 300px;">

                        <div class="d-flex">
                            <button id="search-button" type="submit" class="btn btn-primary rounded-end me-2">
                                <div style="display: inline-flex; align-items: center;">
                                    <!-- Contenedor div para el icono y el texto -->
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" fill="#ffffff"
                                        style="width: 1.5rem; height: 1.5rem;"> <!-- Estilo para el icono -->
                                        <path
                                            d="M416 208c0 45.9-14.9 88.3-40 122.7L502.6 457.4c12.5 12.5 12.5 32.8 0 45.3s-32.8 12.5-45.3 0L330.7 376c-34.4 25.2-76.8 40-122.7 40C93.1 416 0 322.9 0 208S93.1 0 208 0S416 93.1 416 208zM208 352a144 144 0 1 0 0-288 144 144 0 1 0 0 288z" />
                                    </svg>
                                    <span style="margin-left: 0.5rem;"></span> <!-- Estilo para el texto -->
                                </div>
                            </button>

                            <a href="{{ route('ambientes.indexAmbientes') }}"
                                class="btn btn-secondary d-flex align-items-center gap-1">
                                <div style="display: inline-flex; align-items: center;">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" fill="#ffffff"
                                        style="width: 1.5rem; height: 1.5rem;"> <!-- Estilo para el icono -->
                                        <path
                                            d="M416 208c0 45.9-14.9 88.3-40 122.7L502.6 457.4c12.5 12.5 12.5 32.8 0 45.3s-32.8 12.5-45.3 0L330.7 376c-34.4 25.2-76.8 40-122.7 40C93.1 416 0 322.9 0 208S93.1 0 208 0S416 93.1 416 208zM208 352a144 144 0 1 0 0-288 144 144 0 1 0 0 288z" />
                                        <!-- Línea cruzada en diagonal secundaria -->
                                        <line x1="492" y1="20" x2="20" y2="492" stroke="#ffffff"
                                            stroke-width="40" />
                                    </svg>
                                    <span style="margin-left: 0.5rem;"></span> <!-- Estilo para el texto -->
                                </div>
                                Limpiar
                            </a>
                        </div>


                    </div>

                    <div class="container mt-2">
                        <div class="row">
                            <div class="col align-items-center">
                                <label for="periodo">Periodo</label>
                                <select class="form-select form-select-sm" id="horario_from" name="horario_from">
                                    <option value="0" {{ !request()->input('horario_from') ? 'selected' : '' }}>
                                        Seleccionar</option>
                                    @foreach ($periodos as $periodo)
                                        <option value="{{ $periodo->id_periodo }}"
                                            {{ request()->input('horario_from') == $periodo->id_periodo ? 'selected' : '' }}>
                                            {{ $periodo->inicio }} - {{ $periodo->fin }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col align-items-center">
                                <small><label class="form-label mb-0" for="date_from">Fecha</label></small>
                                <input class="form-control form-control-sm" type="date" name="fecha_from" id="date_from"
                                    value="{{ request('fecha_from') ? request('fecha_from') : date('Y-m-d') }}"
                                    min="{{ date('Y-m-d') }}">
                            </div>

                            <div class="col mb-2">
                                <label for="tipo" class="form-label mb-0">Tipo de ambiente</label>
                                <select class="form-select form-select-sm" aria-label="Default select example"
                                    id="tipo" name="id_tipo">
                                    <option value="" {{ !request()->input('id_tipo') ? 'selected' : '' }}>Todas
                                    </option>
                                    @foreach ($tipos as $tipo)
                                        <option value="{{ $tipo->id_tipo }}"
                                            {{ request()->input('id_tipo') == $tipo->id_tipo ? 'selected' : '' }}>
                                            {{ $tipo->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col btn-group rounded py-2" role="group" aria-label="Botones">
                                <button type="button"
                                    class="btn btn-outline-primary rounded-start toggle-button{{ request()->input('lado') === 'nombre' ? ' active' : '' }}"
                                    aria-pressed="{{ request()->input('lado') === 'nombre' ? 'true' : 'false' }}"
                                    name="lado" value="nombre" onclick="updateValue(this)"
                                    style="{{ request()->input('lado') === 'nombre' ? 'background-color: #003459; ' : '' }}">Nombre</button>
                                <button type="button"
                                    class="btn btn-outline-secondary rounded-end toggle-button{{ request()->input('lado') === 'capacidad' ? ' active' : '' }}"
                                    aria-pressed="{{ request()->input('lado') === 'capacidad' ? 'true' : 'false' }}"
                                    name="lado" value="capacidad" onclick="updateValue(this)"
                                    style="{{ request()->input('lado') === 'capacidad' ? 'background-color: #000000;' : '' }}">Capacidad</button>

                                <input type="hidden" id="ladoValue" name="lado"
                                    value="{{ request()->input('lado', 'nombre') }}">
                                <!-- Campo oculto para almacenar el valor del lado -->
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <div class="text-end mt-3">
                <!-- Asegúrate de incluir el CSS de Font Awesome en tu proyecto -->
                <link rel="stylesheet"
                    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

                <button class="btn btn-primary" type="button" data-bs-toggle="modal"
                    data-bs-target="#reservaGenericaModal">
                    <i class="fas fa-calendar-alt" style="margin-right: 0.5rem;"></i>
                    Reserva Genérica
                </button>
                <a href="{{ route('reserva.grupal', ['user_id' => auth()->user()->id]) }}" class="btn btn-primary">
                    <i class="fas fa-users" style="margin-right: 0.5rem;"></i>
                    Reserva Grupal
                </a>
            </div>

            <script>
                window.addEventListener('DOMContentLoaded', (event) => {
                    const lado = document.getElementById('ladoValue').value;
                    const buttons = document.querySelectorAll('.toggle-button');
                    buttons.forEach(button => {
                        if (button.value === lado) {
                            button.classList.add('active');
                            button.setAttribute('aria-pressed', 'true');
                            button.style.backgroundColor = '#003459';
                            button.style.borderColor = '#000000';
                        } else {
                            button.classList.remove('active');
                            button.setAttribute('aria-pressed', 'false');
                            button.style.backgroundColor = '#ffffff';
                            button.style.borderColor = '#000000';
                        }
                    });
                });

                function updateValue(button) {
                    const ladoValueInput = document.getElementById('ladoValue');
                    ladoValueInput.value = button.value;
                    const buttons = document.querySelectorAll('.toggle-button');
                    buttons.forEach(btn => {
                        if (btn === button) {
                            btn.classList.add('active');
                            btn.setAttribute('aria-pressed', 'true');
                            btn.style.backgroundColor = '#003459';
                            btn.style.borderColor = '#000000';
                        } else {
                            btn.classList.remove('active');
                            btn.setAttribute('aria-pressed', 'false');
                            btn.style.backgroundColor = '#ffffff';
                            btn.style.borderColor = '#000000';
                        }
                    });

                    const tipoBusqueda = button.value;

                    const searchInput = document.getElementById('search-input');

                    searchInput.value = '';

                    if (tipoBusqueda === 'nombre') {
                        searchInput.setAttribute('pattern', '[A-Za-z0-9ñÑáéíóúÁÉÍÓÚ ]+');
                        searchInput.setAttribute('oninput',
                            'this.value = this.value.toUpperCase().replace(/[^A-Za-z0-9ÑÁÉÍÓÚ ]/g, "").substring(0, 40)');
                    } else if (tipoBusqueda === 'capacidad') {
                        searchInput.setAttribute('pattern', '[1-9][0-9]?[0-9]?|300');
                        searchInput.setAttribute('oninput', 'this.value = this.value.replace(/[^0-9]/g, "").substring(0, 3)');
                    }
                }
            </script>
        </div>

        <!-- Modal Reserva Genérica -->
        <div class="modal fade" id="reservaGenericaModal" tabindex="-1" aria-labelledby="reservaGenericaModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form method="POST" action="{{ route('reserva.generica') }}" id="reservaGenericaForm">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title" id="reservaGenericaModalLabel">Reserva Genérica</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="fecha_reserva_generica">Fecha de reserva:</label>
                                <input type="date" id="fecha_reserva_generica" name="fecha_reserva"
                                    class="form-control" required min="{{ date('Y-m-d') }}">
                            </div>
                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label for="id_tipo">Tipo de ambiente</label>
                                    <select class="form-select" id="id_tipo" name="id_tipo" required>
                                        @foreach ($tipos as $tipo)
                                            <option value="{{ $tipo->id_tipo }}">{{ $tipo->nombre }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="capacidad">Capacidad</label>
                                    <input type="number" class="form-control" id="capacidad" name="capacidad"
                                        min="50" max="500" required>
                                </div>
                            </div>
                            <div id="periodo-1-generica" class="row g-3 mb-3">
                                <div class="col-md-11">
                                    <label for="periodo">Periodo</label>
                                    <select class="form-select" id="periodo-generica" name="id_periodo[]">
                                        @foreach ($periodos as $periodo)
                                            <option value="{{ $periodo->id_periodo }}">{{ $periodo->inicio }} -
                                                {{ $periodo->fin }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <button type='button' class="btn btn-primary mb-3" id='agregarperiodo-generica'>Agregar
                                periodo</button>
                            <div id="materia-1-generica" class="row g-3 mb-3">
                                <div class="col-md-11">
                                    <label for="id_carrera" class="form-label mb-0">Materias</label>
                                    @foreach ($agrupadasMaterias as $nombre_materia => $asignaciones)
                                        <div class="mb-2">
                                            <label>{{ $nombre_materia }}</label>
                                            <div class="d-flex flex-wrap">
                                                @foreach ($asignaciones as $asignacion)
                                                    <div class="form-check me-2">
                                                        <input class="form-check-input" type="checkbox"
                                                            value="{{ $asignacion->id_imparte }}" name="id_materia[]">
                                                        <label
                                                            class="form-check-label">{{ $asignacion->grupo->nombre }}</label>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="descripcion_generica">Descripcion</label>
                                <input type="text" class="form-control" id="descripcion_generica" name="descripcion"
                                    maxlength="50">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-success" id="submitReservaGenerica">Solicitar
                                reserva</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        @if ($ambientes->isEmpty())
            <div class="text-center mt-5">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" class="mt-3 mx-auto"
                    style="width: 100px; height: 100px; color: #6c757d;"><!--!Font Awesome Free 6.5.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.-->
                    <path
                        d="M416 208c0 45.9-14.9 88.3-40 122.7L502.6 457.4c12.5 12.5 12.5 32.8 0 45.3s-32.8 12.5-45.3 0L330.7 376c-34.4 25.2-76.8 40-122.7 40C93.1 416 0 322.9 0 208S93.1 0 208 0S416 93.1 416 208zM208 352a144 144 0 1 0 0-288 144 144 0 1 0 0 288z" />
                </svg>
                <p class="lead pt-3" style="font-size: 1.5rem;"><strong>¡Lo sentimos!</strong></p>
                <p class="lead pb-3">No se encontraron resultados.</p>

            </div>
        @else
            <style>
                .card-custom {
                    border: none;
                    color: white;
                    padding: 15px;
                }

                .card-title {
                    display: block;
                    margin-bottom: 0.5rem;
                    padding-bottom: 0.5rem;
                    border-bottom: 1px solid white;
                }

                .btn-outline-custom {
                    color: white;
                    border: 1px solid white;
                    background-color: transparent;
                    margin-top: 0.5rem;
                }

                .btn-outline-custom:hover {
                    background-color: white;
                    color: black;
                }

                .modal-footer .btn-danger {
                    display: none;
                }
            </style>

            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 mt-5">
                @foreach ($ambientes as $ambiente)
                    <div class="col mb-4">
                        <div class="card rounded card-custom" style="background-color: {{ $ambiente->tipo->color }};">
                            <div class="card-body">
                                <h5 class="card-text">{{ $ambiente->tipo->nombre }}</h5>
                                <h2 class="card-title">{{ $ambiente->nombre }}</h2>
                                <p class="card-text">Ubicación: {{ $ambiente->ubicacion->nombre }}</p>
                                <p class="card-text">Capacidad: {{ $ambiente->capacidad }}</p>
                                <div class="d-flex justify-content-between">
                                    <button class="btn btn-outline-custom" data-bs-toggle="modal"
                                        data-bs-target="#descriptionModal-{{ $ambiente->id_ambiente }}">Descripción</button>
                                    <!-- Botón de Reservar  -->
                                    <button class="btn btn-outline-custom" data-bs-toggle="modal"
                                        data-bs-target="#reservaModal-{{ $ambiente->id_ambiente }}"
                                        type="button">Reservar</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Modal hacer reserva -->
                    <div class="modal fade" id="reservaModal-{{ $ambiente->id_ambiente }}" tabindex="-1"
                        aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h1 class="modal-title fs-5" id="exampleModalLabel">Reservar
                                        {{ $ambiente->tipo->nombre }} {{ $ambiente->nombre }}</h1>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <form method="POST" action="{{ route('reserva.store') }}">
                                    @csrf
                                    <input type="hidden" name="id_ambiente" value="{{ $ambiente->id_ambiente }}">
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label for="fecha_reserva">Fecha de reserva:</label>
                                            <input type="date" id="fecha_reserva" name="fecha_reserva"
                                                class="form-control" required min="{{ date('Y-m-d') }}">
                                        </div>
                                        <div id="periodo-1" class="row g-3 mb-3">
                                            <div class="col-md-11">
                                                <label for="periodo">Periodo</label>
                                                <select class="form-select" id="periodo{{ $ambiente->id_ambiente }}"
                                                    name="id_periodo[]">
                                                    @foreach ($periodos as $periodo)
                                                        <option value="{{ $periodo->id_periodo }}">{{ $periodo->inicio }}
                                                            - {{ $periodo->fin }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <button type='button' class="btn btn-primary mb-3"
                                            id='agregarperiodo{{ $ambiente->id_ambiente }}'>Agregar periodo</button>
                                        <div id="materia-1" class="row g-3 mb-3">
                                            <div class="col-md-11">
                                                <label for="id_carrera" class="form-label mb-0">Materias</label>
                                                @foreach ($agrupadasMaterias as $nombre_materia => $asignaciones)
                                                    <div class="mb-2">
                                                        <label>{{ $nombre_materia }}</label>
                                                        <div class="d-flex flex-wrap">
                                                            @foreach ($asignaciones as $asignacion)
                                                                <div class="form-check me-2">
                                                                    <input class="form-check-input" type="checkbox"
                                                                        value="{{ $asignacion->id_imparte }}"
                                                                        name="id_materia[]">
                                                                    <label
                                                                        class="form-check-label">{{ $asignacion->grupo->nombre }}</label>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="descripcion">Descripcion</label>
                                            <input type="text" class="form-control" id="descripcion"
                                                name="descripcion" maxlength="50">
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary"
                                            data-bs-dismiss="modal">Cancelar</button>
                                        <button type="submit" class="btn btn-success">Registrar</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Modal para cada ambiente -->
                    <div class="modal fade bd-example-modal-lg" id="descriptionModal-{{ $ambiente->id_ambiente }}"
                        tabindex="-1" aria-labelledby="descriptionModalLabel-{{ $ambiente->id_ambiente }}"
                        aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h1 class="modal-title fs-5" id="descriptionModalLabel-{{ $ambiente->id_ambiente }}">
                                        Descripción del ambiente</h1>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">

                                    </button>
                                </div>
                                <div class="modal-body">
                                    <h3><strong>Ambiente: </strong> {{ $ambiente->nombre }}</h3>
                                    <p><strong>Tipo de ambiente: </strong> {{ $ambiente->tipo->nombre }}</p>
                                    <p><strong>Ubicación: </strong> {{ $ambiente->ubicacion->nombre }}</p>
                                    <p><strong>Capacidad: </strong> {{ $ambiente->capacidad }}</p>
                                    <p><strong>Descripción: </strong> {{ $ambiente->descripcion }}</p>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary"
                                        data-bs-dismiss="modal">Cerrar</button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <!-- Paginación -->
    <nav aria-label="Page navigation example">
        <ul class="pagination justify-content-center mt-4">
            <!-- Enlace "Anterior" -->
            @if ($ambientes->previousPageUrl())
                <li class="page-item">
                    <a class="page-link"
                        href="{{ $ambientes->previousPageUrl() . '&' . http_build_query(request()->except('page')) }}"
                        tabindex="-1" aria-disabled="true">Anterior</a>
                </li>
            @else
                <li class="page-item disabled">
                    <span class="page-link">Anterior</span>
                </li>
            @endif

            <!-- Enlaces de páginas -->
            @foreach ($ambientes->getUrlRange(1, $ambientes->lastPage()) as $pagina => $url)
                <li class="page-item {{ $ambientes->currentPage() == $pagina ? 'active' : '' }}">
                    <a class="page-link"
                        href="{{ $url . '&' . http_build_query(request()->except('page')) }}">{{ $pagina }}</a>
                </li>
            @endforeach

            <!-- Enlace "Siguiente" -->
            @if ($ambientes->nextPageUrl())
                <li class="page-item">
                    <a class="page-link"
                        href="{{ $ambientes->nextPageUrl() . '&' . http_build_query(request()->except('page')) }}">Siguiente</a>
                </li>
            @else
                <li class="page-item disabled">
                    <span class="page-link">Siguiente</span>
                </li>
            @endif
        </ul>
    </nav>
    </div>
    </div>
    @stack('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('reservaGenericaForm');
            const submitButton = document.getElementById('submitReservaGenerica');

            form.addEventListener('submit', function(event) {
                submitButton.disabled = true;
            });
        });
        // Captura el formulario
        const form = document.getElementById('ambientes-form');

        // Agrega un event listener para el evento 'submit' del formulario
        form.addEventListener('submit', function(event) {
            // Captura el botón activo
            const activeButton = document.querySelector('.toggle-button.active');

            // Si se encontró un botón activo, agrega su valor como un parámetro adicional al formulario
            if (activeButton) {
                const ladoValue = activeButton.getAttribute('value');
                const ladoInput = document.createElement('input');
                ladoInput.setAttribute('type', 'hidden');
                ladoInput.setAttribute('name', 'lado');
                ladoInput.setAttribute('value', ladoValue);
                form.appendChild(ladoInput);
            }
        });
    </script>
@endsection
