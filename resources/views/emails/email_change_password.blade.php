<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reserva Fácil</title>
</head>

<body>
    <h1>Reserva Fácil, <strong>{{ $user->nombre }} {{ $user->apellido }}</strong>!</h1>
    <p>Ha cambiado su contraseña en el sistema de reservas de ambientes de la FCYT de la UMSS.</p>
    <p>Su correo registrado es: {{ $user->email }}</p>
    <p>Su nueva contraseña es: {{ $user->password }}</p>
</body>

</html>
