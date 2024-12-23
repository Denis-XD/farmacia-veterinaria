@extends ('layout')
@section('content')

    <div class="Container">
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
                <li class="breadcrumb-item active" aria-current="page">Usuarios</li>
                <li class="breadcrumb-item active" aria-current="page">Permisos</li>
            </ol>
        </nav>
        <h2 class="text-center">Permisos</h2>
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
        {{-- <div class="d-flex justify-content-end my-3">
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addPermisoModal"><svg
                    xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-plus"
                    viewBox="0 0 16 16">
                    <path
                        d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4" />
                </svg>
                Agregar Permisos</button>

            <!-- Add Permiso Modal -->
            <div class="modal fade" id="addPermisoModal" tabindex="-1" aria-labelledby="exampleModalLabel"
                aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5" id="exampleModalLabel">Agregar nuevo permiso</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form action="{{ route('permisos.store') }}" method="POST">
                            <div class="modal-body">
                                @csrf
                                <div class="mb-3">
                                    <label for="nombre" class="form-label">Nombre</label>
                                    <input type="text" class="form-control" id="nombre" name="name">
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
        </div> --}}
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th scope="col" class="sticky-id bg-light align-middle col-sm-1">Id</th>
                        <th scope="col" class="bg-light align-middle">Nombre</th>
                        {{-- <th scope="col" class="bg-light col-sm-1">Acciones</th> --}}
                    </tr>
                </thead>
                <tbody>
                    @forelse ($permisos as $permiso)
                        <tr>
                            <td class="sticky-id bg-light">
                                {{ $permiso->id }}
                            </td>
                            <td>{{ $permiso->name }}</td>
                            {{-- <td>
                                <div class="d-flex justify-content-start align-items-center col">
                                    <button type="button" class="btn btn-danger shadow-sm" data-bs-toggle="modal"
                                        data-bs-target="#deletePermisoModal{{ $permiso->id }}">Eliminar</button>
                                    <div style="width: 0.5rem;"></div>
                                    <button type="button" class="btn btn-info shadow-sm" data-bs-toggle="modal"
                                        data-bs-target="#editPermisoModal{{ $permiso->id }}"
                                        style="color: white">Editar</button>
                                    <!-- Delete Permiso Modal -->
                                    <div class="modal fade" id="deletePermisoModal{{ $permiso->id }}" tabindex="-1"
                                        aria-labelledby="exampleModalLabel" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h1 class="modal-title fs-5" id="exampleModalLabel">Eliminar Permiso
                                                    </h1>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                        aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <p>¿Estás seguro de que deseas eliminar el permiso
                                                        "{{ $permiso->name }}"?</p>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary"
                                                        data-bs-dismiss="modal">Cancelar</button>
                                                    <form action="{{ route('permisos.destroy', $permiso->id) }}"
                                                        method="POST">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger">Eliminar</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Edit Permiso Modal -->
                                    <div class="modal fade" id="editPermisoModal{{ $permiso->id }}" tabindex="-1"
                                        aria-labelledby="exampleModalLabel" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h1 class="modal-title fs-5" id="exampleModalLabel">Editar Permiso
                                                    </h1>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                        aria-label="Close"></button>
                                                </div>
                                                <form action="{{ route('permisos.update', $permiso) }}" method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    <div class="modal-body">
                                                        <div class="mb-3">
                                                            <label for="nombre" class="form-label">Nombre</label>
                                                            <input type="text" class="form-control" id="nombre"
                                                                name="name" autofocus value="{{ $permiso->name }}">
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
                                </div>
                            </td> --}}
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3">
                                <p class="text-center">No existen permisos</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <!-- Paginación -->
        <nav aria-label="Page navigation example">
            <ul class="pagination justify-content-center mt-4">
                <!-- Enlace "Anterior" -->
                @if ($permisos->previousPageUrl())
                    <li class="page-item">
                        <a class="page-link" href="{{ $permisos->previousPageUrl() }}" tabindex="-1"
                            aria-disabled="true">Anterior</a>
                    </li>
                @else
                    <li class="page-item disabled">
                        <span class="page-link">Anterior</span>
                    </li>
                @endif

                <!-- Enlaces de páginas -->
                @foreach ($permisos->getUrlRange(1, $permisos->lastPage()) as $pagina => $url)
                    <li class="page-item {{ $permisos->currentPage() == $pagina ? 'active' : '' }}">
                        <a class="page-link" href="{{ $url }}">{{ $pagina }}</a>
                    </li>
                @endforeach

                <!-- Enlace "Siguiente" -->
                @if ($permisos->nextPageUrl())
                    <li class="page-item">
                        <a class="page-link" href="{{ $permisos->nextPageUrl() }}">Siguiente</a>
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
