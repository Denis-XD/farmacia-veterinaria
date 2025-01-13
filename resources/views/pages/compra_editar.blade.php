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
            <li class="breadcrumb-item"><a href="{{ route('compras.index') }}">Compras</a></li>
            <li class="breadcrumb-item active" aria-current="page">Editar Compra</li>
        </ol>
    </nav>
    <h2 class="text-center">Editar Compra ID: {{ $compra->id_compra }}</h2>
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert" id='cajaOkey'>
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
    <div class="container mt-4">

        <div class="card mb-4">
            <div class="card-header">Buscar Proveedor</div>
            <div class="card-body">
                <div class="input-group mb-3">
                    <input type="text" id="buscarProveedor" class="form-control" placeholder="Buscar por ID o Nombre">
                </div>
                <div id="resultadoProveedor" class="table-responsive">
                    <table class="table table-bordered table-hover d-none" id="tablaProveedores">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Dirección</th>
                                <th>Celular</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Proveedor Seleccionado -->
        <div id="proveedorSeleccionado" class="mb-4">
            <h5>Proveedor Seleccionado:</h5>
            <div class="alert alert-info d-flex justify-content-between align-items-center">
                <span id="nombreProveedor">{{ $compra->proveedor->nombre_proveedor }}</span>
            </div>
        </div>

        <!-- Buscar Productos -->
        <div class="card mb-4">
            <div class="card-header">Buscar Productos</div>
            <div class="card-body">
                <div class="input-group mb-3">
                    <input type="text" id="buscarProducto" class="form-control"
                        placeholder="Buscar por ID, Código de Barra o Nombre">
                </div>
                <div id="resultadoProducto">
                    <div class="table-responsive">
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
        </div>

        <!-- Productos Seleccionados -->
        <div id="productosSeleccionados" class="mb-4">
            <h5>Productos Seleccionados:</h5>
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Precio Compra</th>
                            <th>Cantidad</th>
                            <th>Subtotal</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="listaProductos"></tbody>
                </table>
            </div>
        </div>

        <!-- Opciones de Factura y Resumen -->
        <div class="row mb-4 align-items-center">
            <div class="col-auto d-flex align-items-center">
                <label for="porcentajeDescuento" class="form-label mb-0 me-2">Porcentaje de Descuento:</label>
                <input type="number" id="porcentajeDescuento" class="form-control" value="{{ $compra->descuento_compra }}"
                    min="0" max="100" step="1">
            </div>
            <div class="col-auto d-flex align-items-center ms-3">
                <label for="fechaCompra" class="form-label mb-0 me-2">Fecha de Compra:</label>
                <input type="date" id="fechaCompra" class="form-control"
                    value="{{ $compra->fecha_compra->format('Y-m-d') }}">
            </div>
            <div class="col-auto d-flex align-items-center ms-3">

                <label class="form-check-label mb-0  me-3" for="compraConFactura">¿Compra con factura?</label>
                <input type="checkbox" class="form-check-input me-2" id="compraConFactura"
                    {{ $compra->factura_compra ? 'checked' : '' }}>
            </div>
        </div>

        <div class="d-flex flex-column flex-md-row align-items-center flex-wrap gap-2 justify-content-between">
            <label for="totalCompraInput" class="form-label mb-0">Total de la Compra: Bs</label>
            <input type="number" id="totalCompraInput" class="form-control text-end" style="max-width: 120px;"
                min="0" step="0.01" value="{{ $compra->total_compra }}">

            <div class="ms-auto d-flex gap-2 mt-3 mt-md-0 flex-wrap justify-content-end">
                <button class="btn btn-primary" id="actualizarCompra">Guardar Cambios</button>
                <button type="button" id="btn-cancelar" class="btn btn-secondary">Cancelar</button>
            </div>
        </div>

        <!-- Modal Confirmación -->
        <div class="modal fade" id="confirmarCompraModal" tabindex="-1" aria-labelledby="confirmarCompraModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="confirmarCompraModalLabel">Confirmar Cambios</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        ¿Estás seguro de que deseas guardar los cambios de la compra con ID:
                        <strong>{{ $compra->id_compra }}</strong>?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="button" class="btn btn-success" id="confirmarCambiosCompra">Confirmar</button>
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
            .d-flex.gap-2.justify-content-between {
                flex-direction: column;
                align-items: flex-start;
            }

            .d-flex.gap-2.justify-content-between .ms-auto {
                align-self: stretch;
                width: 100%;
            }

            .d-flex.gap-2.justify-content-between button {
                width: 100%;
            }
        }
    </style>

    <script>
        let proveedorSeleccionado = @json($compra->proveedor);
        let idDetallesProductosEliminados = [];
        let productosSeleccionados = {!! json_encode(
            $compra->detalles->map(function ($detalle) {
                    return [
                        'id_detalle_compra' => $detalle->id_detalle_compra,
                        'id_producto' => $detalle->producto->id_producto,
                        'nombre_producto' => $detalle->producto->nombre_producto,
                        'precio_compra_actual' => $detalle->producto->precio_compra_actual,
                        'cantidad' => $detalle->cantidad_compra,
                        'cantidadOriginal' => $detalle->cantidad_compra, // Guardamos la cantidad original para comparación
                        'subtotal' => $detalle->subtotal_compra,
                        'esExistente' => true,
                        'modificado' => false,
                    ];
                })->toArray(),
        ) !!};

        const idCompra = {{ $compra->id_compra }};
        const proveedores = @json($proveedores);
        const productos = @json($productos);

        document.addEventListener("DOMContentLoaded", () => {
            const buscarProductoInput = document.getElementById('buscarProducto');
            const buscarProveedorInput = document.getElementById('buscarProveedor');
            const totalCompraInput = document.getElementById('totalCompraInput');
            const porcentajeDescuentoInput = document.getElementById('porcentajeDescuento');
            const tabla = document.getElementById('tablaProductos');
            const tbody = tabla.querySelector('tbody');

            renderProductos();

            totalCompraInput.value = {{ $compra->total_compra }};

            // Buscar proveedores dinámicamente
            buscarProveedorInput.addEventListener("input", function() {
                const buscar = this.value.toLowerCase().trim();
                const resultados = buscar ?
                    proveedores.filter(
                        (proveedor) =>
                        proveedor.id_proveedor.toString().includes(buscar) ||
                        proveedor.nombre_proveedor.toLowerCase().includes(buscar)
                    ) : [];
                renderProveedores(resultados);
            });

            function renderProveedores(resultados) {
                const tabla = document.getElementById("tablaProveedores");
                const tbody = tabla.querySelector("tbody");
                tbody.innerHTML = "";

                if (resultados.length > 0) {
                    tabla.classList.remove("d-none");
                    resultados.forEach((proveedor) => {
                        tbody.innerHTML += `
                    <tr>
                        <td>${proveedor.id_proveedor}</td>
                        <td>${proveedor.nombre_proveedor}</td>
                        <td>${proveedor.direccion || "N/A"}</td>
                        <td>${proveedor.celular_proveedor || "N/A"}</td>
                        <td>
                            <button class="btn btn-success btn-sm btn-choose-proveedor" data-id="${proveedor.id_proveedor}">
                                Añadir
                            </button>
                        </td>
                    </tr>`;
                    });
                } else {
                    tabla.classList.add("d-none");
                    showNotification('No se encontraron proveedores.', 'danger');
                }
            }

            // Seleccionar proveedor
            document.addEventListener("click", function(event) {
                if (event.target.classList.contains("btn-choose-proveedor")) {
                    const idProveedor = event.target.dataset.id;
                    const proveedor = proveedores.find((p) => p.id_proveedor == idProveedor);

                    proveedorSeleccionado = proveedor;
                    document.getElementById("proveedorSeleccionado").classList.remove("d-none");
                    document.getElementById("nombreProveedor").innerText = proveedor.nombre_proveedor;
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
                    showNotification('No se encontraron productos.', 'danger');
                }
            });

            // Manejar clic para añadir productos
            document.addEventListener('click', (event) => {
                if (event.target.classList.contains('btn-choose-producto')) {
                    const idProducto = parseInt(event.target.getAttribute('data-id'));
                    const producto = productos.find(p => p.id_producto === idProducto);

                    if (!producto) {
                        showNotification('Producto no encontrado.', 'danger');
                        return;
                    }

                    añadirProducto(producto);
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
                //
                producto.cantidad = 1;
                producto.subtotal = (producto.cantidad * producto.precio_compra_actual).toFixed(2);
                producto.esExistente = false; // Indica que es un producto nuevo
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
                    <td>Bs ${producto.precio_compra_actual}</td>
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

                    if (isNaN(cantidad) || cantidad < 1) {
                        cantidadInput.classList.add('is-invalid');
                    } else {
                        cantidadInput.classList.remove('is-invalid');
                        producto.cantidad = cantidad;
                        producto.subtotal = producto.cantidad * producto.precio_compra_actual;

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
                        actualizarTotal(); // Mantiene la cantidad fija
                    }
                }

                // Validar y aplicar porcentaje de descuento
                if (event.target.id === 'porcentajeDescuento') {
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

            // Quitar producto
            document.addEventListener('click', (event) => {
                if (event.target.classList.contains('btn-quitar-producto')) {
                    const index = event.target.getAttribute('data-index');
                    const producto = productosSeleccionados[index];

                    if (producto.esExistente) {
                        idDetallesProductosEliminados.push(producto.id_detalle_compra);
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

                    totalCompraInput.value = totalConDescuento.toFixed(2);
                } else {
                    totalCompraInput.value = totalSinDescuento.toFixed(2);
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

            document.getElementById('actualizarCompra').addEventListener('click', function() {
                const modal = new bootstrap.Modal(document.getElementById('confirmarCompraModal'));
                modal.show();
            });

            document.getElementById('confirmarCambiosCompra').addEventListener('click', async function() {
                const modalElement = document.getElementById('confirmarCompraModal');
                const modalInstance = bootstrap.Modal.getInstance(modalElement);
                modalInstance.hide();

                if (!proveedorSeleccionado) {
                    showNotification('Debe seleccionar un proveedor.', 'danger');
                    return;
                }

                if (productosSeleccionados.length === 0) {
                    showNotification('Debe seleccionar al menos un producto.', 'danger');
                    return;
                }

                const porcentajeDescuento = parseInt(document.getElementById('porcentajeDescuento')
                    .value) || 0;

                if (porcentajeDescuento < 0 || porcentajeDescuento > 100) {
                    showNotification('El porcentaje de descuento debe estar entre 0 y 100.', 'danger');
                    return;
                }

                const data = {
                    proveedor_id: proveedorSeleccionado.id_proveedor,
                    fecha_compra: document.getElementById('fechaCompra').value || null,
                    factura_compra: document.getElementById('compraConFactura').checked ? 1 : 0,
                    descuento_compra: porcentajeDescuento,
                    total_compra: parseFloat(document.getElementById('totalCompraInput').value) ||
                        0,
                    productosEliminados: idDetallesProductosEliminados,
                    productos: productosSeleccionados.map(producto => ({
                        id: producto.id_producto,
                        cantidad: producto.cantidad,
                        subtotal: producto.subtotal,
                        descripcion: producto.descripcion || null,
                        esExistente: producto.esExistente,
                        modificado: producto.modificado || false,
                    })),
                };

                try {
                    const response = await fetch(`/compras/${idCompra}`, {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                .getAttribute('content'),
                        },
                        body: JSON.stringify(data),
                    });

                    if (!response.ok) {
                        const errorText = await response.text();
                        throw new Error(`Error del servidor: ${errorText}`);
                    }

                    const result = await response.json();

                    if (result.success) {
                        showNotification(result.message, 'success');
                        setTimeout(() => {
                            window.location.reload();
                        }, 3000);
                    } else {
                        showNotification(result.message || 'Error al actualizar la compra.', 'danger');
                    }
                } catch (error) {
                    console.error('Error:', error);
                    showNotification('Hubo un error al actualizar la compra.', 'danger');
                }
            });
        });
    </script>
@endsection
