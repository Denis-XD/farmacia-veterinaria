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
            <li class="breadcrumb-item"><a href="{{ route('ventas.index') }}">Ventas</a></li>
            <li class="breadcrumb-item active" aria-current="page">Editar Venta</li>
        </ol>
    </nav>
    <h2 class="text-center">Editar Venta ID: {{ $venta->id_venta }}</h2>
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

    <div class="container mt-4">


        <div class="row mb-4 align-items-start">
            <!-- Buscar Socio -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">Buscar Socio (Opcional)</div>
                    <div class="card-body">
                        <div class="input-group mb-3">
                            <input type="text" id="buscarSocio" class="form-control"
                                placeholder="Buscar por ID, Nombre o Celular">
                        </div>
                        <div id="resultadoSocio" class="table-responsive">
                            <table class="table table-bordered table-hover d-none" id="tablaSocios">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Nombre</th>
                                        <th>Celular</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Selección de Socio y Encargado -->
            <div class="col-md-6">
                <!-- Encargado -->
                <div class="mb-3">
                    <label for="id_usuario" class="form-label">Encargado</label>
                    <input type="text" class="form-control" value="{{ $venta->usuario->nombre }}" disabled>
                </div>

                <!-- Socio Seleccionado -->
                <div id="socioSeleccionado" class="{{ $venta->socio ? '' : 'd-none' }}">
                    <h5>Socio Seleccionado:</h5>
                    <div class="alert alert-info d-flex justify-content-between align-items-center">
                        <span id="nombreSocio">{{ $venta->socio->nombre_socio ?? '' }}</span>
                        <button class="btn btn-danger btn-sm" id="quitarSocio">Quitar</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Buscador de productos -->
        <div class="card mb-4">
            <div class="card-header">Buscar Productos</div>
            <div class="card-body">
                <div class="input-group mb-3">
                    <input type="text" id="buscarProducto" class="form-control"
                        placeholder="Buscar por ID, Código de Barra o Nombre">
                </div>
                <div id="resultadoProducto" class="table-responsive">
                    <table class="table table-bordered table-hover d-none" id="tablaProductos">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Código de Barra</th>
                                <th>Nombre</th>
                                <th>Stock</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>


        <!-- Productos seleccionados -->
        <div id="productosSeleccionados" class="mb-4">
            <h5>Productos Seleccionados:</h5>
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Precio Unitario</th>
                            <th>Cantidad</th>
                            <th>Subtotal</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="listaProductos"></tbody>
                </table>
            </div>
        </div>

        <!-- Tabla de pagos -->
        <div id="pagosRegistrados" class="mb-4">
            <h5>Pagos Registrados:</h5>
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Monto Pagado</th>
                            <th>Saldo Pendiente</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="listaPagos"></tbody>
                </table>
            </div>
        </div>

        <!-- Opciones de Venta -->
        <div class="row mb-4">
            <div class="col-md-6">
                <label for="monto_pagado" class="form-label">Monto Pagado:</label>
                <input type="number" id="monto_pagado" name="monto_pagado" class="form-control" min="0"
                    step="0.01">
            </div>
            <div class="col-md-6">
                <label for="saldo_pendiente" class="form-label">Saldo Pendiente:</label>
                <input type="number" id="saldo_pendiente" name="saldo_pendiente" class="form-control" min="0"
                    step="0.01">
            </div>
        </div>

        <!-- Opciones adicionales -->
        <div class="row mb-4">
            <div class="col-md-4">
                <label for="credito" class="form-label">¿Venta a Crédito?</label>
                <select name="credito" id="credito" class="form-select">
                    <option value="0" {{ $venta->credito == 0 ? 'selected' : '' }}>No</option>
                    <option value="1" {{ $venta->credito == 1 ? 'selected' : '' }}>Sí</option>
                </select>
            </div>
            <div class="col-md-4">
                <label for="servicio" class="form-label">¿Incluye Servicio?</label>
                <select name="servicio" id="servicio" class="form-select">
                    <option value="0" {{ $venta->servicio == 0 ? 'selected' : '' }}>No</option>
                    <option value="1" {{ $venta->servicio == 1 ? 'selected' : '' }}>Sí</option>
                </select>
            </div>
            <div class="col-md-4">
                <label for="finalizada" class="form-label">¿Venta Finalizada?</label>
                <select name="finalizada" id="finalizada" class="form-select">
                    <option value="1" {{ $venta->finalizada == 1 ? 'selected' : '' }}>Sí</option>
                    <option value="0" {{ $venta->finalizada == 0 ? 'selected' : '' }}>No</option>
                </select>
            </div>
        </div>

        <!-- Descripción -->
        <div class="row mb-4">
            <div class="col-12">
                <label for="descripcion" class="form-label">Descripción (Opcional):</label>
                <textarea id="descripcion" name="descripcion" class="form-control" rows="1"
                    placeholder="Ingrese una descripción (opcional)...">{{ $venta->descripcion ?? '' }}</textarea>
            </div>
        </div>

        <div class="row mb-4 align-items-center">
            <!-- Porcentaje de Descuento -->
            <div class="col-12 col-md-auto d-flex align-items-center gap-2 mb-2 mb-md-0">
                <label for="porcentaje_descuento" class="form-label mb-0">Porcentaje de Descuento:</label>
                <input type="number" id="porcentaje_descuento" class="form-control"
                    value="{{ $venta->descuento_venta }}" min="0" max="100" step="1"
                    style="max-width: 80px;">
            </div>

            <!-- Total de la Venta -->
            <div class="col-12 col-md-auto d-flex align-items-center gap-2 mb-2 mb-md-0">
                <label for="totalVentaInput" class="form-label mb-0">Total de la Venta: Bs</label>
                <input type="number" id="totalVentaInput" name="total_venta" class="form-control text-end"
                    style="max-width: 120px;" min="0" step="0.01" value="{{ $venta->total_venta }}">
            </div>

            <!-- Fecha y Hora de la Venta -->
            <div class="col-12 col-md-auto d-flex align-items-center gap-2 mb-2 mb-md-0">
                <label for="fechaHoraVenta" class="form-label mb-0">Fecha y Hora:</label>
                <input type="datetime-local" id="fechaHoraVenta" name="fecha_hora_venta" class="form-control"
                    style="max-width: 220px;"
                    value="{{ $venta->fecha_venta ? $venta->fecha_venta->format('Y-m-d\TH:i') : '' }}">
            </div>

            <!-- Botón Finalizar Venta -->
            <div class="col-12 col-md-auto ms-md-auto">
                <button class="btn btn-primary" id="actualizarVenta">Guardar Cambios</button>
                <button type="button" id="btn-cancelar" class="btn btn-secondary">Cancelar</button>
            </div>
        </div>

        <!-- Modal Confirmación -->
        <div class="modal fade" id="confirmarVentaModal" tabindex="-1" aria-labelledby="confirmarVentaModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="confirmarVentaModalLabel">Confirmar Cambios</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        ¿Estás seguro de que deseas guardar los cambios de la venta con ID:
                        <strong>{{ $venta->id_venta }}</strong>?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="button" class="btn btn-success" id="confirmarCambiosVenta">Confirmar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <style>
        /* Estilos básicos para las notificaciones */
        .notification {
            position: fixed;
            bottom: -100px;
            /* Empieza fuera de la pantalla */
            left: 50%;
            transform: translateX(-50%);
            background-color: #f8d7da;
            /* Rojo por defecto */
            color: #721c24;
            border: 1px solid #f5c6cb;
            border-radius: 5px;
            padding: 15px 20px;
            font-size: 14px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2);
            z-index: 1000;
            opacity: 0;
            /* Oculto por defecto */
            transition: all 0.4s ease;
            /* Transición suave */
        }

        /* Notificación exitosa */
        .notification.success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        /* Notificación visible */
        .notification.show {
            bottom: 20px;
            /* Aparece en la parte inferior */
            opacity: 1;
            /* Se hace visible */
        }

        @media (max-width: 576px) {

            table th,
            table td {
                font-size: 12px;
                /* Reducir tamaño de texto */
            }

            table th,
            table td {
                white-space: nowrap;
                /* Evitar que el texto salte a la siguiente línea */
            }

            .btn-sm {
                font-size: 10px;
                padding: 0.25rem 0.5rem;
                /* Botones más pequeños para adaptarse a móviles */
            }
        }

        @media (max-width: 768px) {
            .d-flex.align-items-center.flex-wrap {
                justify-content: flex-start;
            }

            .d-flex.align-items-center.flex-wrap>button {
                flex-basis: 100%;
                max-width: 100%;
            }
        }
    </style>
    <script>
        let socioSeleccionado = @json($venta->socio);
        let idDetallesProductosEliminados = [];
        let productosSeleccionados = {!! json_encode(
            $venta->detalles->map(function ($detalle) {
                    return [
                        'id_detalle_venta' => $detalle->id_detalle_venta,
                        'id_producto' => $detalle->producto->id_producto,
                        'nombre_producto' => $detalle->producto->nombre_producto,
                        'precio_venta_actual' => $detalle->producto->precio_venta_actual,
                        'cantidad' => $detalle->cantidad_venta,
                        'cantidadOriginal' => $detalle->cantidad_venta, // Guardamos la cantidad original para comparación
                        'subtotal' => $detalle->subtotal_venta,
                        'esExistente' => true,
                        'modificado' => false,
                    ];
                })->toArray(),
        ) !!};
        let pagosRegistrados = {!! json_encode(
            $venta->pagos->map(function ($pago) {
                    return [
                        'id_pago' => $pago->id_pago,
                        'fecha_pago' => $pago->fecha_pago->format('Y-m-d\TH:i'),
                        'monto_pagado' => $pago->monto_pagado,
                        'saldo_pendiente' => $pago->saldo_pendiente,
                    ];
                })->toArray(),
        ) !!};

        let idPagosEliminados = [];


        const idVenta = {{ $venta->id_venta }};
        const socios = @json($socios);
        const productos = @json($productos);

        document.addEventListener('DOMContentLoaded', () => {
            const buscarProductoInput = document.getElementById('buscarProducto');
            const buscarSocioInput = document.getElementById('buscarSocio');
            const totalVentaInput = document.getElementById('totalVentaInput');
            const montoPagadoInput = document.getElementById('monto_pagado');
            const saldoPendienteInput = document.getElementById('saldo_pendiente');
            const porcentajeDescuentoInput = document.getElementById('porcentaje_descuento');
            const tabla = document.getElementById('tablaProductos');
            const tbody = tabla.querySelector('tbody');

            renderProductos();
            renderPagos();

            totalVentaInput.value = {{ $venta->total_venta }};

            // Buscar proveedores dinámicamente
            buscarSocioInput.addEventListener("input", function() {
                const buscar = this.value.toLowerCase().trim();
                const resultados = buscar ?
                    socios.filter(
                        (socio) =>
                        socio.id_socio.toString().includes(buscar) ||
                        (socio.celular_socio && socio.celular_socio.toString().includes(buscar)) ||
                        socio.nombre_socio.toLowerCase().includes(buscar)
                    ) : [];
                renderSocios(resultados);
            });

            function renderSocios(resultados) {
                const tabla = document.getElementById("tablaSocios");
                const tbody = tabla.querySelector("tbody");
                tbody.innerHTML = "";

                if (resultados.length > 0) {
                    tabla.classList.remove("d-none");
                    resultados.forEach((socio) => {
                        tbody.innerHTML += `
                    <tr>
                        <td>${socio.id_socio}</td>
                        <td>${socio.nombre_socio}</td>
                        <td>${socio.celular_socio || "N/A"}</td>
                        <td>
                            <button class="btn btn-success btn-sm btn-choose-proveedor" data-id="${socio.id_socio}">
                                Añadir
                            </button>
                        </td>
                    </tr>`;
                    });
                } else {
                    tabla.classList.add("d-none");
                    //showNotification('No se encontraron socios.', 'danger');
                }
            }

            // Seleccionar proveedor
            document.addEventListener("click", function(event) {
                if (event.target.classList.contains("btn-choose-proveedor")) {
                    const idSocio = event.target.dataset.id;
                    const socio = socios.find((s) => s.id_socio == idSocio);

                    socioSeleccionado = socio;
                    document.getElementById("socioSeleccionado").classList.remove("d-none");
                    document.getElementById("nombreSocio").innerText = socio.nombre_socio;
                }
            });

            document.addEventListener("click", function(event) {
                if (event.target.id === "quitarSocio") {
                    socioSeleccionado = null;
                    document.getElementById("socioSeleccionado").classList.add("d-none");
                    document.getElementById("nombreSocio").innerText = "";
                }
            });

            // Búsqueda dinámica mientras se escribe
            buscarProductoInput.addEventListener('input', () => {
                const buscar = buscarProductoInput.value.toLowerCase().trim();

                if (!buscar) {
                    tabla.classList.add('d-none');
                    tbody.innerHTML = '';
                    return;
                }

                const resultados = productos.filter(producto =>
                    producto.id_producto.toString().includes(buscar) ||
                    (producto.codigo_barra && producto.codigo_barra.toLowerCase().includes(buscar)) ||
                    producto.nombre_producto.toLowerCase().includes(buscar)
                );

                tbody.innerHTML = '';
                if (resultados.length > 0) {
                    // Si hay un único resultado y el input proviene de un código de barras (numérico)
                    if (resultados.length === 1) {
                        const producto = resultados[0];
                        if (!productosSeleccionados.some(p => p.id_producto === producto.id_producto)) {
                            añadirProducto(producto); // Añade el producto automáticamente
                        } else {
                            showNotification('El producto ya está añadido.', 'danger');
                        }

                        // Limpia el campo de búsqueda con un pequeño retardo
                        setTimeout(() => {
                            buscarProductoInput.value =
                                ''; // Limpia el campo de búsqueda completamente
                        }, 10);

                        tabla.classList.add('d-none');
                        tbody.innerHTML = '';
                        return; // Termina la función aquí
                    }

                    // Si hay más de un resultado, muestra la tabla
                    tabla.classList.remove('d-none');
                    resultados.forEach(producto => {
                        tbody.innerHTML += `
                            <tr>
                                <td>${producto.id_producto}</td>
                                <td>${producto.codigo_barra || 'N/A'}</td>
                                <td>${producto.nombre_producto}</td>
                                <td>${producto.stock}</td>
                                <td>
                                    <button class="btn btn-success btn-sm btn-choose-producto" 
                                        data-id="${producto.id_producto}">Añadir</button>
                                </td>
                            </tr>`;
                    });
                } else {
                    tabla.classList.add('d-none');
                }
            });

            // Manejar clic para añadir productos manualmente
            document.addEventListener('click', (event) => {
                if (event.target.classList.contains('btn-choose-producto')) {
                    const idProducto = parseInt(event.target.getAttribute('data-id'));
                    const producto = productos.find(p => p.id_producto === idProducto);

                    if (!producto) {
                        showNotification('Producto no encontrado.', 'danger');
                        return;
                    }

                    añadirProducto(producto);
                    setTimeout(() => {
                        buscarProductoInput.value =
                            ''; // Limpia el campo después de añadir desde la tabla
                    }, 10);
                }
            });

            function añadirProducto(producto) {
                if (productosSeleccionados.some(p => p.id_producto === producto.id_producto)) {
                    showNotification('El producto ya está añadido.', 'danger');
                    return;
                }

                if (producto.stock <= 0) {
                    showNotification('El producto no tiene stock disponible.', 'danger');
                    return;
                }

                producto.cantidad = 1;
                producto.subtotal = (producto.cantidad * producto.precio_venta_actual).toFixed(2);
                producto.esExistente = false;
                productosSeleccionados.push(producto);
                renderProductos();
            }

            function renderProductos() {
                const tbody = document.getElementById('listaProductos');
                tbody.innerHTML = '';

                productosSeleccionados.forEach((producto, index) => {
                    tbody.innerHTML += `
                        <tr>
                            <td>${producto.nombre_producto}</td>
                            <td>Bs ${producto.precio_venta_actual}</td>
                            <td>
                                <input type="number" class="form-control cantidad" value="${producto.cantidad}" 
                                    step="0.01" 
                                    data-index="${index}" onfocus="this.select()">
                            </td>
                            <td>
                                <input type="number" class="form-control subtotal" value="${producto.subtotal}"
                                    step="0.01" data-index="${index}">
                            </td>
                            <td><button class="btn btn-danger btn-sm btn-quitar-producto" data-index="${index}">Quitar</button></td>
                        </tr>`;
                });

                actualizarTotal();
            }

            // Validar y actualizar cantidad en tiempo real
            document.addEventListener('input', (event) => {
                if (event.target.classList.contains('cantidad')) {
                    const index = event.target.getAttribute('data-index');
                    const cantidadInput = event.target;
                    const cantidad = parseFloat(cantidadInput.value);
                    const producto = productosSeleccionados[index];

                    if (isNaN(cantidad) || cantidad <= 0 || cantidad > producto.stock) {
                        cantidadInput.classList.add('is-invalid');
                    } else {
                        cantidadInput.classList.remove('is-invalid');
                        producto.cantidad = cantidad;
                        producto.subtotal = producto.cantidad * producto.precio_venta_actual;

                        if (producto.esExistente) {
                            if (cantidad !== producto.cantidadOriginal) {
                                producto.modificado = true; // Marcamos como modificado
                            } else {
                                producto.modificado =
                                    false; // Volvemos a false si la cantidad es la original
                            }
                        }

                        actualizarSubtotal(index, producto.subtotal);
                        actualizarTotal();
                    }
                }

                if (event.target.classList.contains('subtotal')) {
                    const index = event.target.getAttribute('data-index');
                    const subtotalInput = event.target;
                    const subtotal = parseFloat(subtotalInput.value);
                    const producto = productosSeleccionados[index];

                    if (isNaN(subtotal) || subtotal < 0) {
                        subtotalInput.classList.add('is-invalid');
                    } else {
                        subtotalInput.classList.remove('is-invalid');
                        producto.subtotal = subtotal;
                        producto.modificado = true;
                        actualizarTotal(); // Mantiene la cantidad fija
                    }
                }

                // Validar y aplicar porcentaje de descuento
                if (event.target.id === 'porcentaje_descuento') {
                    const porcentajeDescuento = parseFloat(porcentajeDescuentoInput.value);
                    if (isNaN(porcentajeDescuento) || porcentajeDescuento < 0 || porcentajeDescuento >
                        100) {
                        porcentajeDescuentoInput.classList.add('is-invalid');

                    } else {
                        porcentajeDescuentoInput.classList.remove('is-invalid');
                        actualizarTotal();
                    }
                }
            });

            // Actualizar "Saldo Pendiente" en tiempo real cuando cambia "Total de la Venta"
            totalVentaInput.addEventListener('input', () => {
                const total = parseFloat(totalVentaInput.value) || 0;
                const montoPagado = parseFloat(montoPagadoInput.value) || 0;
                saldoPendienteInput.value = (total - montoPagado).toFixed(2);
            });

            // Actualizar "Saldo Pendiente" en tiempo real cuando cambia "Monto Pagado"
            montoPagadoInput.addEventListener('input', () => {
                const total = parseFloat(totalVentaInput.value) || 0;
                const montoPagado = parseFloat(montoPagadoInput.value) || 0;
                saldoPendienteInput.value = (total - montoPagado).toFixed(2);
            });

            // Quitar producto
            document.addEventListener('click', (event) => {
                if (event.target.classList.contains('btn-quitar-producto')) {
                    const index = event.target.getAttribute('data-index');
                    const producto = productosSeleccionados[index];

                    if (producto.esExistente) {
                        idDetallesProductosEliminados.push(producto.id_detalle_venta);
                    }
                    productosSeleccionados.splice(index, 1);
                    renderProductos();
                }
            });

            function actualizarTotal() {
                const totalSinDescuento = productosSeleccionados.reduce((sum, producto) => sum + parseFloat(producto
                        .subtotal),
                    0);
                const porcentajeDescuento = parseFloat(porcentajeDescuentoInput.value) || 0;

                if (porcentajeDescuento >= 0 && porcentajeDescuento <= 100) {
                    const descuento = (totalSinDescuento * porcentajeDescuento) / 100;
                    const totalConDescuento = totalSinDescuento - descuento;

                    totalVentaInput.value = totalConDescuento.toFixed(2);
                    if (pagosRegistrados.length > 0) {
                        // Ordenar pagos por fecha para obtener el más reciente
                        const pagosOrdenados = pagosRegistrados.sort((a, b) => new Date(b.fecha_pago) - new Date(a
                            .fecha_pago));
                        const ultimoPago = pagosOrdenados[0];
                        const saldoPendiente = parseFloat(ultimoPago.saldo_pendiente); // Asegurar que es un número

                        if (!isNaN(saldoPendiente)) {
                            saldoPendienteInput.value = saldoPendiente.toFixed(2);
                        } else {
                            saldoPendienteInput.value = "20.00"; // Valor predeterminado en caso de error
                        }
                    } else {
                        // Si no hay pagos, el saldo pendiente se basa en el total con descuento
                        saldoPendienteInput.value = (totalConDescuento - parseFloat(montoPagadoInput.value || 0))
                            .toFixed(2);
                    }
                }
            }

            function actualizarSubtotal(index, subtotal) {
                const producto = productosSeleccionados[index];
                producto.subtotal = parseFloat(subtotal).toFixed(2);

                const subtotalInput = document.querySelector(`.subtotal[data-index="${index}"]`);
                if (subtotalInput) {
                    subtotalInput.value = subtotal.toFixed(2);
                }
            }

            function renderPagos() {
                const tbody = document.getElementById('listaPagos');
                tbody.innerHTML = ''; // Limpia la tabla

                pagosRegistrados.forEach((pago, index) => {
                    tbody.innerHTML += `
                        <tr>
                            <td>
                                <input type="datetime-local" value="${pago.fecha_pago}" class="form-control fecha-pago" data-index="${index}">
                            </td>
                            <td>
                                <input type="number" value="${pago.monto_pagado}" class="form-control monto-pagado" data-index="${index}" min="1">
                            </td>
                            <td>
                                <input type="number" value="${pago.saldo_pendiente}" class="form-control saldo-pendiente" data-index="${index}" min="0">
                            </td>
                            <td>
                                <button class="btn btn-danger btn-sm btn-quitar-pago" data-id="${pago.id_pago}">Quitar</button>
                            </td>
                        </tr>`;
                });
            }

            document.addEventListener('input', (event) => {
                if (event.target.classList.contains('fecha-pago')) {
                    const index = event.target.getAttribute('data-index');
                    pagosRegistrados[index].fecha_pago = event.target.value;
                }
                if (event.target.classList.contains('monto-pagado')) {
                    const index = event.target.getAttribute('data-index');
                    const valor = parseFloat(event.target.value);
                    if (valor >= 1) {
                        pagosRegistrados[index].monto_pagado = valor;
                        event.target.classList.remove('is-invalid');
                    } else {
                        event.target.classList.add('is-invalid');
                    }
                }
                if (event.target.classList.contains('saldo-pendiente')) {
                    const index = event.target.getAttribute('data-index');
                    const valor = parseFloat(event.target.value);
                    if (valor >= 0) {
                        pagosRegistrados[index].saldo_pendiente = valor;
                        event.target.classList.remove('is-invalid');
                    } else {
                        event.target.classList.add('is-invalid');
                    }
                }
            });

            document.addEventListener('click', (event) => {
                if (event.target.classList.contains('btn-quitar-pago')) {
                    const idPago = event.target.getAttribute('data-id');
                    if (idPago) {
                        idPagosEliminados.push(parseInt(idPago));
                    }
                    // Elimina el pago de la lista
                    pagosRegistrados = pagosRegistrados.filter(pago => pago.id_pago != idPago);
                    renderPagos();
                    actualizarTotal();
                }
            });

            function validarPagos() {
                for (const pago of pagosRegistrados) {
                    if (!pago.fecha_pago || pago.monto_pagado < 0 || pago.saldo_pendiente < 0) {
                        showNotification('Todos los campos de los pagos deben ser válidos.', 'danger');
                        return false;
                    }
                }
                return true;
            }

            function showNotification(message, type) {
                const notification = document.createElement('div');
                notification.className = `notification ${type}`;
                notification.innerText = message;

                document.body.appendChild(notification);

                setTimeout(() => {
                    notification.classList.add('show');
                }, 10);

                setTimeout(() => {
                    notification.classList.remove('show');
                    notification.addEventListener('transitionend', () => notification.remove());
                }, 3000);
            }

            document.getElementById('btn-cancelar').addEventListener('click', () => {
                window.location.reload();
            });

            // Finalizar venta
            document.getElementById('actualizarVenta').addEventListener('click', () => {
                const modal = new bootstrap.Modal(document.getElementById('confirmarVentaModal'));
                modal.show();
            });

            document.getElementById('confirmarCambiosVenta').addEventListener('click', async () => {
                const modalElement = document.getElementById('confirmarVentaModal');
                const modalInstance = bootstrap.Modal.getInstance(modalElement);

                if (modalInstance) {
                    modalInstance.hide(); // Oculta el modal
                }

                if (!validarPagos()) return;

                if (productosSeleccionados.length === 0) {
                    showNotification('Debe seleccionar al menos un producto.', 'danger');
                    return;
                }

                const data = {
                    id_socio: socioSeleccionado ? socioSeleccionado.id_socio : null,
                    total_venta: parseFloat(totalVentaInput.value) || 0,
                    descuento_venta: parseFloat(porcentajeDescuentoInput.value) || 0,
                    fecha_venta: document.getElementById('fechaHoraVenta').value || null,
                    monto_pagado: parseFloat(montoPagadoInput.value) || 0,
                    saldo_pendiente: parseFloat(saldoPendienteInput.value) || 0,
                    descripcion: document.getElementById('descripcion').value || null,
                    credito: parseInt(document.getElementById('credito').value) || 0,
                    servicio: parseInt(document.getElementById('servicio').value) || 0,
                    finalizada: parseInt(document.getElementById('finalizada').value) || 0,
                    productosEliminados: idDetallesProductosEliminados,
                    productos: productosSeleccionados.map(producto => ({
                        id: producto.id_producto,
                        cantidad: producto.cantidad,
                        subtotal: producto.subtotal,
                        descripcion: producto.descripcion || null,
                        esExistente: producto.esExistente,
                        modificado: producto.modificado || false
                    })),
                    pagos: pagosRegistrados,
                    pagosEliminados: idPagosEliminados,
                };

                try {
                    const response = await fetch(`/ventas/${idVenta}`, {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                .getAttribute('content'),
                        },
                        body: JSON.stringify(data),
                    });

                    const result = await response.json();
                    if (result.success) {
                        showNotification(result.message, 'success');
                        setTimeout(() => {
                            window.location.reload();
                        }, 3000);
                    } else {
                        showNotification(result.message || 'Error al actualizar la venta.', 'danger');
                    }
                } catch (error) {
                    console.error('Error:', error);
                    showNotification('Hubo un error al actualizar la venta.', 'danger');
                }
            });
        });
    </script>
@endsection
