var contadorMateria = 1;
var contadorPeriodo = 1;

function agregarMateria(idBoton) {
    var campoMateria = document.getElementById("materia-1");
    var clon = campoMateria.cloneNode(true);
    var label = clon.querySelector('label');
    if (label) {
        label.remove();
    }
    var nuevoDiv = document.createElement('div');
    nuevoDiv.classList.add('col-md-1');
    var nuevoBoton = document.createElement('button');
    nuevoBoton.type = 'button';
    nuevoBoton.classList.add('btn', 'btn-danger');
    nuevoBoton.id = 'eliminarMateria';
    nuevoBoton.textContent = 'X';
    nuevoBoton.addEventListener('click', function() {
        clon.remove();
    });

    nuevoDiv.appendChild(nuevoBoton);
    clon.appendChild(nuevoDiv);
    contadorMateria++;
    clon.id = "materia" + contadorMateria;
    var botonAgregar = document.getElementById(idBoton);
    botonAgregar.parentNode.insertBefore(clon, botonAgregar);
    return clon;
}

function agregarPeriodo(idBoton) {
    var campoPeriodo = document.getElementById("periodo-1");
    var clon = campoPeriodo.cloneNode(true);
    var label = clon.querySelector('label');
    if (label) {
        label.remove();
    }
    var nuevoDiv = document.createElement('div');
    nuevoDiv.classList.add('col-md-1');
    var nuevoBoton = document.createElement('button');
    nuevoBoton.type = 'button';
    nuevoBoton.classList.add('btn', 'btn-danger');
    nuevoBoton.id = 'eliminarPeriodo';
    nuevoBoton.textContent = 'X';
    nuevoBoton.addEventListener('click', function() {
        clon.remove();
    });

    nuevoDiv.appendChild(nuevoBoton);
    clon.appendChild(nuevoDiv);
    contadorPeriodo++;
    clon.id = "periodo" + contadorPeriodo;
    var botonAgregar = document.getElementById(idBoton);
    botonAgregar.parentNode.insertBefore(clon, botonAgregar);
    return clon;
}

document.querySelectorAll("[id^='agregarmateria']").forEach(function(button) {
    button.addEventListener('click', function() {
        agregarMateria(button.id)
    });
});

document.querySelectorAll("[id^='agregarperiodo']").forEach(function(button) {
    button.addEventListener('click', function() {
        agregarPeriodo(button.id)
    });
});

document.getElementById('agregarperiodo-generica').addEventListener('click', function() {
    agregarPeriodo('agregarperiodo-generica');
});
