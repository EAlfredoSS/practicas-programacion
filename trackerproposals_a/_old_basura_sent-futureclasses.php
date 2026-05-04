<?php 

/*ini_set('display_errors', 1);

ini_set('display_startup_errors', 1);

error_reporting(E_ALL);*/



require('../templates/header_simplified.html');

require('../files/bd.php');





$mi_identificador=4533;

$time_shift_student='';



	?>

	</br>

	<a href="./studentprepaymentmultiple.php">Proceed to payment multiple classes at once</a>

	</br></br>

	<?php

 



// AND cancelled=0 ???

$query="SELECT * FROM tracker WHERE id_user_student ='" . $mi_identificador . "'  AND proposal_accepted_teacher=2 AND cancelled=0 AND paid=1 ORDER BY start_time_unix ASC ";



//echo " $query ";





$result = mysqli_query($link, $query);

 

$nuevos=mysqli_num_rows($result);



 if (!$nuevos)

        die(" No sessions for this user yet");

	

	

for($i=0;$i<$nuevos;$i++)

{

	$fila=mysqli_fetch_array($result);

	

	$id_of_class=$fila['id_tracking'];

	$creation_timestamp=$fila['created_timestamp'];

	$recurrent=$fila['created_from_recurrent'];

	$id_student=$fila['id_user_student'];

	$id_teacher=$fila['id_user_teacher'];

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

	

	$style_1='';

	/*if($cancelled==1){  $style_1="style=\"text-decoration:line-through;\" ";  }   

	echo "<ul   $style_1   >";



	echo "$id_of_class";

	echo "<li>Student id: $id_student</li>";

	echo "<li>Teacher id: $id_teacher</li>";

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

	

	

	if( $teacher_accepted==0 ) //when the session has already not been accepted by the teacher

	{

		echo "<li style=\"color:red;\">Awaiting confirmation of the teacher</li>";

	}

	

	if( $teacher_accepted==2 AND $session_paid==0 ) //when the session has already been accepted by the teacher

	{

	?>

		<li><a href="./studentprepayment.php?trackid=<?php echo "$id_of_class"; ?>">Proceed to payment</a></li>

	<?php

	}

	else if($session_paid==1)

	{

		echo "<li style=\"color:green;\">Paid</li>";

	}

	if( $session_paid==0 AND $cancelled==0)

	{

	?>

	<li><a href="./studentcancel.php?trackid=<?php echo "$id_of_class"; ?>">Cancel session</a></li>

	<?php	

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

    margin-top: -9.4%;

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
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            display: flex;
            justify-content: flex-start;
            flex-direction: column;
            position: relative;
            margin-bottom: 1px;; 
        }

        .session-info {
    font-size: 14px;
    color: #666;
    margin-top: 5px;
}

.session-info span {
    display: inline-block;
    margin-right: 20px;
}

.usr_quest h3 {
    font-size: 18px;
    margin-bottom: 5px;
    color: #333;
}

        .usr_quest {
            width: 80%;
        }

        .usr_quest h3 {
            font-size: 18px;
            margin-bottom: 15px;
            color: #333;
        }

        .job-dt {
            display: flex;
            flex-wrap: wrap;
            gap: 15px 30px;
            padding: 0;
            margin: 0;
            margin: 15px 0 0 0;
        }

        .job-dt li {
            display: flex;
            align-items: center;
            font-size: 14px;
            color: #666;
            white-space: nowrap;
        }

        .job-dt li i {
            margin-right: 8px;
            color: #e65f00;
        }

        .status-section {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            gap: 10px;
            padding-right: 20px;
        }

        .session-meta {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .payment-status {
            font-weight: 500;
            font-size: 14px;
        }

        .text-success { color: #28a745; }
        .text-warning { color: #e65f00; }

        .btn {
            padding: 6px 12px;
            text-decoration: none;
            font-size: 13px;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background: #e65f00;
            color: white;
        }

        .btn-danger {
            background: #dc3545;
            color: white;
        }

        @media (max-width: 768px) {
            .usr-question {
                flex-direction: column;
                gap: 15px;
            }
            
            .status-section {
                align-items: flex-start;
                padding-right: 0;
            }
            
            .job-dt {
                gap: 10px;
            }
        }
        .pricing-details {
    display: flex;
    flex-direction: column;
    gap: 5px;
}

.job-dt {
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
    padding: 0;
    list-style: none;
}

.job-dt li {
    display: flex;
    align-items: center;
    font-size: 14px;
    color: #666;
}

.job-dt li i {
    margin-right: 8px;
    color: #e65f00; 
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

while ($fila = mysqli_fetch_array($result)) { ?>
<div class="usr-question">
    <div class="usr_quest">
        <h3>Session ID: <?php echo $fila['id_tracking']; ?></h3>
        
        <div class="session-info">
            <span>Student ID: <?php echo $fila['id_user_student']; ?></span>
            <span>Teacher ID: <?php echo $fila['id_user_teacher']; ?></span>
        </div>

        <!-- Contenedor de Precios y Fechas -->
        <div class="pricing-details">
            <ul class="job-dt">
                <li><i class="fas fa-coins"></i> Price per hour: $<?php echo $fila['hourly_rate_original']; ?></li>
                <li><i class="fas fa-coins"></i> Total Price: $<?php echo $fila['price_session_total']; ?></li>
                <li><i class="far fa-hourglass"></i> Duration: <?php echo $fila['session_lenght_minutes']; ?> min</li>
            </ul>

            <!-- Fechas con mismo formato y colores -->
            <ul class="job-dt">
                <li><i class="fas fa-calendar-alt"></i> Start Date (UTC-0): <?php echo $fila['date_start_utc0']; ?></li>
                <li><i class="fas fa-calendar-alt"></i> End Date (UTC-0): <?php echo $fila['date_end_utc0']; ?></li>
            </ul>
        </div>

        <div class="class-details">
            <?php if ($fila['proposal_accepted_teacher'] == 0) { ?>
                <a class="text-warning">Awaiting confirmation from the teacher</a>
            <?php } ?>
            <?php if ($fila['proposal_accepted_teacher'] == 2 && $fila['paid'] == 0) { ?>
                <a href="./studentprepayment.php?trackid=<?php echo $fila['id_tracking']; ?>" class="btn btn-primary">Proceed to Payment</a>
            <?php } ?>
            <?php if ($fila['paid'] == 1) { ?>
                <a class="text-success">Paid</a>
            <?php } ?>
            <?php if ($fila['paid'] == 0 && $fila['cancelled'] == 0) { ?>
                <a href="./studentcancel.php?trackid=<?php echo $fila['id_tracking']; ?>" class="btn btn-danger">Cancel Session</a>
            <?php } ?>
        </div>
    </div>
</div>




<?php }

						?>
