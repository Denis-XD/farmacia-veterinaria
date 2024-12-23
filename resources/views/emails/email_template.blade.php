<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenido a Reserva Fácil</title>
</head>

<body>
    <h1>Bienvenido a Reserva Fácil, <strong>{{ $user->nombre }} {{ $user->apellido }}</strong>!</h1>
    <p>Fuiste registrado al sistema de reservas de ambientes de la FCYT de la UMSS.</p>
    <p>Tu correo registrado es: {{ $user->email }}</p>
    <p>Tu contraseña es: {{ $user->password }}</p>
</body>

</html>
