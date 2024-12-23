<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenido a Farmacia ALVA</title>
</head>

<body>
    <h1>Bienvenido a Farmacia ALVA, <strong>{{ $user->nombre }}</strong>!</h1>
    <p>Fuiste registrado al sistema de ventas, Farmacia ALVA.</p>
    <p>Tu correo registrado es: {{ $user->email }}</p>
    <p>Tu contraseña es: {{ $user->password }}</p>
</body>

</html>
