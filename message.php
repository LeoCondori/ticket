<?php
if(isset($_POST['emailC'])) {

// Debes editar las próximas dos líneas de código de acuerdo con tus preferencias
$email_to = "lcondori@itris.com.ar";
$email_subject = "Nueva consulta desde la app";

// Aquí se deberían validar los datos ingresados por el usuario
if(!isset($_POST['emailC']) || !isset($_POST['message-text'])){

echo "<b>Ocurrió un error y el formulario no ha sido enviado. </b><br />";
echo "Por favor, vuelva atrás y verifique la información ingresada<br />";
die();
}

$email_from = $_POST['emailC'];

$email_message = "Detalles del formulario de contacto:\n\n";
$email_message .= "E-mail: " . $_POST['emailC'] . "\n";
$email_message .= "Comentarios: " . $_POST['message-text'] . "\n\n";

// Ahora se envía el e-mail usando la función mail() de PHP
$headers = 'From: '.$email_from."\r\n".
'Reply-To: '.$email_from."\r\n" .
'X-Mailer: PHP/' . phpversion();
@mail($email_to, $email_subject, $email_message, $headers);

echo '<div class="alert alert-success" role="alert">El mensaje fue enviado con éxito</div>';
}
?>