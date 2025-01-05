<!DOCTYPE html>
<html>

<head>
    <title>Inventario</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
        }

        h2 {
            text-align: center;
        }

        .details {
            margin-top: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table,
        th,
        td {
            border: 1px solid black;
        }

        th,
        td {
            padding: 5px;
            text-align: left;
        }

        .totals {
            margin-top: 20px;
        }

        .totals table {
            width: auto;
            margin: 0 auto;
        }
    </style>
</head>

<body>
    <h2>Inventario de Productos</h2>
    <div class="details">
        <h4>Fecha Inicio: {{ $fechaInicio }}</h4>
        <h4>Fecha Fin: {{ $fechaFin }}</h4>
        <h4>Total valor: Bs {{ number_format($totalValor, 2) }}</h4>
    </div>
    <table>
        <thead>
            <tr>
                <th>Nº</th>
                <th>Descripción</th>
                <th>Unidad</th>
                <th>Cantidad</th>
                <th>C/U</th>
                <th>Valor</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($inventario as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item['descripcion'] }}</td>
                    <td>{{ $item['unidad'] }}</td>
                    <td>{{ $item['cantidad'] }}</td>
                    <td>Bs {{ number_format($item['precio_unitario'], 2) }}</td>
                    <td>Bs {{ number_format($item['valor'], 2) }}</td>
                </tr>
            @endforeach
            <!-- Fila de Totales -->
            <tr>
                <td colspan="3" style="text-align: center; font-weight: bold;">TOTALES</td>
                <td>{{ $inventario->sum('cantidad') }}</td>
                <td>Bs {{ number_format($inventario->sum('precio_unitario'), 2) }}</td>
                <td>Bs {{ number_format($totalValor, 2) }}</td>
            </tr>
        </tbody>
    </table>
</body>

</html>
