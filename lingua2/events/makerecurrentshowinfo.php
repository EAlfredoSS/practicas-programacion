<?php

require('../files/bd.php');

session_start();
$identificador2017=$_SESSION['orden2017'];


$id_evento=$_GET['idev'];

if(!isset($identificador2017))
{
	die("You must be logged in in order to see this page");
}


$query="SELECT * FROM eventoslista WHERE Id='$id_evento'";

//die($query);
$result=mysqli_query($link,$query);
//$nuevos=mysqli_num_rows($result);

//print_r($result);

if( ! mysqli_num_rows($result))
{
	die('<br/>error event!!!<br/>');
}

$fila=mysqli_fetch_array($result);


$id_creador=$fila['id_creador'];


require('../files/idiomasequivalencias.php');

$lengua1=$idiomas_equiv["{$fila['Idioma']}"];
$lengua1=substr($lengua1,0,14);



//$hora_creacion=$fila['Horacreacion'];
$nombre_ev=$fila['event_name'];
$descr_ev=$fila['event_desc'];
$hora_inicio_gmt=$fila['start_time'];

$hora_inicio_unix=$fila['unix_start_time'];

$ciudad_ev=$fila['city'];
$location_ev=$fila['location'];
$country_ev1=$fila['country'];

$broadc=$fila['Broadcasted'];

$es_replica=$fila['Createdfromid'];

$codigo_evento1=$fila['Codigoevento'];

$identificador_creador_evento=$fila['id_creador'];


if($identificador_creador_evento!=$identificador2017)
	die("You only can make your own events recurrent, not the events created by other users.");


//aqui vemos si el evento es una replica de otro evento
if (!is_null( $es_replica ) )
	die('This event belongs to a serie of events. You can only make an event recurrent from the original one.');




//aqui comprobamos que el evento no se ha hecho recurrente anteriormente. si hay algun evento en el que el campo Createdfromid=$id_evento, entonces ya fue recurrente
$query2="SELECT * FROM eventoslista WHERE Createdfromid='$id_evento'";

//die($query);
$result2=mysqli_query($link,$query2);
//$nuevos=mysqli_num_rows($result);

//print_r($result);

if( mysqli_num_rows($result2))
{
	die('<br/>Error: This event has already been made recurrent.<br/>');
}





?>




<!DOCTYPE html>
<html>
<head>

<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-139626327-1"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('js', new Date());
            gtag('config', 'UA-139626327-1');
        </script>

<meta charset="utf-8">
<title>Lingua2 Make the event weekly (info)</title>

</head>
<body>


<?php /*
<script src="https://www.paypal.com/sdk/js?client-id=ATQ4HQkTCoz20V6JrFm-lvtGqJLXBO0SGsAF63OOTsR-wY4OGn9BeFOWmxr8CGjkIqzZsFR_4uimLB_Y&disable-funding=credit,card,sofort&currency=EUR">
</script>

*/ ?>



<?php require("../templates/header_simplified.html"); ?>
<main>

<div class="main-section">
    <div class="container">
        <div class="main-section-data">
            <div class="row">
				<div class="col-lg-3 col-md-4 pd-left-none no-pd"></div>
					<div class="col-lg-6 col-md-7 no-pd" >
						<div	class="main-ws-sec" >
                            <div class="top-profiles ">
                                <div class="pf-hd">
                                    <h3>Make your event recurrent weekly for one year</p>
                                    </h3>
                                </div>
									<div class="post-bar" style="margin: 10px 10px 10px 10px; width: 96%">
                                        <div class="post_topbar">
                                            <div class="usy-dt">

												You can make your event <a href="./eventdetails.php?idev=<?php echo $id_evento; ?>"><?php echo $nombre_ev; ?></a> recurrent for 52 weeks (one year) easily.
												We will create a copy of your event for the following dates:</br></br>

												<?php




												//sacamos el gmt
												$zona_gmt_array=explode('GMT',$hora_inicio_gmt);
												$zona_gmt=$zona_gmt_array[1];

												//sacamos las horas y minutos del gmt para calcular la diferencia
												$zona_gmt_h_m_array=explode(':',$zona_gmt);
												$zona_gmt_horas=$zona_gmt_h_m_array[0]; //la horas pueden ser positivas o negativas
												$zona_gmt_minutos=$zona_gmt_h_m_array[1];

												//echo "</br></br>$zona_gmt_horas -- $zona_gmt_minutos</br></br>";

												if($zona_gmt_horas>=0)
												{
													$dif_segundos_gmt=	$zona_gmt_horas*3600  +	$zona_gmt_minutos*60;
													//echo "</br></br>hola1: $dif_segundos_gmt=	$zona_gmt_horas*3600  +	$zona_gmt_minutos*60</br></br>";
												}
												else
												{
													$dif_segundos_gmt=	$zona_gmt_horas*3600  -	$zona_gmt_minutos*60;
													//echo "</br></br>hola2: $dif_segundos_gmt=	$zona_gmt_horas*3600  -	$zona_gmt_minutos*60</br></br>";
												}

												// echo "</br></br>$dif_segundos_gmt</br></br>"; 

												$time_weekly=$hora_inicio_unix+$dif_segundos_gmt;
												
												//echo "</br></br>$time_weekly=$hora_inicio_unix+$dif_segundos_gmt</br></br>";
												
												/*
												$ttt=strftime ("%e %b %G %R (%a.)", $time_weekly ); 
												echo "</br></br>$ttt</br></br>";
												*/
												
												//$ttt2=strftime (" %c ", $hora_inicio_unix );
												//echo "</br></br>$ttt2</br></br>";



												for($j;$j<52;$j++)
												{
														$time_weekly=$time_weekly+7*24*3600;
														$time_weekly_formateada=strftime ("%d %b %G %R (%a.)", $time_weekly ); 
														//$time_weekly_formateada=date("Y-m-d H:i:s", $time_weekly);
														$time_weekly_formateada=$time_weekly_formateada." GMT$zona_gmt";

														$ind=$j +1;

														echo "<p style=\"font-size: 70%\">$ind.	$time_weekly_formateada</p>";
												}


												//parametros codificados para pasar como parametros por url
												$id_evento_codif=$id_evento*49891-49;
												$codigo_evento1=substr($codigo_evento1,10,20);
												$codigo_evento1=md5($codigo_evento1);
												$codigo_evento1=substr($codigo_evento1,10,20);
												
												//echo $codigo_evento1;
												
												
												
												//calculo del precio por persona

												$query5="
												SELECT country, gdp_percapita_2017
												FROM gps_gdp_by_country
												WHERE country='$country_ev1';
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

												$price_per_event=round(0.36*$factor,2); 
												$price_total=round($price_per_event*52,2);
												$_SESSION['price']=$price_total;
												$tle="Weekly event";
												$_SESSION['title']=$tle;
												$p="./calender-2389150_1280.png";
												$_SESSION['photo']=$p;
												$ok="/events/makerecurrentexe.php?cod1=" . $id_evento_codif . "&cod2=" . $codigo_evento1;
												$fail="/events/makerecurrentshowinfo.php?idev=" . $id_evento ;
												$_SESSION['rediok']=$ok;
												$_SESSION['redifail']=$fail;
												echo "</br></br>Total price for 52 weekly events: $price_total EUR (€)</br></br>";
												
												// echo "</br></br>Note: Total price per event in $pais8: $price_per_event EUR (€)</br></br>";
												

												?>
												
												
												
												
												
												
												<?php		

												$itemid1='event_make_weekly';  //aqui pasamos la información para que la página de success no redirija con header location a la que corresponda
																			//seria un itemID para cada producto para que podamos identificarlo
												$itemname1="Event #$id_evento make weekly in $ciudad_ev";
												$productname1="Event make weekly in $ciudad_ev";
												$itemdescription1="Your event in $ciudad_ev will be made weekly for a year."; //lo usaremos tambien como product description
												$internalcodename1="$id_evento_codif|||||$codigo_evento1";   //pasamos las dos variables secretas del evento separadas por '|||||' para la pagina de success
												$amountprice1=$price_total;


												$itemid1 = base64_encode(openssl_encrypt($itemid1, 'AES-256-CBC', 'Vm95YWNyZWFydW5hQ2w0dmVwNHJhNHBhc2FybG9zZTNudG9z'));
												$itemname1 = base64_encode(openssl_encrypt($itemname1, 'AES-256-CBC', 'Vm95YWNyZWFydW5hQ2w0dmVwNHJhNHBhc2FybG9zZTNudG9z'));
												$productname1 = base64_encode(openssl_encrypt($productname1, 'AES-256-CBC', 'Vm95YWNyZWFydW5hQ2w0dmVwNHJhNHBhc2FybG9zZTNudG9z'));
												$itemdescription1 = base64_encode(openssl_encrypt($itemdescription1, 'AES-256-CBC', 'Vm95YWNyZWFydW5hQ2w0dmVwNHJhNHBhc2FybG9zZTNudG9z'));
												$internalcodename1 = base64_encode(openssl_encrypt($internalcodename1, 'AES-256-CBC', 'Vm95YWNyZWFydW5hQ2w0dmVwNHJhNHBhc2FybG9zZTNudG9z'));
												$amountprice1 = base64_encode(openssl_encrypt($amountprice1, 'AES-256-CBC', 'Vm95YWNyZWFydW5hQ2w0dmVwNHJhNHBhc2FybG9zZTNudG9z'));
 
?> 
												
												
												
												
												
												
												</br></br>
												
												
												<form name="payment_event_weekly" action="../payments/index.php" ENCTYPE="multipart/form-data" method="POST">

											<INPUT TYPE="text" id="itemid" name="itemid" value="<?php echo "$itemid1"; ?>"  hidden>
											<INPUT TYPE="text" id="itemname"name="itemname" value="<?php echo "$itemname1"; ?>" hidden >
											<INPUT TYPE="text" id="productname" name="productname" value="<?php echo "$productname1"; ?>" hidden>
											<INPUT TYPE="text" id="itemdescription" name="itemdescription" value="<?php echo "$itemdescription1"; ?>" hidden>
											<INPUT TYPE="text" id="internalcodename" name="internalcodename" value="<?php echo "$internalcodename1"; ?>" hidden>
											<INPUT TYPE="text" id="amountprice" name="amountprice" value="<?php echo "$amountprice1"; ?>" hidden>
											
											<br><br>
												
												
    											
												
												<?php // <button type="submit" style="width:100%;height:12%;cursor:pointer;" class="btn btn-dark"><i class="far fa-credit-card"></i> Pay</button> -- ?>
												
												
												<button type="submit" style="
										  background-color: #e65f00;  border: none;
										  color: white;
										  padding: 10px 11px;
										  text-align: center;
										  border-radius: 10px;
									  ">Continue to payment</button>
												
												
												</form>
												<br>
												
	<?php /*											

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
													window.location.replace("https://www.languageexchanges.com/events/makerecurrentexe.php?cod1=<?php echo $id_evento_codif ?>&cod2=<?php echo $codigo_evento1 ?>")
														alert('Transaction completed by ' + details.payer.name.given_name);
													});
													}
												}).render('#paypal-button-container');  
												</script>
												
	*/	?>									
												
	</div>	</div>	</div>	</div>	</div>	</div>	</div>	</div>	</div>	</div> 
	

</main>
</body>
</html>
