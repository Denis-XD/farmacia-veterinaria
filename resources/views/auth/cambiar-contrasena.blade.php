@extends('layout')

@section('content')
    <div class="">
        <h2 class="text-center">Cambiar contraseña</h2>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {!! session('error') !!}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <div class="container mt-4">
            <div class="row justify-content-center">
                <div class="col-lg-6 col-md-12 overflow-hidden">
                    <form method="POST"
                        action="{{ route('cambiar_contrasena.update', ['cambiar_contrasena' => Auth::user()->id]) }}"
                        onsubmit="document.getElementById('btn_send').disabled = 1;">
                        @method('PATCH')
                        @csrf

                        <!-- Campo Contraseña Actual -->
                        <div class="mb-3">
                            <label for="current_password" class="form-label">Contraseña Actual</label>
                            <input type="password" class="form-control" id="current_password" name="current_password"
                                minlength="10" maxlength="15" pattern="^\S+$"
                                oninput="this.value = this.value.replace(/\s/g, '').substring(0, 15)" required>
                        </div>

                        <!-- Campo Nueva Contraseña -->
                        <div class="mb-3">
                            <label for="new_password" class="form-label">Nueva Contraseña</label>
                            <input type="password" class="form-control" id="new_password" name="new_password" minlength="10"
                                maxlength="15" pattern="^\S+$"
                                oninput="this.value = this.value.replace(/\s/g, '').substring(0, 15)" required>
                            <small class="form-text text-muted">La nueva contraseña debe cumplir con los siguientes
                                requisitos:</small>
                            <small class="form-text text-muted">
                                <ul class="">
                                    <li id="validation_max" class="" style="font-weight: 600;">Máximo 15 caracteres.
                                    </li>
                                    <li id="validation_min" class="" style="font-weight: 600;">Mínimo 10 caracteres.
                                    </li>
                                    <li id="validation_spaces" class="text-success" style="font-weight: 600;">No se admiten
                                        espacios.
                                    </li>
                                </ul>
                            </small>
                        </div>

                        <!-- Campo Repetir Contraseña -->
                        <div class="mb-3 position-relative">
                            <label for="rep_password" class="form-label">Repetir Contraseña</label>
                            <input type="password" class="form-control position-relative" style="z-index: 10;"
                                id="rep_password" name="rep_password" minlength="10" maxlength="15" pattern="^\S+$"
                                oninput="this.value = this.value.replace(/\s/g, '').substring(0, 15)" required>
                            <small id="password_match_error" class="form-text text-danger position-relative"
                                style="font-weight:600; left: -15rem; transition: left 0.3s ease-in;">Las
                                contraseñas no coinciden.</small>
                        </div>

                        <!-- Botón de Envío -->
                        <div>
                            <a class="btn btn-secondary" href="/">Cancelar</a>
                            <button id="btn_send" type="submit" class="btn btn-primary disabled">Aceptar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
@endsection

<script>
    // Validación de contraseñas
    /*document.getElementById('btn_send').addEventListener('click', function(e) {
        //e.preventDefault();
        let current_password = document.getElementById('current_password').value;
        let new_password = document.getElementById('new_password').value;
        let rep_password = document.getElementById('rep_password').value;

        if (new_password != rep_password) {
            alert('Las contraseñas no coinciden');
            return;
        }

        if (current_password == new_password) {
            alert('La nueva contraseña no puede ser igual a la actual');
            return;
        }

        let regex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d]{10,15}$/;
        if (!regex.test(new_password)) {
            alert('La nueva contraseña no cumple con los requisitos');
            return;
        }

        document.querySelector('form').submit();
    });*/
    window.onload = function() {
        const newPassword = document.getElementById('new_password');
        const repPassword = document.getElementById('rep_password');
        const btnSend = document.getElementById('btn_send');
        const valMax = document.getElementById('validation_max');
        const valMin = document.getElementById('validation_min');
        const pwdMatchError = document.getElementById('password_match_error');

        /*btnSend.addEventListener('click', function() {
            //event.preventDefault();
            let form = document.querySelector('form');
            let formData = new FormData(form);
            console.log(Object.fromEntries(formData.entries()));
        });*/

        let regexMax = /^.{10,15}$/;
        let regexMin = /^.{10,}$/;
        newPassword.addEventListener('input', function() {

            if (regexMax.test(newPassword.value)) {
                valMax.classList.remove('text-danger');
                valMax.classList.add('text-success');
            } else {
                valMax.classList.remove('text-success');
                valMax.classList.add('text-danger');
            }

            if (regexMin.test(newPassword.value)) {
                valMin.classList.remove('text-danger');
                valMin.classList.add('text-success');
            } else {
                valMin.classList.remove('text-success');
                valMin.classList.add('text-danger');
            }

            validateFields();
        });

        repPassword.addEventListener('input', function() {
            validateFields();

        });

        function validateFields() {
            if (regexMax.test(newPassword.value) && regexMin.test(newPassword.value) && newPassword.value ===
                repPassword.value) {
                btnSend.classList.remove('disabled');
            } else {
                btnSend.classList.add('disabled');
            }
            if (newPassword.value === repPassword.value) {
                pwdMatchError.style.left = -15 + 'rem';
            } else {
                pwdMatchError.style.left = 0;
            }
        }
    }
</script>
