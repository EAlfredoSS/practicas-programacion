<?php
//nos pasan cod
//contactado=1 --> el usuario acepta oferta y envia sus datos

require('../files/bd.php');

$cod=$_GET['cod'];

//sacamos los emails de los miembros de la pareja

/*
$query="SELECT * FROM couples2009antiguos WHERE contactado=0 AND code_2='$cod'"; 
$result=mysqli_query($link,$query);
$nuevos=mysqli_num_rows($result);
//esperamos que haya solo uno
if(!$nuevos)
	die('You already answered this request or there was a mistake.');

$fila=mysqli_fetch_array($result);

$email_contactante=$fila['id_1'];
$email_contactado=$fila['id_2'];

*/


$query=
"
SELECT m.Email AS email_contactante1
FROM couples2009antiguos c 
INNER JOIN mentor2009 m 
ON c.user_id_1=m.orden 
WHERE c.contactado=0 AND c.code_2='$cod'
";

//die($query);
$result=mysqli_query($link,$query);
$nuevos=mysqli_num_rows($result);
//esperamos que haya solo uno
if(!$nuevos)
	die('You already answered this request or there was a mistake.');

$fila=mysqli_fetch_array($result);


$email_contactante=$fila['email_contactante1'];







$query222=
"
SELECT m.Email AS email_contactado1
FROM couples2009antiguos c 
INNER JOIN mentor2009 m 
ON c.user_id_2=m.orden 
WHERE c.contactado=0 AND c.code_2='$cod'
";

//die($query);
$result222=mysqli_query($link,$query222);
$nuevos222=mysqli_num_rows($result222);
//esperamos que haya solo uno
if(!$nuevos222)
	die('You already answered this request or there was a mistake.');

$fila222=mysqli_fetch_array($result222);


$email_contactado=$fila222['email_contactado1'];



// die( "contactante:  $email_contactante   -     contactado:   $email_contactado ");





//actualizamos campos
//contactado=2 quiere decir que ha rechazado la oferta
$current_time=time();
$query111="UPDATE couples2009antiguos SET contactado=2,ultimoupdate='$current_time' WHERE code_2='$cod' ";
$result111=mysqli_query($link,$query111);
if(!mysqli_affected_rows($link))
{
  mail('partners@languageexchanges.com','fallo contact_no.php','fallo contact_no.php');
  die('Error 8');
}


//enviar info al contactante
$query="SELECT * FROM mentor2009 WHERE Email='$email_contactado' "; 
$result=mysqli_query($link,$query);
$nuevos=mysqli_num_rows($result);
if(!$nuevos)
	die('The user is no longer in the data base.');

$fila=mysqli_fetch_array($result);
$email_contactado=$fila['Email'];
$nombre_contactado=$fila['nombre'];
$movil_contactado=$fila['Movil'];
$id_contactado=$fila['orden'];


//envio de email template
require('../emailtemplates/email.php'); 

//Send email to user containing username and password
//Read Template File 
$emailBody = readTemplateFile("../emailtemplates/template_no.html"); 
		
//Replace all the variables in template file
$emailBody = str_replace("#emailcontactado#",$email_contactado,$emailBody);
$emailBody = str_replace("#nombrecontactado#",$nombre_contactado,$emailBody);
$emailBody = str_replace("#telefonocontactado#",$movil_contactado,$emailBody);
$emailBody = str_replace("#idcontactado#",$id_contactado,$emailBody); 


//Send email 
$emailStatus = sendEmail ("Lingua2 Language Exchange", "partners@languageexchanges.com", $email_contactante, "$nombre_contactado declined your offer", $emailBody);
$emailStatus = sendEmail ("Lingua2 Language Exchange-$email_contactante", "partners@languageexchanges.com", "partners@languageexchanges.com", "$nombre_contactado declined your offer", $emailBody);

//If email function return false
if ($emailStatus != 1) {
	echo "An error occured while sending email. Please try again later.";
} else {
	echo "Email with account details were sent successfully.";
}	



//header("Location: ./correcto2.php");



?>
<script>window.location.replace("./corr.php");</script>
