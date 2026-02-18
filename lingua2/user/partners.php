<?php

require('../files/bd.php');

session_start();

$jump1=$_GET['jump'];

$identificador2017=$_SESSION['orden2017'];
//echo "$identificador2017";

$posicionpartner2017=$_GET['pos']+$jump1;

if($posicionpartner2017<0 or $posicionpartner2017=='')
	$posicionpartner2017=0;





$query77="SELECT * FROM mentor2009 WHERE orden='$identificador2017' ";
$result77=mysqli_query($link,$query77);
if(!mysqli_num_rows($result77))
		die("User unregistered 1.");
$fila77=mysqli_fetch_array($result77);

$is_teacher=$fila77['Pais'];

$latitud1=$fila77['Gpslat'];
$longitud1=$fila77['Gpslng'];

$idiomademan1=$fila77['Idiomadem1'];
$idiomademan2=$fila77['Idiomadem2'];
$idiomademan3=$fila77['Idiomadem3'];

$idiomaofrec1=$fila77['Idiomaof1'];
$idiomaofrec2=$fila77['Idiomaof2'];
$idiomaofrec3=$fila77['Idiomaof3'];


//aqui damos el valor null a los idiomas que están vacíos. si no en la query de debajo seleccionaría los usuarios que tengan, por ejemplo, m.m.Idiomaof1 vacío
if(empty($idiomademan1))
	$idiomademan1='null';
if(empty($idiomademan2))
	$idiomademan2='null';
if(empty($idiomademan3))
	$idiomademan3='null';

if(empty($idiomaofrec1))
	$idiomaofrec1='null';
if(empty($idiomaofrec2))
	$idiomaofrec2='null';
if(empty($idiomaofrec3))
	$idiomaofrec3='null';


//echo "</br>$latitud1------- $longitud1---------$idiomademan1, $idiomademan2, $idiomademan3";


//__________________________________________________________

/*
$query="
SELECT 
m.orden,

(acos(sin(radians(m.Gpslat)) * sin(radians($latitud1)) + 
cos(radians(m.Gpslat)) * cos(radians($latitud1)) * 
cos(radians(m.Gpslng) - radians($longitud1))) * 6378) 

AS distanciaPunto1Punto2

FROM mentor2009 m

WHERE orden <> '$identificador2017' AND
(
	(Idiomaof1='$idiomademan1' OR  Idiomaof1='$idiomademan2' OR Idiomaof1='$idiomademan3' OR
	Idiomaof2='$idiomademan1' OR  Idiomaof2='$idiomademan2' OR Idiomaof2='$idiomademan3' OR
	Idiomaof3='$idiomademan1' OR  Idiomaof3='$idiomademan2' OR Idiomaof3='$idiomademan3'
	) 
OR
Pais='teacher'
)


ORDER BY distanciaPunto1Punto2 

LIMIT $posicionpartner2017,1


";
*/

if($is_teacher!='teacher')
{

	$query="
	SELECT 
	m.orden,

	(acos(sin(radians(m.Gpslat)) * sin(radians($latitud1)) + 
	cos(radians(m.Gpslat)) * cos(radians($latitud1)) * 
	cos(radians(m.Gpslng) - radians($longitud1))) * 6378) 

	AS distanciaPunto1Punto2

	FROM mentor2009 m

	WHERE orden <> '$identificador2017' AND

		(Idiomaof1='$idiomademan1' OR  Idiomaof1='$idiomademan2' OR Idiomaof1='$idiomademan3' OR
		Idiomaof2='$idiomademan1' OR  Idiomaof2='$idiomademan2' OR Idiomaof2='$idiomademan3' OR
		Idiomaof3='$idiomademan1' OR  Idiomaof3='$idiomademan2' OR Idiomaof3='$idiomademan3'
		) 
		



	ORDER BY distanciaPunto1Punto2 

	LIMIT $posicionpartner2017,1

	";
}
else
{
	
$query="
	SELECT 
	m.orden,

	(acos(sin(radians(m.Gpslat)) * sin(radians($latitud1)) + 
	cos(radians(m.Gpslat)) * cos(radians($latitud1)) * 
	cos(radians(m.Gpslng) - radians($longitud1))) * 6378) 

	AS distanciaPunto1Punto2

	FROM mentor2009 m

	WHERE orden <> '$identificador2017' AND

		(Idiomadem1='$idiomaofrec1' OR  Idiomadem1='$idiomaofrec2' OR Idiomadem1='$idiomaofrec3' OR
		Idiomadem2='$idiomaofrec1' OR  Idiomadem2='$idiomaofrec2' OR Idiomadem2='$idiomaofrec3' OR
		Idiomadem3='$idiomaofrec1' OR  Idiomadem3='$idiomaofrec2' OR Idiomadem3='$idiomaofrec3'
		) 


		AND
		Pais<>'teacher'

	ORDER BY distanciaPunto1Punto2 

	LIMIT $posicionpartner2017,1

	";
	
}




$result=mysqli_query($link,$query);
if(!mysqli_num_rows($result))
		die("</br>There aren't more results for your languages.");
$fila=mysqli_fetch_array($result);



$orden99= $fila['orden'];
$distancia99= round($fila['distanciaPunto1Punto2'],2);

//echo " </br>---- $orden99 -------  $distancia99----</br>";

//echo mysqli_num_rows($result);



if($posicionpartner2017<=0 or $posicionpartner2017=='')
	$posicionpartner2017=1;


//header("Location: ../user/u.php?dst=$distancia99&identificador=$orden99&pos=$posicionpartner2017");
//header("Location: ../u.php?dst=$distancia99&identificador=$orden99&pos=$posicionpartner2017");
	echo "<html><script>window.location.href='./u.php?dst=$distancia99&identificador=$orden99&pos=$posicionpartner2017';</script></html>";

die('header location not working. Error 332. Write webmaster@lingua2.eu ');
?>





