<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Inicio</title>
    <style>
        #pie__ACEPTADO::before {
            background: conic-gradient(#198754 {{ $num_solicitudes ? round(($reservas_por_estado['ACEPTADO']->count() / $num_solicitudes) * 100) : 0 }}%,
                    #ffffff 0 100%);
        }

        #pie__PENDIENTE::before {
            background: conic-gradient(#ffc107 {{ $num_solicitudes ? round(($reservas_por_estado['PENDIENTE']->count() / $num_solicitudes) * 100) : 0 }}%,
                    #ffffff 0 100%);
        }

        #pie__RECHAZADO::before {
            background: conic-gradient(#dc3545 {{ $num_solicitudes ? round(($reservas_por_estado['RECHAZADO']->count() / $num_solicitudes) * 100) : 0 }}%,
                    #ffffff 0 100%);
        }

        #pie__CANCELADO::before {
            background: conic-gradient(#dc3545 {{ $num_solicitudes ? round(($reservas_por_estado['CANCELADO']->count() / $num_solicitudes) * 100) : 0 }}%,
                    #ffffff 0 100%);
        }

        #pie__TOTAL::before {
            background: conic-gradient(#003459 100%,
                    #ffffff 0 100%);
        }
    </style>
</head>

<body>
    @extends('layout')
    @section('content')
        <div class="">
            <h2 class="text-center">Inicio</h2>
        </div>

        <div class="dash__container py-3">
            <div class="dash__container--dash">

                @include('components.dashboard_card', [
                    'title' => 'PENDIENTE',
                    'color' => 'warning',
                    'count' => $reservas_por_estado['PENDIENTE']->count(),
                    'num_solicitudes' => $num_solicitudes,
                ])
                @include('components.dashboard_card', [
                    'title' => 'ACEPTADO',
                    'color' => 'success',
                    'count' => $reservas_por_estado['ACEPTADO']->count(),
                    'num_solicitudes' => $num_solicitudes,
                ])
                @include('components.dashboard_card', [
                    'title' => 'RECHAZADO',
                    'color' => 'danger',
                    'count' => $reservas_por_estado['RECHAZADO']->count(),
                    'num_solicitudes' => $num_solicitudes,
                ])
                @include('components.dashboard_card', [
                    'title' => 'CANCELADO',
                    'color' => 'danger',
                    'count' => $reservas_por_estado['CANCELADO']->count(),
                    'num_solicitudes' => $num_solicitudes,
                ])
                @include('components.dashboard_card', [
                    'title' => 'TOTAL',
                    'color' => 'primary',
                    'count' => $num_solicitudes,
                    'num_solicitudes' => $num_solicitudes,
                ])
            </div>
            <div class="dash__container--details">
                <div class="shadow overflow-hidden rounded-2 w-100">
                    <div class="p-2 bg-primary text-white">
                        <p class="m-0">Detalles</p>
                    </div>
                    <div class="p-4">
                        @can('materia_listar')
                            @include('components.dashboard_detail_item', [
                                'path' => 'materias',
                                'title' => 'Materias',
                                'count' => $materias->count(),
                            ])
                        @endcan
                        @can('ambiente_listar')
                            @include('components.dashboard_detail_item', [
                                'path' => 'ambientes',
                                'title' => 'Ambientes',
                                'count' => $ambientes->count(),
                            ])
                        @endcan
                        @can('grupo_listar')
                            @include('components.dashboard_detail_item', [
                                'path' => 'grupos',
                                'title' => 'Grupos',
                                'count' => $grupos->count(),
                            ])
                        @endcan
                        @can('carrera_listar')
                            @include('components.dashboard_detail_item', [
                                'path' => 'carreras',
                                'title' => 'Carreras',
                                'count' => $carreras->count(),
                            ])
                        @endcan
                        @can('tipo_ambiente_listar')
                            @include('components.dashboard_detail_item', [
                                'path' => 'tipos_ambiente',
                                'title' => 'Tipos',
                                'count' => $tipos->count(),
                            ])
                        @endcan
                        @can('ubicacion_listar')
                            @include('components.dashboard_detail_item', [
                                'path' => 'ubicaciones',
                                'title' => 'Ubicaciones',
                                'count' => $ubicaciones->count(),
                            ])
                        @endcan
                        @can('usuario_listar')
                            @include('components.dashboard_detail_item', [
                                'path' => 'usuarios',
                                'title' => 'Usuarios',
                                'count' => $usuarios,
                            ])
                        @endcan
                        @can('rol_listar')
                            @include('components.dashboard_detail_item', [
                                'path' => 'roles',
                                'title' => 'Roles',
                                'count' => $roles,
                            ])
                        @endcan
                        @can('permiso_listar')
                            @include('components.dashboard_detail_item', [
                                'path' => 'permisos',
                                'title' => 'Permisos',
                                'count' => $permisos,
                            ])
                        @endcan
                    </div>
                </div>
            </div>
        </div>
        <div class="p-2 rounded border border-info">
            <div>
                <h3 class="d-flex justify-content-start gap-2">
                    <span class="p-1 rounded shadow bg-primary text-light d-flex">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-info-circle"
                            width="32" height="32" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                            fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0" />
                            <path d="M12 9h.01" />
                            <path d="M11 12h1v4h1" />
                        </svg>
                    </span>
                    Reglamento
                </h3>
                @if($ultimasReglas)
                    <p>Bienvenidos. Para garantizar un uso eficiente de nuestros ambientes, a continuación te presentamos las reglas y políticas para realizar reservas:</p>
                    <ul>
                        <li>Fecha de inicio de solicitudes de reserva: {{ \Carbon\Carbon::parse($ultimasReglas->fecha_inicio)->format('d/m/Y') }}</li>
                        <li>Fecha de inicio de solicitudes de reserva: {{ \Carbon\Carbon::parse($ultimasReglas->fecha_final)->format('d/m/Y') }}</li>
                        <li>Período de atención a reclamos u observaciones posteriores: {{ $ultimasReglas->atencion_posterior }} días después del plazo de reservas</li>
                        <li>Horario de atención de administradores: de {{ \Carbon\Carbon::parse($ultimasReglas->atencion_inicio)->format('H:i') }} a {{ \Carbon\Carbon::parse($ultimasReglas->atencion_final)->format('H:i') }} </li>
                        <li>Número máximo de reservas de auditorios por día: Se permite un máximo de {{ $ultimasReglas->reservas_auditorio }} reservas por usuario para el mismo auditorio en un mismo día.</li>
                    </ul>
                    
                    @if($ultimasReglas && $ultimasReglas->mas_reglas)
                        <p>Otras Reglas importantes:</p>
                        <ul>
                            @php
                                $reglasArray = explode("\n", $ultimasReglas->mas_reglas);
                            @endphp
                            @foreach($reglasArray as $regla)
                                <li>{{ $regla }}</li>
                            @endforeach
                        </ul>
                    @endif
                @else
                <p>Bienvenido. En este momento, las reglas aún están siendo configuradas. Por favor, tenga paciencia o contacte con un administrador para obtener más información.</p>
                @endif
                
            </div>
        </div>
    @endsection
</body>

</html>
