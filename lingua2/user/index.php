<?php
//hay que pasar la variable identificador del usuario consultado

require('../files/bd.php');


//-- Vemos si tenemos integrado facebook y facebook esta abierto

session_start();

if( !empty($_SESSION['codigolingua2']) )
{
	$query="SELECT * FROM mentor2009 WHERE Codigoborrar='".$_SESSION['codigolingua2']."'"; //seleccionamos todos los campos 
	//$result=mysql_query($query,$link);
	$result=mysqli_query($link, $query);
	if(!mysqli_num_rows($result))
		die("Error 4. Integration with Facebook wrong. Write email to webmaster@lingua2.eu");
	$fila=mysqli_fetch_array($result);
	$nombre_usu_conectado=$fila['nombre'];
	$necesario_introducir_codigo=0;
}
else{	$necesario_introducir_codigo=1;	}


//--------------------------------------------------------------

//mirar que no est� el nick repetido

$query="SELECT * FROM mentor2009 WHERE orden='".$_GET['identificador']."'"; //seleccionamos todos los campos 
$result=mysqli_query($link, $query);
if(!mysqli_num_rows($result))
		die("User unregistered. <a href=\"http://www.lingua2.eu\">Information</a>");
$fila=mysqli_fetch_array($result);
$ciudad1=$fila['Ciudad'];
$id_del_receptor=$fila['orden'];


require('../files/idiomasequivalencias.php');

$tipo_form=$fila['Pais'];
$teacher_price=$fila['teacherprice'];

 $idof1a=$fila['Idiomaof1'];$idof1=$idiomas_equiv["$idof1a"];
$idof2a=$fila['Idiomaof2'];$idof2=$idiomas_equiv["$idof2a"];
$idof3a=$fila['Idiomaof3'];$idof3=$idiomas_equiv["$idof3a"];
//$idof4a=$fila['Idiomaof4'];
//$idiomaextraof=$fila['Idiomaextraofrecido'];

$idde1a=$fila['Idiomadem1']; $idde1=$idiomas_equiv["$idde1a"];
$idde2a=$fila['Idiomadem2']; $idde2=$idiomas_equiv["$idde2a"];
$idde3a=$fila['Idiomadem3']; $idde3=$idiomas_equiv["$idde3a"]; 
//$idde4=$fila['Idiomadem4'];
//$idiomaextradem=$fila['Idiomaextrademandado'];

$idiomas_ofrecidos="$idof1".' '."$idof2".' '."$idof3";
// $idiomas_ofrecidos="$idof1".' '."$idof2".' '."$idof3".' '."$idof4".' '."$idiomaextraof";
$idiomas_demandados="$idde1".' '."$idde2".' '."$idde3";
// $idiomas_demandados="$idde1".' '."$idde2".' '."$idde3".' '."$idde4".' '."$idiomaextradem";
if ($tipo_form=="teacher")
{
	$idiomas_demandados="$teacher_price".' '."&#8364;/hour"; 
}

$foto_nombre=$fila['orden']; 
$fb_ident=$fila['fbid']; 

$jpg_name="../uploader/upload_pic/$foto_nombre.jpg";
$png_name="../uploader/upload_pic/$foto_nombre.png";
$gif_name="../uploader/upload_pic/$foto_nombre.gif";
$bmp_name="../uploader/upload_pic/$foto_nombre.bmp";

if (file_exists($jpg_name) ){
	$foto_nombre=$jpg_name;}
else if (file_exists($png_name) ){
	$foto_nombre=$png_name;}
else if(file_exists($gif_name)){
	$foto_nombre=$gif_name;}
else if(file_exists($bmp_name) ) {
	$foto_nombre=$bmp_name; }

//sacamos la foto de facebook si no ha subido ninguna
else if($fb_ident)  {
	$foto_nombre="../uploader/fb_temp_pics/$foto_nombre.jpg"; 
	copy("https://graph.facebook.com/$fb_ident/picture?type=large","$foto_nombre");	
	$img_properties="style=\"border-style: solid; border-color:red; border-width:1px;\" ";
} 		
else

{	$foto_nombre="../uploader/default.jpg"; }

/*
$idof1=$fila['Idiomaof1'];$idof2=$fila['Idiomaof2'];$idof3=$fila['Idiomaof3'];$idof4=$fila['Idiomaof4'];$idofextra=$fila['Idiomaextraofrecido'];
$idde1=$fila['Idiomadem1'];$idde2=$fila['Idiomadem2'];$idde3=$fila['Idiomadem3'];$idde4=$fila['Idiomadem4'];$iddeextra=$fila['Idiomaextrademandado'];
$idiomas_ofrecidos="$idof1".' '."$idof2".' '."$idof3".' '."$idof4"; 
$idiomas_demandados="$idde1".' '."$idde2".' '."$idde3".' '."$idde4"; 
*/

?>


<!DOCTYPE HTML>
<html>
<head>
<title>Language Exchange</title>
<!-- Custom Theme files -->
<link href="css/style.css" rel="stylesheet" type="text/css" media="all"/>
<link rel="stylesheet" href="./css/languages.css" media="all" />
<!-- for-mobile-apps -->
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" /> 
<meta name="keywords" content="language exchange, conversation exchange" />
<!-- //for-mobile-apps -->

<!--Google Fonts-->
<link href='//fonts.googleapis.com/css?family=Gudea:400,700' rel='stylesheet' type='text/css'>
</head>
<body>
<!--profile start here-->
<h1>Language exchange</h1>
<div class="profile">
	<div class="wrap">
		<div class="profile-main">

			<div class="profile-pic wthree">
				<!-- es necesario poner esta división arriba -->
			</div>

			<div class="profile-ser" style="position:relative; background-color: transparent; position:relative;top:-40px;">
				<div class="profile-ser-grids no-border">
					<img src="images/home.png" alt="">
					<h4>My letters</h4>
				</div>
				<div class="profile-ser-grids no-border">
					<h4> </h4>
				</div>
				<div class="profile-ser-grids no-border">
					<img src="images/settings.png" alt="">
					<h4>My settings</h4>
				</div>
				<div class="clear"> </div>
			</div>			

			<div class="active w3"> <!--separador-->
			</div>
			
			<div class="profile-ser" style="position:relative;top:-100px;">
				<div class="profile-ser-grids">
					<img src="images/msg.png" alt="">
					<!-- <h4>1086</h4> -->
				</div>
				<div class="profile-ser-grids agileinfo">
					<img src="images/p2.png" alt="">
				</div>
				<div class="profile-ser-grids no-border">
					<img src="images/p3.png" alt="">
				</div>
				<div class="clear"> </div>
			</div>
			
		
			<div class="profile-pic wthree" style="position:relative;top:-100px;">
				<img src="images/p1.png" alt="">
				<h2><? echo $fila['nombre'];?>, 34</h2>
				<p>20km away, Barcelona</p>
			</div>
			
			<div class="profile-ser" style="background-color: transparent; position:relative;top:-100px;">
				<div class="profile-ser-grids no-border">
					<img class="language language-<?php echo  $idof1a; ?> "/>
					<!--<h4>C2</h4>-->
				</div>
				<div class="profile-ser-grids no-border">
					<img class="language language-<?php echo  $idof2a; ?>"/>
				</div>
				<div class="profile-ser-grids no-border">
					<img class="language language-<?php echo  $idof3a; ?>"/>
				</div>
				<div class="clear"> </div>
			</div>
			
			<div class="profile-ser" style="background-color: transparent; position:relative;top:-100px;">
				<div class="profile-ser-grids no-border">
					<img class="language language-<?php echo  $idde1a; ?>"/>
					<!--<h4>B2</h4>-->
				</div>
				<div class="profile-ser-grids no-border">
					<img class="language language-<?php echo  $idde2a; ?>"/>
				</div>
				<div class="profile-ser-grids no-border">
					<img class="language language-<?php echo  $idde3a; ?>"/>
				</div>
				<div class="clear"> </div>
			</div>
			
			<div class="active w3" style="position:relative;top:-100px;">
				<h4>YYY evaluations : 85% positive</h4>
				<p>I am available during the weekend and...</p>
				<p>My level of Spanish is native and the...</p>
			</div>
			<div class="profile-ser" style="position:relative;top:-100px;">
				<div class="profile-ser-grids">
					<img src="images/msg.png" alt="">
					<!-- <h4>1086</h4> -->
				</div>
				<div class="profile-ser-grids agileinfo">
					<img src="images/p2.png" alt="">
				</div>
				<div class="profile-ser-grids no-border">
					<img src="images/p3.png" alt="">
				</div>
				<div class="clear"> </div>
			</div>
			
			<div class="active w3" style="position:relative;top:-100px;"><h4>Answers in time : 44%</h4></div>
			
			
			<div class="profile-follows" style="position:relative;top:-120px;">
				<!--<ul>
					<li><a href="#" class="fa"> </a> </li>
					<li><a href="#" class="tw"> </a> </li>
				</ul>-->
			<textarea rows="5" cols="30" wrap="soft"> </textarea>
				<div class="follow-btn" style="position:relative;top:-20px;">
					<a href="#">Send message</a>
				</div>
			</div>
		</div>
	</div>
</div>
<!--profile end here-->
<!--copy rights end here-->
<div class="copy-rights">		 	
	<p>Lingua2© <a href="http://www.lingua2.com" target="_blank">Lingua2</a> </p>		 	
</div>
<!--copyrights start here-->

</body>
</html>