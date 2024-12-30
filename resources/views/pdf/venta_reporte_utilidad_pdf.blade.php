<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Utilidad</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        table th,
        table td {
            border: 1px solid #000;
            padding: 5px;
            text-align: center;
        }

        .totales {
            margin-bottom: 20px;
        }

        .totales p {
            margin: 5px 0;
        }
    </style>
</head>

<body>
    <div class="header">
        <h3>Reporte de Utilidad</h3>
        <p><strong>Filtros Aplicados:</strong></p>
        <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px; font-size: 12px;">
            <tr>
                <td><strong>Crédito:</strong> {{ $filtros['credito'] }}</td>
                <td><strong>Servicio:</strong> {{ $filtros['servicio'] }}</td>
                <td><strong>Finalizada:</strong> {{ $filtros['finalizada'] }}</td>
            </tr>
            <tr>
                <td><strong>Orden:</strong> {{ $filtros['orden'] }}</td>
                <td>
                    <strong>Fecha específica:</strong> {{ !empty($filtros['fecha']) ? $filtros['fecha'] : '' }}
                </td>
                <td>
                    <strong>Fecha desde:</strong> {{ !empty($filtros['fecha_desde']) ? $filtros['fecha_desde'] : '' }}
                </td>
            </tr>
            <tr>
                <td>
                    <strong>Fecha hasta:</strong> {{ !empty($filtros['fecha_hasta']) ? $filtros['fecha_hasta'] : '' }}
                </td>
                <td>
                    <strong>Socio:</strong> {{ !empty($filtros['socio']) ? $filtros['socio'] : '' }}
                </td>
                <td></td>
            </tr>
        </table>
    </div>
    <div class="totales">
        <p>Total de Ventas: <strong>Bs
                {{ number_format($ventas->sum(fn($venta) => $venta->detalles->sum('subtotal_venta')), 2) }}</strong></p>
        <p>Total Costo de Ventas: <strong>Bs
                {{ number_format($ventas->sum(fn($venta) => $venta->detalles->sum(fn($detalle) => $detalle->producto->precio_compra * $detalle->cantidad_venta)), 2) }}</strong>
        </p>
        <p>Total de Utilidad Bruta: <strong>Bs
                {{ number_format($ventas->sum(fn($venta) => $venta->detalles->sum(fn($detalle) => ($detalle->producto->precio_venta_actual - $detalle->producto->precio_compra) * $detalle->cantidad_venta)), 2) }}</strong>
        </p>
    </div>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Cantidad</th>
                <th>Unidad</th>
                <th>Descripción</th>
                <th>Efectivo</th>
                <th>Crédito</th>
                <th>Total Ventas</th>
                <th>Costo de Ventas</th>
                <th>Utilidad Bruta</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($ventas as $venta)
                @foreach ($venta->detalles as $detalle)
                    <tr>
                        <td>{{ $detalle->producto->id_producto }}</td>
                        <td>{{ $detalle->cantidad_venta }}</td>
                        <td>{{ $detalle->producto->unidad }}</td>
                        <td>{{ $detalle->producto->nombre_producto }}</td>
                        <td>{{ $venta->credito ? 'No' : 'Sí' }}</td>
                        <td>{{ $venta->credito ? 'Sí' : 'No' }}</td>
                        <td>Bs
                            {{ number_format($detalle->producto->precio_venta_actual * $detalle->cantidad_venta, 2) }}
                        </td>
                        <td>Bs {{ number_format($detalle->producto->precio_compra * $detalle->cantidad_venta, 2) }}
                        </td>
                        <td>Bs
                            {{ number_format(($detalle->producto->precio_venta_actual - $detalle->producto->precio_compra) * $detalle->cantidad_venta, 2) }}
                        </td>
                    </tr>
                @endforeach
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th colspan="6">TOTALES</th>
                <th>Bs {{ number_format($ventas->sum(fn($venta) => $venta->detalles->sum('subtotal_venta')), 2) }}</th>
                <th>Bs
                    {{ number_format($ventas->sum(fn($venta) => $venta->detalles->sum(fn($detalle) => $detalle->producto->precio_compra * $detalle->cantidad_venta)), 2) }}
                </th>
                <th>Bs
                    {{ number_format($ventas->sum(fn($venta) => $venta->detalles->sum(fn($detalle) => ($detalle->producto->precio_venta_actual - $detalle->producto->precio_compra) * $detalle->cantidad_venta)), 2) }}
                </th>
            </tr>
        </tfoot>
    </table>

</body>

</html>
