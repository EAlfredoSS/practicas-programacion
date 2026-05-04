<?php

//coger $teacher_id de la session

session_start();
$student_id=$_SESSION['orden2017'];


$id_class=$_GET['trackid'];

//echo " $id_class .... $action  ";


require('../files/bd.php');
	

$query=" 
UPDATE tracker SET releasefunds=1, timestamp_releasefunds=NOW()
WHERE id_tracking='$id_class' AND id_user_student='$student_id' AND paid=1 AND releasefunds=0";
$result=mysqli_query($link,$query);
$n_modified=mysqli_affected_rows($link);
if($n_modified!=1)
{ 
	die("Error 8675. Contact webmaster");
}
else
{
	die('Success');
}

 
?>