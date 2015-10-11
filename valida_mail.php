<?php
/*
echo "Por POST: ".$_POST["emailNew"];
echo '<br>';
echo "Por GET: ".$_GET["m"];
echo '<br>';
echo ';-)';
*/
function GrabarArchivo($XMLData,$nombre){
							$now = date('Ymd-H-i-s');
							$fp = fopen($nombre.$now.".xml", "a");
							fwrite($fp, $XMLData. PHP_EOL);
							fclose($fp);	
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
			$email = $_POST["emailNew"];
			
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
				$DESCRIPCION= '';
				$xml = simplexml_load_string($XMLData);
				foreach($xml->ROWDATA->ROW as $f=>$val){
					$FK_ERP_EMPRESAS = $val['FK_ERP_EMPRESAS'];
					$DESCRIPCION = $val['DESCRIPCION'];
					$FK_ERP_EMPRESAS = $val['FK_ERP_EMPRESAS'];
				}
				
				if($DESCRIPCION == ""){
		  echo '<div class="alert alert-info alert-dismissible fade in" role="alert">
					  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					  <h4>Información</h4>
					  <p>El mail ingresado ['.$email.'] parece no estar asociado a ningún cliente.</p>
					  <p><small>Verifique si está dado de alta en el sistema ticket en la siguiente direccion http://itris.com.ar/site/acceso-a-tickets/ </small></p>
					  <p><small>Si el problema persiste comuníquese con la mesa de ayuda al (011)4779-9200</small></p>
					  <hr>
						<p>
							<button class="btn btn-primary btn-lg" type="button" onclick="Reintentar()">
								<span class="glyphicon glyphicon-backward" aria-hidden="true"></span> Volver a intentar
							</button>
						</p>					  
				</div>';																																	 
				}else{
				echo '<p class="bg-success">Excelente <strong>'.$DESCRIPCION.'</strong> hemos verificado que pertenecés a la empresa '.$FK_ERP_EMPRESAS.'. ¡Es todo lo que necesitamos!</p>
					  <p>
						<button class="btn btn-primary btn-lg" type="button" onclick="MostrarForm()">
						¡Comenzar!
						</button>
					  </p>';					
				}
			}	
		}
	}
?>