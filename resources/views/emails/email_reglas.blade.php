<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reglamento</title>
</head>
<body>
    <h1>Hola, <strong>{{ $user->nombre }} {{ $user->apellido }} </strong></h1>
    <p>queríamos informarte que recientemente se ha actualizado el reglamento de reservas. A continuación, te presentamos el texto completo del nuevo reglamento y políticas actualizado:</p>
    <h3 >Reglamento</h3>
        <ul>
            <li>Fecha de inicio de solicitudes de reserva: {{ \Carbon\Carbon::parse($reglas->fecha_inicio)->format('d/m/Y') }}</li>
            <li>Fecha de inicio de solicitudes de reserva: {{ \Carbon\Carbon::parse($reglas->fecha_final)->format('d/m/Y') }}</li>
            <li>Período de atención a reclamos u observaciones posteriores: {{ $reglas->atencion_posterior }} días después del plazo de reservas</li>
            <li>Horario de atención de administradores: de {{ \Carbon\Carbon::parse($reglas->atencion_inicio)->format('H:i') }} a {{ \Carbon\Carbon::parse($reglas->atencion_final)->format('H:i') }} </li>
            <li>Número máximo de reservas de auditorios por día: Se permite un máximo de {{ $reglas->reservas_auditorio }} reservas por usuario para el mismo auditorio en un mismo día.</li>
        </ul>
        
        @if($reglas && $reglas->mas_reglas)
            <p>Otras Reglas importantes:</p>
            <ul>
                @php
                    $reglasArray = explode("\n", $reglas->mas_reglas);
                @endphp
                @foreach($reglasArray as $regla)
                    <li>{{ $regla }}</li>
                @endforeach
            </ul>
        @endif
</body>
</html>