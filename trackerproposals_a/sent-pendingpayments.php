<?php 

session_start();
$mi_identificador=$_SESSION['orden2017'];

require('../templates/header_simplified.html');
require('../files/bd.php');
require('../funcionesphp/funciones_idiomas_usuario.php');




//sacamos time shift

$query77="SELECT timeshift FROM mentor2009 WHERE orden='$mi_identificador' ";
$result77=mysqli_query($link,$query77);
if(!mysqli_num_rows($result77))
		die("User unregistered 1.");
$fila77=mysqli_fetch_array($result77);

$my_timeshift=$fila77['timeshift'];


//sacamos hora de corte segun zona horaria

// Crear un objeto DateTime con la fecha y hora actuales
$fechaHoraActual = new DateTime();

// Formatear la fecha y hora en el formato 'YYYY-MM-DD HH:MM:SS'
$fechaHoraFormateada = $fechaHoraActual->format('Y-m-d H:i:s');
$fechaHoraUTC0 =$fechaHoraFormateada;

//$fechaHoraUTC0 = '2025-01-28 12:00:00'; // Fecha y hora en UTC0
$zonaHoraria = $my_timeshift; // Zona horaria de ejemplo

//$tiempoUnix = obtenerTiempoUnix($fechaHoraUTC0, $zonaHoraria);

//echo "<br><br><br>El tiempo Unix para la fecha $fechaHoraUTC0 en la zona horaria $zonaHoraria es: $tiempoUnix<br>";

$tiempo_corte=time();

//$mi_identificador = 4533;
//$time_shift_student = '';

//$query = "SELECT * FROM tracker WHERE id_user_student ='" . $mi_identificador . "' AND proposal_accepted_teacher=2 AND cancelled=0 AND paid=1 ORDER BY start_time_unix ASC";


$query = "
SELECT t.*, m.*
FROM tracker t
INNER JOIN mentor2009 m
ON t.id_user_student=m.orden
WHERE t.id_user_student ='" . $mi_identificador . "'  AND t.proposal_accepted_teacher=2 AND t.cancelled=0 AND t.paid=0 AND $tiempo_corte<=t.start_time_unix
ORDER BY t.start_time_unix ASC";

//echo "$query ";

$result = mysqli_query($link, $query);
$nuevos = mysqli_num_rows($result);

$n_payments_pending=$nuevos;

/*
if (!$nuevos) 
	echo "No lessons for this user yet";
*/

/*

for ($i = 0; $i < $nuevos; $i++) {
    $fila = mysqli_fetch_array($result);
    $id_of_class = $fila['id_tracking'];
    $creation_timestamp = $fila['created_timestamp'];
    $recurrent = $fila['created_from_recurrent'];
    $id_student = $fila['id_user_student'];
    $id_teacher = $fila['id_user_teacher'];
    //$time_shift_student = $fila['time_shift_student'];
    $dateofstart_utc0 = $fila['date_start_utc0'];
    $dateofend_utc0 = $fila['date_end_utc0'];
    $unixtimestart = $fila['start_time_unix'];
    $unixtimeend = $fila['end_time_unix'];
    $duration_min = $fila['session_lenght_minutes'];
    $language_to_teach = $fila['language_taught'];
    $hourly_price = $fila['hourly_rate_original'];
    $total_price = $fila['price_session_total'];
    $descriptionofsession = $fila['description_session'];
    $teacher_accepted = $fila['proposal_accepted_teacher'];
    $teacher_accepted_timestamp = $fila['proposal_accepted_timestamp'];
    $session_paid = $fila['paid'];
    $session_paid_timestamp = $fila['timestamp_paid'];
    $cancelled = $fila['cancelled'];
	
	
*/
    
	
	/*
	
    $query77="SELECT nombre FROM mentor2009 WHERE orden='$id_student' ";
		$result77=mysqli_query($link,$query77);
		if(!mysqli_num_rows($result77))
				die("User unregistered 1.");
		$fila77=mysqli_fetch_array($result77);
		$student_name=$fila77['nombre'];
		$palabras = explode (" ", $student_name);
		$student_name=ucfirst($palabras[0]);

    echo "<ul>";
    echo "<li>Session ID: $id_of_class</li>";
    echo "<li>Student ID: $id_student</li>";
    echo "<li>Teacher ID: $id_teacher</li>";
    echo "<li>Student timeshift: $time_shift_student</li>";
    echo "<li>Start Date UTC-0: $dateofstart_utc0</li>";
    echo "<li>End Date UTC-0: $dateofend_utc0</li>";
    echo "<li>Start Unix Time: $unixtimestart</li>";
    echo "<li>End Unix Time: $unixtimeend</li>";
    echo "<li>Duration (min): $duration_min</li>";
    echo "<li>Language to teach: $language_to_teach</li>";
    echo "<li>Price per hour: $hourly_price</li>";
    echo "<li>Total session price: $total_price</li>";
    echo "<li>Description of session: $descriptionofsession</li>";
    echo "<li>Teacher accepted?: $teacher_accepted</li>";
    echo "<li>Session has been paid?: $session_paid</li>";
    echo "<li>Session has been cancelled?: $cancelled</li>";
    echo "<li>Info created from recurrently: $recurrent</li>";
    echo "<li>Info Teacher accepted timestamp: $teacher_accepted_timestamp</li>";
    echo "<li>Info Session payment timestamp: $session_paid_timestamp</li>";

    if ($teacher_accepted == 0) {
        echo "<li style=\"color:red;\">Awaiting confirmation of the teacher</li>";
    }
    if ($teacher_accepted == 2 && $session_paid == 0) {
        echo "<li><a href=\"./studentprepayment.php?trackid=$id_of_class\">Proceed to payment</a></li>";
    } else if ($session_paid == 1) {
        echo "<li style=\"color:green;\">Paid</li>";
    }
    if ($session_paid == 0 && $cancelled == 0) {
        echo "<li><a href=\"./studentcancel.php?trackid=$id_of_class\">Cancel session</a></li>";
    }
    echo "</ul>";*/
//}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lessons payment pending</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <style type="text/css">
        .tooltip-container { 
            position: absolute;
            top: 10px;
            right: 10px;
            display: inline-block;
        }
        .tooltip-text { 
            font-size: 16px; 
            visibility: hidden; 
            width: 380px; 
            background-color: #000; 
            color: #fff; 
            text-align: left; 
            border-radius: 6px; 
            padding: 50px; 
            position: absolute; 
            z-index: 1; 
            top: 30%; 
            left: 50%; 
            transform: translateX(-50%); 
            opacity: 0; 
            transition: opacity 0.3s; 
        }
        .tooltip-container:hover .tooltip-text { 
            visibility: visible; 
            opacity: 0.75; 
        }
    </style>
    
    <style>
/* Contenedor de enlaces del foro */
.forum-links {  
    background-color: #fff;  
    padding: 10px 0;  
    margin-bottom: 10px;  
    width: 180%;  
    margin-left: -40%;  
    margin-top: -5.4%;  
}
.forum-links ul {  
    list-style-type: none;  
    display: flex;  
    justify-content: flex-start;  
    padding: 0;  
    margin: 0;  
    padding-left: 450px;  
}
.forum-links ul li {  
    text-align: center;  
    margin-right: 20px;  
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
.forum-links ul li.active a {  
    color: #e65f00;  
    font-weight: bold;  
    position: relative;  
}
.forum-links ul li.active a::after {  
    content: "";  
    position: absolute;  
    bottom: -2px;  
    left: 0;  
    right: 0;  
    height: 2px;  
    background-color: #e65f00;  
}

:root {
    --primary-orange: #d35400;
    --accent-orange: #e67e22;
    --light-orange: #fdf2e9;
    --success-green: #53d690;
    --warning-red: #e77667;
    --text-dark: #2c3e50;
    --text-grey: #7f8c8d;
    --border-color: #ecf0f1;
    --bg-grey: #f4f7f6;
}

body {
    font-family: 'Roboto', sans-serif;
    background-color: var(--bg-grey);
}

/* Card Design */
.usr-question {
    background-color: #fff;
    padding: 20px;
    border-radius: 4px;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
    display: flex;
    flex-direction: row;
    margin-bottom: 20px;
    position: relative;
    border-left: 5px solid var(--primary-orange);
    transition: transform 0.2s, box-shadow 0.2s;
}

.usr-question:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 10px rgba(0,0,0,0.1);
}

.usr-img {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    margin-top: 10px;
    margin-right: 20px;
    flex-shrink: 0;
    border: 2px solid var(--primary-orange);
    overflow: hidden;
}

.usr-img img {
    width: 100%;
    height: 100%;
    border-radius: 50%;
    object-fit: cover;
}

.usr-img center {
    font-size: 11px;
    color: var(--text-dark);
    font-weight: 500;
    margin-top: 5px;
}

.usr_quest {
    width: 75%;
    position: relative;
    min-height: 120px;
}

.usr_quest h3 {
    font-size: 18px;
    font-weight: bold;
    color: var(--text-dark);
    margin: 0 0 10px 0;
    width: 100%;
}

.usr_quest h4 {
    color: var(--text-grey);
    margin: 0 0 15px 0;
    font-size: 14px;
    font-weight: normal;
}

.usr_quest h6 {
    font-size: 13px;
    color: var(--text-dark);
    margin: 10px 0;
    display: flex;
    align-items: center;
    gap: 15px;
    flex-wrap: wrap;
}

.usr_quest h6 i {
    color: var(--accent-orange);
    margin-right: 5px;
}

.quest-posted-time {
    position: absolute;
    top: 10px;
    right: 10px;
    font-size: 13px;
    color: var(--text-grey);
    margin: 0;
    display: flex;
    align-items: center;
    gap: 5px;
}

.quest-posted-time i {
    color: var(--accent-orange);
}

.usr_quest ul.job-dt {
    list-style-type: none;
    padding: 0;
    margin: 15px 0 0 0;
}

.usr_quest ul.quest-tags {
    list-style-type: none;
    padding: 0;
    margin: 15px 0;
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
}

.quest-tags li {
    display: inline-block;
}

.quest-tags li a,
.job-dt li a {
    display: inline-block;
    padding: 8px 12px;
    border-radius: 4px;
    text-decoration: none;
    font-size: 13px;
    font-weight: 500;
    transition: all 0.2s;
    border: 1px solid #ddd;
    cursor: pointer;
}

.fa {
    font-size: 14px;
}

.class-details {
    background-color: transparent !important;
    padding: 0;
    border: none;
}

.class-details h4 {
    font-size: 16px;
    color: #333;
}

.class-details p {
    font-size: 14px;
    color: #666;
}

.forum-page {
    margin-bottom: 20px;
}

/* Media query para pantallas pequeñas (hasta 991px) */
@media (max-width: 991px) {
    .forum-links {
        position: relative; 
        top: auto; 
        left: auto; 
        width: 140%; 
        background-color: #fff; 
        height: auto; 
        padding: 10px 20px; 
        opacity: 1; 
        visibility: visible; 
        margin-left:-20%;
        margin-top: -8.7%;
    }
    
    .usr-question {
        flex-direction: column;
    }
    
    .usr-img {
        margin-bottom: 15px;
    }
    
    .usr_quest {
        width: 100%;
    }
    
    .quest-posted-time {
        position: static;
        margin-top: 10px;
    }
}

/* Botones de aceptar y denegar */
.btn-denegar {
    background-color: #d35400 !important;
    color: white !important;
    border-color: #d35400 !important;
}

.btn-denegar:hover {
    opacity: 0.9;
}

.btn-aceptar {
    background-color: white !important;
    color: #c0392b !important;
    border-color: #ecf0f1 !important;
}

.btn-aceptar:hover {
    opacity: 0.9;
}

.notification.decline {
    background-color: var(--success-green) !important;
}

.button-studentprepaymentmultiple {
    background-color: #e65f00; 
    font-size: 16px;
    font-weight: bold;
    border: none;
    padding: 12px 20px;
    border-radius: 8px;
    cursor: pointer;
}

.button-studentprepaymentmultiple a {
    color: white;
}

.button-studentprepaymentmultiple:hover {
    opacity: 0.9;
}

.button-studentprepaymentmultiple a:hover {
    color: white;
}

.notification {
    position: fixed;
    bottom: 20px;
    right: 20px;
    background-color: #333;
    color: #fff;
    padding: 15px 20px;
    border-radius: 5px;
    opacity: 0;
    visibility: hidden;
    transition: opacity 0.5s ease, visibility 0.5s ease;
    z-index: 9999;
}

.notification.show {
    opacity: 1;
    visibility: visible;
}

.notification.decline {
    background-color: #e77667;
}
    </style>
</head>
<body>
    <div class="wrapper">
        <section class="forum-sec">
            <div class="container">
                <div class="forum-links">
                    <ul>
					
					
					<?php
					
					/*$tiempo_corte=time();
					
					
					$query109="SELECT * FROM tracker WHERE id_user_student ='" . $mi_identificador . "'  AND proposal_accepted_teacher=2 AND cancelled=0 AND paid=0 AND $tiempo_corte<=start_time_unix ORDER BY start_time_unix ASC ";
					$result109 = mysqli_query($link, $query109);
					$n_payments_pending=mysqli_num_rows($result109);
					*/
					
					$query109 = "
					SELECT * FROM tracker t
					WHERE t.id_user_student ='" . $mi_identificador . "'  AND proposal_accepted_teacher=2 AND cancelled=0 AND paid=1  AND end_time_unix>$tiempo_corte";
					$result109 = mysqli_query($link, $query109);
					$n_next_lessons=mysqli_num_rows($result109);
					
					
					$query109 = "
					SELECT * FROM tracker t
					WHERE t.id_user_student ='" . $mi_identificador . "'  AND t.proposal_accepted_teacher=2 AND t.cancelled=0 AND t.paid=1 AND releasefunds=0 AND $tiempo_corte>t.end_time_unix
					";
					$result109 = mysqli_query($link, $query109);
					$n_release_payment_pending=mysqli_num_rows($result109);
					
					
					
					
					
					?>
					
                        <li><a href="./sent-futureclasses.php" title="">Next lessons as student (<?php echo $n_next_lessons; ?>)</a></li>
                        <li class="active"><a href="./sent-pendingpayments.php" title="">Lessons payment pending (<?php echo $n_payments_pending; ?>)</a></li>
                        <li><a href="./sent-pastclassespaymentnotreleased.php" title="">Payment releases pending (<?php echo $n_release_payment_pending; ?>)</a></li>
						<li><a href="./sent-infopayments.php" title="">Lessons Payments information</a></li>
						
                    </ul> 
                </div>
            </div>
        </section>
        <section class="forum-page">
            <div class="container">
                <div class="forum-questions-sec" style="width: 100%">
                    <div class="forum-questions">
					
					<?php
					if (!$n_payments_pending) {
								die( "<br><br><br><center>No lessons available in this section at the moment</center>");
							}
					?>
					
					
					</br>
					
					

					
					
					
					
					<div style="text-align: center;">
  <button class="button-studentprepaymentmultiple">
    <a href="./studentprepaymentmultiple.php">Pay all pending classes</a>
  </button>
</div>
	</br></br>
					
					
					
                        <?php
						
						
						//echo "$query109";
						
						
						
							//my languages
							$my_langs_array_multidim=array( array() );
							$my_langs_full_name_array_multidim=array( array() ); 
							$my_langs_level_array_multidim=array( array() ); 
							$my_langs_forshare_array_multidim=array( array() );
							$my_langs_price_array_multidim=array( array() );
							$my_langs_typeofexchange_array_multidim=array( array() );
							$my_langs_priceorexchangetext_array_multidim=array( array() );
							$my_langs_level_image_array_multidim=array( array() );
							$my_langs_2letters_array_multidim=array( array() );	
							
							//learn languages
	
							$learn_langs_array_multidim=array( array() );
							$learn_langs_full_name_array_multidim=array( array() ); 
							$learn_langs_level_array_multidim=array( array() ); 
							$learn_langs_forshare_array_multidim=array( array() );
							$learn_langs_price_array_multidim=array( array() );
							$learn_langs_typeofexchange_array_multidim=array( array() );
							$learn_langs_priceorexchangetext_array_multidim=array( array() );
							$learn_langs_level_image_array_multidim=array( array() );
							$learn_langs_2letters_array_multidim=array( array() );
						
						
							//aquí sacamos las lenguas del estudiante 

							list($my_langs_array_multidim["$mi_identificador"], $my_langs_full_name_array_multidim["$mi_identificador"], 
							$my_langs_level_array_multidim["$mi_identificador"], 
							$my_langs_forshare_array_multidim["$mi_identificador"], 	
							$my_langs_price_array_multidim["$mi_identificador"], $my_langs_typeofexchange_array_multidim["$mi_identificador"], 
							$my_langs_priceorexchangetext_array_multidim["$mi_identificador"], $my_langs_level_image_array_multidim["$mi_identificador"], 
							$my_langs_2letters_array_multidim["$mi_identificador"])
							= lenguas_que_conoce_usuario($mi_identificador,$link);
							
							list($learn_langs_array_multidim["$mi_identificador"], $learn_langs_full_name_array_multidim["$mi_identificador"], 
							$learn_langs_level_array_multidim["$mi_identificador"], 
							$learn_langs_forshare_array_multidim["$mi_identificador"], 	$learn_langs_price_array_multidim["$mi_identificador"], 
							$learn_langs_typeofexchange_array_multidim["$mi_identificador"], 
							$learn_langs_priceorexchangetext_array_multidim["$mi_identificador"], $learn_langs_level_image_array_multidim["$mi_identificador"], 
							$learn_langs_2letters_array_multidim["$mi_identificador"])
							= lenguas_que_quiere_estudiar_usuario($mi_identificador,$link);
						
						
						
						
						
						
						
						
						
						
						
						
                        $result = mysqli_query($link, $query);
                        while ($fila = mysqli_fetch_array($result)) { 
						
						
							    $id_of_class = $fila['id_tracking'];
								$creation_timestamp = $fila['created_timestamp'];
								$recurrent = $fila['created_from_recurrent'];
								$id_student = $fila['id_user_student'];
								$id_teacher = $fila['id_user_teacher'];
								//$time_shift_student = $fila['time_shift_student'];
								$dateofstart_utc0 = $fila['date_start_utc0'];
								$dateofend_utc0 = $fila['date_end_utc0'];
								$unixtimestart = $fila['start_time_unix'];
								$unixtimeend = $fila['end_time_unix'];
								$duration_min = $fila['session_lenght_minutes'];
								$language_to_teach = $fila['language_taught'];
								$hourly_price = $fila['hourly_rate_original'];
								$total_price = $fila['price_session_total']; $total_price=round($total_price,2);
								$descriptionofsession = $fila['description_session'];
								$teacher_accepted = $fila['proposal_accepted_teacher'];
								$teacher_accepted_timestamp = $fila['proposal_accepted_timestamp'];
								$session_paid = $fila['paid'];
								$session_paid_timestamp = $fila['timestamp_paid'];
								$cancelled = $fila['cancelled'];
								
								$por_internet_o_presencial=$fila['onlineonsite'];
								$local_encuentro=$fila['id_local'];



                            //extraer foto y nombre del profesor
							
							$query77="SELECT * FROM mentor2009 WHERE orden='$id_teacher' ";
							$result77=mysqli_query($link,$query77);
							//if(!mysqli_num_rows($result77))
							//		die("User unregistered 1.");
							$fila77=mysqli_fetch_array($result77);
							$teacher_name=$fila77['nombre'];
							$palabras = explode (" ", $teacher_name);
							$teacher_name=ucfirst($palabras[0]);
							
							
                            //$id_student = $fila['id_user_student'];
                            $extension = $fila77['fotoext'];
							$path_photo="../uploader/upload_pic/thumb_$id_teacher"."."."$extension";

                            if ( !file_exists($path_photo) ) :
								$path_photo="../uploader/default.jpg"; 
							endif;
							
							$time_shift_teacher = $fila77['timeshift'];
							
							
							
							
							
							
							
							
							//aquí sacamos las lenguas del profesor

						
							//estudiante
							list($my_langs_array_multidim["$id_teacher"], $my_langs_full_name_array_multidim["$id_teacher"], 
							$my_langs_level_array_multidim["$id_teacher"], 
							$my_langs_forshare_array_multidim["$id_teacher"], 	
							$my_langs_price_array_multidim["$id_teacher"], $my_langs_typeofexchange_array_multidim["$id_teacher"], 
							$my_langs_priceorexchangetext_array_multidim["$id_teacher"], $my_langs_level_image_array_multidim["$id_teacher"], 
							$my_langs_2letters_array_multidim["$id_teacher"])
							= lenguas_que_conoce_usuario($id_teacher,$link);
							
							list($learn_langs_array_multidim["$id_teacher"], $learn_langs_full_name_array_multidim["$id_teacher"], 
							$learn_langs_level_array_multidim["$id_teacher"], 
							$learn_langs_forshare_array_multidim["$id_teacher"], 	$learn_langs_price_array_multidim["$id_teacher"], 
							$learn_langs_typeofexchange_array_multidim["$id_teacher"], 
							$learn_langs_priceorexchangetext_array_multidim["$id_teacher"], $learn_langs_level_image_array_multidim["$id_teacher"], 
							$learn_langs_2letters_array_multidim["$id_teacher"])
							= lenguas_que_quiere_estudiar_usuario($id_teacher,$link);
							

							$idiomas_comunes = array_intersect($my_langs_array_multidim["$id_teacher"], $my_langs_array_multidim["$mi_identificador"]);
							
							//con esta línea lo que hacemos es borrar los valores vacíos y reorganizar el array
							$idiomas_comunes = array_values(array_filter($idiomas_comunes));
							
							
							$nombre_idioma='';
							// sacamos el nombre completo del idioma sin recurrir a hacer llamada a la bbdd
							for($rr=0;$rr<count($idiomas_comunes);$rr++)
							{
								$key_search=array_search($idiomas_comunes[$rr],$my_langs_array_multidim["$id_teacher"] );
								
								switch ($my_langs_level_array_multidim["$id_teacher"][$key_search]) 
								{
									case 0:
										$level_aux='?';
										break;
									case 1:
										$level_aux='Beginner';
										break;
									case 2:
										$level_aux='A1';
										break;
									case 3:
										$level_aux='A2';
										break;
									case 4:
										$level_aux='B1';
										break;
									case 5:
										$level_aux='B2';
										break;
									case 6:
										$level_aux='C1';
										break;
									case 7:
										$level_aux='C2';
										break;
								}
								
								$nombre_idioma.=" ".$my_langs_full_name_array_multidim["$id_teacher"][$key_search]." (".$level_aux.") "."&nbsp;&nbsp; ";
							}
							
							$nombre_idioma=trim($nombre_idioma); 
							
							if(empty($nombre_idioma)){$nombre_idioma='No common languages';}
							//echo "$nombre_idioma";
							
							
							//sacamos el nombre del idioma que quiere trabajar el estudiante
														
							$key_search2=array_search($language_to_teach,$my_langs_array_multidim["$id_teacher"] );
							
							//echo "<br>$language_to_teach: $key_search2<br>";
							
							//print_r($learn_langs_array_multidim["$id_student"]);
							//print_r($learn_langs_full_name_array_multidim["$id_student"]); 
							
							$language_to_teach_fullname=$my_langs_full_name_array_multidim["$id_teacher"]["$key_search2"];
							$level_language_to_teach=$my_langs_level_array_multidim["$id_teacher"]["$key_search2"]; 

							switch ($level_language_to_teach) 
							{
								case 0:
									$level_language_to_teach_2='?';
									break;
								case 1:
									$level_language_to_teach_2='Beginner';
									break;
								case 2:
									$level_language_to_teach_2='A1';
									break;
								case 3:
									$level_language_to_teach_2='A2';
									break;
								case 4:
									$level_language_to_teach_2='B1';
									break;
								case 5:
									$level_language_to_teach_2='B2';
									break;
								case 6:
									$level_language_to_teach_2='C1';
									break;
								case 7:
									$level_language_to_teach_2='C2';
									break;
							}
							
							
							
							
							
							
							
							//en caso de que haya quitado el estudiante el idioma de su lista de idiomas que quiere aprender
							if(empty($language_to_teach_fullname))
								$cadena_idioma_nivel=$language_to_teach;
							else
								$cadena_idioma_nivel="$language_to_teach_fullname ($level_language_to_teach_2)"; 
							
							//online o bien onsite
							if($por_internet_o_presencial==1)
							{
									$cadena_idioma_nivel.=" - Online";							
							}
							else if($por_internet_o_presencial==2 AND is_numeric($local_encuentro) )
							{
									$query212="SELECT * FROM locales WHERE id_local=$local_encuentro"; 
											
									$result212=mysqli_query($link,$query212);
									$fila212=mysqli_fetch_array($result212);
									$nombre_establecimiento=$fila212['name_local_google'];
									$direccion_establecimiento=$fila212['full_address_google'];
									$ciudad_establecimiento=$fila212['city_google'];
											
									$cadena_idioma_nivel.=" - Onsite in $ciudad_establecimiento: $nombre_establecimiento";
							}
							
							
							
							
							
							
							
														// Ejemplo de uso
							$tiempoUnix_clase = $unixtimestart; // Hora Unix en UTC0 (ejemplo)
							//$zonaHoraria = 'America/Argentina/Buenos_Aires'; // Zona horaria de ejemplo

							$fechaHoraFormateada2 = obtenerFechaHora($tiempoUnix_clase, $zonaHoraria);
							//echo "La fecha y hora correspondiente a la hora Unix $tiempoUnix en la zona horaria $zonaHoraria es: $fechaHoraFormateada";
						
							$fechaHoraFormateada2_utc0=obtenerFechaHora($tiempoUnix_clase, 'UTC');
							
							$fechaHoraFormateada2_teacher = obtenerFechaHora($tiempoUnix_clase, $time_shift_teacher);
							
							
							
							
							
							 
							
?>





<?php
                            echo "<div class=\"usr-question\">";
                            echo "<div class=\"usr-img\"><a href=\"../user/u.php?identificador=$id_teacher\"><img src=\"$path_photo\" alt=\"Student Image\"></a>
							<br><center style=\"margin-top:85%; font-size: 80%\" >$teacher_name</center>
							</div>";
                            echo "<div class=\"usr_quest\">";
                            ?>
                                    <h3><?php echo $cadena_idioma_nivel; ?></h3>
                                    <h4><?php echo $fila['description_session'];?> </h4>
                                    
                                    <div><span class="quest-posted-time"><i class="fa fa-clock-o"></i> <?php echo $fechaHoraFormateada2 . " $zonaHoraria"; ?></span></div>
                            <?php
                                    echo "<h6><i class=\"far fa-hourglass\"></i> $duration_min min &nbsp;&nbsp;&nbsp;&nbsp;
							<i class=\"fas fa-coins\"></i> $total_price € 
							&nbsp;&nbsp;&nbsp;&nbsp;<i class=\"fas fa-comment\"></i>&nbsp; $nombre_idioma</h6>";
                            
                            
                            echo "<ul class=\"job-dt\">";	
							

							?>
							<ul class="quest-tags">
								<li><a class="btn-aceptar" style="background-color:#53d690;" href="./studentprepayment.php?trackid=<?php echo "$id_of_class"; ?>&action=2" >Proceed to Payment</a></li>
								<li><a class="btn-denegar" style="background-color: #e77667;" data-url="./studentcancel.php?trackid=<?php echo "$id_of_class"; ?>&action=1">Cancel</a></li>
							</ul>
						<?php
							//echo "rel: $session_releasefunds - paid: $session_paid ";
							
							
							if($session_paid==0)
							{
								echo "<li><a href=\"#\" title=\"\" style=\"background-color:#b2b2b2;\">Waiting for payment</a></li>";

							}
							else if($session_releasefunds==0 AND $session_paid==1)
							{
								echo "<li><a href=\"#\" title=\"\">Payment made</a></li>";

							}
							else if($session_releasefunds==1 AND $session_paid==1)
							{
								echo "<li><a href=\"#\" title=\"\" style=\"background-color:#53d690;\" >Payment released</a></li>";
							}
							echo "</ul>";				
							
							
							?>
<div class="tooltip-container" style="color:#b2b2b2; margin: -10px 0 0 0; align: center;">
    <i style="color:#b2b2b2;margin-left: 800%;margin-top: 10px;font-size:20px;" class="fas fa-info-circle"></i>
    <span class="tooltip-text" style="font-size: 12px;">
	
	
	
	
																		<?php  
																	
																	

																	
																	
																	if($por_internet_o_presencial==2 AND is_numeric($local_encuentro) )
																	{
																		$direccion_completa="Address:<br>$nombre_establecimiento<br>$direccion_establecimiento<br>$ciudad_establecimiento<br><br>";
																	}
																	
																		echo "Additional information: <br><br>
																		
																		$direccion_completa																	
																		
																		My time zone: $my_timeshift<br>
																		My partner's time zone: $time_shift_teacher<br><br> 
																		
																		Start time(my local time): $fechaHoraFormateada2<br>
																		Start time (my partner's local time): $fechaHoraFormateada2_teacher<br>
																		Start time(GMT0 - Greenwich time): $fechaHoraFormateada2_utc0<br>
																		Duration: $duration_min min<br><br>
																																				
																		Lesson ID: #$id_of_class	<br> 
																		Student ID: #$id_student<br>
																		Language code: $language_to_teach<br>
																		Lesson description: $descriptionofsession<br>
																		Created from serie: $recurrent<br><br>
																		
																		Paid amount: $total_price&euro; ($hourly_price&euro;/h)<br>
																														
																	";	 ?>
	
	
	
	
	
	
	
	</span>
</div>



            
                                </div>
                                
                            </div>
                        <?php }
                        ?>
						
 <br>		<br> 
						
						
<div style="text-align: center;">
  <button class="button-studentprepaymentmultiple">
    <a href="./studentprepaymentmultiple.php">Pay all pending classes</a>
  </button>
</div>
						
						
                    </div>
                </div>
            </div>
        </section>
    </div>
	<script>
  // Nueva funcionalidad para botón Decline
  document.querySelectorAll('.btn-denegar').forEach(button => {
      button.addEventListener('click', function(e) {
          e.preventDefault();
          const url = this.getAttribute('data-url');
          
          // Crear notificación si no existe
          let notification = document.getElementById('notification-decline');
          if (!notification) {
              notification = document.createElement('div');
              notification.id = 'notification-decline';
              notification.className = 'notification decline';
              document.body.appendChild(notification);
          }

          // Mostrar notificación
          notification.textContent = 'Class cancelled successfully!';
          notification.classList.add('show');
          
          // Enviar solicitud de cancelación
          fetch(url)
              .then(response => response.text())
              .then(() => {
                  // Recargar después de 2 segundos
                  setTimeout(() => {
                      window.location.reload();
                  }, 2000);
              })
              .catch(error => console.error('Error:', error));
      });
  });
</script>

</body>
</html>