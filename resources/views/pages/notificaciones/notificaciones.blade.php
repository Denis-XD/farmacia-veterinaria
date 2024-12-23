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
                <li class="breadcrumb-item active" aria-current="page">Notificaciones</li>
            </ol>
        </nav>
        <h2 class="text-center">Notificaciones</h2>
    </div>
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
    <div>
        <div class="rounded bg-white shadow p-2 d-flex flex-column gap-2">
            <form action="{{ route('notificaciones.index') }}" id="search_form" method="GET">
                @csrf
                <div class="align-items-center">
                    <small><label class="form-label" for="correo">Correo</label></small>
                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                        <button type="button" class="btn btn-primary d-flex gap-1" data-bs-toggle="modal"
                            data-bs-target="#searchUserModal">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round"
                                class="icon icon-tabler icons-tabler-outline icon-tabler-user-search">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0" />
                                <path d="M6 21v-2a4 4 0 0 1 4 -4h1.5" />
                                <path d="M18 18m-3 0a3 3 0 1 0 6 0a3 3 0 1 0 -6 0" />
                                <path d="M20.2 20.2l1.8 1.8" />
                            </svg> <span class="d-none d-md-inline-block">Destinatario</span></button>
                        <input class="form-control text-truncate" style="flex-grow: grow;" type="email" name="correo_view"
                            id="correo_view" value="{{ request('correo') }}" disabled readonly>
                        <input class="form-control" hidden type="email" name="correo" id="correo"
                            value="{{ request('correo') }}">
                        <button type="button" class="btn btn-danger" onclick="clearCorreo()">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-user-off">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M8.18 8.189a4.01 4.01 0 0 0 2.616 2.627m3.507 -.545a4 4 0 1 0 -5.59 -5.552" />
                                <path
                                    d="M6 21v-2a4 4 0 0 1 4 -4h4c.412 0 .81 .062 1.183 .178m2.633 2.618c.12 .38 .184 .785 .184 1.204v2" />
                                <path d="M3 3l18 18" />
                            </svg></span>
                        </button>
                    </div>
                    <!-- Modal -->
                    <div class="modal fade" id="searchUserModal" tabindex="-1" aria-labelledby="searchUserModalLabel"
                        aria-hidden="true">
                        <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h1 class="modal-title fs-5" id="staticBackdropLabel">Modal title</h1>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="p-2 d-flex align-items-center gap-2"> <span class="text-primary"><svg
                                            xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                            viewBox="0 0 24 24" fill="currentColor"
                                            class="icon icon-tabler icons-tabler-filled icon-tabler-user">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                            <path d="M12 2a5 5 0 1 1 -5 5l.005 -.217a5 5 0 0 1 4.995 -4.783z" />
                                            <path
                                                d="M14 14a5 5 0 0 1 5 5v1a2 2 0 0 1 -2 2h-10a2 2 0 0 1 -2 -2v-1a5 5 0 0 1 5 -5h4z" />
                                        </svg></span>
                                    <div class="flex-grow-1">
                                        <input class="form-control" id="userInput" type="text"
                                            placeholder="Nombre o correo" onkeyup="filterUsers()">
                                    </div>
                                </div>
                                <div class="modal-body">
                                    <ul class="list-group" id="list_users">
                                    </ul>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary"
                                        data-bs-dismiss="modal">Cancelar</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="d-flex gap-2 flex-wrap">
                    <div class="d-flex flex-wrap gap-2 align-items-center">
                        <div class="align-items-center">
                            <small><label class="form-label" for="tipo">Tipo</label></small>
                            <select class="form-select form-select-sm" name="tipo" id="tipo">
                                <option value="0" {{ request('tipo') == 'tipo' ? 'selected' : '' }}>Todos
                                </option>
                                @foreach ($tipos as $tipo)
                                    <option value="{{ $tipo->id_tipo_notificacion }}"
                                        {{ request('tipo') == $tipo->id_tipo_notificacion ? 'selected' : '' }}>
                                        {{ $tipo->nombre }}
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="align-items-center">
                        <small><label class="form-label" for="fecha">Ver</label></small>
                        <select class="form-select form-select-sm" name="fecha" id="fecha">
                            <option value="todo" {{ request('fecha') == 'todo' ? 'selected' : '' }}>Todas</option>
                            <option value="rango" {{ request('fecha') == 'rango' ? 'selected' : '' }}>Buscar por rango
                            </option>
                        </select>
                    </div>
                    <div class="gap-2" id="fechas" style="display: none">
                        <div class="align-items-center">
                            <small><label class="form-label" for="desde">Inicio</label></small>
                            <input class="form-control form-control-sm" type="date" name="desde" id="desde"
                                value="{{ request('desde') ? request('desde') : date('Y-m-d') }}">
                        </div>
                        <div class="align-items-center">
                            <small><label class="form-label" for="hasta">Fin</label></small>
                            <input class="form-control form-control-sm" type="date" name="hasta" id="hasta"
                                value="{{ request('hasta') ? request('hasta') : date('Y-m-d') }}">
                        </div>
                    </div>
                </div>
            </form>
            <div class="d-flex gap-2">
                <button type="submit" form="search_form" id="btn_submit"
                    class="btn btn-primary d-flex align-items-center gap-1"><svg xmlns="http://www.w3.org/2000/svg"
                        width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        class="icon icon-tabler icons-tabler-outline icon-tabler-search">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <circle cx="10" cy="10" r="7" />
                        <line x1="21" y1="21" x2="15" y2="15" />
                    </svg>Buscar</button>
                @can('notificacion_crear')
                    <a href="{{ route('notificaciones.create') }}"
                        class="btn btn-primary d-flex align-items-center gap-1"><svg xmlns="http://www.w3.org/2000/svg"
                            width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            class="icon icon-tabler icons-tabler-outline icon-tabler-plus">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M12 5l0 14" />
                            <path d="M5 12l14 0" />
                        </svg>Nueva</a>
                @endcan
            </div>
        </div>
        <div class="rounded shadow mt-3 overflow-hidden">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">ID</th>
                            <th scope="col">Asunto</th>
                            <th scope="col">Tipo</th>
                            <th scope="col">Fecha de envío</th>
                            <th scope="col">Destinatarios</th>
                            <th scope="col">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $index = 1; ?>
                        @forelse ($notificaciones as $notificacion)
                            <tr>
                                <th class="align-middle" scope="row">{{ $index }}</th>
                                <?php $index++; ?>
                                <td class="align-middle">{{ $notificacion->id_notificacion }}</td>
                                <td class="align-middle text-truncate" style="min-width: 150px; max-width:250">
                                    {{ $notificacion->asunto }}</td>
                                <td class="align-middle"><span
                                        class="badge rounded-pill @if ($notificacion->tipo->id_tipo_notificacion == 1) text-bg-success @elseif ($notificacion->tipo->id_tipo_notificacion == 2) text-bg-info text-light @elseif ($notificacion->tipo->id_tipo_notificacion == 3) text-bg-warning
                                        @else
                                            text-bg-primary @endif">{{ $notificacion->tipo->nombre }}</span>
                                </td>
                                <td class="align-middle" style="min-width:200px;">
                                    {{ date('d/m/Y - H:i:s', strtotime($notificacion->created_at)) }}</td>
                                <td class="align-middle">
                                    <span class="badge rounded-pill bg-secondary">
                                        <p class="m-0">{{ $notificacion->destinatarios->count() }}</p>
                                    </span>
                                </td>
                                <td class="align-middle">
                                    @can('notificacion_detalles')
                                        <a href="{{ route('notificaciones.show', $notificacion->id_notificacion) }}"
                                            class="btn btn-sm btn-outline-primary"><svg xmlns="http://www.w3.org/2000/svg"
                                                width="24" height="24" viewBox="0 0 24 24" fill="none"
                                                stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                stroke-linejoin="round"
                                                class="icon icon-tabler icons-tabler-outline icon-tabler-eye">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                <path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" />
                                                <path
                                                    d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6" />
                                            </svg></a>
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">No hay notificaciones</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div>
        <!-- Paginación -->
        <nav aria-label="Page navigation example">
            <ul class="pagination justify-content-center mt-4">
                <!-- Enlace "Anterior" -->
                @if ($notificaciones->previousPageUrl())
                    <li class="page-item">
                        <a class="page-link"
                            href="{{ $notificaciones->previousPageUrl() . '&' . http_build_query(request()->except('page')) }}"
                            tabindex="-1" aria-disabled="true">Anterior</a>
                    </li>
                @else
                    <li class="page-item disabled">
                        <span class="page-link">Anterior</span>
                    </li>
                @endif

                <!-- Enlaces de páginas -->
                @foreach ($notificaciones->getUrlRange(1, $notificaciones->lastPage()) as $pagina => $url)
                    <li class="page-item {{ $notificaciones->currentPage() == $pagina ? 'active' : '' }}">
                        <a class="page-link"
                            href="{{ $url . '&' . http_build_query(request()->except('page')) }}">{{ $pagina }}</a>
                    </li>
                @endforeach

                <!-- Enlace "Siguiente" -->
                @if ($notificaciones->nextPageUrl())
                    <li class="page-item">
                        <a class="page-link"
                            href="{{ $notificaciones->nextPageUrl() . '&' . http_build_query(request()->except('page')) }}">Siguiente</a>
                    </li>
                @else
                    <li class="page-item disabled">
                        <span class="page-link">Siguiente</span>
                    </li>
                @endif
            </ul>
        </nav>
    </div>

    <script>
        const usuarios = @json($usuarios);

        const listUsers = document.getElementById('list_users');

        function filterUsers() {
            const input = document.getElementById('userInput');
            const filter = input.value.toLowerCase().trim();

            listUsers.innerHTML = '';
            const filteredUsers = usuarios.filter(user => user.nombre.toLowerCase().includes(filter) || user
                .apellido.toLowerCase().includes(filter) || user.email.toLowerCase().includes(
                    filter));
            console.log(filteredUsers);
            filteredUsers.forEach(user => {
                const item = document.createElement('button');
                item.type = "button"
                item.setAttribute('data-bs-dismiss', 'modal');
                item.classList.add('list-group-item', 'list-group-item-action');
                item.innerHTML = `
                    <div class="ms-2 me-auto">
                        <div class="fw-bold">${user.nombre} ${user.apellido}</div>
                        ${user.email}
                    </div>
                `;
                item.addEventListener('click', function() {
                    const input = document.getElementById('correo');
                    input.value = user.email;
                    document.getElementById('correo_view').value = user.email;
                    //$('#searchUserModal').modal('hide');
                });
                listUsers.appendChild(item);
            });
        }

        function clearCorreo() {
            document.getElementById('correo').value = '';
            document.getElementById('correo_view').value = '';
        }

        // Mostrar todos los usuarios al abrir el modal
        document.getElementById('searchUserModal').addEventListener('shown.bs.modal', () => {
            document.getElementById('userInput').value = '';
            filterUsers();
        });

        const dateContainer = document.getElementById('fechas');
        const sortAll = document.getElementById('fecha');
        if (sortAll.value === 'todo') {
            dateContainer.style.display = 'none';
        } else {
            dateContainer.style.display = 'flex';
        }
        sortAll.addEventListener('change', function() {
            if (this.value === 'todo') {
                dateContainer.style.display = 'none';
            } else {
                dateContainer.style.display = 'flex';
            }
        });
        document.getElementById('search_form').addEventListener('submit', function(e) {
            document.getElementById('btn_submit').style.disabled = true;
        });
    </script>
@endsection
