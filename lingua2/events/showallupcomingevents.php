<?php

require('../files/bd.php');
session_start();



//hay que pasar la variable header 

require('../templates/header_simplified.html');

require('../files/idiomasequivalencias.php');







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













?>














<div class="main-section">
    <div class="container">
        <div class="main-section-data">
            <div class="row">


                <div class="col-lg-3 col-md-4 pd-left-none no-pd">
                    <div class="main-left-sidebar no-margin">
















                        <div id="my_events" class="widget widget-jobs">
                            <div class="sd-title">
                                <h3>My events</h3>
								
								<center>
								<form method="get" action="./createevent.php">
								<button type="submit"
								
								style="
										  background-color: #e65f00;  border: none;
										  color: white;
										  padding: 10px 11px;
										  text-align: center;
										  border-radius: 10px;
									  "
								
								
								>Create new event</button>
								</form>
								</center>
								
								
                                <!-- <i class="la la-ellipsis-v"></i> -->
                            </div>

                            <div class="jobs-list">
                                <div class="job-info">
                                    <?php
                                    $tiempo_corte = time();
                                    //el tiempo de corte lo dejamos a un dia antes por lo de los husos horarios.
                                    $tiempo_corte = time() - 24 * 3600;
                                    //maximos eventos que vamos a mostrar por pantalla
                                    $max_events_shown = 10000;
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



</div></div>



                <div class="col-lg-6 col-md-7 no-pd">
                    <div class="main-ws-sec">




<?php


                                    $tiempo_corte = time();
                                    //el tiempo de corte lo dejamos a un dia antes por lo de los husos horarios.
                                    $tiempo_corte = time() - 24 * 3600;

$query="SELECT DISTINCT city FROM eventoslista WHERE unix_start_time>'$tiempo_corte' AND Yaborrado='0' ORDER BY city ASC";
// echo $query; 
$result=mysqli_query($link,$query);
$nuevos=mysqli_num_rows($result);

$array_ciudades=array();

for($i=0;$i<$nuevos;$i++)
{
		$fila=mysqli_fetch_array($result);
		$array_ciudades[]=$fila['city'];
		
} 

//print_r($array_ciudades);

$ciudadeventoselec=$_POST['ciudadevento'];

?>




                        <div id="events" class="widget widget-jobs">
                            <div class="sd-title">
                                <h3>Events worldwide
								
								
								
								
<form ACTION="<?php echo $PHP_SELF ?>" METHOD="POST" target="" form="formciudadevento" id="formciudadevento">

</br></br>
 
<span style="font-family:arial;font-size:13px;">Filter by city&nbsp;

<SELECT id="ciudadevento" NAME="ciudadevento" onchange="this.form.submit();" style="width:200px"></span> 

<OPTION VALUE="">Select city</OPTION> 
<?php
foreach ($array_ciudades as &$valor_ciudad)  
{
	if($valor_ciudad==$ciudadeventoselec){ $seleccionado="selected"; }	else{ $seleccionado="";}

	// cogemos los valores que no esten vacios
	if( !empty($valor_ciudad) ) 
	{
		$valor_ciudad_abbr=substr($valor_ciudad,0,50);;
		echo "<OPTION $seleccionado VALUE=\"$valor_ciudad\">$valor_ciudad_abbr</OPTION>"; 
	}
}
?> 
</SELECT>


</form>
								
								
								
	<?php		
if(!empty($ciudadeventoselec))
{
	//$filtrar_ciudad="Ciudad='$ciudadeventoselec' AND Unixtimestart>'$tiempo_corte'";
	$filtrar_ciudad="city='$ciudadeventoselec' AND unix_start_time>'$tiempo_corte' ";
}
else
{
	//$filtrar_ciudad=" Unixtimestart>'$tiempo_corte' ";
	//$filtrar_ciudad=" Eventoestatus <> 'not_in_fb' ";
	$filtrar_ciudad="unix_start_time>'$tiempo_corte' ";
}
?> 


			
								
								
								
								</h3>
                                <!-- <i class="la la-ellipsis-v"></i> -->
                            </div>

                            <div class="jobs-list">
                                <div class="job-info">
                                    <?php

                                    //maximos eventos que vamos a mostrar por pantalla
                                    $max_events_shown = 100;
                                    //sacamos los eid de la table eventoslista que sean futuros
                                    $query = "SELECT * FROM eventoslista WHERE $filtrar_ciudad AND Yaborrado='0' ORDER BY unix_start_time ASC LIMIT 100";
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
                                                        <i class="la la-chevron-right" style="margin-right: 20px;"></i></span> </a>

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
						
						
						
			  </div>  </div>			
						
						
						
						
						
						
						
			                <div class="col-lg-3 pd-right-none no-pd ">
                    <div class="right-sidebar">			
						
						
						
						
                        <div id="my_events" class="widget widget-jobs">
                            <div class="sd-title">
                                <h3>Events nearby</h3>
                                <!-- <i class="la la-ellipsis-v"></i> -->
                            </div>

                            <div class="jobs-list">
                                <div class="job-info">
                                    <?php
                                    $tiempo_corte = time();
                                    //el tiempo de corte lo dejamos a un dia antes por lo de los husos horarios.
                                    $tiempo_corte = time() - 24 * 3600;
                                    //maximos eventos que vamos a mostrar por pantalla
                                    $max_events_shown = 10000;
                                    //sacamos los eid de la table eventoslista que sean futuros
                                    
									
									
									
									
									 
                                   


$query="
SELECT 
* ,

(acos(sin(radians(gc.lat)) * sin(radians($gpslat11)) + 
cos(radians(gc.lat)) * cos(radians($gpslat11)) * 
cos(radians(gc.lng) - radians($gpslng11))) * 6378) 

AS distanciaPunto1Punto2

FROM eventoslista el

INNER JOIN gpscities gc

ON el.city = gc.city_ascii

WHERE el.unix_start_time>'$tiempo_corte' AND Yaborrado='0'

HAVING distanciaPunto1Punto2 < 50

ORDER BY el.unix_start_time ASC

";


//echo "$query";






								   $result = mysqli_query($link, $query);
                                    $nuevos = mysqli_num_rows($result);

                                    if (!$nuevos)
                                        echo "No events nearby. <a href=\"../events/createevent.php\">Create one event</a> now.";

                                    ?>
                                    <?php


                                    $max_events_shown_2 = min($max_events_shown, $nuevos);

                                    $clausula_in_eventos = '';
                                    for ($i = 0; $i < $max_events_shown_2; $i++) {
                                        $fila = mysqli_fetch_array($result);
                                        $eid_bd = $fila['city'];
                                        $ev_broadcasted = $fila['el.Broadcasted'];


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