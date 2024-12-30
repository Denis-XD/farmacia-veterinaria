<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Código de Barras</title>
</head>
<body style="text-align: center; font-family: Arial, sans-serif;">
    <h1>{{ $nombre }}</h1>

    <!-- Código de barras grande -->
    <div style="margin-bottom: 20px;">
        <img src="data:image/png;base64,{{ DNS1D::getBarcodePNG($barcode, 'C128', 2, 100) }}" alt="Código de barras grande">
        <p>{{ $barcode }}</p>
    </div>

    <!-- Código de barras mediano -->
    <div style="margin-bottom: 20px;">
        <img src="data:image/png;base64,{{ DNS1D::getBarcodePNG($barcode, 'C128', 1.5, 80) }}" alt="Código de barras mediano">
        <p>{{ $barcode }}</p>
    </div>

    <!-- Código de barras pequeño -->
    <div>
        <img src="data:image/png;base64,{{ DNS1D::getBarcodePNG($barcode, 'C128', 1, 50) }}" alt="Código de barras pequeño">
        <p>{{ $barcode }}</p>
    </div>
</body>
</html>
