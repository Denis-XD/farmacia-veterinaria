<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Compra #{{ $compra->id_compra }}</title>
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

    <h2>Compra #{{ $compra->id_compra }}</h2>
    <div class="info">
        <p><strong>Proveedor:</strong> {{ $compra->proveedor->nombre_proveedor }}</p>
        <p><strong>Fecha:</strong> {{ $compra->fecha_compra->format('d/m/Y') }}</p>
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
            @foreach ($compra->detalles as $detalle)
                <tr>
                    <td>{{ $detalle->cantidad_compra }}</td>
                    <td>{{ $detalle->producto->nombre_producto }}</td>
                    <td>Bs {{ number_format($detalle->producto->precio_compra_actual, 2) }}</td>
                    <td>Bs {{ number_format($detalle->subtotal_compra, 2) }}</td>
                </tr>
                @php $sumaSubtotales += $detalle->subtotal_compra; @endphp
            @endforeach
        </tbody>
    </table>

    <div class="totals">
        <p>Total Productos: Bs {{ number_format($sumaSubtotales, 2) }}</p>
        <p>Descuento: {{ $compra->descuento_compra }}%</p>
        <p>Total: Bs {{ number_format($compra->total_compra, 2) }}</p>
    </div>
</body>

</html>
