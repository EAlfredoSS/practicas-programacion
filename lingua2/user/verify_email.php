<?php

session_start();

require('../files/bd.php');

$identificador2017=$_SESSION['orden2017'];


$query="SELECT * FROM mentor2009 WHERE orden='".$identificador2017."'"; //seleccionamos todos los campos 
$result=mysqli_query($link, $query);
if(!mysqli_num_rows($result))
		die("User unregistered.");
$fila=mysqli_fetch_array($result);
$email_verificado=$fila['Emailverif'];
$nombre2=$fila['nombre'];
$email2=$fila['Email'];
$codborr=$fila['Codigoborrar'];


$url2=$_SERVER['SERVER_NAME']."/user/verify_email_update.php";


if($email_verificado)
	die ("$nombre2, your email has already been verified. Return to the home page");
else
{	
	echo "<br><br>An email has been sent to $email2. Check also your Spam folder and click on the link. It may take some minutes.<br><br>";

	$message1="Click the link below in order to verify your Lingua2 account 
	
	$url2?c=$codborr";
	

	$cabeceras = 'From: recovery@languageexchanges.com' . "\r\n" .  'X-Mailer: PHP/' . phpversion();

	$success = mail($email2, 'Lingua2 Email Verification', $message1 ,  $cabeceras);
	if (!$success) {
		$errorMessage = error_get_last()['message'];
		echo $errorMessage;
	}
}

//$url3=$_SERVER['SERVER_NAME']."/user/me.php";
echo "<br><a href=\"me.php\">Back to home page</a>";


?>

<head>

<title>Email verification resend | Lingua2</title>

<!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-NYB9FFBL5J"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'G-NYB9FFBL5J');
</script>

</head>