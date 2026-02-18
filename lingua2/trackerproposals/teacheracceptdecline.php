<?php

//coger $teacher_id de la session

session_start();
$teacher_id=$_SESSION['orden2017'];


//$teacher_id=4588;

$action=$_GET['action'];
$id_class=$_GET['trackid'];

//echo " $id_class .... $action  ";


require('../files/bd.php');

if($action!=1 AND $action!=2)
	die('Error');
	
//ponemos   AND id_user_teacher='$teacher_id'  porque si no el usuario profesor podría aceptar o borrar eventos que on son suyos
//ponemos   AND proposal_accepted_teacher=0 porque sólo se puede hacer un UPDATE de los que no se han contestado aun, es decir, con esa variable a cero
$query=" 
UPDATE tracker SET proposal_accepted_teacher='$action',	proposal_accepted_timestamp=NOW()
WHERE id_tracking='$id_class' AND id_user_teacher='$teacher_id' AND proposal_accepted_teacher=0";
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