const url = "http://localhost:8000/api/v1";

function login(){ //Método POST
    var user = document.getElementById("usuario").value;
    var pass = document.getElementById("pass").value;
    var alerta = document.getElementById("alertaError");
    alerta.hidden = true;
    
    if(user == "" || pass == ""){
        alerta.innerHTML = "Rellena el usuario y la contraseña";
        alerta.hidden = false;
    }
    else{
        var urlCodUser = encodeURIComponent(user);
        var urlCodPass = encodeURIComponent(pass);
        var urlLogin = url + "/login";
        var peticion = new XMLHttpRequest();
        var labelComprobacion = document.getElementById('labelComprobacion');
        
        peticion.onreadystatechange = function (){
            if (peticion.readyState == 4){
                if (peticion.status == 200) {
                    var datos = JSON.parse(peticion.responseText);
                    if(!datos['activo']){
                        alerta.innerHTML = "No puedes acceder. El usuario está inactivo";
                        alerta.hidden = false;
                    }
                    else{ //está activo
                        guardarDatosLogin(datos['id'], datos['username'], datos['token']);
                        var login = document.getElementById("login");
                        if(datos['esMaestro'])
                            window.location.replace("cuestionesMaestro.html");
                        else //es aprendiz
                            window.location.replace("cuestionesAprendiz.html");
                    }
                }
                else{
                    var error = JSON.parse(peticion.responseText);
                    alerta.hidden = false;
                    alerta.innerHTML = error['message'];
                }
                labelComprobacion.className = "mt-3";
                labelComprobacion.hidden = true;
            }
        }
        peticion.open('POST', urlLogin, true);
        peticion.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
        var queryString = "username=" + urlCodUser + "&pass=" + urlCodPass;
        peticion.send(queryString);
        
        labelComprobacion.hidden = false;
        labelComprobacion.className += " parpadeo";
    } 
}

function guardarDatosLogin(id, username, token){
    window.localStorage.setItem("id", id);
    window.localStorage.setItem("username", username);
    window.localStorage.setItem("token", token);
}

function getToken(){
    return window.localStorage.getItem("token");
}

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

function setIdCuestionAbierta(id){
    return window.localStorage.setItem("idCuestionAbierta", id);
}

function crearUsuario(){
    var user = document.getElementById("user").value;
    var pass = document.getElementById("pass").value;
    var alertaUsuario = document.getElementById("alertaUsuario");
    var alertaDatos = document.getElementById("alertaDatos");
    var labelProcesando = document.getElementById('labelProcesando');
    
    if(user == "" || pass == ""){
        alertaDatos.hidden = false;
    }
    else{
        var urlCreateUser = url + "/register";
        var peticion = new XMLHttpRequest();
        var datos = {
            username: user,
            pass: pass
        };
        peticion.onreadystatechange = function (){
            if (peticion.readyState == 4){
                if(peticion.status == 201){
                    var datos = JSON.parse(peticion.responseText);
                    guardarDatosLogin(datos['id'], datos['username'], datos['token']);
                    window.location.replace("perfil.html");
                    alert("Usuario creado correctamente. Ahora completa tu perfil.");
                }
                else{
                    alertaUsuario.hidden = false;
                }
                labelProcesando.className = "ml-2";
                labelProcesando.hidden = true;
            }
        }
        peticion.open('POST', urlCreateUser, true);
        peticion.send(JSON.stringify(datos));
        
        labelProcesando.hidden = false;
        labelProcesando.className += " parpadeo";
    }
}
function cargarPerfil(){
    var id = getId();
    var username = getUsername();
    var usuario = getUser(id);
    
    document.getElementById("username").value = username;
    if(usuario['email'] != null)
        document.getElementById("email").value = usuario['email'];
    if(usuario['nombre'] != null)
        document.getElementById("nombre").value = usuario['nombre'];
    if(usuario['apellidos'] != null)
        document.getElementById("apellidos").value = usuario['apellidos'];
    if(usuario['telefono'] != null)
        document.getElementById("telefono").value = usuario['telefono'];
}

function guardarPerfil(){
    var email = document.getElementById("email").value;
    if(email == ''){
        document.getElementById("alertaDatos").hidden = false;
    }
    else{
        var pass = document.getElementById("pass").value;
        var nombre = document.getElementById("nombre").value;
        var apellidos = document.getElementById("apellidos").value;
        var telefono = document.getElementById("telefono").value;
        var datos = {};

        datos.email = email;
        if(pass != "")
            datos.pass = pass;
        if(nombre != "")
            datos.nombre = nombre;
        if(apellidos != "")
            datos.apellidos = apellidos;
        if(telefono != "")
            datos.telefono = telefono;

        updateUser(getId(), datos);
    }
}

function updateUser(id, datos){ //PUT User (perfil)
    var urlUpdateUser = url + "/users/" + id;
    var peticion = new XMLHttpRequest();
    peticion.onreadystatechange = function (){
        if (peticion.readyState == 4){
            if(peticion.status == 209){
                var info = JSON.parse(peticion.responseText);
                if(!info['activo']){
                    window.location.replace("login.html");
                    alert("Tu perfil se ha actualizado. Actualmente tu usuario está inactivo. Cuando esté activo podrás iniciar sesión.")
                }
                else{
                    if(info['esMaestro'])
                        window.location.replace("cuestionesMaestro.html");
                    else //es aprendiz
                        window.location.replace("cuestionesAprendiz.html");
                    alert("Tu perfil se ha actualizado correctamente.");
                }
            }
            else if (peticion.status == 400){
                alertaEmail.hidden = false;
            }
            else{ //code 401
                var error = JSON.parse(peticion.responseText);
                alertaError(error);
            }   
        }
    }
    peticion.open('PUT', urlUpdateUser, true);
    peticion.setRequestHeader("X-Token", getToken());
    peticion.send(JSON.stringify(datos));
}

function getUser(id){ //GET User
    var urlUser = url + "/users/" + id;
    var peticion = new XMLHttpRequest();
    var usuario = {};
    peticion.onreadystatechange = function (){
        if (peticion.readyState == 4 && peticion.status == 200)
            usuario = JSON.parse(peticion.responseText);
    }
    peticion.open('GET', urlUser, false);
    peticion.setRequestHeader("X-Token", getToken());
    peticion.send(id);
    return usuario;
}

function ocultarAlertaUsuario(){
    document.getElementById("alertaUsuario").hidden = true;
    ocultarAlertas();
}

function ocultarAlertas(){
    var user = document.getElementById("user").value;
    var pass = document.getElementById("pass").value;
    if(user != "" && pass != "")
        document.getElementById("alertaDatos").hidden = true;
}

function ocultarAlertasPerfil(){
    document.getElementById("alertaEmail").hidden = true;
    document.getElementById("alertaDatos").hidden = true;
}

function cargarUsuarios(){
    document.getElementById("usuarioRegistrado").innerHTML = getUsername();
    var usuarios = getUsers();
    if(usuarios != null){
        usuarios = usuarios['usuarios']; //array
        var usuariosDiv = document.getElementById("usuarios");
        for (let i = 0; i < usuarios.length; i++){
            var usuario = formarUsuario(usuarios[i]['usuario']);
            usuariosDiv.appendChild(usuario);
            }
        }
    else{
        document.getElementById("sinUsuarios").hidden = false;
        document.getElementById("leyenda").hidden = true;
    }
}

function getUsers(){ //Método GET
    var urlUsers = url + "/users";
    var peticion = new XMLHttpRequest();
    var usuarios = {};
    peticion.onreadystatechange = function (){
        if (peticion.readyState == 4){
            if(peticion.status == 200){
                usuarios = JSON.parse(peticion.responseText);
            }
            else{
                usuarios = null;
                var error = JSON.parse(peticion.responseText);
                alertaError(error);
            }
        }
    }
    peticion.open('GET', urlUsers, false);
    peticion.setRequestHeader("X-Token", getToken());
    peticion.send();
    return usuarios;
}

function alertaError(error){
    alert(error['code'] + ': ' + error['message']);
}

function formarUsuario(usuario){
    var contenidoUsuario = 
        '<h4 class="mb-3">' + usuario['username'] + '</h4>' +
        '<h5 class="id font-weight-normal">id: ' + usuario['id'] + '</h5>' +
        '<h5 class="esMaestro font-weight-normal">esMaestro: ' + usuario['esMaestro'] + '</h5>' +
        '<h5 class="activo font-weight-normal">activo: ' + usuario['activo'] + '</h5>' +
        '<h5 class="font-weight-normal">nombre: ' + usuario['nombre'] + '</h5>' +
        '<h5 class="font-weight-normal">apellidos: ' + usuario['apellidos'] + '</h5>' +
        '<h5 class="font-weight-normal">teléfono: ' + usuario['telefono'] + '</h5>' +
        '<h5 class="font-weight-normal">email: ' + usuario['email'] + '</h5>';
    if(usuario['activo'])
        contenidoUsuario += '<button class="btn btn-warning mt-1 pl-3 pr-3" type="button" onclick="cambiarEstadoUsuario(event)">Desactivar</button>';
    else
        contenidoUsuario += '<button class="btn btn-primary mt-1 pl-3 pr-3" type="button" onclick="cambiarEstadoUsuario(event)">Activar</button>';
    
    if(usuario['esMaestro'])
        contenidoUsuario += '<button class="btn btn-info ml-2 mt-1 pl-3 pr-3" type="button" onclick="cambiarRol(event)">Cambiar a aprendiz</button>';
    else
        contenidoUsuario += '<button class="btn btn-info ml-2 mt-1 pl-3 pr-3" type="button" onclick="cambiarRol(event)">Cambiar a maestro</button>';
        
    contenidoUsuario += '<button class="btn btn-danger ml-2 mt-1 pl-3 pr-3" type="button" onclick="eliminarUsuario(event)">Eliminar</button>';
    
    var usuarioNodo = document.createElement("div");
    usuarioNodo.className = "usuario p-3 m-4 border border-dark rounded";
    if(usuario['activo']){
        usuarioNodo.className += " disponible";
    }
    else{
        usuarioNodo.className += " pl-4 pr-4 noDisponible";
    }
    
    usuarioNodo.innerHTML = contenidoUsuario;
    
    return usuarioNodo; 
}

function cambiarEstadoUsuario(event){
    var usuarioNodo = event.target.parentElement;
    var id = usuarioNodo.getElementsByClassName("id")[0].innerHTML.substring(4);
    var activoNodo = usuarioNodo.getElementsByClassName("activo")[0];
    var activo = activoNodo.innerHTML.substring(8);
    
    if(activo == 'true')
        activo = true;
    else
        activo = false;

    var datos = {};
    datos.activo = !activo;
    var respuesta = updateEstadoRol(id, datos);
    if(respuesta == 209){
        if(datos.activo)
            alert("El usuario ahora está activado.");
        else
            alert("El usuario ahora está desactivado.");
        window.location.reload();
    }
    else
        alertaError(JSON.parse(respuesta));
}

function cambiarRol(event){
    var usuarioNodo = event.target.parentElement;
    var id = usuarioNodo.getElementsByClassName("id")[0].innerHTML.substring(4);
    var esMaestroNodo = usuarioNodo.getElementsByClassName("esMaestro")[0];
    var esMaestro = esMaestroNodo.innerHTML.substring(11);
    
    if(esMaestro == 'true')
        esMaestro = true;
    else
        esMaestro = false;

    var datos = {};
    datos.esMaestro = !esMaestro;
    var respuesta = updateEstadoRol(id, datos);
    if(respuesta == 209){
        if(datos.esMaestro)
            alert("El usuario ahora es maestro.");
        else
            alert("El usuario ahora es aprendiz.");
        window.location.reload();
    }
    else
        alertaError(JSON.parse(respuesta));
}

function updateEstadoRol(id, datos){ //PUT User
    var urlUpdateUser = url + "/users/" + id;
    var peticion = new XMLHttpRequest();
    
    peticion.open('PUT', urlUpdateUser, false);
    peticion.setRequestHeader("X-Token", getToken());
    peticion.send(JSON.stringify(datos));
    
    if(peticion.status == 209)
        return peticion.status;
    else if (peticion.status == 401)
        return peticion.responseText;
}

function eliminarUsuario(event){
    var usuarioNodo = event.target.parentElement;
    var id = usuarioNodo.getElementsByClassName("id")[0].innerHTML.substring(4);
    var urlDeleteUser = url + "/users/" + id;
    var peticion = new XMLHttpRequest();
    
    peticion.onreadystatechange = function (){
        if (peticion.readyState == 4){
            if(peticion.status == 204){
                alert("usuario eliminado correctamente");
                usuarioNodo.parentElement.removeChild(usuarioNodo);
            }
            else{
                var error = JSON.parse(peticion.responseText);
                alertaError(error);
            }
        }
    }
    peticion.open('DELETE', urlDeleteUser, true);
    peticion.setRequestHeader("X-Token", getToken());
    peticion.send();
}

function agregarSolucion(){
    var textArea = document.getElementById("textoNuevaSolucion");
    var correcta = document.getElementById("CheckBoxCorrecta");
    
    if(textArea.value.trim() != ""){
        var solucion = formarSolucion(textArea.value, correcta.checked, "");
        var nuevoRazonamiento = solucion.getElementsByClassName("nuevoRazonamiento")[0];
        degradar(solucion);
        degradar(nuevoRazonamiento);
        var soluciones = document.getElementById("soluciones");
        soluciones.appendChild(solucion);
    
        textArea.value = "";
        correcta.checked = false;
    }
    else{
        alert("Por favor, introduzca una solución");
    }    
}

function formarSolucion(texto, correcta, idSolucion){
    var contenidoSolucion =
        '<label for="enunciado" class="h5">Solución</label>' +
        '<label class="idSolucion" hidden>' + idSolucion + '</label>' +
        '<textarea rows="3" class="form-control">' + texto + '</textarea>' +
        '<div class="form-check mt-2">' +
            '<label class="form-check-label">' +
                '<input type="checkbox" class="form-check-input" onclick="solucionCorrectaIncorrecta(event)"' + checked(correcta) + '> Correcta' +
            '</label>' +
            '<button class="btn btn-danger pl-3 pr-3 ml-2" type="button" onclick="eliminarSolucionRazonamiento(event, true)">Eliminar solución</button>' +
        '</div>' +
        '<div class ="propuestasRazonamiento p-3 mb-2 mt-2 ml-5 bg-light border border-dark rounded" hidden>' +
            '<label for="propuestasRazonamiento" class="h5 mb-0">Propuestas de razonamiento</label>' +
        '</div>' +
        '<div class="razonamientos"></div>' +
        '<div class="nuevoRazonamiento ml-5 mt-2"' + hidden(correcta) + '>' +
            '<label for="enunciado" class="h5">Nuevo razonamiento</label>' +
            '<label class="idRazonamiento" hidden></label>' +
            '<textarea rows="3" class="form-control"></textarea>' +
            '<div class="form-check mt-2">' +
                '<label class="form-check-label">' +
                    '<input type="checkbox" class="form-check-input" onclick="ocultarMostrarError(event)" checked> Justificado' +
                '</label>' +
                '<button class="btn btn-primary btn-sm pl-3 pr-3 ml-2" type="button" onclick="agregarRazonamiento(event)">Añadir razonamiento</button>' +
            '</div>' +
            '<div class="error" hidden>' +
                '<label for="error" class="h6">Error</label>' +
                '<textarea rows="2" class="form-control"></textarea>' +
                '<button class="btn btn-primary btn-sm pl-3 pr-3 mt-2" type="button" onclick="agregarRazonamiento(event)">Añadir razonamiento</button>' +
            '</div>' +
        '</div>';
    
    var solucion = document.createElement("div");
    solucion.className = "solucion mt-2";
    solucion.innerHTML = contenidoSolucion;
    return solucion;
}

function checked(correcta){
    if(correcta)
        return " checked";
    else
        return "";
}

function hidden(correcta){
    if(correcta)
        return " hidden";
    else
        return "";
}


function agregarRazonamiento(event){
    var nuevoRazonamiento = event.target.parentElement.parentElement;
    var textAreas = nuevoRazonamiento.getElementsByClassName("form-control");
    var justificado = nuevoRazonamiento.getElementsByClassName("form-check-input")[0];
    if(textAreas[0].value.trim() != "" && (justificado.checked || textAreas[1].value.trim() != "") ){
        //copio el nuevoRazonamiento y lo edito para convertirlo en razonamiento
        var razonamiento = nuevoRazonamiento.cloneNode(true);
        var label = razonamiento.getElementsByClassName("h5")[0];
        var botones = razonamiento.getElementsByClassName("btn");

        razonamiento.className = "razonamiento ml-5 mt-2";
        degradar(razonamiento);
        label.innerHTML = "Razonamiento";
        modificarBoton(botones[0]);
        modificarBoton(botones[1]);
        var solucion = nuevoRazonamiento.parentElement;
        var razonamientos = solucion.getElementsByClassName("razonamientos")[0];
        razonamientos.appendChild(razonamiento);

        //devuelvo NuevoRazonamiento a su estado inicial
        var error = nuevoRazonamiento.getElementsByClassName("error")[0];
        var boton = nuevoRazonamiento.getElementsByClassName("btn")[0];
        textAreas[0].value = "";
        textAreas[1].value = "";
        error.hidden = true;
        justificado.checked = true;
        boton.hidden = false;
    }
    else{
        if(textAreas[0].value.trim() == "")
            alert("Por favor, introduzca el razonamiento");
        else
            alert("Por favor, rellene el error");
    }
}

function degradar(elemento){
    if(!elemento.className.includes("degradadoDiv")){
        elemento.className += " degradadoDiv";
        var textArea = elemento.getElementsByClassName("form-control")[0];
        textArea.className = "form-control degradadoTextArea";
    }   
}

function modificarBoton(boton){
    boton.className = boton.className.replace("primary", "danger");
    boton.setAttribute("onclick", "eliminarSolucionRazonamiento(event)");
    boton.innerHTML = "Eliminar razonamiento";
}

function ocultarMostrarError(event){
    var checkBox = event.target;
    var boton = checkBox.parentElement.parentElement.getElementsByClassName("btn")[0];
    var tipo = checkBox.parentElement.parentElement.parentElement; //propuesta de solucion/razonamiento, razonamiento, nuevo razonamiento  
    var error = tipo.getElementsByClassName("error")[0];
    
    if(checkBox.checked){
        error.hidden = true;
        boton.hidden = false;
    }
    else{
        error.hidden = false;
        boton.hidden = true;
        degradar(error);
    }   
}

function solucionCorrectaIncorrecta(event){
    var checkBox = event.target;
    var solucion = checkBox.parentElement.parentElement.parentElement;
    var propuestasRazonamiento = solucion.getElementsByClassName("propuestasRazonamiento")[0];
    var razonamientos = solucion.getElementsByClassName("razonamientos")[0];
    var nuevoRazonamiento = solucion.getElementsByClassName("nuevoRazonamiento")[0];
    var idSolucion = solucion.getElementsByClassName("idSolucion")[0].innerHTML;
    
    if(checkBox.checked){
        if(!propuestasRazonamiento.hidden && razonamientos.hasChildNodes())
            alert("Aviso: se han eliminado los razonamientos y las propuestas de razonamiento de la solución");
        else if (razonamientos.hasChildNodes()){
            alert("Aviso: se han eliminado los razonamientos de la solución");
        }
        else if(!propuestasRazonamiento.hidden)
            alert("Aviso: se han eliminado las propuestas de razonamiento de la solución");
       
        if(idSolucion >= 1){
            deleteSolucion(idSolucion);
            solucion.getElementsByClassName("idSolucion")[0].innerHTML = "";
        }
            
        razonamientos.innerHTML = "";
        propuestasRazonamiento.hidden = true;
        nuevoRazonamiento.hidden = true;
    }
    else{
        nuevoRazonamiento.hidden = false;
        degradar(nuevoRazonamiento);
    }     
}

function corregir(event, deSolucion){
    var propuesta = event.target.parentElement.parentElement;
    var checkBox = propuesta.getElementsByClassName("form-check-input")[0];
    var textoError = propuesta.getElementsByClassName("form-control")[1];
    if(checkBox.checked || !checkBox.checked && textoError.value.trim() != ""){
        var propuestas = propuesta.parentElement;
        if(deSolucion)
            var idPropuesta = propuesta.getElementsByClassName("idPropSolucion")[0].innerHTML;
        else
            var idPropuesta = propuesta.getElementsByClassName("idPropRazonamiento")[0].innerHTML;
        
        var respuesta = updatePropuesta(idPropuesta, deSolucion, textoError.value);
        if(respuesta = 209){
            propuestas.removeChild(propuesta);
        //Si no hay propuestas se oculta el container
            if(propuestas.childElementCount == 1){ //solo el label
                propuestas.hidden = true;
            }  
        } 
    }
    else{
        alert("Por favor, rellene el error");
    }   
}

function updatePropuesta(idPropuesta, deSolucion, error){ //PUT Propuesta
    if (deSolucion)
        var urlUpdateProp = url + "/propuestas/solucion/" + idPropuesta;
    else
        var urlUpdateProp = url + "/propuestas/razonamiento/" + idPropuesta;
    
    var peticion = new XMLHttpRequest();
    peticion.open('PUT', urlUpdateProp, false);
    peticion.setRequestHeader("X-Token", getToken());
    if(error != ""){
        var error = {error: error};
        peticion.send(JSON.stringify(error));
    }   
    else
        peticion.send();
    
    if(peticion.status == 209)
        return 209;
    else{
        var error = JSON.parse(peticion.responseText);
        alertaError(error);
    }
}

function eliminarSolucionRazonamiento(event, esSolucion){
    var solucionRazonamiento = event.target.parentElement.parentElement;
    var solucionesRazonamientos = solucionRazonamiento.parentElement;
    var id;
    var respuesta = null;
    if(esSolucion){
        id = solucionRazonamiento.getElementsByClassName("idSolucion")[0].innerHTML;
        if(id != "")
            respuesta = deleteSolucion(id);
    }
    else{
        id = solucionRazonamiento.getElementsByClassName("idRazonamiento")[0].innerHTML;
        if(id != "")
            respuesta = deleteRazonamiento(id);
    }
    if(respuesta == 204 || id == ""){
        if(esSolucion)
            alert("Solución eliminada correctamente.");
        else
            alert("Razonamiento eliminado correctamente.");
        solucionesRazonamientos.removeChild(solucionRazonamiento);
    }
}

function deleteSolucion(idSolucion){ //DELETE Solucion
    var urlDeleteSol = url + "/soluciones/" + idSolucion;
    var peticion = new XMLHttpRequest();
    
    peticion.open('DELETE', urlDeleteSol, false);
    peticion.setRequestHeader("X-Token", getToken());
    peticion.send();
    
    if(peticion.status == 204)
        return 204;
    else{
        var error = JSON.parse(peticion.responseText);
        alertaError(error);
    }
}

function deleteRazonamiento(idRazonamiento){ //DELETE Razonamiento
    var urlDeleteRazon = url + "/razonamientos/" + idRazonamiento;
    var peticion = new XMLHttpRequest();
    
    peticion.open('DELETE', urlDeleteRazon, false);
    peticion.setRequestHeader("X-Token", getToken());
    peticion.send();
    
    if(peticion.status == 204)
        return 204;
    else{
        var error = JSON.parse(peticion.responseText);
        alertaError(error);
    }
}

function cerrarCuestion(){
    var cuestion = {};
    cuestion.enunciado = document.getElementById("enunciado").value;
    if(cuestion.enunciado.trim() != ""){
        document.getElementById("procesando").hidden = false;
        cuestion.idCreador = getId();
        cuestion.disponible = document.getElementById("checkBoxDisponible").checked;
        var soluciones = document.getElementById("soluciones").getElementsByClassName("solucion");
        
        if(getIdCuestionAbierta() >= 1){
            respuesta = updateCuestion(cuestion.enunciado, cuestion.disponible);
        }
        else
            respuesta = createCuestion(cuestion);
                    
        if(getIdCuestionAbierta() == "nueva")
            var idCuestion = respuesta.id;
        else
            var idCuestion = getIdCuestionAbierta();
        
        if(respuesta.code == 401){
            alertaError(respuesta);
            return false;
        }
        
        if(soluciones.length > 0){
            respuesta = incluirSoluciones(idCuestion, soluciones);
        } 

        if(respuesta.code == 401){
            alertaError(respuesta);
            return false;
        }
        else
            document.getElementById("formulario").action = "cuestionesMaestro.html";
    }
    else{
        alert("El enunciado de la cuestión está vacío.");
        document.getElementById("enunciado").focus();
        return false;
    }
}

function createCuestion(cuestion){ //POST Cuestion
    var urlCreateCuestion = url + "/cuestiones";
    var peticion = new XMLHttpRequest();
    
    peticion.open('POST', urlCreateCuestion, false);
    peticion.setRequestHeader("X-Token", getToken());
    peticion.send(JSON.stringify(cuestion));
    
    return JSON.parse(peticion.responseText);
}

function updateCuestion(enunciado, disponible){ //PUT Cuestion
    var urlUpdateCuestion = url + "/cuestiones/" + getIdCuestionAbierta();
    var peticion = new XMLHttpRequest();
    
    peticion.open('PUT', urlUpdateCuestion, false);
    peticion.setRequestHeader("X-Token", getToken());
    var datos = {enunciado: enunciado, disponible: disponible};
    peticion.send(JSON.stringify(datos));
    
    return JSON.parse(peticion.responseText);
}

function incluirSoluciones(idCuestion, soluciones){
    for (let i = 0; i < soluciones.length; i++){
        var solucion = {};
        solucion.idCuestion = idCuestion;
        var texto = soluciones[i].getElementsByClassName("form-control")[0].value;
        var correcta = soluciones[i].getElementsByClassName("form-check-input")[0];
        var idSolucion = soluciones[i].getElementsByClassName("idSolucion")[0].innerHTML;
        solucion.texto = texto;
        solucion.correcta = correcta.checked;
        
        if(idSolucion >= 1)
            respuesta = updateSolucion(idSolucion, texto, correcta.checked);
        else{
            respuesta = createSolucion(solucion);
            idSolucion = respuesta.id;
        }
            
        if(!correcta.checked){
            var razonamientos = soluciones[i].getElementsByClassName("razonamiento");
            if(razonamientos.length > 0){
                respuesta = incluirRazonamientos(idSolucion, razonamientos);
            }
        }
    }
    return respuesta;
}

function createSolucion(solucion){
    var urlCreateSolucion = url + "/soluciones";
    var peticion = new XMLHttpRequest();
    
    peticion.open('POST', urlCreateSolucion, false);
    peticion.setRequestHeader("X-Token", getToken());
    peticion.send(JSON.stringify(solucion));
    
    return JSON.parse(peticion.responseText);
}

function updateSolucion(idSolucion, texto, correcta){
    var urlUpdateSol = url + "/soluciones/" + idSolucion;
    var peticion = new XMLHttpRequest();
    
    peticion.open('PUT', urlUpdateSol, false);
    peticion.setRequestHeader("X-Token", getToken());
    var datos = {texto: texto, correcta: correcta};
    peticion.send(JSON.stringify(datos));
    
    return JSON.parse(peticion.responseText);
}

function incluirRazonamientos(idSolucion, razonamientos){
    for (let i = 0; i < razonamientos.length; i++){
        var razonamiento = {};
        razonamiento.idSolucion = idSolucion;
        var texto = razonamientos[i].getElementsByClassName("form-control")[0].value;
        var checkBox = razonamientos[i].getElementsByClassName("form-check-input")[0];
        var idRazonamiento = razonamientos[i].getElementsByClassName("idRazonamiento")[0].innerHTML;
        razonamiento.texto = texto; 
        razonamiento.justificado = checkBox.checked;
        
        if (!checkBox.checked){
            var textoError = razonamientos[i].getElementsByClassName("form-control")[1].value;
            razonamiento.error = textoError;
        }
        if(idRazonamiento >= 1)
            respuesta = updateRazonamiento(idRazonamiento, razonamiento);
        else
            respuesta = createRazonamiento(razonamiento);
    }
    return respuesta;
}

function createRazonamiento(razonamiento){
    var urlCreateRazonamiento = url + "/razonamientos";
    var peticion = new XMLHttpRequest();
    
    peticion.open('POST', urlCreateRazonamiento, false);
    peticion.setRequestHeader("X-Token", getToken());
    peticion.send(JSON.stringify(razonamiento));
    
    return JSON.parse(peticion.responseText);
}

function updateRazonamiento(idRazonamiento, razonamiento){
    var urlUpdateRazon = url + "/razonamientos/" + idRazonamiento;
    var peticion = new XMLHttpRequest();
    
    peticion.open('PUT', urlUpdateRazon, false);
    peticion.setRequestHeader("X-Token", getToken());
    peticion.send(JSON.stringify(razonamiento));
    
    return JSON.parse(peticion.responseText);
}

function cargarCuestiones(deMaestro){
    document.getElementById("usuarioRegistrado").innerHTML = getUsername();
    if(deMaestro)
        var cuestiones = getCuestionesMaestro(getId());
    else
        var cuestiones = getCuestionesAprendiz();
    
    if(cuestiones != null){
        var cuestionesNodo = document.getElementById("cuestiones");
        for (let i = 0; i < cuestiones.length; i++){
                var cuestion = formarCuestion(deMaestro, cuestiones[i]);
                cuestionesNodo.appendChild(cuestion);
            }
    }
    else{
        document.getElementById("sinCuestiones").hidden = false;
        if(deMaestro)
            document.getElementById("leyenda").hidden = true;
    }
}

function getCuestionesMaestro(idUsuario){ //GET Cuestiones (de un usuario)
    var urlGetCuestiones = url + "/cuestiones/" + idUsuario;
    var peticion = new XMLHttpRequest();
    var cuestiones;
    peticion.onreadystatechange = function (){
        if (peticion.readyState == 4){
            if(peticion.status == 200){
                cuestiones = JSON.parse(peticion.responseText);
            }
            else{
                cuestiones = null;
                var error = JSON.parse(peticion.responseText);
                if(error.code == 401)
                    alertaError(error);
            }
        }
    }
    peticion.open('GET', urlGetCuestiones, false);
    peticion.setRequestHeader("X-Token", getToken());
    peticion.send();
    return cuestiones;
}

function getCuestionesAprendiz(){ //GET Cuestiones (solo disponibles)
    var urlGetCuestiones = url + "/cuestiones/disponibles";
    var peticion = new XMLHttpRequest();
    var cuestiones;
    peticion.onreadystatechange = function (){
        if (peticion.readyState == 4){
            if(peticion.status == 200){
                cuestiones = JSON.parse(peticion.responseText);
            }
            else{
                cuestiones = null;
                var error = JSON.parse(peticion.responseText);
                if(error.code == 401)
                    alertaError(error);
            }
        }
    }
    peticion.open('GET', urlGetCuestiones, false);
    peticion.setRequestHeader("X-Token", getToken());
    peticion.send();
    return cuestiones;
} 

function formarCuestion(deMaestro, cuestion){
    var contenidoCuestion = 
        '<label class="idCuestion" hidden>' + cuestion.idCuestion + '</label>' +
        '<h3 class="mb-3">' + cuestion.enunciado + '</h3>' +
        '<a class="btn btn-primary pl-3 pr-3" role="button" ' + enlace(deMaestro) + 'onclick="guardarIdCuestionAbierta(event)">Abrir</a>' +
        botonEliminar(deMaestro);
    var cuestionNodo = document.createElement("div");
    cuestionNodo.className = "cuestion container p-3 mb-4 border border-dark rounded";
    if(deMaestro && cuestion.disponible){
        cuestionNodo.className += " disponible";
    }
    else if(deMaestro && !cuestion.disponible){
        cuestionNodo.className += " noDisponible";
    }
    else{
        cuestionNodo.className += " bg-light";
    }
    cuestionNodo.innerHTML = contenidoCuestion;
    
    return cuestionNodo; 
}

function enlace(deMaestro){
    if(deMaestro){
        return 'href="cuestionAbiertaMaestro.html"';
    } else{
        return 'href="cuestionAbiertaAprendiz.html"';
    }
}

function botonEliminar(deMaestro){
    if(deMaestro){
        return '<button class="btn btn-warning ml-1 pl-3 pr-3" type="button" onclick="eliminarCuestion(event)">Eliminar</button>';
    } else{
        return "";
    }
}

function eliminarCuestion(event){
    var cuestionesNodo = document.getElementById("cuestiones");
    var cuestion = event.target.parentElement;
    var idCuestion = cuestion.getElementsByClassName("idCuestion")[0].innerHTML;    
        
    respuesta = deleteCuestion(idCuestion);
    if(respuesta == 204){
        alert("Cuestión eliminada correctamente.");
        cuestionesNodo.removeChild(cuestion);
    }
    if(cuestionesNodo.childElementCount == 0){
        document.getElementById("leyenda").hidden = true;
        document.getElementById("sinCuestiones").hidden = false;
    }
}

function deleteCuestion(idCuestion){ //DELETE Cuestion
    var urlDeleteCuestion = url + "/cuestiones/" + idCuestion;
    var peticion = new XMLHttpRequest();
    
    peticion.open('DELETE', urlDeleteCuestion, false);
    peticion.setRequestHeader("X-Token", getToken());
    peticion.send();
    
    if(peticion.status == 204)
        return 204;
    else{
        var error = JSON.parse(peticion.responseText);
        alertaError(error);
    }
}

function cargarCuestionMaestro(){
    var idCuestionAbierta = getIdCuestionAbierta();
    document.getElementById("usuarioRegistrado").innerHTML = getUsername();
    if(idCuestionAbierta >= 1){
        var cuestion = getCuestionMaestro(idCuestionAbierta);
        var enunciado = document.getElementById("enunciado");
        var disponible = document.getElementById("checkBoxDisponible");
        var propuestasSolucion = document.getElementById("propuestasSolucion");
        var soluciones = document.getElementById("soluciones");
        
        enunciado.value = cuestion.enunciado;
        disponible.checked = cuestion.disponible;
        if(cuestion.propuestasSolucion != null){
            for(let i = 0; i < cuestion.propuestasSolucion.length; i++){
                if(!cuestion.propuestasSolucion[i].corregida){
                    var propuestaSolucion = cargarPropuestaORazonamiento(cuestion.propuestasSolucion[i], "deSolucion");
                    propuestasSolucion.appendChild(propuestaSolucion);
                }
            }
            if(propuestasSolucion.childElementCount > 1)
                propuestasSolucion.hidden = false;
        }
        if(cuestion.soluciones != null){
           for(let i = 0; i < cuestion.soluciones.length; i++){
                var solucion = cargarSolucion(cuestion.soluciones[i]);
                soluciones.appendChild(solucion);
            } 
        }
    }
}

function getCuestionMaestro(idCuestion){ //GET Cuestion (maestro)
    var urlGetCuestion = url + "/cuestiones/cuestion/maestro/" + idCuestion;
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
        
function cargarSolucion(solucion){
    var solucionNodo = formarSolucion(solucion.texto, solucion.correcta, solucion.idSolucion);
    if(!solucion.correcta){
        if(solucion.propuestasRazonamiento != null){
            var propuestasRazonamientoNodo = solucionNodo.getElementsByClassName("propuestasRazonamiento")[0];
            for(let i = 0; i < solucion.propuestasRazonamiento.length; i++){
                if(!solucion.propuestasRazonamiento[i].corregida){
                    var propuestaRazonamiento = cargarPropuestaORazonamiento(solucion.propuestasRazonamiento[i], "deRazonamiento");
                    propuestasRazonamientoNodo.appendChild(propuestaRazonamiento);
                }
            }
            if(propuestasRazonamientoNodo.childElementCount > 1)
                propuestasRazonamientoNodo.hidden = false;
        }
        if(solucion.razonamientos != null){
            var razonamientosNodo = solucionNodo.getElementsByClassName("razonamientos")[0];
            for(let i=0; i<solucion.razonamientos.length; i++){
                var razonamiento = cargarPropuestaORazonamiento(solucion.razonamientos[i], "razonamiento");
                razonamientosNodo.appendChild(razonamiento);
            }
        }
    }
    return solucionNodo;
}
        
function cargarPropuestaORazonamiento(propuestaORazonamiento, tipo){
    var contenido =
        label(propuestaORazonamiento, tipo) +
        labelIdOculto(propuestaORazonamiento, tipo) +
        textArea(propuestaORazonamiento, tipo) +
        '<div class="form-check mt-2">' +
            '<label class="form-check-label">' +
                '<input type="checkbox" class="form-check-input" onclick="ocultarMostrarError(event)" ' + marcado(propuestaORazonamiento, tipo) + '> ' + checkBox(tipo) +
            '</label>' +
            boton(propuestaORazonamiento, tipo) +
        '</div>' +
        '<div class="error"' + ocultar(propuestaORazonamiento, tipo, false) + '>' +
            '<label for="error" class="h6">Error</label>' +
            '<textarea rows="2" class="form-control">' + textAreaError(propuestaORazonamiento) + '</textarea>' +
            botonError(tipo) +
        '</div>';
    var propuestaORazonamientoNodo = document.createElement("div");
    if(tipo == "razonamiento"){
        propuestaORazonamientoNodo.className = "razonamiento ml-5 mt-2";
    }
    else{
        propuestaORazonamientoNodo.className = "propuesta mt-3";
    }
    propuestaORazonamientoNodo.innerHTML = contenido;
    return propuestaORazonamientoNodo;
}

function labelIdOculto(propuestaORazonamiento, tipo){
    if (tipo == "razonamiento")
        return '<label class="idRazonamiento" hidden>' + propuestaORazonamiento.idRazonamiento + '</label>';
    else if (tipo == "deRazonamiento")
        return '<label class="idPropRazonamiento" hidden>' + propuestaORazonamiento.idPropRazonamiento + '</label>';
    else
        return '<label class="idPropSolucion" hidden>' + propuestaORazonamiento.idPropSolucion + '</label>';
}

function label(propuestaORazonamiento, tipo){
    if(tipo == "deSolucion"){
        return '<label for="propuestaSolucion" class="h6">Propuesta (realizada por ' + propuestaORazonamiento.usernameCreador + ')</label>';
    }
    else if (tipo == "deRazonamiento"){
        return '<label for="propuestaRazonamiento" class="h6">Propuesta (realizada por ' + propuestaORazonamiento.usernameCreador + ')</label>';
    }
    else{
        return '<label for="enunciado" class="h5">Razonamiento</label>';
    }
}

function textArea(propuestaORazonamiento, tipo){
    if(tipo == "razonamiento"){
        return '<textarea rows="3" class="form-control">' + propuestaORazonamiento.texto + '</textarea>';
    }
    else{
        return '<textarea rows="3" class="form-control" readonly>' + propuestaORazonamiento.propuesta + '</textarea>';
    }
}

function marcado(propuestaORazonamiento, tipo){
    if(tipo == "deSolucion"){
        var check = propuestaORazonamiento.correcta;
    }
    else if (tipo == "deRazonamiento"){
        var check = propuestaORazonamiento.justificada;
    }
    else{
        var check = propuestaORazonamiento.justificado;
    }
    if(check){
        return " checked";
    }
    else{
        return "";
    }
}

function checkBox(tipo){
    if(tipo == "deSolucion"){
        return 'Correcta';
    }
    else if (tipo == "deRazonamiento"){
        return 'Justificada';
    }
    else{
        return 'Justificado';
    }
}

function boton(propuestaORazonamiento, tipo){
    if(tipo == "razonamiento"){
        return '<button class="btn btn-danger btn-sm pl-3 pr-3 ml-2" type="button" onclick="eliminarSolucionRazonamiento(event, false)"' + ocultar(propuestaORazonamiento, tipo, true) + '>Eliminar razonamiento</button>';
    }
    else{
        return '<button class="btn btn-info pl-3 pr-3 ml-2" type="button" onclick="corregir(event, ' + tipoPropuesta(tipo) + ')"' + ocultar(propuestaORazonamiento, tipo, true) + '>Corregir</button>';
    }
}

function tipoPropuesta(tipo){
    if (tipo == 'deSolucion')
        return true;
    else
        return false;
}

function botonError(tipo){
    if(tipo == "razonamiento"){
        return '<button class="btn btn-danger btn-sm pl-3 pr-3 mt-2" type="button" onclick="eliminarSolucionRazonamiento(event, false)">Eliminar razonamiento</button>';
    }
    else{
        return '<button class="btn btn-info pl-3 pr-3 mt-2" type="button" onclick="corregir(event, ' + tipoPropuesta(tipo) + ')">Corregir</button>';
    }
}

function ocultar(propuestaORazonamiento, tipo, esBoton){
    if(tipo == "deSolucion"){
        var check = propuestaORazonamiento.correcta;
    }
    else if (tipo == "deRazonamiento"){
        var check = propuestaORazonamiento.justificada;
    }
    else{
        var check = propuestaORazonamiento.justificado;
    }
    if(check && !esBoton || !check && esBoton){
        return " hidden";
    }
    else{
        return "";
    }
}

function textAreaError(propuestaORazonamiento){
    if(propuestaORazonamiento.error != null){
        return propuestaORazonamiento.error;
    }
    else{
        return "";
    }
}

function guardarIdCuestionAbierta(event){
    var idCuestion;
    if (event.target.innerHTML == "Abrir"){
        var cuestionNodo = event.target.parentElement;
        idCuestion = cuestionNodo.getElementsByClassName("idCuestion")[0].innerHTML;
    }
    else{
        idCuestion = "nueva";
    }
    setIdCuestionAbierta(idCuestion);
}