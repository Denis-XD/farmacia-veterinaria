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
                <li class="breadcrumb-item active" aria-current="page">Ambientes</li>
                <li class="breadcrumb-item active" aria-current="page">Ambientes</li>
            </ol>
        </nav>
        <h2 class="text-center">Ambientes</h2>
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
        <div class="d-flex justify-content-end my-3">
            @can('ambiente_crear')
                <button class="btn btn-primary me-2" type="button" data-bs-toggle="modal" data-bs-target="#importModal">
                    <i class="bi bi-upload"></i> Cargar
                </button>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addAmbienteModal"><svg
                        xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-plus"
                        viewBox="0 0 16 16">
                        <path
                            d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4" />
                    </svg>
                    Agregar</button>
                <div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="importModalLabel">Cargar archivo CSV</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <h6 class="modal-title small text-muted mb-3" id="importModalLabel2">Columnas: [Tipo, Ubicacion,
                                    Nombre, Capacidad, Habilitado, Descripcion]</h6>
                                <form action="{{ route('ambientes.import') }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <input type="file" name="file" class="form-control" accept=".csv, .xlsx, .xls"
                                        required>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                                        <button type="submit" class="btn btn-success">Cargar</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Add Ambiente Modal -->
                <div class="modal fade" id="addAmbienteModal" tabindex="-1" aria-labelledby="exampleModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h1 class="modal-title fs-5" id="exampleModalLabel">Agregar nuevo ambiente</h1>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <form action="{{ route('ambientes.store') }}" method="POST">
                                <div class="modal-body">
                                    @csrf
                                    <div class="">
                                        <div class="mb-2">
                                            <label for="tipo" class="form-label mb-0">Tipo de ambiente</label>
                                            <select class="form-select" aria-label="Default select example" id="tipo"
                                                name="id_tipo">
                                                @foreach ($tipos as $tipo)
                                                    <option value="{{ $tipo->id_tipo }}">{{ $tipo->nombre }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="mb-2">
                                            <label for="ubicacion" class="form-label mb-0">Ubicación</label>
                                            <select class="form-select" aria-label="Default select example" id="ubicacion"
                                                name="id_ubicacion">
                                                @foreach ($ubicaciones as $ubicacion)
                                                    <option value="{{ $ubicacion->id_ubicacion }}">{{ $ubicacion->nombre }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="mb-2">
                                            <label for="nombre" class="form-label mb-0">Nombre del ambiente</label>
                                            <input type="text" class="form-control" id="nombre" name="nombre"
                                                placeholder="Nombre del ambiente" pattern="[A-Za-z0-9ñÑáéíóúÁÉÍÓÚ ]+"
                                                maxlength="40" minlength="4"
                                                oninput="this.value = this.value.replace(/[^A-Za-z0-9ñÑáéíóúÁÉÍÓÚ ]/g, '').toUpperCase().substring(0, 40)"
                                                style="text-transform:uppercase;" required>
                                        </div>
                                        <div class="row g-3 mb-2">
                                            <div class="col-auto">
                                                <label for="capacidad" class="form-label mb-0">Capacidad</label>
                                                <input type="number" class="form-control" id="capacidad" name="capacidad"
                                                    placeholder="20" min="10" max="300"
                                                    oninput="this.value = this.value.slice(0, 3)" required>
                                            </div>
                                            <div class="col-auto form-check ">
                                                <label for="habilitado" class="form-label mb-0">Habilitado</label>
                                                <div class="form-check">
                                                    <input type="checkbox" class="form-check-input" id="habilitado"
                                                        name="habilitado" value="1" checked>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="mb-2">
                                            <label for="descripcion" class="form-label mb-0">Descripción</label>
                                            <textarea class="form-control" id="descripcion" name="descripcion" rows="3" maxlength="200"
                                                placeholder="Descripción del ambiente" pattern="[A-Za-z0-9ñÑáéíóúÁÉÍÓÚ .,:\n]+" minlength="0"
                                                style="resize: none; text-transform:uppercase;"
                                                oninput="this.value = this.value.replace(/[^A-Za-z0-9ñÑáéíóúÁÉÍÓÚ .,:\n]/g, '').toUpperCase().substring(0, 200)"></textarea>
                                        </div>
                                    </div>
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
            @endcan
        </div>
        <div class="table-responsive">
            <table class="table">
                <thead class="">
                    <tr class="">
                        <th scope="col" class="sticky-id bg-light align-middle">Id
                        </th>
                        <th scope="col" class="bg-light align-middle">Tipo</th>
                        <th scope="col" class="bg-light">Nombre</th>
                        <th scope="col" class="bg-light">Capacidad</th>
                        <th scope="col" class="bg-light">Habilitado</th>
                        <th scope="col" class="bg-light col-sm-1">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($ambientes as $ambiente)
                        <tr>
                            <td class="sticky-id bg-light col-sm-1">
                                {{ $ambiente->id_ambiente }}
                            </td>
                            <td>{{ $ambiente->tipo->nombre }}</td>
                            <td>{{ $ambiente->nombre }}</td>
                            <td>{{ $ambiente->capacidad }}</td>
                            <td>{{ $ambiente->habilitado ? 'Si' : 'No' }}</td>
                            <td>
                                <div class="d-flex justify-content-start align-items-center col">
                                    <button type="button" class="btn btn-info shadow" data-bs-toggle="modal"
                                        data-bs-target="#descriptionAmbienteModal{{ $ambiente->id_ambiente }}"
                                        style="color:white;">Descripción</button>
                                    <div style="width: 0.5rem;"></div>
                                    @can('ambiente_eliminar')
                                        <button type="button" class="btn btn-info shadow" data-bs-toggle="modal"
                                            data-bs-target="#editAmbienteModal{{ $ambiente->id_ambiente }}"
                                            style="color: white">Editar</button>
                                    @endcan

                                    <!-- Description Ambiente Modal -->
                                    <div class="modal fade bd-example-modal-lg"
                                        id="descriptionAmbienteModal{{ $ambiente->id_ambiente }}" tabindex="-1"
                                        aria-labelledby="descriptionAmbienteModalLabel {{ $ambiente->id_ambiente }}"
                                        aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h1 class="modal-title fs-5" id="exampleModalLabel">Descripción del
                                                        ambiente</h1>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                        aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <h3><strong>Ambiente: </strong> {{ $ambiente->nombre }}</h3>
                                                    <p><strong>Tipo de ambiente:</strong> {{ $ambiente->tipo->nombre }}</p>
                                                    <p><strong>Ubicación:</strong> {{ $ambiente->ubicacion->nombre }}</p>
                                                    <p><strong>Capacidad:</strong> {{ $ambiente->capacidad }}</p>
                                                    <p><strong>Habilitado:</strong>
                                                        {{ $ambiente->habilitado ? 'Si' : 'No' }}
                                                    </p>
                                                    <p><strong>Descripción:</strong>
                                                        {{ $ambiente->descripcion }}
                                                    </p>
                                                </div>
                                                <div class="modal-footer">
                                                    @can('ambiente_eliminar')
                                                        <button type="button" class="btn btn-danger shadow-sm"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#deleteAmbienteModal{{ $ambiente->id_ambiente }}">Eliminar</button>
                                                    @endcan
                                                    <button type="button" class="btn btn-secondary"
                                                        data-bs-dismiss="modal">Cerrar</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Delete Ambiente Modal -->
                                    <div class="modal fade" id="deleteAmbienteModal{{ $ambiente->id_ambiente }}"
                                        tabindex="-1"
                                        aria-labelledby="deleteAmbienteModalLabel {{ $ambiente->id_ambiente }}"
                                        aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header bg-danger text-white">
                                                    <svg width="24px" height="24px" viewBox="0 0 24 24"
                                                        fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path
                                                            d="M21.7605 15.92L15.3605 4.4C14.5005 2.85 13.3105 2 12.0005 2C10.6905 2 9.50047 2.85 8.64047 4.4L2.24047 15.92C1.43047 17.39 1.34047 18.8 1.99047 19.91C2.64047 21.02 3.92047 21.63 5.60047 21.63H18.4005C20.0805 21.63 21.3605 21.02 22.0105 19.91C22.6605 18.8 22.5705 17.38 21.7605 15.92ZM11.2505 9C11.2505 8.59 11.5905 8.25 12.0005 8.25C12.4105 8.25 12.7505 8.59 12.7505 9V14C12.7505 14.41 12.4105 14.75 12.0005 14.75C11.5905 14.75 11.2505 14.41 11.2505 14V9ZM12.7105 17.71C12.6605 17.75 12.6105 17.79 12.5605 17.83C12.5005 17.87 12.4405 17.9 12.3805 17.92C12.3205 17.95 12.2605 17.97 12.1905 17.98C12.1305 17.99 12.0605 18 12.0005 18C11.9405 18 11.8705 17.99 11.8005 17.98C11.7405 17.97 11.6805 17.95 11.6205 17.92C11.5605 17.9 11.5005 17.87 11.4405 17.83C11.3905 17.79 11.3405 17.75 11.2905 17.71C11.1105 17.52 11.0005 17.26 11.0005 17C11.0005 16.74 11.1105 16.48 11.2905 16.29C11.3405 16.25 11.3905 16.21 11.4405 16.17C11.5005 16.13 11.5605 16.1 11.6205 16.08C11.6805 16.05 11.7405 16.03 11.8005 16.02C11.9305 15.99 12.0705 15.99 12.1905 16.02C12.2605 16.03 12.3205 16.05 12.3805 16.08C12.4405 16.1 12.5005 16.13 12.5605 16.17C12.6105 16.21 12.6605 16.25 12.7105 16.29C12.8905 16.48 13.0005 16.74 13.0005 17C13.0005 17.26 12.8905 17.52 12.7105 17.71Z"
                                                            fill="#FFFFFF" />
                                                    </svg>
                                                    <h1 class="modal-title fs-5" id="exampleModalLabel">
                                                        Eliminar ambiente
                                                    </h1>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                        aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <p><strong>¡Cuidado!</strong> ¿Estás seguro de que deseas eliminar el
                                                        ambiente
                                                        <strong>"{{ $ambiente->nombre }}"</strong>?<br><br><strong>Nota:
                                                        </strong>Esta acción
                                                        no
                                                        se puede revertir
                                                        y se
                                                        eliminarán todos los elementos relacionados con este ambiente.
                                                    </p>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary"
                                                        data-bs-dismiss="modal">Cancelar</button>
                                                    <form
                                                        action="{{ route('ambientes.destroy', $ambiente->id_ambiente) }}"
                                                        method="POST">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger">Eliminar</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Edit Ambiente Modal -->
                                    <div class="modal fade" id="editAmbienteModal{{ $ambiente->id_ambiente }}"
                                        tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h1 class="modal-title fs-5" id="exampleModalLabel">Editar ambiente
                                                    </h1>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                        aria-label="Close"></button>
                                                </div>
                                                <form action="{{ route('ambientes.update', $ambiente) }}" method="POST">
                                                    <div class="modal-body">
                                                        @csrf
                                                        @method('PUT')
                                                        <input type="hidden" name="id_ambiente"
                                                            value="{{ $ambiente->id_ambiente }}">
                                                        <div class="mb-2">
                                                            <label for="editTipo" class="form-label mb-0">Tipo de
                                                                ambiente</label>
                                                            <select class="form-select"
                                                                aria-label="Default select example" id="editTipo"
                                                                name="id_tipo">
                                                                @foreach ($tipos as $tipo)
                                                                    <option value="{{ $tipo->id_tipo }}"
                                                                        {{ $ambiente->tipo->id_tipo == $tipo->id_tipo ? 'selected' : '' }}>
                                                                        {{ $tipo->nombre }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div class="mb-2">
                                                            <label for="editUbicacion"
                                                                class="form-label mb-0">Ubicación</label>
                                                            <select class="form-select"
                                                                aria-label="Default select example" id="editUbicacion"
                                                                name="id_ubicacion">
                                                                @foreach ($ubicaciones as $ubicacion)
                                                                    <option value="{{ $ubicacion->id_ubicacion }}"
                                                                        {{ $ambiente->ubicacion->id_ubicacion == $ubicacion->id_ubicacion ? 'selected' : '' }}>
                                                                        {{ $ubicacion->nombre }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div class="mb-2">
                                                            <label for="editNombre" class="form-label mb-0">Nombre del
                                                                ambiente</label>
                                                            <input type="text" class="form-control" id="editNombre"
                                                                name="nombre" value="{{ $ambiente->nombre }}"
                                                                pattern="[A-Za-z0-9ñÑáéíóúÁÉÍÓÚ ]+" maxlength="40"
                                                                minlength="4" placeholder="Nombre del ambiente"
                                                                oninput="this.value = this.value.replace(/[^A-Za-z0-9ñÑáéíóúÁÉÍÓÚ ]/g, '').toUpperCase().substring(0, 40)"
                                                                style="text-transform:uppercase;" required>
                                                        </div>
                                                        <div class="row g-3 mb-2">
                                                            <div class="col-auto">
                                                                <label for="editCapacidad"
                                                                    class="form-label mb-0">Capacidad</label>
                                                                <input type="number" class="form-control"
                                                                    id="editCapacidad" name="capacidad"
                                                                    value="{{ $ambiente->capacidad }}"
                                                                    oninput="this.value = this.value.slice(0, 3)"
                                                                    placeholder="20" min="10" max="300"
                                                                    required>
                                                            </div>
                                                            <div class="col-auto form-check ">
                                                                <label for="editHabilitado"
                                                                    class="form-label mb-0">Habilitado</label>
                                                                <div class="form-check">
                                                                    <input type="checkbox" class="form-check-input"
                                                                        id="editHabilitado" name="habilitado"
                                                                        {{ $ambiente->habilitado ? 'checked' : '' }}>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="mb-2">
                                                            <label for="editDescripcion"
                                                                class="form-label mb-0">Descripción</label>
                                                            <textarea class="form-control" id="editDescripcion" name="descripcion" rows="3"
                                                                style="resize: none; text-transform:uppercase;"
                                                                oninput="this.value = this.value.replace(/[^A-Za-z0-9ñÑáéíóúÁÉÍÓÚ .,:\n]/g, '').toUpperCase().substring(0, 200)"
                                                                pattern="[A-Za-z0-9ñÑáéíóúÁÉÍÓÚ .,:\n]+" placeholder="Descripción del ambiente" minlength="0" maxlength="200">{{ $ambiente->descripcion }}</textarea>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary"
                                                            data-bs-dismiss="modal">Cancelar</button>
                                                        <button type="submit" class="btn btn-success">Guardar
                                                            cambios</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

    </div>

    <!-- Paginación -->
    <nav aria-label="Page navigation example">
        <ul class="pagination justify-content-center mt-4">
            <!-- Enlace "Anterior" -->
            @if ($ambientes->previousPageUrl())
                <li class="page-item">
                    <a class="page-link" href="{{ $ambientes->previousPageUrl() }}" tabindex="-1"
                        aria-disabled="true">Anterior</a>
                </li>
            @else
                <li class="page-item disabled">
                    <span class="page-link">Anterior</span>
                </li>
            @endif

            <!-- Enlaces de páginas -->
            @foreach ($ambientes->getUrlRange(1, $ambientes->lastPage()) as $pagina => $url)
                <li class="page-item {{ $ambientes->currentPage() == $pagina ? 'active' : '' }}">
                    <a class="page-link" href="{{ $url }}">{{ $pagina }}</a>
                </li>
            @endforeach

            <!-- Enlace "Siguiente" -->
            @if ($ambientes->nextPageUrl())
                <li class="page-item">
                    <a class="page-link" href="{{ $ambientes->nextPageUrl() }}">Siguiente</a>
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
