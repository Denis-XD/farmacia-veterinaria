<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Farmacia ALVA</title>
</head>

<body>
    <h1>Farmacia ALVA, <strong>{{ $user->nombre }}</strong>!</h1>
    <p>Ha cambiado su contraseña en el sistema de ventas Farmacia ALVA.</p>
    <p>Su correo registrado es: {{ $user->email }}</p>
    <p>Su nueva contraseña es: {{ $user->password }}</p>
</body>

</html>
