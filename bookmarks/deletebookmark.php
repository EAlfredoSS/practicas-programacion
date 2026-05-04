<?php

session_start();

require('../files/bd.php');

$userid_del_es_guardado=$_GET['idfav'];
$userid_del_que_guarda = $_SESSION['orden2017'];

if(is_null($userid_del_es_guardado) OR is_null($userid_del_que_guarda))
{
	die('Error 5689. Log in again.');
}



//mirar si existe la combinacion en bookmarkusers
$query="SELECT * FROM bookmarkedusers WHERE userwhosaves='$userid_del_que_guarda' AND userwhoissaved='$userid_del_es_guardado' "; 
$result=mysqli_query($link,$query);
$nuevos=mysqli_num_rows($result);
if($nuevos)
{
	
	$query6="DELETE FROM bookmarkedusers WHERE userwhosaves=$userid_del_que_guarda AND userwhoissaved=$userid_del_es_guardado";
	$result6=mysqli_query($link,$query6);
	$n_deleted=mysqli_affected_rows($link);
	
	echo "$n_deleted users deleted";
		
}
else
{
	die('Not possible to delete');
}



?>