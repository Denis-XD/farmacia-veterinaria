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
            <li class="breadcrumb-item active" aria-current="page">Registrar compra</li>
        </ol>
    </nav>
    <h2 class="text-center">Registrar Compra</h2>
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
        <!-- Buscador de proveedor -->
        <div class="card mb-4">
            <div class="card-header">Buscar Proveedor</div>
            <div class="card-body">
                <div class="input-group mb-3">
                    <input type="text" id="buscarProveedor" class="form-control" placeholder="Buscar por ID o Nombre">
                    <button class="btn btn-primary" id="btnBuscarProveedor">Buscar</button>
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
        <div id="proveedorSeleccionado" class="mb-4 d-none">
            <h5>Proveedor Seleccionado:</h5>
            <div class="alert alert-info d-flex justify-content-between align-items-center">
                <span id="nombreProveedor"></span>
                <button class="btn btn-danger btn-sm" id="quitarProveedor">Quitar</button>
            </div>
        </div>

        <!-- Buscador de productos -->
        <div class="card mb-4">
            <div class="card-header">Buscar Productos</div>
            <div class="card-body">
                <div class="input-group mb-3">
                    <input type="text" id="buscarProducto" class="form-control"
                        placeholder="Buscar por ID, Código de Barra o Nombre">
                    <button class="btn btn-primary" id="btnBuscarProducto">Buscar</button>
                </div>
                <!-- Resultados de búsqueda de productos -->
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
        <div class="row mb-4 align-items-center">
            <!-- Porcentaje de Descuento -->
            <div class="col-auto d-flex align-items-center">
                <label for="porcentajeDescuento" class="form-label mb-0 me-2">Porcentaje de Descuento:</label>
                <input type="number" id="porcentajeDescuento" class="form-control" placeholder="0-100" min="0"
                    max="100" style="max-width: 100px;">
            </div>
            <!-- Opciones de Factura -->
            <div class="col-auto d-flex align-items-center ms-3">
                <input type="checkbox" class="form-check-input me-2" id="compraConFactura">
                <label class="form-check-label mb-0" for="compraConFactura">¿Compra con factura?</label>
            </div>
        </div>

        <!-- Resumen de Compra -->
        <div class="d-flex align-items-center flex-wrap gap-2 justify-content-between">
            <h5 class="mb-0">Total de la Compra: <span id="totalCompra">Bs 0.00</span></h5>
            <div class="ms-auto">
                <button class="btn btn-success mt-2 mt-md-0" id="finalizarCompra">Finalizar Compra</button>
            </div>
        </div>

        <!-- Modal Confirmación -->
        <div class="modal fade" id="confirmarCompraModal" tabindex="-1" aria-labelledby="confirmarCompraModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-secondary text-white">
                        <h5 class="modal-title" id="confirmarCompraModalLabel">Confirmar Compra</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        ¿Está seguro de finalizar esta compra?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="button" class="btn btn-primary" id="confirmarCompra">Confirmar</button>
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
        let proveedorSeleccionado = null;
        let productosSeleccionados = [];

        // Obtener los datos de proveedores y productos enviados desde el controlador
        const proveedores = @json($proveedores);
        const productos = @json($productos);

        // Buscar y mostrar proveedores localmente
        document.getElementById('btnBuscarProveedor').addEventListener('click', function() {
            const buscar = document.getElementById('buscarProveedor').value.toLowerCase();

            // Filtrar proveedores localmente
            const resultados = proveedores.filter(proveedor =>
                proveedor.id_proveedor.toString().includes(buscar) ||
                proveedor.nombre_proveedor.toLowerCase().includes(buscar)
            );

            const tbody = document.querySelector('#tablaProveedores tbody');
            const tabla = document.getElementById('tablaProveedores');
            tbody.innerHTML = '';

            if (resultados.length > 0) {
                tabla.classList.remove('d-none');
                resultados.forEach(proveedor => {
                    tbody.innerHTML += `
        <tr>
            <td>${proveedor.id_proveedor}</td>
            <td>${proveedor.nombre_proveedor}</td>
            <td>${proveedor.direccion || 'N/A'}</td>
            <td>${proveedor.celular_proveedor || 'N/A'}</td>
            <td>
                <button class="btn btn-success btn-sm btn-choose-proveedor" data-id="${proveedor.id_proveedor}">Añadir</button>
            </td>
        </tr>`;
                });

            } else {
                tabla.classList.add('d-none');
                alert('No se encontraron proveedores.');
            }
        });
        document.addEventListener('click', function(event) {
            // Selección de proveedor
            if (event.target.classList.contains('btn-choose-proveedor')) {
                const idProveedor = event.target.getAttribute('data-id');
                const proveedor = proveedores.find(p => p.id_proveedor == idProveedor);

                if (proveedorSeleccionado) {
                    showNotification('Solo se puede seleccionar un proveedor.', 'danger');
                    return;
                }

                seleccionarProveedor(proveedor);
            }

            // Selección de producto
            if (event.target.classList.contains('btn-choose-producto')) {
                const idProducto = event.target.getAttribute('data-id');
                const producto = productos.find(p => p.id_producto == idProducto);

                añadirProducto(producto);
            }
        });

        function seleccionarProveedor(proveedor) {
            proveedorSeleccionado = proveedor;
            document.getElementById('proveedorSeleccionado').classList.remove('d-none');
            document.getElementById('nombreProveedor').innerText = proveedor.nombre_proveedor;
        }


        document.getElementById('quitarProveedor').addEventListener('click', function() {
            proveedorSeleccionado = null;
            document.getElementById('proveedorSeleccionado').classList.add('d-none');
        });

        // Buscar y mostrar productos localmente
        document.getElementById('btnBuscarProducto').addEventListener('click', function() {
            const buscar = document.getElementById('buscarProducto').value.toLowerCase();

            // Filtrar productos localmente
            const resultados = productos.filter(producto =>
                producto.id_producto.toString().includes(buscar) ||
                (producto.codigo_barra && producto.codigo_barra.includes(buscar)) ||
                producto.nombre_producto.toLowerCase().includes(buscar)
            );

            const tbody = document.querySelector('#tablaProductos tbody');
            const tabla = document.getElementById('tablaProductos');
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
                <button class="btn btn-success btn-sm btn-choose-producto" data-id="${producto.id_producto}">Añadir</button>
            </td>
        </tr>`;
                });

            } else {
                tabla.classList.add('d-none');
                showNotification('No se encontraron productos.', 'danger');
            }
        });

        function añadirProducto(producto) {
            const existe = productosSeleccionados.some(p => p.id_producto === producto.id_producto);
            if (existe) {
                showNotification('El producto ya está añadido.', 'danger');
                return;
            }

            producto.cantidad = 1; // Cantidad inicial
            producto.subtotal = producto.precio_compra_actual; // Subtotal inicial
            productosSeleccionados.push(producto);
            renderProductos();
        }

        function renderProductos() {
            const tbody = document.getElementById('listaProductos');
            tbody.innerHTML = '';

            productosSeleccionados.forEach(producto => {
                tbody.innerHTML += `
            <tr>
                <td>${producto.nombre_producto}</td>
                <td>Bs ${producto.precio_compra_actual.toFixed(2)}</td>
                <td>
                    <input type="number" class="form-control cantidad" value="${producto.cantidad}" min="1" onchange="actualizarCantidad(${producto.id_producto}, this.value)">
                </td>
                <td>Bs ${(producto.cantidad * producto.precio_compra_actual).toFixed(2)}</td>
                <td><button class="btn btn-danger btn-sm" onclick="quitarProducto(${producto.id_producto})">Quitar</button></td>
            </tr>`;
            });

            actualizarTotal();
        }

        function actualizarCantidad(id, cantidad) {
            const producto = productosSeleccionados.find(p => p.id_producto === id);
            if (producto) {
                producto.cantidad = parseInt(cantidad);
                producto.subtotal = producto.cantidad * producto.precio_compra_actual;
                renderProductos();
            }
        }

        function quitarProducto(id) {
            productosSeleccionados = productosSeleccionados.filter(p => p.id_producto !== id);
            renderProductos();
        }

        function actualizarTotal() {
            const total = productosSeleccionados.reduce((sum, producto) => sum + producto.subtotal, 0);
            document.getElementById('totalCompra').innerText = `Bs ${total.toFixed(2)}`;
        }

        function showNotification(message, type) {
            const notification = document.createElement('div');
            notification.className = `notification ${type}`;
            notification.innerText = message;

            document.body.appendChild(notification);

            // Agregar la clase para la animación de entrada
            setTimeout(() => {
                notification.classList.add('show');
            }, 10);

            // Quitar la notificación después de 3 segundos
            setTimeout(() => {
                notification.classList.remove('show');
                // Remover el elemento después de la transición
                notification.addEventListener('transitionend', () => notification.remove());
            }, 5000);
        }

        function actualizarTotal() {
            let total = productosSeleccionados.reduce((sum, producto) => sum + producto.subtotal, 0);

            const porcentajeDescuento = parseInt(document.getElementById('porcentajeDescuento').value) || 0;

            if (porcentajeDescuento > 0 && porcentajeDescuento <= 100) {
                total -= (total * porcentajeDescuento) / 100;
            }

            document.getElementById('totalCompra').innerText = `Bs ${total.toFixed(2)}`;
        }

        // Escuchar cambios en el porcentaje de descuento
        document.getElementById('porcentajeDescuento').addEventListener('input', actualizarTotal);


        document.getElementById('finalizarCompra').addEventListener('click', function() {
            const modal = new bootstrap.Modal(document.getElementById('confirmarCompraModal'));
            modal.show();
        });

        document.getElementById('confirmarCompra').addEventListener('click', async function() {
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

            const porcentajeDescuento = parseInt(document.getElementById('porcentajeDescuento').value) || 0;

            if (porcentajeDescuento < 0 || porcentajeDescuento > 100) {
                showNotification('El porcentaje de descuento debe estar entre 0 y 100.', 'danger');
                return;
            }

            const data = {
                proveedor_id: proveedorSeleccionado.id_proveedor,
                factura_compra: document.getElementById('compraConFactura').checked ? 1 : 0,
                descuento_compra: porcentajeDescuento,
                productos: productosSeleccionados.map(producto => ({
                    id: producto.id_producto,
                    cantidad: producto.cantidad,
                    subtotal: producto.subtotal,
                    descripcion: producto.descripcion || null,
                })),
            };

            try {
                // Usa `await` aquí para esperar la respuesta
                const response = await fetch('/compras', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                            'content'),
                    },
                    body: JSON.stringify(data),
                });

                if (!response.ok) {
                    const errorText = await response.text(); // Leer la respuesta como texto
                    throw new Error(`Error del servidor: ${errorText}`);
                }

                const result = await response.json();

                if (result.success) {
                    showNotification(result.message, 'success');
                    setTimeout(() => {
                        window.location.href = result.redirect;
                    }, 3000);
                } else {
                    showNotification(result.message || 'Error al registrar la compra.', 'danger');
                }
            } catch (error) {
                console.error('Error:', error);
                showNotification('Hubo un error al registrar la compra.', 'danger');
            }
        });
    </script>
@endsection
