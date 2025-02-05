<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Venta #{{ $venta->id_venta }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            margin: 5px;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #000;
            padding-bottom: 2px;
        }

        .logo {
            width: 60px;
        }

        h1,
        h2 {
            text-align: center;
            margin: 3px 0;
            font-size: 11px;
        }

        .info p {
            margin: 2px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 3px;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 3px;
            text-align: center;
        }

        th {
            background-color: #f2f2f2;
            font-size: 10px;
        }

        td {
            font-size: 9px;
        }

        .totals {
            text-align: right;
            font-weight: bold;
            margin-top: 3px;
        }
    </style>
</head>

<body>
    <div class="header">
        <img src="{{ public_path('assets/logo.jpeg') }}" alt="Logo" class="logo">
        <h1>Farmacia Veterinaria ALVA</h1>
    </div>

    <h2>Nota de entrega: {{ $venta->id_venta }}</h2>
    <div class="info">
        <p><strong>Atendido por:</strong> {{ $venta->usuario->nombre }}</p>
        <p><strong>Socio:</strong> {{ $venta->socio->nombre_socio ?? 'Sin Socio' }}</p>
        <p><strong>Fecha:</strong> {{ $venta->fecha_venta->format('d/m/Y') }}</p>
        <p><strong>Total: Bs</strong> {{ number_format($venta->total_venta, 2) }}</p>
    </div>

    <h2>Productos</h2>
    <table>
        <thead>
            <tr>
                <th>CANT.</th>
                <th>DESCRIPCIÓN</th>
                <th>PRECIO U.</th>
                <th>SUBTOTAL</th>
            </tr>
        </thead>
        <tbody>
            @php $sumaSubtotales = 0; @endphp
            @foreach ($venta->detalles as $detalle)
                <tr>
                    <td>{{ $detalle->cantidad_venta }}</td>
                    <td>{{ $detalle->producto->nombre_producto }}</td>
                    <td>Bs {{ number_format($detalle->producto->precio_venta_actual, 2) }}</td>
                    <td>Bs {{ number_format($detalle->subtotal_venta, 2) }}</td>
                </tr>
                @php $sumaSubtotales += $detalle->subtotal_venta; @endphp
            @endforeach
        </tbody>
    </table>

    <div class="totals">
        <p>Total Productos: Bs {{ number_format($sumaSubtotales, 2) }}</p>
    </div>

    <h2>Pagos</h2>
    <table>
        <thead>
            <tr>
                <th>FECHA</th>
                <th>MONTO</th>
                <th>SALDO</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($venta->pagos as $pago)
                <tr>
                    <td>{{ $pago->fecha_pago->format('d/m/Y') }}</td>
                    <td>Bs {{ number_format($pago->monto_pagado, 2) }}</td>
                    <td>Bs {{ number_format($pago->saldo_pendiente, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>
