<?php 

//requiring FB PHP SDK
//require '../src/facebook.php';
require('../files/bd.php'); 

$tiempo_corte=time();
//el tiempo de corte lo dejamos a un dia antes por lo de los husos horarios.
$tiempo_corte=time()-24*3600;

//maximos eventos que vamos a mostrar por pantalla
$max_events_shown=10;

//sacamos los eid de la table eventoslista que sean futuros
$query="SELECT * FROM eventoslista WHERE unix_start_time>'$tiempo_corte' ORDER BY unix_start_time ASC";   

$result=mysqli_query($link,$query);
$nuevos=mysqli_num_rows($result);

	
if( !$nuevos)
		die('<br/>No events at this moment');


$clausula_in_eventos='';
for($i=0;$i<$max_events_shown;$i++)
{
	$fila=mysqli_fetch_array($result);
	$eid_bd=$fila['city'];
	$ev_broadcasted=$fila['Broadcasted'];
	

	?>    
	
	<table><tr height=25; style="color:dimgrey";><td>
	<a href="./eventdetails.php?idev=<?php echo $fila['Id']; ?>" style="font-weight:none; color: #E65F00;">+Info</a> 


	
 <?php  
	
		$ciudad_abbr=$fila['city'];
		$ciudad_abbr=substr($ciudad_abbr,0,14);
        echo "<span>" . $ciudad_abbr . "</span>";    
		
	
		
	?>  <?php 
	
	
		require('../files/idiomasequivalencias.php');
		
		$lengua1=$idiomas_equiv["{$fila['Idioma']}"];
		$lengua1=substr($lengua1,0,14);
	
        echo "<span>(" . $lengua1 . ")</span>";
		
			?>   <?php
			
			$fecha1=substr($fila['start_time'],0,10);
			
			$unixtime1 = strtotime($fecha1);
			$dayOfWeek = date("l", $unixtime1);
			
			$dayOfWeek_corto=substr($dayOfWeek,0,3);
	
	echo "<span>" . $fecha1 . " ($dayOfWeek_corto.)</span>"; 
			
	
	?>  


	<?php 
	
		if($ev_broadcasted)
		{?>
			<img src="../images/recommended.png" alt="recommended" height="20" />
		<?php }
	
	?>




	</td></tr></table>

	 <?php
	

}

if($nuevos>=$max_events_shown):
	echo "</br>Check more events";


	?>
	
	<a href="../events/showallupcomingevents.php" style="font-weight:none; color: #E65F00;"> here</a>.

		 <?php
 	endif;
  	?>