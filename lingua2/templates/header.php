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

$query = "SELECT * FROM mentor2009 WHERE orden='" . $identificador2017 . "'"; //seleccionamos todos los campos 

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

$availability100=$fila['Disponibilidadcomentarios'];
$othercomments100=$fila['Otroscomentarios']; 

$zonaHoraria=$fila['timeshift'];    
                                    



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




/*
$idof1=$fila['Idiomaof1'];$idof2=$fila['Idiomaof2'];$idof3=$fila['Idiomaof3'];$idof4=$fila['Idiomaof4'];$idofextra=$fila['Idiomaextraofrecido'];
$idde1=$fila['Idiomadem1'];$idde2=$fila['Idiomadem2'];$idde3=$fila['Idiomadem3'];$idde4=$fila['Idiomadem4'];$iddeextra=$fila['Idiomaextrademandado'];
$idiomas_ofrecidos="$idof1".' '."$idof2".' '."$idof3".' '."$idof4"; 
$idiomas_demandados="$idde1".' '."$idde2".' '."$idde3".' '."$idde4"; 
*/


// si no está verificado el email, se muestra el header simplificado para que el usuario no pueda acceder a ninguna función, se muestra mensaje de validar email y se sale del programa con exit.

if(!$email_verified)
{
	require('../templates/header_simplified.html');
	
	
				?>	
			<div class="alert alert-danger" align="center">
			
               To see all the contents you need to validate your email address. If you did not receive any email click <a style="text-decoration: underline;" href=<?php echo "./verify_email.php" ?> >here</a>. (Check also your Spam folder.)</br>
				
			</div>		
			<?php
			
				exit(0);
	
}
?>







<!DOCTYPE HTML>
<html>


<head>
    <script src="https://kit.fontawesome.com/bb6243346a.js" crossorigin="anonymous"></script>

    <script type="text/javascript" src="../public/js/jquery.min.js"></script>
    <script type="text/javascript" src="../public/js/popper.js"></script>
    <script type="text/javascript" src="../public/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="../public/js/jquery.mCustomScrollbar.js"></script>
    <script type="text/javascript" src="../public/lib/slick/slick.min.js"></script>
    <script type="text/javascript" src="../public/js/scrollbar.js"></script>
    <script type="text/javascript" src="../public/js/script.js"></script>
    <title>Personal Dashboard | Lingua2</title>
	
	
	<!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-NYB9FFBL5J"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'G-NYB9FFBL5J');
</script>

<!-- Google Tag Manager -->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','GTM-TSHHJ2LL');</script>
<!-- End Google Tag Manager -->
	
	
	
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

    $query = "SELECT * FROM mentor2009 WHERE orden='" . $identificador2017 . "'"; //seleccionamos todos los campos 

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


	/*
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
	
	*/

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

    <head>
        <!-- <script src="https://kit.fontawesome.com/bb6243346a.js" crossorigin="anonymous"></script> -->
        <title>Language Exchange | Lingua2</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="" />
        <meta name="keywords" content="" />
        <link rel="stylesheet" type="text/css" href="../public/css/animate.css">
        <link rel="stylesheet" type="text/css" href="../public/css/bootstrap.min.css">
        <link rel="stylesheet" type="text/css" href="../public/css/line-awesome.css">
        <link rel="stylesheet" type="text/css" href="../public/css/line-awesome-font-awesome.min.css">
        <link rel="stylesheet" type="text/css" href="../public/css/font-awesome.min.css">
        <link rel="stylesheet" type="text/css" href="../public/css/jquery.mCustomScrollbar.min.css">
        <link rel="stylesheet" type="text/css" href="../public/lib/slick/slick.css">
        <link rel="stylesheet" type="text/css" href="../public/lib/slick/slick-theme.css">
        <link rel="stylesheet" type="text/css" href="../public/css/style.css">
        <link rel="stylesheet" type="text/css" href="../public/css/responsive.css">
        <link rel="stylesheet" href="./css/languages.css" media="all" />
        <style>
            a {
                color: #e65f00;
            }
        </style>
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
    </head>


<body>











    <div class="wrapper">


        <header>
            <div class="container">
                <div class="header-data">
                    <div class="logo">
                        <a href="./me.php" title=""><img src="../public/images/logo-blanco.png" alt=""></a>
                    </div>
                    <!--logo end-->
                    <div class="search-bar" style="width: 160px; margin-left: 20px;">
                        <form action="./u.php?">
                            <input type="text" name="identificador" placeholder="User id # (e.g., 2735)">
                            <button type="submit"><i class="la la-search"></i></button>
                        </form>
                    </div>
                    <!--search-bar end-->
                    <nav>
                        <ul>
						
						<!--
						
						<li>
                                <a href="./partners.php" title="">
                                    <span><i class="fa fa-star" aria-hidden="true" style="font-size: 18px;"></i></span>   
                                    Favs
								</a>
                        </li>
						
						-->
						
                            <li>
                                <a href="../search2/index_paginated.php" title="">
                                    <span><i class="fa fa-users" aria-hidden="true" style="font-size: 18px;"></i></span>   <!-- far fa-address-card -->
                                    Friends
                                    <?php //////////////////////////////////////////// AQUI NOS DICEN CUANTOS MENSAJES NO LEIDOS TENEMOS ////////////////////////////////////////////
                                    /* require('../pms/cpm.class.php');
			$ur_pm = new cpm($ur_userid);
			$ur_new_msg = $ur_pm->getunreadmessages();  */

                                    $row3 = 0;

                                    $userid = $identificador2017;

                                    
									//$sql3 = "SELECT count(*) FROM messages WHERE `to` = '" . $userid . "' && `to_viewed` = '0' && `to_deleted` = '0' ORDER BY `created` DESC";
                                    $sql3 = "SELECT count(*) FROM messages WHERE `to` = '" . $userid . "' && `from` <> '" . $userid . "' &&`to_viewed` = '0' && `to_deleted` = '0' ORDER BY `created` DESC";
									$result3 = mysqli_query($link, $sql3);
                                    //$row3 = mysqli_num_rows($result3);

                                    $fila3 = mysqli_fetch_array($result3);
									
									$nuevos_emails=$fila3[0];

                                    //print_r( $fila3); 

                                    //die($sql3);


                                    //////////////////////////////////////////// FIN CUANTOS MENSAJES NO LEIDOS TENEMOS ////////////////////////////////////////////
                                    ?>

                                </a>
								
								<ul>
									<li><a href="../search2/index_paginated.php" title="">Search for new partners</a></li>
									<li><a href="../bookmarks" title="">Favourite users</a></li>
								</ul>
								
								
								
                            </li>
                            <li>
                                <a href="../pms" title="">
                                    <span><!--<i class="far fa-envelope" aria-hidden="true" style="font-size: 15px;"></i>--><i class="fa fa-solid fa-bolt" aria-hidden="true" style="font-size: 15px;"></i></span>
                                    Alerts (<?php echo $nuevos_emails; ?>)
                                </a>
                                <!-- <ul>
									 <li><a href="companies.html" title="">Companies</a></li>
									<li><a href="company-profile.html" title="">Company Profile</a></li>
								</ul> -->
                            </li>

                            <?php     // estas queries las he copiado del archivo  ..infouser/pendingactions.php
							
							
							


                            $query_vote = "SELECT * FROM couples2009antiguos WHERE (voted_1=0 AND user_id_1='$identificador2017') AND contactado=1 ";
                            $result_vote = mysqli_query($link, $query_vote);
                            $nuevos_vote1 = mysqli_num_rows($result_vote);



                            $query_vote = "SELECT * FROM couples2009antiguos WHERE (voted_2=0 AND user_id_2='$identificador2017') AND (contactado=0 OR contactado=1) ";
                            $result_vote = mysqli_query($link, $query_vote);
                            $nuevos_vote2 = mysqli_num_rows($result_vote);



                            $nuevos_vote_total = $nuevos_vote1 + $nuevos_vote2;


                            ?>
							
							
							
							
							
							
							<li>
								<a href="../trackerproposals/dashboard.php" title="">
									<span><i class='fas fa-chalkboard-teacher'></i></span>
									Lessons
								</a>
								<ul>
									<li><a href="../trackerproposals/received-futureclasses.php" title="">As a teacher</a></li>
									<li><a href="../trackerproposals/sent-futureclasses.php" title="">As a student</a></li>
								</ul>
							</li>
							
							
							
							
							
							
							<li>
                                <a href="../chat" title="">
                                    <span><i class="fas fa-comments" aria-hidden="true" style="font-size: 15px;"></i></span>
                                    Chat
                                </a>
                            </li>
							
							
							
                            <li>
                                <a href="../infouser/pendingactions.php" title="">
                                    <span><i class="fas fa-user-check" aria-hidden="true" style="font-size: 15px;"></i></span>
                                    Actions (<?php echo $nuevos_vote_total; ?>)
                                </a>
                            </li>

                            <?php
							
							
							$total_n_alertas=$nuevos_emails+$nuevos_vote_total;
							
                            //echo "<br><center><a href=\"../events/showallupcomingevents.php\" style=\"color:red; font-size: 20px; font-weight: bold;  text-decoration: underline;\" >See and create events in your city</a></center>";
                            ?>
                            <li>
                                <a href="../events/showallupcomingevents.php" title="">
                                    <span><i class="fas fa-globe-africa" aria-hidden="true" style="font-size: 15px;"></i></span>
                                    Events
                                </a>

                            </li>


                        </ul>
                    </nav>
                    <!--nav end-->
                    <div class="menu-btn">
                        <a href="#" title="" ><i class="fa fa-bars"></i>
						<div  style="color: white; font-weight: bold; float: left; position: relative;top: 15px; right: -55px;
						
						
							width: 27px;
							height: 27px;
							line-height: 30px;
							border-radius: 50%;
							font-size: 15px;
							text-align: center;
							background: red
						
						
						
						"><?php echo "$total_n_alertas"; ?></div>
						</a>
                    </div>
                    <!--menu-btn end-->
                    <div class="user-account">
                        <div class="user-info">
                            <a href="#" title=""> <?php echo $nombre_usuar; ?>&nbsp; </a>
                            
							<!--<img class="profile-min" src="<?php //echo $thumb_nombre;  ?>" alt="" height="30" width="30"> -->
							
							<img class="profile-min" src="<?php echo $thumb_nombre;  ?>?nocache=<?php echo time(); ?>" alt="" height="30" width="30">

                            <i class="la la-sort-down"></i>
                        </div>
                        <div class="user-account-settingss">
                           <!-- <h3>Online Status</h3>
                            
							
							<ul class="on-off-status" hidden>
                                <li>
                                    <div class="fgt-sec">
                                        <input type="radio" name="cc" id="c5">
                                        <label for="c5">
                                            <span></span>
                                        </label>
                                        <small>Online</small>
                                    </div>
                                </li>
                                <li>
                                    <div class="fgt-sec">
                                        <input type="radio" name="cc" id="c6">
                                        <label for="c6">
                                            <span></span>
                                        </label>
                                        <small>Offline</small>
                                    </div>
                                </li>
								
                            </ul>-->
                            <!-- <h3>Custom Status</h3> -->
                            
							<!--
							<div class="search_form" hidden>
                                <form>
                                    <input type="text" name="search">
                                    <button type="submit">Ok</button>
                                </form>
                            </div>
							
							-->
                            <!--search_form end-->
                            <h3>Settings</h3>
                            <ul class="us-links">
							
								<li><a href="../addlanguage/addlanguage.php" title="">Add languages</a></li>
								<li><a href="../addlanguage/deletelanguage.php" title="">Delete languages</a></li>
                                <li><a href="./getgpsposition.php" title="">Update GPS location</a></li>
								<li><a href="./timeshift.php" title="">Update timeshift</a></li>
                                <li><a href="../updatephoto" title="">Change profile photo</a></li>
                                <li><a href="../updateinfo" title="">Edit profile information</a></li>
								
								<?php // <li><a href="../updateinfo/insert_level_offered_language.php" title="">Edit level of offered languages</a></li> ?>	
								
								
								
								<?php if ($tipo_form!='teacher')
									{?>
                                <li><a href="../updateinfo/insert_level_language.php" title="">Edit level of requested languages</a></li>	
								<?php
									}
								?>
								
								<li><a href="../updateinfo/passwordreset.php" title="">Reset password</a></li>
								<li><a href="../recoveryandunregistration/deleterequest.php" title="">Unregister</a></li>
                            </ul>
                            <h3 class="tc"><a href="./logout.php" title="">Logout</a></h3>
                        </div>
                        <!--user-account-settingss end-->
                    </div>
                </div>
                <!--header-data end-->
            </div>
        </header>
        <!--header end-->



        <main>
            <!--FIN NUEVO PERFIL USUARIO-->
			
			<?php
			
			if (!$email_verified)
			{
			?>	
			<div class="alert alert-danger" align="center">
			
               To see all the contents you need to validate your email address. If you did not receive any email click <a style="text-decoration: underline;" href=<?php echo "./verify_email.php" ?> >here</a>.</br>
				
			</div>		
			<?php		
				
				exit(0);
			}
			?>

            <!--ALERTA PARA LOS QUE NO HAN INTRODUCIDO LA UBICACIÓN-->
            <?php
            if ($gpslat11 == 0 and $gpslng11 == 0) {
            ?>
                <div class="alert alert-danger" align="center">
                    <strong>Important!</strong> Provide your location in order to continue. <strong><a href="./getgpsposition.php" style=" text-decoration: underline;"> Add location</a></strong>
                </div>
                <?php
            } 
			
			else if($zonaHoraria=='Antarctica/Casey')
			{
            ?>
                <div class="alert alert-danger" align="center">
                    <strong>Important!</strong> Provide time zone in order to continue. <strong><a href="./timeshift.php" style=" text-decoration: underline;"> Add time zone</a></strong>
                </div>
                <?php
            } 
			
						
			else if ($tipo_form != 'teacher') //if($gpslat11!=0 or $gpslng11!=0)
            {

                //vamos a ver si el usuario normal (que no es profesor) ha enviado ya una solicitud a otro usuario y si no mostramos warning	
                $query110 = "


	SELECT  couples2009antiguos.user_id_1
	FROM couples2009antiguos 
	INNER JOIN mentor2009
	ON mentor2009.orden = couples2009antiguos.user_id_1
	WHERE Email='" . $email_del_usu . "'";

                $result110 = mysqli_query($link, $query110);
                $n_veces_contactante = mysqli_num_rows($result110);


                if (!$n_veces_contactante) {

                ?>
                    <div class="alert alert-warning" align="center">
                        You didn't contact any user yet. Start <a href="./partners.php" style=" text-decoration: underline;"> looking for a language partner</a> now.
                    </div>
                <?php

                }
            }

            //si es un profesor que no ha creado un evento, le sacamos un warning
            else if ($tipo_form == 'teacher') {

                $query17 = "
		SELECT  * 
		FROM eventoslista
		WHERE id_creador=$identificador2017";
                $result17 = mysqli_query($link, $query17);
                $n_ev = mysqli_num_rows($result17);

                if (!$n_ev) {

                ?>
                    <div class="alert alert-warning" align="center">
                        You haven't created any event yet. <strong><a href="../events/showallupcomingevents.php" style=" text-decoration: underline;">Create an event</a> now in order to find customers.</strong>.
                    </div>
            <?php
                }
            }


            ?>