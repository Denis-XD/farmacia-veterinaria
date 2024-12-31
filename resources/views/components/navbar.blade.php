<div>
    <div class="offcanvas offcanvas-start bg-farma d-md-none" tabindex="-1" id="offcanvasExample"
        aria-labelledby="offcanvasExampleLabel">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title text-light" id="offcanvasExampleLabel">Farmacia Veterinaria ALVA</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"
                aria-label="Close"></button>
        </div>
        <div class="offcanvas-body navbar d-flex align-items-start p-0 mt-3 w-100 md-w-auto">
            <?php $data = ['device' => 'Desktop']; ?>
            @include('components.navigation', $data)
        </div>
    </div>

    <nav class="navbar navbar-expand-lg navbar-dark bg-farma d-md-none">
        <div class="container">
            <div class="d-flex align-items-center">
                <button class="btn d-block d-md-none" type="button" data-bs-toggle="offcanvas"
                    data-bs-target="#offcanvasExample" aria-controls="offcanvasExample">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <a class="navbar-brand" href="/">Farmacia ALVA</a>
            </div>

            <div class="" id="navbarText">
                <div class="navbar-text text-wrap" style="display: flex; align-items: center;">
                    @if (Auth::check())
                        Bienvenido,
                        <span class="dropdown">
                            <button class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown"
                                aria-expanded="false">
                                {{ Auth::user()->nombre }}
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item text-dark" href="/cambiar_contrasena">Cambiar contraseña</a>
                                </li>
                            </ul>
                        </span>
                    @else
                        Bienvenido, Invitado
                    @endif
                </div>
            </div>

        </div>
    </nav>
</div>
