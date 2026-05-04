<?php
//nos pasan cod
//contactado=1 --> el usuario acepta oferta y envia sus datos

require('../files/bd.php');

$cod=$_GET['cod'];

//sacamos los emails de los miembros de la pareja

//"SELECT * FROM couples2009antiguos WHERE contactado=0 AND code_2='$cod'"; 


$query=
"
SELECT m.Email AS email_contactante1, m.orden AS userid_contactante1
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
$userid_contactante=$fila['userid_contactante1'];




$query222=
"
SELECT m.Email AS email_contactado1, m.orden AS userid_contactado1
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
$userid_contactado=$fila222['userid_contactado1'];

//die("<br><br>User ID: $userid_contactado<br><br>  ");


//die( "contactante:  $email_contactante   -     contactado:   $email_contactado ");



//actualizamos campos
$current_time=time();
$query111="UPDATE couples2009antiguos SET contactado=1,ultimoupdate='$current_time' WHERE code_2='$cod' ";
$result111=mysqli_query($link,$query111);
if(!mysqli_affected_rows($link))
{
  mail('partners@languageexchanges.com','fallo contact_yes.php','fallo contact_yes.php');
  die('Error 8');
}


//enviar info al contactante
$query="SELECT * FROM mentor2009 WHERE Email='$email_contactado' "; 
$result=mysqli_query($link,$query);
$nuevos=mysqli_num_rows($result);
if(!$nuevos)
{
	//echo "$query";
	die("The user is no longer in the data base. " );
}

$fila=mysqli_fetch_array($result);
$email_contactado=$fila['Email'];
$nombre_contactado=$fila['nombre'];
$movil_contactado=$fila['Movil'];
$id_contactado=$fila['orden'];
$skypeident=$fila['skype'];


//envio de email template
require('../emailtemplates/email.php'); 

//Send email to user containing username and password
//Read Template File 
$emailBody = readTemplateFile("../emailtemplates/template_yes.html"); 
		
//Replace all the variables in template file
$emailBody = str_replace("#emailcontactado#",$email_contactado,$emailBody);
$emailBody = str_replace("#nombrecontactado#",$nombre_contactado,$emailBody);
$emailBody = str_replace("#telefonocontactado#",$movil_contactado,$emailBody);
$emailBody = str_replace("#idcontactado#",$id_contactado,$emailBody);
$emailBody = str_replace("#skype1#",$skypeident,$emailBody);

		
//Send email 
$emailStatus = sendEmail ("Lingua2 Language Exchange", "partners@languageexchanges.com", $email_contactante, "$nombre_contactado accepted your request and sent you information", $emailBody);
$emailStatus = sendEmail ("Lingua2 Language Exchange", "partners@languageexchanges.com", "partners@languageexchanges.com", "$nombre_contactado accepted your request and sent you information", $emailBody);
  
//If email function return false
if ($emailStatus != 1) {
	echo "An error occured while sending email. Please try again later.";
} else {
	echo "Email with account details were sent successfully.";
}	

 
 
 
//aqui metemos en la tabla de favoritos como favoritos el uno del otro en ambas direcciones


//mirar si existe la combinacion en bookmarkusers
$query="SELECT * FROM bookmarkedusers WHERE userwhosaves='$userid_contactado' AND userwhoissaved='$userid_contactante' "; 
$result=mysqli_query($link,$query);
$nuevos=mysqli_num_rows($result);
if(!$nuevos)
{
		$query172=
	"INSERT INTO bookmarkedusers (userwhosaves, userwhoissaved)
	VALUES ($userid_contactado, $userid_contactante)";

	//die("$query172");

	mysqli_query($link, $query172);

	/*
	// esto de debajo no lo ponemos porque si ya estaba en favoritos, al no poder repetirse, daria el error

	if (!mysqli_query($link, $query172)) 
	{
				echo "Error 4555. Contact webmaster.";
	}
	*/
}


//mirar si existe la combinacion en bookmarkusers
$query="SELECT * FROM bookmarkedusers WHERE userwhosaves='$userid_contactante' AND userwhoissaved='$userid_contactado' "; 
$result=mysqli_query($link,$query);
$nuevos=mysqli_num_rows($result);
if(!$nuevos)
{


	$query173=
	"INSERT INTO bookmarkedusers (userwhosaves, userwhoissaved)
	VALUES ($userid_contactante, $userid_contactado)";

	//die("$query173");

	mysqli_query($link, $query173);
 
} 
 

//header("Location: ./correcto2.php");


?>

<script>window.location.replace("./corr.php");</script>
