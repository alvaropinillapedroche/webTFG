const url = "http://localhost:8000/api/v1";

function getId(){
    return JSON.parse(window.localStorage.getItem("id"));
}

function getUsername(){
    return window.localStorage.getItem("username");
}

function getIdCuestionAbierta(){
    var id = window.localStorage.getItem("idCuestionAbierta");
    if (id == "nueva")
        return id;
    else
        return JSON.parse(id);
}

function getToken(){
    return window.localStorage.getItem("token");
}

function cargarCuestionAprendiz(){
    document.getElementById("usuarioRegistrado").innerHTML = getUsername();
    var cuestion = getCuestionAprendiz(getIdCuestionAbierta());
    document.getElementById("creador").innerHTML = cuestion.usernameCreador;
    window.localStorage.setItem("cuestion", JSON.stringify(cuestion)); //para poder acceder después
    var enunciado = document.getElementById("enunciado");
    enunciado.value = cuestion.enunciado;
    if(cuestion.soluciones != null){
        var oculto = document.getElementById("oculto");
        //Inicializo el avance por las soluciones y razonamientos
        setAvance(-1, -1, -1);
        for(let i = 0; i < cuestion.soluciones.length; i++){
            var solucion = generarSolucion(cuestion.soluciones[i].texto);
            oculto.appendChild(solucion);
            if(!cuestion.soluciones[i].correcta){
                oculto.appendChild(generarPropuesta());
                if(cuestion.soluciones[i].razonamientos != null){
                    for(let j = 0; j < cuestion.soluciones[i].razonamientos.length; j++){
                        var razonamiento = generarRazonamiento(cuestion.soluciones[i].razonamientos[j].texto);
                        oculto.appendChild(razonamiento);
                    }
                }
            } 
        }
    }
}

function getCuestionAprendiz(idCuestion){ //GET Cuestion (aprendiz)
    var urlGetCuestion = url + "/cuestiones/cuestion/aprendiz/" + idCuestion;
    var peticion = new XMLHttpRequest();
    
    peticion.open('GET', urlGetCuestion, false);
    peticion.setRequestHeader("X-Token", getToken());
    peticion.send();
    
    if(peticion.status == 401){
        var error = JSON.parse(peticion.responseText);
        alertaError(error);
    }
    else
        return JSON.parse(peticion.responseText);
}

function generarSolucion(enunciado){
    var contenido =
        '<label class="h5">Solución</label>' +
        '<textarea rows="3" class="form-control degradadoTextAreaAprendiz" readonly>' + enunciado + '</textarea>' +
        '<div class="form-check mt-2">' +
            '<label class="form-check-label">' +
                '<input type="checkbox" class="form-check-input"> Correcta' +
            '</label>' +
            '<a class="btn btn-primary pl-3 pr-3 ml-2" href="#fin" onclick="corregirAprendiz(event,true)">Corregir</a>' +
            '<label class="mensaje ml-1"></label>' +
        '</div>';
    var solucion = document.createElement("div");
    solucion.className = "solucion degradadoDiv";
    solucion.hidden = true;
    solucion.innerHTML = contenido;
    return solucion;
}

function generarRazonamiento(enunciado){
    var contenido =
        '<label class="h5">Razonamiento</label>' +
        '<textarea rows="3" class="form-control degradadoTextAreaAprendiz" readonly>' + enunciado + '</textarea>' +
        '<div class="form-check mt-2">' +
            '<label class="form-check-label">' +
                '<input type="checkbox" class="form-check-input"> Justificado' +
            '</label>' +
            '<a class="btn btn-primary pl-3 pr-3 ml-2" href="#fin" onclick="corregirAprendiz(event,false)">Corregir</a>' +
        '</div>' +
        '<label class="mensaje mt-1"></label>';
    var razonamiento = document.createElement("div");
    razonamiento.className = "razonamiento ml-5 degradadoDiv";
    razonamiento.hidden = true;
    razonamiento.innerHTML = contenido;
    return razonamiento;
}

function generarPropuesta(){
    var contenido =
        '<label class="h5">Propuesta de razonamiento</label>' +
        '<textarea rows="3" class="form-control degradadoTextArea"></textarea>' +
        '<div class="mt-2">' +
            '<a class="btn btn-info pl-3 pr-3" href="#fin" onclick="enviarRazonamiento(event)">Enviar</a>' +
            '<label class="mensaje ml-1"></label>' +
        '</div>';
    var propuestaRazonamiento = document.createElement("div");
    propuestaRazonamiento.className = "propuestaRazonamiento ml-5 degradadoDiv";
    propuestaRazonamiento.hidden = true;
    propuestaRazonamiento.innerHTML = contenido;
    return propuestaRazonamiento;
}



function setAvance(solucion, razonamiento, elemento){
    var avance = {solucionActual : solucion, razonamientoActual : razonamiento, elementoActual : elemento};
    window.localStorage.setItem("avance", JSON.stringify(avance));
}

function getAvance(){
    return JSON.parse(window.localStorage.getItem("avance"));
}

function enviarSolucion(event){
    var solucionNodo = event.target.parentElement.parentElement;
    var texto = getTexto(solucionNodo);
    if(texto.trim() != ""){
        var idCuestionAbierta = getIdCuestionAbierta();
        var propSolucion = {
            idCuestion : idCuestionAbierta,
            idCreador: getId(),
            propuesta: texto
        };
        var respuesta = createPropSolucion(propSolucion);
        if(respuesta == 201){
            setMensaje(solucionNodo, "Propuesta enviada para su corrección", "info");
            desactivar(solucionNodo);
            mostrarSiguiente();
        }
    }
    else{
        alert("Por favor, introduzca la propuesta.");
    }
}

function createPropSolucion(propSolucion){ //POST PropSolucion
    var urlCreatePropSol = url + "/propuestas/solucion";
    var peticion = new XMLHttpRequest();
    
    peticion.open('POST', urlCreatePropSol, false);
    peticion.setRequestHeader("X-Token", getToken());
    peticion.send(JSON.stringify(propSolucion));
    
    if(peticion.status == 201)
        return 201;
    else{
        var error = JSON.parse(peticion.responseText);
        alertaError(error);
    }
}

function enviarRazonamiento(event){
    var razonamientoNodo = event.target.parentElement.parentElement;
    var texto = getTexto(razonamientoNodo);
    if(texto.trim() != ""){
        var idCuestionAbierta = getIdCuestionAbierta();
        var cuestion = JSON.parse(window.localStorage.getItem("cuestion"));
        var avance = getAvance();
        var propRazonamiento = {
            idSolucion: cuestion.soluciones[avance.solucionActual].idSolucion,
            idCreador: getId(),
            propuesta: texto
        };
        var respuesta = createPropRazonamiento(propRazonamiento);
        if(respuesta == 201){
            setMensaje(razonamientoNodo, "Propuesta enviada para su corrección", "info");
            desactivar(razonamientoNodo);
            mostrarSiguiente();
        }
    }
    else{
        alert("Por favor, introduzca la respuesta.");
    }
}

function createPropRazonamiento(propRazonamiento){ //POST PropRazonamiento
    var urlCreatePropRazon = url + "/propuestas/razonamiento";
    var peticion = new XMLHttpRequest();
    
    peticion.open('POST', urlCreatePropRazon, false);
    peticion.setRequestHeader("X-Token", getToken());
    peticion.send(JSON.stringify(propRazonamiento));
    
    if(peticion.status == 201)
        return 201;
    else{
        var error = JSON.parse(peticion.responseText);
        alertaError(error);
    }
}

function corregirAprendiz(event, esSolucion){
    var solucionORazonamiento = event.target.parentElement.parentElement;
    var texto = getTexto(solucionORazonamiento);
    if(texto.trim() != ""){
        var cuestion = JSON.parse(window.localStorage.getItem("cuestion"));
        var checkBox = solucionORazonamiento.getElementsByClassName("form-check-input")[0];
        var avance = getAvance();
        if(esSolucion){
            var correcta = cuestion.soluciones[avance.solucionActual].correcta;
            if(checkBox.checked == correcta)
                setMensaje(solucionORazonamiento, "Respuesta correcta", "acierto");
            else
                setMensaje(solucionORazonamiento, "Respuesta incorrecta", "fallo");
        }
        else{ //es razonamiento
            var razonamiento = cuestion.soluciones[avance.solucionActual].razonamientos[avance.razonamientoActual];
            if(checkBox.checked == razonamiento.justificado)
                setMensaje(solucionORazonamiento, "Respuesta correcta", "acierto");
            else if(razonamiento.error != null){
                setMensaje(solucionORazonamiento, "No está justificado porque: " + razonamiento.error, "fallo");
            }else
                setMensaje(solucionORazonamiento, "Respuesta incorrecta", "fallo");
        }
        desactivar(solucionORazonamiento);
        mostrarSiguiente();
    }
    else{
        alert("Por favor, introduzca la respuesta.");
    }
}

function desactivar(elemento){
    elemento.getElementsByClassName("form-control")[0].readOnly = true;
    elemento.getElementsByClassName("btn")[0].disabled = true;
    var checkBox = elemento.getElementsByClassName("form-check-input")[0];
    if(checkBox != null)
        checkBox.disabled = true;   
}

function getTexto(elemento){
    return elemento.getElementsByClassName("form-control")[0].value;
}

function setMensaje(elemento, mensaje, tipo){
    var label = elemento.getElementsByClassName("mensaje")[0];
    label.innerHTML = mensaje;
    if(tipo == "info")
        label.className += " alert alert-primary";
    else if (tipo == "acierto")
        label.className += " alert alert-success";
    else
        label.className += " alert alert-danger";
}

function mostrarSiguiente(){
    var elementos = document.getElementById("oculto").childNodes;
    var avance = getAvance();
    var finCuestion = document.getElementById("finCuestion");
    var siguiente = avance.elementoActual + 1;
    if(siguiente < elementos.length){
        elementos[siguiente].hidden = false;
        finCuestion.focus();
        var tipo = getTipo(elementos[siguiente]);
        if(tipo == "solucion")
            setAvance(avance.solucionActual+1, -1, siguiente);
        else if(tipo == "razonamiento")
            setAvance(avance.solucionActual, avance.razonamientoActual+1, siguiente);
        else
            setAvance(avance.solucionActual, avance.razonamientoActual, siguiente);
    }
    else{
        finCuestion.hidden = false;
        finCuestion.className += " parpadeo"
    }
}

function getTipo(elemento){
    return elemento.className.substring(0, elemento.className.indexOf(" "));
}

function cargarPropuestas(){
    document.getElementById("usuarioRegistrado").innerHTML = getUsername();
    
    var respuesta = getPropuestas();
    
    var noPropSol = document.getElementById("noPropSol");
    var noPropRazon = document.getElementById("noPropRazon");
    var propSolNodo = document.getElementById("propSolucion");
    var propRazonNodo = document.getElementById("propRazonamiento");
    var leyenda = document.getElementById("leyenda");
    
    if(respuesta.code == 200){
        if(respuesta.propuestasSolucion.length > 0){
            cargarPropSol(respuesta.propuestasSolucion);
            leyenda.hidden = false;
        }
        else{
            noPropSol.hidden = false;   
        }

        if(respuesta.propuestasRazonamiento.length > 0){
            cargarPropRazon(respuesta.propuestasRazonamiento);
            propRazonNodo.hidden = false;
            leyenda.hidden = false;
        }
        else{
            noPropRazon.hidden = false;
        }
    }
    else{
        noPropSol.hidden = false;
        noPropRazon.hidden = false;
    }
    
    propSolNodo.hidden = false;
    propRazonNodo.hidden = false;
}

function getPropuestas(){ //GET Propuestas (de solución y razonamiento)
    var cargando = document.getElementById("cargando");
    var urlGetPropuestas = url + "/propuestas/" + getId();
    var peticion = new XMLHttpRequest();
    
    peticion.open('GET', urlGetPropuestas, false);
    peticion.setRequestHeader("X-Token", getToken());
    peticion.send();
    cargando.hidden = true;
    
    if(peticion.status == 401){
        var error = JSON.parse(peticion.responseText);
        alertaError(error);
    }
    else
        return JSON.parse(peticion.responseText);
        
}

function cargarPropSol(propuestasSolucion){
    var propSolNodo = document.getElementById("propSolucion");
    for (let i = 0; i<propuestasSolucion.length; i++){
        var propSol = formarPropSol(propuestasSolucion[i]);
        propSolNodo.appendChild(propSol);
    }
}

function formarPropSol(propuesta){
    var contenido =
        '<h4 class="mb-3">Cuestión: ' + propuesta.cuestion + '</h4>' +
        '<h5 class="mb-0">Respondiste: ' + propuesta.propuesta + '</h5>';
    
    var propSol = document.createElement("div");
    propSol.className = 'container p-3 mb-4 mt-3 border border-dark rounded';
    
    if(propuesta.corregida){
        if(propuesta.correcta)
            propSol.className += ' correcta';
        else{
            propSol.className += ' erronea';
            contenido += '<h5 class="mt-3 mb-0">Corrección: ' + propuesta.error + '</h5>';
        }    
    }
    else
        propSol.className += ' pendiente';
    
    propSol.innerHTML = contenido;
    return propSol;
}

function  cargarPropRazon(propuestasRazonamiento){
    var propRazonNodo = document.getElementById("propRazonamiento");
    for (let i = 0; i<propuestasRazonamiento.length; i++){
        var propRazon = formarPropRazon(propuestasRazonamiento[i]);
        propRazonNodo.appendChild(propRazon);
    }
}

function formarPropRazon(propuesta){
    var contenido =
        '<h4 class="mb-3">Cuestión: ' + propuesta.cuestion +'</h4>' +
        '<label class="h5 mb-3">Solución:&nbsp</label>' +
        '<label class="h5">' + ' ' + propuesta.solucion +'</label>' +
        '<h5 class="mb-0">Respondiste: ' + propuesta.propuesta +'</h5>';
    
    var propRazon = document.createElement("div");
    propRazon.className = 'container p-3 mb-4 mt-3 border border-dark rounded';
    
    if(propuesta.corregida){
        if(propuesta.justificada)
            propRazon.className += ' correcta';
        else{
            propRazon.className += ' erronea';
            contenido += '<h5 class="mt-3 mb-0">Corrección: ' + propuesta.error + '</h5>';
        }    
    }
    else
        propRazon.className += ' pendiente';
    
    propRazon.innerHTML = contenido;
    return propRazon;
}