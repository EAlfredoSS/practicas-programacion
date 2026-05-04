<?php

require('../files/bd.php');
require('../funcionesphp/funciones_idiomas_usuario.php');

session_start();


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
echo "<h2>Form Information</h2>";
	
	
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
<body>   


<br><br>
We need to show the time and date of the start time for the teacher according his local time. Therefore, we must consider the time shift difference<br>
of the teacher and the student.
<br><br>
When doing the INSERT in the data base, time shifts of the student user is still not considered.

 
<br><br>


  <form method="post" action="<?php echo $_SERVER['PHP_SELF']."?tid=$teacher_id_orden"; ?>">
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
    <input type="time" id="start_time" name="start_time" required>
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
	
	<label for="description_of_sessions">Short description of the topics that you want to learn in these session(s):</label><br>
	<input type="text" name="description_of_sessions" maxlength="255" id="description_of_sessions" placeholder="Write your text...">
	
	
	
	<br><br><br>
	
	
	
	
	
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
	 
	 
	<br><br><br><br>
	
    <input type="submit" name="submit1" value="See proposed classes">
	
	<br><br>
  </form>
  <script>
    $(function() {
      $("#start_date").datepicker({
        dateFormat: "dd/mm/yy"
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
</body>
</html>