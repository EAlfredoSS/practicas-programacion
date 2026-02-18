<?php

session_start();
$student_id=$_SESSION['orden2017'];

//coger $teacher_id de la session
//$student_id=4533;

$id_class=$_GET['trackid'];

require('../files/bd.php');

//ponemos   AND id_user_student='$student_id'  porque si no el usuario estudiante podria pagar las clases que no son suyas
//ponemos   AND paid=0, para que no se pueda pagar dos veces
//ponemos 	AND proposal_accepted_teacher=2 porque solo se pueden pagar las clases que ha aceptado previamente el profesor

$query=" 
SELECT * 
FROM tracker
WHERE id_tracking='$id_class' AND id_user_student='$student_id' AND paid=0 AND cancelled=0 AND proposal_accepted_teacher=2
";


$result = mysqli_query($link, $query);
 
$nuevos=mysqli_num_rows($result);

if ($nuevos!=1)
	die(" Error 4562. Contact webmaster.  ");


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
	
	
if( $teacher_accepted==2 AND $session_paid==0 ) //when the session has already been accepted by the teacher
{
	
//encriptamos para meterlo en herramienta de pago y lo recuperamos en la pantalla de exito despues del pago
$encoded_class = base64_encode(openssl_encrypt($id_of_class, 'AES-256-CBC', 'Vm95YWNyZWFydW5hQ2w0dmVwNHJhNHBhc2FybG9zZTNudG9z'));
$encoded_amount = base64_encode(openssl_encrypt($total_price, 'AES-256-CBC', 'Vm95YWNyZWFydW5hQ2w0dmVwNHJhNHBhc2FybG9zZTNudG9z'));



//$destination_page="./studentpayment.php?".'id='.$encoded_class.'&am='.$encoded_amount;
	
	
}

	
echo "</ul>";

 
?>


<?php		

$itemid1='single_payment';  //aqui pasamos la información para que la página de success no redirija con header location a la que corresponda
							//seria un itemID para cada producto para que podamos identificarlo
$itemname1="Class (language: $language_to_teach) ($duration_min min)";
$productname1="Single class";
$itemdescription1="$duration_min min class (language: $language_to_teach)"; //lo usaremos tambien como product description
$internalcodename1="$encoded_class||||||||||||||$encoded_amount";   //pasamos los parametros para la pagina de success
$amountprice1=$total_price;


$itemid1 = base64_encode(openssl_encrypt($itemid1, 'AES-256-CBC', 'Vm95YWNyZWFydW5hQ2w0dmVwNHJhNHBhc2FybG9zZTNudG9z'));
$itemname1 = base64_encode(openssl_encrypt($itemname1, 'AES-256-CBC', 'Vm95YWNyZWFydW5hQ2w0dmVwNHJhNHBhc2FybG9zZTNudG9z'));
$productname1 = base64_encode(openssl_encrypt($productname1, 'AES-256-CBC', 'Vm95YWNyZWFydW5hQ2w0dmVwNHJhNHBhc2FybG9zZTNudG9z'));
$itemdescription1 = base64_encode(openssl_encrypt($itemdescription1, 'AES-256-CBC', 'Vm95YWNyZWFydW5hQ2w0dmVwNHJhNHBhc2FybG9zZTNudG9z'));
$internalcodename1 = base64_encode(openssl_encrypt($internalcodename1, 'AES-256-CBC', 'Vm95YWNyZWFydW5hQ2w0dmVwNHJhNHBhc2FybG9zZTNudG9z'));
$amountprice1 = base64_encode(openssl_encrypt($amountprice1, 'AES-256-CBC', 'Vm95YWNyZWFydW5hQ2w0dmVwNHJhNHBhc2FybG9zZTNudG9z'));
 
?> 

<br>
<br>
<form name="payment_event_promotion" action="../payments/index.php" ENCTYPE="multipart/form-data" method="POST">

	<INPUT TYPE="text" id="itemid" name="itemid" value="<?php echo "$itemid1"; ?>"  hidden>
	<INPUT TYPE="text" id="itemname"name="itemname" value="<?php echo "$itemname1"; ?>" hidden >
	<INPUT TYPE="text" id="productname" name="productname" value="<?php echo "$productname1"; ?>" hidden>
	<INPUT TYPE="text" id="itemdescription" name="itemdescription" value="<?php echo "$itemdescription1"; ?>" hidden>
	<INPUT TYPE="text" id="internalcodename" name="internalcodename" value="<?php echo "$internalcodename1"; ?>" hidden>
	<INPUT TYPE="text" id="amountprice" name="amountprice" value="<?php echo "$amountprice1"; ?>" hidden>
	
	<br><br>


	<button type="submit" style="
										  background-color: #e65f00;  border: none;
										  color: white;
										  padding: 10px 11px;
										  text-align: center;
										  border-radius: 10px;
									  ">Continue to payment</button>


   
</form>