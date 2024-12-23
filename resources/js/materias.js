var campoCarrera = document.getElementById("carrera1");
var contador = 1;
function agregarCarrera(idBoton) {
    var clon = campoCarrera.cloneNode(true);
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
    contador++;
    clon.id = "carrera" + contador;
    var botonAgregar = document.getElementById(idBoton);
    botonAgregar.parentNode.insertBefore(clon, botonAgregar);
    return clon;
}

function agregarCarreraBoton(idBoton) {
    let selects = campoCarrera.querySelectorAll('select');
    if (selects[0].options.length > 0) {
        var clon = agregarCarrera(idBoton);
        clon.classList.remove("d-none");
        document.getElementById('labelsCarreras'+ idBoton.replace("agregarCarrera", "")).classList.remove("d-none");
    }else{
        var mensaje = document.getElementById("mensajeSinCarrera");
        if (mensaje == null) {
            sinCarreras(idBoton);
        }
    }
}

function agregarCarrerasFormEditar(carreras) {
    carreras.data.forEach(materia => {
        materia.materias_carreras.forEach((carrera, index) => {
            document.getElementById('labelsCarreras'+ carrera.id_materia).classList.remove("d-none");
            var clonEditar = agregarCarrera('agregarCarrera' + carrera.id_materia);
            clonEditar.classList.remove("d-none");
            let selects = clonEditar.querySelectorAll('select');
            selects[0].value = carrera.id_carrera
            selects[1].value = carrera.nivel
        })
    })

}
function sinCarreras(idBoton){
    var nuevoDiv = document.createElement('div');
    nuevoDiv.classList.add('mb-3', 'alert', 'alert-danger', 'alert-dismissible', 'fade', 'show');
    nuevoDiv.setAttribute("role", "alert");
    nuevoDiv.textContent = "No existen carreras registradas";
    nuevoDiv.id = "mensajeSinCarrera"

    var nuevoBoton = document.createElement('button');
    nuevoBoton.setAttribute("type", "button");
    nuevoBoton.classList.add("btn-close");
    nuevoBoton.setAttribute("data-bs-dismiss", "alert");
    nuevoBoton.setAttribute("aria-label", "Close");

    nuevoDiv.appendChild(nuevoBoton);
    var botonAgregar = document.getElementById(idBoton);
    botonAgregar.parentNode.insertBefore(nuevoDiv, botonAgregar);
}
agregarCarrerasFormEditar(materias);
// Obtener todos los botones "Agregar Carrera" en los modales de edición y asignarles el evento click
document.querySelectorAll("[id^='agregarCarrera']").forEach(function (button) {
    button.addEventListener('click', function () { agregarCarreraBoton(button.id) });
});


