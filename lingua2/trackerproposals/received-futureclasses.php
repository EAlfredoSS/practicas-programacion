<?php
//$mi_identificador = 4588;
session_start();
$mi_identificador=$_SESSION['orden2017'];

require('../templates/header_simplified.html');
require('../files/bd.php');
require('../funcionesphp/funciones_idiomas_usuario.php');

//sacamos time shift
$query77="SELECT timeshift FROM mentor2009 WHERE orden='$mi_identificador' ";
$result77=mysqli_query($link,$query77);
if(!mysqli_num_rows($result77))
    die("User unregistered 1.");
$fila77=mysqli_fetch_array($result77);

$my_timeshift=$fila77['timeshift'];

// Crear un objeto DateTime con la fecha y hora actuales
$fechaHoraActual = new DateTime();
$fechaHoraFormateada = $fechaHoraActual->format('Y-m-d H:i:s');
$fechaHoraUTC0 =$fechaHoraFormateada;
$zonaHoraria = $my_timeshift;
$tiempoUnix=time();

// Consulta SQL para obtener las clases
$query = "
SELECT t.*, m.*
FROM tracker t
INNER JOIN mentor2009 m
ON t.id_user_teacher=m.orden
WHERE t.id_user_teacher ='" . $mi_identificador . "'  AND proposal_accepted_teacher=2 AND cancelled=0  AND end_time_unix>$tiempoUnix
ORDER BY t.start_time_unix ASC";

$result = mysqli_query($link, $query);
$nuevos = mysqli_num_rows($result);
$n_next_lessons=$nuevos;

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Next classes as teacher</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
<style>
:root {
    --primary-orange: #d35400;
    --accent-orange: #e67e22;
    --light-orange: #fdf2e9;
    --pending-yellow: #f1c40f;
    --pending-bg: #fcf3cf;
    --confirmed-green: #27ae60;
    --confirmed-bg: #eafaf1;
    --waiting-grey: #95a5a6;
    --waiting-bg: #f2f3f4;
    --text-dark: #2c3e50;
    --text-grey: #7f8c8d;
    --border-color: #ecf0f1;
    --bg-grey: #f4f7f6;
}

body {
    font-family: 'Roboto', sans-serif;
    background-color: var(--bg-grey);
    margin: 0;
    padding: 0;
}

/* Forum/Nav Links */
.forum-links {
    background-color: #fff;
    padding: 10px 0;
    margin-bottom: 10px;
    width: 180%;
    margin-left: -40%;
    margin-top: -5.3%;
}

.forum-links ul {
    list-style-type: none;
    display: flex;
    justify-content: flex-start;
    padding: 0;
    margin: 0;
    padding-left: 450px;
}

.forum-links ul li {
    text-align: center;
    margin-right: 20px;
}

.forum-links ul li a {
    display: inline-block;
    padding: 10px 0;
    text-decoration: none;
    color: #999;
    font-weight: normal;
    font-size: 16px;
    transition: color 0.3s ease;
}

.forum-links ul li.active a {
    color: #e65f00;
    font-weight: bold;
    position: relative;
}

.forum-links ul li.active a::after {
    content: "";
    position: absolute;
    bottom: -2px;
    left: 0;
    right: 0;
    height: 2px;
    background-color: #e65f00;
}

/* Proposal Card */
.proposal-card {
    background: white;
    border-radius: 8px;
    box-shadow: 0 3px 10px rgba(0,0,0,0.08);
    margin-bottom: 20px;
    display: flex;
    flex-wrap: wrap;
    padding: 25px;
    position: relative;
    border-left: 5px solid var(--primary-orange);
    transition: transform 0.2s, box-shadow 0.2s;
}

.proposal-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.12);
}

/* Left Side */
.card-left {
    display: flex;
    flex-direction: column;
    align-items: center;
    margin-right: 25px;
    min-width: 90px;
}

.avatar-circle {
    width: 85px;
    height: 85px;
    border-radius: 100%;
    border: 3px solid var(--primary-orange);
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
    overflow: visible;
    box-shadow: 0 2px 8px rgba(211, 84, 0, 0.2);
}

.star-badge {
    position: absolute;
    bottom: 0;
    right: -5px;
    color: #f1c40f;
    font-size: 16px;
    background: white;
    border-radius: 50%;
    padding: 3px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.status-badge {
    margin-top: 12px;
    font-size: 10px;
    font-weight: bold;
    padding: 5px 10px;
    border-radius: 12px;
    text-transform: uppercase;
    text-align: center;
    min-width: 85px;
}

.status-pending {
    background-color: var(--pending-bg);
    color: #d4ac0d;
    border: 1px solid #f1c40f;
}

.status-confirmed {
    background-color: var(--confirmed-bg);
    color: var(--confirmed-green);
    border: 1px solid #27ae60;
}

.status-waiting {
    background-color: var(--waiting-bg);
    color: var(--waiting-grey);
    border: 1px solid #95a5a6;
}

/* Middle Side */
.card-middle {
    flex-grow: 1;
    padding-right: 20px;
    min-width: 250px;
}

.card-title {
    font-size: 21px;
    font-weight: bold;
    color: var(--text-dark);
    margin-top: 5px;
    margin-bottom: 12px;
    line-height: 1.3;
}

.meta-row {
    display: flex;
    gap: 30px;
    font-size: 15px;
    color: var(--text-dark);
    margin-bottom: 18px;
    flex-wrap: wrap;
}

.meta-item {
    display: flex;
    align-items: center;
    gap: 6px;
    font-weight: 500;
}

.meta-item i { 
    color: var(--accent-orange); 
    width: 18px; 
    text-align: center;
    font-size: 16px;
}

.languages-label {
    font-size: 11px;
    color: #999;
    text-transform: uppercase;
    margin-bottom: 8px;
    margin-top: 5px;
    font-weight: 600;
    letter-spacing: 0.5px;
}

.language-tags {
    display: flex;
    gap: 6px;
    flex-wrap: wrap;
}

.lang-tag {
    background-color: var(--light-orange);
    color: var(--primary-orange);
    padding: 5px 10px;
    border-radius: 5px;
    font-size: 12px;
    font-weight: 600;
    border: 1px solid #f5dcc4;
}

/* Right Side */
.card-right {
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    justify-content: flex-start;
    min-width: 200px;
}

.date-info {
    font-size: 14px;
    color: var(--text-grey);
    margin-bottom: 15px;
    margin-top: 20px;
    width: 100%;
    text-align: right;
    font-weight: 500;
}

.date-info i { 
    margin-right: 6px;
    color: var(--accent-orange);
}

.action-buttons {
    display: flex;
    flex-direction: column;
    gap: 10px;
    width: 100%;
}

.btn {
    border: 2px solid #e67e22;
    background: linear-gradient(135deg, #fdf2e9 0%, #fce8d6 100%);
    padding: 12px 20px;
    border-radius: 8px;
    cursor: pointer;
    font-size: 14px;
    font-weight: 600;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    transition: all 0.3s ease;
    width: 100%;
    color: #d35400;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    box-shadow: 0 2px 5px rgba(230, 126, 34, 0.15);
    text-decoration: none;
}

.btn:hover { 
    background: linear-gradient(135deg, #e67e22 0%, #d35400 100%);
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(230, 126, 34, 0.3);
}

.btn i {
    transition: transform 0.3s ease;
}

.btn:hover i {
    transform: scale(1.1);
}

/* Class Details */
.class-details {
    background: linear-gradient(135deg, #fafafa 0%, #f5f5f5 100%);
    padding: 20px;
    border-radius: 8px;
    border: 1px solid #e0e0e0;
    margin-top: 20px;
    display: none;
    box-shadow: inset 0 1px 3px rgba(0,0,0,0.05);
}

.class-details h6 {
    color: var(--primary-orange);
    font-size: 16px;
    font-weight: bold;
    margin-bottom: 15px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.class-details h4 {
    color: var(--primary-orange);
    font-size: 16px;
    font-weight: bold;
    margin-bottom: 15px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.class-details p {
    margin-bottom: 10px;
    line-height: 1.6;
    color: var(--text-dark);
}

.class-details hr {
    border: none;
    border-top: 2px solid #e0e0e0;
    margin: 15px 0;
}

.class-details strong {
    color: var(--primary-orange);
    font-weight: 600;
}

.forum-page {
    margin-bottom: 20px;
}

/* Mensaje sin resultados */
.empty-state {
    background: white;
    border-radius: 12px;
    box-shadow: 0 3px 15px rgba(0,0,0,0.08);
    padding: 60px 40px;
    text-align: center;
    margin: 40px auto;
    max-width: 600px;
    border-top: 4px solid var(--primary-orange);
}

.empty-state-icon {
    width: 80px;
    height: 80px;
    background: linear-gradient(135deg, #fdf2e9 0%, #fce8d6 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 25px;
    box-shadow: 0 4px 12px rgba(230, 126, 34, 0.2);
}

.empty-state-icon i {
    font-size: 36px;
    color: var(--primary-orange);
}

.empty-state h3 {
    color: var(--text-dark);
    font-size: 24px;
    font-weight: 600;
    margin-bottom: 12px;
}

.empty-state p {
    color: var(--text-grey);
    font-size: 16px;
    line-height: 1.6;
    margin-bottom: 0;
}

/* Media queries */
@media (max-width: 991px) {
    .forum-links {
        position: relative; 
        top: auto; 
        left: auto; 
        width: 140%; 
        background-color: #fff; 
        height: auto; 
        padding: 10px 20px; 
        opacity: 1; 
        visibility: visible; 
        margin-left:-20%;
        margin-top: -8.7%;
    }
    .forum-questions .usr_quest {
        width: 60%;
    }
}

@media (max-width: 768px) {
    .proposal-card {
        flex-direction: column;
        padding: 20px;
    }
    
    .card-left, .card-middle, .card-right {
        width: 100%;
        align-items: flex-start;
        margin-right: 0;
        margin-bottom: 15px;
    }
    
    .card-left {
        flex-direction: row;
        gap: 15px;
        align-items: center;
    }
    
    .date-info {
        text-align: left;
    }
    
    .card-right {
        align-items: flex-start;
    }
    
    .btn {
        width: 100%;
    }
    
    .action-buttons {
        flex-direction: column;
    }
}
</style>
</head>

<?php
$query109="SELECT * FROM tracker WHERE id_user_teacher ='" . $mi_identificador . "'  AND proposal_accepted_teacher=0 AND cancelled=0 AND start_time_unix>$tiempoUnix";
$result109 = mysqli_query($link, $query109);
$n_received_proposals=mysqli_num_rows($result109);

$query109="SELECT * FROM tracker WHERE id_user_teacher ='" . $mi_identificador . "'  AND proposal_accepted_teacher=2 AND cancelled=0 AND paid=1 AND releasefunds=0 AND start_time_unix<=$tiempoUnix";
$result109 = mysqli_query($link, $query109);
$n_past_lessons_not_released=mysqli_num_rows($result109);
?>

<body>
    <div class="wrapper">
        <section class="forum-sec">
            <div class="container">
                <div class="forum-links">
                    <ul>
                        <li class="active"><a href="./received-futureclasses.php" title="">Next lessons as teacher (<?php echo $n_next_lessons; ?>)</a></li>
                        <li><a href="./received-pendingproposals.php" title="">Received proposals as teacher (<?php echo $n_received_proposals; ?>)</a></li>
                        <li><a href="./received-pendingreleasefunds.php" title="">Pending fund releases (<?php echo $n_past_lessons_not_released; ?>)</a></li>
                    </ul>
                </div>
            </div>
        </section>

        <section class="forum-page">
            <div class="container">
                <div class="forum-questions-sec" style="width: 100%">
                    <div class="forum-questions">
                        <?php
                        
                            if(!$n_next_lessons)
                            {
                                echo '
                                <div class="empty-state">
                                    <div class="empty-state-icon">
                                        <i class="fas fa-calendar"></i>
                                    </div>
                                    <h3>No lessons available in this section at the moment</h3>
                                </div>
                                ';
                                require('../templates/footer.php');
                                echo '</div></div></section></div></body></html>';
                                exit;
                            }
                        
                            //my languages
                            $my_langs_array_multidim=array( array() );
                            $my_langs_full_name_array_multidim=array( array() ); 
                            $my_langs_level_array_multidim=array( array() ); 
                            $my_langs_forshare_array_multidim=array( array() );
                            $my_langs_price_array_multidim=array( array() );
                            $my_langs_typeofexchange_array_multidim=array( array() );
                            $my_langs_priceorexchangetext_array_multidim=array( array() );
                            $my_langs_level_image_array_multidim=array( array() );
                            $my_langs_2letters_array_multidim=array( array() ); 
                            
                            //learn languages
                            $learn_langs_array_multidim=array( array() );
                            $learn_langs_full_name_array_multidim=array( array() ); 
                            $learn_langs_level_array_multidim=array( array() ); 
                            $learn_langs_forshare_array_multidim=array( array() );
                            $learn_langs_price_array_multidim=array( array() );
                            $learn_langs_typeofexchange_array_multidim=array( array() );
                            $learn_langs_priceorexchangetext_array_multidim=array( array() );
                            $learn_langs_level_image_array_multidim=array( array() );
                            $learn_langs_2letters_array_multidim=array( array() );
                        
                            //aquí sacamos las lenguas del profe
                            list($my_langs_array_multidim["$mi_identificador"], $my_langs_full_name_array_multidim["$mi_identificador"], 
                            $my_langs_level_array_multidim["$mi_identificador"], 
                            $my_langs_forshare_array_multidim["$mi_identificador"],   
                            $my_langs_price_array_multidim["$mi_identificador"], $my_langs_typeofexchange_array_multidim["$mi_identificador"], 
                            $my_langs_priceorexchangetext_array_multidim["$mi_identificador"], $my_langs_level_image_array_multidim["$mi_identificador"], 
                            $my_langs_2letters_array_multidim["$mi_identificador"])
                            = lenguas_que_conoce_usuario($mi_identificador,$link);
                            
                            list($learn_langs_array_multidim["$mi_identificador"], $learn_langs_full_name_array_multidim["$mi_identificador"], 
                            $learn_langs_level_array_multidim["$mi_identificador"], 
                            $learn_langs_forshare_array_multidim["$mi_identificador"],   $learn_langs_price_array_multidim["$mi_identificador"], 
                            $learn_langs_typeofexchange_array_multidim["$mi_identificador"], 
                            $learn_langs_priceorexchangetext_array_multidim["$mi_identificador"], $learn_langs_level_image_array_multidim["$mi_identificador"], 
                            $learn_langs_2letters_array_multidim["$mi_identificador"])
                            = lenguas_que_quiere_estudiar_usuario($mi_identificador,$link);
                        
                        
                            while ($fila = mysqli_fetch_array($result)) {
                                // Datos de la clase
                                $unixtimestart = $fila['start_time_unix'];
                                $id_of_class=$fila['id_tracking']; 
                                $creation_timestamp=$fila['created_timestamp'];
                                $recurrent=$fila['created_from_recurrent']; if($recurrent==1){$recurrent='Yes';} else{$recurrent='No';}
                                $id_student=$fila['id_user_student'];
                                $time_shift_student=$fila['time_shift_student'];
                                $dateofstart_local=$fila['date_start_local'];
                                $dateofend_local=$fila['date_end_local'];
                                $unixtimestart=$fila['start_time_unix'];
                                $unixtimeend=$fila['end_time_unix'];
                                $duration_min=$fila['session_lenght_minutes'];
                                $language_to_teach=$fila['language_taught'];
                                $hourly_price=$fila['hourly_rate_original'];
                                $total_price=$fila['price_session_total'];  $total_price=round($total_price,2);
                                $descriptionofsession=$fila['description_session'];
                                $teacher_accepted=$fila['proposal_accepted_teacher'];
                                $teacher_accepted_timestamp=$fila['proposal_accepted_timestamp'];
                                $session_paid=$fila['paid'];
                                $session_paid_timestamp=$fila['timestamp_paid'];
                                
                                $session_releasefunds=$fila['releasefunds'];
                                
                                $cancelled=$fila['cancelled'];
                                $fee_percentage=$fila['price_fee_percentage'];
                                $amount_received_by_teacher=$total_price*(100-$fee_percentage)/100;
                                
                                $por_internet_o_presencial=$fila['onlineonsite'];
                                $local_encuentro=$fila['id_local'];
                                
                                // sacamos huso horario
                                $query99="SELECT timeshift FROM mentor2009 WHERE orden='$id_student' ";
                                $result99=mysqli_query($link,$query99);
                                if(!mysqli_num_rows($result99))
                                        die("User unregistered 1.");
                                $fila99=mysqli_fetch_array($result99);

                                $time_shift_student=$fila99['timeshift'];
                                
                                //sacamos la foto
                                $extension = $fila['fotoext'];
                                $path_photo="../uploader/upload_pic/thumb_$id_student"."."."$extension";

                                if ( !file_exists($path_photo) ) :
                                    $path_photo="../uploader/default.jpg";
                                endif;
                                
                                //aquí sacamos las lenguas del alumno
                                //estudiante
                                list($my_langs_array_multidim["$id_student"], $my_langs_full_name_array_multidim["$id_student"], 
                                $my_langs_level_array_multidim["$id_student"], 
                                $my_langs_forshare_array_multidim["$id_student"],   
                                $my_langs_price_array_multidim["$id_student"], $my_langs_typeofexchange_array_multidim["$id_student"], 
                                $my_langs_priceorexchangetext_array_multidim["$id_student"], $my_langs_level_image_array_multidim["$id_student"], 
                                $my_langs_2letters_array_multidim["$id_student"])
                                = lenguas_que_conoce_usuario($id_student,$link);
                                
                                list($learn_langs_array_multidim["$id_student"], $learn_langs_full_name_array_multidim["$id_student"], 
                                $learn_langs_level_array_multidim["$id_student"], 
                                $learn_langs_forshare_array_multidim["$id_student"],   $learn_langs_price_array_multidim["$id_student"], 
                                $learn_langs_typeofexchange_array_multidim["$id_student"], 
                                $learn_langs_priceorexchangetext_array_multidim["$id_student"], $learn_langs_level_image_array_multidim["$id_student"], 
                                $learn_langs_2letters_array_multidim["$id_student"])
                                = lenguas_que_quiere_estudiar_usuario($id_student,$link);
                                

                                $idiomas_comunes = array_intersect($my_langs_array_multidim["$id_student"], $my_langs_array_multidim["$mi_identificador"]);
                                
                                //con esta línea lo que hacemos es borrar los valores vacíos y reorganizar el array
                                $idiomas_comunes = array_values(array_filter($idiomas_comunes));
                                
                                $nombre_idioma='';
                                // sacamos el nombre completo del idioma sin recurrir a hacer llamada a la bbdd
                                for($rr=0;$rr<count($idiomas_comunes);$rr++)
                                {
                                    $key_search=array_search($idiomas_comunes[$rr],$my_langs_array_multidim["$id_student"] );
                                    
                                    switch ($my_langs_level_array_multidim["$id_student"][$key_search]) 
                                    {
                                        case 0:
                                            $level_aux='Level unknown';
                                            break;
                                        case 1:
                                            $level_aux='Beginner';
                                            break;
                                        case 2:
                                            $level_aux='A1';
                                            break;
                                        case 3:
                                            $level_aux='A2';
                                            break;
                                        case 4:
                                            $level_aux='B1';
                                            break;
                                        case 5:
                                            $level_aux='B2';
                                            break;
                                        case 6:
                                            $level_aux='C1';
                                            break;
                                        case 7:
                                            $level_aux='C2';
                                            break;
                                    }
                                    
                                    $nombre_idioma.=" ".$my_langs_full_name_array_multidim["$id_student"][$key_search]." (".$level_aux.") "."&nbsp;&nbsp; ";
                                }
                                
                                $nombre_idioma=trim($nombre_idioma); 
                                
                                if(empty($nombre_idioma)){$nombre_idioma='No common languages';}
                                
                                //sacamos el nombre del idioma que quiere trabajar el estudiante
                                $key_search2=array_search($language_to_teach,$my_langs_array_multidim["$mi_identificador"] );
                                
                                $language_to_teach_fullname=$my_langs_full_name_array_multidim["$mi_identificador"]["$key_search2"];
                                
                                //sacamos nivel del alumno, no del profesor
                                $key_search3=array_search($language_to_teach,$learn_langs_array_multidim["$id_student"] );
                                $level_language_to_teach=$learn_langs_level_array_multidim["$id_student"]["$key_search3"]; 

                                switch ($level_language_to_teach) 
                                {
                                    case 0:
                                        $level_language_to_teach_2='Level unknown';
                                        break;
                                    case 1:
                                        $level_language_to_teach_2='Beginner';
                                        break;
                                    case 2:
                                        $level_language_to_teach_2='A1';
                                        break;
                                    case 3:
                                        $level_language_to_teach_2='A2';
                                        break;
                                    case 4:
                                        $level_language_to_teach_2='B1';
                                        break;
                                    case 5:
                                        $level_language_to_teach_2='B2';
                                        break;
                                    case 6:
                                        $level_language_to_teach_2='C1';
                                        break;
                                    case 7:
                                        $level_language_to_teach_2='C2';
                                        break;
                                }

                                //sacamos nombre del estudiante
                                $query77="SELECT nombre FROM mentor2009 WHERE orden='$id_student' ";
                                $result77=mysqli_query($link,$query77);
                                if(!mysqli_num_rows($result77))
                                        die("User unregistered 1.");
                                $fila77=mysqli_fetch_array($result77);

                                $student_name=$fila77['nombre'];
                                $palabras = explode (" ", $student_name);
                                $student_name=ucfirst($palabras[0]);
                                
                                //en caso de que haya quitado el estudiante el idioma de su lista de idiomas que quiere aprender
                                if(empty($language_to_teach_fullname))
                                    $cadena_idioma_nivel=$language_to_teach;
                                else
                                    $cadena_idioma_nivel="$language_to_teach_fullname ($level_language_to_teach_2)"; 
                                
                                //online o bien onsite
                                if($por_internet_o_presencial==1)
                                {
                                        $cadena_idioma_nivel.=" - Online";                          
                                }
                                else if($por_internet_o_presencial==2 AND is_numeric($local_encuentro) )
                                {
                                        $query212="SELECT * FROM locales WHERE id_local=$local_encuentro"; 
                                                
                                        $result212=mysqli_query($link,$query212);
                                        $fila212=mysqli_fetch_array($result212);
                                        $nombre_establecimiento=$fila212['name_local_google'];
                                        $direccion_establecimiento=$fila212['full_address_google'];
                                        $ciudad_establecimiento=$fila212['city_google'];
                                                
                                        $cadena_idioma_nivel.=" - Onsite in $ciudad_establecimiento: $nombre_establecimiento";
                                }
                                
                                // LOGIC EXTRACTION
                                $tiempoUnix_clase = $unixtimestart; 
                                $fechaHoraFormateada2 = obtenerFechaHora($tiempoUnix_clase, $zonaHoraria);
                                $fechaHoraFormateada2_utc0 = obtenerFechaHora($tiempoUnix_clase, 'UTC');
                                $fechaHoraFormateada2_student = obtenerFechaHora($tiempoUnix_clase, $time_shift_student);
                                
                                $direccion_completa = "";
                                if($por_internet_o_presencial==2 AND is_numeric($local_encuentro) ) {
                                    $direccion_completa="Address:<br>$nombre_establecimiento<br>$direccion_establecimiento<br>$ciudad_establecimiento";
                                }
                                
                                // Determine Badge Status and Color
                                $status_text = "SCHEDULED";
                                $status_class = "status-pending";
                                
                                if($session_paid==0) {
                                    $status_text = "WAITING DEPOSIT";
                                    $status_class = "status-waiting";
                                } elseif ($session_releasefunds==0 && $session_paid==1) {
                                    $status_text = "DEPOSIT PAID";
                                    $status_class = "status-confirmed";
                                } elseif ($session_releasefunds==1) {
                                    $status_text = "FUNDS RELEASED";
                                    $status_class = "status-confirmed";
                                }
                                
                                $location_label = ($por_internet_o_presencial == 2) ? "Onsite" : "Online";

                                // CARD HTML
                                echo "<div class=\"proposal-card\">";
                                    // LEFT
                                    echo "<div class=\"card-left\">";
                                        echo "<div class=\"avatar-circle\">";
                                            echo "<img src=\"$path_photo\" alt=\"Student\" style=\"width:100%; height:100%; border-radius:100%; object-fit:cover;\">";
                                            echo "<i class=\"fas fa-star star-badge\"></i>";
                                        echo "</div>";
                                        echo "<div class=\"status-badge $status_class\">$status_text</div>";
                                    echo "</div>";

                                    // MIDDLE
                                    echo "<div class=\"card-middle\">";
                                        echo "<div class=\"card-title\">$cadena_idioma_nivel</div>";
                                        
                                        echo "<div class=\"meta-row\">";
                                            echo "<div class=\"meta-item\"><i class=\"far fa-clock\"></i> $duration_min min</div>";
                                            echo "<div class=\"meta-item\"><i class=\"fas fa-dollar-sign\"></i> $total_price&euro;</div>";
                                            echo "<div class=\"meta-item\"><i class=\"fas fa-globe\"></i> $location_label</div>";
                                        echo "</div>";
                                        
                                        echo "<div class=\"languages-label\">LANGUAGES OFFERED</div>";
                                        echo "<div class=\"language-tags\">";
                                            $student_langs = $my_langs_full_name_array_multidim["$id_student"];
                                            if (is_array($student_langs)) {
                                                foreach($student_langs as $idx => $lang) {
                                                    if(!empty($lang)) {
                                                        echo "<div class=\"lang-tag\">$lang</div>";
                                                    }
                                                }
                                            }
                                        echo "</div>";
                                        
                                        // Detail hidden block
                                        echo "<div class=\"class-details\" id=\"details-class$id_of_class\">";
                                            echo "<h6><i class=\"fas fa-info-circle\"></i> Class Details</h6>";
                                            echo "<p><strong>Description:</strong> $descriptionofsession</p>";
                                            echo "<hr>";
                                            echo "<p><strong>Time & Location:</strong><br>";
                                            echo "Start Time (My timezone $my_timeshift): $fechaHoraFormateada2<br>";
                                            echo "Start Time (Student timezone $time_shift_student): $fechaHoraFormateada2_student<br>";
                                            echo "Duration: $duration_min min<br>";
                                            if($direccion_completa) echo "<br>$direccion_completa<br>";
                                            echo "</p>";
                                            echo "<p><strong>Financials:</strong><br>";
                                            echo "Total Price: $total_price&euro; ($hourly_price&euro;/h)<br>";
                                            echo "Net Amount: $amount_received_by_teacher&euro;<br>";
                                            echo "Status: $status_text<br>";
                                            echo "</p>";
                                        echo "</div>";

                                    echo "</div>"; // End Middle

                                    // RIGHT - CON BORDE CORREGIDO
                                    echo "<div class=\"card-right\">";
                                        echo "<div class=\"date-info\"><i class=\"far fa-clock\"></i> $fechaHoraFormateada2</div>";
                                        echo "<div class=\"action-buttons\">";
                                            echo "<button class=\"btn class-name\" data-id=\"$id_of_class\"><i class=\"fas fa-eye\"></i> View Details</button>";
                                        echo "</div>";
                                    echo "</div>";

                                echo "</div>"; // End Card
                            }
                        ?>
                    </div>
                </div>
            </div>
        </section>
        <?php
            require('../templates/footer.php');
        ?>
    </div>

    <script type="text/javascript" src="../public/js/jquery.min.js"></script>
    <script type="text/javascript" src="../public/js/bootstrap.min.js"></script>
    <script type="text/javascript">
        document.addEventListener('DOMContentLoaded', function () {
            var classNames = document.querySelectorAll('.class-name');
            classNames.forEach(function (className) {
                className.addEventListener('click', function () {
                    var classId = className.getAttribute('data-id');
                    var classDetails = document.getElementById('details-class' + classId);
                    if (classDetails.style.display === 'none' || classDetails.style.display === '') {
                        classDetails.style.display = 'block';
                    } else {
                        classDetails.style.display = 'none';
                    }
                });
            });
        });
    </script>
</body>
</html>