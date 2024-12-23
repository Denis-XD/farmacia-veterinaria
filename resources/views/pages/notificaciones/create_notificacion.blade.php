<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link href="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.snow.css" rel="stylesheet" />
    <title>Nueva notificación</title>
    <style>
        .suggestions {
            max-height: 200px;
            overflow-y: auto;
        }

        .icon-btn {
            cursor: pointer;
        }
    </style>
</head>

<body>
    @extends('layout')

    @section('content')
        <div class="">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a disabled><svg xmlns="http://www.w3.org/2000/svg" width="18"
                                height="18" viewBox="0 0 24 24" fill="currentColor"
                                class="icon icon-tabler icons-tabler-filled icon-tabler-home">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path
                                    d="M12.707 2.293l9 9c.63 .63 .184 1.707 -.707 1.707h-1v6a3 3 0 0 1 -3 3h-1v-7a3 3 0 0 0 -2.824 -2.995l-.176 -.005h-2a3 3 0 0 0 -3 3v7h-1a3 3 0 0 1 -3 -3v-6h-1c-.89 0 -1.337 -1.077 -.707 -1.707l9 -9a1 1 0 0 1 1.414 0m.293 11.707a1 1 0 0 1 1 1v7h-4v-7a1 1 0 0 1 .883 -.993l.117 -.007z" />
                            </svg></a></li>
                    <li class="breadcrumb-item"><a href="{{ route('reservas') }}">Inicio</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('notificaciones.index') }}">Notificaciones</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Nueva notificación</li>
                </ol>
            </nav>
            <h2 class="text-center">Crear nueva notificación</h2>
        </div>
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {!! session('error') !!}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <ul class="m-0">
                    @foreach ($errors->all() as $error)
                        <li class="m-0">{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        <div class="px-md-5 px-0">
            <form id="form" action="{{ route('notificaciones.store') }}" method="POST">
                @csrf
                <div class="row col col-md-6 mb-3">
                    <div class="" style="width: 100%">
                        <label for="asunto" class="form-label">Asunto</label>
                        <input type="text" class="form-control" id="asunto" name="asunto"
                            pattern="[A-Za-z0-9ñÑáéíóúÁÉÍÓÚ ]+" maxlength="50" minlength="4"
                            oninput="this.value = this.value.replace(/[^A-Za-zñÑáéíóúÁÉÍÓÚ ]/g, '').substring(0, 50)"
                            required>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Destinatario(s):</label>
                    <div id="chipContainer" class="d-flex flex-wrap align-items-center gap-2">
                    </div>
                    <span class="btn btn-primary d-flex align-items-center gap-1 mt-1" style="max-width: max-content;"
                        data-bs-toggle="modal" data-bs-target="#userModal"><svg xmlns="http://www.w3.org/2000/svg"
                            width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            class="icon icon-tabler icons-tabler-outline icon-tabler-plus">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M12 5l0 14" />
                            <path d="M5 12l14 0" />
                        </svg> Agregar</span>
                    <!-- Modal -->
                    <div class="modal fade" id="userModal" tabindex="-1" aria-labelledby="userModalLabel"
                        aria-hidden="true">
                        <div class="modal-dialog modal-dialog-scrollable">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="userModalLabel">Seleccionar Usuarios</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <input type="text" id="userInput" class="form-control mb-3"
                                        placeholder="Escribe un nombre..." onkeyup="filterUsers()">
                                    <div id="suggestions" class="suggestions list-group">
                                        <!-- Las sugerencias se mostrarán aquí -->
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary"
                                        data-bs-dismiss="modal">Cerrar</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <input type="hidden" name="selected_users" id="selected_users">
                <div class="row mb-3">
                    <div class="col-6">
                        <label for="tipo_notif" class="form-label">Tipo</label>
                        <select class="form-select" name="tipo_notif" id="tipo_notif">
                            @foreach ($tipos as $tipo)
                                <option value="{{ $tipo->id_tipo_notificacion }}"
                                    {{ old('tipo_notif') == $tipo->id_tipo_notificacion ? 'selected' : '' }}>
                                    {{ $tipo->nombre }}
                            @endforeach
                        </select>
                    </div>
                    <div class="col-6">
                        <label for="send_to" class="form-label">Enviar a</label>
                        <select class="form-select" name="send_to" id="send_to">
                            <option value="0">Solo seleccionados</option>
                            @foreach ($roles as $role)
                                <option value="{{ $role->id }}">
                                    (Rol)
                                    {{ $role->name }} + seleccionados
                            @endforeach
                            <option value="999">Todos</option>
                        </select>
                    </div>
                </div>
                <div class="mb-3">
                    <label for="descripcion" class="form-label">Contenido</label>
                    <div id="contenido" class="bg-white">

                    </div>
                </div>
                <input type="hidden" name="contenido" id="contenidoInput">
                <button type="submit" id="enviar" class="btn btn-primary">Enviar</button>
            </form>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.js"></script>
        <script>
            const quill = new Quill('#contenido', {
                theme: 'snow'
            });

            const usuarios = @json($usuarios);
            let selectedUsers = [];

            function filterUsers() {
                const input = document.getElementById('userInput');
                const filter = input.value.toLowerCase();
                const suggestions = document.getElementById('suggestions');

                suggestions.innerHTML = '';
                const filteredUsers = usuarios.filter(user => user.nombre.toLowerCase().includes(filter) || user.apellido
                    .toLowerCase().includes(filter) || user.email.toLowerCase().includes(filter));
                filteredUsers.forEach(user => {
                    const div = document.createElement('div');
                    div.classList.add('list-group-item', 'd-flex', 'justify-content-between', 'align-items-center');
                    div.innerHTML = `
                <span>${user.nombre} ${user.apellido} (${user.email})</span>
                <span class="icon-btn text-primary" onclick="toggleUser(${user.id})">${selectedUsers.includes(user.id) ? '<svg  xmlns="http://www.w3.org/2000/svg"  width="16"  height="16"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-minus"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l14 0" /></svg>' : '<svg  xmlns="http://www.w3.org/2000/svg"  width="16"  height="16"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-plus"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 5l0 14" /><path d="M5 12l14 0" /></svg>'}</span>
            `;
                    suggestions.appendChild(div);
                });
            }

            function toggleUser(userId) {
                if (selectedUsers.includes(userId)) {
                    removeUser(userId);
                } else {
                    addUser(userId);
                }
                filterUsers();
            }

            function addUser(userId) {
                selectedUsers.push(userId);
                const user = usuarios.find(u => u.id === userId);
                const chipContainer = document.getElementById('chipContainer');
                const chip = document.createElement('div');
                chip.style.display = 'inline-block';
                chip.classList.add('rounded-pill', 'text-primary', 'px-2', 'fw-semibold', 'bg-white', 'shadow-sm', 'border',
                    'border-primary');
                chip.innerHTML = `<svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="1.5"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-mail-fast"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 7h3" /><path d="M3 11h2" /><path d="M9.02 8.801l-.6 6a2 2 0 0 0 1.99 2.199h7.98a2 2 0 0 0 1.99 -1.801l.6 -6a2 2 0 0 0 -1.99 -2.199h-7.98a2 2 0 0 0 -1.99 1.801z" /><path d="M9.8 7.5l2.982 3.28a3 3 0 0 0 4.238 .202l3.28 -2.982" /></svg> 
            ${user.email} <span class="closebtn" style="cursor: pointer;" onclick="removeUser(${user.id}, this)">&times;</span>
        `;
                chipContainer.appendChild(chip);
            }

            function removeUser(userId, element = null) {
                selectedUsers = selectedUsers.filter(id => id !== userId);
                if (element) {
                    element.parentElement.remove();
                } else {
                    updateChipContainer();
                }
            }

            function updateChipContainer() {
                const chipContainer = document.getElementById('chipContainer');
                chipContainer.innerHTML = '';
                selectedUsers.forEach(userId => {
                    addUser(userId);
                });
            }

            // Mostrar todos los usuarios al abrir el modal
            document.getElementById('userModal').addEventListener('shown.bs.modal', () => {
                document.getElementById('userInput').value = '';
                filterUsers();
            });

            document.getElementById('form').addEventListener('submit', function(event) {
                document.getElementById('selected_users').value = JSON.stringify(selectedUsers);
                document.getElementById('contenidoInput').value = quill.getSemanticHTML(0, quill.getLength());
            });
        </script>
    @endsection
</body>

</html>
