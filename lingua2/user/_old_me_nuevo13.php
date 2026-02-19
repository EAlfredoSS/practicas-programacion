<?php
//hay que pasar la variable header 


/*
           OJO CON LAS VARIABLES $tipo_form y $is_teacher, que pueden ser confusas. Intentar usar una de las dos  !!!!!!!!!
*/

require('../templates/header.php');

// require('../files/idiomasequivalencias.php');

require('../files/idiomasnivel.php');


// sacamos zona horaria y calculamos hora local del usuario
require('../funcionesphp/funciones_idiomas_usuario.php');

//$zonaHoraria=$fila77['timeshift'];
$tiempo_unix_actual=time();
$fechaHoraFormateada2 = obtenerFechaHora($tiempo_unix_actual, $zonaHoraria);

function obtenerHora($fechaHora) {
    // Convertir el string de fecha y hora a un timestamp
    $timestamp = strtotime($fechaHora);
    
    // Formatear el timestamp para obtener solo la hora (HH:MM)
    $hora = date("H:i", $timestamp);
    
    return $hora;
}

// Ejemplo de uso

$horaFormateada2 = obtenerHora($fechaHoraFormateada2);

//echo "La hora es: " . $hora; // Salida: La hora es: 14:30





//echo "<br>$zonaHoraria: $fechaHoraFormateada2";

// fin zona horaria



?>

<style type="text/css">

.tooltip-container {
  position: relative; /*relative: los elementos se posicionan de forma relativa a su posición normal.*/
  display: inline-block;
}

.tooltip-text {
  font-size: 10px;
  visibility: hidden;
  width: 120px;
  background-color: #000;
  color: #fff;
  text-align: center;
  border-radius: 6px;
  padding: 5px;
  position: absolute;
  z-index: 1;
  top: 125%; /* Posiciona el tooltip bajo del elemento */
  left: 50%;
  transform: translateX(-50%);
  opacity: 0;
  transition: opacity 0.3s;
}
.tooltip-container:hover .tooltip-text {
  visibility: visible;
  opacity: 0.75;
}








    .alerta-9 {
        background: rgba(236, 236, 236, 1);
        color: #b2b2b2;
        padding: 12px 14px;
        font-size: 14px;
        display: flex;
        align-items: center;
        gap: 10px;
        font-weight: 500;
        border-radius: 20px;
        width: 100%;
    }
    
    .alerta-9 .icono {
        font-size: 18px;
        margin-right: 8px;
    }
    
    .alerta-9 a {
        font-weight: bold;
        text-decoration: underline;
        color: #e65f00;
    }








</style>





<div class="main-section">
    <div class="container">
        <div class="main-section-data">
            <div class="row">
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
								
								style="height: <?php echo $profile_photo_height_for_photo; ?>px;                  <?php // if( $tipo_form=='teacher' ) { echo "background-color: #2e8b57";           }?> "                      > 
								
								<?//php endif; ?>
								
                                    <div class="usr-pic">
									
                                        <img src="<?php  echo $foto_nombre; ?>?nocache=<?php echo time(); ?>"> 
										

										
										<?php //echo $foto_nombre; ?>
										
                                    </div>
                                </div>
								
								<?php // if( $tipo_form=='teacher' ) { ?> <!-- <p style="color: #2e8b57; font-size:80%;">**I'm a professional teacher**</p> --> <?php /*  <i class="fa fa-star" style="font-size:80%;"></i>*/ // } ?> 
								
                                <!--username-dt end-->
                                <div class="user-specs ">
                                    <h3><?php
                                        echo $nombre_usuar;

                                        //vemos si ha verificado su email ya. si no, le ponemos link a pagina correspodiente

                                        ?></h3>
                                    <span><?php
                                            //vemos si ha verificado su email ya. si no, le ponemos link a pagina correspodiente

                                            if (!$email_verified)
                                                echo "</br><a style=\"font-size: 12px;font-weight: bold; color: #e65f00\"  href=\"./verify_email.php\">Verify your email</a></br>";

                                            ?></span>
                                    <span><?php


                                            //si nunca se le configuraron las coordenadas gps le sacamos un botón
                                            if ($gpslat11 == 0 and $gpslng11 == 0) {
                                                //echo "<br><center><a href=\"./getgpsposition.php\" style=\"color:red; font-size: 12px; font-weight: bold;\" >Add your location</a></center>";
                                            } else {
                                                echo $fila['Ciudad'] . " area";
                                            }
											
											echo "<br>($horaFormateada2)";
											
											//sacamos la organizacion, si la hay
											$domain1 = substr($email_del_usu, (int) strpos($email_del_usu, '@') + 1);
											
											$query123="
											SELECT organization_name AS org_name
											FROM organization_emails orgem 
											INNER JOIN organizations org 
											ON org.organization_id = orgem.organization_id
											WHERE orgem.email_domain='$domain1'
											";
											
																					
											$result123=mysqli_query($link,$query123);
											if(mysqli_num_rows($result123))
											{
												$fila123=mysqli_fetch_array($result123);
												$organization1=$fila123['org_name'];
												
												?>
												<br/><br/>
												<p style="color: #e65f00 <?php   //if($tipo_form=='teacher'){ echo "#2e8b57";  } else { echo "#e65f00"; }     ?>  ">
												<i class="fas fa-user-check" aria-hidden="true" style="font-size: 15px;"></i>
												<?php 
												
												
												echo " $organization1 ";
											}
											?>
											</p>
												<?php 
											

                                            ?></span>
                                    <br>

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





















<?php


//Buscamos los usuarios profesores aleatoriamente


require('../files/bd.php');

session_start();


$identificador2017=$_SESSION['orden2017'];


//SACAMOS LENGUAS QUE CONOCE EL USUARIO my_langs

$query_my_langs = "
SELECT my_l.*, l_names.Print_Name AS full_lang_name, l.lang_id AS lang_codigo2letras
FROM my_langs my_l
LEFT JOIN  languages_names l_names
ON my_l.lang_id=l_names.Id
LEFT JOIN languages1 l
ON  my_l.lang_id=l.Id
WHERE my_l.id='$identificador2017' 
ORDER BY my_l.level_id 
DESC";

$result_my_langs = mysqli_query($link, $query_my_langs);
$num_my_langs = mysqli_num_rows($result_my_langs);

$my_langs_array=array();
$my_langs_2letters_array=array();
$my_langs_full_name_array=array();
$my_langs_level_array=array();
$my_langs_forshare_array=array();
$my_langs_price_array=array();
$my_langs_typeofexchange_array=array();
$my_langs_priceorexchangetext_array=array();
$my_langs_level_image_array=array();



for ($jjj = 0; $jjj < $num_my_langs; $jjj++) 
{
	$fila_my_langs = mysqli_fetch_array($result_my_langs);
	array_push($my_langs_full_name_array,$fila_my_langs['full_lang_name']);
	array_push($my_langs_array,$fila_my_langs['lang_id']);
	array_push($my_langs_2letters_array,$fila_my_langs['lang_codigo2letras']);
	array_push($my_langs_level_array,$fila_my_langs['level_id']);
	array_push($my_langs_forshare_array,$fila_my_langs['for_share']);
	array_push($my_langs_price_array,$fila_my_langs['lang_price']);
	
}


//print_r($my_langs_2letters_array);


// en la tabla languages_names, para algunos idiomas hay id repetido, por ejemplo para spa hay dos lineas (spanish, castilian)
//por eso hay que detectar las repeticiones y borrarlas, pero el nombre del idioma queda: Castilian | Spanish

$duplicate_langs = array_count_values($my_langs_array);
$lista_idiomas_dup=array();



//$n_dups=0;


//echo "<br><br>duplicate_langs antes:<br>";
//print_r($duplicate_langs);

for ($jjj = 0; $jjj < $num_my_langs; $jjj++) 
{
	$lang1=$my_langs_array[$jjj];
	if($duplicate_langs["$lang1"]==1)
	{
		unset($duplicate_langs["$lang1"]);
		//$n_dups++;
	}
} 

$n_dups=count($duplicate_langs);


//echo "<br><br>duplicate_langs despues:<br>";
//print_r($duplicate_langs);

$lista_idiomas_dup=array_keys($duplicate_langs);

//echo "<br><br>idiomas duplicados<br>";
//print_r($lista_idiomas_dup);


//echo "<br>n dups: $n_dups<br>";

for ($iiii = 0; $iiii < $n_dups; $iiii++) 
{
	$nombre_idiomas_array='';
	$lang2=$lista_idiomas_dup[$iiii];
	//echo "$lang2";
		
	//$cnt tiene el número de veces que se repite el valor $lang
	$tmp = array_count_values($my_langs_array);
	$cnt = $tmp[$lang2];
	
	for($jjjj=0;$jjjj<$cnt;$jjjj++) 
	{	

		$key2 = array_search($lang2, $my_langs_array); //echo "<br>$jjjj --- $key2 - $lang2 <br>";
		if($jjjj<$cnt-1)//nos cargamos todos menos 1 valor
		{
			$nombre_idiomas_array.="$my_langs_full_name_array[$key2] | ";
			$my_langs_array[$key2]='_delete_';
		}
		else
		{
			$nombre_idiomas_array.="$my_langs_full_name_array[$key2]";
		}
		
		$my_langs_full_name_array[$key2]=$nombre_idiomas_array;
	}
		
}




//echo "<br><br>idiomas<br>";
//print_r($my_langs_array);






// escogemos la imagen de los idiomas my_langs



for($iii=0;$iii<count($my_langs_level_array);$iii++)
{
	switch ($my_langs_level_array[$iii]) 
	{
		case 0:
			$my_langs_level_image_array[$iii]='no_data.png';
			break;
		case 1:
			$my_langs_level_image_array[$iii]='zero_knowledge.png';
			break;
		case 2:
			$my_langs_level_image_array[$iii]='a1.png';
			break;
		case 3:
			$my_langs_level_image_array[$iii]='a2.png';
			break;
		case 4:
			$my_langs_level_image_array[$iii]='b1.png';
			break;
		case 5:
			$my_langs_level_image_array[$iii]='b2.png';
			break;
		case 6:
			$my_langs_level_image_array[$iii]='c1.png';
			break;
		case 7:
			$my_langs_level_image_array[$iii]='c2.png';
			break;
	}
}


//segun el idioma tres opciones: no comparte, comparte por dinero o comparte por otro idioma

for($iii=0;$iii<count($my_langs_array);$iii++)
{
	if($my_langs_forshare_array[$iii]==0)
	{
		$my_langs_typeofexchange_array[$iii]='I know this language, but I do not want to exchange or teach it.';
		$my_langs_priceorexchangetext_array[$iii]='';
	}
	else if($my_langs_price_array[$iii]==null)
	{
		$my_langs_typeofexchange_array[$iii]='I know this language and I want to exchange it for another user\'s language (exchange free of cost).';
		$my_langs_priceorexchangetext_array[$iii]="EXCH.";
	}
	else
	{
		$my_langs_typeofexchange_array[$iii]="I know this language and I want to teach it for money.";
		$my_langs_priceorexchangetext_array[$iii]="$my_langs_price_array[$iii] &#8364;/h";
	}
}
 

/*
print_r($my_langs_array);
print_r($my_langs_level_array); 
print_r($my_langs_level_image_array);
print_r($my_langs_forshare_array); 
print_r($my_langs_price_array); 
print_r($my_langs_typeofexchange_array); 
*/


//aqui quitamos las lenguas borradas con '_delete_' (pero quedan desorganizados)

$key3 = array_search('_delete_', $my_langs_array);
while($key3!==false)
{
	
	//echo "-$key3-";
	
	unset($my_langs_array[$key3]);
	unset($my_langs_full_name_array[$key3]);
	unset($my_langs_level_array[$key3]);
	unset($my_langs_forshare_array[$key3]);
	unset($my_langs_price_array[$key3]);
	unset($my_langs_typeofexchange_array[$key3]);
	unset($my_langs_priceorexchangetext_array[$key3]);
	unset($my_langs_level_image_array[$key3]);
	unset($my_langs_2letters_array[$key3]);
		
	$key3 = array_search('_delete_', $my_langs_array);
}


//print_r($my_langs_array);

$tmp1_array=array();
$tmp2_array=array();
$tmp3_array=array();
$tmp4_array=array();
$tmp5_array=array();
$tmp6_array=array();
$tmp7_array=array();
$tmp8_array=array();
$tmp9_array=array();



$n_lenguas=count($my_langs_array);

for ($i=0;$i<$n_lenguas;$i++)
{
	$tmp1_array[$i]=array_pop($my_langs_array);
	$tmp2_array[$i]=array_pop($my_langs_full_name_array);
	$tmp3_array[$i]=array_pop($my_langs_level_array);
	$tmp4_array[$i]=array_pop($my_langs_forshare_array);
	$tmp5_array[$i]=array_pop($my_langs_price_array);
	$tmp6_array[$i]=array_pop($my_langs_typeofexchange_array);
	$tmp7_array[$i]=array_pop($my_langs_priceorexchangetext_array);
	$tmp8_array[$i]=array_pop($my_langs_level_image_array);
	$tmp9_array[$i]=array_pop($my_langs_2letters_array);
}

$my_langs_array=array_reverse($tmp1_array);
$my_langs_full_name_array=array_reverse($tmp2_array);
$my_langs_level_array=array_reverse($tmp3_array);
$my_langs_forshare_array=array_reverse($tmp4_array);
$my_langs_price_array=array_reverse($tmp5_array);
$my_langs_typeofexchange_array=array_reverse($tmp6_array);
$my_langs_priceorexchangetext_array=array_reverse($tmp7_array);
$my_langs_level_image_array=array_reverse($tmp8_array);
$my_langs_2letters_array=array_reverse($tmp9_array);


//print_r($my_langs_array);





// FIN SACAMOS LENGUAS QUE CONOCE EL USUARIO my_langs







//SACAMOS LENGUAS QUE QUIERE APRENDER EL USUARIO learn_langs

/*

$query_req_langs = "SELECT * FROM learn_langs WHERE id='$identificador2017' ORDER BY level_id DESC";
$result_req_langs = mysqli_query($link, $query_req_langs);
$num_req_langs = mysqli_num_rows($result_req_langs);

$req_langs_array=array();
$req_langs_level_array=array();

for ($jjj = 0; $jjj < $num_req_langs; $jjj++) 
{
	$fila_req_langs = mysqli_fetch_array($result_req_langs);
	array_push($req_langs_array,$fila_req_langs['lang_id']);
	array_push($req_langs_level_array,$fila_req_langs['level_id']);
}

// escogemos la imagen de los idiomas my_langs

$req_langs_level_image_array=array();

for($iii=0;$iii<count($req_langs_level_array);$iii++)
{
	switch ($req_langs_level_array[$iii]) 
	{
		case 0:
			$req_langs_level_image_array[$iii]='no_data.png';
			break;
		case 1:
			$req_langs_level_image_array[$iii]='zero_knowledge.png';
			break;
		case 2:
			$req_langs_level_image_array[$iii]='a1.png';
			break;
		case 3:
			$req_langs_level_image_array[$iii]='a2.png';
			break;
		case 4:
			$req_langs_level_image_array[$iii]='b1.png';
			break;
		case 5:
			$req_langs_level_image_array[$iii]='b2.png';
			break;
		case 6:
			$req_langs_level_image_array[$iii]='c1.png';
			break;
		case 7:
			$req_langs_level_image_array[$iii]='c2.png';
			break;
	}
}


*/

/*
print_r($req_langs_array);
print_r($req_langs_level_array); 
print_r($req_langs_level_image_array); 
*/







// SACAMOS LENGUAS QUE QUIERE APRENDER EL USUARIO learn_langs










//SACAMOS LENGUAS QUE CONOCE EL USUARIO my_langs

$query_learn_langs = "
SELECT learn_l.*, l_names.Print_Name AS full_lang_name, l.lang_id AS lang_codigo2letras
FROM learn_langs learn_l
LEFT JOIN  languages_names l_names
ON learn_l.lang_id=l_names.Id
LEFT JOIN languages1 l
ON  learn_l.lang_id=l.Id
WHERE learn_l.id='$identificador2017' 
ORDER BY learn_l.level_id 
DESC";

$result_learn_langs = mysqli_query($link, $query_learn_langs);
$num_learn_langs = mysqli_num_rows($result_learn_langs);

$learn_langs_array=array();
$learn_langs_2letters_array=array();
$learn_langs_full_name_array=array();
$learn_langs_level_array=array();
$learn_langs_forshare_array=array();
$learn_langs_price_array=array();
$learn_langs_typeofexchange_array=array();
$learn_langs_priceorexchangetext_array=array();
$learn_langs_level_image_array=array();



for ($jjj = 0; $jjj < $num_learn_langs; $jjj++) 
{
	$fila_learn_langs = mysqli_fetch_array($result_learn_langs);
	array_push($learn_langs_full_name_array,$fila_learn_langs['full_lang_name']);
	array_push($learn_langs_array,$fila_learn_langs['lang_id']);
	array_push($learn_langs_2letters_array,$fila_learn_langs['lang_codigo2letras']);
	array_push($learn_langs_level_array,$fila_learn_langs['level_id']);
	array_push($learn_langs_forshare_array,$fila_learn_langs['for_share']);
	array_push($learn_langs_price_array,$fila_learn_langs['lang_price']);
	
}


//print_r($learn_langs_2letters_array);


// en la tabla languages_names, para algunos idiomas hay id repetido, por ejemplo para spa hay dos lineas (spanish, castilian)
//por eso hay que detectar las repeticiones y borrarlas, pero el nombre del idioma queda: Castilian, Spanish

$duplicate_langs = array_count_values($learn_langs_array);
$lista_idiomas_dup=array();

//print_r($duplicate_langs);

//$n_dups=0;

for ($jjj = 0; $jjj < $num_learn_langs; $jjj++) 
{
	$lang1=$learn_langs_array[$jjj];
	if($duplicate_langs["$lang1"]==1)
	{
		unset($duplicate_langs["$lang1"]);
		//$n_dups++;
	}
}

$n_dups=count($duplicate_langs);

//print_r($duplicate_langs);
$lista_idiomas_dup=array_keys($duplicate_langs);
//print_r($lista_idiomas_dup);





for ($iiii = 0; $iiii < $n_dups; $iiii++) 
{
	$nombre_idiomas_array='';
	$lang2=$lista_idiomas_dup[$iiii];
	//echo "$lang2";
		
	//$cnt tiene el número de veces que se repite el valor $lang
	$tmp = array_count_values($learn_langs_array);
	$cnt = $tmp[$lang2];
	
	for($jjjj=0;$jjjj<$cnt;$jjjj++) 
	{
		$key2 = array_search($lang2, $learn_langs_array);
		if($jjjj<$cnt-1)//nos cargamos todos menos 1 valor
		{
			$nombre_idiomas_array.="$learn_langs_full_name_array[$key2] | ";
			$learn_langs_array[$key2]='_delete_';
		}
		else
		{
			$nombre_idiomas_array.="$learn_langs_full_name_array[$key2]";
		}
		
		$learn_langs_full_name_array[$key2]=$nombre_idiomas_array;
	}
		
} 

//echo "<br><br>idiomas<br>";
//print_r($learn_langs_array);






// escogemos la imagen de los idiomas learn_langs



for($iii=0;$iii<count($learn_langs_level_array);$iii++)
{
	switch ($learn_langs_level_array[$iii]) 
	{
		case 0:
			$learn_langs_level_image_array[$iii]='no_data.png';
			break;
		case 1:
			$learn_langs_level_image_array[$iii]='zero_knowledge.png';
			break;
		case 2:
			$learn_langs_level_image_array[$iii]='a1.png';
			break;
		case 3:
			$learn_langs_level_image_array[$iii]='a2.png';
			break;
		case 4:
			$learn_langs_level_image_array[$iii]='b1.png';
			break;
		case 5:
			$learn_langs_level_image_array[$iii]='b2.png';
			break;
		case 6:
			$learn_langs_level_image_array[$iii]='c1.png';
			break;
		case 7:
			$learn_langs_level_image_array[$iii]='c2.png';
			break;
	}
}



/*
print_r($learn_langs_array);
print_r($learn_langs_level_array); 
print_r($learn_langs_level_image_array);
print_r($learn_langs_forshare_array); 
print_r($learn_langs_price_array); 
print_r($learn_langs_typeofexchange_array); 
*/


//aqui quitamos las lenguas borradas con '_delete_' (pero quedan desorganizados)

$key3 = array_search('_delete_', $learn_langs_array);
while($key3!==false)
{
	
	//echo "-$key3-";
	
	unset($learn_langs_array[$key3]);
	unset($learn_langs_full_name_array[$key3]);
	unset($learn_langs_level_array[$key3]);
	unset($learn_langs_forshare_array[$key3]);
	unset($learn_langs_price_array[$key3]);
	unset($learn_langs_typeofexchange_array[$key3]);
	unset($learn_langs_priceorexchangetext_array[$key3]);
	unset($learn_langs_level_image_array[$key3]);
	unset($learn_langs_2letters_array[$key3]);
		
	$key3 = array_search('_delete_', $learn_langs_array);
}


//print_r($learn_langs_array);

$tmp1_array=array();
$tmp2_array=array();
$tmp3_array=array();
$tmp4_array=array();
$tmp5_array=array();
$tmp6_array=array();
$tmp7_array=array();
$tmp8_array=array();
$tmp9_array=array();



$n_lenguas=count($learn_langs_array);

for ($i=0;$i<$n_lenguas;$i++)
{
	$tmp1_array[$i]=array_pop($learn_langs_array);
	$tmp2_array[$i]=array_pop($learn_langs_full_name_array);
	$tmp3_array[$i]=array_pop($learn_langs_level_array);
	$tmp4_array[$i]=array_pop($learn_langs_forshare_array);
	$tmp5_array[$i]=array_pop($learn_langs_price_array);
	$tmp6_array[$i]=array_pop($learn_langs_typeofexchange_array);
	$tmp7_array[$i]=array_pop($learn_langs_priceorexchangetext_array);
	$tmp8_array[$i]=array_pop($learn_langs_level_image_array);
	$tmp9_array[$i]=array_pop($learn_langs_2letters_array);
}

$learn_langs_array=array_reverse($tmp1_array);
$learn_langs_full_name_array=array_reverse($tmp2_array);
$learn_langs_level_array=array_reverse($tmp3_array);
$learn_langs_forshare_array=array_reverse($tmp4_array);
$learn_langs_price_array=array_reverse($tmp5_array);
$learn_langs_typeofexchange_array=array_reverse($tmp6_array);
$learn_langs_priceorexchangetext_array=array_reverse($tmp7_array);
$learn_langs_level_image_array=array_reverse($tmp8_array);
$learn_langs_2letters_array=array_reverse($tmp9_array);








//FINAL DE SACAMOS LENGUAS QUE CONOCE EL USUARIO learn_langs













$query77="SELECT * FROM mentor2009 WHERE orden='$identificador2017' ";
$result77=mysqli_query($link,$query77);
if(!mysqli_num_rows($result77))
		die("User unregistered 1.");
$fila77=mysqli_fetch_array($result77);

$latitud1=$fila77['Gpslat'];
$longitud1=$fila77['Gpslng'];

/*
$idiomademan1=$fila77['Idiomadem1'];
$idiomademan2=$fila77['Idiomadem2'];
$idiomademan3=$fila77['Idiomadem3'];

$idiomaofre1=$fila77['Idiomaof1'];
$idiomaofre2=$fila77['Idiomaof2'];
$idiomaofre3=$fila77['Idiomaof3'];



$level_idiomademan1=$fila77['Idiomadem1_level'];
$level_idiomademan2=$fila77['Idiomadem2_level'];
$level_idiomademan3=$fila77['Idiomadem3_level'];


$level_idiomaofre1=$fila77['Idiomaof1_level'];
$level_idiomaofre2=$fila77['Idiomaof2_level'];
$level_idiomaofre3=$fila77['Idiomaof3_level'];

*/

$id_de_la_org_del_usu=$fila77['id_org'];





$is_teacher=$fila77['Pais'];

//aqui damos el valor null a los idiomas que están vacíos. si no en la query de debajo seleccionaría los usuarios que tengan, por ejemplo, m.m.Idiomaof1 vacío
/*
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

*/



//______________________________________________

//si no está actualizada la organización en mentor2009 (id_org==0)
//entonces hacemos un update
//en u.php no usamos la tabla 'mentor2009' para extraer la organizacion



$id_de_la_org_del_usu=$fila77['id_org'];

if($id_de_la_org_del_usu==0)
{
	//un codigo parecido (no igual) esta mas arriba, pero lo volvemos a copiar
	
	$domain1 = substr($email_del_usu, (int) strpos($email_del_usu, '@') + 1);
	//echo "dominio2: $domain1";
	
	
	$query123456="
	SELECT org.organization_id AS id_de_la_org
	FROM organization_emails orgem 
	INNER JOIN organizations org 
	ON org.organization_id = orgem.organization_id
	WHERE orgem.email_domain='$domain1'
	";
	
	//echo "$query123456";
	
									
	$result123456=mysqli_query($link,$query123456);
	if(mysqli_num_rows($result123456))
	{
		$fila123456=mysqli_fetch_array($result123456);
		$organization_id1=$fila123456['id_de_la_org'];
		
		$query_update_org="UPDATE mentor2009 SET id_org = $organization_id1 WHERE orden = $identificador2017";
		$result=mysqli_query($link,$query_update_org);
	}
    
}



//__________________________________________


//los profes solo se muestran a los usuarios que no son profesores

/*
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



*/

?>
















                        <div class="suggestions full-width">
                            <div class="sd-title">
                                <h3>Translation Jobs for you</h3>
                                <!-- <i class="la la-ellipsis-v"></i> -->
                            </div>
                            <!--sd-title end-->
                            <div class="suggestions-list">
                                <div class="suggestion-usd">


                                    <img src="../uploader/default.jpg<?php echo "$path_photo"; ?>" height="35px" ; width="35px" ;>



                                    <div class="sgt-text">
                                        <h4>English to<br> Spanish<br><?php echo "$nombre_usuario_short"; ?></h4>
                                        <p>450words - 35<?php echo "$precioprof"."€"; ?> (0.078&euro;/w.)</p>

                                        
                                    </div>

                                    <!--<span><a href="<?php // echo "../user/u.php?dst=$distancia99&identificador=$orden99"; ?>" ><i class="la la-chevron-right"></i></a></span>  -->

                                </div>

                                <div class="view-more">
                                    <a href="../testingprototypes/translationjob.php" title="">View Job</a>
                                </div>
                            </div>
                            <!--suggestions-list end-->
                        </div>












<?php
/*
}
*/


/*


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


*/

?>














                        <div class="suggestions full-width ">
                            <div class="sd-title">
                                <h3>Long-term Jobs for you</h3>
                                <!-- <i class="la la-ellipsis-v"></i> -->
                            </div>
                            <!--sd-title end-->
                            <div class="suggestions-list">
                                <div class="suggestion-usd">


                                    <img src="<?php echo "../uploader/default.jpg"; ?>" height="35px" ; width="35px" ;>


                                    <div class="sgt-text">
                                        <h4>Hewlett Packard<?php echo "$nombre_usuario_short"; ?></h4>
										<p>Business Analyst<?php echo "$ciudad97"; ?></p>
                                        <p style="font-style: italic; font-size: 85%">Sant Cugat (Spain)<?php echo "$ciudad97"; ?></p>


                                    </div>

                                    <!-- <span><a href="<?php // echo "../user/u.php?dst=$distancia99&identificador=$orden99"; ?>"><i class="la la-chevron-right"></i></a></span> -->

                                </div>

                                <div class="view-more">
                                    <a href="../testingprototypes/longtermjob.php" title="">View Job</a>
                                </div>
                            </div>
                            <!--suggestions-list end-->
                        </div>


<?php

/*


 } else //si es profesor
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
	
	*/
	
	

/*
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



*/

/*

}

*/
?>



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
                                    <h3>I know these languages</h3>

                                    <ul class="skill-tags">
									
										
									
									        <?php for($iii=0;$iii<count($my_langs_array);$iii++)
											{
												$idof=$my_langs_array[$iii];
												$idof_2letras=$my_langs_2letters_array[$iii];
												$level_idiomaofre=$my_langs_level_array[$iii];
												$image_level_lang_ofr=$my_langs_level_image_array[$iii];
												$tooltip_ofr=$my_langs_typeofexchange_array[$iii];
												
												
												$related_macrolanguage_3_letters_code="";
												
												// esta no es la manera eficiente de hacerlo pero va a ser más lioso de otra manera sacar si un idioma es subidioma y su macroidioma
												$query479="SELECT * FROM languages_macrolanguages WHERE I_Id='$idof' ";
												//echo "$query479";
												$result479=mysqli_query($link,$query479);
												//if(mysqli_num_rows($result479))
												//{
													$fila479=mysqli_fetch_array($result479);
													$related_macrolanguage_3_letters_code=$fila479['M_Id'];
												//}
												
												
																		
												
												
											?> 
												<li>
															<center>
																											
																<a 

																<?php
																	if(!empty($related_macrolanguage_3_letters_code))
																	{
																	$alert_message=" \'$idof\' is a sublanguage of \'$related_macrolanguage_3_letters_code\'. You cannot modify or delete a sublanguage directly. You need to click on the \'$related_macrolanguage_3_letters_code\' flag to make modifications.";	
																
																echo " onclick=\"alert('$alert_message');\" ";

																	} 
																	else{  
																	?>
																
																href="../addlanguage/addlanguage.php?lang=<?php echo  "$idof"; ?>&use=know" title="" style="text-align:center;" 
																
																<?php
																	} 
																	
																?>
																
																>
																
																
																<img class="language language-<?php echo  $idof_2letras; ?> " />
															
															</br>
															

																<img style="margin-top: 5px; width: 50px; height: 5px;" src="./images/language_levels/<?php echo $image_level_lang_ofr; ?>" alt="<?php echo  $idiomas_nivel["$level_idiomaofre"]; ?>" />
															</center>
																		<br> 
															<div style="margin: -20px 0 0 0;" >			
																		<center>
																		 <?php echo  "$idof"; //echo  $idiomas_equiv["$idof"]; ?> 
																		 (<?php echo  $idiomas_nivel["$level_idiomaofre"];?>)
																		 </center>
															</div>			 
															</a>

															<br>
															
															<center>
															
															
															<span style="color:#b2b2b2; font-size: 12px;"><?php echo "$my_langs_priceorexchangetext_array[$iii]";	 ?></span>
															<div class="tooltip-container" style="font-size: 12px; color:#b2b2b2; margin: -5px 0 0 0; align: center;">
																
																	 <i style="color:#b2b2b2;" class="fas fa-info-circle"></i> 
																	<span class="tooltip-text"><?php echo "$my_langs_full_name_array[$iii]:<br>$tooltip_ofr<br>";	 ?></span>
																
															</div>
															</center>
															

												</li>
												
                                            <?php
                                            }
											?>
											
											<li style="vertical-align:top;">
											
											<a href="../addlanguage/addlanguage.php" style="font-size: 33px;"><i class="fas fa-plus-circle"></i></a>
											
											</li>

											
											<?php 
									//aqui avisamos si algún idioma introducido no tiene su nivel insertado
									for($iii=0;$iii<count($my_langs_level_array);$iii++)
									{
										//echo " $my_langs_level_array[$iii] ";
										
										if( $my_langs_level_array[$iii]==0 )
										{ ?> 

										    <div class="alerta alerta-9"; >
											
												(*) Click on the flags to add more information about your languages. 
											
											</div> 
										
										
										<?php 
										//<h5 style="color: red;">(*) Insert your level for each offered language <a style="color: red;font-weight: bold; text-decoration: underline;" href="../updateinfo/insert_level_offered_language.php">here</a> &#9888;</h5>
										
										
										break; //cuando se detecta uno solo, se rompe el bucle
										}
										
									}?>
											 
											
											
	
									
                                        <?php /* if (!empty($idof1a)) { ?> <li><a href="#" title="" style="text-align:center;"> <img class="language language-<?php echo  $idof1a; ?> " />
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
                                        
									
									print_r($req_langs_array);
									print_r($req_langs_level_array); 
									print_r($req_langs_level_image_array); 
									
									*/
									
									?>

                                    </ul>
                                </div>
                                <?php //if ($tipo_form != "teacher") {

                                ?>
                                    <div class="epi-sec">

                                    </div>
									
									
									
									
									
									
									
									
									
									
									
									
									
									
									
									
									
								<div class="job_descp ">
                                    <h3>I want to learn these languages</h3>

                                    <ul class="skill-tags">
									
										
									
									        <?php for($iii=0;$iii<count($learn_langs_array);$iii++)
											{
												$idof=$learn_langs_array[$iii];
												$idof_2letras=$learn_langs_2letters_array[$iii];
												$level_idiomaofre=$learn_langs_level_array[$iii];
												$image_level_lang_ofr=$learn_langs_level_image_array[$iii];
												$tooltip_ofr=$learn_langs_typeofexchange_array[$iii];
												
												
												$related_macrolanguage_3_letters_code="";
												
												// esta no es la manera eficiente de hacerlo pero va a ser más lioso de otra manera sacar si un idioma es subidioma y su macroidioma
												$query479="SELECT * FROM languages_macrolanguages WHERE I_Id='$idof' ";
												$result479=mysqli_query($link,$query479);
												$fila479=mysqli_fetch_array($result479);
												$related_macrolanguage_3_letters_code=$fila479['M_Id'];
												
												
												
											?> 
												<li>
															<center>
																<a 
																
																
																
																<?php
																	if(!empty($related_macrolanguage_3_letters_code))
																	{
																	$alert_message=" \'$idof\' is a sublanguage of \'$related_macrolanguage_3_letters_code\'. You cannot modify or delete a sublanguage directly. You need to click on the \'$related_macrolanguage_3_letters_code\' flag to make modifications.";	
																
																echo " onclick=\"alert('$alert_message');\" ";

																	} 
																	else{  
																	
																	
																	?>
																
																href="../addlanguage/addlanguage.php?lang=<?php echo  "$idof"; ?>&use=learn" title="" style="text-align:center;"
																
																<?php
																	} 
																	
																?>
																
																
																> 
																
																<img class="language language-<?php echo  $idof_2letras; ?> " 
																
																
			
																>
															
															</br>
															

																<img style="margin-top: 5px; width: 50px; height: 5px;" src="./images/language_levels/<?php echo $image_level_lang_ofr; ?>" alt="<?php echo  $idiomas_nivel["$level_idiomaofre"]; ?>" />
															</center>
																		<br> 
															<div style="margin: -20px 0 0 0;" >			
																		<center>
																		 <?php echo  "$idof"; //echo  $idiomas_equiv["$idof"]; ?> 
																		 (<?php echo  $idiomas_nivel["$level_idiomaofre"];?>)
																		 </center>
															</div>			 
															</a>

															<br>
															
															<center>
															
															
															<span style="color:#b2b2b2; font-size: 12px;"><?php echo "$learn_langs_priceorexchangetext_array[$iii]";	 ?></span>
															<div class="tooltip-container" style="font-size: 12px; color:#b2b2b2; margin: -5px 0 0 0; align: center;">
																
																	 <i style="color:#b2b2b2;" class="fas fa-info-circle"></i> 
																	<span class="tooltip-text"><?php echo "$learn_langs_full_name_array[$iii]";	 ?></span>
																
															</div>
															</center>
															

												</li>
                                            <?php
                                            }
											?>
											
											<li style=" vertical-align:top;">
											
											<a href="../addlanguage/addlanguage.php" style="font-size: 33px;"><i class="fas fa-plus-circle"></i></a>
											
											</li>

											
											<?php 
									//aqui avisamos si algún idioma introducido no tiene su nivel insertado
									for($iii=0;$iii<count($learn_langs_level_array);$iii++)
									{
										//echo " $learn_langs_level_array[$iii] ";
										
										if( $learn_langs_level_array[$iii]==0 )
										{ ?>  
										
										<div class="alerta alerta-9">
												(*) Click on the flags to add more information about your languages. 
										</div> 
										
										
										<?php 
										//<h5 style="color: red;">(*) Insert your level for each requested language <a style="color: red;font-weight: bold; text-decoration: underline;" href="../updateinfo/insert_level_offered_language.php">here</a> &#9888;</h5>
										
										break; //cuando se detecta uno solo, se rompe el bucle
										}
										
									}?>
											 
											
											
	
									
                                        <?php /* if (!empty($idof1a)) { ?> <li><a href="#" title="" style="text-align:center;"> <img class="language language-<?php echo  $idof1a; ?> " />
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
                                        
									
									print_r($req_langs_array);
									print_r($req_langs_level_array); 
									print_r($req_langs_level_image_array); 
									
									*/
									
									?>

                                    </ul>
                                </div>
                                <?php //if ($tipo_form != "teacher") {

                                ?>
                                    <div class="epi-sec">

                                    </div>	
									
									
									
									
								
									

									

									 

                                <?         //} 
								

                                ?>


                                <?php        //todos los comentarios
                                $query1 = "
		SELECT  * 
		FROM comentarios 
		WHERE (id_aludido='$identificador2017') AND censurado=0 ORDER BY horacreacion DESC ";
                                $result1 = mysqli_query($link, $query1);
                                $n_comentarios = mysqli_num_rows($result1);
                                //if($n_comentarios)
                                //{ 




                                //los comentarios positivos

                                $query432 = "
		SELECT  * 
		FROM comentarios 
		WHERE (id_aludido='$identificador2017') AND censurado=0 AND rating=1 ORDER BY horacreacion DESC ";
                                $result432 = mysqli_query($link, $query432);
                                $n_comentarios_positivos = mysqli_num_rows($result432);


// para evitar error en php8 de division por cero ponemos if

							if($n_comentarios!=0)
                                $porcentaje_positivos = round($n_comentarios_positivos * 100 / $n_comentarios);
								
								
                                ?>




                                <?php
                                //} 
                                ?>
                                <div class="job_descp">

                                    </br>
									<h3>More information</h3> 
									
                                    <span style="color:#666666;"><?php echo $availability100; ?></span></br></br>
                                    <span style="color:#666666;"><?php echo "$othercomments100"; ?></span>


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

                                        <p style="font-size: 16px"><a href="../infouser/evdone.php"><?php echo "$n_comentarios"; ?> evaluations received </a>

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
		
		WHERE comentarios.id_aludido='$identificador2017' AND comentarios.censurado=0 
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
										
										//php8: a explode no se le puede meter nada con null. si no existe ya el usuario en la bbdd parece que recibe null $autor_ev
										if(!is_null($autor_ev))
										{
											$palabras = explode (" ", $autor_ev);
											$autor_ev=ucfirst($palabras[0]);
										}
										
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

                                            <a href="../infouser/evdone.php" title="">View older evaluations</a> </br>

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













                        <div id="my_events" class="widget widget-jobs">
                            <div class="sd-title">
                                <h3>My events</h3>
                                <!-- <i class="la la-ellipsis-v"></i> -->
                            </div>

                            <div class="jobs-list">
                                <div class="job-info">
                                    <?php
                                    $tiempo_corte = time();
                                    //el tiempo de corte lo dejamos a un dia antes por lo de los husos horarios.
                                    $tiempo_corte = time() - 24 * 3600;
                                    //maximos eventos que vamos a mostrar por pantalla
                                    $max_events_shown = 5;
                                    //sacamos los eid de la table eventoslista que sean futuros
                                    $query = "SELECT * FROM eventoslista WHERE unix_start_time>'$tiempo_corte' AND id_creador='$identificador2017' AND Yaborrado='0' ORDER BY unix_start_time ASC";
                                    $result = mysqli_query($link, $query);
                                    $nuevos = mysqli_num_rows($result);

                                    if (!$nuevos)
                                        echo "You have not created any event. <a href=\"../events/createevent.php\">Create one event</a> now.";

                                    ?>
                                    <?php


                                    $max_events_shown_2 = min($max_events_shown, $nuevos);

                                    $clausula_in_eventos = '';
                                    for ($i = 0; $i < $max_events_shown_2; $i++) {
                                        $fila = mysqli_fetch_array($result);
                                        $eid_bd = $fila['city'];
                                        $ev_broadcasted = $fila['Broadcasted'];
										$id_event1=$fila['Id'];
										$es_replica_ev=$fila['Createdfromid'];

                                    ?>
                                        <?php

                                        $ciudad_abbr = $fila['city'];
                                        $ciudad_abbr = substr($ciudad_abbr, 0, 14);
                                        // echo "$ciudad_abbr ";    



                                        ?>


                                        <?php


                                        require('../files/idiomasequivalencias.php');

                                        $lengua1 = $idiomas_equiv["{$fila['Idioma']}"];
                                        $lengua1 = substr($lengua1, 0, 14);

                                        // echo "(" . $lengua1 . ")";

                                        ?> <?php

                                            $fecha1 = substr($fila['start_time'], 0, 10);

                                            $unixtime1 = strtotime($fecha1);
                                            $dayOfWeek = date("l", $unixtime1);

                                            $dayOfWeek_corto = substr($dayOfWeek, 0, 3);

                                            // echo "<span>$fecha1 ($dayOfWeek_corto.)</span>"; 


                                            ?>







                                        <div class="job-details sgt-text suggestion-usd">
                                            <div class="hr-rate">
                                                <a href="../events/eventdetails.php?idev=<?php echo $fila['Id']; ?>">
                                                    <span>
                                                        <i class="la la-chevron-right"></i></span> </a>

                                            </div>
                                            <!-- <div class="sgt-text"> -->
                                            <a href="../events/eventdetails.php?idev=<?php echo $fila['Id']; ?>">
                                                <h4>In <?php echo $ciudad_abbr; ?> <?php echo "(" . $lengua1 . ")"; ?>
                                                    <?php

                                                    if ($ev_broadcasted) {
                                                    ?>
                                                        <img src="../images/recommended.png" alt="Promoted by Lingua2" height="15" />
                                                    <?php }

                                                    ?>
                                                </h4>
                                            </a>

                                            <p> <?php echo $fecha1 . "(" . $dayOfWeek_corto . ")";  ?>
											
											</br>
											
											<span style="font-size: 80%; ">	<?php if (!$ev_broadcasted) {   echo "<a href=\"../events/radaruserevent.php?evid=$id_event1\">Promote</a> · ";   }     ?>  	
											
											<?php if(is_null($es_replica_ev)){  echo "<a href=\"../events/makerecurrentshowinfo.php?idev=$id_event1\">Make weekly</a> · ";    }    ?>
											
											<?php  echo "<a href=\"../events/cancelevent.php?idev=$id_event1\">Cancel</a> ";        ?>
											
											
											</span>
											
											</p>
                                            <!-- </div> -->
											
											

                                        </div>





                                    <?php


                                    }
                                    ?>
                                    <?php




                                    if ($nuevos >= $max_events_shown) :
                                    ?>

                                        <div class="view-more" style="height: 50px;">

                                            <a href="../events/showallupcomingevents.php" title="">View More</a>

                                        </div>

                                    <?php
                                    endif;
                                    ?>
                                </div>
                                <!--job-info end-->
                            </div>
                            <!--jobs-list end-->
                        </div>














                        <div id="events" class="widget widget-jobs">
                            <div class="sd-title">
                                <h3>Events worldwide</h3>
                                <!-- <i class="la la-ellipsis-v"></i> -->
                            </div>

                            <div class="jobs-list">
                                <div class="job-info">
                                    <?php
                                    $tiempo_corte = time();
                                    //el tiempo de corte lo dejamos a un dia antes por lo de los husos horarios.
                                    $tiempo_corte = time() - 24 * 3600;
                                    //maximos eventos que vamos a mostrar por pantalla
                                    $max_events_shown = 5;
                                    //sacamos los eid de la table eventoslista que sean futuros
                                    $query = "SELECT * FROM eventoslista WHERE unix_start_time>'$tiempo_corte' AND Yaborrado='0' ORDER BY unix_start_time ASC";
                                    $result = mysqli_query($link, $query);
                                    $nuevos = mysqli_num_rows($result);

                                    if (!$nuevos)
                                        echo "No events at the moment. <a href=\"../events/createevent.php\">Create one event</a> now.";

                                    ?>
                                    <?php
									
									$max_events_shown_2 = min($max_events_shown, $nuevos);

                                    $clausula_in_eventos = '';
                                    for ($i = 0; $i < $max_events_shown_2; $i++) {
                                        $fila = mysqli_fetch_array($result);
                                        $eid_bd = $fila['city'];
                                        $ev_broadcasted = $fila['Broadcasted'];


                                    ?>
                                        <?php

                                        $ciudad_abbr = $fila['city'];
                                        $ciudad_abbr = substr($ciudad_abbr, 0, 14);
                                        // echo "$ciudad_abbr ";    



                                        ?>


                                        <?php


                                        

                                        $lengua1 = $idiomas_equiv["{$fila['Idioma']}"];
                                        $lengua1 = substr($lengua1, 0, 14);

                                        // echo "(" . $lengua1 . ")";

                                        ?> <?php

                                            $fecha1 = substr($fila['start_time'], 0, 10);

                                            $unixtime1 = strtotime($fecha1);
                                            $dayOfWeek = date("l", $unixtime1);

                                            $dayOfWeek_corto = substr($dayOfWeek, 0, 3);

                                            // echo "<span>$fecha1 ($dayOfWeek_corto.)</span>"; 


                                            ?>







                                        <div class="job-details sgt-text suggestion-usd">
                                            <div class="hr-rate">
                                                <a href="../events/eventdetails.php?idev=<?php echo $fila['Id']; ?>">
                                                    <span>
                                                        <i class="la la-chevron-right"></i></span> </a>

                                            </div>
                                            <!-- <div class="sgt-text"> -->
                                            <a href="../events/eventdetails.php?idev=<?php echo $fila['Id']; ?>">
                                                <h4>In <?php echo $ciudad_abbr; ?> <?php echo "(" . $lengua1 . ")"; ?>
                                                    <?php

                                                    if ($ev_broadcasted) {
                                                    ?>
                                                        <img src="../images/recommended.png" alt="Promoted by Lingua2" height="15" />
                                                    <?php }

                                                    ?>
                                                </h4>
                                            </a>

                                            <p> <?php echo $fecha1 . "(" . $dayOfWeek_corto . ")";  ?></p>
                                            <!-- </div> -->

                                        </div>





                                    <?php


                                    }
                                    ?>
                                    <?php




                                    if ($nuevos >= $max_events_shown) :
                                    ?>

                                        <div class="view-more" style="height: 50px;">

                                            <a href="../events/showallupcomingevents.php" title="">View More</a>

                                        </div>

                                    <?php
                                    endif;
                                    ?>
                                </div>
                                <!--job-info end-->
                            </div>
                            <!--jobs-list end-->
                        </div>

                    </div>
                    <!--widget-about end-->

                    <!--widget-jobs end-->
                    <div class="widget widget-jobs" >
                        <div class="sd-title">
                            <h3>Most Viewed Jobs This Week</h3>
                            <!-- <i class="la la-ellipsis-v"></i> -->
                        </div>
                        <div class="jobs-list">
                            <div class="job-info">
                                <div class="job-details">
                                    <h3>Controller Junior</h3>
                                    <p>Deloitte (Remote job)</p>
                                </div>
                                <div class="hr-rate">
                                    <span>22,000&euro;/year</span>
                                </div>
                            </div>
                            <!--job-info end-->
                            <div class="job-info">
                                <div class="job-details">
                                    <h3>Senior UI / UX Designer</h3>
                                    <p>Lintech (Barcelona)</p>
                                </div>
                                <div class="hr-rate">
                                    <span>21&euro;/h</span>
                                </div>
                            </div>
                            <!--job-info end-->
                            <div class="job-info">
                                <div class="job-details">
                                    <h3>Junior SEO Designer</h3>
                                    <p>HCL (Remote job)</p>
                                </div>
                                <div class="hr-rate">
                                    <span>16&euro;/h</span>
                                </div>
                            </div>
                            <!--job-info end-->
							
							
							<div class="view-more">
                                    <a href="../testingprototypes/mostviewedjobs.php" title="">Check them out!</a>
                            </div>
							
							
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
            $("#seccion_teach").attr("hidden", true);
            $("#seccion_teach_clone").attr("hidden", false);
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

<?php
//hay que pasar la variable identificador del usuario consultado

require('../templates/footer.php');

?>