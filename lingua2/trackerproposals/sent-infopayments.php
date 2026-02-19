<?php 

require('../files/bd.php');

echo "My spent money<br><br>";


$mi_identificador=4533;
//$mi_identificador=4588;


$query="SELECT * FROM tracker WHERE id_user_student ='" . $mi_identificador . "'  AND paid=1  ORDER BY start_time_unix ASC ";

//echo " $query ";


$result = mysqli_query($link, $query);
 
$nuevos=mysqli_num_rows($result);

 if (!$nuevos)
        echo " No payments made by this user yet.";
	

$total_amount_paid_by_user=0;	
	
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
	
	$total_amount_paid_by_user+=$total_price;
	
	$style_1='';
	if($cancelled==1){  $style_1="style=\"text-decoration:line-through;\" ";  }   
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
	echo "<li style=\"font-weight: bold;\">Total session price: $total_price</li>";
	echo "<li>Description of session: $descriptionofsession</li>";
	echo "<li>Teacher accepted?: $teacher_accepted</li>";
	echo "<li>Session has been paid?: $session_paid</li>";	
	echo "<li>Session has been cancelled?: $cancelled</li>";
	echo "<li>Info created from recurrently: $recurrent</li>";
	echo "<li>Info Teacher accepted timestamp: $teacher_accepted_timestamp</li>";
	echo "<li>Info Session payment timestamp: $session_paid_timestamp</li>";
	echo "</ul>";
	

}

echo "<p style=\"font-weight: bold;\">---- Total amount paid: $total_amount_paid_by_user ---  </p><br><br>";


/*
echo "------------------------------------------------------------------------------------------<br><br>
My money earnt<br><br>";




$query22="SELECT * FROM tracker WHERE id_user_teacher ='" . $mi_identificador . "'  AND paid=1  ORDER BY start_time_unix ASC ";

//echo " $query ";


$result22 = mysqli_query($link, $query22);
 
$nuevos22=mysqli_num_rows($result22);

 if (!$nuevos22)
        echo " No payments received by this user yet.";
	

$total_amount_paid_to_user=0;	
	
for($i=0;$i<$nuevos22;$i++)
{
	$fila22=mysqli_fetch_array($result22);
	
	$id_of_class=$fila22['id_tracking'];
	$creation_timestamp=$fila22['created_timestamp'];
	$recurrent=$fila22['created_from_recurrent'];
	$id_student=$fila22['id_user_student'];
	$id_teacher=$fila22['id_user_teacher'];	
	$time_shift_student=$fila22['time_shift_student'];
	$dateofstart_utc0=$fila22['date_start_utc0'];
	$dateofend_utc0=$fila22['date_end_utc0'];
	$unixtimestart=$fila22['start_time_unix'];
	$unixtimeend=$fila22['end_time_unix'];
	$duration_min=$fila22['session_lenght_minutes'];
	$language_to_teach=$fila22['language_taught'];
	$hourly_price=$fila22['hourly_rate_original'];
	$total_price=$fila22['price_session_total'];
	$descriptionofsession=$fila22['description_session'];
	$teacher_accepted=$fila22['proposal_accepted_teacher'];
	$teacher_accepted_timestamp=$fila22['proposal_accepted_timestamp'];
	$session_paid=$fila22['paid'];
	$session_paid_timestamp=$fila22['timestamp_paid'];
	$cancelled=$fila22['cancelled'];
	
	$total_amount_paid_to_user+=$total_price;
	
	$style_1='';
	if($cancelled==1){  $style_1="style=\"text-decoration:line-through;\" ";  }   
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
	echo "<li style=\"font-weight: bold;\">Total session price: $total_price</li>";
	echo "<li>Description of session: $descriptionofsession</li>";
	echo "<li>Teacher accepted?: $teacher_accepted</li>";
	echo "<li>Session has been paid?: $session_paid</li>";	
	echo "<li>Session has been cancelled?: $cancelled</li>";
	echo "<li>Info created from recurrently: $recurrent</li>";
	echo "<li>Info Teacher accepted timestamp: $teacher_accepted_timestamp</li>";
	echo "<li>Info Session payment timestamp: $session_paid_timestamp</li>";
	echo "</ul>";
	

}

echo "<p style=\"font-weight: bold;\">---- Total amount paid: $total_amount_paid_to_user ---  </p><br><br>";

*/

?>



