<?php

session_start();


require('../templates/header_simplified.html');

require('../files/idiomasequivalencias.php');

require('../files/bd.php');



$identificador2017 = $_SESSION['orden2017'];
$_SESSION['idusuario2019'] = $identificador2017; //esto es solo por si hay el usuario quiere cambiar la foto de perfil
$identificador_usu_buscado = $identificador2017;

//mirar que no esté el nick repetido

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

$availability100 = $fila['Disponibilidadcomentarios'];
$othercomments100 = $fila['Otroscomentarios'];






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
    <title>Search| Lingua2</title>



    <link rel="stylesheet" type="text/css" href="../public/css/animate.css">
    <link rel="stylesheet" href="../public/css/bootstrap-4.2.1.css">
    <link rel="stylesheet" type="text/css" href="../public/css/jquery.range.css">
    <link rel="stylesheet" type="text/css" href="../public/css/line-awesome.css">
    <link rel="stylesheet" type="text/css" href="../public/css/line-awesome-font-awesome.min.css">

    <link rel="stylesheet" type="text/css" href="../public/css/slick.css">
    <link rel="stylesheet" type="text/css" href="../public/css/slick-theme.css">

    <link rel="stylesheet" type="text/css" href="../public/css/responsive.css">

    <link rel="stylesheet" type="text/css" href="../public/css/style.css">
    <link rel="stylesheet" href="../public/css/font-awesome.4.7.0.css">

    <link rel="stylesheet" href="../user/css/languages.css" media="all" />

    <!-- ***** Añadido: eliminado <STYLE> y trasladado a search.css -->
    <link rel="stylesheet" href="search.css" media="all" />

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
    //mirar que no esté el nick repetido

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

    //php8 division por cero no aceptada, por eso ponemos un if
    if ($n_comentarios != 0)
        $porcentaje_positivos = round($n_comentarios_positivos * 100 / $n_comentarios);

    ?>

    <div class="main-section">
        <div class="container">
            <div class="main-section-data">
                <div class="row">

                    <!--     <div class="col-lg-3 col-md-4 pd-left-none no-pd"> -->

                    <?php

                    //columna de la izquierda
                    require('./search_dashboard.php');
                    ?>

                    <!-- col-lg-9    indica en este caso que ocupa 9 columnas. si ponemos 0, pues ocupa cero columnas -->

                    <div class="col-lg-9 col-md-7 no-pd">
                        <div class="main-ws-sec">

                            <div class="posts-section">
                                <div id="seccion_teach" class="post-bar">
                                    <div class="epi-sec">
                                    </div>
                                    <div class="container-fluid m-0 p-0">
                                        <?php
                                        // echo $identificador2017;
                                        //job_descp d-flex flex-column align-items-center text-center 
                                        require('./search_results.php');
                                        $query = "SELECT m.nombre AS mentor_name, m.orden, m.Ciudad
                                        FROM bookmarkedusers bu
                                        JOIN mentor2009 m ON bu.userwhoissaved = m.orden
                                        WHERE bu.userwhosaves = '" . $identificador2017 . "'
                                        ORDER BY bu.savedtime DESC";                                        // Add this where you want to display the bookmarked users
                                        if ($stmt = $link->prepare($query)) {
                                            $stmt->execute();
                                            $result = $stmt->get_result();
                                            if ($result->num_rows > 0) {
                                                while ($row = $result->fetch_assoc()) {
                                                    // Get user details for each bookmarked user
                                                    $bookmarked_user_id = $row['orden'];
                                                    $nombre_usuar = $row['mentor_name'];
                                                    $ciudad = $row['Ciudad'];

                                                    // Process the name to get first name
                                                    $arr = explode(' ', trim($nombre_usuar));
                                                    $nombre_usuar = $arr[0];
                                                    $nombre_usuar = ucfirst(substr($nombre_usuar, 0, 13));

                                                    // Get profile image
                                                    $thumb_nombre = $bookmarked_user_id;
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

                                                    // Count evaluations for this user
                                                    $query_eval = "SELECT COUNT(*) as total FROM comentarios WHERE id_aludido='$bookmarked_user_id' AND censurado=0";
                                                    $result_eval = mysqli_query($link, $query_eval);
                                                    $row_eval = mysqli_fetch_assoc($result_eval);
                                                    $n_comentarios = $row_eval['total'];

                                                    // Count positive evaluations
                                                    $query_pos = "SELECT COUNT(*) as total FROM comentarios WHERE id_aludido='$bookmarked_user_id' AND censurado=0 AND rating=1";
                                                    $result_pos = mysqli_query($link, $query_pos);
                                                    $row_pos = mysqli_fetch_assoc($result_pos);
                                                    $n_comentarios_positivos = $row_pos['total'];

                                                    // Calculate percentage of positive evaluations
                                                    $porcentaje_positivos = ($n_comentarios > 0) ? round($n_comentarios_positivos * 100 / $n_comentarios) : 0;

                                                    // Use the function to generate and output the user card
                                                    echo generateUserCard(
                                                        $bookmarked_user_id,
                                                        $nombre_usuar,
                                                        $ciudad,
                                                        $thumb_nombre,
                                                        $n_comentarios,
                                                        $porcentaje_positivos
                                                    );
                                                }
                                            } else {
                                                echo '<div class="col-12"><p>You have no bookmarked users yet.</p></div>';
                                            }

                                            $stmt->close();
                                        } else {
                                            echo '<div class="col-12"><p>Error executing query.</p></div>';
                                        }


                                        ?>
                                    </div>
                                        <!-- <div class="section-divider"></div> -->
                                        <div class="job_descp ">

                                        <h3>I am able to teach</h3>
                                        <ul class="skill-tags">
                                            <?php if (!empty($idof1a)) { ?>
                                                <li><a href="#" title="" style="text-align:center;"> <img class="language language-<?php echo  $idof1a; ?> " />
                                                        </br><?php echo  $idiomas_equiv["$idof1a"]; ?></a></li>
                                            <?php } ?>
                                            <?php if (!empty($idof2a)) { ?>
                                                <li><a href="#" title="" style="text-align:center;"> <img class="language language-<?php echo  $idof2a; ?> " />
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
                                        <div class="job_descp">
                                            <h3>I want to learn</h3>


                                            <!-- <ul class="skill-tags"> -->

                                            <ul>
                                                <?php if (!empty($idde1a)) { ?> <li><a href="#" title="" style="text-align:center;"> <img class="language language-<?php echo  $idde1a; ?> " />
                                                            </br><?php echo  $idiomas_equiv["$idde1a"]; ?></a></li>
                                                <?php
                                                }
                                                ?>
                                                <?php if (!empty($idde2a)) { ?> <li><a href="#" title="" style="text-align:center;"> <img class="language language-<?php echo  $idde2a; ?> " />
                                                            </br><?php echo  $idiomas_equiv["$idde2a"]; ?></a></li>
                                                <?php
                                                }
                                                ?>
                                                <?php if (!empty($idde3a)) { ?> <li><a href="#" title="" style="text-align:center;"> <img class="language language-<?php echo  $idde3a; ?> " />
                                                            </br><?php echo  $idiomas_equiv["$idde3a"]; ?></a></li>

                                                <?php
                                                }
                                                ?>
                                            </ul>
                                        </div>

                                    <?php         } else {

                                        // echo "<div style=\"font-size:30px;text-align:center;position:relative;top:-80px;height: 90px;\">$idiomas_demandados</div>";

                                    ?>


                                        <div class="epi-sec">

                                        </div>
                                        <div class="job_descp">
                                            <h3>My hourly rate is</h3>

                                            <?php echo "<div style=\"font-size:30px;text-align:center;color:#666666;\">$idiomas_demandados</div>";     ?>

                                        </div>
                                    <?php    }


                                    ?>
                                </div>
                                <!--post-bar end-->


                                <?php
                                //numero maximo de evaluaciones que queremos mostrar
                                $num_max_ev_mostradas = 3;



                                ?>



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






                    <div class="col-lg-0 pd-right-none no-pd ">
                        <div class="right-sidebar">





                        </div>

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

            function resize_movil() {
                $("#seccion_teach_clone").css("margin-bottom", "0px");
                $("#events_clone").css("margin-bottom", "0px");

                if (screen.width < 768) {
                    //$("#seccion_teach").attr("hidden", true);
                    //$("#seccion_teach_clone").attr("hidden", false);
                    $("#events").attr("hidden", true);
                    $("#events_clone").attr("hidden", false);
                    $("#my_events").attr("hidden", true);
                    $("#my_events_clone").attr("hidden", false);
                } else {
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
            img.onclick = function() {
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
