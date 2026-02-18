<?php
//hay que pasar la variable header 

session_start();

require('../templates/header_simplified.html');
//require('../templates/header.php');

require('../files/idiomasequivalencias.php');

require('../files/idiomasnivel.php');




$distanciap1p2=$_GET['dst'];
$posi2017=$_GET['pos'];
$nextposi2017=$posi2017;








//hay que pasar la variable identificador del usuario consultado

require('../files/bd.php');



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

$identificador_usu_buscado=$_GET['identificador'];


$identificador2017 = $_SESSION['orden2017'];
$_SESSION['idusuario2019'] = $identificador2017; //esto es solo por si hay el usuario quiere cambiar la foto de perfil
//mirar que no est� el nick repetido

$query = "SELECT * FROM mentor2009 WHERE orden='" . $identificador_usu_buscado . "'"; //seleccionamos todos los campos 

$result = mysqli_query($link, $query);
if (!mysqli_num_rows($result))
    die("User unregistered. <a href=\"http://www.lingua2.com\">Information</a>");
$fila = mysqli_fetch_array($result);
$ciudad1 = $fila['Ciudad'];
$id_del_receptor = $fila['orden'];

$gpslat11 = $fila['Gpslat'];
$gpslng11 = $fila['Gpslng'];

$email_del_usu = $fila['Email'];
$em = $email_del_usu;


$email_verified = $fila['Emailverif'];

$availability100=$fila['Disponibilidadcomentarios'];
$othercomments100=$fila['Otroscomentarios']; 

                                     
                                    



require('../files/idiomasequivalencias.php');

$tipo_form = $fila['Pais'];
$teacher_price = $fila['teacherprice'];

$idof1a = $fila['Idiomaof1'];
$idof1 = $idiomas_equiv["$idof1a"];
$idof2a = $fila['Idiomaof2'];
$idof2 = $idiomas_equiv["$idof2a"];
$idof3a = $fila['Idiomaof3'];
$idof3 = $idiomas_equiv["$idof3a"];
//$idof4a=$fila['Idiomaof4'];
//$idiomaextraof=$fila['Idiomaextraofrecido'];

$idde1a = $fila['Idiomadem1'];
$idde1 = $idiomas_equiv["$idde1a"];
$idde2a = $fila['Idiomadem2'];
$idde2 = $idiomas_equiv["$idde2a"];
$idde3a = $fila['Idiomadem3'];
$idde3 = $idiomas_equiv["$idde3a"];
//$idde4=$fila['Idiomadem4'];
//$idiomaextradem=$fila['Idiomaextrademandado'];

$idiomas_ofrecidos = "$idof1" . ' ' . "$idof2" . ' ' . "$idof3";
// $idiomas_ofrecidos="$idof1".' '."$idof2".' '."$idof3".' '."$idof4".' '."$idiomaextraof";
$idiomas_demandados = "$idde1" . ' ' . "$idde2" . ' ' . "$idde3";
// $idiomas_demandados="$idde1".' '."$idde2".' '."$idde3".' '."$idde4".' '."$idiomaextradem";
if ($tipo_form == "teacher") {
    $idiomas_demandados = "$teacher_price" . ' ' . "&#8364;/hour";
}







//niveles

$level_idiomademan1=$fila['Idiomadem1_level'];
$level_idiomademan2=$fila['Idiomadem2_level'];
$level_idiomademan3=$fila['Idiomadem3_level'];


// escogemos la imagen del nivel del idioma 1
switch ($level_idiomademan1) 
{
    case 0:
        $image_level_lang_dem_1='no_data.png';
        break;
    case 1:
        $image_level_lang_dem_1='zero_knowledge.png';
        break;
    case 2:
        $image_level_lang_dem_1='a1.png';
        break;
	case 3:
        $image_level_lang_dem_1='a2.png';
        break;
    case 4:
        $image_level_lang_dem_1='b1.png';
        break;
    case 5:
        $image_level_lang_dem_1='b2.png';
        break;
    case 6:
        $image_level_lang_dem_1='c1.png';
        break;
    case 7:
        $image_level_lang_dem_1='c2.png';
        break;
}


// escogemos la imagen del nivel del idioma 2
switch ($level_idiomademan2) 
{
    case 0:
        $image_level_lang_dem_2='no_data.png';
        break;
    case 1:
        $image_level_lang_dem_2='zero_knowledge.png';
        break;
    case 2:
        $image_level_lang_dem_2='a1.png';
        break;
	case 3:
        $image_level_lang_dem_2='a2.png';
        break;
    case 4:
        $image_level_lang_dem_2='b1.png';
        break;
    case 5:
        $image_level_lang_dem_2='b2.png';
        break;
    case 6:
        $image_level_lang_dem_2='c1.png';
        break;
    case 7:
        $image_level_lang_dem_2='c2.png';
        break;
}


// escogemos la imagen del nivel del idioma 3
switch ($level_idiomademan3) 
{
    case 0:
        $image_level_lang_dem_3='no_data.png';
        break;
    case 1:
        $image_level_lang_dem_3='zero_knowledge.png';
        break;
    case 2:
        $image_level_lang_dem_3='a1.png';
        break;
	case 3:
        $image_level_lang_dem_3='a2.png';
        break;
    case 4:
        $image_level_lang_dem_3='b1.png';
        break;
    case 5:
        $image_level_lang_dem_3='b2.png';
        break;
    case 6:
        $image_level_lang_dem_3='c1.png';
        break;
    case 7:
        $image_level_lang_dem_3='c2.png';
        break;
}











$foto_nombre = $fila['orden'];

$thumb_nombre = $fila['orden'];

$fb_ident = $fila['fbid'];

$jpg_name = "../uploader/upload_pic/$foto_nombre.jpg";
$png_name = "../uploader/upload_pic/$foto_nombre.png";
$gif_name = "../uploader/upload_pic/$foto_nombre.gif";
$bmp_name = "../uploader/upload_pic/$foto_nombre.bmp";

if (file_exists($jpg_name)) {
    $foto_nombre = $jpg_name;
} else if (file_exists($png_name)) {
    $foto_nombre = $png_name;
} else if (file_exists($gif_name)) {
    $foto_nombre = $gif_name;
} else if (file_exists($bmp_name)) {
    $foto_nombre = $bmp_name;
}

//sacamos la foto de facebook si no ha subido ninguna
/*else if($fb_ident)  {
	$foto_nombre="../uploader/fb_temp_pics/$foto_nombre.jpg"; 
	copy("https://graph.facebook.com/$fb_ident/picture?type=large","$foto_nombre");	
	$img_properties="style=\"border-style: solid; border-color:red; border-width:1px;\" ";
} */ else {
    $foto_nombre = "../uploader/default.jpg";
}


//thumb

$jpg_name = "../uploader/upload_pic/thumb_$thumb_nombre.jpg";
$png_name = "../uploader/upload_pic/thumb_$thumb_nombre.png";
$gif_name = "../uploader/upload_pic/thumb_$thumb_nombre.gif";
$bmp_name = "../uploader/upload_pic/thumb_$thumb_nombre.bmp";

if (file_exists($jpg_name)) {
    $thumb_nombre = $jpg_name;
} else if (file_exists($png_name)) {
    $thumb_nombre = $png_name;
} else if (file_exists($gif_name)) {
    $thumb_nombre = $gif_name;
} else if (file_exists($bmp_name)) {
    $thumb_nombre = $bmp_name;
} else {
    $thumb_nombre = "../uploader/default.jpg";
}


$nombre_usuar = $fila["nombre"];

$myvalue = $nombre_usuar;
$arr = explode(' ', trim($myvalue));
$nombre_usuar = $arr[0];

$nombre_usuar = ucfirst(substr($nombre_usuar, 0, 13));




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

    function gtag() {
        dataLayer.push(arguments);
    }
    gtag('js', new Date());
    gtag('config', 'UA-139626327-1');
</script>

<head>
    <script src="https://kit.fontawesome.com/bb6243346a.js" crossorigin="anonymous"></script>
    <script type="text/javascript" src="../public/js/jquery.min.js"></script>
    <script type="text/javascript" src="../public/js/popper.js"></script>
    <script type="text/javascript" src="../public/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="../public/js/jquery.mCustomScrollbar.js"></script>
    <script type="text/javascript" src="../public/lib/slick/slick.min.js"></script>
    <script type="text/javascript" src="../public/js/scrollbar.js"></script>
    <script type="text/javascript" src="../public/js/script.js"></script>
    <title>Language Exchange | Lingua2</title>
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

    $identificador2017 = $_SESSION['orden2017'];
    $_SESSION['idusuario2019'] = $identificador2017; //esto es solo por si hay el usuario quiere cambiar la foto de perfil
    //mirar que no est� el nick repetido

    $query = "SELECT * FROM mentor2009 WHERE orden='" . $identificador_usu_buscado . "'"; //seleccionamos todos los campos 

    $result = mysqli_query($link, $query);
    if (!mysqli_num_rows($result))
        die("User unregistered. <a href=\"http://www.lingua2.eu\">Information</a>");
    $fila = mysqli_fetch_array($result);
    $ciudad1 = $fila['Ciudad'];
    $id_del_receptor = $fila['orden'];

    $gpslat11 = $fila['Gpslat'];
    $gpslng11 = $fila['Gpslng'];

    $email_del_usu = $fila['Email'];
    $em = $email_del_usu;


    $email_verified = $fila['Emailverif'];



    require('../files/idiomasequivalencias.php');

    $tipo_form = $fila['Pais'];
    $teacher_price = $fila['teacherprice'];

    $idof1a = $fila['Idiomaof1'];
    $idof1 = $idiomas_equiv["$idof1a"];
    $idof2a = $fila['Idiomaof2'];
    $idof2 = $idiomas_equiv["$idof2a"];
    $idof3a = $fila['Idiomaof3'];
    $idof3 = $idiomas_equiv["$idof3a"];
    //$idof4a=$fila['Idiomaof4'];
    //$idiomaextraof=$fila['Idiomaextraofrecido'];

    $idde1a = $fila['Idiomadem1'];
    $idde1 = $idiomas_equiv["$idde1a"];
    $idde2a = $fila['Idiomadem2'];
    $idde2 = $idiomas_equiv["$idde2a"];
    $idde3a = $fila['Idiomadem3'];
    $idde3 = $idiomas_equiv["$idde3a"];
    //$idde4=$fila['Idiomadem4'];
    //$idiomaextradem=$fila['Idiomaextrademandado'];

    $idiomas_ofrecidos = "$idof1" . ' ' . "$idof2" . ' ' . "$idof3";
    // $idiomas_ofrecidos="$idof1".' '."$idof2".' '."$idof3".' '."$idof4".' '."$idiomaextraof";
    $idiomas_demandados = "$idde1" . ' ' . "$idde2" . ' ' . "$idde3";
    // $idiomas_demandados="$idde1".' '."$idde2".' '."$idde3".' '."$idde4".' '."$idiomaextradem";
    if ($tipo_form == "teacher") {
        $idiomas_demandados = "$teacher_price" . ' ' . "&#8364;/hour";
    }

    $foto_nombre = $fila['orden'];

    $thumb_nombre = $fila['orden'];

    $fb_ident = $fila['fbid'];

    $jpg_name = "../uploader/upload_pic/$foto_nombre.jpg";
    $png_name = "../uploader/upload_pic/$foto_nombre.png";
    $gif_name = "../uploader/upload_pic/$foto_nombre.gif";
    $bmp_name = "../uploader/upload_pic/$foto_nombre.bmp";

    if (file_exists($jpg_name)) {
        $foto_nombre = $jpg_name;
    } else if (file_exists($png_name)) {
        $foto_nombre = $png_name;
    } else if (file_exists($gif_name)) {
        $foto_nombre = $gif_name;
    } else if (file_exists($bmp_name)) {
        $foto_nombre = $bmp_name;
    }

    //sacamos la foto de facebook si no ha subido ninguna
    /*else if($fb_ident)  {
	$foto_nombre="../uploader/fb_temp_pics/$foto_nombre.jpg"; 
	copy("https://graph.facebook.com/$fb_ident/picture?type=large","$foto_nombre");	
	$img_properties="style=\"border-style: solid; border-color:red; border-width:1px;\" ";
} */ else {
        $foto_nombre = "../uploader/default.jpg";
    }


    //thumb

    $jpg_name = "../uploader/upload_pic/thumb_$thumb_nombre.jpg";
    $png_name = "../uploader/upload_pic/thumb_$thumb_nombre.png";
    $gif_name = "../uploader/upload_pic/thumb_$thumb_nombre.gif";
    $bmp_name = "../uploader/upload_pic/thumb_$thumb_nombre.bmp";

    if (file_exists($jpg_name)) {
        $thumb_nombre = $jpg_name;
    } else if (file_exists($png_name)) {
        $thumb_nombre = $png_name;
    } else if (file_exists($gif_name)) {
        $thumb_nombre = $gif_name;
    } else if (file_exists($bmp_name)) {
        $thumb_nombre = $bmp_name;
    } else {
        $thumb_nombre = "../uploader/default.jpg";
    }


    $nombre_usuar = $fila["nombre"];

    $myvalue = $nombre_usuar;
    $arr = explode(' ', trim($myvalue));
    $nombre_usuar = $arr[0];

    $nombre_usuar = ucfirst(substr($nombre_usuar, 0, 13));










                                       //todos los comentarios
                                $query1 = "
		SELECT  * 
		FROM comentarios 
		WHERE (id_aludido='$identificador_usu_buscado') AND censurado=0 ORDER BY horacreacion DESC ";
                                $result1 = mysqli_query($link, $query1);
                                $n_comentarios = mysqli_num_rows($result1);
                                //if($n_comentarios)
                                //{ 




                                //los comentarios positivos

                                $query432 = "
		SELECT  * 
		FROM comentarios 
		WHERE (id_aludido='$identificador_usu_buscado') AND censurado=0 AND rating=1 ORDER BY horacreacion DESC ";
                                $result432 = mysqli_query($link, $query432);
                                $n_comentarios_positivos = mysqli_num_rows($result432);


// para evitar error en php8 de division por cero ponemos if

							if($n_comentarios!=0)
                                $porcentaje_positivos = round($n_comentarios_positivos * 100 / $n_comentarios);
                                

















?>
<style>
    #myImg {
  border-radius: 5px;
  cursor: pointer;
  transition: 0.3s;
  display:flex;
}

#myImg:hover {opacity: 0.7;}

/* The Modal (background) */
.modal {
  display: none; /* Hidden by default */
  position: fixed; /* Stay in place */
  z-index: 1; /* Sit on top */
  padding-top: 100px; /* Location of the box */
  left: 0;
  top: 0;
  width: 100%; /* Full width */
  height: 100%; /* Full height */
  overflow: auto; /* Enable scroll if needed */
  background-color: rgb(0,0,0); /* Fallback color */
  background-color: rgba(0,0,0,0.9); /* Black w/ opacity */
}

/* Modal Content (Image) */
.modal-content {
  margin: auto;
  display: flex;
  width: 80%;
  max-width: 700px;
}

/* Caption of Modal Image (Image Text) - Same Width as the Image */
#caption {
  margin: auto;
  display: block;
  width: 80%;
  max-width: 700px;
  text-align: center;
  color: #ccc;
  padding: 10px 0;
  height: 150px;
}

/* Add Animation - Zoom in the Modal */
.modal-content, #caption {
  animation-name: zoom;
  animation-duration: 0.6s;
}

@keyframes zoom {
  from {transform:scale(0)}
  to {transform:scale(1)}
}

/* The Close Button */
.close {
  position: absolute;
  top: 15px;
  right: 35px;
  color: #f1f1f1;
  font-size: 40px;
  font-weight: bold;
  transition: 0.3s;
}

.close:hover,
.close:focus {
  color: #bbb;
  text-decoration: none;
  cursor: pointer;
}

/* 100% Image Width on Smaller Screens */
@media only screen and (max-width: 700px){
  .modal-content {
    width: 100%;
  }
}
</style>
<div class="main-section">
    <div class="container">
        <div class="main-section-data">
            <div class="row">
			

			<div class="main-left-sidebar no-margin" >
				<div class="user-data full-width ">
					<div class="user-profile" style="font-size: 50px;">
			
			<p >
			<center>
				<a href="./partners.php?pos=<?php echo $nextposi2017; ?>&jump=-10"><i class="fas fa-angle-double-left"></i></a> 
				<a href="./partners.php?pos=<?php echo $nextposi2017; ?>&jump=-1"><i class="fas fa-angle-left"></i></a>
				<?php echo $nextposi2017; ?>
				<a href="./partners.php?pos=<?php echo $nextposi2017; ?>&jump=1"><i class="fas fa-angle-right"></i></a> 
				<a href="./partners.php?pos=<?php echo $nextposi2017; ?>&jump=10"><i class="fas fa-angle-double-right"></i></a>
			
			</center>
			</p>
		
			</div></div></div>
			
			
                <div class="col-lg-3 col-md-4 pd-left-none no-pd">
                    <div class="main-left-sidebar no-margin">
                        <div class="user-data full-width ">
                            <div class="user-profile">
							
							<?php
							//aquí cogemos las medidas de la foto para hacer la cajeta username-dt tan larga como lo requiera la foto
							$size = getimagesize("$foto_nombre");
							//print_r( $size);
							$profile_photo_width=$size[0];
							$profile_photo_height=$size[1];
							
							$profile_photo_proportion=$profile_photo_height/$profile_photo_width;
							
							//echo "$profile_photo_width $profile_photo_height";
							
							//teniendo en cuenta que en el css está fijado el ancho a 110px para mostrar la foto
							$profile_photo_ratio=110/$profile_photo_width;
							$profile_photo_height_with_ratio=$profile_photo_height*$profile_photo_ratio;
							//echo $profile_photo_height_with_ratio;
							//teniendo en cuenta que en el css está fijado el top padding a 40px.
							$profile_photo_height_for_photo=80+$profile_photo_height_with_ratio;
							?>
							
							

                                <div class="username-dt" 
								
								<?// php if($profile_photo_proportion<1.1 OR $profile_photo_proportion>1.1) : ?>
								
								style="height: <?php echo $profile_photo_height_for_photo; ?>px;                  <?php if( $tipo_form=='teacher' ) { echo "background-color: #2e8b57";           }?>                         "       >
								
								<?//php endif; ?>
								
                                    <div class="usr-pic">
                                        
                                    <!-- Trigger the Modal -->
                                    <img style="border-radius:100px;" id="myImg" src="<?php  echo $foto_nombre; ?>?nocache=<?php echo time(); ?>">

                                    <!-- The Modal -->
                                    <div id="myModal" class="modal">

                                    <!-- The Close Button -->
                                    <span class="close">&times;</span>

                                    <!-- Modal Content (The Image) -->
                                    <img style="width:40%" class="modal-content" id="img01">

                                    <!-- Modal Caption (Image Text) -->
                                    </div>
										
										<?php //echo $foto_nombre; ?>
										
                                    </div>
                                </div>
								
								
								<?php if( $tipo_form=='teacher' ) { ?> <p style="color: #2e8b57; font-size:80%;">**I'm a professional teacher**</p><?php } ?> 
								
                                <!--username-dt end-->
                                <div class="user-specs ">
                                    <h3><?php
                                        echo $nombre_usuar;

                                        //vemos si ha verificado su email ya. si no, le ponemos link a pagina correspodiente

                                        ?></h3>


										 <?php
										 if($n_comentarios>0)
										 {
										 ?>
 
 
                                        <p style="font-size: 16px"><a href="../infouser/evdonepartners.php?u=<?php echo $identificador_usu_buscado; ?>"><?php echo "$n_comentarios"; ?> eval. </a>
										
										

                                            <?php if ($porcentaje_positivos >= 0 and $porcentaje_positivos <= 100) { ?>

                                                (<?php echo $porcentaje_positivos;  ?>% positive)<?php } ?></p>


										 <?php
										 }
										 ?>


                                    <span><?php


                                            //si nunca se le configuraron las coordenadas gps le sacamos un botón
                                            if ($gpslat11 == 0 and $gpslng11 == 0) {
                                                //echo "<br><center><a href=\"./getgpsposition.php\" style=\"color:red; font-size: 12px; font-weight: bold;\" >Add your location</a></center>";
                                            } else {
                                                echo $fila['Ciudad'] . " area";
                                            }

                                            ?></span>
											
										<p><?php echo "$distanciap1p2 km away" ?></p>
									
									
																		
									
									
									<?php //sacamos la organización si la tiene

											$domain1 = substr($email_del_usu, (int) strpos($email_del_usu, '@') + 1);
									
											$query123="
											SELECT organization_name AS org_name
											FROM organization_emails orgem 
											INNER JOIN organizations org 
											ON org.organization_id = orgem.id_organization_email 
											WHERE orgem.email_domain='$domain1'
											";
											
																					
											$result123=mysqli_query($link,$query123);
											if(mysqli_num_rows($result123))
											{
												$fila123=mysqli_fetch_array($result123);
												$organization1=$fila123['org_name'];
												
												?>
												<br/>
												<p style="color:#686868";>
												<i class="fas fa-user-check" aria-hidden="true" style="font-size: 15px;"></i>
												<?php 
												
												
												echo " $organization1 ";
											}
											?>
									
									
									
									
									
									
									
									
									
                                    <br> <br> 
									
									
									
									
									
			<form name="mensaje_usu" ENCTYPE="multipart/form-data" ACTION="<?php echo "sndmsg.php?id_receptor=$id_del_receptor"; ?>" METHOD="POST">	
				
				
			<textarea rows="5" cols="20" wrap="soft" name="mensajedelusuario" maxlength="255"> </textarea>
				<div class="follow-btn" style="position:relative;top:-20px;">
				</br></br>
					<a href=""><input type="submit" name="enviar" style="background-color: #e65f00;  border: none;
  color: white;
  padding: 15px 32px;
  text-align: center;
  border-radius: 10px;
  
  "value="Send message" /></a>
					
					</div>
				</form>	
									
									
									
									
									
									

                                </div>
								
															
                            </div>
                            <!--user-profile end-->

                        </div>
                        <!--user-data end-->
                   
				   
				   
						<!-- 
                        <div id="column1" class="suggestions full-width" >
           
                        </div> 
                        <div id="column2" class="suggestions full-width" >
           
                        </div> 
                        <div id="column3" class="suggestions full-width" >
           
                        </div>

						-->























                        <!--suggestions end-->



                        <!--tags-sec end-->
                    </div>
				
					
                    <!--main-left-sidebar end-->
                </div>
				
				
		
				
				
				
				
                <div class="col-lg-6 col-md-7 no-pd">
                    <div class="main-ws-sec">

                        <div class="posts-section">
                            <div id="seccion_teach" class="post-bar">

                                <div class="epi-sec">

                                </div>
                                <div class="job_descp ">
                                    <h3>I am able to teach</h3>

                                    <ul class="skill-tags">
                                        <?php if (!empty($idof1a)) { ?> <li><a href="#" title="" style="text-align:center;"> <img class="language language-<?php echo  $idof1a; ?> " />
                                                    </br><?php echo  $idiomas_equiv["$idof1a"]; ?></a></li>
                                        <?php
                                        }
                                        ?>
                                        <?php if (!empty($idof2a)) { ?> <li><a href="#" title="" style="text-align:center;"> <img class="language language-<?php echo  $idof2a; ?> " />
                                                    </br><?php echo  $idiomas_equiv["$idof2a"]; ?></a></li>
                                        <?php
                                        }
                                        ?>
                                        <?php if (!empty($idof3a)) { ?> <li><a href="#" title="" style="text-align:center;"> <img class="language language-<?php echo  $idof3a; ?> " />
                                                    </br><?php echo  $idiomas_equiv["$idof3a"]; ?></a></li>

                                        <?php
                                        }
                                        ?>

                                    </ul>
                                </div>
                                <?php if ($tipo_form != "teacher") {

                                ?>
                                    <div class="epi-sec">

                                    </div>
                                    <div class="job_descp">
                                        <h3>I want to learn</h3>

                                        <ul class="skill-tags">
										
										
										
										
										    <?php if (!empty($idde1a)) { ?> 
												<li>
														
															<a href="#" title="" style="text-align:center;"> <img class="language language-<?php echo  $idde1a; ?> " />
															
															</br>
															

																					<img style="margin-top: 5px; width: 50px; height: 5px;" src="./images/language_levels/<?php echo $image_level_lang_dem_1; ?>" alt="<?php echo  $idiomas_nivel["$level_idiomademan1"]; ?>" />

																		<br>   
																		 <?php echo  $idiomas_equiv["$idde1a"]; ?> (<?php echo  $idiomas_nivel["$level_idiomademan1"];?>)
															</a>

															

												</li>
                                            <?php
                                            }
											
											
                                            ?>
										
										
										
										
										
										
										
										
                                            <?php /* if (!empty($idde1a)) { ?> <li><a href="#" title="" style="text-align:center;"> <img class="language language-<?php echo  $idde1a; ?> " />
                                                        </br><?php echo  $idiomas_equiv["$idde1a"]; ?></a></li> 
                                            <?php
                                            }*/
                                            ?>
											
											
											
											
											
											<?php if (!empty($idde2a)) { ?> 
												<li>
														
															<a href="#" title="" style="text-align:center;"> <img class="language language-<?php echo  $idde2a; ?> " />
															
															<br>
															

																					<img style="margin-top: 5px; width: 50px; height: 5px;" src="./images/language_levels/<?php echo $image_level_lang_dem_2; ?>" alt="<?php echo  $idiomas_nivel["$level_idiomademan2"]; ?>" />

																		<br>   
																		 <?php echo  $idiomas_equiv["$idde2a"]; ?> (<?php echo  $idiomas_nivel["$level_idiomademan2"];?>)
															</a>
												</li>
                                            <?php
                                            }
                                            ?>
											
																					
											
											
											
											
                                            <?php
											
											/* if (!empty($idde2a)) { ?> <li><a href="#" title="" style="text-align:center;"> <img class="language language-<?php echo  $idde2a; ?> " />
                                                        </br><?php echo  $idiomas_equiv["$idde2a"]; ?></a></li>
                                            <?php
                                            }*/
                                            ?>
											
											
											
											
											<?php if (!empty($idde3a)) { ?> 
												<li>
														
															<a href="#" title="" style="text-align:center;"> <img class="language language-<?php echo  $idde3a; ?> " />
															
															<br>
															

																					<img style="margin-top: 5px; width: 50px; height: 5px;" src="./images/language_levels/<?php echo $image_level_lang_dem_3; ?>" alt="<?php echo  $idiomas_nivel["$level_idiomademan3"]; ?>" />

																		<br>   
																		 <?php echo  $idiomas_equiv["$idde3a"]; ?> (<?php echo  $idiomas_nivel["$level_idiomademan3"]; ?>)
															</a>
														
												</li>
                                            <?php
                                            }
                                            ?>		
											
											
											
											
											
                                            <?php /* if (!empty($idde3a)) { ?> <li><a href="#" title="" style="text-align:center;"> <img class="language language-<?php echo  $idde3a; ?> " />
                                                        </br><?php echo  $idiomas_equiv["$idde3a"]; ?></a></li>

                                            <?php
                                            }*/
                                            ?>
                                        </ul>
										
									<?php 
									//aqui avisamos si algún idioma introducido no tiene su nivel insertado										
										if( ($level_idiomademan1 == 0 AND !empty($idde1a)) OR  ($level_idiomademan2 == 0 AND !empty($idde2a)) OR  ($level_idiomademan3 == 0 AND !empty($idde3a)) )
									{ ?>
											
											<br> <p style="font-size: 70%; color: #b2b2b2;">(*) This user didn't update the level of languages yet.</p>
											
										<?php 
												} ?>
										
                                    </div>

                                <?         } else {

                                    //echo "<div style=\"font-size:30px;text-align:center;position:relative;top:-80px;height: 90px;\">$idiomas_demandados</div>";
                                
								?>
								
								
								<div class="epi-sec">

                                    </div>
                                    <div class="job_descp">
                                        <h3>My hourly rate is</h3>
										
										<?php	echo "<div style=\"font-size:30px;text-align:center;color:#666666;\">$idiomas_demandados</div>";	 ?>						
								
									</div>
										<?php	}
								

                                ?>







                                <?php
                                //} 
                                ?>
                                <div class="job_descp">

                                    </br>
									<h3>More information</h3> 
									
                                    <span style="color:#666666;"><?php echo $availability100; ?></span></br></br>
                                    <span style="color:#666666;"><?php echo $othercomments100; ?></span>


                                    <p> <a href="#" title=""></a></p>

                                </div>



                            </div>
                            <!--post-bar end-->


                            <?php
                            //numero maximo de evaluaciones que queremos mostrar
                            $num_max_ev_mostradas = 3;



                            ?>


                            <div class="top-profiles ">
                                <div class="pf-hd">
                                    <h3>Evaluations

                                        <p style="font-size: 16px"><a href="../infouser/evdonepartners.php?u=<?php echo $identificador_usu_buscado; ?>"><?php echo "$n_comentarios"; ?> evaluations received </a>

                                            <?php if ($porcentaje_positivos >= 0 and $porcentaje_positivos <= 100) { ?>

                                                (<?php echo $porcentaje_positivos;  ?>% positive)<?php } ?></p>
                                    </h3>

                                </div>




                                <div class="post-bar" style="margin: 10px 10px 10px 10px; width: 96%">


                                    <?php

                                    //si no uso LEFT JOIN no funciona
                                    $query1010 = "
		SELECT  m.nombre AS nombre1, m.orden AS orden1,m.fotoext AS fotoext1, comentarios.comment, comentarios.hora, comentarios.rating
		FROM comentarios
		
		LEFT JOIN mentor2009 AS m
		ON m.orden = comentarios.id_autor
		
		WHERE comentarios.id_aludido='$identificador_usu_buscado' AND comentarios.censurado=0 
		ORDER BY comentarios.horacreacion DESC ";
                                    $result1010 = mysqli_query($link, $query1010);
                                    $num_evaluaciones = mysqli_num_rows($result1010);


                                    $num_ev_bucle = min($num_max_ev_mostradas, $num_evaluaciones);

                                    for ($jjj = 0; $jjj < $num_ev_bucle; $jjj++) {
                                        $fila1010 = mysqli_fetch_array($result1010);
                                        $comentario_ev = $fila1010['comment'];
                                        $hora_ev = $fila1010['hora'];
                                        $rating_ev = $fila1010['rating'];
                                        $color = "";
                                        if ($rating_ev == 1) {
                                            $rating_ev = "POSITIVE";
                                            $color = "green";
                                        }
                                        if ($rating_ev == 2) {
                                            $rating_ev = "NEUTRAL";
                                            $color = "gray";
                                        }
                                        if ($rating_ev == 3) {
                                            $rating_ev = "NEGATIVE";
                                            $color = "red";
                                        }
                                        if ($rating_ev == 4) {
                                            $rating_ev = "NO ANSWER";
                                            $color = "orange";
                                        }


                                        $autor_ev = $fila1010['nombre1'];
										$palabras = explode (" ", $autor_ev);
										$autor_ev=ucfirst($palabras[0]);
										
										
                                        if ($autor_ev == '') {
                                            $autor_ev = "User unregistered";
                                        }

                                        $foto_extension = $fila1010['fotoext1'];

                                        $orden47 = $fila1010['orden1'];

                                        $foto_autor = $fila1010['orden1'];

                                        $foto_autor = "../uploader/upload_pic/thumb_$foto_autor" . "." . "$foto_extension";
										
										//echo "$foto_autor";

                                        if (!file_exists($foto_autor))
                                            $foto_autor = "../uploader/default.jpg";
                                    ?>



                                        <div class="post_topbar">
                                            <div class="usy-dt">
                                                <img src="<?php echo "$foto_autor"; ?>" alt="">
                                                <div class="usy-name">
                                                    <h3><?php echo "$autor_ev"; ?></h3>
                                                    <span><img src="images/clock.png" alt=""><?php echo " Evaluated on $hora_ev"; ?></span>
                                                </div>
                                            </div>
                                            <div class="ed-opts">
                                                <a href="#" title="" class="ed-opts-open"><i class="la la-ellipsis-v"></i></a>
                                                <ul class="ed-options">
                                                    <li><a href="./u.php?identificador=<?php echo $orden47; ?>" title="">Visit profile</a></li>
                                                    <li><a href="./reportabuse.php" title="">Report abuse</a></li>
													<li><a href="./resolutioncenter.php" title="">Resolution center</a></li>
                                                </ul>
                                            </div>
											
 

                                        </div>

                                        <div class="job_descp">

                                            <ul class="job-dt">
                                                <li><a href="#" title="" style="background-color:<?php echo $color; ?>"> <?php echo "$rating_ev"; ?> </a></li>

                                            </ul>


                                            <p><?php echo "$comentario_ev"; ?> </p>

                                        </div>





                                    <?php }

                                    //view similar users solo si supera el número máximo de evaluaciones que se muestran en esta página


                                    if ($num_evaluaciones > $num_max_ev_mostradas) :

                                    ?>
                                        <div class="view-more" style="height: 50px;">

                                            <a href="../infouser/evdonepartners.php?u=<?php echo "$identificador_usu_buscado";  ?>"    title="">View Similar Users</a> </br>

                                        </div>



                                    <?php

                                    endif;

                                    ?>


                                </div>

                            </div>
                            <!--post-bar end-->
                            <div class="posty" hidden>
                                <div class="post-bar no-margin">
                                    <div class="post_topbar">
                                        <div class="usy-dt">
                                            <img src="http://via.placeholder.com/50x50" alt="">
                                            <div class="usy-name">
                                                <h3>John Doe</h3>
                                                <span><img src="images/clock.png" alt="">3 min ago</span>
                                            </div>
                                        </div>
                                        <div class="ed-opts">
                                            <a href="#" title="" class="ed-opts-open"><i class="la la-ellipsis-v"></i></a>
                                            <ul class="ed-options">
                                                <li><a href="#" title="">Edit Post</a></li>
                                                <li><a href="#" title="">Unsaved</a></li>
                                                <li><a href="#" title="">Unbid</a></li>
                                                <li><a href="#" title="">Close</a></li>
                                                <li><a href="#" title="">Hide</a></li>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="epi-sec">
                                        <ul class="descp">
                                            <li><img src="images/icon8.png" alt=""><span>Epic Coder</span></li>
                                            <li><img src="images/icon9.png" alt=""><span>India</span></li>
                                        </ul>
                                        <ul class="bk-links">
                                            <li><a href="#" title=""><i class="la la-bookmark"></i></a></li>
                                            <li><a href="#" title=""><i class="la la-envelope"></i></a></li>
                                        </ul>
                                    </div>
                                    <div class="job_descp">
                                        <h3>Senior Wordpress Developer</h3>
                                        <ul class="job-dt">
                                            <li><a href="#" title="">Full Time</a></li>
                                            <li><span>$30 / hr</span></li>
                                        </ul>
                                        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aliquam luctus hendrerit metus, ut ullamcorper quam finibus at. Etiam id magna sit amet... <a href="#" title="">view more</a></p>
                                        <ul class="skill-tags">
                                            <li><a href="#" title="">HTML</a></li>
                                            <li><a href="#" title="">PHP</a></li>
                                            <li><a href="#" title="">CSS</a></li>
                                            <li><a href="#" title="">Javascript</a></li>
                                            <li><a href="#" title="">Wordpress</a></li>
                                        </ul>
                                    </div>
                                    <div class="job-status-bar">
                                        <ul class="like-com">
                                            <li>
                                                <a href="#"><i class="la la-heart"></i> Like</a>
                                                <img src="images/liked-img.png" alt="">
                                                <span>25</span>
                                            </li>
                                            <li><a href="#" title="" class="com"><img src="images/com.png" alt=""> Comment 15</a></li>
                                        </ul>
                                        <a><i class="la la-eye"></i>Views 50</a>
                                    </div>
                                </div>
                                <!--post-bar end-->
                                <div class="comment-section">
                                    <div class="plus-ic">
                                        <i class="la la-plus"></i>
                                    </div>
                                    <div class="comment-sec">
                                        <ul>
                                            <li>
                                                <div class="comment-list">
                                                    <div class="bg-img">
                                                        <img src="http://via.placeholder.com/40x40" alt="">
                                                    </div>
                                                    <div class="comment">
                                                        <h3>John Doe</h3>
                                                        <span><img src="images/clock.png" alt=""> 3 min ago</span>
                                                        <p>Lorem ipsum dolor sit amet, </p>
                                                        <a href="#" title="" class="active"><i class="fa fa-reply-all"></i>Reply</a>
                                                    </div>
                                                </div>
                                                <!--comment-list end-->
                                                <ul>
                                                    <li>
                                                        <div class="comment-list">
                                                            <div class="bg-img">
                                                                <img src="http://via.placeholder.com/40x40" alt="">
                                                            </div>
                                                            <div class="comment">
                                                                <h3>John Doe</h3>
                                                                <span><img src="images/clock.png" alt=""> 3 min ago</span>
                                                                <p>Hi John </p>
                                                                <a href="#" title=""><i class="fa fa-reply-all"></i>Reply</a>
                                                            </div>
                                                        </div>
                                                        <!--comment-list end-->
                                                    </li>
                                                </ul>
                                            </li>
                                            <li>
                                                <div class="comment-list">
                                                    <div class="bg-img">
                                                        <img src="http://via.placeholder.com/40x40" alt="">
                                                    </div>
                                                    <div class="comment">
                                                        <h3>John Doe</h3>
                                                        <span><img src="images/clock.png" alt=""> 3 min ago</span>
                                                        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aliquam luctus hendrerit metus, ut ullamcorper quam finibus at.</p>
                                                        <a href="#" title=""><i class="fa fa-reply-all"></i>Reply</a>
                                                    </div>
                                                </div>
                                                <!--comment-list end-->
                                            </li>
                                        </ul>
                                    </div>
                                    <!--comment-sec end-->
                                    <div class="post-comment">
                                        <div class="cm_img">
                                            <img src="http://via.placeholder.com/40x40" alt="">
                                        </div>
                                        <div class="comment_box">
                                            <form>
                                                <input type="text" placeholder="Post a comment">
                                                <button type="submit">Send</button>
                                            </form>
                                        </div>
                                    </div>
                                    <!--post-comment end-->
                                </div>
                                <!--comment-section end-->
                            </div>
                            <!--posty end-->
                            <div class="process-comm">
                                <div class="spinner">
                                    <div class="bounce1"></div>
                                    <div class="bounce2"></div>
                                    <div class="bounce3"></div>
                                </div>
                            </div>
                            <!--process-comm end-->
                        </div>
                        <!--posts-section end-->
                    </div>
                    <!--main-ws-sec end-->
                </div>






                <div class="col-lg-3 pd-right-none no-pd ">
                    <div class="right-sidebar">







<?php


//Buscamos los usuarios profesores aleatoriamente


require('../files/bd.php');

session_start();


$identificador2017=$_SESSION['orden2017'];


$query77="SELECT * FROM mentor2009 WHERE orden='$identificador2017' ";
$result77=mysqli_query($link,$query77);
if(!mysqli_num_rows($result77))
		die("User unregistered 1.");
$fila77=mysqli_fetch_array($result77);

$latitud1=$fila77['Gpslat'];
$longitud1=$fila77['Gpslng'];
$idiomademan1=$fila77['Idiomadem1'];
$idiomademan2=$fila77['Idiomadem2'];
$idiomademan3=$fila77['Idiomadem3'];

$idiomaofre1=$fila77['Idiomaof1'];
$idiomaofre2=$fila77['Idiomaof2'];
$idiomaofre3=$fila77['Idiomaof3'];

$is_teacher=$fila77['Pais'];

//aqui damos el valor null a los idiomas que están vacíos. si no en la query de debajo seleccionaría los usuarios que tengan, por ejemplo, m.m.Idiomaof1 vacío
if(empty($idiomademan1))
	$idiomademan1='null';

if(empty($idiomademan2))
	$idiomademan2='null';

if(empty($idiomademan3))
	$idiomademan3='null';

if(empty($idiomaofre1))
	$idiomaofre1='null';

if(empty($idiomaofre2))
	$idiomaofre2='null';

if(empty($idiomaofre3))
	$idiomaofre3='null';




//__________________________________________


//los profes solo se muestran a los usuarios que no son profesores
if($is_teacher!='teacher')
{

$query="
SELECT 
m.orden,

(acos(sin(radians(m.Gpslat)) * sin(radians($latitud1)) + 
cos(radians(m.Gpslat)) * cos(radians($latitud1)) * 
cos(radians(m.Gpslng) - radians($longitud1))) * 6378) 

AS distanciaPunto1Punto2, m.teacherprice, m.nombre, m.fotoext

FROM mentor2009 m

WHERE

orden <> '$identificador2017' 

AND


(m.Idiomaof1='$idiomademan1' OR  m.Idiomaof1='$idiomademan2' OR m.Idiomaof1='$idiomademan3' OR
m.Idiomaof2='$idiomademan1' OR  m.Idiomaof2='$idiomademan2' OR m.Idiomaof2='$idiomademan3' OR
m.Idiomaof3='$idiomademan1' OR  m.Idiomaof3='$idiomademan2' OR m.Idiomaof3='$idiomademan3'
) 
	
AND
Pais = 'teacher'

HAVING

distanciaPunto1Punto2 < 50


ORDER BY RAND()

LIMIT 1


";


//echo "</br></br>$query</br></br>";


$result=mysqli_query($link,$query);

//si no hay ninguno en el radio indicado, entonces cogemos la búsqueda global sin tener en cuenta el radio
if(!mysqli_num_rows($result))
{
		$query="
	SELECT 
	m.orden,

	(acos(sin(radians(m.Gpslat)) * sin(radians($latitud1)) + 
	cos(radians(m.Gpslat)) * cos(radians($latitud1)) * 
	cos(radians(m.Gpslng) - radians($longitud1))) * 6378) 

	AS distanciaPunto1Punto2, m.teacherprice, m.nombre, m.fotoext

	FROM mentor2009 m

	WHERE

	orden <> '$identificador2017' 

	AND


	(m.Idiomaof1='$idiomademan1' OR  m.Idiomaof1='$idiomademan2' OR m.Idiomaof1='$idiomademan3' OR
	m.Idiomaof2='$idiomademan1' OR  m.Idiomaof2='$idiomademan2' OR m.Idiomaof2='$idiomademan3' OR
	m.Idiomaof3='$idiomademan1' OR  m.Idiomaof3='$idiomademan2' OR m.Idiomaof3='$idiomademan3'
	) 
		
	AND
	Pais = 'teacher'

	ORDER BY RAND()

	LIMIT 1


	";

	$result=mysqli_query($link,$query);
}

 
	
$fila=mysqli_fetch_array($result);



$orden99= $fila['orden'];
$distancia99= round($fila['distanciaPunto1Punto2'],2);
$precioprof= $fila['teacherprice'];
$nombre_usuario= $fila['nombre'];

$vector1=array();

$vector1= preg_split('/\s+/', $nombre_usuario);

$nombre_usuario_short=$vector1[0];

$nombre_usuario_short=ucfirst($nombre_usuario_short);


$extension = $fila['fotoext'];

$path_photo="../uploader/upload_pic/thumb_$orden99"."."."$extension";

//echo "$path_photo";

//echo "</br></br>$path_photo</br></br>";

if ( !file_exists($path_photo) ) :
	$path_photo="../uploader/default.jpg";
endif;
 


//echo "<a href=\"../user/u.php?dst=$distancia99&identificador=$orden99\">click $precioprof"."€ $nombre_usuario_short   $path_photo</a> ";





?>
















                        <div class="suggestions full-width">
                            <div class="sd-title">
                                <h3>Random teacher nearby</h3>
                                <!-- <i class="la la-ellipsis-v"></i> -->
                            </div>
                            <!--sd-title end-->
                            <div class="suggestions-list">
                                <div class="suggestion-usd">


                                    <img src="<?php echo "$path_photo"; ?>" height="35px" ; width="35px" ;>


                                    <div class="sgt-text">
                                        <h4><?php echo "$nombre_usuario_short"; ?></h4>
                                        <p><?php echo "$precioprof"."€/hour"; ?></p>

                                        
                                    </div>

                                    <span><a href="<?php echo "../user/u.php?dst=$distancia99&identificador=$orden99"; ?>" ><i class="la la-chevron-right"></i></a></span>

                                </div>

                                <div class="view-more">
                                    <a href="./partners.php" title="">View Similar Users</a>
                                </div>
                            </div>
                            <!--suggestions-list end-->
                        </div>












<?php
}

//la logica de los profesores es diferente: hay que buscar el idioma que ellos OFRECEN, no el que DEMANDAN
if($is_teacher!='teacher')
{
//aqui sacamos a los usuarios no-profesores aleatoriamente
$query="
SELECT 
m.orden,

(acos(sin(radians(m.Gpslat)) * sin(radians($latitud1)) + 
cos(radians(m.Gpslat)) * cos(radians($latitud1)) * 
cos(radians(m.Gpslng) - radians($longitud1))) * 6378) 

AS distanciaPunto1Punto2, m.teacherprice, m.nombre, m.Ciudad, m.fotoext

FROM mentor2009 m

WHERE

orden <> '$identificador2017' 

AND


(m.Idiomaof1='$idiomademan1' OR  m.Idiomaof1='$idiomademan2' OR m.Idiomaof1='$idiomademan3' OR
m.Idiomaof2='$idiomademan1' OR  m.Idiomaof2='$idiomademan2' OR m.Idiomaof2='$idiomademan3' OR
m.Idiomaof3='$idiomademan1' OR  m.Idiomaof3='$idiomademan2' OR m.Idiomaof3='$idiomademan3'
) 
	
AND
Pais <> 'teacher'

HAVING

distanciaPunto1Punto2 < 50


ORDER BY RAND()

LIMIT 1


";


//echo "</br></br>$query</br></br>";


$result=mysqli_query($link,$query);

//si no hay ninguno en el radio indicado, entonces cogemos la búsqueda global sin tener en cuenta el radio
if(!mysqli_num_rows($result))
{
		$query="
	SELECT 
	m.orden,

	(acos(sin(radians(m.Gpslat)) * sin(radians($latitud1)) + 
	cos(radians(m.Gpslat)) * cos(radians($latitud1)) * 
	cos(radians(m.Gpslng) - radians($longitud1))) * 6378) 

	AS distanciaPunto1Punto2, m.teacherprice, m.nombre, m.Ciudad, m.fotoext

	FROM mentor2009 m
 
	WHERE

	orden <> '$identificador2017' 

	AND


	(m.Idiomaof1='$idiomademan1' OR  m.Idiomaof1='$idiomademan2' OR m.Idiomaof1='$idiomademan3' OR
	m.Idiomaof2='$idiomademan1' OR  m.Idiomaof2='$idiomademan2' OR m.Idiomaof2='$idiomademan3' OR
	m.Idiomaof3='$idiomademan1' OR  m.Idiomaof3='$idiomademan2' OR m.Idiomaof3='$idiomademan3'
	) 
		
	AND
	Pais <> 'teacher'

	ORDER BY RAND()

	LIMIT 1


	";

	$result=mysqli_query($link,$query);
}

 
	
$fila=mysqli_fetch_array($result);



$orden99= $fila['orden'];
$distancia99= round($fila['distanciaPunto1Punto2'],2);
$precioprof= $fila['teacherprice'];
$nombre_usuario= $fila['nombre'];
$ciudad97=$fila['Ciudad'];

$vector1=array();

$vector1= preg_split('/\s+/', $nombre_usuario);

$nombre_usuario_short=$vector1[0];

$nombre_usuario_short=ucfirst($nombre_usuario_short);


$extension = $fila['fotoext'];

$path_photo="../uploader/upload_pic/thumb_$orden99"."."."$extension";

//echo "</br></br>$path_photo</br></br>";

if ( !file_exists($path_photo) ) :
	$path_photo="../uploader/default.jpg";
endif;
 


//echo "<a href=\"../user/u.php?dst=$distancia99&identificador=$orden99\">click $precioprof"."€ $nombre_usuario_short   $path_photo</a> ";

?>














                        <div class="suggestions full-width ">
                            <div class="sd-title">
                                <h3>Random user close to you </h3>
                                <!-- <i class="la la-ellipsis-v"></i> -->
                            </div>
                            <!--sd-title end-->
                            <div class="suggestions-list">
                                <div class="suggestion-usd">


                                    <img src="<?php echo "$path_photo"; ?>" height="35px" ; width="35px" ;>


                                    <div class="sgt-text">
                                        <h4><?php echo "$nombre_usuario_short"; ?></h4>
                                        <p><?php echo "$ciudad97"; ?></p>


                                    </div>

                                    <span><a href="<?php echo "../user/u.php?dst=$distancia99&identificador=$orden99"; ?>"><i class="la la-chevron-right"></i></a></span>

                                </div>

                                <div class="view-more">
                                    <a href="./partners.php" title="">View Similar Users</a>
                                </div>
                            </div>
                            <!--suggestions-list end-->
                        </div>


<?php } else //si es profesor
		{		
	
	
	//aqui sacamos a los usuarios no-profesores aleatoriamente, pero cambiando la logica de la consulta (ahora los buscamos idiomas demandados de otros usuarios
$query="
SELECT 
m.orden,

(acos(sin(radians(m.Gpslat)) * sin(radians($latitud1)) + 
cos(radians(m.Gpslat)) * cos(radians($latitud1)) * 
cos(radians(m.Gpslng) - radians($longitud1))) * 6378) 

AS distanciaPunto1Punto2, m.teacherprice, m.nombre, m.Ciudad, m.fotoext

FROM mentor2009 m

WHERE

orden <> '$identificador2017' 

AND


(m.Idiomadem1='$idiomaofre1' OR  m.Idiomadem1='$idiomaofre2' OR m.Idiomadem1='$idiomaofre3' OR
m.Idiomadem2='$idiomaofre1' OR  m.Idiomadem2='$idiomaofre2' OR m.Idiomadem2='$idiomaofre3' OR
m.Idiomadem3='$idiomaofre1' OR  m.Idiomadem3='$idiomaofre2' OR m.Idiomadem3='$idiomaofre3'
) 
	
AND
Pais <> 'teacher'

HAVING

distanciaPunto1Punto2 < 50


ORDER BY RAND()

LIMIT 1


";


//echo "</br></br>$query</br></br>";


$result=mysqli_query($link,$query);

//si no hay ninguno en el radio indicado, entonces cogemos la búsqueda global sin tener en cuenta el radio
if(!mysqli_num_rows($result))
{
		$query="
	SELECT 
	m.orden,

	(acos(sin(radians(m.Gpslat)) * sin(radians($latitud1)) + 
	cos(radians(m.Gpslat)) * cos(radians($latitud1)) * 
	cos(radians(m.Gpslng) - radians($longitud1))) * 6378) 

	AS distanciaPunto1Punto2, m.teacherprice, m.nombre, m.Ciudad, m.fotoext

	FROM mentor2009 m
 
	WHERE

	orden <> '$identificador2017' 

	AND


(m.Idiomadem1='$idiomaofre1' OR  m.Idiomadem1='$idiomaofre2' OR m.Idiomadem1='$idiomaofre3' OR
m.Idiomadem2='$idiomaofre1' OR  m.Idiomadem2='$idiomaofre2' OR m.Idiomadem2='$idiomaofre3' OR
m.Idiomadem3='$idiomaofre1' OR  m.Idiomadem3='$idiomaofre2' OR m.Idiomadem3='$idiomaofre3'
) 
		
	AND
	Pais <> 'teacher'

	ORDER BY RAND()

	LIMIT 1


	";

	$result=mysqli_query($link,$query);
}

 
	
$fila=mysqli_fetch_array($result);



$orden99= $fila['orden'];
$distancia99= round($fila['distanciaPunto1Punto2'],2);
$precioprof= $fila['teacherprice'];
$nombre_usuario= $fila['nombre'];
$ciudad97=$fila['Ciudad'];

$vector1=array();

$vector1= preg_split('/\s+/', $nombre_usuario);

$nombre_usuario_short=$vector1[0];


$extension = $fila['fotoext'];

$path_photo="../uploader/upload_pic/thumb_$orden99"."."."$extension";

//echo "</br></br>$path_photo</br></br>";

if ( !file_exists($path_photo) ) :
	$path_photo="../uploader/default.jpg";
endif;
	
	//echo $query;
	
	?>


                        <div class="suggestions full-width ">
                            <div class="sd-title">
                                <h3>Random students nearby</h3>
                                <!-- <i class="la la-ellipsis-v"></i> -->
                            </div>
                            <!--sd-title end-->
                            <div class="suggestions-list">
                                <div class="suggestion-usd">


                                    <img src="<?php echo "$path_photo"; ?>" height="35px" ; width="35px" ;>


                                    <div class="sgt-text">
                                        <h4><?php echo "$nombre_usuario_short"; ?></h4>
                                        <p><?php echo "$ciudad97"; ?></p>


                                    </div>

                                    <span><a href="<?php echo "../user/u.php?dst=$distancia99&identificador=$orden99"; ?>"><i class="la la-chevron-right"></i></a></span>

                                </div>

                                <div class="view-more">
                                    <a href="./partners.php" title="">View Similar Users</a>
                                </div>
                            </div>
                            <!--suggestions-list end-->
                        </div>



<?php }?>





























                    </div>
                    <!--widget-about end-->

                    <!--widget-jobs end-->
                    <div class="widget widget-jobs" hidden>
                        <div class="sd-title">
                            <h3>Most Viewed This Week</h3>
                            <i class="la la-ellipsis-v"></i>
                        </div>
                        <div class="jobs-list">
                            <div class="job-info">
                                <div class="job-details">
                                    <h3>Senior Product Designer</h3>
                                    <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit..</p>
                                </div>
                                <div class="hr-rate">
                                    <span>$25/hr</span>
                                </div>
                            </div>
                            <!--job-info end-->
                            <div class="job-info">
                                <div class="job-details">
                                    <h3>Senior UI / UX Designer</h3>
                                    <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit..</p>
                                </div>
                                <div class="hr-rate">
                                    <span>$25/hr</span>
                                </div>
                            </div>
                            <!--job-info end-->
                            <div class="job-info">
                                <div class="job-details">
                                    <h3>Junior Seo Designer</h3>
                                    <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit..</p>
                                </div>
                                <div class="hr-rate">
                                    <span>$25/hr</span>
                                </div>
                            </div>
                            <!--job-info end-->
                        </div>
                        <!--jobs-list end-->
                    </div>
                    <!--widget-jobs end-->

                </div>
                <!--right-sidebar end-->
            </div>
        </div>
    </div><!-- main-section-data end-->

</div>

<script>
    $(document).ready(function() {
        var column1 = $('#seccion_teach').clone().attr('id', 'seccion_teach_clone');
        $('#column1').append(column1);  
        var column2 = $('#events').clone().attr('id', 'events_clone');
        $('#column2').append(column2);  
        var column3 = $('#my_events').clone().attr('id', 'my_events_clone');
        $('#column3').append(column3);
       

        // $("#column1").attr("hidden", true);
        //   $('#columna2').html(columna1);
        //   $('#columna3').html(columna1);
        //   $('#columna4').html(columna1);
        //   $('#columna5').html(columna1);
        //   $('#columna6').html(columna1);
        //   $('#columna7').html(columna1);
        resize_movil();
        window.addEventListener("resize", function() {
        resize_movil();
        });
    });

    function resize_movil()
    {
        $("#seccion_teach_clone").css("margin-bottom", "0px");
        $("#events_clone").css("margin-bottom", "0px");

        if (screen.width < 768) {
            //$("#seccion_teach").attr("hidden", true);
            //$("#seccion_teach_clone").attr("hidden", false);
            $("#events").attr("hidden", true);
            $("#events_clone").attr("hidden", false);
            $("#my_events").attr("hidden", true);
            $("#my_events_clone").attr("hidden", false);
            }

            else {
                $("#seccion_teach").attr("hidden", false);
                $("#seccion_teach_clone").attr("hidden", true);
                $("#events").attr("hidden", false);
                $("#events_clone").attr("hidden", true);
                $("#my_events").attr("hidden", false);
                $("#my_events_clone").attr("hidden", true);
                


            }

    }
</script>
<script>
    // Get the modal
var modal = document.getElementById("myModal");

// Get the image and insert it inside the modal - use its "alt" text as a caption
var img = document.getElementById("myImg");
var modalImg = document.getElementById("img01");
var captionText = document.getElementById("caption");
img.onclick = function(){
  modal.style.display = "flex";
  modalImg.src = this.src;
  captionText.innerHTML = this.alt;
}

// Get the <span> element that closes the modal
var span = document.getElementsByClassName("close")[0];

// When the user clicks on <span> (x), close the modal
span.onclick = function() {
  modal.style.display = "none";
}
</script>

<?php
//hay que pasar la variable identificador del usuario consultado

require('../templates/footer.php');

?>