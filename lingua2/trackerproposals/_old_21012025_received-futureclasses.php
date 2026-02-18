<?php 
require('../templates/header_simplified.html');
require('../files/bd.php');

$mi_identificador = 4588;
$time_shift_teacher = '';

// Consulta SQL para obtener las clases
$query = "
SELECT t.*, m.*
FROM tracker t
INNER JOIN mentor2009 m
ON t.id_user_teacher=m.orden
WHERE t.id_user_teacher ='" . $mi_identificador . "'  AND proposal_accepted_teacher=2 AND cancelled=0  
ORDER BY t.start_time_unix DESC";

// Ejecutamos la consulta
$result = mysqli_query($link, $query);

// Contamos cuántos resultados hay
$nuevos = mysqli_num_rows($result);


//echo "$query";


if (!$nuevos) {
    die("No sessions for this user yet");
    //for($i=0;$i<$nuevos;$i++)
//{
	//$fila=mysqli_fetch_array($result);
	
	//$id_of_class=$fila['id_tracking'];	
	//$creation_timestamp=$fila['created_timestamp'];
	//$recurrent=$fila['created_from_recurrent'];
	//$id_student=$fila['id_user_student'];
	//$time_shift_student=$fila['time_shift_student'];
	//$dateofstart_utc0=$fila['date_start_utc0'];
	//$dateofend_utc0=$fila['date_end_utc0'];
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
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Next classes as teacher</title>
	
	
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
  top: 125%; /* Posiciona el tooltip bajo del elemento */
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
    color: #e74c3c; /* Color del enlace activo */
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
    background-color: #e74c3c; /* Línea roja debajo del enlace activo */
}


        .usr-question {
            background-color: #fff;
            padding: 20px;
            border-radius: 1px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            display: flex;
            flex-direction: row;
            margin-bottom: 1px;
        }

        .usr-img {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            margin-top: 10px;
            margin-right: 15px;
            flex-shrink: 0;
        }

        .usr-img img {
            width: 100%;
            height: 100%;
            border-radius: 50%;
        }

        .usr_quest {
            width: 75%;
        }

        .usr_quest h3, .usr_quest h4, .usr_quest h6 {
            margin: 10px 0;
            width:100%;
        }

        .usr_quest h3 {
            font-size: 18px;
            font-weight: bold;
            color: #333;
        }

        .usr_quest h4 {
            color: #555;
        }

        .usr_quest h6 {
            font-size: 14px;
            color: #666;
        }

        .usr_quest ul.job-dt {
            list-style-type: none;
            padding: 0;
            margin: 0;
        }

        .usr_quest ul.job-dt li {
            font-size: 14px;
            color: #888;
            margin-bottom: 8px;
        }

        .job-dt li a {
            background-color: #51a5fb;
            border-radius: 2px;
        }

        .quest-posted-time {
            font-size: 12px;
            color: #aaa;
            margin-top: 9%;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .class-details {
            background-color: #f4f4f4;
            padding: 15px;
            border-radius: 8px;
            margin-top: 10px;
        }

        .class-details h4 {
            font-size: 16px;
            color: #333;
        }

        .class-details p {
            font-size: 14px;
            color: #666;
        }
        .forum-page{
			margin-bottom:20px;
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
			.forum-questions .usr_quest {
				width: 60%;
			}
		}
    </style>
</head>

<body>
    <div class="wrapper">
        <section class="forum-sec">
            <div class="container">
                <div class="forum-links">
                    <ul>
                        <li class="active"><a href="#" title="">Next lessons as teacher</a></li>
                        <li><a href="#" title="">Received proposals as teacher</a></li>
                        <li><a href="#" title="">Past lessons as teacher</a></li>
                    </ul>
                </div>
            </div>
        </section>

        <section class="forum-page">
            <div class="container">
                <div class="forum-questions-sec" style="width: 80%">
                    <div class="forum-questions">
                        <?php
                        while ($fila = mysqli_fetch_array($result)) {
                            // Datos de la clase
							/*
                            $id_of_class = $fila['id_tracking'];
                            $language_to_teach = $fila['language_taught'];
                            $descriptionofsession = $fila['description_session'];
                            $duration_min = $fila['session_lenght_minutes'];
                            $total_price = $fila['price_session_total'];
                            $unixtimestart = $fila['start_time_unix'];
							*/
							
							
							$id_of_class=$fila['id_tracking'];	
							$creation_timestamp=$fila['created_timestamp'];
							$recurrent=$fila['created_from_recurrent']; if($recurrent==1){$recurrent='Yes';} else{$recurrent='No';}
							$id_student=$fila['id_user_student'];
							$time_shift_student=$fila['time_shift_student'];
							$dateofstart_utc0=$fila['date_start_utc0'];
							$dateofend_utc0=$fila['date_end_utc0'];
							$unixtimestart=$fila['start_time_unix'];
							$unixtimeend=$fila['end_time_unix'];
							$duration_min=$fila['session_lenght_minutes'];
							$language_to_teach=$fila['language_taught'];
							$hourly_price=$fila['hourly_rate_original'];
							$total_price=$fila['price_session_total'];
							$descriptionofsession=$fila['description_session'];
							$teacher_accepted=$fila['proposal_accepted_teacher'];
							$teacher_accepted_timestamp=$fila['proposal_accepted_timestamp'];
							$session_paid=$fila['paid'];
							$session_paid_timestamp=$fila['timestamp_paid'];
							$cancelled=$fila['cancelled'];
							$fee_percentage=$fila['price_fee_percentage'];
							$amount_received_by_teacher=$total_price*(100-$fee_percentage)/100;
							
									 
							
                            $student_name = $fila['nombre'];
							
							
							//sacamos la foto
							$extension = $fila['fotoext'];
							$path_photo="../uploader/upload_pic/thumb_$mi_identificador"."."."$extension";

							//echo "</br></br>$path_photo</br></br>";

							if ( !file_exists($path_photo) ) :
								$path_photo="../uploader/default.jpg";
							endif;
							
							

                            echo "<div class=\"usr-question\">";
                            echo "<div class=\"usr-img\"><img src=\"$path_photo\" alt=\"Student Image\">
							<br><center>$student_name</center>
							</div>";
                            echo "<div class=\"usr_quest\">";
                            echo "<h3 class=\"class-name\" data-id=\"$id_of_class\" style=\"color: #000;\"> $language_to_teach</h3>";
                            echo "<h4>$descriptionofsession</h4>";
                            echo "<h6><i class=\"far fa-hourglass\"></i> $duration_min min &nbsp;&nbsp;&nbsp;&nbsp;<i class=\"fas fa-coins\"></i> $total_price €  </h6>";

														
							
                            echo "<ul class=\"job-dt\">";
                            echo "<li><a href=\"#\" title=\"\">Student already deposited the money</a></li>";
                            echo "</ul>";
                            echo "</div>";
							
							
							
							
							?>
							
								<div class="tooltip-container" style="font-size: 16px; color:#b2b2b2; margin: -10px 0 0 0; align: center;">
																
																	 <i style="color:#b2b2b2;" class="fas fa-info-circle"></i> 
																	<span class="tooltip-text">
																	
																	
																	<?php echo "Additional information: <br><br>
																		Lesson ID: #$id_of_class	<br>
																		Created from a serie of lessons: $recurrent<br>
																		Student ID: #$id_student<br>
																		Student time shift: $time_shift_student<br>
																		Date of start UTC-0: $dateofstart_utc0<br>
																		Date of start UTC-0: $dateofend_utc0<br>
																		$language_to_teach<br>
																		Hourly price: $hourly_price<br>
																		Total price: $total_price<br>
																		Lesson description: $descriptionofsession<br>
																		Amount received by the teacher: $amount_received_by_teacher<br>
																		Creation date: $creation_timestamp <br>
												
																	";	 ?></span> 
																
								</div>
								
								<br><br>
							
							<?php
							
							
							
							
							
							
                            echo "<span class=\"quest-posted-time\"><i class=\"fa fa-clock-o\"></i> " . date('Y-m-d H:i:s', $unixtimestart) . "</span>";
                            echo "<div class=\"class-details\" id=\"details-class$id_of_class\" style=\"display:none;\">";
                            echo "<h4>Detalles de la Clase $id_of_class</h4>";
                            echo "<p>Información detallada sobre la clase. Aquí puedes agregar más detalles o información extra.</p>";
                            echo "</div>";
                            echo "</div>";
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
    </script>
</body>

</html>
