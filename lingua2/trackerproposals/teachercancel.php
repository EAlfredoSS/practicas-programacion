<?php


session_start();
$teacher_id=$_SESSION['orden2017'];




//coger $teacher_id de la session
//$teacher_id=4588;

$id_class=$_GET['trackid'];


require('../files/bd.php');

	
//ponemos   AND id_user_teacher='$teacher_id'  porque si no el usuario profesor podría cancelar eventos que no son suyos
//ponemos   AND paid=0 porque no se pueden cancelar clases ya pagadas


$query=" 
UPDATE tracker SET cancelled=1
WHERE id_tracking='$id_class' AND id_user_teacher='$teacher_id' AND cancelled=0 AND paid=0";
$result=mysqli_query($link,$query);
$n_modified=mysqli_affected_rows($link);
if($n_modified!=1)
{ 
	die("Error 8679. Contact webmaster");
}
else
{
	die('Cancellation success');
}

 
?>