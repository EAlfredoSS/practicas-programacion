<?php

//$list_encoded_classes=$_GET['ids'];
//$encoded_total_amount=$_GET['am'];


$received_codes=$_GET['codmul']; 

$pieces = explode("|||", $received_codes);

$list_encoded_classes=$pieces[0];
$encoded_total_amount=$pieces[1];



$classes_to_pay=array();
$classes_string = openssl_decrypt(base64_decode($list_encoded_classes), 'AES-256-CBC', 'Vm95YWNyZWFydW5hQ2w0dmVwNHJhNHBhc2FybG9zZTNudG9z');
$classes_to_pay = explode("|||", $classes_string);

//print_r($classes_to_pay);

$num_classes=count($classes_to_pay);

$string_where='';
for($jjj=0;$jjj<$num_classes;$jjj++)
{
	$string_where.="id_tracking='";
	$string_where.=$classes_to_pay[$jjj];
	$string_where.="'";
	if($jjj!=$num_classes-1)
	{
		$string_where.=" OR ";
	}
}

//die("$string_where");

$total_amount = openssl_decrypt(base64_decode($encoded_total_amount), 'AES-256-CBC', 'Vm95YWNyZWFydW5hQ2w0dmVwNHJhNHBhc2FybG9zZTNudG9z');
//echo " $total_amount";

require('../files/bd.php');


// aqui miramos que la suma total de las clases coincida
// en el WHERE metemos todos los cursos a pagar
$query43="
SELECT SUM(price_session_total) AS sum_price
FROM tracker
WHERE $string_where
";

$result43 = mysqli_query($link, $query43);
$nuevos43=mysqli_num_rows($result43);

if (!$nuevos43)
        die("Error 8631. Contact webmaster.");
	
	
$fila43=mysqli_fetch_array($result43);
$suma_de_precios=$fila43['sum_price'];	

//die("$total_amount  ----  $suma_de_precios ");

//if the price passed by url and the calculated price from the data base differ, the error
if($total_amount!=$suma_de_precios )
{
	die("Error 1631. Contact webmaster.");
}


//ponemos paid=0 porque si no, si ejecutamos dos veces seguidas la página, se sigue haciendo el update y no salta un error
$query=" 
UPDATE tracker SET paid='1', timestamp_paid=NOW()
WHERE ($string_where) AND paid=0";
$result=mysqli_query($link,$query);
$n_modified=mysqli_affected_rows($link);
if($n_modified==0)
{ 
	die("Error 2759. Contact webmaster.");
}
else
{
	die('Payment done correctly');
}



?> 