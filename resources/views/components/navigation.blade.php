<div class="navbar-nav navbar-dark w-100 accordion accordion-flush" id="accordionFlush{{ $data['device'] }}">
    <div class="d-none d-md-block p-2 text-white p-4 mb-4" id="navbarText">
        <h4 class="m-0">Farmacia Veterinaria ALVA</h4>
        <div class="navbar-text text-wrap">
            @if (Auth::check())
                Bienvenido, <span class="dropdown">
                    <button class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                        {{ Auth::user()->nombre }}
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item text-dark" href="/cambiar_contrasena">Cambiar contraseña</a>
                        </li>
                    </ul>
                </span>
            @else
                Bienvenido, Invitado
            @endif
        </div>
    </div>

    <div class="accordion-item">
        <h2 class="accordion-header">
            <button class="accordion-button collapsed text-white" type="button" data-bs-toggle="collapse"
                data-bs-target="#flush-collapseInicio" aria-expanded="false" aria-controls="flush-collapseInicio">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512">
                    <path
                        d="M575.8 255.5c0 18-15 32.1-32 32.1h-32l.7 160.2c0 2.7-.2 5.4-.5 8.1V472c0 22.1-17.9 40-40 40H456c-1.1 0-2.2 0-3.3-.1c-1.4 .1-2.8 .1-4.2 .1H416 392c-22.1 0-40-17.9-40-40V448 384c0-17.7-14.3-32-32-32H256c-17.7 0-32 14.3-32 32v64 24c0 22.1-17.9 40-40 40H160 128.1c-1.5 0-3-.1-4.5-.2c-1.2 .1-2.4 .2-3.6 .2H104c-22.1 0-40-17.9-40-40V360c0-.9 0-1.9 .1-2.8V287.6H32c-18 0-32-14-32-32.1c0-9 3-17 10-24L266.4 8c7-7 15-8 22-8s15 2 21 7L564.8 231.5c8 7 12 15 11 24z" />
                </svg> Inicio
            </button>
        </h2>
        <div id="flush-collapseInicio" class="accordion-collapse collapse"
            data-bs-parent="#accordionFlush{{ $data['device'] }}">
            <div class="accordion-body">

                <li class="nav-item">
                    <a class="nav-link @if (Route::currentRouteName() == '/') active @endif"
                        href="{{ url('') }}">Inicio</a>
                </li>
            </div>
        </div>
    </div>
    <div class="accordion-item">
        <h2 class="accordion-header">
            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                data-bs-target="#flush-collapseUsuarios" aria-expanded="false" aria-controls="flush-collapseUsuarios">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512">
                    <path
                        d="M224 256A128 128 0 1 0 224 0a128 128 0 1 0 0 256zm-45.7 48C79.8 304 0 383.8 0 482.3C0 498.7 13.3 512 29.7 512H418.3c16.4 0 29.7-13.3 29.7-29.7C448 383.8 368.2 304 269.7 304H178.3z" />
                </svg> Usuarios
            </button>
        </h2>
        <div id="flush-collapseUsuarios" class="accordion-collapse collapse"
            data-bs-parent="#accordionFlush{{ $data['device'] }}">
            <div class="accordion-body">

                @can('usuario_listar')
                    <li class="nav-item">
                        <a class="nav-link @if (Route::currentRouteName() == 'usuarios.index') active @endif"
                            href="{{ url('usuarios') }}">Usuarios</a>
                    </li>
                @endcan
                @can('proveedor_listar')
                    <li class="nav-item">
                        <a class="nav-link @if (Route::currentRouteName() == 'proveedores.index') active @endif"
                            href="{{ url('proveedores') }}">Proveedores</a>
                    </li>
                @endcan
                @can('socio_listar')
                    <li class="nav-item">
                        <a class="nav-link @if (Route::currentRouteName() == 'socios.index') active @endif"
                            href="{{ url('socios') }}">Socios</a>
                    </li>
                @endcan
                @can('rol_listar')
                    <li class="nav-item">
                        <a class="nav-link @if (Route::currentRouteName() == 'roles.index') active @endif"
                            href="{{ url('roles') }}">Roles</a>
                    </li>
                @endcan
                @can('permiso_listar')
                    <li class="nav-item">
                        <a class="nav-link @if (Route::currentRouteName() == 'permisos.index') active @endif"
                            href="{{ url('permisos') }}">Permisos</a>
                    </li>
                @endcan
            </div>
        </div>
    </div>
    <div class="accordion-item">
        <h2 class="accordion-header">
            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                data-bs-target="#flush-collapseProductos" aria-expanded="false" aria-controls="flush-collapseProductos">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="42px"
                    height="42px">
                    <path
                        d="M4 4h16a1 1 0 0 1 1 1v14a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V5a1 1 0 0 1 1-1zm1 2v12h14V6H5zm3 3h2v2H8V9zm6 0h2v2h-2V9zm-6 4h2v2H8v-2zm6 0h2v2h-2v-2z" />
                </svg> Productos
            </button>
        </h2>
        <div id="flush-collapseProductos" class="accordion-collapse collapse"
            data-bs-parent="#accordionFlush{{ $data['device'] }}">
            <div class="accordion-body">

                @can('producto_listar')
                    <li class="nav-item">
                        <a class="nav-link @if (Route::currentRouteName() == 'productos.index') active @endif"
                            href="{{ url('productos') }}">Productos</a>
                    </li>
                @endcan
                @can('producto_crear')
                    <li class="nav-item">
                        <a class="nav-link @if (Route::currentRouteName() == 'productos.index') active @endif"
                            href="{{ route('productos.create') }}">Crear Producto</a>
                    </li>
                @endcan
                @can('producto_verifi_stock')
                    <li class="nav-item">
                        <a class="nav-link @if (Route::currentRouteName() == 'productos.index') active @endif"
                            href="{{ route('productos.stock_minimo') }}">Verificar Stock @if ($cantidadProductosStock > 0)
                                <span class="badge badge-pill bg-danger ms-1">{{ $cantidadProductosStock }}</span>
                            @endif
                        </a>
                    </li>
                @endcan
                @can('producto_inventario')
                    <li class="nav-item">
                        <a class="nav-link @if (Route::currentRouteName() == 'productos.index') active @endif"
                            href="{{ route('productos.inventario') }}">Inventario</a>
                    </li>
                @endcan
            </div>
        </div>
    </div>
    <div class="accordion-item">
        <h2 class="accordion-header">
            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                data-bs-target="#flush-collapseCompras" aria-expanded="false" aria-controls="flush-collapseCompras">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512">
                    <path
                        d="M528.12 301.319l47.273-208C579.27 81.517 564.817 64 545.971 64H112L99.101 12.431A24 24 0 0 0 75.553 0H24C10.746 0 0 10.746 0 24v16c0 13.255 10.746 24 24 24h32l89.6 384H24c-13.255 0-24 10.745-24 24v16c0 13.255 10.746 24 24 24h455.422c11.324 0 20.939-7.932 23.479-18.99l47.986-192a24.002 24.002 0 0 0-22.765-29.69H135.821l-5.518-24h397.938c11.324 0 20.939-7.932 23.479-18.99zM192 464c0 26.51-21.49 48-48 48s-48-21.49-48-48 21.49-48 48-48 48 21.49 48 48zm288 0c0 26.51-21.49 48-48 48s-48-21.49-48-48 21.49-48 48-48 48 21.49 48 48z" />
                </svg> Compras
            </button>
        </h2>
        <div id="flush-collapseCompras" class="accordion-collapse collapse"
            data-bs-parent="#accordionFlush{{ $data['device'] }}">
            <div class="accordion-body">
                @can('compra_listar')
                    <li class="nav-item">
                        <a class="nav-link @if (Route::currentRouteName() == 'compras.index') active @endif"
                            href="{{ url('compras') }}">Compras</a>
                    </li>
                @endcan
                @can('compra_registrar')
                    <li class="nav-item">
                        <a class="nav-link @if (Route::currentRouteName() == 'compras.index') active @endif"
                            href="{{ route('compras.registrar') }}">Registrar compra</a>
                    </li>
                @endcan
                @can('compra_dashboard')
                    <li class="nav-item">
                        <a class="nav-link @if (Route::currentRouteName() == 'compras.index') active @endif"
                            href="{{ route('compras.dashboard') }}">Dashboard</a>
                    </li>
                @endcan
            </div>
        </div>
    </div>
    <div class="accordion-item">
        <h2 class="accordion-header">
            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                data-bs-target="#flush-collapseVentas" aria-expanded="false" aria-controls="flush-collapseReservas">
                <svg xmlns="http://www.w3.org/2000/svg" height="48" width="48" viewBox="0 0 24 24"
                    fill="currentColor" class="icon icon-tabler icons-tabler-filled icon-tabler-shopping-bag">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                    <path
                        d="M16 6v-1a4 4 0 0 0 -8 0v1h-2a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2 -2v-12a2 2 0 0 0 -2 -2h-2zM9 5a2 2 0 0 1 4 0v1h-4v-1zm-2 7a1 1 0 1 1 2 0a1 1 0 0 1 -2 0zm6 0a1 1 0 1 1 2 0a1 1 0 0 1 -2 0z" />
                </svg> Ventas
            </button>
        </h2>
        <div id="flush-collapseVentas" class="accordion-collapse collapse"
            data-bs-parent="#accordionFlush{{ $data['device'] }}">
            <div class="accordion-body">
                @can('venta_listar')
                <li class="nav-item">
                    <a class="nav-link @if (Route::currentRouteName() == 'ventas.index') active @endif"
                        href="{{ url('ventas') }}">Ventas</a>
                </li>
                @endcan
                @can('venta_registrar')
                    <li class="nav-item">
                        <a class="nav-link @if (Route::currentRouteName() == 'ventas.index') active @endif"
                            href="{{ route('ventas.registrar') }}">Registrar venta</a>
                    </li>
                @endcan
                @can('venta_dashboard')
                    <li class="nav-item">
                        <a class="nav-link @if (Route::currentRouteName() == 'ventas.index') active @endif"
                            href="{{ route('ventas.dashboard') }}">Dashboard</a>
                    </li>
                @endcan
            </div>
        </div>
    </div>
    @canany(['notificacion_listar', 'notificacion_crear', 'mensaje_listar', 'mensaje_crear'])
        <div class="accordion-item">
            <h2 class="accordion-header">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                    data-bs-target="#flush-collapseNotificacion" aria-expanded="false"
                    aria-controls="flush-collapseNotificacion">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round"
                        stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-bell">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path d="M10 5a2 2 0 1 1 4 0a7 7 0 0 1 4 6v3a4 4 0 0 0 2 3h-16a4 4 0 0 0 2 -3v-3a7 7 0 0 1 4 -6" />
                        <path d="M9 17v1a3 3 0 0 0 6 0v-1" />
                    </svg>Notificaciones @if ($cantidadMensajes > 0)
                        <span class="p-1 bg-danger border border-light rounded-circle ms-1">
                            <span class="visually-hidden">New alerts</span>
                        </span>
                    @endif
                </button>
            </h2>
            <div id="flush-collapseNotificacion" class="accordion-collapse collapse"
                data-bs-parent="#accordionFlush{{ $data['device'] }}">
                <div class="accordion-body">

                    @can('notificacion_listar')
                        <li class="nav-item">
                            <a class="nav-link @if (Route::currentRouteName() == 'notificaciones.index') active @endif"
                                href="{{ url('notificaciones') }}">Notificaciones</a>
                        </li>
                    @endcan
                    @can('notificacion_crear')
                        <li class="nav-item">
                            <a class="nav-link @if (Route::currentRouteName() == 'notificaciones.create') active @endif"
                                href="{{ url('notificaciones/create') }}">Nueva notificación</a>
                        </li>
                    @endcan
                    @can('mensaje_listar')
                        <li class="nav-item">
                            <a class="nav-link @if (Route::currentRouteName() == 'mensajes.index') active @endif"
                                href="{{ url('mensajes') }}">Mensajes @if ($cantidadMensajes > 0)
                                    <span class="badge badge-pill bg-danger ms-1">{{ $cantidadMensajes }}</span>
                                @endif
                            </a>
                        </li>
                    @endcan
                    @can('mensaje_crear')
                        <li class="nav-item">
                            <a class="nav-link @if (Route::currentRouteName() == 'mensajes.create') active @endif"
                                href="{{ url('mensajes/create') }}">Nuevo mensaje</a>
                        </li>
                    @endcan
                </div>
            </div>
        </div>
    @endcanany

    @if (Auth::check())
        <li class="nav-item text-white">
            <a class="nav-link text-white px-4" href="{{ route('users.logout') }}"><svg
                    xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"
                    style="width: 1rem; height: 1rem; fill: white;">
                    <path
                        d="M377.9 105.9L500.7 228.7c7.2 7.2 11.3 17.1 11.3 27.3s-4.1 20.1-11.3 27.3L377.9 406.1c-6.4 6.4-15 9.9-24 9.9c-18.7 0-33.9-15.2-33.9-33.9l0-62.1-128 0c-17.7 0-32-14.3-32-32l0-64c0-17.7 14.3-32 32-32l128 0 0-62.1c0-18.7 15.2-33.9 33.9-33.9c9 0 17.6 3.6 24 9.9zM160 96L96 96c-17.7 0-32 14.3-32 32l0 256c0 17.7 14.3 32 32 32l64 0c17.7 0 32 14.3 32 32s-14.3 32-32 32l-64 0c-53 0-96-43-96-96L0 128C0 75 43 32 96 32l64 0c17.7 0 32 14.3 32 32s-14.3 32-32 32z" />
                </svg> Salir</a>
        </li>
    @endif
</div>
