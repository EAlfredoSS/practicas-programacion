<?php 
require('../templates/header_simplified.html');
require('../files/bd.php');
$mi_identificador = 4533;
$time_shift_student = '';

$query = "SELECT * FROM tracker WHERE id_user_student ='" . $mi_identificador . "' AND proposal_accepted_teacher=2 AND cancelled=0 AND paid=1 ORDER BY start_time_unix ASC";
$result = mysqli_query($link, $query);
$nuevos = mysqli_num_rows($result);
if (!$nuevos) die("No sessions for this user yet");

for ($i = 0; $i < $nuevos; $i++) {
    $fila = mysqli_fetch_array($result);
    $id_of_class = $fila['id_tracking'];
    $creation_timestamp = $fila['created_timestamp'];
    $recurrent = $fila['created_from_recurrent'];
    $id_student = $fila['id_user_student'];
    $id_teacher = $fila['id_user_teacher'];
    $time_shift_student = $fila['time_shift_student'];
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
    
    $query77="SELECT nombre FROM mentor2009 WHERE orden='$id_student' ";
		$result77=mysqli_query($link,$query77);
		if(!mysqli_num_rows($result77))
				die("User unregistered 1.");
		$fila77=mysqli_fetch_array($result77);
		$student_name=$fila77['nombre'];
		$palabras = explode (" ", $student_name);
		$student_name=ucfirst($palabras[0]);

    /*echo "<ul>";
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
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Next classes as teacher</title>
    <style type="text/css">
        .tooltip-container { position: relative; display: inline-block; }
        .tooltip-text { font-size: 16px; visibility: hidden; width: 380px; background-color: #000; color: #fff; text-align: left; border-radius: 6px; padding: 50px; position: absolute; z-index: 1; top: 30%; left: 50%; transform: translateX(-50%); opacity: 0; transition: opacity 0.3s; }
        .tooltip-container:hover .tooltip-text { visibility: visible; opacity: 0.75; }
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
    position: relative; /* Añade esto */
    min-height: 120px; /* Opcional: para asegurar espacio suficiente */
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
    position: absolute;
    bottom: 10px;
    right: 10px;
    font-size: 12px;
    color: #aaa;
    margin-top: 0;
    display: block; /* Quita el flex */
}
        .fa{
			font-size: 15px;
		}

        .class-details {
    background-color: transparent !important; /* Asegura que el fondo sea transparente */
    padding: 0; /* Elimina cualquier espacio extra */
    border: none; /* Asegura que no haya bordes */
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

        .tooltip-container {
            position: absolute;
            top: 10px;
            right: 10px;
            display: inline-block;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <section class="forum-sec">
            <div class="container">
                <div class="forum-links">
                    <ul>
                        <li class="active"><a href="./received-futureclasses.php" title="">Next lessons as teacher (<?php echo $n_next_lessons; ?>)</a></li>
                        <li><a href="./received-pendingproposals.php" title="">Received proposals as teacher (<?php echo $n_received_proposals; ?>)</a></li>
                        <li><a href="./received-pastclasses.php" title="">Past lessons as teacher (<?php echo $n_past_lessons; ?>)</a></li>
                    </ul>
                </div>
            </div>
        </section>
        <section class="forum-page">
            <div class="container">
                <div class="forum-questions-sec" style="width: 100%">
                    <div class="forum-questions">
                        <?php
                        $result = mysqli_query($link, $query);
                        while ($fila = mysqli_fetch_array($result)) { 

                            //extraer foto
                            $id_student = $fila['id_user_student'];
                            $extension = $fila['fotoext'];
							$path_photo="../uploader/upload_pic/thumb_$id_student"."."."$extension";

                            if ( !file_exists($path_photo) ) :
								$path_photo="../uploader/default.jpg";
							endif;
?>
<?php
                            echo "<div class=\"usr-question\">";
                            echo "<div class=\"usr-img\"><a href=\"../user/u.php?identificador=$id_student\"><img src=\"$path_photo\" alt=\"Student Image\"></a>
							<br><center style=\"margin-top:85%; font-size: 80%\" >$student_name</center>
							</div>";
                            echo "<div class=\"usr_quest\">";
                            ?>
                                    <h3><?php echo $fila['language_taught']; ?></h3>
                                    <h4><?php echo $fila['description_session'];?> </h4>
                                    
                                    <div><span class="quest-posted-time"><i class="fa fa-clock-o"></i> <?php echo $fila['date_start_utc0']; ?></span></div>
                            <?php
                                    echo "<h6><i class=\"far fa-hourglass\"></i> $duration_min min &nbsp;&nbsp;&nbsp;&nbsp;
							<i class=\"fas fa-coins\"></i> $total_price € 
							&nbsp;&nbsp;&nbsp;&nbsp;<i class=\"fas fa-comment\"></i>&nbsp; $nombre_idioma</h6>";
                            
                            
?>
                            <ul class="class-details">
    <?php 
        if ($fila['proposal_accepted_teacher'] == 0) {
            echo '<li><a href="#" class="text-warning" style="background-color:#b2b2b2; padding: 5px 10px; border-radius: 2px; display: inline-block;">Awaiting confirmation from the teacher</a></li>';
        }

        if ($fila['proposal_accepted_teacher'] == 2 && $fila['paid'] == 0) {
            echo '<li><a href="./studentprepayment.php?trackid=' . $fila['id_tracking'] . '" class="btn btn-primary" style="padding: 5px 10px; display: inline-block;">Proceed to Payment</a></li>';
        }

        if ($fila['paid'] == 1) {
            echo '<li><a href="#" class="text-success" style="background-color:#53d690; padding: 5px 10px; border-radius: 2px; display: inline-block;">Paid</a></li>';
        }

        if ($fila['paid'] == 0 && $fila['cancelled'] == 0) {
            echo '<li><a href="./studentcancel.php?trackid=' . $fila['id_tracking'] . '" class="btn btn-danger" style="padding: 5px 10px; display: inline-block;">Cancel Session</a></li>';
        }
    ?>
</ul>
<div class="tooltip-container" style="color:#b2b2b2; margin: -10px 0 0 0; align: center;">
    <i style="color:#b2b2b2;margin-left: 800%;margin-top: 10px;font-size:20px;" class="fas fa-info-circle"></i>
    <span class="tooltip-text" style="font-size: 12px;"></span>
</div>
            
                                </div>
                                
                            </div>
                        <?php }
                        ?>
                    </div>
                </div>
            </div>
        </section>
    </div>
</body>
</html>