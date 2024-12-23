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
                <li class="breadcrumb-item active" aria-current="page">Productos</li>
                <li class="breadcrumb-item active" aria-current="page">Verificar stock</li>
            </ol>
        </nav>
        <h2 class="text-center mt-3">Productos con Stock menor al mínimo</h2>
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

    <div class="table-responsive mt-5">

        <table class="table">
            <thead>
                <tr>
                    <th scope="col" class="sticky-id bg-light align-middle">ID</th>
                    <th scope="col" class="bg-light align-middle">Código de Barra</th>
                    <th scope="col" class="bg-light">Nombre</th>
                    <th scope="col" class="bg-light">Stock actual</th>
                    <th scope="col" class="bg-light">Stock mínimo</th>
                    <th scope="col" class="bg-light">Mensaje</th>
                    <th scope="col" class="bg-light">Fecha</th>

                </tr>
            </thead>
            <tbody>
                @foreach ($productosMinimoStock as $producto)
                    <tr>
                        <td class="sticky-id bg-light">{{ $producto->id_producto }}</td>
                        <td>{{ $producto->codigo_barra }}</td>
                        <td>{{ $producto->nombre_producto }}</td>
                        <td>{{ $producto->stock }}</td>
                        <td>{{ $producto->stock_minimo }}</td>
                        <td>{{ $producto->mensaje }}</td>
                        <td>{{ $producto->fecha }}</td>
                    </tr>
                @endforeach
                @if ($productosMinimoStock->isEmpty())
                    <tr>
                        <td colspan="7" class="text-center">
                            <div class="my-2">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"
                                    style="width: 16px; height: 16px;"><!--!Font Awesome Free 6.5.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.-->
                                    <path
                                        d="M256 512A256 256 0 1 0 256 0a256 256 0 1 0 0 512zm0-384c13.3 0 24 10.7 24 24V264c0 13.3-10.7 24-24 24s-24-10.7-24-24V152c0-13.3 10.7-24 24-24zM224 352a32 32 0 1 1 64 0 32 32 0 1 1 -64 0z" />
                                </svg>
                                No hay productos con stock mínimo o bajo.
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
            @if ($productosMinimoStock->previousPageUrl())
                <li class="page-item">
                    <a class="page-link"
                        href="{{ $productosMinimoStock->previousPageUrl() . '&' . http_build_query(request()->except('page')) }}"
                        tabindex="-1" aria-disabled="true">Anterior</a>
                </li>
            @else
                <li class="page-item disabled">
                    <span class="page-link">Anterior</span>
                </li>
            @endif

            <!-- Enlaces de páginas -->
            @foreach ($productosMinimoStock->getUrlRange(1, $productosMinimoStock->lastPage()) as $pagina => $url)
                <li class="page-item {{ $productosMinimoStock->currentPage() == $pagina ? 'active' : '' }}">
                    <a class="page-link"
                        href="{{ $url . '&' . http_build_query(request()->except('page')) }}">{{ $pagina }}</a>
                </li>
            @endforeach

            <!-- Enlace "Siguiente" -->
            @if ($productosMinimoStock->nextPageUrl())
                <li class="page-item">
                    <a class="page-link"
                        href="{{ $productosMinimoStock->nextPageUrl() . '&' . http_build_query(request()->except('page')) }}">Siguiente</a>
                </li>
            @else
                <li class="page-item disabled">
                    <span class="page-link">Siguiente</span>
                </li>
            @endif
        </ul>
    </nav>
@endsection
