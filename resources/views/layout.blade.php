<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="theme-color" content="#003459">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Reserva Facil</title>

    <link rel="icon" href="assets/logo.svg" type="image/x-icon">
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="{{ asset('css/bootstrap.css') }}" rel="stylesheet">
    <link href="{{ asset('css/dash_styles.css') }}" rel="stylesheet">
    <style>
        .sticky-id {
            position: sticky;
            left: 0;
            z-index: 2;
        }

        .accordion {
            --bs-accordion-btn-bg: var(--bs-primary);
            --bs-accordion-btn-color: var(--bs-white);
            --bs-accordion-active-bg: var(--bs-info);
            --bs-accordion-active-color: var(--bs-white);
            --bs-accordion-bg: var(--bs-info);
            --bs-accordion-border-color: none;
        }

        .accordion-button svg {
            height: 0.9rem;
            width: 0.9rem;
            fill: var(--bs-white);
            margin-right: 0.5rem;
        }

        .accordion-button:focus {
            box-shadow: none;
            outline: none;
            border: none;
        }

        .accordion-button:after {
            background-image: url("data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='%23ffffff'><path fill-rule='evenodd' d='M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z'/></svg>");
            transition: all 0.3s;
        }

        .accordion-button:not(.collapsed)::after {
            background-image: url("data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='%23ffffff'><path fill-rule='evenodd' d='M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z'/></svg>");
            transition: all 0.3s;
        }

        .active {
            font-weight: bold;
        }

        .content {
            flex-grow: 1;
            box-sizing: border-box;
        }

        .side_navigation {
            overflow-y: auto;
            position: sticky;
            min-width: 300px;
            top: 0;
            height: 100vh;
            display: none;
        }

        @media screen and (min-width: 768px) {

            .offcanvas-backdrop {
                display: none;
            }

            .content {
                overflow-x: auto;
            }

            .side_navigation {
                display: block;
            }
        }
    </style>
</head>

<body class="bg-light">
    @include('components.navbar')
    <div class="d-flex">
        <div class="side_navigation bg-primary">
            <?php $data = ['device' => 'Mobile']; ?>
            @include('components.navigation', $data)
        </div>
        <div class="container p-4 content">
            @yield('content')
        </div>
    </div>
    <script src="{{ asset('js/bootstrap.js') }}"></script>
    <script>
        const buttons = document.querySelectorAll('.toggle-button');

        buttons.forEach(button => {
            button.addEventListener('click', () => {
                buttons.forEach(b => b.classList.remove('active'));
                console.log('Hello worldss');
                button.classList.add('active');
            });
        });
    </script>
</body>

</html>
<script src="{{ asset('js/app.js') }}"></script>
