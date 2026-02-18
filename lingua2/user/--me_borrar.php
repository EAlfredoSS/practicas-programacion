<?php
//hay que pasar la variable identificador del usuario consultado

require('../files/bd.php');
session_start();


/*
//-- Vemos si tenemos integrado facebook y facebook esta abierto



if( !empty($_SESSION['codigolingua2']) )
{
	$query="SELECT * FROM mentor2009 WHERE Codigoborrar='".$_SESSION['codigolingua2']."'"; //seleccionamos todos los campos 
	$result=mysql_query($query,$link);
	if(!mysql_num_rows($result))
		die("Error 4. Integration with Facebook wrong. Write email to webmaster@lingua2.eu");
	$fila=mysql_fetch_array($result);
	$nombre_usu_conectado=$fila['nombre'];
	$necesario_introducir_codigo=0;
}
else{	$necesario_introducir_codigo=1;	}


//--------------------------------------------------------------
*/

$identificador2017=$_SESSION['orden2017'];
$_SESSION['idusuario2019']=$identificador2017; //esto es solo por si hay el usuario quiere cambiar la foto de perfil
//mirar que no est� el nick repetido

$query="SELECT * FROM mentor2009 WHERE orden='".$identificador2017."'"; //seleccionamos todos los campos 

$result=mysqli_query($link, $query);
if(!mysqli_num_rows($result))
		die("User unregistered. <a href=\"http://www.lingua2.eu\">Information</a>");
$fila=mysqli_fetch_array($result);
$ciudad1=$fila['Ciudad'];
$id_del_receptor=$fila['orden'];

$gpslat11=$fila['Gpslat'];
$gpslng11=$fila['Gpslng'];

$email_del_usu=$fila['Email'];
$em=$email_del_usu;


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

$thumb_nombre=$fila['orden']; 

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
/*else if($fb_ident)  {
	$foto_nombre="../uploader/fb_temp_pics/$foto_nombre.jpg"; 
	copy("https://graph.facebook.com/$fb_ident/picture?type=large","$foto_nombre");	
	$img_properties="style=\"border-style: solid; border-color:red; border-width:1px;\" ";
} */		
else

{	$foto_nombre="../uploader/default.jpg"; }


//thumb

$jpg_name="../uploader/upload_pic/thumb_$thumb_nombre.jpg";
$png_name="../uploader/upload_pic/thumb_$thumb_nombre.png";
$gif_name="../uploader/upload_pic/thumb_$thumb_nombre.gif";
$bmp_name="../uploader/upload_pic/thumb_$thumb_nombre.bmp";

if (file_exists($jpg_name) ){
	$thumb_nombre=$jpg_name;}
else if (file_exists($png_name) ){
	$thumb_nombre=$png_name;}
else if(file_exists($gif_name)){
	$thumb_nombre=$gif_name;}
else if(file_exists($bmp_name) ) {
	$thumb_nombre=$bmp_name; }

else

{	$thumb_nombre="../uploader/default.jpg"; }


$nombre_usuar=$fila["nombre"]; 
				
$myvalue = $nombre_usuar;
$arr = explode(' ',trim($myvalue));
$nombre_usuar=$arr[0];

$nombre_usuar=ucfirst(substr($nombre_usuar,0,13)); 




/*
$idof1=$fila['Idiomaof1'];$idof2=$fila['Idiomaof2'];$idof3=$fila['Idiomaof3'];$idof4=$fila['Idiomaof4'];$idofextra=$fila['Idiomaextraofrecido'];
$idde1=$fila['Idiomadem1'];$idde2=$fila['Idiomadem2'];$idde3=$fila['Idiomadem3'];$idde4=$fila['Idiomadem4'];$iddeextra=$fila['Idiomaextrademandado'];
$idiomas_ofrecidos="$idof1".' '."$idof2".' '."$idof3".' '."$idof4"; 
$idiomas_demandados="$idde1".' '."$idde2".' '."$idde3".' '."$idde4"; 
*/

?>


<!DOCTYPE HTML>
<html>
<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-139626327-1"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('js', new Date());
            gtag('config', 'UA-139626327-1');
        </script>
<head>
<script src="https://kit.fontawesome.com/bb6243346a.js" crossorigin="anonymous"></script>
<title>Language Exchange | Lingua2</title>
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


 <!-- esto es para el botón con la foto y con el desplegable -->
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>





</head>
<body>
<!--profile start here-->


<script>
if (window.innerWidth < 1360) {
    alert('Less');

</script>

<!--NUEVO PERFIL USUARIO -->
<header id="header_perfil_usuario">
<nav class="horizontal">
        <div class="logo">
            <a href=""><img src="../images/logo_orange-150px.png" alt="Logo Lingua2" height="50%" width="50%"></a>
        </div>
        <div class="menu">
            <ul>
			

                <li style="font-size: 12px;font-weight: bold; color: #e65f00">
                   People search 
				<?php //   <a href="./partners.php"><img src="../images/buscar-usuarios.png" alt="Buscar Usuarios" width="15px"></a> ?>
					
				<a href="./partners.php"><i class="far fa-address-card" aria-hidden="true" style="font-size: 15px;" ></i></a>


			<?php //////////////////////////////////////////// AQUI NOS DICEN CUANTOS MENSAJES NO LEIDOS TENEMOS ////////////////////////////////////////////
			/* require('../pms/cpm.class.php');
			$ur_pm = new cpm($ur_userid);
			$ur_new_msg = $ur_pm->getunreadmessages();  */
			
			$row3=0;
			
			$userid=$identificador2017;
			
			$sql3 = "SELECT count(*) FROM messages WHERE `to` = '".$userid."' && `to_viewed` = '0' && `to_deleted` = '0' ORDER BY `created` DESC";
			$result3 = mysqli_query($link,$sql3);
			//$row3 = mysqli_num_rows($result3);
			
			$fila3=mysqli_fetch_array($result3); 
			
			//print_r( $fila3); 
			
			//die($sql3);
			
			
			//////////////////////////////////////////// FIN CUANTOS MENSAJES NO LEIDOS TENEMOS ////////////////////////////////////////////?>

                </li>
                <li style="font-size: 12px;font-weight: bold; color: #e65f00">
                   Mail (<?php	echo $fila3[0]; ?>)  <a href="../pms"><i class="far fa-envelope" aria-hidden="true" style="font-size: 15px;" ></i></a>
                </li>
				
			<?php 	// estas queries las he copiado del archivo  ..infouser/pendingactions.php
				
				
				$query_vote="SELECT * FROM couples2009antiguos WHERE (voted_1=0 AND id_1='$em') AND contactado=1 ";
				$result_vote=mysqli_query($link,$query_vote);
				$nuevos_vote1=mysqli_num_rows($result_vote);
				

				
				$query_vote="SELECT * FROM couples2009antiguos WHERE (voted_2=0 AND id_2='$em') AND (contactado=0 OR contactado=1) ";
				$result_vote=mysqli_query($link,$query_vote);
				$nuevos_vote2=mysqli_num_rows($result_vote);
				

				
				$nuevos_vote_total=$nuevos_vote1+$nuevos_vote2;

				
				?>
				
                <li style="font-size: 12px;font-weight: bold; color: #e65f00">
                   Pending actions (<?php echo $nuevos_vote_total; ?>) <a href="../infouser/pendingactions.php"><i class="fas fa-user-check" aria-hidden="true" style="font-size: 15px;"></i></a>
                </li>
				
				<?php	
	//echo "<br><center><a href=\"../events/showallupcomingevents.php\" style=\"color:red; font-size: 20px; font-weight: bold;  text-decoration: underline;\" >See and create events in your city</a></center>";
?>	
				
				
				<li style="font-size: 12px;font-weight: bold; color: #e65f00">
				Events in your city <a href="../events/showallupcomingevents.php"><i class="fas fa-globe-africa" aria-hidden="true" style="font-size: 15px;"></i></a>
				</li>
		  			    <li>
                    
					  <div class="dropdown">
    <button type="button" class="btn btn dropdown-toggle" data-toggle="dropdown">
  

  <?php echo $nombre_usuar;  ?>  
  

  <img src="<?php echo $thumb_nombre;  ?>" height="25px"/>

  
    </button>
    <div class="dropdown-menu">
		  <a class="dropdown-item" href="./getgpsposition.php">Update GPS location</a>
		  <a class="dropdown-item" href="../registration/uploadphoto/">Change profile photo</a>
		  <a class="dropdown-item" href="../recoveryandunregistration/deleterequest.php">Unregister</a> 
		  <a class="dropdown-item" href="#">Edit details</a>
 
			<hr>
		  <a class="dropdown-item" href="./logout.php">Log out</a>
	  
	
    </div>
  </div>
					
					
					
                </li>
		  
	  
                  
	
            </ul>
    </div>
</nav>
</header>
	<!--FIN NUEVO PERFIL USUARIO-->
	
<script>	
	}
else {
	
</script>	

 


	
	
	
	
	
	
	<!--NUEVO PERFIL USUARIO -->
<header id="header_perfil_usuario">
<nav class="horizontal">
        <div class="logo">
            
			
			
			
                    
					  <div class="dropdown">
    <button type="button" class="btn btn dropdown-toggle" data-toggle="dropdown">
  

  <?php echo $nombre_usuar;  ?>  
  

  <img src="<?php echo $thumb_nombre;  ?>" height="25px"/>

  
    </button>
    <div class="dropdown-menu">
		  <a class="dropdown-item" href="./getgpsposition.php">Update GPS location</a>
		  <a class="dropdown-item" href="../registration/uploadphoto/">Change profile photo</a>
		  <a class="dropdown-item" href="../recoveryandunregistration/deleterequest.php">Unregister</a> 
		  <a class="dropdown-item" href="#">Edit details</a>
 
			<hr>
		  <a class="dropdown-item" href="./logout.php">Log out</a>
	  
	
    </div>
  </div>
			
			
			
			
			
        </div>
        <div class="menu">
            <ul>
			

                <li style="font-size: 12px;font-weight: bold; color: #e65f00">
                   People search 
				<?php //   <a href="./partners.php"><img src="../images/buscar-usuarios.png" alt="Buscar Usuarios" width="15px"></a> ?>
					
				<a href="./partners.php"><i class="far fa-address-card" aria-hidden="true" style="font-size: 15px;" ></i></a>


			<?php //////////////////////////////////////////// AQUI NOS DICEN CUANTOS MENSAJES NO LEIDOS TENEMOS ////////////////////////////////////////////
			/* require('../pms/cpm.class.php');
			$ur_pm = new cpm($ur_userid);
			$ur_new_msg = $ur_pm->getunreadmessages();  */
			
			$row3=0;
			
			$userid=$identificador2017;
			
			$sql3 = "SELECT count(*) FROM messages WHERE `to` = '".$userid."' && `to_viewed` = '0' && `to_deleted` = '0' ORDER BY `created` DESC";
			$result3 = mysqli_query($link,$sql3);
			//$row3 = mysqli_num_rows($result3);
			
			$fila3=mysqli_fetch_array($result3); 
			
			//print_r( $fila3); 
			
			//die($sql3);
			
			
			//////////////////////////////////////////// FIN CUANTOS MENSAJES NO LEIDOS TENEMOS ////////////////////////////////////////////?>

                </li>
                <li style="font-size: 12px;font-weight: bold; color: #e65f00">
                   Mail (<?php	echo $fila3[0]; ?>)  <a href="../pms"><i class="far fa-envelope" aria-hidden="true" style="font-size: 15px;" ></i></a>
                </li>
				
			<?php 	// estas queries las he copiado del archivo  ..infouser/pendingactions.php
				
				
				$query_vote="SELECT * FROM couples2009antiguos WHERE (voted_1=0 AND id_1='$em') AND contactado=1 ";
				$result_vote=mysqli_query($link,$query_vote);
				$nuevos_vote1=mysqli_num_rows($result_vote);
				

				
				$query_vote="SELECT * FROM couples2009antiguos WHERE (voted_2=0 AND id_2='$em') AND (contactado=0 OR contactado=1) ";
				$result_vote=mysqli_query($link,$query_vote);
				$nuevos_vote2=mysqli_num_rows($result_vote);
				

				
				$nuevos_vote_total=$nuevos_vote1+$nuevos_vote2;

				
				?>
				
                <li style="font-size: 12px;font-weight: bold; color: #e65f00">
                   Pending actions (<?php echo $nuevos_vote_total; ?>) <a href="../infouser/pendingactions.php"><i class="fas fa-user-check" aria-hidden="true" style="font-size: 15px;"></i></a>
                </li>
				
				<?php	
	//echo "<br><center><a href=\"../events/showallupcomingevents.php\" style=\"color:red; font-size: 20px; font-weight: bold;  text-decoration: underline;\" >See and create events in your city</a></center>";
?>	
				
				
				<li style="font-size: 12px;font-weight: bold; color: #e65f00">
				Events in your city <a href="../events/showallupcomingevents.php"><i class="fas fa-globe-africa" aria-hidden="true" style="font-size: 15px;"></i></a>
				</li>
		  			    
					
					
					
           
		  
	  
                  
	
            </ul>
    </div>
</nav>
</header>
	<!--FIN NUEVO PERFIL USUARIO MOVIL-->
	
	
	
<script>	
	}
</script>	
	
	
	
	
	
	
	
	<!--ALERTA PARA LOS QUE NO HAN INTRODUCIDO LA UBICACIÓN-->
<?php	
if($gpslat11==0 and $gpslng11==0)
{
?>	
<div class="alert alert-danger">
  <strong>Important!</strong> Provide your location in order to continue. <strong><a href="./getgpsposition.php" style=" text-decoration: underline;"> Add location</a></strong>
</div>
<?php	
}
	?>
	
<br><br><br>	
	
			
	
	
	
<div class="profile">
	<div class="wrap">
		<div class="profile-main">

			<div class="profile-pic wthree">
				<!-- es necesario poner esta división arriba -->
			</div>

			<!--<div class="profile-ser" style="position:relative; background-color: transparent; position:relative;top:-40px;">
				<div class="profile-ser-grids no-border">
					<a href="../pms"><img src="images/home.png" alt="See your messages"></a>
					<h4>My letters</h4>
				</div>
				<div class="profile-ser-grids no-border">
					<a href="../infouser/evdone.php"><img src="images/p2.png" alt=""></a>
					<h4>My ratings</h4>
				</div>
				<div class="profile-ser-grids no-border">
					<a href="./partners.php"><img src="images/coffee.png" alt=""></a>
					<h4>My matches</h4>
				</div>
				<div class="clear"> </div>-->
			</div>			

<!--
			<div class="active w3"> 
			</div> -->
			
			

			
		
		
			<div class="profile-pic wthree" style="position:relative;top:-100px;">
				<img src="../uploader/<?php echo $foto_nombre;?>" alt="">
				<h2 style="color:black"><? //echo substr($fila["nombre"],0,13); ?>
				<?php     
				echo $nombre_usuar; 
				?>
				
				<!--,--> <?php // echo $fila['Edad']; ?></h2>
				
				<p>
				<?php 
				
				
				//si nunca se le configuraron las coordenadas gps le sacamos un botón
				if($gpslat11==0 and $gpslng11==0)
				{
						//echo "<br><center><a href=\"./getgpsposition.php\" style=\"color:red; font-size: 12px; font-weight: bold;\" >Add your location</a></center>";
				}
				else
				{
						echo $fila['Ciudad']; 
				}
				
				?></p>
			</div>
			
			
		
			
						
			<div style="text-align:center;position:relative;top:-100px;">I am able to teach</div>
			
			
			
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
			
<?php  if ($tipo_form!="teacher")
		{
		
	?>
	
	
	<div style="text-align:center;position:relative;top:-100px;">I want to learn</div>
	
	
	
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
			
			<? 		} else {
	
echo "<div style=\"font-size:30px;text-align:center;position:relative;top:-80px;height: 90px;\">$idiomas_demandados</div>";

}


?>

			
			<div class="active w3" style="position:relative;top:-100px;">
			
	<?php		
			$query1="
		SELECT  * 
		FROM comentarios 
		WHERE (aludido='$email_del_usu') AND censurado=0 ORDER BY horacreacion DESC ";
		$result1=mysqli_query($link,$query1);
		$n_comentarios=mysqli_num_rows($result1);
		//if($n_comentarios)
		//{ ?>
			<h4><a href="../infouser/evdone.php" ><?php echo "$n_comentarios"; ?> evaluations received </a></h4> </br>  
		<?php
		//} 
		?>
			
			
				
				
				
				
				
				<p align="justify" style="font-size:13px"><?php echo $fila['Disponibilidadcomentarios']; ?> ... </p>
				<p align="justify" style="font-size:13px"><?php echo $fila['Otroscomentarios']; ?> ...</p>
			</div>
			



		</div>
	</div>
</div>
<!--profile end here-->
<!--copy rights end here-->
<div class="copy-rights" style="position:relative;top:0px;">		 	
	<p>Lingua2&copy; <a href="http://www.lingua2.com" target="_blank">Language Exchange</a> </p>	

	</br></br></br>
</div>
<!--copyrights start here-->

</body>
</html>