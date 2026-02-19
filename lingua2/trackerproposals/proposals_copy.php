<?php 
/*ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);*/

//$mi_identificador = 4588;
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
$tiempoUnix=time();

//$tiempoact=time();

//echo "$tiempoUnix - $tiempoact"; 

//echo "<br><br><br>El tiempo Unix para la fecha $fechaHoraUTC0 en la zona horaria $zonaHoraria es: $tiempoUnix<br>";

// Consulta SQL para obtener las clases
$query = "
SELECT t.*, m.*
FROM tracker t
INNER JOIN mentor2009 m
ON t.id_user_teacher=m.orden
WHERE t.id_user_teacher ='" . $mi_identificador . "'  AND proposal_accepted_teacher=0 AND cancelled=0  AND end_time_unix>$tiempoUnix
ORDER BY t.start_time_unix ASC";

// Ejecutamos la consulta
$result = mysqli_query($link, $query);

// Contamos cuántos resultados hay
$nuevos = mysqli_num_rows($result);


//echo "$query";

$n_received_proposals=$nuevos;


//if (!$nuevos) {
 //   die("No sessions for this user yet");
 
    //for($i=0;$i<$nuevos;$i++)
//{
	//$fila=mysqli_fetch_array($result);
	
	//$id_of_class=$fila['id_tracking'];	
	//$creation_timestamp=$fila['created_timestamp'];
	//$recurrent=$fila['created_from_recurrent'];
	//$id_student=$fila['id_user_student'];
	//$time_shift_student=$fila['time_shift_student'];
	//$dateofstart_local=$fila['date_start_local'];
	//$dateofend_local=$fila['date_end_local'];
	//$unixtimestart=$fila['start_time_unix'];
	//$unixtimeend=$fila['end_time_unix'];
	//$duration_min=$fila['session_lenght_minutes'];
	//$language_to_teach=$fila['language_taught'];
	//$hourly_price=$fila['hourly_rate_original'];
	//$total_price=$fila['price_session_total'];
	//$descriptionofsession=$fila['description_session'];
	//$teacher_accepted=$fila['proposal_accepted_teacher'];
	//$teacher_accepted_timestamp=$fila['proposal_accepted_timestamp'];
	//$session_paid=$fila['paid'];
	//$session_paid_timestamp=$fila['timestamp_paid'];
	//$cancelled=$fila['cancelled'];
	//$fee_percentage=$fila['price_fee_percentage'];
	//$amount_received_by_teacher=$total_price*(100-$fee_percentage)/100;
	
	//$style_1='';
//}
//}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Next classes as teacher</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
	
	
<style type="text/css">

.tooltip-container {
  position: relative; /*relative: los elementos se posicionan de forma relativa a su posición normal.*/
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
  top: 30%; /* Posiciona el tooltip bajo del elemento */
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
.forum-links {
    background-color: #fff;
    padding: 10px 0;
    margin-bottom: 10px;
    width: 180%;
    margin-left: -40%;
    margin-top: -5.3%;
}

.forum-links ul {
    list-style-type: none;
    display: flex;
    justify-content: flex-start; /* Alinear elementos al inicio */
    padding: 0;
    margin: 0;
    padding-left: 450px; /* Espacio desde el borde izquierdo del contenedor */
}

.forum-links ul li {
    text-align: center;
    margin-right: 20px; /* Espacio entre los elementos */
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
    color: #e65f00; /* Color del enlace activo */
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
    background-color: #e65f00; /* Línea roja debajo del enlace activo */
}


        :root {
            --primary-orange: #d35400;
            --accent-orange: #e67e22;
            --light-orange: #fdf2e9;
            --pending-yellow: #f1c40f;
            --pending-bg: #fcf3cf;
            --text-dark: #2c3e50;
            --text-grey: #7f8c8d;
            --border-color: #ecf0f1;
            --bg-grey: #f4f7f6;
        }

        body {
            font-family: 'Roboto', sans-serif;
            background-color: var(--bg-grey);
        }

        /* Proposal Card */
        .proposal-card {
            background: white;
            border-radius: 4px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
            margin-bottom: 20px;
            display: flex; /* flex layout */
            flex-wrap: wrap; /* allow wrapping on mobile */
            padding: 20px;
            position: relative;
            border-left: 5px solid #f39c12;
        }

        /* Left Side */
        .card-left {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-right: 25px;
            min-width: 80px;
        }
        .avatar-circle {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            border: 2px solid var(--primary-orange);
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: visible; /* changed to visible for badge */
        }
        .star-badge {
            position: absolute;
            bottom: 0;
            right: -5px;
            color: #f1c40f;
            font-size: 14px;
            background: white;
            border-radius: 50%;
            padding: 2px;
        }
        .pending-status {
            margin-top: 10px;
            background-color: var(--pending-bg);
            color: #d4ac0d;
            font-size: 10px;
            font-weight: bold;
            padding: 4px 8px;
            border-radius: 12px;
            text-transform: uppercase;
        }

        /* Middle Side */
        .card-middle {
            flex-grow: 1;
            padding-right: 20px;
            min-width: 250px;
        }
        .card-title {
            font-size: 18px;
            font-weight: bold;
            color: var(--text-dark);
            margin-bottom: 8px;
        }
        .meta-row {
            display: flex;
            gap: 20px;
            font-size: 14px;
            color: var(--text-dark);
            margin-bottom: 15px;
            flex-wrap: wrap;
        }
        .meta-item {
            display: flex;
            align-items: center;
            gap: 5px;
        }
        .meta-item i { color: var(--accent-orange); width: 16px; text-align: center; }

        .languages-label {
            font-size: 10px;
            color: #aaa;
            text-transform: uppercase;
            margin-bottom: 5px;
            font-weight: bold;
        }
        .language-tags {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }
        .lang-tag {
            background-color: #fcece0;
            color: var(--primary-orange);
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 500;
        }

        /* Right Side */
        .card-right {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            justify-content: flex-start;
            min-width: 150px;
        }
        .date-info {
            font-size: 13px;
            color: var(--text-grey);
            margin-bottom: 15px;
            width: 100%;
            text-align: right;
        }
        .date-info i { margin-right: 5px; }

        .action-buttons {
            display: flex;
            flex-direction: column;
            gap: 8px;
            width: 100%;
        }
        .btn {
            border: 1px solid #ddd;
            background: white;
            padding: 8px 12px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 13px;
            font-weight: 500;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 5px;
            transition: all 0.2s;
            width: 100%;
            text-decoration: none;
            box-sizing: border-box;
            color: var(--text-dark);
        }
        
        .btn-view {
             color: var(--text-dark);
        }

        .btn-accept {
            background-color: #d35400 !important;
            color: white !important;
            border-color: #d35400;
        }
        .btn-decline {
            color: #c0392b !important;
            border-color: #ecf0f1;
        }
        .btn:hover { opacity: 0.9; }

        .class-details {
            background-color: #f9f9f9;
            padding: 15px;
            border-radius: 8px;
            border: 1px solid #eee;
        }

        @media (max-width: 768px) {
            .proposal-card {
                flex-direction: column;
            }
            .card-left, .card-middle, .card-right {
                width: 100%;
                align-items: flex-start;
                margin-right: 0;
                margin-bottom: 15px;
            }
            .card-left {
                flex-direction: row;
                gap: 15px;
                align-items: center;
            }
            .date-info {
                text-align: left;
            }
            .action-buttons {
                flex-direction: row;
            }
        }
    </style>
</head>

<?php

/*
$query109="SELECT * FROM tracker WHERE id_user_teacher ='" . $mi_identificador . "'  AND proposal_accepted_teacher=0 AND cancelled=0 AND start_time_unix>$tiempoUnix";
$result109 = mysqli_query($link, $query109);
$n_received_proposals=mysqli_num_rows($result109);

*/

$query109="SELECT * FROM tracker WHERE id_user_teacher ='" . $mi_identificador . "'  AND proposal_accepted_teacher=2 AND cancelled=0 AND start_time_unix>$tiempoUnix";
$result109 = mysqli_query($link, $query109);
$n_next_lessons=mysqli_num_rows($result109);

$query109="SELECT * FROM tracker WHERE id_user_teacher ='" . $mi_identificador . "'  AND proposal_accepted_teacher=2 AND cancelled=0 AND paid=1 AND releasefunds=0 AND start_time_unix<=$tiempoUnix";
$result109 = mysqli_query($link, $query109);
$n_past_lessons_not_released=mysqli_num_rows($result109);




 
?>

<body>
    <div class="wrapper">
        <section class="forum-sec">
            <div class="container">
                <div class="forum-links">
                    <ul>
                        <li><a href="./received-futureclasses.php" title="">Next lessons as teacher (<?php echo $n_next_lessons; ?>)</a></li>
                        <li  class="active"><a href="./received-pendingproposals.php" title="">Received proposals as teacher (<?php echo $n_received_proposals; ?>)</a></li>
                        <li><a href="./received-pendingreleasefunds.php" title="">Pending fund releases (<?php echo $n_past_lessons_not_released; ?>)</a></li>
                    </ul>
                </div>
            </div>
        </section>

        <section class="forum-page">
            <div class="container">
                <div class="forum-questions-sec" style="width: 100%">
                    <div class="forum-questions">
                        <?php
						
							if(!$n_received_proposals)
							{
								die('<br><br><br><center>No lessons available in this section at the moment</center>');
							}
						
						
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
						
						
							//aquí sacamos las lenguas del profe

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
						
						
                        while ($fila = mysqli_fetch_array($result)) {
                            // Datos de la clase
							/*
                            $id_of_class = $fila['id_tracking'];
                            $language_to_teach = $fila['language_taught'];
                            $descriptionofsession = $fila['description_session'];
                            $duration_min = $fila['session_lenght_minutes'];
                            $total_price = $fila['price_session_total'];
							
							*/
							
							
                            $unixtimestart = $fila['start_time_unix'];
							$id_of_class=$fila['id_tracking'];	
							$creation_timestamp=$fila['created_timestamp'];
							$recurrent=$fila['created_from_recurrent']; if($recurrent==1){$recurrent='Yes';} else{$recurrent='No';}
							$id_student=$fila['id_user_student'];
							$time_shift_student=$fila['time_shift_student'];
							$dateofstart_local=$fila['date_start_local'];
							$dateofend_local=$fila['date_end_local'];
							$unixtimestart=$fila['start_time_unix'];
							$unixtimeend=$fila['end_time_unix'];
							$duration_min=$fila['session_lenght_minutes'];
							$language_to_teach=$fila['language_taught'];
							$hourly_price=$fila['hourly_rate_original'];
							$total_price=$fila['price_session_total']; $total_price=round($total_price,2);
							$descriptionofsession=$fila['description_session'];
							$teacher_accepted=$fila['proposal_accepted_teacher'];
							$teacher_accepted_timestamp=$fila['proposal_accepted_timestamp'];
							$session_paid=$fila['paid'];
							$session_paid_timestamp=$fila['timestamp_paid'];
							
							$session_releasefunds=$fila['releasefunds'];
							
							$cancelled=$fila['cancelled'];
							$fee_percentage=$fila['price_fee_percentage'];
							$amount_received_by_teacher=$total_price*(100-$fee_percentage)/100;
							
							$por_internet_o_presencial=$fila['onlineonsite'];
							$local_encuentro=$fila['id_local'];
							
							
							// sacamos huso horario
							$query99="SELECT timeshift FROM mentor2009 WHERE orden='$id_student' ";
							$result99=mysqli_query($link,$query99);
							if(!mysqli_num_rows($result99))
									die("User unregistered 1.");
							$fila99=mysqli_fetch_array($result99);

							$time_shift_student=$fila99['timeshift'];
							
							
	

							//sacamos la foto
							$extension = $fila['fotoext'];
							$path_photo="../uploader/upload_pic/thumb_$id_student"."."."$extension";

							//echo "</br></br>$path_photo</br></br>";

							if ( !file_exists($path_photo) ) :
								$path_photo="../uploader/default.jpg";
							endif;
							
							
		
							
							//aquí sacamos las lenguas del alumno

						
							//estudiante
							list($my_langs_array_multidim["$id_student"], $my_langs_full_name_array_multidim["$id_student"], 
							$my_langs_level_array_multidim["$id_student"], 
							$my_langs_forshare_array_multidim["$id_student"], 	
							$my_langs_price_array_multidim["$id_student"], $my_langs_typeofexchange_array_multidim["$id_student"], 
							$my_langs_priceorexchangetext_array_multidim["$id_student"], $my_langs_level_image_array_multidim["$id_student"], 
							$my_langs_2letters_array_multidim["$id_student"])
							= lenguas_que_conoce_usuario($id_student,$link);
							
							list($learn_langs_array_multidim["$id_student"], $learn_langs_full_name_array_multidim["$id_student"], 
							$learn_langs_level_array_multidim["$id_student"], 
							$learn_langs_forshare_array_multidim["$id_student"], 	$learn_langs_price_array_multidim["$id_student"], 
							$learn_langs_typeofexchange_array_multidim["$id_student"], 
							$learn_langs_priceorexchangetext_array_multidim["$id_student"], $learn_langs_level_image_array_multidim["$id_student"], 
							$learn_langs_2letters_array_multidim["$id_student"])
							= lenguas_que_quiere_estudiar_usuario($id_student,$link);
							

							$idiomas_comunes = array_intersect($my_langs_array_multidim["$id_student"], $my_langs_array_multidim["$mi_identificador"]);
							
							//con esta línea lo que hacemos es borrar los valores vacíos y reorganizar el array
							$idiomas_comunes = array_values(array_filter($idiomas_comunes));
							
							//print_r($idiomas_comunes); 
							
							$nombre_idioma='';
							// sacamos el nombre completo del idioma sin recurrir a hacer llamada a la bbdd
							for($rr=0;$rr<count($idiomas_comunes);$rr++)
							{
								$key_search=array_search($idiomas_comunes[$rr],$my_langs_array_multidim["$id_student"] );
								
								switch ($my_langs_level_array_multidim["$id_student"][$key_search])
								{
									case 0:
										$level_aux='Level unknown';
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
								
								$nombre_idioma.=" ".$my_langs_full_name_array_multidim["$id_student"][$key_search]." (".$level_aux.") "."&nbsp;&nbsp; ";
							}
							
							$nombre_idioma=trim($nombre_idioma); 
							
							if(empty($nombre_idioma)){$nombre_idioma='No common languages';}
							//echo "$nombre_idioma";
							
							
							//sacamos el nombre del idioma que quiere trabajar el estudiante
														
							$key_search2=array_search($language_to_teach,$my_langs_array_multidim["$mi_identificador"] );
							
							//echo "<br>$language_to_teach: $key_search2<br>";
							
							//print_r($my_langs_array_multidim["$mi_identificador"]);
							//print_r($my_langs_full_name_array_multidim["$mi_identificador"]); 
							
							$language_to_teach_fullname=$my_langs_full_name_array_multidim["$mi_identificador"]["$key_search2"];
							
							
							//sacamos nivel del estudiante, no del profesor
							$key_search3=array_search($language_to_teach,$learn_langs_array_multidim["$id_student"] );
							$level_language_to_teach=$learn_langs_level_array_multidim["$id_student"]["$key_search3"];
							

							switch ($level_language_to_teach)  
							{
								case 0:
									$level_language_to_teach_2='Level unknown';
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


							
							
							//sacamos nombre del estudiante
							
							
							$query77="SELECT nombre FROM mentor2009 WHERE orden='$id_student' ";
							$result77=mysqli_query($link,$query77);
							if(!mysqli_num_rows($result77))
									die("User unregistered 1.");
							$fila77=mysqli_fetch_array($result77);

							$student_name=$fila77['nombre'];
							$palabras = explode (" ", $student_name);
							$student_name=ucfirst($palabras[0]);
							
							
							//en caso de que haya quitado el estudiante el idioma de su lista de idiomas que quiere aprender
							if(empty($language_to_teach_fullname))
								$cadena_idioma_nivel="$language_to_teach ($level_language_to_teach_2)";
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
							
							

                            // LOGIC EXTRACTION
							$tiempoUnix_clase = $unixtimestart; 
							$fechaHoraFormateada2 = obtenerFechaHora($tiempoUnix_clase, $zonaHoraria);
							$fechaHoraFormateada2_utc0 = obtenerFechaHora($tiempoUnix_clase, 'UTC');
							$fechaHoraFormateada2_student = obtenerFechaHora($tiempoUnix_clase, $time_shift_student);

							$direccion_completa = "";
							if($por_internet_o_presencial==2 AND is_numeric($local_encuentro) ) {
								$direccion_completa="Address:<br>$nombre_establecimiento<br>$direccion_establecimiento<br>$ciudad_establecimiento<br><br>";
							}

							// LOCATION TEXT
							$location_text = "Online";
							if ($por_internet_o_presencial == 2) {
								$location_text = "Onsite"; 
							}

							// CARD HTML
							echo "<div class=\"proposal-card\">";

								// LEFT
								echo "<div class=\"card-left\">";
									echo "<div class=\"avatar-circle\">";
										echo "<img src=\"$path_photo\" alt=\"Student\" style=\"width:100%; height:100%; border-radius:50%; object-fit:cover;\">";
										echo "<i class=\"fas fa-star star-badge\"></i>";
									echo "</div>";
									echo "<span class=\"pending-status\">PENDING</span>";
								echo "</div>";

								// MIDDLE
								echo "<div class=\"card-middle\">";
									// Title
									echo "<div class=\"card-title\">$language_to_teach_fullname</div>";
									// Meta
									echo "<div class=\"meta-row\">";
										echo "<div class=\"meta-item\"><i class=\"far fa-clock\"></i> $duration_min min</div>";
										echo "<div class=\"meta-item\"><i class=\"fas fa-dollar-sign\"></i> $total_price&euro;</div>";
										echo "<div class=\"meta-item\"><i class=\"fas fa-globe\"></i> $location_text</div>";
									echo "</div>";
									
									// Languages
									echo "<div class=\"languages-label\">LANGUAGES OFFERED</div>";
									echo "<div class=\"language-tags\">";
										$student_langs = $my_langs_full_name_array_multidim["$id_student"];
										 if (is_array($student_langs)) {
											 foreach($student_langs as $idx => $lang) {
												 if(!empty($lang)) {
													echo "<div class=\"lang-tag\">$lang</div>";
												 }
											 }
										}
									echo "</div>";
									
									// Expanded Details
									echo "<div class=\"class-details\" id=\"details-class$id_of_class\" style=\"display:none; margin-top:15px;\">";
										 echo "<h4>Details</h4>";
										 echo "<p><strong>Message:</strong> $descriptionofsession</p>";
										 echo "<hr>";
										 echo "<p><strong>Additional Info:</strong><br>";
										 echo "My time zone: $my_timeshift<br>";
										 echo "Student time zone: $time_shift_student<br>";
										 echo "Start time (My local): $fechaHoraFormateada2<br>";
										 echo "Start time (Student local): $fechaHoraFormateada2_student<br>";
										 echo "Netto amount: $amount_received_by_teacher&euro;<br>"; 
										 echo "$direccion_completa";
										 echo "</p>";
										 if($session_paid==0) echo "<p style='color:orange'>Waiting for student deposit</p>";
										 elseif($session_releasefunds==0) echo "<p style='color:green'>Student made deposit</p>";
									echo "</div>";

								echo "</div>"; // end middle

								// RIGHT
								echo "<div class=\"card-right\">";
									echo "<div class=\"date-info\"><i class=\"far fa-clock\"></i> $fechaHoraFormateada2</div>";
									echo "<div class=\"action-buttons\">";
										echo "<button class=\"btn btn-view class-name\" data-id=\"$id_of_class\">View Details</button>";
										echo "<a href=\"#\" class=\"btn btn-accept btn-aceptar\" data-url=\"./teacheracceptdecline.php?trackid=$id_of_class&action=2\"><i class=\"fas fa-check\"></i> Accept</a>";
										echo "<a href=\"#\" class=\"btn btn-decline btn-denegar\" data-url=\"./teacheracceptdecline.php?trackid=$id_of_class&action=1\"><i class=\"fas fa-times\"></i> Decline</a>";
									echo "</div>";
								echo "</div>"; // end right

							echo "</div>"; // end card
                        }
                        ?>
                    </div>
                </div>
            </div>
        </section>
        <?php
			require('../templates/footer.php');
		?>
    </div>

    <script type="text/javascript" src="../public/js/jquery.min.js"></script>
    <script type="text/javascript" src="../public/js/bootstrap.min.js"></script>
    <script type="text/javascript">
		//Boton de aceptar y denegar
		const btnAceptars = document.querySelectorAll('.btn-aceptar');
    	const btnDenegars = document.querySelectorAll('.btn-denegar');
    	let notification = document.querySelector('.notification');
        
		document.addEventListener('DOMContentLoaded', function () {
            var classNames = document.querySelectorAll('.class-name');
            classNames.forEach(function (className) {
                className.addEventListener('click', function () {
                    var classId = className.getAttribute('data-id');
                    var classDetails = document.getElementById('details-class' + classId);
                    if (classDetails.style.display === 'none' || classDetails.style.display === '') {
                        classDetails.style.display = 'block';
                    } else {
                        classDetails.style.display = 'none';
                    }
                });
            });
        });

		// Si no existe, la creamos dinámicamente
		if (!notification) {
        	notification = document.createElement('div');
        	notification.classList.add('notification');
        	document.body.appendChild(notification); // La agregamos al body o el contenedor que prefieras
    	}

		// Procesar cada botón de "Aceptar"
		btnAceptars.forEach(function(btnAceptar) {
        	btnAceptar.addEventListener('click', function(event) {
        	    event.preventDefault(); // Prevenir que el enlace realice la acción de navegación
        	    console.log('Botón Aceptar presionado'); 
        	    const url = btnAceptar.getAttribute('data-url'); // Obtener la URL del atributo data-url

        	    fetch(url, { // Realizar la solicitud HTTP sin redirigir (usando fetch)
        	        method: 'GET',
        	    })
        	    .then(response => response.text())
        	    .then(data => {
        	        console.log('Respuesta recibida:', data); // Mostrar la respuesta del servidor si es necesario
        	    })
        	    .catch(error => {
        	        console.error('Error en la solicitud:', error); // Manejo de errores
        	    });

        	    // Mostrar la notificación
        	    notification.classList.add('show');
        	    notification.style.display = 'block';

        	    // Ocultar la notificación después de 3 segundos
        	    setTimeout(() => {
        	        notification.classList.remove('show');
        	        notification.style.display = 'none';
        	    }, 3000);

        	    // Redirigir a la URL
        	    setTimeout(function() { window.location.href = btnAceptar.href;}, 100);
        	});
    	});
		// Procesar cada botón de "Denegar"
		btnDenegars.forEach(function(btnDenegar) {
    	    btnDenegar.addEventListener('click', function(event) {
    	        event.preventDefault(); // Prevenir que el enlace realice la acción de navegación
    	        console.log('Botón Denegar presionado');
    	        const url = btnDenegar.getAttribute('data-url'); // Obtener la URL desde el atributo data-url

    	        fetch(url, { // Realizar la solicitud HTTP sin redirigir (usando fetch)
    	            method: 'GET',
    	        })
    	        .then(response => response.text())
    	        .then(data => {
    	            console.log('Respuesta recibida:', data);
    	        })
    	        .catch(error => {
    	            console.error('Error en la solicitud:', error);
    	        });

    	        // Mostrar la notificación (puedes personalizar el mensaje)
    	        notification.classList.add('show', 'decline');
    	        notification.style.display = 'block';
    	        notification.innerText = 'You have declined the session!';

    	        // Ocultar la notificación después de 3 segundos
    	        setTimeout(() => {
    	            notification.classList.remove('show', 'decline');
    	            notification.style.display = 'none';
    	        }, 4000);

    	        // Redirigir a la URL
    	        setTimeout(function() { window.location.href = btnDenegar.href;}, 600);
    	    });
    	});

    </script>
</body>

</html>
