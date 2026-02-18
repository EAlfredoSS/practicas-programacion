<?php

require('bd');
session_start();
$identificador2017=$_SESSION['orden2017'];

if(!isset($identificador2017))
{
	die("You must be logged in in order to see this page");
}


//extraemos numero de evento
$cadena_recibida=$_GET['cod1'];

$pieces = explode("|||||", $cadena_recibida);

$id_evento_cod=$pieces[0];
$cod_codificado2=$pieces[1];



//decodificamos el numero de evento
$id_evento=($id_evento_cod+49)/49891;


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

$lengua_code=$fila['Idioma'];

//$lengua1=$idiomas_equiv["{$fila['Idioma']}"];
//$lengua1=substr($lengua1,0,14);



//$hora_creacion=$fila['Horacreacion'];
$nombre_ev=$fila['event_name'];
$descr_ev=$fila['event_desc'];
$hora_inicio_gmt=$fila['start_time'];

$hora_inicio_unix=$fila['unix_start_time'];

$ciudad_ev=$fila['city'];
$location_ev=$fila['location'];
$country_ev=$fila['country'];


$broadc=$fila['Broadcasted'];

$es_replica=$fila['Createdfromid'];

$codigo_evento1=$fila['Codigoevento'];


$id_del_local=$fila['id_local'];


$codigo_evento1=substr($codigo_evento1,10,20);
$codigo_evento1=md5($codigo_evento1);
$codigo_evento1=substr($codigo_evento1,10,20);


//extraemos código 2, que contiene el codigo codificado


//$cod_codificado2=$_GET['cod2'];

if($codigo_evento1!=$cod_codificado2)
	die ("Error making the event recurrent. Contact webmaster: webmaster@lingua2.com");





//aqui vemos si el evento es una replica de otro evento
if (!is_null( $es_replica ) )
	die('This event belongs to a serie of events.');




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
<title>Lingua2 Make the event weekly</title>

</head>
<body>







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

												Your event <a href="./eventdetails.php?idev=<?php echo $id_evento; ?>"><?php echo $nombre_ev; ?></a> has been made recurrent for 52 weeks.
												Go back to the <a href="../user/me.php">home page</a>.</br></br>

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
														//$time_weekly_formateada=strftime ("%d %b %G %R (%a.)", $time_weekly ); 
														$time_weekly_formateada=date("Y-m-d H:i:s", $time_weekly);
														$time_weekly_formateada=$time_weekly_formateada." GMT$zona_gmt";

														//$ind=$j +1;
														//echo "$ind.	$time_weekly_formateada</br>";
														$unix_time_event=strtotime ($time_weekly_formateada);
														
														//codigo del evento
														$rand111=rand(1, 99999999);
														$time2=$time_weekly;
														$timecod=$rand111+$time2;
														$timecod=md5("$timecod",false);
														$timecod=substr($timecod,0,19);
														
														$codigoevento1=md5("$timecod"."$rand111"."$time2",false);
														$codigoevento1=substr($codigoevento1,0,39);
														
														$fecha_formulario = $_POST['fecha_evento'];
														$fecha_unix = strtotime($fecha_formulario); 

														$query="INSERT INTO eventoslista (id_creador, Idioma, event_name, event_desc, unix_start_time, ...) 
														VALUES ('$id_creador', '$lengua_code', '$nombre_ev', '$descr_ev', '$fecha_unix', ...)";
															
	$query="INSERT INTO eventoslista (id_creador,Idioma,event_name, event_desc,unix_start_time,start_time,city,location,country,Codigoevento,Createdfromid,id_local)
	VALUES('$id_creador','$lengua_code','$nombre_ev','$descr_ev','$unix_time_event','$time_weekly_formateada','$ciudad_ev','$location_ev','$country_ev','$codigoevento1','$id_evento','$id_del_local')";
	
	
	$result=mysqli_query($link,$query);
	
	$boolean1=mysqli_affected_rows($link);
	
	
	
	//Comprobar que ha funcionado----------------------------------
	if(!$boolean1)
		die ('<br>\n\nThere was an error making the events recurrent. Contact webmaster sending a screenshot: webmaster@lingua2.com ');   
														
														
													
														
				
														
												}




												?>
												
												
												
												
	</div>	</div>	</div>	</div>	</div>	</div>	</div>	</div>	</div>	</div>


</main>
</body>
</html>











