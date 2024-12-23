@extends('layout')
@section('content')
    <div class="my-3">
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
                <li class="breadcrumb-item"><a href="{{ route('usuarios.index') }}">Usuarios</a></li>
                <li class="breadcrumb-item active" aria-current="page">Asignaciones</li>
            </ol>
        </nav>
        <h2 class="text-center">TODAS LAS MATERIAS</h2>
        @if ($docente)
            <h2 class="text-center">{{ $docente->apellido }} {{ $docente->nombre }}</h2>
        @endif
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
        @can('asignacion_crear')
            <div class="d-flex justify-content-end my-3">
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addTipoModal"><svg
                        xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-plus"
                        viewBox="0 0 16 16">
                        <path
                            d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4" />
                    </svg>
                    Asignar</button>
                <!-- Add Tipo Modal -->
                <div class="modal fade" id="addTipoModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h1 class="modal-title fs-5" id="exampleModalLabel">Asignar nueva materia</h1>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <form action="{{ route('asignaciones.store') }}" method="POST">
                                <div class="modal-body">
                                    @csrf
                                    <input type="hidden" name="id_docente" value="{{ $docente->id }}">
                                    <div class="mb-3">
                                        <label for="materia" class="form-label">Nombre de la materia</label>
                                        <select class="form-select" id="id_materia" name="id_materia" required>
                                            @foreach ($materias as $materia)
                                                <option value="{{ $materia->id_materia }}">{{ $materia->nombre_materia }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="row g-3 mb-3">
                                        <div class="col-md-6">
                                            <label for="grupo" class="form-label">Grupo</label>
                                            <select class="form-select" id="id_grupo" name="id_grupo" required>
                                                @foreach ($grupos as $grupo)
                                                    <option value="{{ $grupo->id_grupo }}">{{ $grupo->nombre }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div id="labelsCarreras" class="row g-3 mb-3">
                                        <div class="col-md-9">
                                            <label for="id_carrera" class="form-label mb-0">Carreras</label>
                                        </div>
                                    </div>
                                    <div id="carreraContainer">
                                        <div id="carreraTemplate" class="row g-3 mb-3 carreraRow" style="display: none;">
                                            <div class="col-md-9">
                                                <select class="form-select" name="id_carrera[]">
                                                    <option value="" disabled selected>Selecciona una carrera</option>
                                                    @foreach ($carreras as $carrera)
                                                        <option value="{{ $carrera->id_carrera }}">{{ $carrera->nombre }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-2 col-6">
                                                <select class="form-select" name="nivel[]">
                                                    <option value="" disabled selected>Nivel</option>
                                                    @for ($i = 65; $i <= 74; $i++)
                                                        <option value="{{ chr($i) }}">{{ chr($i) }}</option>
                                                    @endfor
                                                </select>
                                            </div>
                                            <div class="col-md-1 col-6 d-flex align-items-center">
                                                <button type="button" class="btn btn-danger removeCarreraBtn">X</button>
                                            </div>
                                        </div>
                                    </div>
                                    <button type='button' class="btn btn-primary mb-3" id='agregarCarrera'>Agregar
                                        Carrera</button>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary"
                                        data-bs-dismiss="modal">Cancelar</button>
                                    <button type="submit" class="btn btn-success">Agregar</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @endcan
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th scope="col" class="sticky-id bg-light align-middle col-sm-1">Id</th>
                        <th scope="col" class="bg-light align-middle">Nombre materia</th>
                        <th scope="col" class="bg-light align-middle">Grupo</th>
                        <th scope="col" class="bg-light align-middle">Carreras</th>
                        <th scope="col" class="bg-light col-sm-1">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($imparte as $asignacion)
                        <tr>
                            <td class="sticky-id bg-light col-sm-1">{{ $asignacion->id_imparte }}</td>
                            <td>{{ $asignacion->materia->nombre_materia }}</td>
                            <td>{{ $asignacion->grupo->nombre }}</td>
                            <td>
                                @foreach ($asignacion->impartesCarreras as $imparteCarrera)
                                    <span class="badge bg-primary">{{ $imparteCarrera->carrera->nombre }}
                                        ({{ $imparteCarrera->nivel }})
                                    </span>
                                @endforeach
                            </td>
                            <td class="d-flex justify-content-end align-items-center col">
                                @can('asignacion_eliminar')
                                    <button class="btn btn-danger" type="button" data-bs-toggle="modal"
                                        data-bs-target="#deleteAsignacionModal{{ $asignacion->id_imparte }}">
                                        Eliminar
                                    </button>
                                @endcan
                                <div style="width: 0.5rem;"></div>
                                @can('asignacion_actualizar')
                                    <button class="btn btn-info" type="button" data-bs-toggle="modal"
                                        data-bs-target="#editAsignacionModal{{ $asignacion->id_imparte }}">
                                        <span style="color: white;">Editar</span>
                                    </button>
                                @endcan
                                <!-- Delete Modal -->
                                <div class="modal fade" id="deleteAsignacionModal{{ $asignacion->id_imparte }}"
                                    tabindex="-1"
                                    aria-labelledby="deleteAsignacionModalLabel{{ $asignacion->id_imparte }}"
                                    aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header text-white bg-danger">
                                                <svg width="24px" height="24px" viewBox="0 0 24 24" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        d="M21.7605 15.92L15.3605 4.4C14.5005 2.85 13.3105 2 12.0005 2C10.6905 2 9.50047 2.85 8.64047 4.4L2.24047 15.92C1.43047 17.39 1.34047 18.8 1.99047 19.91C2.64047 21.02 3.92047 21.63 5.60047 21.63H18.4005C20.0805 21.63 21.3605 21.02 22.0105 19.91C22.6605 18.8 22.5705 17.38 21.7605 15.92ZM11.2505 9C11.2505 8.59 11.5905 8.25 12.0005 8.25C12.4105 8.25 12.7505 8.59 12.7505 9V14C12.7505 14.41 12.4105 14.75 12.0005 14.75C11.5905 14.75 11.2505 14.41 11.2505 14V9ZM12.7105 17.71C12.6605 17.75 12.6105 17.79 12.5605 17.83C12.5005 17.87 12.4405 17.9 12.3805 17.92C12.3205 17.95 12.2605 17.97 12.1905 17.98C12.1305 17.99 12.0605 18 12.0005 18C11.9405 18 11.8705 17.99 11.8005 17.98C11.7405 17.97 11.6805 17.95 11.6205 17.92C11.5605 17.9 11.5005 17.87 11.4405 17.83C11.3905 17.79 11.3405 17.75 11.2905 17.71C11.1105 17.52 11.0005 17.26 11.0005 17C11.0005 16.74 11.1105 16.48 11.2905 16.29C11.3405 16.25 11.3905 16.21 11.4405 16.17C11.5005 16.13 11.5605 16.1 11.6205 16.08C11.6805 16.05 11.7405 16.03 11.8005 16.02C11.9305 15.99 12.0705 15.99 12.1905 16.02C12.2605 16.03 12.3205 16.05 12.3805 16.08C12.4405 16.1 12.5005 16.13 12.5605 16.17C12.6105 16.21 12.6605 16.25 12.7105 16.29C12.8905 16.48 13.0005 16.74 13.0005 17C13.0005 17.26 12.8905 17.52 12.7105 17.71Z"
                                                        fill="#FFFFFF" />
                                                </svg>
                                                <h1 class="modal-title fs-5"
                                                    id="deleteAsignacionModalLabel{{ $asignacion->id_imparte }}">Eliminar
                                                    asignación de materia</h1>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <p><strong>¡Cuidado!</strong> ¿Estás seguro de que deseas
                                                    <strong>eliminar</strong> la asignación de la materia
                                                    <strong>"{{ $asignacion->materia->nombre_materia }}
                                                        {{ $asignacion->grupo->nombre }}"</strong>?
                                                    <br><br><strong>Nota:</strong> Esta acción no se puede revertir y se
                                                    eliminan de todos los registros de reservas de ambientes relacionadas a
                                                    esta asignacion.
                                                </p>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary"
                                                    data-bs-dismiss="modal">Cancelar</button>
                                                <form
                                                    action="{{ route('asignaciones.destroy', $asignacion->id_imparte) }}"
                                                    method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger">Eliminar</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- Edit Modal -->
                                <div class="modal fade" id="editAsignacionModal{{ $asignacion->id_imparte }}"
                                    tabindex="-1" aria-labelledby="editAsignacionModal" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title w-100 text-center" id="editAsignacionModal">Editar
                                                    asignación de materia</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>
                                            <form method="POST"
                                                action="{{ route('asignaciones.update', $asignacion->id_imparte) }}">
                                                @csrf
                                                @method('PUT')
                                                <div class="modal-body">
                                                    <input type="hidden" name="id_docente" value="{{ $docente->id }}">
                                                    <div class="mb-3">
                                                        <label for="materia" class="form-label">Nombre de la
                                                            materia</label>
                                                        <select class="form-select" id="id_materia" name="id_materia"
                                                            required>
                                                            @foreach ($materias as $materia)
                                                                <option value="{{ $materia->id_materia }}"
                                                                    {{ $asignacion->id_materia == $materia->id_materia ? 'selected' : '' }}>
                                                                    {{ $materia->nombre_materia }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="row g-3 mb-3">
                                                        <div class="col-md-6">
                                                            <label for="grupo" class="form-label">Grupo</label>
                                                            <select class="form-select" id="id_grupo" name="id_grupo"
                                                                required>
                                                                @foreach ($grupos as $grupo)
                                                                    <option value="{{ $grupo->id_grupo }}"
                                                                        {{ $asignacion->id_grupo == $grupo->id_grupo ? 'selected' : '' }}>
                                                                        {{ $grupo->nombre }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div id="labelsCarreras" class="row g-3 mb-3">
                                                        <div class="col-md-9">
                                                            <label for="id_carrera"
                                                                class="form-label mb-0">Carreras</label>
                                                        </div>
                                                    </div>
                                                    <div id="editCarreraContainer{{ $asignacion->id_imparte }}">
                                                        @foreach ($asignacion->impartesCarreras as $imparteCarrera)
                                                            <div class="row g-3 mb-3 carreraRow">
                                                                <div class="col-md-9">
                                                                    <select class="form-select" name="id_carrera[]"
                                                                        required>
                                                                        @foreach ($carreras as $carrera)
                                                                            <option value="{{ $carrera->id_carrera }}"
                                                                                {{ $imparteCarrera->id_carrera == $carrera->id_carrera ? 'selected' : '' }}>
                                                                                {{ $carrera->nombre }}
                                                                            </option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                                <div class="col-md-2 col-6">
                                                                    <select class="form-select" name="nivel[]" required>
                                                                        @for ($i = 65; $i <= 74; $i++)
                                                                            <option value="{{ chr($i) }}"
                                                                                {{ $imparteCarrera->nivel == chr($i) ? 'selected' : '' }}>
                                                                                {{ chr($i) }}
                                                                            </option>
                                                                        @endfor
                                                                    </select>
                                                                </div>
                                                                <div class="col-md-1 col-6 d-flex align-items-center">
                                                                    <button type="button"
                                                                        class="btn btn-danger removeCarreraBtn">X</button>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                    <button type='button' class="btn btn-primary mb-3 editAgregarCarrera"
                                                        data-container-id="editCarreraContainer{{ $asignacion->id_imparte }}">Agregar
                                                        Carrera</button>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary"
                                                        data-bs-dismiss="modal">Cancelar</button>
                                                    <button type="submit" class="btn btn-success">Guardar</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <nav aria-label="Page navigation example">
            <ul class="pagination justify-content-center mt-4">
                <!-- Enlace "Anterior" -->
                @if ($imparte->previousPageUrl())
                    <li class="page-item">
                        <a class="page-link" href="{{ $imparte->previousPageUrl() }}" tabindex="-1"
                            aria-disabled="true">Anterior</a>
                    </li>
                @else
                    <li class="page-item disabled">
                        <span class="page-link">Anterior</span>
                    </li>
                @endif
                <!-- Enlaces de páginas -->
                @foreach ($imparte->getUrlRange(1, $imparte->lastPage()) as $pagina => $url)
                    <li class="page-item {{ $imparte->currentPage() == $pagina ? 'active' : '' }}">
                        <a class="page-link" href="{{ $url }}">{{ $pagina }}</a>
                    </li>
                @endforeach
                <!-- Enlace "Siguiente" -->
                @if ($imparte->nextPageUrl())
                    <li class="page-item">
                        <a class="page-link" href="{{ $imparte->nextPageUrl() }}">Siguiente</a>
                    </li>
                @else
                    <li class="page-item disabled">
                        <span class="page-link">Siguiente</span>
                    </li>
                @endif
            </ul>
        </nav>
    </div>
@endsection

<script>
    document.addEventListener('DOMContentLoaded', function() {
        function agregarEventoAgregarCarrera(boton, containerId, templateId) {
            boton.addEventListener('click', function() {
                const template = document.getElementById(templateId);
                const newCarrera = template.cloneNode(true);
                newCarrera.id = '';
                newCarrera.style.display = 'flex';
                document.getElementById(containerId).appendChild(newCarrera);
                newCarrera.querySelector('.removeCarreraBtn').addEventListener('click', function() {
                    newCarrera.remove();
                });
            });
        }

        function agregarEventoEliminarCarrera() {
            document.querySelectorAll('.removeCarreraBtn').forEach(button => {
                button.addEventListener('click', function() {
                    button.closest('.carreraRow').remove();
                });
            });
        }

        // Agregar carrera en el modal de agregar
        const botonAgregarCarrera = document.getElementById('agregarCarrera');
        agregarEventoAgregarCarrera(botonAgregarCarrera, 'carreraContainer', 'carreraTemplate');

        // Agregar carrera en el modal de editar
        document.querySelectorAll('.editAgregarCarrera').forEach(boton => {
            const containerId = boton.getAttribute('data-container-id');
            agregarEventoAgregarCarrera(boton, containerId, 'carreraTemplate');
        });

        agregarEventoEliminarCarrera();

        // Cambiar el texto del botón de eliminar en función del tamaño de la pantalla
        function actualizarTextoBotones() {
            const width = window.innerWidth;
            document.querySelectorAll('.removeCarreraBtn').forEach(button => {
                button.textContent = width < 768 ? 'Eliminar' : 'X';
            });
        }

        window.addEventListener('resize', actualizarTextoBotones);
        actualizarTextoBotones();
    });
</script>
