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
            <li class="breadcrumb-item"><a href="{{ route('productos.index') }}">Productos</a></li>
            <li class="breadcrumb-item active" aria-current="page">Editar Producto</li>
        </ol>
    </nav>
    <h1 class="text-center">Editar Producto {{ $producto->nombre_producto }}</h1>
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
        <form id="form-editar-producto" action="{{ route('productos.update', $producto->id_producto) }}" method="POST">
            @csrf
            @method('PUT')

            <!-- Primera fila -->
            <div class="row mb-3">
                <div class="col-md-8">
                    <label for="codigo_barra" class="form-label">Código de Barras (Opcional)</label>
                    <input type="text" name="codigo_barra" id="codigo_barra" class="form-control" maxlength="15"
                        minlength="5" value="{{ $producto->codigo_barra }}">
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="button" id="generateBarcode" class="btn btn-secondary w-100">Generar Código de
                        Barras</button>
                </div>
            </div>

            <!-- Segunda fila -->
            <div class="row mb-3">
                <div class="col-md-8">
                    <label for="nombre_producto" class="form-label">Nombre del Producto</label>
                    <input type="text" name="nombre_producto" id="nombre_producto" class="form-control" required
                        maxlength="50" minlength="4" value="{{ $producto->nombre_producto }}">
                </div>
                <div class="col-md-4">
                    <label for="unidad" class="form-label">Unidad</label>
                    <input type="text" name="unidad" id="unidad" class="form-control" required maxlength="20"
                        minlength="2" value="{{ $producto->unidad }}">
                </div>
            </div>

            <!-- Tercera fila -->
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="fecha_vencimiento" class="form-label">Fecha de Vencimiento (Opcional)</label>
                    <input type="date" name="fecha_vencimiento" id="fecha_vencimiento" class="form-control"
                        value="{{ \Carbon\Carbon::parse($producto->fecha_vencimiento)->format('Y-m-d') }}">
                </div>
                <div class="col-md-6">
                    <label for="precio_compra_actual" class="form-label">Precio de Compra</label>
                    <input type="number" name="precio_compra_actual" id="precio_compra_actual" class="form-control"
                        required min="0" step="0.01" value="{{ $producto->precio_compra_actual }}">
                </div>
            </div>

            <!-- Cuarta fila -->
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="porcentaje_utilidad" class="form-label">Porcentaje de Utilidad (%)</label>
                    <input type="number" name="porcentaje_utilidad" id="porcentaje_utilidad" class="form-control"
                        required min="0" max="100" step="0.01"
                        value="{{ $producto->porcentaje_utilidad }}">
                </div>
                <div class="col-md-6">
                    <label for="precio_venta_actual" class="form-label">Precio de Venta</label>
                    <input type="number" name="precio_venta_actual" id="precio_venta_actual" class="form-control"
                        required min="0" step="0.01" value="{{ $producto->precio_venta_actual }}">
                </div>
            </div>

            <!-- Quinta fila -->
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="stock" class="form-label">Stock</label>
                    <input type="number" name="stock" id="stock" class="form-control" required min="0"
                        value="{{ $producto->stock }}">
                </div>
                <div class="col-md-6">
                    <label for="stock_minimo" class="form-label">Stock Mínimo</label>
                    <input type="number" name="stock_minimo" id="stock_minimo" class="form-control" required
                        min="0" value="{{ $producto->stock_minimo }}">
                </div>
            </div>

            <div class="d-flex justify-content-end mt-4">
                <button type="button" id="btn-confirmar" class="btn btn-primary me-2">Guardar Cambios</button>
                <button type="button" id="btn-cancelar" class="btn btn-secondary">Cancelar</button>
            </div>
        </form>
    </div>

    <!-- Modal de confirmación -->
    <div class="modal fade" id="modalConfirmacion" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalLabel">Confirmar Cambios</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    ¿Estás seguro de que deseas guardar los cambios en el producto
                    <strong>{{ $producto->nombre_producto }}</strong>?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" id="btn-confirmar-guardar" class="btn btn-success">Confirmar</button>
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
    </style>

    <script>
        document.getElementById('generateBarcode').addEventListener('click', async () => {
            const nombreProducto = document.getElementById('nombre_producto').value;

            if (!nombreProducto) {
                showNotification(
                    'Por favor, ingrese el nombre del producto antes de generar el código de barras.',
                    'danger');
                return;
            }

            try {
                const csrfTokenMeta = document.querySelector('meta[name="csrf-token"]');
                if (!csrfTokenMeta) {
                    throw new Error('CSRF token no encontrado. Verifica que esté incluido en el HTML.');
                }

                const csrfToken = csrfTokenMeta.getAttribute('content');

                const response = await fetch('/productos/generate-barcode', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    body: JSON.stringify({
                        name: nombreProducto,
                    }),
                });

                if (!response.ok) {
                    throw new Error('Error generando el código de barras.');
                }

                const data = await response.json();
                document.getElementById('codigo_barra').value = data.barcode;

                // Descargar el PDF automáticamente
                const pdfBlob = new Blob([Uint8Array.from(atob(data.pdf_base64), c => c.charCodeAt(0))], {
                    type: 'application/pdf',
                });
                const link = document.createElement('a');
                link.href = window.URL.createObjectURL(pdfBlob);
                link.download = `${nombreProducto}-barcode.pdf`;
                link.click();

                // Notificación de éxito
                showNotification('Código de barras generado correctamente.', 'success');
            } catch (error) {
                console.error(error);
                showNotification('Error al generar el código de barras.', 'danger');
            }
        });

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

        document.getElementById('precio_compra_actual').addEventListener('input', calcularPrecioVenta);
        document.getElementById('porcentaje_utilidad').addEventListener('input', calcularPrecioVenta);

        function calcularPrecioVenta() {
            const precioCompra = parseFloat(document.getElementById('precio_compra_actual').value);
            const utilidad = parseFloat(document.getElementById('porcentaje_utilidad').value);

            if (!isNaN(precioCompra) && !isNaN(utilidad)) {
                const precioVenta = precioCompra * (1 + utilidad / 100);
                document.getElementById('precio_venta_actual').value = precioVenta.toFixed(2);
            } else {
                document.getElementById('precio_venta_actual').value = '';
            }
        }

        document.getElementById('btn-cancelar').addEventListener('click', () => {
            window.location.reload();
        });

        // Confirmar antes de guardar cambios
        document.getElementById('btn-confirmar').addEventListener('click', () => {
            const modal = new bootstrap.Modal(document.getElementById('modalConfirmacion'));
            modal.show();
        });

        // Guardar cambios al confirmar en el modal
        document.getElementById('btn-confirmar-guardar').addEventListener('click', () => {
            document.getElementById('form-editar-producto').submit();
        });
    </script>
@endsection
