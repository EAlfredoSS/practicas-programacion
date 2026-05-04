<?php

session_start();
require("../files/bd.php");

$identificador2017 = $_SESSION['orden2017'];

//die("hola: $identificador2017");


//IMPORTANTE: puede darse el caso de que un usuario pendiente de evaluar se haya dado de baja
//en este caso lo que haremos será borrar la pareja en la tabla 'couples2009antiguos' para que no salga

$iddeloscompaneros = array();


$query_46 = "SELECT * FROM mentor2009 WHERE orden='$identificador2017' ";

$result_46 = mysqli_query($link, $query_46);
$nuevos_46 = mysqli_num_rows($result_46);
if (!$nuevos_46)
    die('User does not exist or you disconnected. Login from the Homepage');

$fila_46 = mysqli_fetch_array($result_46);
$miemail = $fila_46['Email'];

//echo "$miemail";

//sacamos las parejas del usuario cuando este se encuentra en id_1
$query_couples1 = "SELECT * FROM couples2009antiguos WHERE user_id_1='$identificador2017' ";

$result_couples1 = mysqli_query($link, $query_couples1);
$nuevos_couples1 = mysqli_num_rows($result_couples1);

//echo "nuevos: $nuevos_couples1";

for ($i = 0; $i < $nuevos_couples1; $i++) {
    $fila_couples1 = mysqli_fetch_array($result_couples1);
    $id_del_companero1 = $fila_couples1['user_id_2'];
    array_push($iddeloscompaneros, $id_del_companero1);

    //echo "$email_del_companero1 ";
}

//print_r($emaildeloscompaneros);


//sacamos las parejas del usuario cuando este se encuentra en id_2
$query_couples2 = "SELECT * FROM couples2009antiguos WHERE user_id_2='$identificador2017' ";

$result_couples2 = mysqli_query($link, $query_couples2);
$nuevos_couples2 = mysqli_num_rows($result_couples2);

//echo "nuevos: $nuevos_couples1";

for ($i = 0; $i < $nuevos_couples2; $i++) {
    $fila_couples2 = mysqli_fetch_array($result_couples2);
    $id_del_companero2 = $fila_couples2['user_id_1'];
    array_push($iddeloscompaneros, $id_del_companero2);
}

//print_r($emaildeloscompaneros);

$sizeof_array = count($iddeloscompaneros);

//echo "</br></br>count $sizeof_array ---</br></br>";

for ($i = 0; $i < $sizeof_array; $i++) {
    $id_de_partner = array_pop($iddeloscompaneros);

    //echo "$i $email_de_partner ";

    $query_detectar_borrados = "SELECT * FROM mentor2009 WHERE orden='$id_de_partner'";
    //echo "</br>$query_detectar_borrados</br>";
    $result_detectar_borrados = mysqli_query($link, $query_detectar_borrados);
    $nuevos_detectar_borrados = mysqli_num_rows($result_detectar_borrados);

    if ($nuevos_detectar_borrados == 0) {
        $query_borrar1 = "DELETE FROM couples2009antiguos WHERE user_id_1 = '$id_de_partner' AND user_id_2='$identificador2017' ";
        $result_borrar1 = mysqli_query($link, $query_borrar1);
        $nuevos_borrar1 = mysqli_num_rows($result_borrar1);

        $query_borrar2 = "DELETE FROM couples2009antiguos WHERE user_id_2 = '$id_de_partner' AND user_id_1='$identificador2017' ";
        $result_borrar2 = mysqli_query($link, $query_borrar2);
        $nuevos_borrar2 = mysqli_num_rows($result_borrar2);

        $couples_borradas = $nuevos_borrar1 + $nuevos_borrar2;

        /*if($couples_borradas):
				echo "</br></br> parejas borradas: $couples_borradas</br></br>";
			endif;	
			*/
    }
}




////////// final borrado de las parejas en la cuales su compañero no se ha dado de baja ///////////	
?>




<?php require("../templates/header_simplified.html"); ?>
<div class="main-section">
    <div class="container">
        <div class="main-section-data">
            <div class="row">
                <!--post-bar end-->
                
                    <div class="col-lg-3 col-md-4 pd-left-none no-pd">
                        <div class="main-left-sidebar no-margin">
                        </div>
                    </div>

                    <div class="col-lg-6 col-md-7 no-pd">
                        <div class="main-ws-sec">
                        <div class="top-profiles">
                         <div class="pf-hd"> 
                            <?php

                            //Necesitamos el Codigoborrar para pasarlo a las consultas de debajo

                            $query_23 = "SELECT * FROM mentor2009 WHERE orden='$identificador2017' ";

                            $result_23 = mysqli_query($link, $query_23);
                            $nuevos_23 = mysqli_num_rows($result_23);
                            if (!$nuevos_23)
                                die('User does not exist or you disconnected. Login from the Homepage');

                            $fila_23 = mysqli_fetch_array($result_23);
                            $c = $fila_23['Codigoborrar'];

                            //die("$c");

                            //////////////////////////////////////////////////////////////////////////////////////////////////////////////////

                            $query_vote = "SELECT * FROM mentor2009 WHERE Codigoborrar='$c' ";

                            $result_vote = mysqli_query($link, $query_vote);
                            $nuevos_vote = mysqli_num_rows($result_vote);
                            if (!$nuevos_vote)
                                die('User does not exist or you disconnected. Login from the Homepage');

                            $fila_vote = mysqli_fetch_array($result_vote);
                            $identifier = $fila_vote['orden'];

                            //contactado=1 porque solo se evalua a los que han aceptado la solicitud
                            $query_vote = "SELECT * FROM couples2009antiguos WHERE (voted_1=0 AND user_id_1='$identifier') AND contactado=1 ";

                            $result_vote = mysqli_query($link, $query_vote);
                            $nuevos_vote = mysqli_num_rows($result_vote);

                            ?>

                            <h3>

                                <?php

                                $total_de_acciones = $nuevos_vote;

                                if ($nuevos_vote)
                                    echo "Users that accepted your request (Pending actions: $nuevos_vote)";

                                ?>
                            </h3>


                           <!-- </div> -->
                            </div>

                           
                        

                            <?php

                            //echo "<table border=\"0\" >";
                            for ($i = 0; $i < $nuevos_vote; $i++) {

                                $fila_vote = mysqli_fetch_array($result_vote);
                                $ident_otro_usu = $fila_vote['user_id_2'];
                                $codigo_votante = $fila_vote['code_1'];

                                //info del otro usuario
                                $query_vote1 = "SELECT * FROM mentor2009 WHERE orden='$ident_otro_usu' ";
                                $result_vote1 = mysqli_query($link, $query_vote1);

                                $fila_vote1 = mysqli_fetch_array($result_vote1);
                                $ident_otro_usu = $fila_vote1['orden'];
                                $nombre_otro_usu = $fila_vote1['nombre'];
                                $fotoext1 = $fila_vote1['fotoext'];

                                $ciudad3 = $fila_vote1['Ciudad'];
                                $pais3 = $fila_vote1['Provincia'];


                                // end info otro usu
                                /*
		echo "<tr height=\"10\"><td> <a href=\"./user_card.php?identificador=$ident_otro_usu \" target=\"_blank\">$nombre_otro_usu personal information</a> &nbsp;&nbsp;&nbsp;&nbsp;</td><td>  
		<a href=\"./rate_friend.php?c=$codigo_votante\" target=\"_blank\">Rate $nombre_otro_usu</a> </td></tr>  <br>";
		*/

                                if (file_exists("../uploader/upload_pic/thumb_$ident_otro_usu.$fotoext1"))
                                    $image1 = "../uploader/upload_pic/thumb_$ident_otro_usu.$fotoext1";
                                else
                                    $image1 = "../uploader/default.jpg";




                            ?>
                                <div class="profiles-slider">
                                    <div class="user-profy">


                                        <img src="<?php echo $image1; ?>" alt="" height="57" width="57">
                                        <h3><?php echo $nombre_otro_usu; ?></h3>

                                        <?php if ($pais3) :
                                            $ubicacion3 = "$ciudad3 ($pais3)";

                                        else :
                                            $ubicacion3 = "$ciudad3";

                                        endif; ?>

                                        <span><?php echo "$ubicacion3"; ?></span>

                                        <ul>
                                            <li><a href="./rate_friend.php?c=<?php echo "$codigo_votante"; ?>" title="" style="background-color:#e65f00">Write evaluation</a></li>
                                        </ul>
                                        <a href="../user/u.php?identificador=<?php echo "$ident_otro_usu"; ?>" title="">View profile</a>


                                    </div>
                                    <!--user-profy end-->
                                </div>
                                <!--profiles-slider end-->


                            <?php //////cambio de tarjeta gorda/////////////////////////////////////////////////////////////////////////////////////////////////////////////////// 




                            }
                            ?>

                            
                            <!--top-profiles end-->
</div>

                            <div class="top-profiles">
                                <div class="pf-hd">

                                    <?php


                                    //echo "</table>";

                                    //votaciones pendientes
                                    //hay que pasar el codigo de usuario c

                                    //require('bd.php');

                                    $query_vote = "SELECT * FROM mentor2009 WHERE Codigoborrar='$c' ";

                                    $result_vote = mysqli_query($link, $query_vote);
                                    $nuevos_vote = mysqli_num_rows($result_vote);
                                    if (!$nuevos_vote)
                                        die('User does not exist.');

                                    $fila_vote = mysqli_fetch_array($result_vote);
                                    $identifier = $fila_vote['orden'];

                                    ////////////////////////



                                    //aqui no hay que poner el contactado=1 porque eres tu el que tienes que aceptar la solicitud
                                    $query_vote = "SELECT * FROM couples2009antiguos WHERE (voted_2=0 AND user_id_2='$identifier') AND (contactado=0 OR contactado=1) ";

                                    $result_vote = mysqli_query($link, $query_vote);
                                    $nuevos_vote = mysqli_num_rows($result_vote);

                                    ?>


                                    <h3>

                                        <?php
                                        $total_de_acciones = $total_de_acciones + $nuevos_vote;

                                        if ($nuevos_vote)
                                            echo "Users contacted by you (Pending actions: $nuevos_vote)";

                                        ?>
                                    </h3>

                                    <!--  -->
                                    </div>

                                    <?php



                                    for ($i = 0; $i < $nuevos_vote; $i++) {
                                    ?>
                                        <div class="profiles-slider">
                                            <div class="user-profy">


                                                <?php


                                                $fila_vote = mysqli_fetch_array($result_vote);
                                                $ident_otro_usu = $fila_vote['user_id_1'];
                                                $codigo_votante = $fila_vote['code_2'];


                                                //info del otro usuario
                                                $query_vote1 = "SELECT * FROM mentor2009 WHERE orden='$ident_otro_usu' ";
                                                $result_vote1 = mysqli_query($link, $query_vote1);

                                                $fila_vote1 = mysqli_fetch_array($result_vote1);
                                                $ident_otro_usu = $fila_vote1['orden'];
                                                $nombre_otro_usu = $fila_vote1['nombre'];
                                                $fotoext2 = $fila_vote1['fotoext'];

                                                $ciudad4 = $fila_vote1['Ciudad'];
                                                $pais4 = $fila_vote1['Provincia'];

                                                if ($pais4) :
                                                    $ubicacion4 = "$ciudad4 ($pais4)";

                                                else :
                                                    $ubicacion4 = "$ciudad4";

                                                endif;


                                                // end info otro usu


                                                if (file_exists("../uploader/upload_pic/thumb_$ident_otro_usu.$fotoext2"))
                                                    $image2 = "../uploader/upload_pic/thumb_$ident_otro_usu.$fotoext2";
                                                else
                                                    $image2 = "../uploader/default.jpg";

                                                ?>



                                                <img src="<?php echo $image2; ?>" alt="" height="57" width="57">
                                                <h3><?php echo $nombre_otro_usu; ?></h3>
                                                <span><?php echo $ubicacion4; ?></span>


                                                <?php


                                                //<pre> conserva espacios y tabuladores
                                                //echo "<a href=\"./rate_friend.php?c=$codigo_votante\" style=\"color:red; font-size:14px;  \"  target=\"_blank\">Evaluate $nombre_otro_usu</a> &nbsp;&nbsp;<a href=\"./user_card.php?identificador=$ident_otro_usu \"  style=\"color:grey; font-size:10px;  \"  target=\"_blank\">[$nombre_otro_usu info]</a> <br>";

                                                //<pre> conserva espacios y tabuladores 
                                                if ($fila_vote['contactado'] == 0) {

                                                ?>

                                                    <ul>
                                                        <li><a href="./contact_yes.php?cod=<?php echo $codigo_votante; ?>" title="" style="background-color:green">Accept request</a></li>
                                                        <!--<li><a href="#" title="" class="envlp"><img src="images/envelop.png" alt=""></a></li>-->
                                                        <li><a href="./contact_no.php?cod=<?php echo $codigo_votante; ?>" title="" style="background-color:red">Reject request</a></li>
                                                    </ul>
                                                    <a href="../user/u.php?identificador=<?php echo $ident_otro_usu; ?>" title="">View Profile</a>
                                                <?php

                                                }
                                                if ($fila_vote['contactado'] == 1) {

                                                ?>

                                                    <ul>
                                                        <li><a href="./rate_friend.php?c=<?php echo $codigo_votante; ?>" title="" style="background-color:#e65f00">Write evaluation</a></li>
                                                    </ul>
                                                    <a href="../user/u.php?identificador=<?php echo $ident_otro_usu; ?>" title="">View Profile</a>




                                                <?php





                                                }

                                                ?>

                                            </div>
                                            <!--user-profy end-->
                                        </div>
                                        <!--profiles-slider end-->

                                    <?php
                                    }





                                    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                                    ?>




                                  
                                    </div>
                                    <!--top-profiles end-->


                                    <?php
                                    if ($total_de_acciones == 0) :
                                        echo "</br><center>You have no actions pending at this moment</center></br>";
                                    endif;
                                    ?>


                                </div>
                            </div>


                        </div>
                    </div>

                    <div class="col-lg-3 pd-right-none no-pd ">
                        <div class="right-sidebar">

                        </div><!-- main-section-data end-->

                    </div>

                </div><!-- main-section-data end-->

            </div>
        </div>
    </body>

        </html>

        <?php
//footer

require('../templates/footer.php');

?>
    