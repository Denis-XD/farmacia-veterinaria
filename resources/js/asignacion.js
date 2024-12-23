/*var contadorCarrera = 1;
function agregarCarrera(idBoton) {
    var campoCarrera = document.getElementById("carrera1");
    var clon = campoCarrera.cloneNode(true);
    var label = clon.querySelector('label'); 
    if (label) { 
        label.remove(); 
    }
    var nuevoDiv = document.createElement('div');
    nuevoDiv.classList.add('col-md-1');
    var nuevoBoton = document.createElement('button');
    nuevoBoton.type = 'button';
    nuevoBoton.classList.add('btn', 'btn-danger');
    nuevoBoton.id = 'eliminarCarrera';
    nuevoBoton.textContent = 'X';
    nuevoBoton.addEventListener('click', function () {
        clon.remove();
    });

    nuevoDiv.appendChild(nuevoBoton);
    clon.appendChild(nuevoDiv);
    contadorCarrera++;
    clon.id = "carrera" + contadorCarrera;
    var botonAgregar = document.getElementById(idBoton);
    botonAgregar.parentNode.insertBefore(clon, botonAgregar);
    return clon;
}

document.querySelectorAll("[id^='agregarCarrera']").forEach(function (button) {
    button.addEventListener('click', function () { agregarCarrera(button.id) });
});*/