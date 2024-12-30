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
            <li class="breadcrumb-item active" aria-current="page">Registrar venta</li>
        </ol>
    </nav>
    <h2 class="text-center">Registrar Venta</h2>
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
        <!-- Selección de socio -->
        <div class="row mb-3">
            <div class="col-md-6">
                <label for="id_socio" class="form-label">Socio</label>
                <select name="id_socio" id="id_socio" class="form-select">
                    <option value="">Seleccionar Socio (Opcional)</option>
                    @foreach ($socios as $socio)
                        <option value="{{ $socio->id_socio }}">{{ $socio->nombre_socio }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <label for="id_usuario" class="form-label">Encargado</label>
                <input type="text" class="form-control" value="{{ $usuario->nombre }}" disabled>
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
                    <option value="0">No</option>
                    <option value="1">Sí</option>
                </select>
            </div>
            <div class="col-md-4">
                <label for="servicio" class="form-label">¿Incluye Servicio?</label>
                <select name="servicio" id="servicio" class="form-select">
                    <option value="0">No</option>
                    <option value="1">Sí</option>
                </select>
            </div>
            <div class="col-md-4">
                <label for="finalizada" class="form-label">¿Venta Finalizada?</label>
                <select name="finalizada" id="finalizada" class="form-select">
                    <option value="1">Sí</option>
                    <option value="0">No</option>
                </select>
            </div>
        </div>

        <!-- Descripción -->
        <div class="row mb-4">
            <div class="col-12">
                <label for="descripcion" class="form-label">Descripción (Opcional):</label>
                <textarea id="descripcion" name="descripcion" class="form-control" rows="3"
                    placeholder="Ingrese una descripción (opcional)..."></textarea>
            </div>
        </div>

        <!-- Total de la Venta -->
        <div class="d-flex align-items-center flex-wrap gap-2 justify-content-between">
            <div class="d-flex align-items-center gap-1">
                <label for="totalVentaInput" class="form-label mb-0">Total de la Venta: Bs</label>
                <input type="number" id="totalVentaInput" name="total_venta" class="form-control text-end"
                    style="max-width: 120px;" min="0" step="0.01" value="0.00">
            </div>
            <div class="ms-auto">
                <button class="btn btn-success mt-2 mt-md-0" id="finalizarVenta">Finalizar Venta</button>
            </div>
        </div>

        <!-- Modal Confirmación -->
        <div class="modal fade" id="confirmarVentaModal" tabindex="-1" aria-labelledby="confirmarVentaModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-secondary text-white">
                        <h5 class="modal-title" id="confirmarCompraModalLabel">Confirmar Venta</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        ¿Está seguro de finalizar esta venta?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="button" class="btn btn-primary" id="confirmarVenta">Confirmar</button>
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
        let productosSeleccionados = [];
        const productos = @json($productos);

        document.addEventListener('DOMContentLoaded', () => {
            const totalVentaInput = document.getElementById('totalVentaInput');
            const montoPagadoInput = document.getElementById('monto_pagado');
            const saldoPendienteInput = document.getElementById('saldo_pendiente');

            // Actualizar saldo pendiente cuando cambia el total
            totalVentaInput.addEventListener('input', () => {
                const total = parseFloat(totalVentaInput.value) || 0;
                const pagado = parseFloat(montoPagadoInput.value) || 0;
                saldoPendienteInput.value = (total - pagado).toFixed(2);
            });

            // Actualizar saldo pendiente cuando cambia el monto pagado
            montoPagadoInput.addEventListener('input', () => {
                const total = parseFloat(totalVentaInput.value) || 0;
                const pagado = parseFloat(montoPagadoInput.value) || 0;
                saldoPendienteInput.value = (total - pagado).toFixed(2);
            });
        });

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

        document.addEventListener('click', function(event) {
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
            const existe = productosSeleccionados.some(p => p.id_producto === producto.id_producto);

            if (existe) {
                showNotification('El producto ya está añadido.', 'danger');
                return;
            }

            if (producto.stock <= 0) {
                showNotification('El producto no tiene stock disponible.', 'danger');
                return;
            }

            producto.cantidad = 1; // Cantidad inicial
            producto.subtotal = producto.cantidad * producto.precio_venta_actual;
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
            <td>Bs ${producto.precio_venta_actual.toFixed(2)}</td>
            <td>
                <input type="number" class="form-control cantidad" value="${producto.cantidad}" 
                       min="1" max="${producto.stock}" 
                       onchange="actualizarCantidad(${producto.id_producto}, this.value)">
            </td>
            <td>Bs ${(producto.cantidad * producto.precio_venta_actual).toFixed(2)}</td>
            <td><button class="btn btn-danger btn-sm" onclick="quitarProducto(${producto.id_producto})">Quitar</button></td>
        </tr>`;
            });

            actualizarTotal();
        }

        function actualizarCantidad(idProducto, cantidad) {
            const producto = productosSeleccionados.find(p => p.id_producto === idProducto);

            if (!producto) {
                return;
            }

            const cant = cantidad.trim();
            const cantidadNumerica = parseInt(cant, 10);

            if (cantidadNumerica < 1 || cantidadNumerica > producto.stock || !cant || isNaN(cantidadNumerica)) {
                showNotification('La cantidad debe estar entre 1 y el stock disponible.', 'danger');
                renderProductos();
                return;
            }

            producto.cantidad = cantidadNumerica;
            producto.subtotal = producto.cantidad * producto.precio_venta_actual;
            renderProductos();
        }

        function quitarProducto(idProducto) {
            productosSeleccionados = productosSeleccionados.filter(p => p.id_producto !== idProducto);
            renderProductos();
        }

        function actualizarTotal() {
            const total = productosSeleccionados.reduce((sum, producto) => sum + producto.subtotal, 0);
            document.getElementById('totalVentaInput').value = total.toFixed(2);
            document.getElementById('monto_pagado').dispatchEvent(new Event('input')); // Forzar actualización
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

        document.getElementById('finalizarVenta').addEventListener('click', function() {
            const modal = new bootstrap.Modal(document.getElementById('confirmarVentaModal'));
            modal.show();
        });

        document.getElementById('confirmarVenta').addEventListener('click', async function() {
            const modalElement = document.getElementById('confirmarVentaModal');
            const modalInstance = bootstrap.Modal.getInstance(modalElement);

            if (modalInstance) {
                modalInstance.hide(); // Oculta el modal
            }

            if (productosSeleccionados.length === 0) {
                showNotification('Debe seleccionar al menos un producto.', 'danger');
                return;
            }

            const data = {
                id_socio: document.getElementById('id_socio').value || null,
                total_venta: parseFloat(document.getElementById('totalVentaInput').value) || 0,
                monto_pagado: parseFloat(document.getElementById('monto_pagado').value) || 0,
                saldo_pendiente: parseFloat(document.getElementById('saldo_pendiente').value) || 0,
                descripcion: document.getElementById('descripcion').value || null,
                credito: parseInt(document.getElementById('credito').value) || 0,
                servicio: parseInt(document.getElementById('servicio').value) || 0,
                finalizada: parseInt(document.getElementById('finalizada').value) || 0,
                productos: productosSeleccionados.map(producto => ({
                    id: producto.id_producto,
                    cantidad: producto.cantidad,
                    subtotal: producto.subtotal
                }))
            };

            try {
                // Usa `await` aquí para esperar la respuesta
                const response = await fetch('/ventas', {
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
                    showNotification(result.message || 'Error al registrar la venta.', 'danger');
                }
            } catch (error) {
                console.error('Error:', error);
                showNotification('Hubo un error al registrar la venta.', 'danger');
            }
        });
    </script>
@endsection
