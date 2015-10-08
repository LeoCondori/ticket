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
					<a class="btn btn-default" href="index.html" role="button">Volver</a>
				</p>
			</div>
	</div>
';
?>