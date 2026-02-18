<?php

require('../files/bd.php');

session_start();
$identificador2017=$_SESSION['orden2017'];


$id_evento=$_GET['idev'];
?>


<head>
<script src="https://kit.fontawesome.com/bb6243346a.js" crossorigin="anonymous"></script>
<title>Lingua2 Event <?php echo "$id_evento"; ?> </title>
<link rel="stylesheet" href="../user/css/languages.css" media="all" />
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



<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-139626327-1"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('js', new Date());
            gtag('config', 'UA-139626327-1');
        </script>

		<link rel="stylesheet" type="text/css" href="../public/css/responsive.css">
		<style>
            a {
                color: #e65f00;
            }
        </style>



	<script type="text/javascript" src="../public/js/jquery.min.js"></script>
    <script type="text/javascript" src="../public/js/popper.js"></script>
    <script type="text/javascript" src="../public/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="../public/js/jquery.mCustomScrollbar.js"></script>
    <script type="text/javascript" src="../public/lib/slick/slick.min.js"></script>
    <script type="text/javascript" src="../public/js/scrollbar.js"></script>
    <script type="text/javascript" src="../public/js/script.js"></script>

	

</head>





<body>

<?php
require_once("../templates/header_simplified.html");
?>

<style>
	
			@media(max-width: 991px){
				.comment_box{
					width: 85%;
				}

				.comment_box input{
					width: 81%;
				}
			}

			@media(max-width: 576px){
				.post-comment{
					display: flex;
				}

				.cm_img{
					width: auto;
				}

				.comment_box{
					display: flex;
				}

				.comment_box input{
					width: 60%;
				}
			}

	</style>

<main>
<div class="main-section">
<div class="container">
<div class="main-section-data">
<div class="row" style="justify-content:center;">
<div class="col-lg-6 col-md-8 no-pd">
<div class="main-ws-sec">




<br>
<h1 style="color: dimgrey; font-size:36;">Event Information</h1>
<br><br>

<div class="posts-section">
<div class="posty">
<div class="post-bar no-margin p-3">
<div class="job-description">

<?php



if(!isset($identificador2017))
{
	die("You must be logged in in order to see this page");
}

$enviar1=$_POST['enviar'];
$mensaje1=$_POST['mensaje']; $mensaje1=preg_replace("/[^A-Za-z0-9 ]/", '', $mensaje1);



if(!empty($enviar1))
{
	if(empty($mensaje1) OR empty($id_evento)  OR  empty($mensaje1))
	{
		die('error 502. Contact webmaster@lingua2.eu');
	}
		
	$query4="INSERT INTO eventcomments (	author, idevent, comment)
	VALUES('$identificador2017','$id_evento','$mensaje1')";
	
	//echo "$<br>$query <br>";  
	
	$result4=mysqli_query($link,$query4);
	//Comprobar que ha funcionado----------------------------------
	if(!mysqli_affected_rows($link))
		die ('\n<br>\n\nThere was an error and your message was not submitted');   

}





		$query="SELECT * FROM eventoslista WHERE Id='$id_evento'"; 
		
		//die($query);
		$result=mysqli_query($link,$query);
		//$nuevos=mysqli_num_rows($result);
		
		//print_r($result);

		if( ! mysqli_num_rows($result))
		{
			die('<br/>error evento!!!<br/>');
		}
		
		$fila=mysqli_fetch_array($result);


		$id_creador=$fila['id_creador'];
		
		
		require('../files/idiomasequivalencias.php');
		
		$lengua1=$idiomas_equiv["{$fila['Idioma']}"];
		$lengua1=substr($lengua1,0,14);
	
	
		
		$hora_creacion=$fila['Horacreacion'];
		$nombre_ev=$fila['event_name'];
		$descr_ev=$fila['event_desc'];
		$hora_inicio=$fila['start_time'];
		$ciudad_ev=$fila['city'];
		$location_ev=$fila['location']; 
		$broadc=$fila['Broadcasted']; 

echo "<br/>Title: $nombre_ev ";

if($broadc)
{
	echo "<img src=\"../images/recommended.png\" alt=\"recommended\" height=\"30\" />";
}

echo "

<br/><br/>Type: $lengua1 



<br/><br/>Description: $descr_ev <br/><br/>Start time:  $hora_inicio <br/><br/>Location (approximate): $ciudad_ev <br/><br/>Exact location: $location_ev<br/><br/>Created by: <br/><br/>";



		$query100="SELECT Nombre, fotoext FROM mentor2009 WHERE orden='$id_creador'"; 
		
		//die($query100);
		$result100=mysqli_query($link,$query100);
		//$nuevos=mysqli_num_rows($result);
	
		if( ! mysqli_num_rows($result100))
				die('<br/>error!!!<br/>');

		$fila100=mysqli_fetch_array($result100);
		

		$nombre_creador=$fila100['Nombre'];
		
		$myvalue = $nombre_creador;
		$arr = explode(' ',trim($myvalue));
		$nombre_creador=$arr[0];
		
		$nombre_creador=substr($nombre_creador,0,14);
		
		
		$ext_foto_creador=$fila100['fotoext'];


		$foto_creador="../uploader/upload_pic/thumb_".$id_creador.'.'."$ext_foto_creador";
		
		if(!file_exists($foto_creador))
		{
			$foto_creador="../uploader/default.jpg";
		}
		
		
		echo "<div style='float: left; margin: 0 2px 0 0;display:flex'>";
		echo "<div class='bg-img'>";
		echo "<a href=\"../user/u.php?identificador=$id_creador \" >";
		echo "<img src={$foto_creador} height=\"40\" width=\"40\" border=\"0\" />"; 
		//echo "<img src='http://via.placeholder.com/50x50'>"; 
		echo "</a>";
		echo "</div>";
		echo "<div>";
		echo "<br/>";
		echo "<p style='padding-left:5px'> $nombre_creador on $hora_creacion <br/><br/>";
		echo "</div>";
		echo "</div><br/><br/><br/>";
		
		///////////// formulario mensajes ///////////////////////////////////////////////////////
		
		//echo "<br/><br/>";
		
		?>
		</div>
		</div>
		<div class="comment-section my-3">
		<div class="post-comment">
		<div class="cm_img">
		<?php
			echo "<img src={$foto_creador} height=\"40\" width=\"40\" border=\"0\" />"; 
		?>
		<!--<img src="http://via.placeholder.com/40x40" alt="">-->
		</div>
		<div class="comment_box">
		<form name="formmentor" ENCTYPE="multipart/form-data" ACTION="<? echo $PHP_SELF?>" METHOD="POST" target="_self">
		

	<?php //	<img src="./logos/uqe.png" align="middle"> 
	//<p class="subtitulo" style="margin-top:-10px; margin-left:20px;" >Publish the message WITH MORE DETAILS HERE!!</p>

	?>
	
	
	<?php
	
	
		$query200="SELECT Nombre FROM mentor2009 WHERE orden='$identificador2017'"; 
		
		//die($query100);
		$result200=mysqli_query($link,$query200);
		//$nuevos=mysqli_num_rows($result);
	
		if( ! mysqli_num_rows($result200))
				die('<br/>error!!!<br/>');
	
	
		$fila200=mysqli_fetch_array($result200);
	
		$nombre_creador=$fila200['Nombre'];
	
		$myvalue = $nombre_creador;
		$arr = explode(' ',trim($myvalue));
		$nombre_creador=$arr[0];
		
		$nombre_creador=ucfirst(substr($nombre_creador,0,13)); 
		
	?>
	
		
		<input type="text" name="mensaje" id="mensaje" size="50" maxlength="255" placeholder="Post a comment">	
		<button type="submit" name="enviar" value="Publish message">Send</button>
	</form>	
	</div>
	</div>
	<br><br><br>
	<br><br>
<div class="comment-list">
<ul>
<?php		
		
		///////////// leer mensajes del event  ////////////////
		
		
		$query1="
		SELECT  * 
		FROM eventcomments 
		WHERE idevent='$id_evento'
		ORDER BY messagetime DESC";
		$result1=mysqli_query($link,$query1);
		$n_comentarios=mysqli_num_rows($result1);
		//if($n_comentarios)
		//	echo "<center><h1 style=\"color:white;\">Evaluations</h1></center></br></br></br>"; //style=\"color:#4D4D4D;\"

		
		for($i=0;$i<$n_comentarios;$i++) 
		{
			$fila1=mysqli_fetch_array($result1); 
			$autor=$fila1['author'];

			$comment1=$fila1['comment'];
			$time_creation=$fila1['messagetime'];
			
			
			
			$query300="SELECT Nombre FROM mentor2009 WHERE orden='$autor'"; 
			
			//die($query100);
			$result300=mysqli_query($link,$query300);
			//$nuevos=mysqli_num_rows($result);
		
			if( ! mysqli_num_rows($result200))
					echo('User missing');
		
		
			$fila300=mysqli_fetch_array($result300);
		
			$nombre_creador=$fila300['Nombre'];
		
			$myvalue = $nombre_creador;
			$arr = explode(' ',trim($myvalue));
			$nombre_creador=$arr[0];
			
			$nombre_creador=ucfirst(substr($nombre_creador,0,13)); 
		
		
		echo "<li>";
		echo "<div class='comment-list'>";
			echo "<div class='bg-img'>";
				//echo "<img src='http://via.placeholder.com/40x40'>";
				echo "<img src={$foto_creador} height=\"40\" width=\"40\" border=\"0\" />"; 
			echo "</div>";
			echo "<div class='comment'>";
				echo "<h3>$nombre_creador</h3>";
				echo "<span>$time_creation</span>";
				echo "<p>$comment1</p>";
				//echo "<a href='#' title='' class='active'><i class='fa fa-reply-all'></i>Reply</a>";
			echo "</div>";
		echo "</div>";
		echo "</li>";
		//echo "$nombre_creador  -  $comment1 -   $time_creation <br/><br/>";
		
		}
		
		
?>
</ul>



</br>
</div>


</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</main>

</div>
</div>
</body>

