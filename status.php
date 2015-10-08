<?php
$email = $_POST["email"];
$ticket = $_POST["ticketno"];

echo '
	<div class="login-box col-md-6">
		<div><strong>Holis</strong></div>
			<div>
				<p>'.$email.'</p>
				<p>'.$ticket.'</p>
				<p>
					<button class="button" type="button" onclick="MostrarForm()"><span class="glyphicon glyphicon-backward" aria-hidden="true"></span> Volver</button>
				</p>
			</div>
	</div>
';
?>