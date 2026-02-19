<?php

require('../files/bd.php');
session_start();

$identificador2017=$_SESSION['orden2017'];

if(empty($identificador2017))
{
	die("fordidden...$identificador2017");
}



		//aqui extraemos el nombre del usuario
		
		$query77="SELECT nombre FROM mentor2009 WHERE orden='$identificador2017' ";
		$result77=mysqli_query($link,$query77);
		if(!mysqli_num_rows($result77))
				die("User unregistered 1.");
		$fila77=mysqli_fetch_array($result77);

		$nombreusu77=$fila77['nombre'];
		
		//die($nombreusu77);






$lat11=$_GET['lat']; //die($lat11);
$lng11=$_GET['lng'];

//if(isset($lat11) AND isset($lat11) )
//{
	if( is_numeric($lat11) AND is_numeric ($lng11) AND $lat11<90 AND $lat11>-90 AND $lng11<180 AND $lng11>-180 )
	{
		$query="UPDATE mentor2009 SET Gpslat='$lat11' , Gpslng='$lng11' WHERE orden='$identificador2017'"; //ponemos Emparejado a un valor por debajo del 0
		//die($query);

		$result=mysqli_query($link,$query); 


		// este if de debajo lo quito porque si el usuario hace un update de algo igual a lo que existe 
		// en la bd salta el error
		
		/*
		if(!mysqli_affected_rows($link))
			die ('Error 445. Contact webmaster@lingua2.eu');
		*/

		//}

		//echo "$lat11  $lng11";
		



//si el usuario ya tiene coordenadas gps se hace un UPDATE, si no, un INSERT














		// AQUI EXTRAEMOS LA CIUDAD MAS CERCANA DE LA TABLA gpscities Y HACEMOS UN UPDATE EN CITY


		$latitud1=$lat11; 
		$longitud1=$lng11;


		$query="
		SELECT 
		gc.city,
		gc.country,

		(acos(sin(radians(gc.lat)) * sin(radians($latitud1)) + 
		cos(radians(gc.lat)) * cos(radians($latitud1)) * 
		cos(radians(gc.lng) - radians($longitud1))) * 6378) 

		AS distanciaPunto1Punto2

		FROM gpscities gc

		WHERE 1


		ORDER BY distanciaPunto1Punto2 

		LIMIT 1


		";
		//die( "<br/><br/>$query<br/><br/>");

		$result=mysqli_query($link,$query);
		if(!mysqli_num_rows($result))
				echo "</br>User unregistered 2.";
		$fila=mysqli_fetch_array($result);

		$city88= $fila['city'];
		$distancia88= round($fila['distanciaPunto1Punto2'],2);
		$country77=$fila['country'];

		
		if(!empty($city88))
		{
			$query3="UPDATE mentor2009 SET Ciudad='$city88', Provincia='$country77' WHERE orden='$identificador2017'"; //ponemos Emparejado a un valor por debajo del 0
			//die($query3);
			
			$result3=mysqli_query($link,$query3); 
			
			//OJO CON LA COMPROBACION DE DEBAJO. SI SE ACTUALIZA CON EL MISMA CIUDAD EL CAMPO NO CAMBIA Y LAS AFFECTED ROWS SON 0??????
			//if(!mysqli_affected_rows($link))
			//	die ('Error 447. Contact webmaster@lingua2.eu');
		}
		
echo "<html><script>window.location.href='./me.php';</script></html>";
//header("Location: ./me.php ");


	}
	//echo "<br><br>We didn't detect the GPS coordinates automatically.<br><br> Please click on the button to update your location.<br><br>";
	




?>


<!DOCTYPE html>
<html>
<head>

<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-139626327-1"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('js', new Date());
            gtag('config', 'UA-139626327-1');
        </script>

<meta charset="utf-8">
<title>My Location | Lingua2</title>
<script>
function getLocation()
{
  // Check whether browser supports Geolocation API or not
  if (navigator.geolocation) { // Supported
  
    // To add PositionOptions
	
	navigator.geolocation.getCurrentPosition(getPosition);
  } else { // Not supported
	alert("Oops! This browser does not support HTML Geolocation.");
  }
}
function getPosition(position)
{

/*
  document.getElementById("location").innerHTML = 
	  "Latitude: " + position.coords.latitude + "<br>" +
	  "Longitude: " + position.coords.longitude;
*/
	  //added by Aitor
	  

	  
	  window.location.href =  window.location + "?lat=" + position.coords.latitude + "&lng=" + position.coords.longitude
	  
}

// To add catchError(positionError) function

</script>
</head>
<body>







<?php require("../templates/header_simplified.html"); ?>
<main>

<div class="main-section">
    <div class="container">
        <div class="main-section-data">
            <div class="row">							
				<div class="col-lg-3 col-md-4 pd-left-none no-pd"></div>
					<div class="col-lg-6 col-md-7 no-pd" >
						<div	class="main-ws-sec" >
                            <div class="top-profiles ">
                                <div class="pf-hd">
                                    <h3><?php echo "$nombreusu77"; ?>: add your city to find friends nearby</p>
                                    </h3>
                                </div>
									<div class="post-bar" style="margin: 10px 10px 10px 10px; width: 96%">
                                        <div class="post_topbar">
                                            <div class="usy-dt">
											
												IMPORTANT: Add or update your current city in order to find language partners in your area. </br></br></br></br>
											
												<center>
												
												
													<button onclick="getLocation()" style="background-color: #e65f00;  border: none;
  color: white;
  padding: 15px 32px;
  text-align: center;
  border-radius: 10px;
  ">
													Find my city</button>
													<p id="location"></p>
												
													
												</center>
												</br></br>
												
	</div>	</div>	</div>	</div>	</div>	</div>	</div>	</div>	</div>	</div>								
	

</main>	
</body>
</html>
