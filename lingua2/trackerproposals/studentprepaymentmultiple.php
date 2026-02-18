<?php

session_start();
$student_id=$_SESSION['orden2017'];

//coger $teacher_id de la session
//$student_id=4533;

//$id_class=$_GET['trackid'];

require('../files/bd.php');


?>

Here we show all the classes of the student user that has been accepted by the teacher pending of payment.
<br><br>


<?php

$amount_of_classes_pending_of_payment=0;
$id_of_classes_to_pay=array();


//ponemos   AND id_user_student='$student_id'  porque si no el usuario estudiante podria pagar las clases que no son suyas
//ponemos   AND paid=0, para que no se pueda pagar dos veces
//ponemos 	AND proposal_accepted_teacher=2 porque solo se pueden pagar las clases que ha aceptado previamente el profesor


 //hay que pagar con 12 horas de antelacion, si no desaparecerá de la lista
 $tiempo_corte=time();
 //$tiempo_corte=$tiempo_corte+3600*12;

$query=" 
SELECT * 
FROM tracker
WHERE id_user_student='$student_id' AND paid=0 AND cancelled=0 AND proposal_accepted_teacher=2 AND $tiempo_corte<=start_time_unix
";


$result = mysqli_query($link, $query);
 
$nuevos=mysqli_num_rows($result);

if (!$nuevos)
	die(" Error 4530. Contact webmaster.  ");


for($iii=0;$iii<$nuevos;$iii++)
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
	
	$amount_of_classes_pending_of_payment+=$total_price;
	array_push($id_of_classes_to_pay,"$id_of_class");
		
		
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
<br><br>
Total amount: <?php echo "$amount_of_classes_pending_of_payment"; ?>

<br>

<?php 
//print_r($id_of_classes_to_pay); 
$list_classes_to_encode = implode('|||', $id_of_classes_to_pay);

//$string_url = 'ids='.$string_url."&am=$amount_of_classes_pending_of_payment";

//here we will consider that the string lenght is not too long for the browsers
$longitud_string=strlen($string_url);
if($longitud_string>500)
{
	die('Too many classes to be payed. Error 46210. Contact webmaster.');
}


//encriptamos para meterlo en herramienta de pago y lo recuperamos en la pantalla de exito despues del pago
$encoded_classes = base64_encode(openssl_encrypt($list_classes_to_encode, 'AES-256-CBC', 'Vm95YWNyZWFydW5hQ2w0dmVwNHJhNHBhc2FybG9zZTNudG9z'));
$encoded_total_amount = base64_encode(openssl_encrypt($amount_of_classes_pending_of_payment, 'AES-256-CBC', 'Vm95YWNyZWFydW5hQ2w0dmVwNHJhNHBhc2FybG9zZTNudG9z'));



//$destination_page="./studentpaymentmultiple.php?".'ids='.$encoded_classes.'&am='.$encoded_total_amount;

/*

<br><br>
<a href="<?php echo "$destination_page"; ?> " > Pay all the classes below at once</a>
<br>

<br>

*/	

$itemid1='multiple_payment';  //aqui pasamos la información para que la página de success no redirija con header location a la que corresponda
							//seria un itemID para cada producto para que podamos identificarlo
$itemname1="Multiple classes";
$productname1="Multiple classes";
$itemdescription1="Multiple classes pending of payment"; //lo usaremos tambien como product description
$internalcodename1="$encoded_classes|||$encoded_total_amount";   //pasamos los parametros para la pagina de success
$amountprice1=$amount_of_classes_pending_of_payment;


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


   <br>
<br><br>
<br>
</form>

