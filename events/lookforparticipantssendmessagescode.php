<?php
//AQUÍ MARCAMOS EL TIEMPO DE EJECUCIÓN. POR DEFECTO SON 30 SEGUNDOS.
set_time_limit(300);

require('../files/bd.php');

session_start();
$identificador2017=$_SESSION['orden2017'];

if(!isset($identificador2017))
{
	die("You must be logged in in order to use this functionality.");
}

$code_event=$_GET['code'];


?>

<head>


<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-139626327-1"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('js', new Date());
            gtag('config', 'UA-139626327-1');
        </script>


<script src="https://kit.fontawesome.com/bb6243346a.js" crossorigin="anonymous"></script>
<title>Promote Event</title>
<!-- Custom Theme files -->
<!-- for-mobile-apps -->
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" /> 
<meta name="keywords" content="language exchange, conversation exchange" />
<!-- //for-mobile-apps -->

<!--Google Fonts-->
<link href='//fonts.googleapis.com/css?family=Gudea:400,700' rel='stylesheet' type='text/css'>


 <!-- esto es para el botón con la foto y con el desplegable -->
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>


<!-- <script src="https://kit.fontawesome.com/bb6243346a.js" crossorigin="anonymous"></script> -->
		<link rel="stylesheet" type="text/css" href="../public/css/animate.css">
		<link rel="stylesheet" type="text/css" href="../public/css/bootstrap.min.css">
		<link rel="stylesheet" type="text/css" href="../public/css/line-awesome.css">
		<link rel="stylesheet" type="text/css" href="../public/css/line-awesome-font-awesome.min.css">
		<link rel="stylesheet" type="text/css" href="../public/css/font-awesome.min.css">
		<link rel="stylesheet" type="text/css" href="../public/css/jquery.mCustomScrollbar.min.css">
		<link rel="stylesheet" type="text/css" href="../public/lib/slick/slick.css">
		<link rel="stylesheet" type="text/css" href="../public/lib/slick/slick-theme.css">
		<link rel="stylesheet" type="text/css" href="../public/css/style.css">
		<link rel="stylesheet" type="text/css" href="../public/css/responsive.css">
		<link rel="stylesheet" href="../user/css/languages.css" media="all" />


</head>


<body>

<?php
require_once("../templates/header_simplified.html");
?>

<?php



//aqui indicamos que no se puede volver a promocionar un evento que ya ha sido promocionado
$query_37="SELECT * FROM eventoslista WHERE Codigoevento='$code_event'  ";
$result_37=mysqli_query($link,$query_37);
$nuevos_37=mysqli_num_rows($result_37);
if(!$nuevos_37)
	die(' This event does not exist. Contact webmaster.'); 

//aqui indicamos que no se puede volver a promocionar un evento que ya ha sido promocionado
$query_37="SELECT * FROM eventoslista WHERE Codigoevento='$code_event' AND Broadcasted='0' ";
$result_37=mysqli_query($link,$query_37);
$nuevos_37=mysqli_num_rows($result_37);
if(!$nuevos_37)
	die(' This event has already been promoted before. Contact webmaster.'); 


	//encodes messages to save them to db
	function encode($string,$key) {
		$key = sha1($key);
		$strLen = strlen($string);
		$keyLen = strlen($key);
		$j=0;
		$hash='';
		for ($i = 0; $i < $strLen; $i++) {
			$ordStr = ord(substr($string,$i,1));
			if ($j == $keyLen) { $j = 0; }
			$ordKey = ord(substr($key,$j,1));
			$j++;
			$hash .= strrev(base_convert(dechex($ordStr + $ordKey),16,36));
		}
		return $hash;
	}
	






//sacamos datos del evento pasado por url

$query_33="SELECT * FROM eventoslista WHERE Codigoevento='$code_event' ";

$result_33=mysqli_query($link,$query_33);
$nuevos_33=mysqli_num_rows($result_33);
if(!$nuevos_33)
	die(' Error 423. Contact webmaster. ');

$fila_33=mysqli_fetch_array($result_33);
$city9999=$fila_33['city'];
$eventtype=$fila_33['Idioma'];
$eventtime=$fila_33['start_time'];
$eventloc=$fila_33['location'];
$eventcreator=$fila_33['id_creador'];
$id_event=$fila_33['Id'];
$country4=$fila_33['country'];
$nombre_del_evento=$fila_33['event_name'];

$limit_users=$fila_33['usersbroadcasted'];

//if($limit_users='')
//	die('users selected do not exist. Error 408. Contact webasmter.');

if($limit_users<=0) 
	die("Problem with number of users selected: $limit_users. Contact webmaster.");

/*
$city4=explode(' ', $cityandcountry);
$city4=$city4[0];

//$city4='Barcelona';

$country4=explode(' ', $cityandcountry);
$country4=$country4[1];

*/

//$country4='Spain';

//print_r($city4);
//print_r($country4);


//die("$city4 -- $country4 ");


// Extraemos nombre del creador


	$query_123="SELECT Nombre FROM mentor2009 WHERE orden='$eventcreator' ";

	$result_123=mysqli_query($link,$query_123);
	$nuevos_123=mysqli_num_rows($result_123);
	if(!$nuevos_123)
		die(' Error 705. Contact webmaster. ');

	$fila_123=mysqli_fetch_array($result_123);
	$nombre_creador_evento=$fila_123['Nombre'];



// Extraemos las coordenadas gps de la ciudad donde se hace el evento

	$query_23="SELECT lat, lng FROM gpscities WHERE city_ascii='$city9999' AND  country='$country4' ";
	
	// echo $query_23;

	$result_23=mysqli_query($link,$query_23);
	$nuevos_23=mysqli_num_rows($result_23);
	if(!$nuevos_23)
		die(' Error 703. Contact webmaster. ');

	$fila_23=mysqli_fetch_array($result_23);
	$lat11=$fila_23['lat'];
	$lng11=$fila_23['lng'];
		
	//if ($lat11==0 AND $lng11==0)
	//	die("</br></br></br>You haven't added your location.To use this functionality you need to add your location first. Click <a href=\"../user/getgpsposition.php\">here</a>.");



$latitud1=$lat11;
$longitud1=$lng11;

//echo "Detecting users close to the GPS coordinates $latitud1 , $longitud1 corresponding to $city9999 ($country4)... ";


// extraction of users close to the event  (ATENCION AL USO DE 'HAVING' EN VEZ DE 'WHERE')


$query="
SELECT 
m.orden, m.Nombre,

(acos(sin(radians(m.Gpslat)) * sin(radians($latitud1)) + 
cos(radians(m.Gpslat)) * cos(radians($latitud1)) * 
cos(radians(m.Gpslng) - radians($longitud1))) * 6378) 

AS distanciaPunto1Punto2

FROM mentor2009 m

HAVING distanciaPunto1Punto2 < 50

ORDER BY RAND() 

LIMIT $limit_users
";

//echo "</br></br>$query</br></br>";

$result=mysqli_query($link,$query);
if(!mysqli_num_rows($result))
		die("</br>Error 676. Contact webmaster.");

	
$numfilas=mysqli_num_rows($result);

	//echo "</br>$numfilas</br></br> ";
	
	
$pila_orden = array();
$pila_nombre = array();
$pila_distancias = array();

	
for($iii=0;$iii<$numfilas;$iii++)
{
	$fila=mysqli_fetch_array($result);
	$nombre99= $fila['Nombre'];
	$orden99= $fila['orden'];
	$distancia99= round($fila['distanciaPunto1Punto2'],2);
	
	//echo "</br>$iii -- $nombre99 -- $orden99 -- $distancia99 </br> ";
	
	array_push($pila_orden,$orden99);
	array_push($pila_nombre,$nombre99);
	array_push($pila_distancias,$distancia99);	
}
	//Guardamos este mensaje en el correo ///////////////////////////////////////////////////////////

/*
print_r($pila_orden);
echo "</br></br></br>";
print_r($pila_nombre);
echo "</br></br></br>";
print_r($pila_distancias);
echo "</br></br></br>";

*/

// extraemos email para enviar correos
$pila_email = array();

for($iii=0;$iii<$numfilas;$iii++)
{
	$query_83="SELECT Email FROM mentor2009 WHERE orden='$pila_orden[$iii]' ";
	//echo $query_83;

	$result_83=mysqli_query($link,$query_83);
	$nuevos_83=mysqli_num_rows($result_83);
	if(!$nuevos_83)
		die(' Error 523. Contact webmaster. ');

	$fila_83=mysqli_fetch_array($result_83);
	$email83=$fila_83['Email'];
	
	array_push($pila_email,$email83);	
}

// print_r($pila_email);


require('../emailtemplates/email.php');


//enviamos los correos a la mensajeria y al mail 
for($iii=0;$iii<$numfilas;$iii++)
{
	
	$nombre99=$pila_nombre[$iii];
	$orden99=$pila_orden[$iii];
	
	
	
	$message_from_user="Hi $nombre99,
	
	We invite you to the event on the $eventtime. Go back to the Home page, click on \"Events in your city\" and open the event page to see all the information and publish your comment.
	
	The location is: 
	
	$eventloc
	
	Let me know if you are coming by writing a comment in the event page. 
	
	If you have questions don't hesitate to contact me";
	
	
		//el secret pas esta copiado del archivo cpm.class.php
		//this is the password to encrypr/decrypt messages to/from db. Once set it cannot be changed, as messages won't be decoded right.
		$secretPass = 'kljhflk73#OO#*U$O(*YO'; 	
		$message = encode($message_from_user,$secretPass);
		
	$query="INSERT INTO messages (`to`,`from`,`title`,`message`,`created`) VALUES('$orden99','$eventcreator','$nombre_creador_evento invites you to a new event in $city9999','$message',NOW())";	
	$result=mysqli_query($link,$query);
	if(!mysqli_affected_rows($link))
		die ('Error base datos 792. contact webmaster@lingua2.eu');   



		
//FIN Guardamos este mensaje en el correo ////////////////////////////////////////////////////////	
	
	
	//enviamos el correspondiente email
	
	
	$para      = $pila_email[$iii];
	$titulo    = "$nombre_creador_evento invites you to a new event in $city9999";


	/* envio sin html viejo
	
	
	$mensaje   = "Hi $nombre99,
		
		We invite you to the event on the $eventtime. Go to the Lingua2 www.languageexchanges.com Home page, click on \"Events in your city\" and open the event page to see all the information and publish your comment.
		
		The location is: 
		
		$eventloc
		
		Let me know if you are coming by writing a comment in the event page. 
		
		If you have questions don't hesitate to contact me";


	$cabeceras = 'From: staff@lingua2.eu' . "\r\n" .
		'Reply-To: no-reply@lingua2.eu' . "\r\n" .
		'X-Mailer: PHP/' . phpversion();

	//mail($para, $titulo, $mensaje, $cabeceras);
	sendEmail ("Lingua2 Language Exchange", "notifications@lingua2.com", $para, $titulo, $mensaje);

	// echo "Sent $iii ";
	
	
	*/
	
	
	//envio de email template
			//require('../emailtemplates/email.php'); 

			//Send email to user containing username and password
			//Read Template File 
			$emailBody = readTemplateFile("../emailtemplates/templatenew_invitacionevento.html"); 
					
			//Replace all the variables in template file
			$emailBody = str_replace("#nombre99#",$nombre99,$emailBody);
			$emailBody = str_replace("#eventtime#",$eventtime,$emailBody);
			$emailBody = str_replace("#eventloc#",$eventloc,$emailBody);			
			$emailBody = str_replace("#eventhost#",$nombre_creador_evento,$emailBody);
			$emailBody = str_replace("#eventnombre#",$nombre_del_evento,$emailBody);

			
			//Send email 
			$emailStatus = sendEmail ("Lingua2 Language Exchange", "events@languageexchanges.com", $para, $titulo, $emailBody);  
			//$emailStatus = sendEmail ("Lingua2 Ratings", "notifications@lingua2.com", "staff@lingua2.eu", "$nombre_autor has just evaluated you", $emailBody);   
	
	
	

	
	
}

?>

<main>
<div class="main-section">
<div class="container">
<div class="main-section-data">
<div style="justify-content:center" class="row">
<div class="col-lg-6">
<div class="profiles-slider">
<div class="user-profy p-3">
<br>
<h3>Your event has been promoted succesfully</h3>
<br>
<img style="width:15%;height:25%" src="../public/images/tickverde-sinfondo.png" alt="ok"> 
<?php
echo "</br></br>Message sent to the email and the Lingua2 inbox of $numfilas random users living in $city9999 ($country4).
	</br></br><a href=\"../user/me.php\"> Return to homepage</a>";
?>

</div>
</div>
</div>
</div>
</div>
</div>
</div>
</main>

<?php
//aqui marcamos el evento como ya promocionado

$query74="UPDATE eventoslista SET 	Broadcasted='1' WHERE Id=$id_event";
$result74=mysqli_query($link,$query74);
$n_mod=mysqli_affected_rows($link);
if(! $n_mod)
		die("Fallo en programa. Contacta con webmaster.");

?>

	<script type="text/javascript" src="../public/js/jquery.min.js"></script>
	<script type="text/javascript" src="../public/js/popper.js"></script>
	<script type="text/javascript" src="../public/js/bootstrap.min.js"></script>
	<script type="text/javascript" src="../public/js/jquery.mCustomScrollbar.js"></script>
	<script type="text/javascript" src="../public/lib/slick/slick.min.js"></script>
	<script type="text/javascript" src="../public/js/scrollbar.js"></script>
	<script type="text/javascript" src="../public/js/script.js"></script>


</body>
