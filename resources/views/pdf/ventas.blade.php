<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Venta {{ $venta->id_venta }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            width: 100px;
            height: auto;
        }

        .header-left {
            font-size: 14px;
        }

        h1,
        h2 {
            text-align: center;
            margin: 10px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table th,
        table td {
            border: 1px solid #000;
            padding: 8px;
            text-align: center;
        }

        table th {
            background-color: #f2f2f2;
        }

        .totals {
            margin-top: 10px;
            text-align: right;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <div class="header">
        <div>
            <img src="{{ public_path('assets/logo.jpeg') }}" alt="Logo" class="logo">
        </div>
        <div>
            <h1>Farmacia Veterinaria ALVA</h1>
        </div>
    </div>

    <h2>Numero de venta: {{ $venta->id_venta }}</h2>
    <div class="header-left">
        <p><strong>Atendido por:</strong> {{ $venta->usuario->nombre }}</p>
        <p><strong>Socio:</strong> {{ $venta->socio->nombre_socio ?? 'Sin Socio' }}</p>
        <p><strong>Fecha:</strong> {{ $venta->fecha_venta->format('d/m/Y') }}</p>
        <p><strong>Total Venta: Bs</strong> {{ number_format($venta->total_venta, 2) }}</p>
    </div>

    <h2>Detalles de Productos</h2>
    <table>
        <thead>
            <tr>
                <th>CANT.</th>
                <th>DESCRIPCIÓN</th>
                <th>PRECIO UNITARIO</th>
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
                <th>SALDO PENDIENTE</th>
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
