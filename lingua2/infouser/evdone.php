<?php

session_start();
require("../files/bd.php");

$identificador2017 = $_SESSION['orden2017'];

//die("hola: $identificador2017");




$idendeloscompaneros = array();


$query_46 = "SELECT * FROM mentor2009 WHERE orden='$identificador2017' ";

$result_46 = mysqli_query($link, $query_46);
$nuevos_46 = mysqli_num_rows($result_46);
if (!$nuevos_46)
    die('User does not exist or you disconnected. Login from the Homepage');

$fila_46 = mysqli_fetch_array($result_46);
$miemail = $fila_46['Email'];

$email_del_usu = $miemail; //esta variable la usamos más abajo

//echo "$miemail";



?>




<?php require("../templates/header_simplified.html"); ?>
<div class="main-section">
    <div class="container">

					
					
					
					
					
					                            <?php
												
												
												
												
												
												
												
												
												
								                                        //todos los comentarios
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


								//php8: division por cero no se puede, por eso el if
								if($n_comentarios!=0)
								{
									$porcentaje_positivos = round($n_comentarios_positivos * 100 / $n_comentarios);
                               	}		
												
												
												
												
												
												
												
                            //numero maximo de evaluaciones que queremos mostrar
                            //$num_max_ev_mostradas = 2;



                            ?>
							
							
        <div class="main-section-data">
            <div class="row">							
							
 <div class="col-lg-3 col-md-4 pd-left-none no-pd"></div>


 <div class="col-lg-6 col-md-7 no-pd" >
 <div	class="main-ws-sec" >
 



                            <div class="top-profiles ">
                                <div class="pf-hd">
                                    <h3>All my evaluations

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


                                    $num_ev_bucle = $num_evaluaciones;

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
                                                    <li><a href="../user/u.php?identificador=<?php echo $orden47; ?>" title="">Visit profile</a></li>
                                                      <li><a href="../user/reportabuse.php" title="">Report abuse</a></li>
													<li><a href="../user/resolutioncenter.php" title="">Resolution center</a></li>
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

                                    //view more solo si supera el número máximo de evaluaciones que se muestran en esta página

/*
                                    if ($num_evaluaciones > $num_max_ev_mostradas) :

                                    ?>
                                        <div class="view-more" style="height: 50px;">

                                            <a href="../infouser/evdone.php" title="">View more</a> </br>

                                        </div>



                                    <?php

                                    endif;
									*/

                                    ?>


                                </div>

                            </div>
					
					
					
					
					
					
					
					
					
					
					

                    <div class="col-lg-3 pd-right-none no-pd ">
                        <div class="right-sidebar">

                        </div><!-- main-section-data end-->

                    </div>

                </div><!-- main-section-data end-->

            </div>
        </div>
		
		
    </div> </div> </div>    
		
		
		
    </body>

        </html>

        <?php
//footer

require('../templates/footer.php');

?>
    