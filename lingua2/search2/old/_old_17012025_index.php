<?php
session_start();

$identificador2017 = $_SESSION['orden2017'];

$_SESSION['idusuario2019'] = $identificador2017; //esto es solo por si hay el usuario quiere cambiar la foto de perfil
//mirar que no est� el nick repetido

//echo "id: $identificador2017";

if( !is_numeric( $identificador2017 ) )
	die ("You are not logged in. $identificador2017 ");


//require("../templates/header_simplified.html");
//<link rel="stylesheet" type="text/css" href="../public/css/bootstrap.min.css">




require('../files/bd.php');


// extract the lat and lng of the user who is searching


$query_user1 = "SELECT * FROM mentor2009 WHERE orden=$identificador2017";

$result_user1 = mysqli_query($link, $query_user1);
if (!mysqli_num_rows($result_user1))
    die("<br/>.........No user......<br/>");

//$affected_users_111=mysqli_num_rows($result_user1);


$fila_user1 = mysqli_fetch_array($result_user1);


$latitud1=$fila_user1['Gpslat'];
$longitud1=$fila_user1['Gpslng'];

//echo "   $latitud1    ---   $longitud1     ";


// FIN ---  extract the lat and lng of the user who is searching------



//si el usuario no ha introducido coordenadas sacamos mensaje de alerta







// si el usuario no ha introducido coordenadas ponemos las de Greenwhich Londres
if ($latitud1==0 AND $longitud1==0)
{
	$latitud1=51.477928;
	$longitud1=0;
	
	
	?>
	
	
	<br/><br/>
	<div class="alert alert-danger" align="center">
	   You have not indicated your city. Insert your city now <a style="text-decoration: underline;" href=<?php echo "../user/getgpsposition.php" ?> >here</a>, 
	   otherwise you will see London, UK as your default city.</br>
	</div>	
	<br/><br/>
	
	<?php
	
	
	
	
}


$partnertype=$_GET['partner'];
$userislearning=$_GET['learns'];
$useristeaching=$_GET['teaches'];
$minimumlevel=$_GET['min_level'];
$maximumlevel=$_GET['max_level'];

// recogemos los valores multiples de las organizaciones

$organizationslist=array();
foreach ($_GET['orgs'] as $organi)
{
	array_push($organizationslist,$organi);
}
//print_r($organizationslist);


$ismale=$_GET['male'];
$isfemale=$_GET['female']; 


$sexo_query='';
if($ismale=='on')
	$sexo_query="Sexo='M'";
if($isfemale=='on')
	$sexo_query="Sexo='F'";
if($ismale=='on' AND $isfemale=='on')
	$sexo_query="Sexo='M' OR Sexo='F'";



//echo "level: $minimumlevel ----- $maximumlevel </br>";




$where_clause_student="Pais<>'teacher' AND ($sexo_query) ";

if (!empty($userislearning))
{
		$where_clause_student.=" AND (Idiomaof1='$userislearning' OR Idiomaof2='$userislearning' OR Idiomaof3='$userislearning')";
}


//busqueda si hemos seleccionado un idioma que queremos enseñar

if (!empty($useristeaching))
{
	
	//busqueda por nivel si está seleccionada
	if($minimumlevel!=0 AND $maximumlevel!=0)
	{
		$where_clause_student.=" AND 
		
		(
		
		(Idiomadem1='$useristeaching' AND Idiomadem1_level>=$minimumlevel AND Idiomadem1_level<=$maximumlevel) OR 
		(Idiomadem2='$useristeaching' AND Idiomadem2_level>=$minimumlevel AND Idiomadem3_level<=$maximumlevel) OR 
		(Idiomadem2='$useristeaching' AND Idiomadem2_level>=$minimumlevel AND Idiomadem3_level<=$maximumlevel)
		
		)";
	}
	else
	{
		$where_clause_student.=" AND (Idiomadem1='$useristeaching' OR Idiomadem2='$useristeaching' OR Idiomadem3='$useristeaching')";
	}


}
//segun las organizaciones seleccionadas

$n_orgs=count($organizationslist);
$where_orgs='';

if ( $n_orgs>0 )
{
	for($jjjj=0;$jjjj<$n_orgs;$jjjj++)
	{
		//echo " $jjjj/$n_orgs ";
		
		$organizacion=array_pop($organizationslist);
		if($jjjj==0)
		{
			$where_orgs="AND ( id_org=$organizacion";
		}	
		else
		{
			$where_orgs.=" OR id_org=$organizacion";
		}
		
		if($jjjj==$n_orgs-1)
		{
			$where_orgs.=" )";
		}
	}
}
//echo "orgswhere: $where_orgs ";

if (!empty($where_orgs))
{
		$where_clause_student.="$where_orgs";
}

// we extract the distance



//$where_clause_student.=" HAVING distanciaPunto1Punto2<50";




if($partnertype=='student')
{
	


echo "-- student --";


 $query = "SELECT *, (acos(sin(radians(Gpslat)) * sin(radians($latitud1)) + 
cos(radians(Gpslat)) * cos(radians($latitud1)) * 
cos(radians(Gpslng) - radians($longitud1))) * 6378)

AS distanciaPunto1Punto2 

FROM mentor2009 WHERE $where_clause_student 

HAVING distanciaPunto1Punto2<150

ORDER BY distanciaPunto1Punto2 DESC";

echo "$query";



$result = mysqli_query($link, $query);
if (!mysqli_num_rows($result))
    die("<br/>.........No results......<br/>");

$number_of_affected_users=mysqli_num_rows($result);


$orden_usuarios=array();

$nameuser=array();

$idioma_ofr_1=array();
$idioma_ofr_2=array();
$idioma_ofr_3=array();

$idioma_dem_1=array();
$idioma_dem_2=array();
$idioma_dem_3=array();

$idioma_ofr_level_1=array();
$idioma_ofr_level_2=array();
$idioma_ofr_level_3=array();

$idioma_dem_level_1=array();
$idioma_dem_level_2=array();
$idioma_dem_level_3=array();

$organiz_id=array();

$distancia111=array();

$array_num_evalu=array();
$array_nota_evalu=array();


	for($iiii=0; $iiii < $number_of_affected_users ; $iiii++     )
	{
		$fila = mysqli_fetch_array($result);
		
		array_push($orden_usuarios, $fila['orden']);
		
		array_push($nameuser, $fila['nombre']);
		
		array_push($idioma_ofr_1, $fila['Idiomaof1']);
		array_push($idioma_ofr_2, $fila['Idiomaof2']);
		array_push($idioma_ofr_3, $fila['Idiomaof3']);
		
		
		array_push($idioma_dem_1, $fila['Idiomadem1']);
		array_push($idioma_dem_2, $fila['Idiomadem2']);
		array_push($idioma_dem_3, $fila['Idiomadem3']);
		
		
		array_push($idioma_ofr_level_1, $fila['Idiomaof1_level']);
		array_push($idioma_ofr_level_2, $fila['Idiomaof2_level']);
		array_push($idioma_ofr_level_3, $fila['Idiomaof3_level']);
		
		
		array_push($idioma_dem_level_1, $fila['Idiomadem1_level']);
		array_push($idioma_dem_level_2, $fila['Idiomadem2_level']);
		array_push($idioma_dem_level_3, $fila['Idiomadem3_level']);
		
		array_push($organiz_id, $fila['id_org']);
		
		array_push($distancia111, round( $fila['distanciaPunto1Punto2'],2) );
		
		array_push($array_num_evalu, $fila['ev_num_diaria']);
		array_push($array_nota_evalu, $fila['ev_proporc_diaria']);
		
		
		
		//nameuser[]=$fila['nombre'];

		

		
		//$ciudad1 = $fila['Ciudad'];

	}


  // print_r($idioma_dem_level_1);
   
   
}
	

else if($partnertype=='teacher')
{

echo "teacher";
}


?>




<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Search Page</title>
<!--  <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.3/themes/base/jquery-ui.css" /> -->
  <link rel="stylesheet" href="jquery-ui-1.13.3.custom/jquery-ui.css" />
  <link rel="stylesheet" href="/resources/demos/style.css" />
  <link rel="stylesheet" href="widgets.css" />
  <link rel="stylesheet" href="lingua2general.css" />
  <link rel="stylesheet" href="estilo.css" />
<!-- <script src="https://code.jquery.com/jquery-3.7.1.js"></script> -->
<script src="jquery-ui-1.13.3.custom/external/jquery/jquery.js"></script>
<!-- <script src="https://code.jquery.com/ui/1.13.3/jquery-ui.js"></script> -->
<script src="jquery-ui-1.13.3.custom/jquery-ui.js"></script>
<script src="https://unpkg.com/leaflet@1.6.0/dist/leaflet.js"></script>
<link href="https://unpkg.com/leaflet@1.6.0/dist/leaflet.css" rel="stylesheet" />










<?php
//require_once('jQueryWidgets.php');
//require_once('idiomasequivalencias.php');












?>












</head>
<body>
 
<div class='marco ui-widget-content'>
<h1>Search Page</h1>
<nav>
	<?php require_once('search-form.php'); ?>
</nav>
<section>
<h3>Resultados</h3>
<div class='marco-fichas'>
	<?php // for ($j = 1; $j <= 4; $j++): ?> 
	<div>
	<?php 
	
	//lista de niveles y como se muestran en la ficha
	$lista_niveles_mostrar=array(	 
	"0"=>"?",
	"1"=>"zero",
	"2"=>"A1",
	"3"=>"A2",
	"4"=>"B1",
	"5"=>"B2",
	"6"=>"C1",
	"7"=>"C2",
	);
	
	//organizacion
	
	
	
	$lista_de_orgs=array();
	
	$query_orgs = "SELECT * FROM organizations WHERE 1 ";
	$result_orgs = mysqli_query($link, $query_orgs);
	$number_of_orgs=mysqli_num_rows($result_orgs);
	

	
	for($iiii=0; $iiii < $number_of_orgs ; $iiii++     )
	{
		$fila_orgs = mysqli_fetch_array($result_orgs);
		
		
		$o_id=$fila_orgs['organization_id'];
		$o_name=$fila_orgs['organization_name'];
		
		//echo "$o_id -- $o_name        ";
		
		$lista_de_orgs[$o_id] = "$o_name";
		//$lista_de_orgs=array("$o_id" => "$o_name");
		
	}
	
	// print_r($lista_de_orgs);
	
	
	// ---fin-organizacion---
	
	for ($i = 0; $i < $number_of_affected_users; $i++): 
	
	
	$orden_usu = array_pop($orden_usuarios);
	
	 $nombreusu = array_pop($nameuser);
	 
	 $idi_of_1 = array_pop($idioma_ofr_1);
	 $idi_of_2 = array_pop($idioma_ofr_2);
	 $idi_of_3 = array_pop($idioma_ofr_3);
	 
	 $idi_dem_1 = array_pop($idioma_dem_1);
	 $idi_dem_2 = array_pop($idioma_dem_2);
	 $idi_dem_3 = array_pop($idioma_dem_3);
	 
	 $idi_of_level_1 = array_pop($idioma_ofr_level_1); $idi_of_level_1=$lista_niveles_mostrar["$idi_of_level_1"];
	 $idi_of_level_2 = array_pop($idioma_ofr_level_2); $idi_of_level_2=$lista_niveles_mostrar["$idi_of_level_2"];
	 $idi_of_level_3 = array_pop($idioma_ofr_level_3); $idi_of_level_3=$lista_niveles_mostrar["$idi_of_level_3"];
	 
	 $idi_dem_level_1 = array_pop($idioma_dem_level_1); $idi_dem_level_1=$lista_niveles_mostrar["$idi_dem_level_1"];
	 $idi_dem_level_2 = array_pop($idioma_dem_level_2); $idi_dem_level_2=$lista_niveles_mostrar["$idi_dem_level_2"];
	 $idi_dem_level_3 = array_pop($idioma_dem_level_3); $idi_dem_level_3=$lista_niveles_mostrar["$idi_dem_level_3"];
	 
	 $organizac=array_pop($organiz_id); $organizac=$lista_de_orgs[$organizac];
	 
	 $distancia12=array_pop($distancia111);
	 
	 $num_evalu=array_pop($array_num_evalu);
	 $nota_evalu=array_pop($array_nota_evalu);
	 
	 ?>
	
	<div class='ficha'>
	
	
		<div>
		
			<?php if( file_exists("../uploader/upload_pic/thumb_$orden_usu.jpg")){ ?>
					<img src="<?php echo "../uploader/upload_pic/thumb_$orden_usu.jpg"; ?>"  /> 
			<?php } 
			
			
			else{?>
			
			<img src="<?php echo "../uploader/default.jpg"; ?>"  />
				
			<?php }
			
			 
			?>
			
			
		</div>
	
	
	
		<div><?php echo "$i. "; echo "$nombreusu #$orden_usu"; ?></div>
		
		<div><?php echo "$idi_of_1 ($idi_of_level_1) - $idi_of_2 ($idi_of_level_2)- $idi_of_3 ($idi_of_level_3)"; ?></div>
		
		<div> 
			<?php if( !empty($idi_of_1 ) ){ ?>
				<img width="25px" height="16px" src="<?php echo "./banderasseparadas2024/$idi_of_1.png  "; ?>"  /> 
			<?php } ?>

			<?php if( !empty($idi_of_2 ) ){ ?>
				<img width="25px" height="16px" src="<?php echo "./banderasseparadas2024/$idi_of_2.png  "; ?>"  /> 
			<?php } ?>

			<?php if( !empty($idi_of_3 ) ){ ?>
				<img width="25px" height="16px" src="<?php echo "./banderasseparadas2024/$idi_of_3.png"; ?>"  /> 
			<?php } ?>
		</div>
		
		
		<div><?php echo "$idi_dem_1 ($idi_dem_level_1)- $idi_dem_2 ($idi_dem_level_2)- $idi_dem_3 ($idi_dem_level_3)"; ?></div>
		
		<div> 
			<?php if( !empty($idi_dem_1 ) ){ ?>
				<img width="25px" height="16px" src="<?php echo "./banderasseparadas2024/$idi_dem_1.png  "; ?>"  /> 
			<?php } ?>

			<?php if( !empty($idi_dem_2 ) ){ ?>
				<img width="25px" height="16px" src="<?php echo "./banderasseparadas2024/$idi_dem_2.png  "; ?>"  /> 
			<?php } ?>

			<?php if( !empty($idi_dem_3 ) ){ ?>
				<img width="25px" height="16px" src="<?php echo "./banderasseparadas2024/$idi_dem_3.png"; ?>"  /> 
			<?php } ?>
		</div>
		
		
		<div><?php echo "$organizac"; ?></div>
		
		<div><?php echo "$distancia12 km from me"; ?></div>
		
		<div><?php echo "Numero evaluaciones: $num_evalu"; ?></div>
		
		<div><?php echo "Nota evaluacion:$nota_evalu"; ?></div>
		

		
		
	</div>
	
	
	<?php endfor; ?>
	
	
	</div>
	<?php // endfor; ?>
</div>
</section>

<!--
<section>
<h3>Información extra</h3>
<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Fusce egestas faucibus suscipit. Proin sodales quam turpis, eu cursus urna tincidunt id. Suspendisse euismod aliquet turpis, ac ullamcorper est sollicitudin quis. Donec mollis ultricies libero quis dignissim. Fusce porttitor scelerisque ipsum, sed maximus ipsum ullamcorper vitae. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia curae; Fusce eu eros a velit cursus pharetra a eu felis. Donec nec urna lacus. Nullam at nibh gravida, faucibus velit nec, dapibus augue. Aliquam in risus ac sapien tempus ultricies a sit amet quam. Donec accumsan in est malesuada tempor. Nulla vitae eleifend neque, quis egestas risus.
</p>
</section>

-->

</div>

<?php
 // phpInfo( 32 );  
// enableCheckboxRadio();
?>
</body></html>
