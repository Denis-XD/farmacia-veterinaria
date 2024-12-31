<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#003459">
    <title>Inicio de sesión</title>

    <link rel="icon" href="assets/logo.jpeg" type="image/x-icon">

    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="{{ asset('css/bootstrap.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/styles.css') }}">

    <style>
        body {
            margin: 0;
            font-family: 'Nunito', sans-serif;
        }

        .container__login {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color:#56E59A; /* Fondo verde */
        }

        .card__login {
            display: flex;
            width: 90%;
            max-width: 900px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            overflow: hidden;
            background-color: #fff; /* Fondo blanco de la tarjeta */
        }

        .card__login-lefttop {
            position: relative;
            width: 50%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            background-color: #047031; /* Fondo verde más oscuro */
            color: white;
            padding: 20px;
        }

        .card__login-lefttop img {
            width: 100px;
            height: auto;
            margin-bottom: 15px;
        }

        .card__login-rightbottom {
            width: 50%;
            padding: 30px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }

        .card__login-rightbottom h1 {
            margin-bottom: 20px;
            color: #333;
        }

        .btn-primary {
            background-color: #27ae60;
            border-color: #27ae60;
        }

        .btn-primary:hover {
            background-color: #2ecc71;
            border-color: #2ecc71;
        }

        @media (max-width: 768px) {
            .card__login {
                flex-direction: column;
            }

            .card__login-lefttop,
            .card__login-rightbottom {
                width: 100%;
            }
        }
    </style>
</head>

<body>
    <div class="container__login">
        <div class="card__login">
            <div class="card__login-lefttop">
                <img class="card__login--background" src="{{ asset('assets/logo.svg') }}" alt="">
                <h1>Bienvenido</h1>
                <p>Asociación de Lecheros del Valle Alto.</p>
                <p class="text-center">Inicia sesión para continuar <span><svg xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 512 512" width="20" height="20">
                            <path
                                d="M334.5 414c8.8 3.8 19 2 26-4.6l144-136c4.8-4.5 7.5-10.8 7.5-17.4s-2.7-12.9-7.5-17.4l-144-136c-7-6.6-17.2-8.4-26-4.6s-14.5 12.5-14.5 22l0 72L32 192c-17.7 0-32 14.3-32 32l0 64c0 17.7 14.3 32 32 32l288 0 0 72c0 9.6 5.7 18.2 14.5 22z" />
                        </svg></span></p>
            </div>
            <div class="card__login-rightbottom">
                <h1 class="text-center">Iniciar sesión</h1>
                <form class="w-100" method="POST" action="{{ route('users.login') }}">
                    @csrf
                    <div class="mb-3">
                        <label for="email" class="form-label">Correo electrónico</label>
                        <input type="email" class="form-control form__email" id="email" name="email"
                            placeholder="Correo electrónico" maxlength="50" required>
                        @error('email')
                            <div class="invalid-feedback d-block w-100 mt-2" role="alert">
                                <strong>{{ $message }}</strong>
                            </div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Contraseña</label>
                        <input type="password" class="form-control form__password" id="password" name="password"
                            placeholder="Contraseña" required>
                        @error('password')
                            <span class="invalid-feedback d-block w-100 mt-2" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <button type="submit" class="btn btn-primary">Iniciar sesión</button>
                </form>
            </div>
        </div>
    </div>

    <script src="{{ asset('js/bootstrap.js') }}"></script>
</body>

</html>
