<?php

require('../files/bd.php');

session_start();
$identificador2017=$_SESSION['orden2017'];


if(!isset($identificador2017))
{
	die("You must be logged in in order to use this functionality.");
}

$id_event=$_GET['evid'];
$_SESSION['ev']=$id_event;

if (!isset($id_event))
{
	die("No event selected");
}


$just_created=false;
$just_created=$_GET['justcreated'];

//aqui actualizamos en la bbdd el numero maximo de usuarios al que se les tiene que enviar el email
if( !empty( $_POST['enviar'] )	)     
{
	$maximum_users_updated=$_POST['maxusers'];
	$total_users=$_POST['totalusers'];
	
	if($total_users>$maximum_users_updated AND $maximum_users_updated>0)
	{
		$query79="UPDATE eventoslista SET usersbroadcasted='$maximum_users_updated' WHERE Id='$id_event' ";
	}
	else
	{		
		$query79="UPDATE eventoslista SET usersbroadcasted='$total_users' WHERE Id='$id_event' ";
	}
	//die ("$query79" );
	$result79=mysqli_query($link,$query79);
	$n_aff=mysqli_affected_rows($link);
	
	//esto lo comento porque puede ser que el usuario actualice las filas con el mismo número que ya está en la bbdd y saltaría error
	//if(! $n_aff)
	//		die("Contact webmaster, please");
	
}


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
/*
<script src="https://www.paypal.com/sdk/js?client-id=ATQ4HQkTCoz20V6JrFm-lvtGqJLXBO0SGsAF63OOTsR-wY4OGn9BeFOWmxr8CGjkIqzZsFR_4uimLB_Y&disable-funding=credit,card,sofort&currency=EUR">
</script>
*/
?>

<?php
require_once("../templates/header_simplified.html");
?>

<main>
<div class="main-section">
<div class="container">
<div class="main-section-data">
<div class="row">
<?php
if($just_created==true):

?>

	<div class="alert alert-success alert-dismissible">
	  <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
	  <strong>Success!</strong> Your event has been created.
	</div>

<?php 
endif;
?>

		
			<div class="pf-hd">


<h3>Good news! Now you can attract Lingua2 users to your event</h3>

</div>


<?php








//aqui indicamos que no se puede volver a promocionar un evento que ya ha sido promocionado
$query_37="SELECT * FROM eventoslista WHERE Id='$id_event' AND Broadcasted='0' ";
$result_37=mysqli_query($link,$query_37);
$nuevos_37=mysqli_num_rows($result_37);
if(!$nuevos_37)
	die(' This event has already been promoted or it does not exist. ');

$fila_37=mysqli_fetch_array($result_37);
$creador=$fila_37['id_creador'];

$n_usuarios_destinatarios_max=$fila_37['usersbroadcasted'];





if($creador!=$identificador2017)
	die("User not allowed.");


//sacamos datos del evento pasado por url

$query_33="SELECT * FROM eventoslista WHERE Id='$id_event' ";

$result_33=mysqli_query($link,$query_33);
$nuevos_33=mysqli_num_rows($result_33);
if(!$nuevos_33)
	die(' Error 423. Contact webmaster. ');

$fila_33=mysqli_fetch_array($result_33);
$city8888=$fila_33['city'];
$eventtype=$fila_33['Idioma'];
$eventtime=$fila_33['start_time'];
$eventloc=$fila_33['location'];
$eventcreator=$fila_33['id_creador'];

$country100=$fila_33['country'];


/*
$city4=explode(' ', $cityandcountry);
$city4=$city4[0];

//$city4='Barcelona';

$country4=explode(' ', $cityandcountry);
$country4=$country4[1];
*/

$codigosecretodelevento=$fila_33['Codigoevento'];


//$country4='Spain';

//print_r($city4);
//print_r($country4);


//die("$city4 -- $country4 ");


// Extraemos las coordenadas gps de la ciudad donde se hace el evento

	$query_23="SELECT lat, lng FROM gpscities WHERE city_ascii='$city8888' AND  country='$country100' ";
	
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

ORDER BY distanciaPunto1Punto2 

";

// echo "</br></br>$query</br></br>";

$result=mysqli_query($link,$query);
if(!mysqli_num_rows($result))
		die("</br>No users nearby at the moment. <a href=\"./showallupcomingevents.php\">Come back to the events list</a>.");
	
	
$numfilas=mysqli_num_rows($result);


if($n_usuarios_destinatarios_max==NULL) :
$n_usuarios_destinatarios_max=$numfilas;
endif;

//	echo "</br>$numfilas</br></br> ";



//cuando se crea un evento el campo de 'eventoslista', 'usersbroadcasted', está a NULL en la base de datos y tiene que tener en
//la base de datos algún valor. Hay que comprobar si está a null en la bbdd y si lo está ponerle el valor máximo

$query_370="SELECT * FROM eventoslista WHERE usersbroadcasted IS NULL AND Id='$id_event' ";


$result_370=mysqli_query($link,$query_370);
$nuevos_370=mysqli_num_rows($result_370);
if($nuevos_370)
{
	$query790="UPDATE eventoslista SET usersbroadcasted='$numfilas' WHERE Id='$id_event' ";
	$result790=mysqli_query($link,$query790);
	$n_aff=mysqli_affected_rows($link);
	
	//esto lo comento porque puede ser que el usuario actualice las filas con el mismo número que ya está en la bbdd y saltaría error
	//if(! $n_aff)
	//		die("Contact webmaster, please");
	
	//die($query790);
}

//////////////////////////////////// 









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

//calculo del precio por persona

$query5="
SELECT country, gdp_percapita_2017
FROM gps_gdp_by_country
WHERE country='$country100';
";

//echo "</br></br>$query5</br></br>";

$result5=mysqli_query($link,$query5);

if(!mysqli_num_rows($result5))
{
	$factor=0.2;  //valor por defecto si no encuentra el pais
}
else
{
	$fila_5=mysqli_fetch_array($result5);
	$pais8=$fila_5['country'];
	
	//if(empty($pais8)) { $pais8="your region";}
	
	$factor=$fila_5['gdp_percapita_2017']/105280;   //se divide por 105208, que es el valor maximo de la columna  gdp_percapita_2017
}


//echo "country $pais         Factor: $factor  ";
//die("Factor: $factor");

$price_per_person=round(0.3*$factor,2); 
$price_total=round($price_per_person*$n_usuarios_destinatarios_max,2);


if($price_total<=0.99)
{
		$price_total=0.99;
}

//$_SESSION['price']=$price_total;
//$tle="Event promotion";
//$_SESSION['title']=$tle;
//$p="./envelope-4313721_1280.png";
//$_SESSION['photo']=$p;

//$ok="/events/lookforparticipantssendmessagescode.php?code=$codigosecretodelevento";
//$fail="/events/radaruserevent.php?evid=" . $id_event ;

//$_SESSION['rediok']=$ok;
//$_SESSION['redifail']=$fail;

?>
		<div class="col-lg-6">
		<div class="profiles-slider">
			<div class="user-profy p-3">
<?php

echo "
HEY, WE FOUND SOME USERS NEXT TO YOU!

</br></br></br>We found <b>$numfilas users</b>* near the GPS coordinates $latitud1 , $longitud1 corresponding to the city center of <b>$city8888 ($country100)</b>. </br></br></br></br>

Now Lingua2 can promote your event among these users by sending them a notification via email and a message to their Lingua2 inbox.

</br></br></br>
<a href=\"./eventdetails.php?idev=$id_event\" target=\"_blank\">Review your event</a>
</br></br></br>

</br>
<p style=\"font-size:80%;\">* You are included among those $numfilas users</p></br>
"; 
?>
		
		
<?php

		
if($price_total!=1)
{

	echo "<p style=\"font-size:80%;\">* The cost of messaging per person in $pais8 is $price_per_person EUR (€) </p></br>";

}
?>	

</div>
		<!--profiles-slider end-->
		</div>
		<!--user-profy end-->
		</div>
		<div class="col-lg-6">
		<div class="profiles-slider">
			<div class="user-profy p-3">
		


<?php		
echo "

PROMOTE THIS EVENT
		</br></br>
		Choose the amount of users that you want to contact:";
		
		?>
		
		<FORM name="formmaxusers" ENCTYPE="multipart/form-data" ACTION="<? echo $PHP_SELF?>" METHOD="POST" target="_self">

		<center>
			<INPUT TYPE="number" id="maxusers" min="1" max="<?php echo "$numfilas"; ?>" NAME="maxusers" value="<?php echo "$n_usuarios_destinatarios_max"; ?>"> </strong> <?php echo "/$numfilas"; ?>
			
			<INPUT TYPE="text" id="totalusers" NAME="totalusers" SIZE="8" MAXLENGTH="8" value="<?php echo "$numfilas"; ?>" hidden> </strong>
			
			<input type="submit" name="enviar" value="Update" />
		</center>

		</FORM>

<?php		

	echo"</br></br>
	
	Send an email to <b>$n_usuarios_destinatarios_max random Lingua2 users</b> living in <b>$city8888</b> and a message to their Lingua2 Inbox recommending this event.
	
	</br></br></br>
	
	<center> <p style=\"font-size:150%;\"><b>$price_total EUR (€)</b> </center></p>";
	

//$_SESSION['code']=$codigosecretodelevento;

?>

 
<?php		

$itemid1='event_promotion';  //aqui pasamos la información para que la página de success no redirija con header location a la que corresponda
							//seria un itemID para cada producto para que podamos identificarlo
$itemname1="Event #$id_event promotion in $city8888 for $n_usuarios_destinatarios_max users (user: $identificador2017)";
$productname1="Event promotion in $city8888";
$itemdescription1="An alert will be sent to the mailbox of $n_usuarios_destinatarios_max users living in $city8888."; //lo usaremos tambien como product description
$internalcodename1="$codigosecretodelevento";   //pasamos el código secreto de evento para la pagina de success
$amountprice1=$price_total;


$itemid1 = base64_encode(openssl_encrypt($itemid1, 'AES-256-CBC', 'Vm95YWNyZWFydW5hQ2w0dmVwNHJhNHBhc2FybG9zZTNudG9z'));
$itemname1 = base64_encode(openssl_encrypt($itemname1, 'AES-256-CBC', 'Vm95YWNyZWFydW5hQ2w0dmVwNHJhNHBhc2FybG9zZTNudG9z'));
$productname1 = base64_encode(openssl_encrypt($productname1, 'AES-256-CBC', 'Vm95YWNyZWFydW5hQ2w0dmVwNHJhNHBhc2FybG9zZTNudG9z'));
$itemdescription1 = base64_encode(openssl_encrypt($itemdescription1, 'AES-256-CBC', 'Vm95YWNyZWFydW5hQ2w0dmVwNHJhNHBhc2FybG9zZTNudG9z'));
$internalcodename1 = base64_encode(openssl_encrypt($internalcodename1, 'AES-256-CBC', 'Vm95YWNyZWFydW5hQ2w0dmVwNHJhNHBhc2FybG9zZTNudG9z'));
$amountprice1 = base64_encode(openssl_encrypt($amountprice1, 'AES-256-CBC', 'Vm95YWNyZWFydW5hQ2w0dmVwNHJhNHBhc2FybG9zZTNudG9z'));
 
?> 

<br>
<br>
<form name="payment_event_promotion" action="../payments/index.php" ENCTYPE="multipart/form-data" method="POST">

	<INPUT TYPE="text" id="itemid" name="itemid" value="<?php echo "$itemid1"; ?>"  hidden>
	<INPUT TYPE="text" id="itemname"name="itemname" value="<?php echo "$itemname1"; ?>" hidden >
	<INPUT TYPE="text" id="productname" name="productname" value="<?php echo "$productname1"; ?>" hidden>
	<INPUT TYPE="text" id="itemdescription" name="itemdescription" value="<?php echo "$itemdescription1"; ?>" hidden>
	<INPUT TYPE="text" id="internalcodename" name="internalcodename" value="<?php echo "$internalcodename1"; ?>" hidden>
	<INPUT TYPE="text" id="amountprice" name="amountprice" value="<?php echo "$amountprice1"; ?>" hidden>
	
	<br><br>


	<button type="submit" style="
										  background-color: #e65f00;  border: none;
										  color: white;
										  padding: 10px 11px;
										  text-align: center;
										  border-radius: 10px;
									  ">Continue to payment</button>


   
</form>
<br>


<?php	/*


 <button type="submit" style="width:100%;height:12%" class="btn btn-dark"><i class="far fa-credit-card"></i>  Continue to payment</button>




<div id="paypal-button-container"></div>

<script>
      paypal.Buttons({
        createOrder: function(data, actions) {
          return actions.order.create({
            purchase_units: [{
              amount: {
                value: <?php echo $price_total ?>
              }
            }]
          });
        },
        onApprove: function(data, actions) {
          return actions.order.capture().then(function(details) {
	    window.location.replace("https://www.languageexchanges.com/events/lookforparticipantssendmessagescode.php?code=<?php echo $codigosecretodelevento ; ?>")
            alert('Transaction completed by ' + details.payer.name.given_name);
          });
        }
      }).render('#paypal-button-container'); 
    </script>
	
	*/
	
?>	
			
		<!--user-profy end-->
		</div>
		<!--profiles-slider end-->
		
		</div>
	<!--top-profiles end-->	

</div>
</div>
</div>
</div>
</main>
</div>
<!--end-wrapper-->
	
<script type="text/javascript" src="../public/js/jquery.min.js"></script>
	<script type="text/javascript" src="../public/js/popper.js"></script>
	<script type="text/javascript" src="../public/js/bootstrap.min.js"></script>
	<script type="text/javascript" src="../public/js/jquery.mCustomScrollbar.js"></script>
	<script type="text/javascript" src="../public/lib/slick/slick.min.js"></script>
	<script type="text/javascript" src="../public/js/scrollbar.js"></script>
	<script type="text/javascript" src="../public/js/script.js"></script>

</body>



