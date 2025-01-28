<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Compra {{ $compra->id_compra }}</title>
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
    <h2>Numero de compra: {{ $compra->id_compra }}</h2>
    <div class="header-left">
        <p><strong>Proveedor:</strong> {{ $compra->proveedor->nombre_proveedor }}</p>
        <p><strong>Fecha:</strong> {{ $compra->fecha_compra->format('d/m/Y') }}</p>
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
        <p>Total suma: Bs {{ number_format($sumaSubtotales, 2) }}</p>
        <p>Descuento: {{ $compra->descuento_compra }}%</p>
        <p>Total: Bs {{ number_format($compra->total_compra, 2) }}</p>
    </div>
</body>

</html>
