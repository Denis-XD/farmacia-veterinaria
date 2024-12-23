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
                <li class="breadcrumb-item active" aria-current="page">Materias</li>
                <li class="breadcrumb-item active" aria-current="page">Grupos</li>
            </ol>
        </nav>
        <h2 class="text-center">Grupos</h2>
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
        <div class="d-flex justify-content-end my-3">
            @can('grupo_crear')
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addGrupoModal">
                    + Agregar
                </button>
                <div class="modal fade" id="addGrupoModal" tabindex="-1" aria-labelledby="addGrupoModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h1 class="modal-title fs-5 w-100 text-center" id="addGrupoModalLabel">Agregar nuevo grupo</h1>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <form action="{{ route('grupos.store') }}" method="POST">
                                <div class="modal-body">
                                    @csrf
                                    <div class="mb-3">
                                        <label for="nombre" class="form-label">Nombre</label>
                                        <input type="text" class="form-control" id="nombre" name="nombre"
                                            placeholder='EJEMPLO: G3' pattern="[A-Za-zñÑáéíóúÁÉÍÓÚ0-9 ]{2,20}" maxlength="20"
                                            oninput="this.value = this.value.replace(/[^A-Za-z0-9ñÑáéíóúÁÉÍÓÚ ]/g, '').toUpperCase().substring(0, 20)"
                                            title="Minimo 2 caracteres y Máximo 20 caracteres."
                                            style="text-transform:uppercase;" required>
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
        </div>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th scope="col" class="sticky-id bg-light align-middle col-sm-1">Id</th>
                        <th scope="col" class="bg-light align-middle">Nombre</th>
                        <th scope="col" class="bg-light col-sm-1">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($grupos as $grupo)
                        <tr>
                            <td class="sticky-id bg-light">
                                {{ $grupo->id_grupo }}
                            </td>
                            <td>{{ $grupo->nombre }}</td>
                            <td>
                                <div class="d-flex justify-content-start align-items-center">
                                    @can('grupo_eliminar')
                                        <button type="button" class="btn btn-danger me-2" data-bs-toggle="modal"
                                            data-bs-target="#deleteGrupoModal{{ $grupo->id_grupo }}">
                                            Eliminar
                                        </button>
                                    @endcan
                                    <div style="width: 0.5rem;"></div>
                                    @can('grupo_actualizar')
                                        <button type="button" class="btn btn-info" data-bs-toggle="modal"
                                            data-bs-target="#editGrupoModal{{ $grupo->id_grupo }}" style="color: white">
                                            Editar
                                        </button>
                                    @endcan
                                    <div class="modal fade" id="editGrupoModal{{ $grupo->id_grupo }}" tabindex="-1"
                                        aria-labelledby="editGrupoModalLabel{{ $grupo->id_grupo }}" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title"
                                                        id="editGrupoModalLabel{{ $grupo->id_grupo }}">
                                                        Editar grupo</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                        aria-label="Close"></button>
                                                </div>
                                                <form action="{{ route('grupos.update', $grupo->id_grupo) }}"
                                                    method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    <div class="modal-body">
                                                        <div class="mb-3">
                                                            <label for="nombre{{ $grupo->id_grupo }}"
                                                                class="form-label">Nombre</label>
                                                            <input type="text" class="form-control"
                                                                id="nombre{{ $grupo->id_grupo }}" name="nombre"
                                                                value="{{ $grupo->nombre }}"
                                                                pattern="[A-Za-zñÑáéíóúÁÉÍÓÚ0-9 ]{2,20}" maxlength="20"
                                                                oninput="this.value = this.value.replace(/[^A-Za-z0-9ñÑáéíóúÁÉÍÓÚ ]/g, '').toUpperCase().substring(0, 20)"
                                                                title="Minimo 2 caracteres y Máximo 20 caracteres."
                                                                style="text-transform:uppercase;" required>
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
                                    <div class="modal fade" id="deleteGrupoModal{{ $grupo->id_grupo }}" tabindex="-1"
                                        aria-labelledby="deleteGrupoModalLabel{{ $grupo->id_grupo }}" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header text-white bg-danger">
                                                    <svg width="24px" height="24px" viewBox="0 0 24 24"
                                                        fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path
                                                            d="M21.7605 15.92L15.3605 4.4C14.5005 2.85 13.3105 2 12.0005 2C10.6905 2 9.50047 2.85 8.64047 4.4L2.24047 15.92C1.43047 17.39 1.34047 18.8 1.99047 19.91C2.64047 21.02 3.92047 21.63 5.60047 21.63H18.4005C20.0805 21.63 21.3605 21.02 22.0105 19.91C22.6605 18.8 22.5705 17.38 21.7605 15.92ZM11.2505 9C11.2505 8.59 11.5905 8.25 12.0005 8.25C12.4105 8.25 12.7505 8.59 12.7505 9V14C12.7505 14.41 12.4105 14.75 12.0005 14.75C11.5905 14.75 11.2505 14.41 11.2505 14V9ZM12.7105 17.71C12.6605 17.75 12.6105 17.79 12.5605 17.83C12.5005 17.87 12.4405 17.9 12.3805 17.92C12.3205 17.95 12.2605 17.97 12.1905 17.98C12.1305 17.99 12.0605 18 12.0005 18C11.9405 18 11.8705 17.99 11.8005 17.98C11.7405 17.97 11.6805 17.95 11.6205 17.92C11.5605 17.9 11.5005 17.87 11.4405 17.83C11.3905 17.79 11.3405 17.75 11.2905 17.71C11.1105 17.52 11.0005 17.26 11.0005 17C11.0005 16.74 11.1105 16.48 11.2905 16.29C11.3405 16.25 11.3905 16.21 11.4405 16.17C11.5005 16.13 11.5605 16.1 11.6205 16.08C11.6805 16.05 11.7405 16.03 11.8005 16.02C11.9305 15.99 12.0705 15.99 12.1905 16.02C12.2605 16.03 12.3205 16.05 12.3805 16.08C12.4405 16.1 12.5005 16.13 12.5605 16.17C12.6105 16.21 12.6605 16.25 12.7105 16.29C12.8905 16.48 13.0005 16.74 13.0005 17C13.0005 17.26 12.8905 17.52 12.7105 17.71Z"
                                                            fill="#FFFFFF" />
                                                    </svg>
                                                    <h1 class="modal-title fs-5" id="exampleModalLabel">Eliminar grupo
                                                    </h1>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                        aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <p><strong>¡Cuidado!</strong> ¿Estás seguro de que deseas
                                                        <strong>eliminar</strong> el
                                                        grupo
                                                        <strong>"{{ $grupo->nombre }}"</strong>?
                                                        <br><br><strong>Nota:</strong>
                                                        Esta acción no se puede revertir y se eliminan todos los registros
                                                        de asignaciones que contienen a este grupo.
                                                    </p>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary"
                                                        data-bs-dismiss="modal">Cancelar</button>
                                                    <form action="{{ route('grupos.destroy', $grupo->id_grupo) }}"
                                                        method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger">Eliminar</button>
                                                    </form>
                                                </div>
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
        <nav aria-label="Page navigation example">
            <ul class="pagination justify-content-center mt-4">
                <!-- Enlace "Anterior" -->
                @if ($grupos->previousPageUrl())
                    <li class="page-item">
                        <a class="page-link" href="{{ $grupos->previousPageUrl() }}" tabindex="-1"
                            aria-disabled="true">Anterior</a>
                    </li>
                @else
                    <li class="page-item disabled">
                        <span class="page-link">Anterior</span>
                    </li>
                @endif

                <!-- Enlaces de páginas -->
                @foreach ($grupos->getUrlRange(1, $grupos->lastPage()) as $pagina => $url)
                    <li class="page-item {{ $grupos->currentPage() == $pagina ? 'active' : '' }}">
                        <a class="page-link" href="{{ $url }}">{{ $pagina }}</a>
                    </li>
                @endforeach

                <!-- Enlace "Siguiente" -->
                @if ($grupos->nextPageUrl())
                    <li class="page-item">
                        <a class="page-link" href="{{ $grupos->nextPageUrl() }}">Siguiente</a>
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
