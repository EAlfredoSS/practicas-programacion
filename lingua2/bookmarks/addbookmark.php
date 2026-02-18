<?php

session_start();

require('../files/bd.php');

$userid_del_es_guardado=$_GET['idfav'];
$userid_del_que_guarda = $_SESSION['orden2017'];

if(is_null($userid_del_es_guardado) OR is_null($userid_del_que_guarda))
{
	die('Error 5683. Log in again.');
}



//mirar si existe la combinacion en bookmarkusers
$query="SELECT * FROM bookmarkedusers WHERE userwhosaves='$userid_del_que_guarda' AND userwhoissaved='$userid_del_es_guardado' "; 
$result=mysqli_query($link,$query);
$nuevos=mysqli_num_rows($result);
if(!$nuevos)
{
		$query172=
	"INSERT INTO bookmarkedusers (userwhosaves, userwhoissaved)
	VALUES ($userid_del_que_guarda, $userid_del_es_guardado)";

	//die("$query172");

	mysqli_query($link, $query172);

	/*
	// esto de debajo no lo ponemos porque si ya estaba en favoritos, al no poder repetirse, daria el error

	if (!mysqli_query($link, $query172)) 
	{
				echo "Error 4555. Contact webmaster.";
	}
	*/
}
else
{
	die('User was added as favourite previously');
}



?>