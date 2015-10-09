<?php
//Función para guardar archivo físico en disco.
function GrabarArchivo($XMLData,$nombre){
							$now = date('Ymd-H-i-s');
							$fp = fopen($nombre.$now.".xml", "a");
							fwrite($fp, $XMLData. PHP_EOL);
							fclose($fp);	
						}

function ObtenerError($session){
			$LastErro = $client->call('ItsGetLastError', array('UserSession' => $session) );	
			echo '
			<div class="alert alert-danger alert-dismissible fade in" role="alert">
				  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				  <h4>Oh oh! Algo salió mal!</h4>
				  <p>'.utf8_encode($LastErro['Error']).'</p>
			</div>
			';
			}						
	require_once('lib/nusoap.php');	
	$client = new nusoap_client("http://itris.no-ip.com:85/ITSWS/ItsCliSvrWS.asmx?WSDL",true);
	$sError = $client->getError();
	if ($sError) {	
		echo '<span class="label label-danger"> No se pudo realizar la conexión '.$sError.'</span>';
	}else{
		$login = $client->call('ItsLogin', array('DBName' => 'ITRIS', 'UserName' => 'lcondori', 'UserPwd' => 'agaces', 'LicType'=>'WS') );			
		$error = $login['ItsLoginResult'];
		$session = $login['UserSession'];
		//var_dump($error);
		if($error){
			$LastErro = $client->call('ItsGetLastError', array('UserSession' => $session) );
			echo '
			<div class="alert alert-danger alert-dismissible fade in" role="alert">
				  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				  <h4>Oh oh! Algo salió mal!</h4>
				  <p>'.utf8_encode($LastErro['Error']).'</p>
			</div>
			';			
		}else{
			$email = $_POST["email"];
			$ticket = $_POST["ticketno"];
			
			$contacto = $client->call('ItsGetData', array('UserSession' => $session, 'ItsClassName' => 'ERP_CONTACTOS', 'RecordCount' => '-1', 'SQLFilter'=>"EMAIL = '".$email."'"  , 'SQLSort'=> '') );
			$ItsGetDataResult = $contacto["ItsGetDataResult"];

			if($ItsGetDataResult){
				$LastErro = $client->call('ItsGetLastError', array('UserSession' => $session) );
				echo '
				<div class="alert alert-danger alert-dismissible fade in" role="alert">
					  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					  <h4>Oh oh! Algo salió mal! (Contactos)</h4>
					  <p>'.utf8_encode($LastErro['Error']).'</p>
				</div>
				';
			}else{
				$XMLData = $contacto["XMLData"];
				
				//GrabarArchivo($XMLData,'ItsGetData');
				
				$xml = simplexml_load_string($XMLData);
				foreach($xml->ROWDATA->ROW as $f=>$val){
					$FK_ERP_EMPRESAS = $val['FK_ERP_EMPRESAS'];
				}
				if($FK_ERP_EMPRESAS == ""){
		  echo '<div class="alert alert-info alert-dismissible fade in" role="alert">
					  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					  <h4>Información</h4>
					  <p>El mail ingresado ['.$email.'] parece no estar asociado a ningún cliente.</p>
					  <p><small>Verifique si está dado de alta en el sistema ticket en la siguiente direccion http://itris.com.ar/site/acceso-a-tickets/ </small></p>
					  <p><small>Si el problema persiste comuníquese con la mesa de ayuda al (011)4770-9200</small></p>
					  <hr>
						<p>
							<button class="button" type="button" onclick="MostrarForm()">
								<span class="glyphicon glyphicon-backward" aria-hidden="true"></span> Volver
							</button>
						</p>					  
				</div>';																																	 
				}else{
				$tickets = $client->call('ItsGetData', array('UserSession' => $session, 'ItsClassName' => 'IT_BUGS', 'RecordCount' => '-1', 'SQLFilter'=>"ID = '".$ticket."' and FK_CLIENTES= '".$FK_ERP_EMPRESAS."' "  , 'SQLSort'=> '') );	

			$ItsGetDataResultTicket = $tickets["ItsGetDataResult"];

			if($ItsGetDataResultTicket){
				$LastErro = $client->call('ItsGetLastError', array('UserSession' => $session) );
				echo '
				<div class="alert alert-danger alert-dismissible fade in" role="alert">
					  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					  <h4>Oh oh! Algo salió mal! (Ticket)</h4>
					  <p>'.utf8_encode($LastErro['Error']).'</p>
				</div>
				<hr>
				<p>
					<button class="button" type="button" onclick="MostrarForm()">
						<span class="glyphicon glyphicon-backward" aria-hidden="true"></span> Volver
					</button>
				</p>
				';
				}else{					
					$XMLDataTicket = $tickets["XMLData"]; 
				
					$xml = simplexml_load_string($XMLDataTicket);
					foreach($xml->ROWDATA->ROW as $f=>$val){
						$FK_CLIENTES = $val['FK_CLIENTES'];
						$Z_FK_CONTACTOS = $val['Z_FK_CONTACTOS'];
						$Z_AREA_ASIGNADA = $val['Z_AREA_ASIGNADA'];
						$Z_ASIGNADO_A = $val['Z_ASIGNADO_A'];
						$Z_ESTADO = $val['Z_ESTADO'];
						$DES_CLI = $val['DES_CLI'];
					}
					if($FK_CLIENTES == ""){
					  echo '<div class="alert alert-info alert-dismissible fade in" role="alert">
								  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
								  <h4>Información</h4>
								  <p>Parece no haber tickets generados con el número '.$ticket.' para la empresa '.$FK_ERP_EMPRESAS.'.</p>
								  <p><small>Si considera que es un error comuníquese con la mesa de ayuda al (011)4770-9200</small></p>
								  <hr>
									<p>
										<button class="button" type="button" onclick="MostrarForm()">
											<span class="glyphicon glyphicon-backward" aria-hidden="true"></span> Volver
										</button>
									</p>					  
							</div>';						
					}else{
							echo '
								<div class="login-box col-md-6">
									<div><strong>Bienvenido '.$Z_FK_CONTACTOS.'</strong></div>
										<div>
											<p>Empresa: '.$FK_ERP_EMPRESAS.'</p>
										<hr>
										<p>Área asignada: '.$Z_AREA_ASIGNADA.'</p>
										<p>Agente asignado: '.$Z_ASIGNADO_A.'</p>
										<p>Estado: '.$Z_ESTADO.'</p>
										<textarea class="form-control" rows="15">'.$DES_CLI.'</textarea>
											<p>
												<button class="button" type="button" onclick="MostrarForm()">
													<span class="glyphicon glyphicon-backward" aria-hidden="true"></span> Volver
												</button>
											</p>
										</div>
								</div>
							';
						}
					}
				}
			}
		}

//Luego de mostrar la version ->Cierro sesion
					//FUNCIONA LOGOUT PERFECTO!
						$LogOut = $client->call('ItsLogout', array('UserSession' => $session) );				
						$res = $LogOut['ItsLogoutResult'];
						if($res){
							echo '<span class="glyphicon glyphicon-alert" aria-hidden="true"></span>';
						}else{
							echo '<span class="glyphicon glyphicon-thumbs-up" aria-hidden="true"></span>';
						}

	}
//BORRADORES:
//Guardo XML en disco->
						/*$now = date('Ymd-H-i-s');
						$fp = fopen("ERP_CONTACTOS".$now.".xml", "a");
						fwrite($fp, $XMLData. PHP_EOL);
						fclose($fp);
						*/	
?>