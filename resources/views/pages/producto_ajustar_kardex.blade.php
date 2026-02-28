@extends('layout')

@section('content')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a disabled>
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="currentColor"
                        class="icon icon-tabler icons-tabler-filled icon-tabler-home">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path
                            d="M12.707 2.293l9 9c.63 .63 .184 1.707 -.707 1.707h-1v6a3 3 0 0 1 -3 3h-1v-7a3 3 0 0 0 -2.824 -2.995l-.176 -.005h-2a3 3 0 0 0 -3 3v7h-1a3 3 0 0 1 -3 -3v-6h-1c-.89 0 -1.337 -1.077 -.707 -1.707l9 -9a1 1 0 0 1 1.414 0m.293 11.707a1 1 0 0 1 1 1v7h-4v-7a1 1 0 0 1 .883 -.993l.117 -.007z" />
                    </svg>
                </a>
            </li>
            <li class="breadcrumb-item"><a href="{{ route('reservas') }}">Inicio</a></li>
            <li class="breadcrumb-item"><a href="{{ route('productos.index') }}">Productos</a></li>
            <li class="breadcrumb-item active" aria-current="page">Ajustar Kardex</li>
        </ol>
    </nav>

    {{-- Corrección 2: más separación entre título y buscador --}}
    <h2 class="text-center mt-3 mb-4">Ajuste de Inventario</h2>

    <div class="d-flex justify-content-center mb-3">
        <div class="input-group" style="max-width: 500px;">
            <input type="text" id="buscarProducto" class="form-control"
                placeholder="Buscar por ID, Código de Barra o Nombre">
            <button class="btn btn-secondary" id="btnLimpiarBusqueda">Limpiar</button>
        </div>
    </div>

    {{-- Corrección 4: tabla responsiva con botones en línea --}}
    <div class="table-responsive">
        <table class="table table-bordered table-hover" id="tablaAjuste" style="min-width: 550px;">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Código de Barra</th>
                    <th>Nombre</th>
                    <th>Stock Actual</th>
                    <th class="text-center" style="min-width: 160px;">Acciones</th>
                </tr>
            </thead>
            <tbody id="cuerpoTabla">
                @forelse ($productos as $producto)
                    <tr data-id="{{ $producto->id_producto }}" data-nombre="{{ strtolower($producto->nombre_producto) }}"
                        data-codigo="{{ strtolower($producto->codigo_barra ?? '') }}">
                        <td>{{ $producto->id_producto }}</td>
                        <td>{{ $producto->codigo_barra ?? 'N/A' }}</td>
                        <td>{{ $producto->nombre_producto }}</td>
                        <td>
                            <span class="fw-bold" id="stock-{{ $producto->id_producto }}">
                                {{ $producto->stock }}
                            </span>
                        </td>
                        {{-- Corrección 4: nowrap para que los botones no se partan --}}
                        <td class="text-center" style="white-space: nowrap;">
                            <button class="btn btn-success btn-sm btn-ajuste" data-id="{{ $producto->id_producto }}"
                                data-nombre="{{ $producto->nombre_producto }}" data-stock="{{ $producto->stock }}"
                                data-tipo="positivo" title="Ajuste positivo (entrada)">
                                + Entrada
                            </button>
                            <button class="btn btn-danger btn-sm btn-ajuste ms-1" data-id="{{ $producto->id_producto }}"
                                data-nombre="{{ $producto->nombre_producto }}" data-stock="{{ $producto->stock }}"
                                data-tipo="negativo" title="Ajuste negativo (salida)">
                                − Salida
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr id="filaVacia">
                        <td colspan="5" class="text-center">No hay productos registrados.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Corrección 3: paginación JS robusta y responsiva --}}
    <div class="d-flex flex-wrap justify-content-center align-items-center gap-1 mt-3" id="paginacion"></div>

    {{-- Modal de ajuste --}}
    <div class="modal fade" id="modalAjuste" tabindex="-1" aria-labelledby="modalAjusteLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header" id="modalAjusteHeader">
                    <h5 class="modal-title" id="modalAjusteLabel">Ajuste de Stock</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p id="modalAjusteDescripcion" class="mb-3"></p>

                    <div class="mb-3">
                        <label for="inputCantidad" class="form-label fw-bold">Cantidad</label>
                        <input type="number" id="inputCantidad" class="form-control" min="0.01" max="50000"
                            step="0.01" placeholder="Máximo 50,000">
                        <div class="invalid-feedback" id="errorCantidad"></div>
                    </div>

                    <div class="mb-3">
                        {{-- Corrección 5: maxlength 50 en el input --}}
                        <label for="inputMotivo" class="form-label">
                            Motivo (opcional)
                            <small class="text-muted" id="contadorMotivo">0/50</small>
                        </label>
                        <input type="text" id="inputMotivo" class="form-control" maxlength="50"
                            placeholder="Ej: Inventario físico, merma, donación...">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn" id="btnConfirmarAjuste">Confirmar</button>
                </div>
            </div>
        </div>
    </div>

    <style>
        .notification {
            position: fixed;
            bottom: -100px;
            left: 50%;
            transform: translateX(-50%);
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
            border-radius: 5px;
            padding: 15px 20px;
            font-size: 14px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2);
            z-index: 2000;
            opacity: 0;
            transition: all 0.4s ease;
            white-space: nowrap;
        }

        .notification.success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .notification.show {
            bottom: 20px;
            opacity: 1;
        }

        /* Corrección 3: paginación responsiva sin colapsar */
        #paginacion .btn {
            min-width: 36px;
        }

        @media (max-width: 576px) {
            #paginacion {
                gap: 0.25rem !important;
            }

            #paginacion .btn {
                font-size: 12px;
                padding: 0.25rem 0.5rem;
                min-width: 30px;
            }
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', () => {

            // ── Datos y estado ───────────────────────────────────────────────
            const ITEMS_POR_PAGINA = 10;
            let todasLasFilas = Array.from(document.querySelectorAll('#cuerpoTabla tr[data-id]'));
            let filasFiltradas = [...todasLasFilas];
            let paginaActual = 1;
            let productoActual = null;
            let isSubmitting = false;

            const modal = new bootstrap.Modal(document.getElementById('modalAjuste'));
            const modalHeader = document.getElementById('modalAjusteHeader');
            const modalLabel = document.getElementById('modalAjusteLabel');
            const modalDesc = document.getElementById('modalAjusteDescripcion');
            const inputCantidad = document.getElementById('inputCantidad');
            const inputMotivo = document.getElementById('inputMotivo');
            const btnConfirmar = document.getElementById('btnConfirmarAjuste');
            const errorCantidad = document.getElementById('errorCantidad');
            const contadorMotivo = document.getElementById('contadorMotivo');

            // ── Contador de caracteres del motivo ────────────────────────────
            inputMotivo.addEventListener('input', () => {
                contadorMotivo.textContent = `${inputMotivo.value.length}/50`;
            });

            // ── Paginación ───────────────────────────────────────────────────
            function mostrarPagina(pagina) {
                paginaActual = pagina;
                const inicio = (pagina - 1) * ITEMS_POR_PAGINA;
                const fin = inicio + ITEMS_POR_PAGINA;

                // Ocultar todas
                todasLasFilas.forEach(f => f.style.display = 'none');

                // Mostrar solo las de esta página
                filasFiltradas.slice(inicio, fin).forEach(f => f.style.display = '');

                // Manejar fila vacía
                const filaVacia = document.getElementById('filaVacia');
                if (filaVacia) {
                    filaVacia.style.display = filasFiltradas.length === 0 ? '' : 'none';
                }

                renderPaginacion();
            }

            function renderPaginacion() {
                const total = Math.ceil(filasFiltradas.length / ITEMS_POR_PAGINA);
                const contenedor = document.getElementById('paginacion');
                contenedor.innerHTML = '';

                if (total <= 1) return;

                // Botón Anterior
                const btnAnterior = document.createElement('button');
                btnAnterior.className =
                    `btn btn-sm ${paginaActual === 1 ? 'btn-secondary disabled' : 'btn-outline-secondary'}`;
                btnAnterior.textContent = 'Anterior';
                btnAnterior.addEventListener('click', () => {
                    if (paginaActual > 1) mostrarPagina(paginaActual - 1);
                });
                contenedor.appendChild(btnAnterior);

                // Páginas con ventana deslizante — nunca colapsa
                const rango = 2; // páginas a cada lado de la actual
                const inicio = Math.max(1, paginaActual - rango);
                const fin = Math.min(total, paginaActual + rango);

                if (inicio > 1) {
                    agregarBotonPagina(contenedor, 1, total);
                    if (inicio > 2) agregarEllipsis(contenedor);
                }

                for (let i = inicio; i <= fin; i++) {
                    agregarBotonPagina(contenedor, i, total);
                }

                if (fin < total) {
                    if (fin < total - 1) agregarEllipsis(contenedor);
                    agregarBotonPagina(contenedor, total, total);
                }

                // Botón Siguiente
                const btnSiguiente = document.createElement('button');
                btnSiguiente.className =
                    `btn btn-sm ${paginaActual === total ? 'btn-secondary disabled' : 'btn-outline-secondary'}`;
                btnSiguiente.textContent = 'Siguiente';
                btnSiguiente.addEventListener('click', () => {
                    if (paginaActual < total) mostrarPagina(paginaActual + 1);
                });
                contenedor.appendChild(btnSiguiente);
            }

            function agregarBotonPagina(contenedor, numero) {
                const btn = document.createElement('button');
                btn.className = `btn btn-sm ${numero === paginaActual ? 'btn-primary' : 'btn-outline-primary'}`;
                btn.textContent = numero;
                btn.addEventListener('click', () => mostrarPagina(numero));
                contenedor.appendChild(btn);
            }

            function agregarEllipsis(contenedor) {
                const span = document.createElement('span');
                span.className = 'btn btn-sm btn-outline-secondary disabled';
                span.textContent = '...';
                contenedor.appendChild(span);
            }

            // Inicializar paginación
            mostrarPagina(1);

            // ── Buscador ─────────────────────────────────────────────────────
            document.getElementById('buscarProducto').addEventListener('input', function() {
                const buscar = this.value.toLowerCase().trim();

                filasFiltradas = buscar ?
                    todasLasFilas.filter(fila =>
                        (fila.dataset.nombre || '').includes(buscar) ||
                        (fila.dataset.codigo || '').includes(buscar) ||
                        (fila.dataset.id || '').includes(buscar)
                    ) : [...todasLasFilas];

                mostrarPagina(1);
            });

            document.getElementById('btnLimpiarBusqueda').addEventListener('click', () => {
                document.getElementById('buscarProducto').value = '';
                filasFiltradas = [...todasLasFilas];
                mostrarPagina(1);
            });

            // ── Abrir modal ──────────────────────────────────────────────────
            document.addEventListener('click', event => {
                if (!event.target.classList.contains('btn-ajuste')) return;

                const btn = event.target;
                const tipo = btn.dataset.tipo;

                productoActual = {
                    id: btn.dataset.id,
                    nombre: btn.dataset.nombre,
                    stock: parseFloat(btn.dataset.stock),
                    tipo: tipo,
                };

                if (tipo === 'positivo') {
                    modalHeader.className = 'modal-header bg-success text-white';
                    modalLabel.textContent = 'Ajuste de Entrada (+ Stock)';
                    btnConfirmar.className = 'btn btn-success';
                    btnConfirmar.textContent = 'Confirmar Entrada';
                    modalDesc.innerHTML =
                        `Producto: <strong>${productoActual.nombre}</strong><br>
                         Stock actual: <strong>${productoActual.stock}</strong><br>
                         <span class="text-success">Se <strong>sumará</strong> la cantidad indicada al stock.</span>`;
                } else {
                    modalHeader.className = 'modal-header bg-danger text-white';
                    modalLabel.textContent = 'Ajuste de Salida (− Stock)';
                    btnConfirmar.className = 'btn btn-danger';
                    btnConfirmar.textContent = 'Confirmar Salida';
                    modalDesc.innerHTML =
                        `Producto: <strong>${productoActual.nombre}</strong><br>
                         Stock actual: <strong>${productoActual.stock}</strong><br>
                         <span class="text-danger">Se <strong>restará</strong> la cantidad indicada del stock.</span>`;
                }

                inputCantidad.value = '';
                inputMotivo.value = '';
                contadorMotivo.textContent = '0/50';
                inputCantidad.classList.remove('is-invalid');

                modal.show();
                setTimeout(() => inputCantidad.focus(), 400);
            });

            // ── Confirmar ajuste ─────────────────────────────────────────────
            btnConfirmar.addEventListener('click', async () => {
                if (isSubmitting) return;

                const cantidad = parseFloat(inputCantidad.value);

                // Validaciones front-end — Corrección 5
                if (isNaN(cantidad) || cantidad <= 0) {
                    inputCantidad.classList.add('is-invalid');
                    errorCantidad.textContent = 'Ingrese una cantidad válida mayor a 0.';
                    return;
                }
                if (cantidad > 50000) {
                    inputCantidad.classList.add('is-invalid');
                    errorCantidad.textContent = 'La cantidad no puede superar 50,000.';
                    return;
                }
                if (productoActual.tipo === 'negativo' && cantidad > productoActual.stock) {
                    inputCantidad.classList.add('is-invalid');
                    errorCantidad.textContent =
                        `No puede restar ${cantidad}, el stock actual es ${productoActual.stock}.`;
                    return;
                }
                if (inputMotivo.value.length > 50) {
                    showNotification('El motivo no puede superar los 50 caracteres.', 'danger');
                    return;
                }
                inputCantidad.classList.remove('is-invalid');

                isSubmitting = true;
                btnConfirmar.disabled = true;
                btnConfirmar.textContent = 'Procesando...';

                try {
                    const response = await fetch(`/productos/ajustar-kardex/${productoActual.id}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                .getAttribute('content'),
                        },
                        body: JSON.stringify({
                            tipo: productoActual.tipo,
                            cantidad: cantidad,
                            motivo: inputMotivo.value.trim() || null,
                        }),
                    });

                    const result = await response.json();

                    if (result.success) {
                        modal.hide();
                        showNotification(result.message, 'success');

                        // Actualizar stock en tabla sin recargar
                        const spanStock = document.getElementById(`stock-${productoActual.id}`);
                        if (spanStock) spanStock.textContent = result.nuevo_stock;

                        // Actualizar data-stock en los botones de esa fila
                        const fila = document.querySelector(`tr[data-id="${productoActual.id}"]`);
                        if (fila) {
                            fila.querySelectorAll('.btn-ajuste').forEach(b => {
                                b.dataset.stock = result.nuevo_stock;
                            });
                            // Actualizar también en el array local
                            productoActual.stock = parseFloat(result.nuevo_stock);
                        }
                    } else {
                        showNotification(result.message || 'Error al registrar el ajuste.', 'danger');
                    }
                } catch (error) {
                    console.error('Error:', error);
                    showNotification('Hubo un error de conexión.', 'danger');
                } finally {
                    isSubmitting = false;
                    btnConfirmar.disabled = false;
                    btnConfirmar.textContent = productoActual.tipo === 'positivo' ?
                        'Confirmar Entrada' :
                        'Confirmar Salida';
                }
            });

            // ── Notificaciones ───────────────────────────────────────────────
            function showNotification(message, type) {
                const n = document.createElement('div');
                n.className = `notification ${type}`;
                n.innerText = message;
                document.body.appendChild(n);
                setTimeout(() => n.classList.add('show'), 10);
                setTimeout(() => {
                    n.classList.remove('show');
                    n.addEventListener('transitionend', () => n.remove());
                }, 3000);
            }
        });
    </script>
@endsection
