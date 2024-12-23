<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>No autorizado</title>
    <link rel="stylesheet" href="{{ asset('css/error_styles.css') }}">
    <link href="{{ asset('css/bootstrap.css') }}" rel="stylesheet">
</head>

<body>
    <div class="d-flex vh-100 align-items-center justify-content-center"
        style="background-image: url(/assets/bg.jpg); background-position: center; background-size: cover;">
        <div
            class="d-flex flex-column gap-2 align-items-center justify-content-center glass_card text-white text-center">
            <h2 class="fs-1">Reserva Fácil</h2>
            <div class="error">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-ban" width="72"
                    height="72" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none"
                    stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                    <path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" />
                    <path d="M5.7 5.7l12.6 12.6" />
                </svg>
                <p>Error 403</p>
            </div>
            <p class="fs-3">No tiene permisos para acceder a esta sección.</p>
            <p class="fs-5">Si cree que es un error, póngase en contacto con un administrador.</p>
            <a href="{{ url('') }}" class="btn-go-to-home">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-smart-home"
                    viewBox="0 0 24 24" stroke-width="1.5" width="24" height="24" stroke="currentColor"
                    fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                    <path d="M5 12l-2 0l9 -9l9 9l-2 0" />
                    <path d="M5 12v7a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-7" />
                    <path d="M9 21v-6a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v6" />
                </svg>Volver al inicio</a>
        </div>
    </div>
    <script src="{{ asset('js/bootstrap.js') }}"></script>
</body>

</html>
