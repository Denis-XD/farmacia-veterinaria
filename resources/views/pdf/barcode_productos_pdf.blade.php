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
            width: 30%;
            border: 1px solid #000000;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin: 0 auto;
            page-break-inside: avoid;
            /* Evita que el elemento se divida entre páginas */
        }

        .barcode-item img {
            max-width: 100%;
            /* Asegura que las imágenes no se salgan del contenedor */
            height: auto;
        }

        .barcode-item .nombre-producto {
            font-weight: bold;
            margin-bottom: 5px;
        }

        .barcode-item .numero-codigo {
            font-size: 12px;
            color: rgb(0, 0, 0);
            margin-top: 5px;
        }

        @media print {

            /* Controla los márgenes y la altura total de la página */
            body {
                margin: 0;
                padding: 0;
            }

            .barcode-container {
                gap: 40px;
                /* Reduce el espacio entre códigos al imprimir */
            }

            .barcode-item {
                margin-bottom: 40px;
                /* Espaciado uniforme al imprimir */
            }

            /* Configuración específica para limitar a 5 códigos por hoja */
            @page {
                size: A4;
                /* Tamaño carta */
                margin: 20mm;
                /* Márgenes de la página */
            }

            .barcode-container {
                display: grid;
                grid-template-rows: repeat(5, auto);
                /* Asegura 5 filas por hoja */
                grid-auto-flow: row;
            }
        }
    </style>
</head>

<body>
    <h2 style="text-align: center; font-family: Arial, sans-serif;">Códigos de Barra</h2>
    <div class="barcode-container">
        @foreach ($productosActualizados as $producto)
            <div class="barcode-item">
                <p class="nombre-producto">{{ $producto->nombre_producto }}</p>
                <img src="data:image/png;base64,{{ DNS1D::getBarcodePNG($producto->codigo_barra, 'C128', 1, 50) }}"
                    alt="Código de Barra">
                <p class="numero-codigo">{{ $producto->codigo_barra }}</p>
            </div>
        @endforeach
    </div>
</body>

</html>
