<?php

session_start();
$student_id=$_SESSION['orden2017'];

//coger $teacher_id de la session
//$student_id=4533;


$received_codes=$_GET['codes'];

$pieces = explode("||||||||||||||", $received_codes);

$list_encoded_class=$pieces[0];
$encoded_amount=$pieces[1];



$class_id = openssl_decrypt(base64_decode($list_encoded_class), 'AES-256-CBC', 'Vm95YWNyZWFydW5hQ2w0dmVwNHJhNHBhc2FybG9zZTNudG9z');
$total_amount = openssl_decrypt(base64_decode($encoded_amount), 'AES-256-CBC', 'Vm95YWNyZWFydW5hQ2w0dmVwNHJhNHBhc2FybG9zZTNudG9z');


//echo "$class_id -- $total_amount";




//echo "$class_id ... $total_amount";


require('../files/bd.php');

//ponemos   AND id_user_student='$student_id'  porque si no el usuario estudiante podria pagar las clases que no son suyas
//ponemos   AND paid=0, para que no se pueda pagar dos veces
//ponemos 	AND proposal_accepted_teacher=2 porque solo se pueden pagar las clases aceptadas previamente por el profesor
//ponemos 	AND price_session_total tiene que coincidir con el amount $total_amount para evitar que nos hagan la pirula de alguna forma
$query=" 
UPDATE tracker SET paid='1', timestamp_paid=NOW()
WHERE id_tracking='$class_id' AND id_user_student='$student_id' AND paid=0 AND proposal_accepted_teacher=2 AND price_session_total='$total_amount' ";
$result=mysqli_query($link,$query);
$n_modified=mysqli_affected_rows($link);
if($n_modified!=1)
{ 
	die("Error 8699. Contact webmaster");
}
else
{
	die('Payment done correctly');
}

 
?>