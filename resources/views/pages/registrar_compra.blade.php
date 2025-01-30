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

        <!-- Buscar Proveedor -->
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
        <div id="proveedorSeleccionado" class="mb-4 d-none">
            <h5>Proveedor Seleccionado:</h5>
            <div class="alert alert-info d-flex justify-content-between align-items-center">
                <span id="nombreProveedor"></span>
                <!-- <button class="btn btn-danger btn-sm" id="quitarProveedor">Quitar</button>-->
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

        <div class="row mb-4 align-items-center">
            <!-- Porcentaje de Descuento -->
            <div class="col-auto d-flex align-items-center" style="gap: 0.001rem;">
                <label for="porcentajeDescuento" class="form-label mb-0 me-2">Porcentaje de Descuento:</label>
                <input type="number" id="porcentajeDescuento" class="form-control" placeholder="0-100" min="0"
                    max="100" step="1" style="max-width: 100px;" value="0">
            </div>
            <!-- Fecha de Compra -->
            <div class="col-auto d-flex align-items-center ms-3">
                <label for="fechaCompra" class="form-label mb-0 me-2">Fecha de Compra:</label>
                <input type="date" id="fechaCompra" class="form-control" style="max-width: 200px;">
            </div>
            <!-- Opciones de Factura -->
            <div class="col-auto d-flex align-items-center ms-3">

                <label class="form-check-label mb-0  me-3" for="compraConFactura">¿Compra con factura?</label>
                <input type="checkbox" class="form-check-input me-2" id="compraConFactura">
            </div>
        </div>

        <!-- Resumen de Compra -->
        <div class="d-flex align-items-center flex-wrap gap-2 justify-content-between">
            <label for="totalCompraInput" class="form-label mb-0">Total de la Compra: Bs</label>
            <input type="number" id="totalCompraInput" class="form-control text-end" style="max-width: 120px;"
                min="0" step="0.01" value="0.00">

            <div class="ms-auto">
                <button class="btn btn-success mt-2 mt-md-0" id="finalizarCompra">Finalizar Compra</button>
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
        const proveedores = @json($proveedores);
        const productos = @json($productos);

        document.addEventListener("DOMContentLoaded", () => {
            const buscarProductoInput = document.getElementById('buscarProducto');
            const buscarProveedorInput = document.getElementById('buscarProveedor');
            const totalCompraInput = document.getElementById('totalCompraInput');
            const porcentajeDescuentoInput = document.getElementById('porcentajeDescuento');
            const tabla = document.getElementById('tablaProductos');
            const tbody = tabla.querySelector('tbody');

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
                    //showNotification('No se encontraron proveedores.', 'danger');
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
                    // Si hay un único resultado y el input proviene de un código de barras (numérico)
                    if (resultados.length === 1 && !isNaN(buscar) && resultados[0].codigo_barra &&
                        resultados[0].codigo_barra.toLowerCase() === buscar) {
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
                producto.subtotal = (producto.cantidad * producto.precio_compra_actual).toFixed(2);
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

            document.getElementById('finalizarCompra').addEventListener('click', async function() {

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
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                .getAttribute(
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
                        }, 2000);
                    } else {
                        showNotification(result.message || 'Error al registrar la compra.', 'danger');
                    }
                } catch (error) {
                    console.error('Error:', error);
                    showNotification('Hubo un error al registrar la compra.', 'danger');
                }
            });

        });
    </script>
@endsection
