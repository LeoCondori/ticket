	var i_log = 0;
function mkLog(text){
	var date = new Date();
	var txt = i_log + " - " + date.getHours() + "-" + date.getMinutes() + "-" + date.getSeconds() + ": " + text; 
	i_log++;
	console.log(txt);
	$("#log").append(txt + '<br>');
}
	var existe_db
	var db
	
	var menuOpen = true;
    var menuDiv = "";
	menuDiv = document.querySelector("#menu");


function onBodyLoad(){
	//mkLog("Ejecuté el onBodyLoad");
	document.addEventListener("deviceready", onDeviceReady, false);
	menuDiv = document.querySelector("#menu");
}

function onDeviceReady(){
    //mkLog("Ejecuté el onDeviceReady");
	
	existe_db = window.localStorage.getItem("existe_db");	
	
	db = window.openDatabase("erp_mobile", "1.0", "Pedidos Offline", 200000);
	
	if(existe_db == null){
	    //mkLog("la BD es null");
		creaDB();
	}else{
		//mkLog("la BD está kkkk");
		cargaDatos();
	}

	//Habilita la función del botón atrás.
	document.addEventListener("backbutton", onBackKeyDown, false);	

	//Habilita la función del botón menú.
	document.addEventListener("menubutton", onMenuKeyDown, false);
	
}

// Función activada. Botón Menú.
function onMenuKeyDown() {
	alert('No hay opciones de menu disponible por el momento.');

		//var menu = $('#menu');
		/*$('#menu').html('<ul class="list-group">' +
  							'<li class="list-group-item">Cras justo odio</li>' +
						    '<li class="list-group-item">Dapibus ac facilisis in</li>' +
	  						'<li class="list-group-item">Morbi leo risus</li>' +
  							'<li class="list-group-item">Porta ac consectetur ac</li>' +
	  						'<li class="list-group-item">Vestibulum at eros</li>' +
						'</ul>');*/
	 /*
	 if(menuOpen) {
                    console.log("close the menu");
                    menuDiv.style.display="none";
                    menuOpen = false;
                }else{
                        console.log("open the menu");
                        menuDiv.style.display="block";
                        menuOpen = true;
                }					
	  */					
    }

function onBackKeyDown() {
            if( confirm("Realmente desea salir de la aplicación? Para navegar por esta app utilice los enlaces internos.") )
            {
                  navigator.app.exitApp();
            }
		}
		
function checkConnection() {
            var networkState = navigator.connection.type;

            var states = {};
            states[Connection.UNKNOWN]  = 'No podemos determinar tu tipo de conexión a una red de datos.';
            states[Connection.ETHERNET] = 'Estás conectado a la red mediante Ethernet connection, estamos listo para sincronizar los datos.';
            states[Connection.WIFI]     = 'Estás conectado a la red mediante WiFi, estamos listo para sincronizar los datos.';
            states[Connection.CELL_2G]  = 'Estás conectado a la red mediante Cell 2G connection, estamos listo para sincronizar los datos.';
            states[Connection.CELL_3G]  = 'Estás conectado a la red mediante Cell 3G connection, estamos listo para sincronizar los datos.';
            states[Connection.CELL_4G]  = 'Estás conectado a la red mediante Cell 4G connection, estamos listo para sincronizar los datos.';
            states[Connection.CELL]     = 'Estás conectado a la red mediante Cell generic connection, podrías experimentar lentitud en la sincronización.';
            states[Connection.NONE]     = '¡Atención! tu dispositivo no tiene conexion a datos, no podrás sincronizar, sin embargo podrás seguir trabajando de manera offline.';
			
			if(navigator.network.connection.type == Connection.NONE){
				// No tenemos conexión
				alert(states[networkState]);
			}else{
				// Si tenemos conexión
				alert(states[networkState]);
			}
			
            //alert(states[networkState]);
        }
		
/*
*Creación de base de datos
*/
function creaDB(){
	db.transaction(creaNuevaDB, errorDB, crearSuccess);
	}

function creaNuevaDB(tx){
	mkLog("Creando base de datos.");
	
	tx.executeSql('DROP TABLE IF EXISTS erp_paises');
	var sql = "CREATE TABLE IF NOT EXISTS erp_paises ( " +
	          "id INTEGER PRIMARY KEY AUTOINCREMENT, " +
			  "descripcion VARCHAR(50)," +
			  "sigla VARCHAR(5) )";
			  
	tx.executeSql(sql);

	tx.executeSql("INSERT INTO erp_paises (id, descripcion, sigla) VALUES (1, 'Argentina', 'AR')" );
	tx.executeSql("INSERT INTO erp_paises (id, descripcion, sigla) VALUES (2, 'Brasil', 'BR')" );
	tx.executeSql("INSERT INTO erp_paises (id, descripcion, sigla) VALUES (3, 'Bolivia', 'BO')" );
	tx.executeSql("INSERT INTO erp_paises (id, descripcion, sigla) VALUES (4, 'Colombia', 'CO')" );
	tx.executeSql("INSERT INTO erp_paises (id, descripcion, sigla) VALUES (5, 'Chile', 'CH')" );
	tx.executeSql("INSERT INTO erp_paises (id, descripcion, sigla) VALUES (6, 'Ecuador', 'EC')" );
	tx.executeSql("INSERT INTO erp_paises (id, descripcion, sigla) VALUES (7, 'Paraguay', 'PY')" );
	tx.executeSql("INSERT INTO erp_paises (id, descripcion, sigla) VALUES (8, 'Uruguay', 'UY')" );
}

function crearSuccess(){
	cargaDatos();
}

function errorDB(err){
	mkLog("Error procesando SQL:" + err.code);
	navigator.notification.alert("Error procesando SQL:" + err.code);
}

/*
*Cargar desde la base de datos
*/
function cargaDatos(){
	db.transaction(cargaRegistros, errorDB);
}

function cargaRegistros(tx){
	mkLog("Cargando registros de la base de datos.");
	tx.executeSql('select * from erp_paises', [], cargaDatosSuccess, errorDB);
}

function cargaDatosSuccess(tx, results){
	mkLog("Recibidos de la base de datos" + results.rows.length + " registros");
	if(results.rows.length == 0){
		mkLog("La tabla está vacía.");
		navigator.notification.alert("La tabla está vacía.");
	}
	
	for(var i=0; i<results.rows.length; i++){
		var paises = results.rows.item(i);
		var selector = $('#muestroresultado');
		selector.append('<tr>' +
							'<th scope="row">' + paises.id + '</th>' +
							'<td>' + paises.descripcion + '</td>' +
							'<td>' + paises.sigla + '</td>' +
						'</tr>');
	}
}

/*
	*Guardando datos en local storage
*/

function submitForm(){
	var _webs = $("[name='ws']").val();
	var _base = $("[name='bd']").val();
	var _users = $("[name='user']").val();
	var _pass = $("[name='pass']").val();
	
	ws = window.localStorage.setItem("ws", _webs);
	bd = window.localStorage.setItem("bd", _base);
	user = window.localStorage.setItem("user", _users);
	password = window.localStorage.setItem("password", _pass);
	
	return false;
}

function datosConexion(){
	alert('Este es el WebService: ' + window.localStorage.getItem("ws"));
	alert('Esta es base de datos: ' + window.localStorage.getItem("bd"));
	alert('Este es el Usuario: ' + window.localStorage.getItem("user"));
	alert('Este es el Password: ' + window.localStorage.getItem("password"));

	$('#output').html("Ws: " + window.localStorage.getItem("ws") + "<br>" +
						"BD: " + window.localStorage.getItem("bd") + "<br>" +
						"USer: " + window.localStorage.getItem("user") + "<br>" +
						"Pass: " + window.localStorage.getItem("password") + "<br>");
}
	