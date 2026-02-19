<?php

session_start();

require('../templates/header_simplified.html');
require('../files/bd.php');
require('../funcionesphp/funciones_idiomas_usuario.php');



$my_id_orden=$_SESSION['orden2017'];
//$my_id_orden=4533;
$teacher_id_orden=$_GET['tid'];

// esto de aquí lo hacemos porque desde la página user/u.php se envía el teacher id a traves de post, no de get
if(empty($teacher_id_orden))
{
  $teacher_id_orden=$_POST['tid'];
}

//$teacher_id_orden=4588;
$language_taught=$_POST['language_to_learn'];

//tenemos que comprobar que la solicitud de amistad haya sido aceptada

$query101="SELECT n_pareja FROM couples2009antiguos WHERE user_id_2='$my_id_orden' AND user_id_1='$teacher_id_orden'";
$result101=mysqli_query($link,$query101);
$fila101=mysqli_fetch_array($result101);
$resultado101=$fila101['n_pareja'];

$query102="SELECT n_pareja FROM couples2009antiguos WHERE user_id_1='$my_id_orden' AND user_id_2='$teacher_id_orden' ";
$result102=mysqli_query($link,$query102);
$fila102=mysqli_fetch_array($result102);
$resultado102=$fila102['n_pareja'];

//echo " $resultado101 ";
//echo " $resultado102 ";

if( empty($resultado101) AND empty($resultado102) )
		die("Forbidden. Teacher incorrect. --$my_id_orden"); 

//date_default_timezone_set($my_timeshift);

$query77="SELECT timeshift, Gpslat, Gpslng FROM mentor2009 WHERE orden='$my_id_orden' ";
$result77=mysqli_query($link,$query77);
if(!mysqli_num_rows($result77))
		die("You need to log in.");
$fila77=mysqli_fetch_array($result77);

$my_timeshift=$fila77['timeshift'];
$latitud1=$fila77['Gpslat'];
$longitud1=$fila77['Gpslng'];
//date_default_timezone_set($my_timeshift);

$query88="SELECT nombre, fotoext, timeshift, Gpslat, Gpslng FROM mentor2009 WHERE orden='$teacher_id_orden' ";
$result88=mysqli_query($link,$query88);
if(!mysqli_num_rows($result88))
		die("Teacher does not exist.");
$fila88=mysqli_fetch_array($result88);

$latitud2=$fila88['Gpslat']; 
$longitud2=$fila88['Gpslng'];
//echo "----------$query88--------------------- $latitud2 -- $longitud2 ---------------------------------- ";

$teacher_timeshift=$fila88['timeshift'];

//sacamos la foto
$extension_teacher = $fila88['fotoext'];
$path_photo="../uploader/upload_pic/thumb_$teacher_id_orden"."."."$extension_teacher";

//echo "foto: $extension_teacher -- $path_foto  --";

//echo "</br></br>$path_photo</br></br>";

if ( !file_exists($path_photo) ) :
	$path_photo="../uploader/default.jpg";
endif;

//nombre prof

$t_name=$fila88['nombre'];
$palabras = explode (" ", $t_name);
$t_name=ucfirst($palabras[0]);

//$my_timeshift="1";


// Display the information
//echo "<h2>Form Information</h2>";
	
	
// Check if form is submitted
if (isset($_POST['start_date'], $_POST['start_time'], $_POST['duration'], $language_taught )  )
{
	
	
	$number_repetitions = $_POST['repetitions'];
	$recurrence_type = $_POST['recurrence'];
	
	$description=$_POST['description_of_sessions'];
	
	//si la clase se crea a través de un evento de dos o más repeticiones, marcaremos la variable a 1. Si no, cero.
		
	if($number_repetitions < 2) //uno o menor
	{
		$is_recurrent=0;
		$recurrence_type='none';
	}
	else
	{
		$is_recurrent=1;
		
		switch ($recurrence_type) 
		{
			case "every_week":
				$interval_recurrence="P7D";
				break;
			case "every_2_weeks":
				$interval_recurrence="P14D";
				break;
			case "every_4_weeks":
				$interval_recurrence="P28D";
				break;
		}
	}
	
	
	echo "$number_repetitions -- $recurrence_type"; 
	
	// Get form data
	$startDate = $_POST['start_date'];
	$startTime = $_POST['start_time'];
	$duration = (int) $_POST['duration'];
	
	$online_u_offline=$_POST['presencial_u_online'];
	
	if($online_u_offline==1) //si es online el local del encuentro será null
		$local_del_encuentro=null; 
	else
		$local_del_encuentro=$_POST['id_local_event']; //echo "hola: $local_del_encuentro";
	
	//prices
	$hourly_rate='11'; // euros per hour
	$total_session_price=$hourly_rate*$duration/60; //euros

	// estas dos líneas es para pasar la variable $startDate1 formateada como lo requiere la "DateTime" más abajo
	$dateParts = explode('/', $startDate);
	$startDate1 = $dateParts[1] . '/' . $dateParts[0] . '/' . $dateParts[2];
	//--------------------
	
	$zona = new DateTimeZone($my_timeshift);
	
	//--------------------
		
	// Calculate start date
	$startDate = new DateTime($startDate1 . " " . $startTime , $zona);
	
	// Calculate end date and time
	$endDate = new DateTime($startDate1 . " " . $startTime , $zona);
	$endDate->add(new DateInterval("PT{$duration}M"));

	for($iii=0;$iii<$number_repetitions;$iii++)
	{	

		//local dates to insert in the data base. We will have to add or substract the time shift of the student user
		$date_start_insert_db = $startDate->format('Y-m-d H:i:s'); // echo " $date_start_insert_db ";
		$date_end_insert_db = $endDate->format('Y-m-d H:i:s'); //echo "$date_end_insert_db ";
		
			
		////unix time to insert in the data base. time shift of the student user should have been substracted or added before.
		//$unixtime_start_insert_db = strtotime($date_start_insert_db);  //echo " $unixtime_start_insert_db ";
		//$unixtime_end_insert_db = strtotime($date_end_insert_db); //echo " $unixtime_end_insert_db ";
		$unixtime_start_insert_db = $startDate->getTimestamp();
		$unixtime_end_insert_db = $endDate->getTimestamp();
		
		//local dates of the student to show in his screen
		$formattedStartDate = $startDate->format('D, d M Y');
		$formattedStartTime = $startDate->format('H:i');

		$formattedEndDate = $endDate->format('D, d M Y');
		$formattedEndTime = $endDate->format('H:i');
		
	
		echo "<ul>";
		echo "<li>Start Date: $formattedStartDate</li>";
		echo "<li>Start Time: $formattedStartTime</li>";
		echo "<li>Duration: $duration minutes</li>";
		echo "<li>End Date: $formattedEndDate</li>";
		echo "<li>End Time: $formattedEndTime</li>";
		echo "</ul>";
		 
		
		
		
		$query="
		
		INSERT INTO tracker (	created_from_recurrent,id_user_teacher,id_user_student,time_shift_student,start_time_unix,end_time_unix,date_start_local,date_end_local,session_lenght_minutes,
								language_taught,hourly_rate_original, price_session_total, description_session, created_timestamp, id_local, onlineonsite )
		VALUES(					'$is_recurrent','$teacher_id_orden','$my_id_orden','$my_timeshift','$unixtime_start_insert_db','$unixtime_end_insert_db','$date_start_insert_db','$date_end_insert_db','$duration',
								'$language_taught','$hourly_rate','$total_session_price','$description',NOW(), '$local_del_encuentro', '$online_u_offline')
		
		";
		
		//echo " $query ";
	 
		if (!mysqli_query($link, $query)) 
		{
			echo "Error 4653. Contact webmaster.";
		}
		
		//aqui añadimos el tiempo segun sea una semana, 2, 4...
		$startDate->add(new DateInterval("$interval_recurrence"));		
		$endDate->add(new DateInterval("$interval_recurrence"));

	  
	}
	
	exit(0); 
} 

?>

<!DOCTYPE html>
<html>
<head>
  <title>Create class</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-ui/1.13.2/themes/required/jquery-ui.css">
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>
</head>
<style>
main{
	padding:0;
}
/*.wrapper{
	padding:60px 0px;
}*/
.forum-sec {
	display:flex;
	background-color: #fff;
}
.forum-links {
    background-color: #fff;
    padding: 10px 0;
}

.forum-links ul {
    list-style-type: none;
    display: flex;
	margin-left: 30px;
	gap: 20px;
}

.forum-links ul li {
    text-align: center;
    margin-right: 20px; /* Espacio entre los elementos */
	border-bottom: 2px solid transparent;
}

.forum-links ul li a {
    display: inline-block;
    padding: 10px 0;
    text-decoration: none;
    color: #999;
    font-weight: normal;
    font-size: 16px;
    transition: color 0.3s ease;
}
.forum-links-btn {
    display: none;
}
.forum-links ul li.active {
    border-color: #e65f00;
}
.forum-links ul li.active a {
    color: #e65f00;
}

@media screen and (max-width: 768px) { /* Puedes ajustar el ancho según tu necesidad */
    .forum-links-btn {
        display: block;
    }
}

.cuerpo{
	display:flex;
	/*background-color:#babaff;*/
	padding-left: 7px;
	margin: 10px 20%;
	width: 60%;
}
.contenedor1{
	/*background-color:yellow;*/
	width: 70%;
	padding: 20px 0px;
}
.post-comment-box, .usr-question{
	background-color: #fff;
    padding: 25px 25px 10px 25px;
	border-bottom: 1px solid #e5e5e5;
}
/* Topics that you would like in the lesson */
.post_comment_sec form textarea {
    float: left;
    width: 100%;
    height: 100px;
    border: 1px solid #dce2eb;
    padding: 15px;
    resize: none;
}
.post-comment-box h3 {
    float: left;
    width: 100%;
    color: #000000;
    font-size: 18px;
    font-weight: 600;
    margin-bottom: 30px;
}
#charCount { 
    color: #aaa; 
    font-size: 12px; 
	text-align: right;
}
/* On site */
.post_comment_sec form button, .input-proposed {
    color: #ffffff;
    font-size: 14px;
    background-color: #e65f00;
    padding: 10px 25px;
    border: 0;
    font-weight: 600;
    margin-top: 20px;
	margin-bottom:20px;
    cursor: pointer;
}
/* Para contenedor 2 parte de la columna 2 last lessons */
.contenedor2{
	width: 40%;
	padding: 20px;
}
.widget{
	float: left;
	width: 100%;
	background-color: #fff;
	border-left: none;
	border-right: none;
	border-bottom: none;
}
.widget-feat {
    padding: 25px 20px;
}
.widget-feat ul {
	display:flex;
	gap: 20px;
}
.widget-feat ul li {
    float: left;
    width: 25%;
    text-align: center;
}
.widget-feat ul li i {
  display: block;
  font-size: 18px;
  margin-bottom: 9px;
  margin-left: 18%;
  margin-top: 10px;
}
.widget-feat ul li span {
    display: block;
    color: #686868;
    font-size: 16px;
    font-weight: 500;
}
.widget-feat ul li i.fa-heart {
    color: #53d690;
}
.widget-feat ul li i.fa-comment {
    color: #e44d3a;
}
.widget-feat ul li i.fa-share-alt {
    color: #51a5fb;
}
.widget-feat ul li i.fa-eye {
    color: #00b540;
}
/* Last lessons */
.title-wd {
    float: left;
    width: 100%;
    color: #000000;
    font-size: 18px;
    font-weight: 600;
    border-bottom: 1px solid #e5e5e5;
    padding: 25px 20px;
}
.widget-user ul {
    float: left;
    width: 100%;
    padding: 15px;
}

.widget-user li {
  display: flex; 
  align-items: center; 
  padding: 10px; 
}

.usr-ms-img {
  width: 40px; 
  height: 40px; 
  margin-right: 15px;
}

.usr-mg-info {
  flex-grow: 1;
}

.usr-mg-info h3 {
  margin: 0; 
  font-size: 16px; 
}

.usr-mg-info p {
  font-size: 14px; 
  color: #888;
}

.widget-user li span {
  margin-left: auto;
  font-size: 16px;
}
.usr-msg-details{
	display:flex;
}
/*.widget-adver{
	background-color: #7d7d7d;
	height: 25%;
}*/
/* Contenedor */

/* BOTONES DE DESPUES */
#preview-container {
  padding: 20px;
  background-color: #f9f9f9;
  border: 1px solid #ccc;
  margin-top: 20px;
  display: none;
}
#preview-list {
  list-style-type: none;
  padding: 0;
}

button {
	color: #ffffff;
    font-size: 14px;
    background-color: #e65f00;
    padding: 10px 25px;
    border: 0;
    font-weight: 600;
    margin-top: 20px;
    cursor: pointer;
}

/* Hora y fetcha */

/* Estilo general del calendario */
.ui-datepicker {
  background: #ffffff;
  border: 2px solid #e65f00;
  border-radius: 12px;
  padding: 10px;
  box-shadow: 0px 4px 15px rgba(0, 0, 0, 0.2);
  font-family: 'Arial', sans-serif;
}

/* Flechas de navegación */
.ui-datepicker-prev, .ui-datepicker-next {
  background: #e65f00;
  color: white;
  border-radius: 5px;
  padding: 5px;
  transition: all 0.3s ease-in-out;
}

.ui-datepicker-prev:hover, .ui-datepicker-next:hover {
  background: #cc5200;
}

/* Mes y año en el título */
.ui-datepicker-title {
  font-weight: bold;
  font-size: 18px;
  color: #e65f00;
}

/* Estilo de los días del calendario */
.ui-datepicker-calendar td {
  text-align: center;
}

/* Días normales */
.ui-datepicker-calendar td a {
  display: block;
  text-align: center;
  background: white;
  color: #333;
  padding: 10px;
  border-radius: 6px;
  transition: all 0.3s ease-in-out;
  font-weight: bold;
  text-decoration: none;
}

/* Día actual (hoy) */
.ui-datepicker-today a {
  background: #e65f00 !important;
  color: white !important;
  font-weight: bold;
  border-radius: 50%;
}

/* Días seleccionados */
.ui-datepicker-calendar td a:hover, 
.ui-datepicker-calendar .ui-state-active {
  background: #e65f00 !important;
  color: white !important;
  border-radius: 50%;
}

/* Días deshabilitados (otros meses) */
.ui-datepicker-calendar .ui-state-disabled {
  color: #aaa !important;
  opacity: 0.6;
}
/* Contenedor del encabezado del calendario */
.ui-datepicker-header {
  display: flex;
  justify-content: space-between; /* Separa los elementos */
  align-items: center;
  padding: 10px;
}

/* Título del mes y año */
.ui-datepicker-title {
  font-size: 18px;
  font-weight: bold;
  color: #e65f00;
  order: -1; 
}

/* Botones de navegación (prev y next) */
.ui-datepicker-prev, .ui-datepicker-next {
  margin: 0 5px;
}
/* Estilos para el select */
#start_hour, #start_minute, #start_am_pm  {
    padding: 8px;
    border-radius: 5px;
    border: 1px solid #ccc;
    background-color: #fff;
    font-size: 14px;
    width: 70px;  /* Ajuste el ancho */
    text-align: center;
    appearance: none;
    -webkit-appearance: none;
    -moz-appearance: none;
    display: inline-block;
    width: auto;
}

/* Personalizar el estilo cuando el select está enfocado */
#start_hour:focus, #start_minute:focus, #start_am_pm:focus {
    border-color: #e65f00; /* Borde azul al enfocar */
    outline: none;
}

/* Estilo para los elementos dentro de los select */
option {
    padding: 10px;
    text-align: center;
}
</style>
<body>  
<section class="forum-sec">
	<div class="container">
		<div class="forum-links">
			<ul>
				<li class="active"><a href="#" title="">Latest</a></li>
				<li><a href="#" title="">Unanswered</a></li>
				<li><a href="#" title="">Treading</a></li>
				<li><a href="#" title="">Popular This Week</a></li>
				<li><a href="#" title="">Popular of Month</a></li>
			</ul>
		</div><!--forum-links end-->
		<div class="forum-links-btn">
			<a href="#" title=""><i class="fa fa-bars"></i></a>
		</div>
	</div>
</section>
<!-- echo "<h2>Form Information</h2>"; -->
<section class="cuerpo">
	<div class="contenedor1">
		<div class="usr-question">
	<br>
We need to show the time and date of the start time for the teacher according his local time. Therefore, we must consider the time shift difference<br>
of the teacher and the student.
<br><br>
When doing the INSERT in the data base, time shifts of the student user is still not considered.
 
<br><br>

  <form id="class-form" method="post" action="<?php echo $_SERVER['PHP_SELF']."?tid=$teacher_id_orden"; ?>">
  <br>
	<br>
	My user id: <?php echo "#$my_id_orden"; ?> 
	<br>
	My teachers id: <?php echo "#$teacher_id_orden -- $t_name"; ?>   <img src="<?php echo "$path_photo"; ?>" alt="" />
	<br>
	<br>
	Select language to be taught and your level (if you want to learn a language that is not in your list you need to add it -link to the addlanguage.php-)<br>
	* free language exchange means that the teacher offers his language for one of your languages, not for money. Your partner will teach you the selected language and, in return, you will teach your partner one of the languages that you know during the meeting.<br><br>
	
	<select id="language_to_learn" name="language_to_learn">
 
	<?php
	list($idiomas_profe_array_multidim["$my_id_orden"], $idiomas_profe_full_name_array_multidim["$my_id_orden"], 
							$idiomas_profe_level_array_multidim["$my_id_orden"], 
							$idiomas_profe_forshare_array_multidim["$my_id_orden"], 	$idiomas_profe_price_array_multidim["$my_id_orden"], 
							$idiomas_profe_typeofexchange_array_multidim["$my_id_orden"], 
							$idiomas_profe_priceorexchangetext_array_multidim["$my_id_orden"], $idiomas_profe_level_image_array_multidim["$my_id_orden"], 
							$idiomas_profe_2letters_array_multidim["$my_id_orden"])
							= lenguas_que_conoce_usuario($teacher_id_orden,$link);
							
							
							
	for($uu=0;$uu<count($idiomas_profe_array_multidim["$my_id_orden"]);$uu++ )
	{
		if($idiomas_profe_forshare_array_multidim["$my_id_orden"][$uu]==1)
		{
		?> <option value="<?php echo $idiomas_profe_array_multidim["$my_id_orden"][$uu]; ?>"><?php echo $idiomas_profe_full_name_array_multidim["$my_id_orden"][$uu]; 
		}
		if( !is_null($idiomas_profe_price_array_multidim["$my_id_orden"][$uu]) )
		{
			echo ' ('. $idiomas_profe_price_array_multidim["$my_id_orden"][$uu].'&#8364;/hour)';
		}
		else
		{
			echo ' (free language exchange)';			
		}
		
		?></option><?php
	}
			
							
	?>
	</select>
	
	
	
  <br>
  ---
  <br>
	My tyme shift: 
	
	<?php 
	//print_r($idiomas_profe_price_array_multidim["$my_id_orden"]); 
	
	echo $my_timeshift; 
	
	
		// Establecer la zona horaria
        date_default_timezone_set($my_timeshift);
        
        // Convertir el timestamp Unix a fecha y hora local
        $localDateTime_student = date('Y-m-d H:i', time() );
        
        echo 'Fecha y hora locales: ' . $localDateTime_student;
	

	?>
	
	
	<br>
	My teacher's time shift: 
	<?php echo $teacher_timeshift; 
	
		// Establecer la zona horaria
        date_default_timezone_set($teacher_timeshift);
        
        // Convertir el timestamp Unix a fecha y hora local
        $localDateTime_teacher = date('Y-m-d H:i', time() );
        
        echo 'Fecha y hora locales: ' . $localDateTime_teacher;	
	

	?>
  <br>
  <br>
  
    <label for="start_date">Starting local date (<?php echo $my_timeshift; ?>):</label>
    <input type="text" id="start_date" name="start_date" required>
    <label for="start_time">Starting local time (<?php echo $my_timeshift; ?>):</label>
   <!-- <input type="time" id="start_time" name="start_time" required>-->

	<!-- Select para la hora -->
	<select id="start_hour" name="start_hour" required>
	    <option value="" disabled selected>Hour</option>
	    <?php
	    // Generamos las opciones para las horas de 1 a 12
	    for ($i = 1; $i <= 12; $i++) {
	        echo "<option value='$i'>" . str_pad($i, 2, "0", STR_PAD_LEFT) . "</option>";
	    }
	    ?>
	</select>

	<!-- Select para los minutos -->
	<select id="start_minute" name="start_minute" required>
	    <option value="" disabled selected>Minute</option>
	    <?php
	    // Generamos las opciones para los minutos (00, 05, 10, ..., 55)
	    for ($i = 0; $i < 60; $i+=5) {
	        $minute = str_pad($i, 2, "0", STR_PAD_LEFT);
	        echo "<option value='$minute'>$minute</option>";
	    }
	    ?>
	</select>

	<!-- Select para AM/PM -->
	<select id="start_am_pm" name="start_am_pm" required>
	<option value="" disabled selected>AM/PM</option>
	    <option value="AM">AM</option>
	    <option value="PM">PM</option>
	</select>
	
    <br>
    <label for="duration">Length of the class (in minutes):</label>
    <input type="number" id="duration" name="duration" min="1" max="300" value="60" required>
	
	Price by class: XX €
	
	<!--
    <br>
    <label for="end_date">End local date:</label>
    <input type="text" id="end_date" name="end_date" readonly>
    <label for="end_time">End local time:</label>
    <input type="time" id="end_time" name="end_time" readonly>
    <br>
	-->

    <br>
    <label for="repetitions">Number of repetitions:</label>
    <input type="number" id="repetitions" name="repetitions" min="1" max="10" value="1">
    <br>

    <div id="recurrence-container" style="display: none;">
      <label for="recurrence">Recurrence frequency:</label>
      <select name="recurrence">
        <option value="every_week">Every week</option>
		<option value="every_2_weeks">Every two weeks</option>
        <option value="every_4_weeks">Every four weeks</option>
      </select>
    </div>
    <br>
	
	<!--<label for="description_of_sessions">Short description of the topics that you want to learn in these session(s):</label><br>
	<input type="text" name="description_of_sessions" maxlength="255" id="description_of_sessions" placeholder="Write your text..."> 
	<textarea name="description_of_sessions" id="description_of_sessions" placeholder="Write your text..." maxlength="255"></textarea>-->

	<br>

	<?php
	
	if( ($latitud1==0 AND $longitud1==0)  OR  ($latitud2==0 AND $longitud2==0) )
	{
		$type_of_meeting='online meeting';
		$type_of_meeting_id="1";
	}
	
	else
	{
		$type_of_meeting='onsite meeting';
		$type_of_meeting_id="2";
		
		$query333="
			SELECT 
			lc.id_local, lc.full_address_google,lc.country_google,lc.city_google,lc.name_local_google,

			(acos(sin(radians(lc.lat)) * sin(radians($latitud1)) + 
			cos(radians(lc.lat)) * cos(radians($latitud1)) * 
			cos(radians(lc.lng) - radians($longitud1))) * 6378) 

			AS distanciaPunto1Punto2,
			
			(acos(sin(radians(lc.lat)) * sin(radians($latitud2)) + 
			cos(radians(lc.lat)) * cos(radians($latitud2)) * 
			cos(radians(lc.lng) - radians($longitud2))) * 6378) 

			AS distanciaPunto1Punto2_teacher

			FROM locales lc

			HAVING distanciaPunto1Punto2<10 AND distanciaPunto1Punto2_teacher<10
			
			ORDER BY distanciaPunto1Punto2 ASC

			LIMIT 1000

			";
			//echo "<br><br>$query333<br><br>";

			$result333=mysqli_query($link,$query333);
			
			$num_rows_locals=mysqli_num_rows($result333);
			
			if($num_rows_locals)
			{		
				
				?><br><br><br>
				<label for="id_local_event" style="margin-bottom:5px;color:dimgrey;">*Full Address of the event</label><br/>
				<select name="id_local_event" class="form-control" style="appearance:listbox" required>
				<?php
				for($ii=0;$ii<$num_rows_locals;$ii++)
				{
					$fila333=mysqli_fetch_array($result333);
					
					$local_id=$fila333['id_local']; 
					$full_addr=$fila333['full_address_google'];
					//$city88= $fila['city_google'];  // ATENCION city_ascii, no city a secas
					//$country88= $fila['country_google'];
					$dist=$fila333['distanciaPunto1Punto2']; $dist=number_format($dist,2);
					
					$dist_teacher=$fila333['distanciaPunto1Punto2_teacher']; $dist_teacher=number_format($dist_teacher,2);
					
					$name_local=$fila333['name_local_google'];
					$event_adress=""; 
					?>

					<option value="<?php echo $local_id ; ?>" ><?php echo "$dist Km from me"." ($dist_teacher ". " Km from teacher) - " . $name_local . " - " . $full_addr ; ?></option>
					
					
					<?php
				}
			}
	}
	
	if ($num_rows_locals==0) //si están más lejos de lo que pone en el HAVING no dará resultados y lo haremos online
	{
		$type_of_meeting='online meeting';
		$type_of_meeting_id="1";
	}
	
		
	?>
	 </select>
	 <?php echo "Tipo de reunion: ----$type_of_meeting-----"; ?>
	  
	 <input type="text" name="presencial_u_online" maxlength="10" id="presencial_u_online" value="<?php echo $type_of_meeting_id; ?>" hidden >
	 
	<br>	
    <!-- <input class="input-proposed" type="submit" name="submit1" value="See proposed classes"> -->
	<br><br>
  </form>
  </div>
  <div class="post-comment-box">
	<h3>Topics that you would like to practice in the lesson</h3>
	<div class="user-poster">
		<div class="usr-post-img">
			 <img src="images/resources/bg-img2.png" alt="">  
		</div>
		<div class="post_comment_sec">
			<form>
                <textarea id="description_of_sessions" name="description_of_sessions" maxlength="255" placeholder="I want to practice conditional sentences..."></textarea>
                <p id="charCount">0/255</p>
            </form>
		</div><!--post_comment_sec end-->
	</div><!--user-poster end-->
</div><!--post-comment-box end-->

<div class="post-comment-box">
	<h3>On site/ Offline</h3>
	<div class="user-poster">
		<div class="usr-post-img">
			 <img src="images/resources/bg-img2.png" alt="">  
		</div>
		<div class="post_comment_sec">
			<form>
				Select the establishment for the lesson:<br><br>
				INPUT BOX FOR ON SITE LESSONS											
				<br>
				<!--<button type="submit">Sumbit proposal</button>-->
				<input class="input-proposed" type="submit" name="submit1" value="See proposed classes">
			</form>
		</div><!--post_comment_sec end-->
	</div><!--user-poster end-->
</div><!--post-comment-box end-->
<!-- Contenedor de previsualización oculto por defecto -->
<div id="preview-container" style="display: none;">
    <h3>Previsualización de la clase</h3>
    <ul id="preview-list"></ul>
    <button id="confirm-submit">Confirmar y Enviar</button>
    <button id="cancel-submit">Cancelar</button>
</div>
	</div> <!-- cerrar classe contenedor1 -->
	<div class="contenedor2">
							<div class="widget widget-feat">
								<ul>
									<li>
										n_exch.<i class="fa fa-heart"></i>
										<span>49</span>
									</li>
									<li>
										nota_med<i class="fa fa-comment"></i>
										<span>4.8/5</span>
									</li>
									<li>
										money_earnt
										<i class="fa fa-share-alt"></i>
										<span>1120&euro;</span>
									</li>
									<li>
										otro
										<i class="fa fa-eye"></i>
										<span>1009</span>
									</li>
								</ul>
							</div><!--widget-feat end-->
							<div class="widget widget-user">
								<h3 class="title-wd">Last lessons</h3>
								<ul>
									<li>
										<div class="usr-msg-details">
											<div class="usr-ms-img">
												<img src="images/resources/m-img1.png" alt="">
											</div>
											<div class="usr-mg-info">
												<h3>Jessica William</h3>
												<p>English</p>
											</div><!--usr-mg-info end-->
										</div>
										<span><i class="fas fa-star"></i> 5/5</span>
									</li>
									<li>
										<div class="usr-msg-details">
											<div class="usr-ms-img">
												<img src="images/resources/m-img2.png" alt="">
											</div>
											<div class="usr-mg-info">
												<h3>John Doe</h3>
												<p>Italian</p>
											</div><!--usr-mg-info end-->
										</div>
										<span><i class="fas fa-star"></i> 4/5</span>
									</li>
									<li>
										<div class="usr-msg-details">
											<div class="usr-ms-img">
												<img src="images/resources/m-img3.png" alt="">
											</div>
											<div class="usr-mg-info">
												<h3>Poonam</h3>
												<p>English</p>
											</div><!--usr-mg-info end-->
										</div>
										<span><i class="fas fa-star"></i> 4/5</span>
									</li>
									<li>
										<div class="usr-msg-details">
											<div class="usr-ms-img">
												<img src="images/resources/m-img4.png" alt="">
											</div>
											<div class="usr-mg-info">
												<h3>Bill Gates</h3>
												<p>C & C++ Developer </p>
											</div><!--usr-mg-info end-->
										</div>
										<span><img src="images/price4.png" alt="">1009</span>
									</li>
								</ul>
							</div><!--widget-user end-->
							<div class="widget widget-adver">
							</div><!--widget-adver end-->
						</div> <!-- Cerrar contenedor2 -->
</div> <!-- Cerrar cuerpo -->
  <?php
			require('../templates/footer.php');
		?>
  <script>
    $(function() {
      $("#start_date").datepicker({
        dateFormat: "dd/mm/yy",
		minDate: 0,
		maxDate: "+60d",
    	showAnim: "fadeIn", 
    	beforeShow: function(input, inst) {
      		setTimeout(function() {
        	inst.dpDiv.css({
        	  top: $(input).offset().top + $(input).outerHeight() + 5, 
        	  left: $(input).offset().left
        	});
      		}, 10);
   		}
      });


      // Calcular fecha y hora de finalización al cambiar start_date, start_time o duration
      $("#start_date, #start_time, #duration").on("change", function() {
        // Obtener valores de los campos
        var startDate = new Date($("#start_date").datepicker("getDate"));
        var startTime = $("#start_time").val();
        var duration = parseInt($("#duration").val());

        // Calcular fecha y hora de finalización
        startDate.setMinutes(startDate.getMinutes() + duration);
        var endDate = new Date(startDate);
        var endTime = endDate.toLocaleTimeString([], { hour: '2-digit', minute:'2-digit'});

        // Formatear fecha y hora de finalización y asignar a los campos
        $("#end_date").val(endDate.toLocaleDateString('es-ES'));
        $("#end_time").val(endTime);
      });

      // Mostrar o ocultar el contenedor de recurrencia según el valor de repeticiones
      $("#repetitions").on("change", function() {
        if ($(this).val() > 1) {
          $("#recurrence-container").show();
        } else {
          $("#recurrence-container").hide();
        }
      });
    });
  </script>
  <script> /* Para el contador de caracteres */
	function checkInput() {
			var mensaje = document.getElementById("description_of_sessions");
	        var charCount = document.getElementById("charCount");

	        var mensajeLength = mensaje.value.length; // Contar los caracteres del mensaje

	        // Si el mensaje supera los 255 caracteres, se corta el texto y no permite más caracteres
	        if (mensajeLength > 255) {
	            mensaje.value = mensaje.value.substring(0, 255);
	            mensajeLength = 255; 
	        }
		
	        charCount.textContent = mensajeLength + "/255"; // Mostrar el contador de caracteres
	    }
	// Agregar el evento 'input' al textarea para que se actualice el contador
	document.getElementById("description_of_sessions").addEventListener("input", checkInput);
</script>
<script>
// Script para la comprobación del formulario
$(document).ready(function() {
    // Cuando el usuario hace clic en "See proposed classes"
    $("input[name='submit1']").on("click", function(event) {
        event.preventDefault(); // Evitar el envío del formulario

        // Recoger los valores de los campos del formulario
        var startDate = $("#start_date").val();
        var startHour = $("#start_hour").val();
        var startMinute = $("#start_minute").val();
        var startAmPm = $("#start_am_pm").val();
        var duration = $("#duration").val();
        var description = $("#description_of_sessions").val();
        var language = $("#language_to_learn").val(); // Obtener el valor de la opción seleccionada
        var teacherTimeShift = "<?php echo $teacher_timeshift; ?>";
        var studentTimeShift = "<?php echo $my_timeshift; ?>";

        // Validación: comprobar si todos los campos requeridos están rellenos
        if (startDate === "" || startHour === "" || startMinute === "" || startAmPm === "" || duration === "" || description === "" || language === "" || startHour === null || startMinute === null || startAmPm === null) {
            alert("Por favor, rellena todos los campos antes de continuar.");
            return; // Detener la ejecución si algún campo está vacío
        }

		// Función para convertir de 12h a 24h
		function convertTo24HourFormat(hour, minute, ampm) {
		    // Convertir hora AM/PM a formato 24 horas
		    if (ampm === "PM" && hour < 12) {
		        hour += 12; // Si es PM y no es 12, sumamos 12
		    }
		    if (ampm === "AM" && hour === 12) {
		        hour = 0; // Si es AM y es 12, lo convertimos a 00
		    }
		    // Aseguramos que la hora y minuto tengan siempre 2 dígitos
		    return (hour < 10 ? "0" : "") + hour + ":" + (minute < 10 ? "0" : "") + minute;
		}

		// Usar la función para obtener la hora en formato 24h
		var startTime = convertTo24HourFormat(parseInt(startHour), parseInt(startMinute), startAmPm);

        // Crear el contenido de la previsualización
        var previewContent = `
            <li><strong>Idioma a aprender:</strong> ${language}</li>
            <li><strong>Fecha de inicio:</strong> ${startDate}</li>
            <li><strong>Hora de inicio:</strong> ${startTime}</li>
            <li><strong>Duración:</strong> ${duration} minutos</li>
            <li><strong>Descripción:</strong> ${description}</li>
            <li><strong>Zona horaria del estudiante:</strong> ${studentTimeShift}</li>
            <li><strong>Zona horaria del profesor:</strong> ${teacherTimeShift}</li>
        `;

        // Insertar los datos en la previsualización
        $("#preview-list").html(previewContent);

        // Mostrar el contenedor de la previsualización
        $("#preview-container").show();

        // Ocultar solo el formulario que queremos (en este caso el de la clase)
        $("#class-form").hide(); 

        // Asegurarnos de que los contenedores que no quieres ocultar sigan visibles
        $(".post-comment-box").show(); 

        // Ocultamos el boton "See proposed classes"
        $("input[name='submit1']").hide(); 
    });
    // Cuando el usuario confirma la previsualización y quiere enviar el formulario
    $("#confirm-submit").on("click", function() {
        // Asegúrate de que los datos del formulario se mantengan antes de enviarlo
        $("#class-form").show(); // Mostrar el formulario
        $("#preview-container").hide(); // Ocultar la previsualización

        // Para enviar el formulario correctamente, solo hace falta usar submit
        $("#class-form").submit(); // Enviar el formulario
    });

    // Si el usuario cancela, volver a mostrar el formulario y ocultar la previsualización
    $("#cancel-submit").on("click", function() {
        $("#preview-container").hide(); // Ocultar la previsualización
        $("#class-form").show(); // Mostrar el formulario otra vez
		$("input[name='submit1']").show(); //Muestra el boton "See proposed classes"
    });
});
</script>
</body>
</html>