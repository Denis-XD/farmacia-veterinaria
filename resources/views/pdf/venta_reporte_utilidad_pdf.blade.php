<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
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

        /* ✅ Tabla de filtros separada con su propio estilo */
        .tabla-filtros {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 11px;
        }

        .tabla-filtros td {
            padding: 3px 6px;
            border: 1px solid #ccc;
            text-align: left;
        }

        /* Tabla principal de datos */
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
        <h3>Reporte de Utilidad ({{ \Carbon\Carbon::now()->format('d/m/Y H:i:s') }})</h3>
    </div>

    {{-- ✅ Filtros dentro de una tabla válida, sin <td> huérfanos --}}
    <p><strong>Filtros Aplicados:</strong></p>
    @if (count($filtrosLegibles) > 0)
        <table class="tabla-filtros">
            @foreach ($filtrosLegibles->chunk(3) as $fila)
                <tr>
                    @foreach ($fila as $nombre => $valor)
                        <td><strong>{{ $nombre }}:</strong> {{ $valor }}</td>
                    @endforeach
                    {{-- Rellenar celdas vacías si la fila no tiene 3 elementos --}}
                    @for ($i = count($fila); $i < 3; $i++)
                        <td></td>
                    @endfor
                </tr>
            @endforeach
        </table>
    @else
        <p>No se aplicaron filtros.</p>
    @endif

    <div class="totales">
        <p>Total Efectivo: <strong>Bs {{ number_format($totalEfectivo, 2) }}</strong></p>
        <p>Total Crédito: <strong>Bs {{ number_format($totalCredito, 2) }}</strong></p>
        <p>Total Ventas: <strong>Bs {{ number_format($totalVentas, 2) }}</strong></p>
        <p>Total Costo de Ventas: <strong>Bs {{ number_format($totalCosto, 2) }}</strong></p>
        <p>Total Utilidad Bruta: <strong>Bs {{ number_format($totalUtilidad, 2) }}</strong></p>
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
                        <td>Bs {{ number_format($detalle->efectivo, 2) }}</td>
                        <td>Bs {{ number_format($detalle->monto_credito, 2) }}</td>
                        <td>Bs {{ number_format($detalle->subtotal_venta, 2) }}</td>
                        <td>Bs {{ number_format($detalle->subtotal_costo, 2) }}</td>
                        <td>Bs {{ number_format($detalle->subtotal_utilidad, 2) }}</td>
                    </tr>
                @endforeach
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th colspan="4">TOTALES</th>
                <th>Bs {{ number_format($totalEfectivo, 2) }}</th>
                <th>Bs {{ number_format($totalCredito, 2) }}</th>
                <th>Bs {{ number_format($totalVentas, 2) }}</th>
                <th>Bs {{ number_format($totalCosto, 2) }}</th>
                <th>Bs {{ number_format($totalUtilidad, 2) }}</th>
            </tr>
        </tfoot>
    </table>

</body>
</html>