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
                <li class="breadcrumb-item"><a href="{{ route('usuarios.index') }}">Usuarios</a></li>
                <li class="breadcrumb-item active" aria-current="page">Socios</li>
            </ol>
        </nav>
        <h2 class="text-center mt-3">Todos los Socios</h2>
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
    </div>
    @can('socio_crear')
        <div class="d-flex justify-content-end my-3">
            <button class="btn btn-primary me-2" type="button" data-bs-toggle="modal" data-bs-target="#importModal">
                <i class="bi bi-upload"></i> Cargar
            </button>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addSocioModal">
                + Agregar
            </button>
        </div>
        <div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="importModalLabel">Cargar archivo CSV</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <h6 class="modal-title small text-muted mb-3" id="importModalLabel2">Columnas: [Nombre, Celular]</h6>
                        <form action="{{ route('socios.import') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <input type="file" name="file" class="form-control" accept=".csv, .xlsx, .xls" required>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                                <button type="submit" class="btn btn-success">Cargar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="addSocioModal" tabindex="-1" aria-labelledby="addSocioModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title w-100 text-center" id="addSocioModalLabel">Agregar nuevo socio</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('socios.store') }}" method="POST">
                        @csrf
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="nombre_socio" class="form-label">Nombre:</label>
                                <input type="text" class="form-control" id="nombre_socio" name="nombre_socio"
                                    placeholder="Nombre" pattern="[A-Za-z0-9ñÑáéíóúÁÉÍÓÚ ]+" maxlength="50" minlength="4"
                                    oninput="this.value = this.value.replace(/[^A-Za-zñÑáéíóúÁÉÍÓÚ ]/g, '').toUpperCase().substring(0, 50)"
                                    style="text-transform:uppercase;" required>
                            </div>
                            <div class="mb-3">
                                <label for="celular_socio" class="form-label">Celular:</label>
                                <input type="text" class="form-control" id="celular_socio" name="celular_socio"
                                    placeholder="Celular" maxlength="8" minlength="0"
                                    oninput="this.value = this.value.replace(/[^0-9]/g, '').substring(0, 8)">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-success">Agregar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endcan
    <div class="d-flex justify-content-center mb-3 mt-n5">
        <form action="{{ route('socios.index') }}" method="GET" class="d-flex justify-content-center">
            <div class="input-group rounded d-flex justify-content-center">
                <label for="search-input" class="visually-hidden">Buscar</label>
                <input id="search-input" type="search" name="buscar" class="form-control rounded-start "
                    placeholder="Buscar" aria-label="Buscar" aria-describedby="search-addon"
                    pattern="[A-Za-z0-9ñÑáéíóúÁÉÍÓÚ ]+" maxlength="40" minlength="4"
                    oninput="this.value = this.value.replace(/[^A-Za-z0-9ñÑáéíóúÁÉÍÓÚ ]/g, '').toUpperCase().substring(0, 40)"
                    value="{{ old('buscar', $buscar) }}">
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

                    <a href="{{ route('socios.index') }}" class="btn btn-secondary d-flex align-items-center gap-1">
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
        </form>
    </div>
    <div class="table-responsive">

        <table class="table">
            <thead>
                <tr>
                    <th scope="col" class="sticky-id bg-light align-middle">Id</th>
                    <th scope="col" class="bg-light align-middle">Nombre</th>
                    <th scope="col" class="bg-light">Celular</th>
                    <th scope="col" class="bg-light col-sm-2">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($socios as $socio)
                    <tr>
                        <td class="sticky-id bg-light">{{ $socio->id_socio }}</td>
                        <td>{{ $socio->nombre_socio }}</td>
                        <td>{{ $socio->celular_socio }}</td>
                        <td class="">
                            <div class="d-flex justify-content-start align-items-center col">
                                @can('usuario_eliminar')
                                    <button type="button" class="btn btn-danger me-2" data-bs-toggle="modal"
                                        data-bs-target="#deleteSocioModal{{ $socio->id_socio }}">
                                        Eliminar
                                    </button>
                                @endcan
                                @can('socio_actualizar')
                                    <button type="button" class="btn btn-info text-white" data-bs-toggle="modal"
                                        data-bs-target="#editSocioModal{{ $socio->id_socio }}">
                                        Editar
                                    </button>
                                @endcan
                                @can('socio_actualizar')
                                    <div class="modal fade" id="editSocioModal{{ $socio->id_socio }}" tabindex="-1"
                                        aria-labelledby="editSocioModalLabel" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title w-100 text-center" id="editSocioModalLabel">Editar
                                                        socio</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                        aria-label="Close"></button>
                                                </div>
                                                <form method="POST" action="{{ route('socios.update', $socio->id_socio) }}">
                                                    @csrf
                                                    @method('PUT')
                                                    <div class="modal-body ">
                                                        <div class="mb-3 mt-3 mx-3">
                                                            <label for="nombre_socio" class="form-label">Nombre:</label>
                                                            <input type="text" class="form-control" id="nombre_socio"
                                                                name="nombre_socio" value="{{ $socio->nombre_socio }}"
                                                                placeholder="Nombre" pattern="[A-Za-z0-9ñÑáéíóúÁÉÍÓÚ ]+"
                                                                maxlength="50" minlength="4"
                                                                oninput="this.value = this.value.replace(/[^A-Za-zñÑáéíóúÁÉÍÓÚ ]/g, '').toUpperCase().substring(0, 50)"
                                                                style="text-transform:uppercase;" required>
                                                        </div>
                                                        <div style="">
                                                            <div class="mb-3 mx-3">
                                                                <label for="celular_socio" class="form-label">Celular:</label>
                                                                <input type="text" class="form-control" id="celular_socio"
                                                                    name="celular_socio" value="{{ $socio->celular_socio }}"
                                                                    placeholder="Celular" maxlength="8" minlength="0"
                                                                    oninput="this.value = this.value.replace(/[^0-9]/g, '').substring(0, 8)">
                                                            </div>
                                                        </div>
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
                                @endcan
                                @can('socio_eliminar')
                                    <div class="modal fade" id="deleteSocioModal{{ $socio->id_socio }}" tabindex="-1"
                                        aria-labelledby="deleteSocioModalLabel" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header text-white bg-danger">
                                                    <svg width="24px" height="24px" viewBox="0 0 24 24" fill="none"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <path
                                                            d="M21.7605 15.92L15.3605 4.4C14.5005 2.85 13.3105 2 12.0005 2C10.6905 2 9.50047 2.85 8.64047 4.4L2.24047 15.92C1.43047 17.39 1.34047 18.8 1.99047 19.91C2.64047 21.02 3.92047 21.63 5.60047 21.63H18.4005C20.0805 21.63 21.3605 21.02 22.0105 19.91C22.6605 18.8 22.5705 17.38 21.7605 15.92ZM11.2505 9C11.2505 8.59 11.5905 8.25 12.0005 8.25C12.4105 8.25 12.7505 8.59 12.7505 9V14C12.7505 14.41 12.4105 14.75 12.0005 14.75C11.5905 14.75 11.2505 14.41 11.2505 14V9ZM12.7105 17.71C12.6605 17.75 12.6105 17.79 12.5605 17.83C12.5005 17.87 12.4405 17.9 12.3805 17.92C12.3205 17.95 12.2605 17.97 12.1905 17.98C12.1305 17.99 12.0605 18 12.0005 18C11.9405 18 11.8705 17.99 11.8005 17.98C11.7405 17.97 11.6805 17.95 11.6205 17.92C11.5605 17.9 11.5005 17.87 11.4405 17.83C11.3905 17.79 11.3405 17.75 11.2905 17.71C11.1105 17.52 11.0005 17.26 11.0005 17C11.0005 16.74 11.1105 16.48 11.2905 16.29C11.3405 16.25 11.3905 16.21 11.4405 16.17C11.5005 16.13 11.5605 16.1 11.6205 16.08C11.6805 16.05 11.7405 16.03 11.8005 16.02C11.9305 15.99 12.0705 15.99 12.1905 16.02C12.2605 16.03 12.3205 16.05 12.3805 16.08C12.4405 16.1 12.5005 16.13 12.5605 16.17C12.6105 16.21 12.6605 16.25 12.7105 16.29C12.8905 16.48 13.0005 16.74 13.0005 17C13.0005 17.26 12.8905 17.52 12.7105 17.71Z"
                                                            fill="#FFFFFF" />
                                                    </svg>
                                                    <h1 class="modal-title fs-5" id="exampleModalLabel">Eliminar socio
                                                    </h1>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                        aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <p><strong>¡Cuidado!</strong> ¿Estás seguro de que deseas
                                                        <strong>eliminar</strong> al
                                                        socio
                                                        <strong>"{{ $socio->nombre_socio }}"</strong>?
                                                        <br><br><strong>Nota:
                                                        </strong>Esta acción
                                                        no se puede revertir
                                                        y se
                                                        eliminarán todas las ventas relacionadas con este socio.
                                                    </p>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary"
                                                        data-bs-dismiss="modal">Cancelar</button>
                                                    <form action="{{ route('socios.destroy', $socio->id_socio) }}"
                                                        method="POST">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger">Eliminar</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endcan
                        </td>
                    </tr>
                @endforeach
                @if ($socios->isEmpty())
                    <tr>
                        <td colspan="7" class="text-center">
                            <div class="my-2">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"
                                    style="width: 16px; height: 16px;"><!--!Font Awesome Free 6.5.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.-->
                                    <path
                                        d="M256 512A256 256 0 1 0 256 0a256 256 0 1 0 0 512zm0-384c13.3 0 24 10.7 24 24V264c0 13.3-10.7 24-24 24s-24-10.7-24-24V152c0-13.3 10.7-24 24-24zM224 352a32 32 0 1 1 64 0 32 32 0 1 1 -64 0z" />
                                </svg>
                                No se encontraron socios que coincidan con la búsqueda.
                            </div>
                        </td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>
    <nav aria-label="Page navigation example">
        <ul class="pagination justify-content-center mt-4">
            <!-- Enlace "Anterior" -->
            @if ($socios->previousPageUrl())
                <li class="page-item">
                    <a class="page-link"
                        href="{{ $socios->previousPageUrl() . '&' . http_build_query(request()->except('page')) }}"
                        tabindex="-1" aria-disabled="true">Anterior</a>
                </li>
            @else
                <li class="page-item disabled">
                    <span class="page-link">Anterior</span>
                </li>
            @endif

            <!-- Enlaces de páginas -->
            @php
                $totalPages = $socios->lastPage();
                $currentPage = $socios->currentPage();
                $range = 1; // Número de páginas visibles a izquierda y derecha de la actual
                $start = max(1, $currentPage - $range);
                $end = min($totalPages, $currentPage + $range);
            @endphp

            @if ($start > 1)
                <li class="page-item">
                    <a class="page-link"
                        href="{{ $socios->url(1) . '&' . http_build_query(request()->except('page')) }}">1</a>
                </li>
                @if ($start > 2)
                    <li class="page-item disabled d-none d-md-block">
                        <span class="page-link">...</span>
                    </li>
                @endif
            @endif

            @for ($i = $start; $i <= $end; $i++)
                <li class="page-item {{ $socios->currentPage() == $i ? 'active' : '' }}">
                    <a class="page-link"
                        href="{{ $socios->url($i) . '&' . http_build_query(request()->except('page')) }}">{{ $i }}</a>
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
                        href="{{ $socios->url($totalPages) . '&' . http_build_query(request()->except('page')) }}">{{ $totalPages }}</a>
                </li>
            @endif

            <!-- Enlace "Siguiente" -->
            @if ($socios->nextPageUrl())
                <li class="page-item">
                    <a class="page-link"
                        href="{{ $socios->nextPageUrl() . '&' . http_build_query(request()->except('page')) }}">Siguiente</a>
                </li>
            @else
                <li class="page-item disabled">
                    <span class="page-link">Siguiente</span>
                </li>
            @endif
        </ul>
    </nav>
@endsection
