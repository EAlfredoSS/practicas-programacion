<?php // mi c&oacute;digo


//anadir campo comentarios a mentor2009
//if empty c1 salimos para evitar que se mofiquen los que no tengan codigo
//poner que no sera posible modificar el voto una vez enviado
//poner google analytics 


require('../files/bd.php');

if( !empty( $_POST['enviar'] )	) 
{
	$rating1=$_POST["rating"]; 
	
	$comment_user=$_POST['comment_user'];  			//si pongo el $_POST no funciona???
	
	//die("comment: $comment_user");
	
	$comentario_autor=$comment_user; 
	
	$c1=$_GET['c'];
	
	if( empty( $rating1 ) )
		die('You have to select Positive, Neutral, Negative or No Answer. Click BACKWARDS on your browser.');
	if( empty($comment_user) )
		die('You have to write a little comment about your conversation friend. Click BACKWARDS on your browser.');
	if( empty($c1) )
		die('Code empty. Contact webmaster.');
		
	$query1="SELECT * FROM couples2009antiguos WHERE code_1='$c1' AND voted_1=0"; //seleccionamos todos los campos 
	$result1=mysqli_query($link,$query1);
	if( mysqli_num_rows($result1))
	{	
		$fila1=mysqli_fetch_array($result1);
		$ide_autor=$fila1['user_id_1'];
		$ide_aludido=$fila1['user_id_2'];
		$valoracion=$rating1;
		$tiempo_act=time();
	
		
		$query2="INSERT INTO comentarios (id_autor,id_aludido,rating,comment,direccion,horacreacion)
		VALUES('$ide_autor','$ide_aludido','$valoracion','$comentario_autor',1,'$tiempo_act')"; 
		
		//die($query2);
		
		$result2=mysqli_query($link,$query2);
		//Comprobar que ha funcionado----------------------------------
		if(!mysqli_affected_rows($link))
			die ('Error en comentarios');
		
	
			
		//marcamos como que ya ha votado
		$query22="UPDATE couples2009antiguos SET voted_1=1 WHERE code_1='$c1'";
		$result22=mysqli_query($link,$query22);
		if(!mysqli_affected_rows($link))
		  die('Error2.1');
		  
		
		$query12="SELECT * FROM mentor2009 WHERE orden='$ide_autor' "; //seleccionamos todos los campos 
		$result12=mysqli_query($link,$query12);
		if( mysqli_num_rows($result12))
		{	
			$fila12=mysqli_fetch_array($result12);
			$nombre_autor=$fila12['nombre'];
		} 
		
		$query12="SELECT * FROM mentor2009 WHERE orden='$ide_aludido' "; //seleccionamos todos los campos 
		$result12=mysqli_query($link,$query12);
		if( mysqli_num_rows($result12))
		{	
			$fila12=mysqli_fetch_array($result12);
			$nombre_aludido=$fila12['nombre'];
			$codigo_aludido=$fila12['Codigoborrar'];
			$email_aludido=$fila12['Email'];
		} 
		

		
		//envio de email template
		require('../emailtemplates/email.php'); 

		//Send email to user containing username and password
		//Read Template File 
		$emailBody = readTemplateFile("../emailtemplates/template_avisoevaluado.html"); 
				
		//Replace all the variables in template file
		$emailBody = str_replace("#username#",$nombre_aludido,$emailBody);
		$emailBody = str_replace("#evaluador#",$nombre_autor,$emailBody);
		$emailBody = str_replace("#codigo_1#",$codigo_aludido,$emailBody); 

		//Send email 
		$emailStatus = sendEmail ("Lingua2 Ratings", "evaluations@languageexchanges.com", $email_aludido, "$nombre_autor has just evaluated you", $emailBody);  
		$emailStatus = sendEmail ("Lingua2 Ratings", "evaluations@languageexchanges.com", "evaluations@languageexchanges.com", "$nombre_autor has just evaluated you", $emailBody);   
	}	

	else
	{
	
		$query3="SELECT  * FROM couples2009antiguos WHERE code_2='$c1' AND voted_2=0";
		$result3=mysqli_query($link,$query3);
		if(mysqli_num_rows($result3))
		{
			//extraemos los datos de la pareja e insertamos informacion en tabla comentarios 
			$fila3=mysqli_fetch_array($result3);
			$ide_autor=$fila3['user_id_2'];
			$ide_aludido=$fila3['user_id_1']; 
			$valoracion=$rating1;
			$tiempo_act=time();

			$query4="INSERT INTO comentarios (id_autor,id_aludido,rating,comment,direccion,horacreacion) VALUES ('$ide_autor','$ide_aludido','$valoracion','$comentario_autor',2,'$tiempo_act')";
			$result4=mysqli_query($link,$query4);
			if(!mysqli_affected_rows($link))
			  die('Error');
			//marcamos como que ya ha votado
			$query44="UPDATE couples2009antiguos SET voted_2=1 WHERE code_2='$c1' ";
			$result44=mysqli_query($link,$query44);
			if(!mysqli_affected_rows($link))
			  die('Error2.2');
			//avisamos a la persona evaluada con un email
			
			
			$query12="SELECT * FROM mentor2009 WHERE orden='$ide_autor' "; //seleccionamos todos los campos 
			$result12=mysqli_query($link,$query12);
			if( mysqli_num_rows($result12))
			{	
				$fila12=mysqli_fetch_array($result12);
				$nombre_autor=$fila12['nombre'];
			} 
			
			$query12="SELECT * FROM mentor2009 WHERE orden='$ide_aludido' "; //seleccionamos todos los campos 
			$result12=mysqli_query($link,$query12);
			if( mysqli_num_rows($result12))
			{	
				$fila12=mysqli_fetch_array($result12);
				$nombre_aludido=$fila12['nombre'];
				$codigo_aludido=$fila12['Codigoborrar'];
				$email_aludido=$fila12['Email'];
			} 
			
			  
			  
			//envio de email template
			require('../emailtemplates/email.php'); 

			//Send email to user containing username and password
			//Read Template File 
			$emailBody = readTemplateFile("../emailtemplates/template_avisoevaluado.html"); 
					
			//Replace all the variables in template file
			$emailBody = str_replace("#username#",$nombre_aludido,$emailBody);
			$emailBody = str_replace("#evaluador#",$nombre_autor,$emailBody);
			$emailBody = str_replace("#codigo_1#",$codigo_aludido,$emailBody); 

			//Send email 
			$emailStatus = sendEmail ("Lingua2 Ratings", "evaluations@languageexchanges.com", $email_aludido, "$nombre_autor has just evaluated you", $emailBody);  
			$emailStatus = sendEmail ("Lingua2 Ratings", "evaluations@languageexchanges.com", "evaluations@languageexchanges.com", "$nombre_autor has just evaluated you", $emailBody);   
		}
		else
		{
			die ("You can only vote once.");
		}
	}
header("Location: ./corr.php");
die("--");
}
?>
<html>







<script async src="https://www.googletagmanager.com/gtag/js?id=UA-139626327-1"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('js', new Date());
            gtag('config', 'UA-139626327-1');
        </script>
<head>
<script src="https://kit.fontawesome.com/bb6243346a.js" crossorigin="anonymous"></script>
<title>Lingua2 Evaluate Partner</title>
<!-- Custom Theme files -->
<link href="css/style.css" rel="stylesheet" type="text/css" media="all"/>
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
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>

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






<style type="text/css">
body {background: white;color:blue;}
.body {font-family:Trebuchet MS;font-size:13px;color:black;}
.body a {color:blue;text-decoration:none;}
.body a:hover {text-decoration:none;color:orange;}

.head {font-size:24px;font-weight:bold;font-family:arial,Trebuchet MS;color:black;}
</style>



<script language="JavaScript"> 

function commentuser_blank()
{
 		document.getElementById('commentuser1').value="";
}
 

function fill_commentuser(f)
{	
	if(f=="")
 	{
		document.getElementById('commentuser1').value="Tell us about your meeting and your partner..."; 
		//esto es cuando clicas en otra parte de la pantalla
	}  
	else 
	{
		document.getElementById('commentuser1').value=f;
	}
}


function validate()
{
	com=document.getElementById('commentuser1').value;  
			
	if(com=="Tell us about your meeting and your partner...")
	{ 
		document.getElementById('commentuser1').focus();
		document.getElementById('commentuser1').style.borderColor="red";
		return false;
	}	
	
	else if(com=="Tell us about your meeting and your partner...")
	{
		document.getElementById('commentuser1').focus();
		document.getElementById('commentuser1').style.borderColor="red";
		return false ;
	}

	var option = getRVBN('rating'); 
	//Check if no radio is selected
	if(option=='')
	{
		alert("You must fill all the fields");
		return false;
	}
}

$(document).ready(function()
{
	$("#commentuser1").keyup(function()
	{
	
		var box=$(this).val();
		var main = box.length *100;
		var value= (main / 255);
		var count= 255 - box.length;

		if(box.length <= 255)
		{
			$('#commentuser1_count').html(count + "Characters left");
		}
		else
		{
			alert('Full');
		}
		return false;
	});

});

function question1 () {

var option = getRVBN('rating'); 

//Check if no radio is selected
if(option==''){
alert("You must fill all the fields");
return false;
}
}                 

function getRVBN(rName) {
var radioButtons = document.getElementsByName(rName);
for (var i = 0; i < radioButtons.length; i++) {
	if (radioButtons[i].checked) return radioButtons[i].value;
}
return '';
}

</script>


<title>Lingua2 Evaluation</title>
</head>
<body>



<?php
//queremos saber el nombre de la persona a la que vamos a evaluar para que lo vea el usuario

$c=$_GET['c'];

$query6="SELECT * FROM couples2009antiguos WHERE code_1='$c' OR code_2='$c' "; //seleccionamos todos los campos 
$result6=mysqli_query($link,$query6);
$fila6=mysqli_fetch_array($result6);
$em1=$fila6['id_1'];
$em2=$fila6['id_2'];

$query7="SELECT  * FROM mentor2009 WHERE Email='$em1' ";
$result7=mysqli_query($link,$query7);
$fila7=mysqli_fetch_array($result7);
$nombre1=$fila7['nombre'];


$query8="SELECT  * FROM mentor2009 WHERE Email='$em2' ";
$result8=mysqli_query($link,$query8);
$fila8=mysqli_fetch_array($result8);
$nombre2=$fila8['nombre'];

$pareja="$nombre1 and $nombre2";

?>


<?php require("../templates/header_simplified.html"); ?>


<div class="main-section">
    <div class="container">
        <div class="main-section-data">
            <div class="row" style="display:flex;justify-content:center;">							
					<div class="col-lg-6 col-md-7 no-pd" style="justify-content: center" >
						<div class="main-ws-sec" >
                            <div class="top-profiles ">
                                <div class="pf-hd" style="text-align:center;">
                                    <h3 style="font-size: 30px;float:none;">You are rating your conversation partner</h3>
                                </div>
									<div class="post-bar" style="margin: 10px 10px 10px 10px; width: 96%; text-align:center;">
                                        <div class="post_topbar">




<span style="color: dimgrey;">We need your opinion for this meeting: </span>

</br></br></br>

<p style="font-weight: bold; font-size: 100%;">

Meeting between <? echo $pareja; ?>

</p>
</br></br></br>


<form name="ratingform"  ENCTYPE="multipart/form-data" ACTION="<? echo $PHP_SELF?>" METHOD="POST" onsubmit="return validate();" >

<div style="display:flex;flex-direction:column;" >
<div>
<input type="radio" name="rating" value="1"> <span style="color: dimgrey;">Positive</span> &#128077; &nbsp;
</div><br><div>
<input type="radio" name="rating" value="2"> <span style="color: dimgrey;">Neutral</span> &#11093; &nbsp;
</div><br><div style="margin-left:1%">
<input type="radio" name="rating" value="3"> <span style="color: dimgrey;">Negative</span> &#128078; &nbsp;
</div><br><div style="margin-left:20%;">
<input type="radio" name="rating" value="4"> <span style="color: dimgrey;">No answer/ No contact</span> &#8987; &nbsp;
</div>
</div>


</BR></BR></BR>


<input TYPE="text" class="form-control" style="width:100%;" onfocus=commentuser_blank();  onblur=fill_commentuser(this.value); id="commentuser1" value="Tell us about your meeting and your partner..."  NAME="comment_user" MAXLENGTH="255" style="border: 1;"><br>
<div id="commentuser1_count" style="color: dimgrey;">255 Characters left</div>
</span>
</br></br>

<input type="hidden" NAME="c1" value="<? echo $c; ?>"> 

<br><br>
<input type="submit" name="enviar" value="Evaluate your partner" style="background-color: #E65F00; border: white; font-weight: bold; font-size: 13px; color: white; height: 40px; width:100%;">

</br></br></br></br>

</FORM>



</div></div></div></div></div></div></div></div></div>


</main>
</body>
</html>