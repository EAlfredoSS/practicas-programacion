<?php
require('../files/bd.php');
session_start();

//$codigo_contactante=$_POST["code_sender"];
$codigo_contactante=$_SESSION['codigoborrar2017'];
$codigo_contactante=trim($codigo_contactante); //quitamos caracteres en blanco al principio y final 

	
$identificador_contactado=$_GET['id_receptor'];

//die("------------ $identificador_contactado  ----------");


if( empty( $codigo_contactante ) OR !isset($codigo_contactante) )
	die('Error 10.');
if( empty( $identificador_contactado ) )
	die('Error 11');





if( !empty( $_POST['enviar'] )	) 
{
	
	$message_usu=$_POST['mensajedelusuario'];
	
	//paramentros pasados
	//$message_from_user=$message_usu;
	
	//$message_from_user=htmlentities($message_usu, ENT_QUOTES, "UTF-8");  //decimos que lo que escribe el usu es utf-8
	
	///le quitamos los emails para que no salgan de lingua2
	
	function quitaremails($string)
	{
		$pattern = "/[^@\s]*@[^@\s]*\.[^@\s]*/";
		$replacement = " ";
		$output_string= preg_replace($pattern, $replacement, $string);

		$pattern = "/[^@\s]*(at)[^@\s]*\.[^@\s]*/";
		$replacement = " ";
		$output_string=  preg_replace($pattern, $replacement, $output_string);

		$pattern = "/yahoo/";
		$replacement = " ";
		$output_string=  preg_replace($pattern, $replacement, $output_string);

		$pattern = "/ymail/";
		$replacement = " ";
		$output_string=  preg_replace($pattern, $replacement, $output_string);

		$pattern = "/hotmail/";
		$replacement = " ";
		$output_string=  preg_replace($pattern, $replacement, $output_string);

		$pattern = "/gmail/";
		$replacement = " ";
		return preg_replace($pattern, $replacement, $output_string);
	}

	$message_from_user= quitaremails($message_usu);
	
	$message_from_user=htmlentities($message_from_user, ENT_QUOTES, "UTF-8");  //decimos que lo que escribe el usu es utf-8 
	
	////////////////////////////////////////////////////////7
	

		
//	require('../files/bd.php');
	
	//datos del contactante
	$query1="SELECT * FROM mentor2009 WHERE Codigoborrar='$codigo_contactante'"; 
	$result1=mysqli_query($link,$query1);
	$n_filas1=mysqli_num_rows($result1);
	if( $n_filas1 )
	{	
		$fila1=mysqli_fetch_array($result1);
		$nombre_contactante=$fila1['nombre'];
		$id_contactante=$fila1['orden'];
		$email_contactante=$fila1['Email'];
		
		
		$jpg_name="uploader/upload_pic/thumb_$id_contactante.jpg";
		$png_name="uploader/upload_pic/thumb_$id_contactante.png";
		$gif_name="uploader/upload_pic/thumb_$id_contactante.gif";
		$bmp_name="uploader/upload_pic/thumb_$id_contactante.bmp";
		
		//default
		$foto_nombre='uploader/default.jpg'; 
		
		if (file_exists($jpg_name) ){
			$foto_nombre=$jpg_name;}
		else if (file_exists($png_name) ){
			$foto_nombre=$png_name;}
		else if(file_exists($gif_name)){
			$foto_nombre=$gif_name;}
		else if(file_exists($bmp_name) ) {
			$foto_nombre=$bmp_name; }
			

		$foto_nombre='../'.$foto_nombre;
		//die($foto_nombre);
			
		
		//echo "$id_contactante - $nombre_contactante - $email_contactante <br>";
	}
	else
	{
		die('Error 1. Incorrect code. Do not forget to remove the space characters when you insert the code. <br> If you do not solve it report it to webmaster@lingua2.com');
	}
	
	//datos del contactado
	$query1="SELECT * FROM mentor2009 WHERE orden='$identificador_contactado'";
	$result1=mysqli_query($link,$query1);
	if( mysqli_num_rows($result1))
	{	
		$fila1=mysqli_fetch_array($result1); 
		$nombre_contactado=$fila1['nombre'];
		$email_contactado=$fila1['Email'];
		
		//echo "$nombre_contactado - $email_contactado <br><br>";
	}
	else
	{
		die('Error 2. If you do not solve it report it to webmaster@lingua2.com');
	}
	
	//condicion1: no sea el mismo que se elige a si mismo
	if ($identificador_contactado==$id_contactante)
		die('You cannot choose yourself');
	//condicion2: que la pareja no este repetida
	$query1="SELECT * FROM couples2009antiguos WHERE (user_id_1='$id_contactante' AND user_id_2='$identificador_contactado') OR (user_id_2='$id_contactante' AND user_id_1='$identificador_contactado') ";
	$result1=mysqli_query($link,$query1);
	if( mysqli_num_rows($result1))
	{
		die('You selected this user before or viceversa. If you want to send him or her another message go to your Personal Area and open your mailbox. Then send him a message.');
	}
	//condicion3: que no haya contactado a mas de 30 usuarios en las 24 ultimas horas para evitar spam
	$current_time2=time();
	$tiempo_corte2=$current_time2-3600*24;
	$query9="SELECT n_pareja FROM couples2009antiguos WHERE user_id_1='$id_contactante' AND tiempocreacion>$tiempo_corte2";
	$result9=mysqli_query($link,$query9);
	if( mysqli_num_rows($result9) > 29)
	{	
		die("Your message was NOT delivered. You contacted too many users today. Try again in 24 hours.<br/><br/><br/>_____<br/>$message_from_user");
	}
	
	if(empty($nombre_contactante) OR empty($nombre_contactado) OR !isset($nombre_contactante) OR !isset($nombre_contactado) ) 
	{
		$ip33=$_SERVER["REMOTE_ADDR"];
		mail ( 'partners@languageexchanges.com' , 'nombre_contante vacio. intentando enviar emails hacker.' , "$ip33 -->ip hacker. user_card_email.php" );
		die('There was an error. Contact webmaster@lingua2.com');
	}
	
	//registramos la pareja
	$tiempo_php=time();
	$codigo_usu1=md5($tiempo_php*3+9887);
	$codigo_usu2=md5($tiempo_php*2+1234);
	
	$query7="INSERT INTO couples2009antiguos (user_id_1,user_id_2,code_1,code_2,tiempocreacion,ultimoupdate,contactado) VALUES('$id_contactante','$identificador_contactado','$codigo_usu1','$codigo_usu2',$tiempo_php,$tiempo_php,0 )";
	//die("$query7");
	$result7=mysqli_query($link,$query7);
	if(!mysqli_affected_rows($link))
		die ('Error 3. Report to webmaster@lingua2.com');  
}

//envio de email template
			
require('../emailtemplates/email.php'); 

//Send email to user containing username and password
//Read Template File 
$emailBody = readTemplateFile("../emailtemplates/templatenew_contacto.html"); 
		
//Replace all the variables in template file
$emailBody = str_replace("#receptor#",$nombre_contactado,$emailBody);
$emailBody = str_replace("#emisor#",$nombre_contactante,$emailBody);
$emailBody = str_replace("#mensajeemisor#",$message_from_user,$emailBody);
$emailBody = str_replace("#codcontacto#",$codigo_usu2,$emailBody);
$emailBody = str_replace("#idemi#",$id_contactante,$emailBody);
$emailBody = str_replace("#photoname#",$foto_nombre,$emailBody);

if( empty($nombre_contactado) OR !isset($nombre_contactado) )
	die('Error 55.');
		
//Send email 
$emailStatus = sendEmail ("Lingua2 Language Exchange", "partners@languageexchanges.com", $email_contactado, "New private message from $nombre_contactante.", $emailBody);
$emailStatus = sendEmail ("Lingua2 Language Exchange", "partners@languageexchanges.com", "partners@languageexchanges.com", "$nombre_contactado: New private message from $nombre_contactante.", $emailBody);

//Guardamos este mensaje en el correo ///////////////////////////////////////////////////////////

	//encodes messages to save them to db
	function encode($string,$key) {
		$key = sha1($key);
		$strLen = strlen($string);
		$keyLen = strlen($key);
		$j=0;
		$hash='';
		for ($i = 0; $i < $strLen; $i++) {
			$ordStr = ord(substr($string,$i,1));
			if ($j == $keyLen) { $j = 0; }
			$ordKey = ord(substr($key,$j,1));
			$j++;
			$hash .= strrev(base_convert(dechex($ordStr + $ordKey),16,36));
		}
		return $hash;
	}
	
		//el secret pas esta copiado del archivo cpm.class.php
		//this is the password to encrypr/decrypt messages to/from db. Once set it cannot be changed, as messages won't be decoded right.
		$secretPass = 'kljhflk73#OO#*U$O(*YO'; 	
		$message = encode($message_from_user,$secretPass);
		
	$query="INSERT INTO messages (`to`,`from`,`title`,`message`,`created`) VALUES('$identificador_contactado','$id_contactante','Introduction message','$message',NOW())";	
	$result=mysqli_query($link,$query);
	if(!mysqli_affected_rows($link))
		die ('error base datos. contact webmaster@lingua2.com');  
/*
	echo "$<br>$query<br>";  
	die('---'); 
*/ 
		
//FIN Guardamos este mensaje en el correo ////////////////////////////////////////////////////////	
		

//If email function return false
if ($emailStatus != 1) {
	echo "An error occured while sending email. Please try again later. Cod: $emailStatus";
} else {
	header("Location: ./corr.php?uid=$identificador_contactado");
}	


?>
