<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Códigos de Barra</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 14px;
            margin: 0;
            padding: 20px;
            text-align: center;
        }

        h2 {
            text-align: center;
            margin-bottom: 30px;
        }

        .barcode-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 60px;
            /* Mayor separación entre los códigos */
        }

        .barcode-item {
            text-align: center;
            width: 250px;
            /* Tamaño más pequeño */
            border: 1px solid #ddd;
            padding: 15px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin: 0 auto;
        }

        .barcode-item img {
            width: 70%;
            /* Más pequeño horizontalmente */
            max-height: 60px;
            /* Más pequeño verticalmente */
            object-fit: contain;
            margin: 10px auto;
        }

        .barcode-item p {
            margin: 8px 0;
            font-weight: bold;
        }

        .barcode-item .nombre-producto {
            font-size: 18px;
            /* Nombre ligeramente más grande */
        }

        .barcode-item .numero-codigo {
            font-size: 14px;
            /* Código ligeramente más pequeño */
        }
    </style>
</head>

<body>
    <h2 style="text-align: center; font-family: Arial, sans-serif;">Códigos de Barra</h2>
    <div class="barcode-container">
        @foreach ($productosActualizados as $producto)
            <div class="barcode-item" style="margin-bottom: 10px;">
                <p class="nombre-producto">{{ $producto->nombre_producto }}</p>
                <img src="data:image/png;base64,{{ DNS1D::getBarcodePNG($producto->codigo_barra, 'C128', 1.2, 30) }}"
                    alt="Código de Barra">
                <p class="numero-codigo">{{ $producto->codigo_barra }}</p>
            </div>
        @endforeach
    </div>
</body>

</html>
