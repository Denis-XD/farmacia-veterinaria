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
                <li class="breadcrumb-item active" aria-current="page">Mensajes</li>
            </ol>
        </nav>
        <h2 class="text-center">Mensajes</h2>
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
            <form action="{{ route('mensajes.index') }}" id="search_form" method="GET">
                @csrf
                <div class="d-flex gap-2 flex-wrap">
                    <div class="d-flex flex-wrap gap-2 align-items-center">
                        <div class="align-items-center">
                            <small><label class="form-label" for="estado">Estado</label></small>
                            <select class="form-select form-select-sm" name="estado" id="estado">
                                <option value="0" {{ request('estado') == 'estado' ? 'selected' : '' }}>Todos
                                </option>
                                @foreach ($estados_mensaje as $estado)
                                    <option value="{{ $estado->id_estado_mensaje }}"
                                        {{ request('estado') == $estado->id_estado_mensaje ? 'selected' : '' }}>
                                        {{ $estado->nombre }}</option>
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
                @can('mensaje_crear')
                    <a href="{{ route('mensajes.create') }}" class="btn btn-primary d-flex align-items-center gap-1"><svg
                            xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-plus">
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
                            <th scope="col">Correo</th>
                            <th scope="col">Asunto</th>
                            <th scope="col">Estado</th>
                            <th scope="col">Fecha de envío</th>
                            <th scope="col">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $index = 1; ?>
                        @forelse ($mensajes as $mensaje)
                            <tr>
                                <th class="align-middle" scope="row">{{ $index }}</th>
                                <?php $index++; ?>
                                <td class="align-middle">{{ $mensaje->id_mensaje }}</td>
                                <td class="align-middle text-truncate" style="min-width: 150px; max-width:250">
                                    {{ $mensaje->usuario->email }}</td>
                                <td class="align-middle text-truncate" style="min-width: 150px; max-width:250">
                                    {{ $mensaje->asunto }}</td>
                                <td class="align-middle"><span
                                        class="badge rounded-pill @if ($mensaje->estado->id_estado_mensaje == 1) text-bg-warning text-light @elseif ($mensaje->estado->id_estado_mensaje == 2) text-bg-success @elseif ($mensaje->estado->id_estado_mensaje == 3) text-bg-danger
                                        @else
                                            text-bg-primary @endif">{{ $mensaje->estado->nombre }}</span>
                                </td>
                                <td class="align-middle" style="min-width:200px;">
                                    {{ date('d/m/Y - H:i:s', strtotime($mensaje->created_at)) }}</td>
                                <td class="align-middle">
                                    @can('mensaje_detalles')
                                        <a href="{{ route('mensajes.show', $mensaje->id_mensaje) }}"
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
                                <td colspan="7" class="text-center">No hay mensajes</td>
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
                @if ($mensajes->previousPageUrl())
                    <li class="page-item">
                        <a class="page-link"
                            href="{{ $mensajes->previousPageUrl() . '&' . http_build_query(request()->except('page')) }}"
                            tabindex="-1" aria-disabled="true">Anterior</a>
                    </li>
                @else
                    <li class="page-item disabled">
                        <span class="page-link">Anterior</span>
                    </li>
                @endif

                <!-- Enlaces de páginas -->
                @foreach ($mensajes->getUrlRange(1, $mensajes->lastPage()) as $pagina => $url)
                    <li class="page-item {{ $mensajes->currentPage() == $pagina ? 'active' : '' }}">
                        <a class="page-link"
                            href="{{ $url . '&' . http_build_query(request()->except('page')) }}">{{ $pagina }}</a>
                    </li>
                @endforeach

                <!-- Enlace "Siguiente" -->
                @if ($mensajes->nextPageUrl())
                    <li class="page-item">
                        <a class="page-link"
                            href="{{ $mensajes->nextPageUrl() . '&' . http_build_query(request()->except('page')) }}">Siguiente</a>
                    </li>
                @else
                    <li class="page-item disabled">
                        <span class="page-link">Siguiente</span>
                    </li>
                @endif
            </ul>
        </nav>
    </div>
    @can('enviar_mensaje_correo')
        <div>
            <span>¿Su mensaje no fue atendido? Póngase en contacto con un administrador mediante correo electrónico: </span><a
                href="mailto:reservafacil@gmail.com" target="_blank">aquí</a>
        </div>
    @endcan

    <script>
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
