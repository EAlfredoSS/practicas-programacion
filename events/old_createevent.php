<?php

// mi c&oacute;digo
require('../files/bd.php');


session_start();
$identificador2017 = $_SESSION['orden2017'];
$query_eventtypes = "SELECT eventtypeid, eventtypecode, eventtypename FROM eventtypeother ORDER BY eventtypeid";
$result_eventtypes = mysqli_query($link, $query_eventtypes);
?>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- jQuery UI -->
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>

<!-- jQuery UI CSS -->
<link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">



<head>
	<script src="https://kit.fontawesome.com/bb6243346a.js" crossorigin="anonymous"></script>
	<title>Create Event | Lingua2</title>
	<!-- Custom Theme files -->
	<link rel="stylesheet" href="../user/css/languages.css" media="all" />
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



	<!-- Global site tag (gtag.js) - Google Analytics -->
	<script async src="https://www.googletagmanager.com/gtag/js?id=UA-139626327-1"></script>
	<script>
		window.dataLayer = window.dataLayer || [];

		function gtag() {
			dataLayer.push(arguments);
		}
		gtag('js', new Date());
		gtag('config', 'UA-139626327-1');
	</script>

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
	<style>
		a {
			color: #e65f00;
		}
	</style>



	<script type="text/javascript" src="../public/js/jquery.min.js"></script>
	<script type="text/javascript" src="../public/js/popper.js"></script>
	<script type="text/javascript" src="../public/js/bootstrap.min.js"></script>
	<script type="text/javascript" src="../public/js/jquery.mCustomScrollbar.js"></script>
	<script type="text/javascript" src="../public/lib/slick/slick.min.js"></script>
	<script type="text/javascript" src="../public/js/scrollbar.js"></script>
	<script type="text/javascript" src="../public/js/script.js"></script>



</head>

<body>


	<?php
	require_once("../templates/header_simplified.html");
	?>


	<?php


	if (!isset($identificador2017)) {
		die("You must be logged in in order to use this functionality.");
	}



	//aqui solo vamos a permitir crear eventos si tiene menos de X eventos en el futuro
	$tiempo_corte5 = time() - 24 * 3600;;

	$query = "
SELECT * 
FROM eventoslista 
WHERE id_creador='$identificador2017' AND unix_start_time>'$tiempo_corte5' AND Createdfromid IS NULL
ORDER BY unix_start_time ASC";

	//die("<br/>$query<br/>");

	$result = mysqli_query($link, $query);
	$nuevos = mysqli_num_rows($result);

	if ($nuevos > 5)
		die('<br/><br/><p>The maximum amount of future events that you can have is 6.</p>');






	// Extraemos las coordenadas gps del usuario logueado

	$query_23 = "SELECT Gpslat, Gpslng FROM mentor2009 WHERE orden='$identificador2017' ";

	$result_23 = mysqli_query($link, $query_23);
	$nuevos_23 = mysqli_num_rows($result_23);
	if (!$nuevos_23)
		die('User does not exist or you disconnected. Login from the Homepage');

	$fila_23 = mysqli_fetch_array($result_23);
	$lat11 = $fila_23['Gpslat'];
	$lng11 = $fila_23['Gpslng'];

	if ($lat11 == 0 and $lng11 == 0)
		die("</br></br></br>You haven't added your location.To use this functionality you need to add your location first. Click <a href=\"../user/getgpsposition.php\">here</a>.");





	if (!empty($_POST['enviar']))                        ///ojoooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooo
	{
		$language_code = $_POST['language_code'] ?? null;
		$nombre_evento = $_POST['event_name']; //$nombre_evento= preg_replace("/[^A-Za-z0-9 ]/", '', $nombre_evento);
		$nombre_evento = strip_tags($nombre_evento);

		$descrip_evento = $_POST['event_desc']; //$descrip_evento=preg_replace("/[^A-Za-z0-9 ]/", '', $descrip_evento);
		$descrip_evento = strip_tags($descrip_evento);

		$idioma_evento = $_POST['language']; //$idioma_evento=preg_replace("/[^A-Za-z0-9 ]/", '', $idioma_evento);
		$idioma_evento = strip_tags($idioma_evento);

		$hora_evento = $_POST['start_hour'];
		$minuto_evento = $_POST['start_minute'];
		$fecha_evento = $_POST['start_date'];
		$gmt_evento = $_POST['gmt'];

		$country99 = $_POST['country99'];

		$city_evento = $_POST['city']; //$city_evento=preg_replace("/[^A-Za-z0-9 ]/", '', $city_evento);
		$city_evento = strip_tags($city_evento);
		$full_address_evento = $_POST['event_address']; //$full_address_evento=preg_replace("/[^A-Za-z0-9 ]/", '', $full_address_evento); 
		$full_address_evento = strip_tags($full_address_evento);
		$id_local_num = $_POST['id_local_event'];


		//die ("valor: $id_local_num");

		if (!empty($id_local_num)) {
			$query = "
			SELECT 
			lc.full_address_google, lc.name_local_google
	
			FROM locales lc
	
			WHERE lc.id_local = $id_local_num
	
			";
			$result = mysqli_query($link, $query);

			$num_rows_locals = mysqli_num_rows($result);

			if ($num_rows_locals) {

				$fila = mysqli_fetch_array($result);

				$local_name = $fila['name_local_google'];
				$full_addr = $fila['full_address_google'];
				$full_address_evento = $local_name . " - " . $full_addr;
			}
			$local_num = $id_local_num;
		} else {
			$local_num = -1;
		}

		//die ("valor2: $local_num");

		$event_start_time = "$fecha_evento $hora_evento:$minuto_evento:00 $gmt_evento";

		//die($event_start_time);

		$unix_time_evento = strtotime($event_start_time);

		if (!is_numeric($unix_time_evento))
			die("error in the date");

		//2012-10-03 20:00

		/*echo "
	nombre $nombre_evento<br/>
	$idioma_evento<br/>
	descrip $descrip_evento<br/>
	$event_start_time<br/>
	$unix_time_evento<br/>
	city $city_evento<br/>
	address $full_address_evento<br/>
	
	";*/


		//codigo del evento
		$time111 = time();
		$timecod = $time111 + 150;
		$timecod = md5("$timecod", false);
		$timecod = substr($timecod, 0, 19);

		$codigoevento1 = md5("$timecod" . "$time111", false);
		$codigoevento1 = substr($codigoevento1, 0, 39);




		if (empty($nombre_evento))
			die("Event name cannot be empty. Go back to the form.");

		if (empty($descrip_evento))
			die("Event description cannot be empty. Go back to the form.");

		if (empty($idioma_evento))
			die("Language cannot be empty. Go back to the form.");

		if (empty($hora_evento))
			die("Event time hour field cannot be empty. Go back to the form.");

		if (empty($minuto_evento))
			die("Event time minute field cannot be empty. Go back to the form.");

		if (empty($fecha_evento))
			die("Event data field cannot be empty. Go back to the form.");

		if (empty($gmt_evento))
			die("Event GMT field cannot be empty. Go back to the form.");

		if (empty($country99))
			die("Event country field cannot be empty. Go back to the form.");

		if (empty($city_evento))
			die("Event city field cannot be empty. Go back to the form.");

		if (empty($full_address_evento))
			die("Full address field cannot be empty. Go back to the form.");



		//comprobamos que el evento no se ponga en fechas del pasado	

		if ($unix_time_evento < $tiempo_corte5)
			die("The event cannot be established in a past date. Go back to the form and select a future date.");



		if ($local_num == -1) {
			$query = "INSERT INTO eventoslista (id_creador,Idioma,event_name, event_desc,unix_start_time,start_time,city,location,country,Codigoevento,id_local)
	VALUES('$identificador2017','$idioma_evento','$nombre_evento','$descrip_evento','$unix_time_evento','$event_start_time','$city_evento','$full_address_evento','$country99','$codigoevento1',NULL)";
		} else {
			$query = "INSERT INTO eventoslista (id_creador,Idioma,event_name, event_desc,unix_start_time,start_time,city,location,country,Codigoevento,id_local)
	VALUES('$identificador2017','$idioma_evento','$nombre_evento','$descrip_evento','$unix_time_evento','$event_start_time','$city_evento','$full_address_evento','$country99','$codigoevento1','$local_num')";
		}


		//die("$query"); 


		$result = mysqli_query($link, $query);

		$boolean1 = mysqli_affected_rows($link);

		//Comprobar que ha funcionado----------------------------------
		if (!$boolean1)
			die('\n<br>\n\nThere was an error and your application was not submitted');


		//extraemos el id del ultimo evento insertado para pasarlo como parametro por url
		if ($boolean1) {
			$last_id = mysqli_insert_id($link);
			//echo "New record created successfully. Last inserted ID is: " . $last_id;
		} else {
			die("Error 956. Contact webmaster");
		}



		//header("Location: ./event_success.php");
	?> <script>
			window.location.replace("./event_success.php?evid=<?php echo $last_id; ?>");
		</script> <?php


				}



					?>



	<div class="main-section">
		<div class="container">
			<div class="main-section-data">
				<div class="row" style="justify-content:center;">
					<div class="col-lg-6 col-md-8 no-pd">
						<div class="main-ws-sec">


							<h1 style="color:dimgrey;font-size: 40px;">Create Event</h1>

							<hr>
							</br>

							<div class="posts-section mb-4">
								<div class="posty">
									<div class="post-bar no-margin p-3">
										<div class="job-description">

											<p style="font-size:12px"> This is how it works:</p>

											<p style="font-size:12px"> If you <b>are a participant</b>, you can find interesting events in your city. Check the event and write your comment if you are taking part in it.</p>

											<p style="font-size:12px"> If you <b>want to promote an international event</b> in your city, you can set up an event to create a language exchange group or to promote your products or establishments.</p>

											<p style="font-size:12px"> If you <b>are a professional teacher</b> you can set up an event to find customers. You can offer the participants one-on-one classes or group classes.</p>


											<p style="font-size:12px"> When your <a href="./createevent.php">create an event</a> you will have the chance to invite our Lingua2 users living in your city</p>

											<p style="font-size:10px"> Fields marked with an asterisk (*) are required</p>
											</br>

										</div>
									</div>
								</div>
							</div>


							<div class="posts-section">
								<div class="posty">
									<div class="post-bar no-margin p-3">
										<div class="job-description">


											<form name="formevent" enctype="multipart/form-data" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST" target="_self" onsubmit="return validate();">

												<div class="form-group">

													<label for="event_name" style="margin-bottom:5px;color:dimgrey;">*Event Name:</label>

													<input type="text" class="form-control" name="event_name" id="event_name" maxlength="50" placeholder="Insert your event name" required /><label style="color:#F00;" id="event_name_error">
														<br /><br />
														<label for="event_desc" style="margin-bottom:5px;color:dimgrey;">*Event Description and contact phone:</label><br />
														<textarea rows="6" cols="27" class="form-control" placeholder="Give as many details as you can" maxlength="255" name="event_desc" id="event_desc" required></textarea><br /><label style="color:#F00;" id="event_desc_error"></label>
														<br /><br />
														<label for="start_date" style="margin-bottom:5px;color:dimgrey;">*Start Date:</label><br />
														<input type="date" class="form-control" name="start_date" id="start_date" required />
														<br /><br />

														<label for="start_hour" style="margin-bottom:5px;color:dimgrey;">*Select Hour:</label><br />

														<select name="start_hour" id="start_hour" class="form-control" style="appearance:listbox" required>
															<option value="">Select Hour</option>
															<option value="00">00</option>
															<option value="01">01</option>
															<option value="02">02</option>
															<option value="03">03</option>
															<option value="04">04</option>
															<option value="05">05</option>
															<option value="06">06</option>
															<option value="07">07</option>
															<option value="08">08</option>
															<option value="09">09</option>
															<option value="10">10</option>
															<option value="11">11</option>
															<option value="12">12</option>
															<option value="13">13</option>
															<option value="14">14</option>
															<option value="15">15</option>
															<option value="16">16</option>
															<option value="17">17</option>
															<option value="18">18</option>
															<option value="19">19</option>
															<option value="20">20</option>
															<option value="21">21</option>
															<option value="22">22</option>
															<option value="23">23</option>

														</select><br /><br />

														<label for="start_minute" style="margin-bottom:5px;color:dimgrey;">*Select Minute:</label><br />

														<select name="start_minute" id="start_minute" class="form-control" style="appearance:listbox" required>
															<option value="">Select minute</option>

															<option value="00">00</option>
															<option value="05">05</option>
															<option value="10">10</option>
															<option value="15">15</option>
															<option value="20">20</option>
															<option value="25">25</option>
															<option value="30">30</option>
															<option value="35">35</option>
															<option value="40">40</option>
															<option value="45">45</option>
															<option value="50">50</option>
															<option value="55">55</option>


														</select><br /><br />


														<label for="gmt" style="margin-bottom:5px;color:dimgrey;">*Select Time Zone</label><br />

														<select name="gmt" id="gmt" class="form-control" style="appearance:listbox" required>

															<option value="GMT-12:00">(GMT -12:00) Eniwetok, Kwajalein</option>
															<option value="GMT-11:00">(GMT -11:00) Midway Island, Samoa</option>
															<option value="GMT-10:00">(GMT -10:00) Hawaii</option>
															<option value="GMT-09:30">(GMT -9:30) Taiohae</option>
															<option value="GMT-09:00">(GMT -9:00) Alaska</option>
															<option value="GMT-08:00">(GMT -8:00) Pacific Time (US &amp; Canada)</option>
															<option value="GMT-07:00">(GMT -7:00) Mountain Time (US &amp; Canada)</option>
															<option value="GMT-06:00">(GMT -6:00) Central Time (US &amp; Canada), Mexico City</option>
															<option value="GMT-05:00">(GMT -5:00) Eastern Time (US &amp; Canada), Bogota, Lima</option>
															<option value="GMT-04:30">(GMT -4:30) Caracas</option>
															<option value="GMT-04:00">(GMT -4:00) Atlantic Time (Canada), Caracas, La Paz</option>
															<option value="GMT-03:30">(GMT -3:30) Newfoundland</option>
															<option value="GMT-03:00">(GMT -3:00) Brazil, Buenos Aires, Georgetown</option>
															<option value="GMT-02:00">(GMT -2:00) Mid-Atlantic</option>
															<option value="GMT-01:00">(GMT -1:00) Azores, Cape Verde Islands</option>
															<option value="GMT+00:00">(GMT) Western Europe Time, London, Lisbon, Casablanca</option>
															<option value="GMT+01:00" selected="selected">(GMT +1:00) Brussels, Copenhagen, Madrid, Paris</option>
															<option value="GMT+02:00">(GMT +2:00) Kaliningrad, South Africa</option>
															<option value="GMT+03:00">(GMT +3:00) Baghdad, Riyadh, Moscow, St. Petersburg</option>
															<option value="GMT+03:30">(GMT +3:30) Tehran</option>
															<option value="GMT+04:00">(GMT +4:00) Abu Dhabi, Muscat, Baku, Tbilisi</option>
															<option value="GMT+04:30">(GMT +4:30) Kabul</option>
															<option value="GMT+05:00">(GMT +5:00) Ekaterinburg, Islamabad, Karachi, Tashkent</option>
															<option value="GMT+05:30">(GMT +5:30) Bombay, Calcutta, Madras, New Delhi</option>
															<option value="GMT+05:45">(GMT +5:45) Kathmandu, Pokhara</option>
															<option value="GMT+06:00">(GMT +6:00) Almaty, Dhaka, Colombo</option>
															<option value="GMT+06:30">(GMT +6:30) Yangon, Mandalay</option>
															<option value="GMT+07:00">(GMT +7:00) Bangkok, Hanoi, Jakarta</option>
															<option value="GMT+08:00">(GMT +8:00) Beijing, Perth, Singapore, Hong Kong</option>
															<option value="GMT+08:45">(GMT +8:45) Eucla</option>
															<option value="GMT+09:00">(GMT +9:00) Tokyo, Seoul, Osaka, Sapporo, Yakutsk</option>
															<option value="GMT+09:30">(GMT +9:30) Adelaide, Darwin</option>
															<option value="GMT+10:00">(GMT +10:00) Eastern Australia, Guam, Vladivostok</option>
															<option value="GMT+10:30">(GMT +10:30) Lord Howe Island</option>
															<option value="GMT+11:00">(GMT +11:00) Magadan, Solomon Islands, New Caledonia</option>
															<option value="GMT+11:30">(GMT +11:30) Norfolk Island</option>
															<option value="GMT+12:00">(GMT +12:00) Auckland, Wellington, Fiji, Kamchatka</option>
															<option value="GMT+12:45">(GMT +12:45) Chatham Islands</option>
															<option value="GMT+13:00">(GMT +13:00) Apia, Nukualofa</option>
															<option value="GMT+14:00">(GMT +14:00) Line Islands, Tokelau</option>
														</select><br /><br />
														<!--
															<label for="gmt" style="margin-bottom:5px;color:dimgrey;">*Select Language or Event type :</label><br />

															-->
														<label for="event_type_main" style="margin-bottom: 5px; color: dimgrey;">*Select Event Type:</label><br />
														<select id="event_type_main" name="event_type_main" class="form-control" required>
															<option value="">-- Select --</option>
															<option value="language">Language event</option>
															<option value="multi">Multilanguage or other event type</option>
														</select>
														<br /><br />

														<div id="language_event_block" style="display:none;">
															<label for="eventtypeother" style="margin-bottom: 5px; color: dimgrey;">*Language Event Type:</label><br />
															<select id="eventtypeother" name="eventtypeother" class="form-control">
																<?php while ($row = mysqli_fetch_assoc($result_eventtypes)): ?>
																	<option value="<?= htmlspecialchars($row['eventtypecode']) ?>">
																		<?= htmlspecialchars($row['eventtypename']) ?>
																	</option>
																<?php endwhile; ?>
															</select>
														</div>

														<div id="multi_event_block" style="display:none;">
															<label for="language_search" style="margin-bottom: 5px; color: dimgrey;">*Search Language:</label><br />
															<input type="text" id="language_search" name="language_search" class="form-control" placeholder="Type a language...">
															<input type="hidden" id="language_code" name="language_code">
															<div id="code_display" style="margin-top: 5px; color: green;"></div>

														</div>
														<br /><br />



														<br /><br />








														<?php





														// AQUI EXTRAEMOS LA CIUDAD MAS CERCANA DE LA TABLA gpscities Y HACEMOS UN UPDATE EN CITY


														$latitud1 = $lat11;
														$longitud1 = $lng11;

														//die("$latitud1  -  $longitud1 ");


														$query = "
		SELECT 
		gc.city_ascii, gc.country,

		(acos(sin(radians(gc.lat)) * sin(radians($latitud1)) + 
		cos(radians(gc.lat)) * cos(radians($latitud1)) * 
		cos(radians(gc.lng) - radians($longitud1))) * 6378) 

		AS distanciaPunto1Punto2

		FROM gpscities gc

		WHERE 1


		ORDER BY distanciaPunto1Punto2 

		LIMIT 1


		";
														//echo "<br/><br/>$query<br/><br/>";

														$result = mysqli_query($link, $query);
														if (!mysqli_num_rows($result))
															echo "</br>Error 506. Contact webmaster.";
														$fila = mysqli_fetch_array($result);

														$city88 = $fila['city_ascii'];  // ATENCION city_ascii, no city a secas
														$country88 = $fila['country'];

														//die("$city88  -  $country88 ");

														//$distancia88= round($fila['distanciaPunto1Punto2'],2);

														?>



														<label style="color:#F00;" id="language_error"></label>
														<label for="city" style="margin-bottom:5px;color:dimgrey;">*Nearest city (approximately):</label><br />
														<input type="text" name="city" id="city" class="form-control" style="background-color:white; margin-bottom:5px;" value="<?php echo "$city88"; ?>" readonly /><label style="color:#F00;" id="location_error"></label>
														<br />
														<a href="../user/getgpsposition.php" style="font-size: 70%;">Not your city? Update your location</a>

														<br /> <br />



														<?php



														$query = "
		SELECT 
		lc.id_local, lc.full_address_google,lc.country_google,lc.city_google,lc.name_local_google,

		(acos(sin(radians(lc.lat)) * sin(radians($latitud1)) + 
		cos(radians(lc.lat)) * cos(radians($latitud1)) * 
		cos(radians(lc.lng) - radians($longitud1))) * 6378) 

		AS distanciaPunto1Punto2

		FROM locales lc

		HAVING distanciaPunto1Punto2<20

		ORDER BY distanciaPunto1Punto2 ASC

		LIMIT 1000


		";
														//echo "<br/><br/>$query<br/><br/>";

														$result = mysqli_query($link, $query);

														$num_rows_locals = mysqli_num_rows($result);

														if ($num_rows_locals) {

														?>
															<label for="id_local_event" style="margin-bottom:5px;color:dimgrey;">*Full Address of the event</label><br />
															<select name="id_local_event" class="form-control" style="appearance:listbox" required>
																<?php
																for ($ii = 0; $ii < $num_rows_locals; $ii++) {
																	$fila = mysqli_fetch_array($result);

																	$local_id = $fila['id_local'];
																	$full_addr = $fila['full_address_google'];
																	//$city88= $fila['city_google'];  // ATENCION city_ascii, no city a secas
																	//$country88= $fila['country_google'];
																	$dist = $fila['distanciaPunto1Punto2'];
																	$dist = number_format($dist, 2);
																	$name_local = $fila['name_local_google'];
																	$event_adress = "";
																?>

																	<option value="<?php echo $local_id; ?>"><?php echo $dist . " Km - " . $name_local . " - " . $full_addr; ?></option>


																<?php
																}
															} else {
																//$id_local_event=-1;
																?>

																<label for="event_address" style="margin-bottom:5px;color:dimgrey;">*Full Address of the event</label><br />
																<textarea rows="12" cols="27" class="form-control" name="event_address" id="event_address" required></textarea>
																<br /><br />


															<?php }
															?>

															<input type="hidden" name="country99" id="country99" maxlength="45" value="<?php echo $country88; ?>" />

															<br /><br />

															<button type="submit" name="enviar" value="Create event" style="background-color: #e65f00;  border: none;color: white;padding: 10px 11px;text-align: center;border-radius: 10px;">Create new event</button>
												</div>
											</form>

										</div>
									</div>
								</div>
							</div>

						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<script>
		$(function() {
			$("#language_search").autocomplete({
				source: "events/search_languages.php",
				minLength: 2,
				select: function(event, ui) {
					$("#language_code").val(ui.item.code);
					// Mostrar el código en pantalla para depuración
					$("#code_display").text("Código seleccionado: " + ui.item.code);

				}
			});
		});
	</script>


	<script>
		document.getElementById('event_type_main').addEventListener('change', function() {
			const value = this.value;
			document.getElementById('language_event_block').style.display = value === 'language' ? 'block' : 'none';
			document.getElementById('multi_event_block').style.display = value === 'multi' ? 'block' : 'none';
		});
	</script>


</body>