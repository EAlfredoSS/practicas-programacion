<?php 

require('../files/bd.php');


$mi_identificador=4588;
$time_shift_teacher='';
 


$query="SELECT * FROM tracker WHERE id_user_teacher ='" . $mi_identificador . "'";

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
	
	
	echo "<ul>";
	echo "$id_of_class";
	echo "<li>Student id: $id_student</li>";
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
	echo "</ul>";
	
	
}


?>



