<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reserva Fácil</title>
</head>

<body>
    <h1>Hola, <strong>{{ $user->nombre }} {{ $user->apellido }}</strong></h1>
    <p>Lamentamos informarte que tu solicitud de reserva ha sido <strong>rechazada</strong>.</p>
    <p>Detalles de la reserva:</p>
    <p><strong>Ambiente(s):</strong>
        @if ($reserva->generico)
            Sin asignar ({{ $reserva->tipoAmbiente->nombre }})
        @else
            @foreach ($reserva->ambientes as $ambiente)
                {{ $loop->first ? '' : ', ' }}
                {{ $ambiente->nombre }}
            @endforeach
            ({{ $ambiente->tipo->nombre }})
        @endif
    </p>
    <p><strong>Ubicación:</strong>
        @if ($reserva->generico)
            -
        @else
            {{ $reserva->ambientes->first()->ubicacion->nombre }}
        @endif
    </p>
    <p><strong>Fecha de reserva:</strong> {{ $reserva->fecha_reserva }}</p>
    <p><strong>Fecha de solicitud:</strong> {{ $reserva->fecha_solicitud }}</p>
    <p><strong>Hora de reserva:</strong>
        @foreach ($reserva->reservasPeriodos as $reservaPeriodo)
            {{ $loop->first ? '' : ', ' }}
            {{ $reservaPeriodo->periodo->inicio }} - {{ $reservaPeriodo->periodo->fin }}
        @endforeach
    </p>
    <p><strong>Motivo del rechazo:</strong> {{ $motivo }}</p>
    <p>Gracias, Reserva Fácil.</p>
</body>

</html>
