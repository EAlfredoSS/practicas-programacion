<?php 
session_start();
//$mi_identificador=$_SESSION['orden2017'];  // en require de list of queries

require('../templates/header_simplified.html');
require('../files/bd.php');
require('../funcionesphp/funciones_idiomas_usuario.php');
require('./listofqueries.php');

// Verificar que la función obtenerFechaHora existe
if (!function_exists('obtenerFechaHora')) {
    function obtenerFechaHora($timestamp, $timezone) {
        $dt = new DateTime("@$timestamp");
        $dt->setTimezone(new DateTimeZone($timezone));
        return $dt->format('d/m/Y H:i');
    }
}

$query77="SELECT timeshift FROM mentor2009 WHERE orden='$mi_identificador'";
$result77=mysqli_query($link,$query77);
if(!mysqli_num_rows($result77)) die("User unregistered 1.");
$fila77=mysqli_fetch_array($result77);
$my_timeshift=$fila77['timeshift'];
$zonaHoraria = $my_timeshift;
//$tiempo_corte=time();   //en el require de listofqueries

/*
$query = "
    SELECT t.*, m.*
    FROM tracker t
    INNER JOIN mentor2009 m ON t.id_user_student=m.orden
    WHERE t.id_user_student ='".$mi_identificador."'  
      AND proposal_accepted_teacher=2 
      AND cancelled=0 
      AND paid=1  
      AND end_time_unix>$tiempo_corte
    ORDER BY t.start_time_unix ASC";
*/
	
$query = $QUERY_STUDENT_FUTURE_LESSONS;
$result = mysqli_query($link, $query);
$n_next_lessons = mysqli_num_rows($result);

/*
$query109="SELECT * FROM tracker WHERE id_user_student ='".$mi_identificador."' AND proposal_accepted_teacher=2 AND cancelled=0 AND paid=0 AND $tiempo_corte<=start_time_unix ORDER BY start_time_unix ASC";
$result109 = mysqli_query($link, $query109);
$n_payments_pending = mysqli_num_rows($result109);

$query109 = "SELECT * FROM tracker t WHERE t.id_user_student ='".$mi_identificador."' AND t.proposal_accepted_teacher=2 AND t.cancelled=0 AND t.paid=1 AND releasefunds=0 AND $tiempo_corte>t.end_time_unix";
$result109 = mysqli_query($link, $query109);
$n_release_payment_pending = mysqli_num_rows($result109);
*/
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Next classes as student</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-orange: #d35400;
            --accent-orange: #e67e22;
            --light-orange: #fdf2e9;
            --confirmed-green: #27ae60;
            --confirmed-bg: #eafaf1;
            --waiting-grey: #95a5a6;
            --waiting-bg: #f2f3f4;
            --text-dark: #2c3e50;
            --text-grey: #7f8c8d;
            --bg-grey: #f4f7f6;
        }

        body {
            font-family: 'Roboto', sans-serif;
            background-color: var(--bg-grey);
            margin: 0; padding: 0;
        }

        .btn-dashboard {
            border: 2px solid #d35400 !important;
            background: #d35400 !important;
            padding: 10px 20px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
            color: #fdf2e9 !important;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            box-shadow: 0 2px 5px rgba(230,126,34,0.15);
            text-decoration: none;
        }
        .btn-dashboard:hover {
            background: #fdf2e9 !important;
            color: #d35400 !important;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(230,126,34,0.3);
        }

        .btn {
            border: 2px solid #d35400 !important;
            background: #fdf2e9;
            padding: 10px 20px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
            color: #d35400;
            text-transform: uppercase;
            text-decoration: none;
        }
        .btn:hover {
            background: #d35400 !important;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(230,126,34,0.3);
        }

        /* ── TARJETA ── */
        .proposal-card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.08);
            margin-bottom: 20px;
            display: flex;
            flex-wrap: wrap;
            padding: 25px;
            border-left: 5px solid var(--primary-orange);
            border-bottom: 2px solid var(--primary-orange);
            transition: transform 0.2s, box-shadow 0.2s;
            position: relative; 
        }
        .proposal-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.12);
        }

        /* Etiqueta distintiva "STUDENT VIEW" */
        .view-tag {
            position: absolute;
            top: 10px;
            right: 15px;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #d35400;
            background: #fdf2e9;
            padding: 4px 10px;
            border-radius: 12px;
            border: 1px solid #d35400;
            opacity: 0.8;
        }

        .card-left {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-right: 25px;
            min-width: 110px;
        }

        .avatar-circle {
            width: 110px;
            height: 110px;
            border-radius: 100%;
            border: 3.5px solid var(--text-grey);
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: visible;
            box-shadow: 0 2px 8px rgba(211, 84, 0, 0.2);
        }
        .avatar-circle img { width: 100%; height: 100%; border-radius: 50%; object-fit: cover; }

        /* cambio de icono: birrete en vez de estrella */
        .student-badge {
            position: absolute;
            bottom: 0; right: -5px;
            color: #f1c40f;
            font-size: 16px;
            background: white;
            border-radius: 50%;
            padding: 3px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .teacher-name {
            margin-top: 10px;
            font-size: 16px; font-weight: 600;
            color: var(--text-dark);
            text-align: center;
            word-break: break-word;
            max-width: 110px;
        }

        .status-badge {
            margin-top: 8px;
            font-size: 10px; font-weight: bold;
            padding: 5px 10px;
            border-radius: 12px;
            text-transform: uppercase;
            text-align: center;
            min-width: 85px;
        }
        .status-paid {
            background-color: var(--confirmed-bg);
            color: var(--confirmed-green);
            border: 1px solid #27ae60;
        }

        .card-right-content { flex: 1; min-width: 280px; }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 10px;
            flex-wrap: wrap; gap: 10px;
        }
        .card-title { font-size: 21px; font-weight: bold; color: var(--text-dark); line-height: 1.3; }

        .date-info { font-size: 14px; color: var(--text-grey); font-weight: 500; white-space: nowrap; }
        .date-info i { margin-right: 6px; color: var(--accent-orange); }

        .meta-row { display: flex; gap: 30px; font-size: 15px; color: var(--text-dark); margin-bottom: 18px; flex-wrap: wrap; }
        .meta-item { display: flex; align-items: center; gap: 6px; font-weight: 500; }
        .meta-item i { color: var(--accent-orange); width: 18px; text-align: center; font-size: 16px; }

        .languages-label {
            font-size: 11px; color: #999; text-transform: uppercase;
            margin-bottom: 8px; margin-top: 5px;
            font-weight: 600; letter-spacing: 0.5px;
        }
        .language-tags { display: flex; gap: 6px; flex-wrap: wrap; margin-bottom: 20px; }
        .lang-tag {
            background-color: #f2f4f5; color: #7a7d81;
            padding: 5px 10px; border-radius: 5px;
            font-size: 12px; font-weight: 600;
            border: 1px solid #dcdde1;
            box-shadow: 0 1px 2px rgba(0,0,0,0.05);
        }

        .action-buttons {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-bottom: 10px;
        }

        .class-details {
            background: linear-gradient(135deg,#fafafa 0%,#f5f5f5 100%);
            padding: 20px; border-radius: 8px;
            border: 1px solid #e0e0e0;
            margin-top: 15px; display: none;
            box-shadow: inset 0 1px 3px rgba(0,0,0,0.05);
            width: 100%;
        }
        .class-details h6 { color: var(--primary-orange); font-size: 16px; font-weight: bold; margin-bottom: 15px; text-transform: uppercase; letter-spacing: 0.5px; }
        .class-details p  { margin-bottom: 10px; line-height: 1.6; color: var(--text-dark); }
        .class-details hr { border: none; border-top: 2px solid #e0e0e0; margin: 15px 0; }
        .class-details strong { color: var(--primary-orange); font-weight: 600; }

        .empty-state {
            background: white; border-radius: 12px;
            box-shadow: 0 3px 15px rgba(0,0,0,0.08);
            padding: 60px 40px; text-align: center;
            margin: 40px auto; max-width: 600px;
            border-top: 4px solid var(--primary-orange);
        }
        .empty-state-icon {
            width: 80px; height: 80px;
            background: linear-gradient(135deg,#fdf2e9 0%,#fce8d6 100%);
            border-radius: 50%; display: flex;
            align-items: center; justify-content: center;
            margin: 0 auto 25px;
            box-shadow: 0 4px 12px rgba(230,126,34,0.2);
        }
        .empty-state-icon i { font-size: 36px; color: var(--primary-orange); }
        .empty-state h3 { color: var(--text-dark); font-size: 24px; font-weight: 600; margin-bottom: 12px; }

        @media (max-width: 768px) {
            .proposal-card { flex-direction: column; padding: 20px; }
            .card-left { flex-direction: row; gap: 15px; align-items: center; margin-bottom: 15px; width: 100%; }
            .teacher-name { margin-top: 0; text-align: left; max-width: none; }
            .status-badge { margin-top: 0; }
            .card-header { flex-direction: column; align-items: flex-start; }
            .date-info { white-space: normal; }
            .action-buttons { justify-content: center; }
            .view-tag { position: static; margin-bottom: 10px; display: inline-block; }
        }
    </style>
</head>
<body>
<div class="wrapper">
    <section class="forum-sec">
        <div class="container">
            <!-- Botón único de retorno al dashboard (alineado a la derecha) -->
            <div style="text-align: right; padding: 10px 0;">
                <a href="../trackerproposals/dashboard.php" class="btn-dashboard">
                    Return to Dashboard
                </a>
            </div>
        </div>
    </section>

    <section class="forum-page">
        <div class="container">
            <div class="forum-questions-sec" style="width:100%">
                <div class="forum-questions">
                <?php
                if (!$n_next_lessons) {
                    echo '<div class="empty-state">
                            <div class="empty-state-icon"><i class="fas fa-calendar"></i></div>
                            <h3>No lessons available in this section at the moment</h3>
                          </div>';
                    require('../templates/footer.php');
                    echo '</div></div></section></div></body></html>';
                    exit;
                }

                // Arrays para idiomas
                $my_langs_array_multidim = array(array());
                $my_langs_full_name_array_multidim = array(array());
                $my_langs_level_array_multidim = array(array());
                $my_langs_forshare_array_multidim = array(array());
                $my_langs_price_array_multidim = array(array());
                $my_langs_typeofexchange_array_multidim = array(array());
                $my_langs_priceorexchangetext_array_multidim = array(array());
                $my_langs_level_image_array_multidim = array(array());
                $my_langs_2letters_array_multidim = array(array());

                $learn_langs_array_multidim = array(array());
                $learn_langs_full_name_array_multidim = array(array());
                $learn_langs_level_array_multidim = array(array());
                $learn_langs_forshare_array_multidim = array(array());
                $learn_langs_price_array_multidim = array(array());
                $learn_langs_typeofexchange_array_multidim = array(array());
                $learn_langs_priceorexchangetext_array_multidim = array(array());
                $learn_langs_level_image_array_multidim = array(array());
                $learn_langs_2letters_array_multidim = array(array());

                // Idiomas del estudiante
                list($my_langs_array_multidim["$mi_identificador"], $my_langs_full_name_array_multidim["$mi_identificador"],
                     $my_langs_level_array_multidim["$mi_identificador"], $my_langs_forshare_array_multidim["$mi_identificador"],
                     $my_langs_price_array_multidim["$mi_identificador"], $my_langs_typeofexchange_array_multidim["$mi_identificador"],
                     $my_langs_priceorexchangetext_array_multidim["$mi_identificador"], $my_langs_level_image_array_multidim["$mi_identificador"],
                     $my_langs_2letters_array_multidim["$mi_identificador"])
                = lenguas_que_conoce_usuario($mi_identificador,$link);

                list($learn_langs_array_multidim["$mi_identificador"], $learn_langs_full_name_array_multidim["$mi_identificador"],
                     $learn_langs_level_array_multidim["$mi_identificador"], $learn_langs_forshare_array_multidim["$mi_identificador"],
                     $learn_langs_price_array_multidim["$mi_identificador"], $learn_langs_typeofexchange_array_multidim["$mi_identificador"],
                     $learn_langs_priceorexchangetext_array_multidim["$mi_identificador"], $learn_langs_level_image_array_multidim["$mi_identificador"],
                     $learn_langs_2letters_array_multidim["$mi_identificador"])
                = lenguas_que_quiere_estudiar_usuario($mi_identificador,$link);

                // Re-ejecutar la consulta para el bucle
                $result = mysqli_query($link, $query);
                while ($fila = mysqli_fetch_array($result)) {
                    $id_of_class = $fila['id_tracking'];
                    $id_teacher = $fila['id_user_teacher'];
                    $duration_min = $fila['session_lenght_minutes'];
                    $language_to_teach = $fila['language_taught'];
                    $hourly_price = $fila['hourly_rate_original'];
                    $total_price = round($fila['price_session_total'],2);
                    $descriptionofsession = $fila['description_session'];
                    $session_paid = $fila['paid'];
                    $session_releasefunds = $fila['releasefunds'];
                    $por_internet_o_presencial = $fila['onlineonsite'];
                    $local_encuentro = $fila['id_local'];
                    $unixtimestart = $fila['start_time_unix'];
                    $recurrent = $fila['created_from_recurrent'] ? 'Yes' : 'No';

                    // Datos del profesor
                    $query77="SELECT * FROM mentor2009 WHERE orden='$id_teacher'";
                    $result77=mysqli_query($link,$query77);
                    $fila77=mysqli_fetch_array($result77);
                    $teacher_name = ucfirst(explode(" ",trim($fila77['nombre']))[0]);
                    $extension = $fila77['fotoext'];
                    $path_photo = "../uploader/upload_pic/thumb_$id_teacher.$extension";
                    if (!file_exists($path_photo)) $path_photo = "../uploader/default.jpg";
                    $time_shift_teacher = $fila77['timeshift'];

                    // Idiomas del profesor
                    list($my_langs_array_multidim["$id_teacher"], $my_langs_full_name_array_multidim["$id_teacher"],
                         $my_langs_level_array_multidim["$id_teacher"], $my_langs_forshare_array_multidim["$id_teacher"],
                         $my_langs_price_array_multidim["$id_teacher"], $my_langs_typeofexchange_array_multidim["$id_teacher"],
                         $my_langs_priceorexchangetext_array_multidim["$id_teacher"], $my_langs_level_image_array_multidim["$id_teacher"],
                         $my_langs_2letters_array_multidim["$id_teacher"])
                    = lenguas_que_conoce_usuario($id_teacher,$link);

                    list($learn_langs_array_multidim["$id_teacher"], $learn_langs_full_name_array_multidim["$id_teacher"],
                         $learn_langs_level_array_multidim["$id_teacher"], $learn_langs_forshare_array_multidim["$id_teacher"],
                         $learn_langs_price_array_multidim["$id_teacher"], $learn_langs_typeofexchange_array_multidim["$id_teacher"],
                         $learn_langs_priceorexchangetext_array_multidim["$id_teacher"], $learn_langs_level_image_array_multidim["$id_teacher"],
                         $learn_langs_2letters_array_multidim["$id_teacher"])
                    = lenguas_que_quiere_estudiar_usuario($id_teacher,$link);

                    // Idiomas comunes (estudiante-profesor)
                    $idiomas_comunes = array_intersect($my_langs_array_multidim["$id_teacher"], $my_langs_array_multidim["$mi_identificador"]);
                    $idiomas_comunes = array_values(array_filter($idiomas_comunes));
                    $common_langs_badges = [];
                    for ($rr=0; $rr<count($idiomas_comunes); $rr++) {
                        $key_search = array_search($idiomas_comunes[$rr], $my_langs_array_multidim["$id_teacher"]);
                        if ($key_search !== false) {
                            $lang_name = $my_langs_full_name_array_multidim["$id_teacher"][$key_search];
                            $level_code = $my_langs_level_array_multidim["$id_teacher"][$key_search];
                            switch ($level_code) {
                                case 1: $level_aux='Beginner'; break;
                                case 2: $level_aux='A1'; break;
                                case 3: $level_aux='A2'; break;
                                case 4: $level_aux='B1'; break;
                                case 5: $level_aux='B2'; break;
                                case 6: $level_aux='C1'; break;
                                case 7: $level_aux='C2'; break;
                                default: $level_aux='Level unknown';
                            }
                            $common_langs_badges[] = $lang_name." (".$level_aux.")";
                        }
                    }

                    // Idioma que se enseña
                    $key_search2 = array_search($language_to_teach, $my_langs_array_multidim["$id_teacher"]);
                    $language_to_teach_fullname = $my_langs_full_name_array_multidim["$id_teacher"][$key_search2] ?? $language_to_teach;
                    $level_language_to_teach = $my_langs_level_array_multidim["$id_teacher"][$key_search2] ?? 0;
                    switch ($level_language_to_teach) {
                        case 1: $level_text='Beginner'; break;
                        case 2: $level_text='A1'; break;
                        case 3: $level_text='A2'; break;
                        case 4: $level_text='B1'; break;
                        case 5: $level_text='B2'; break;
                        case 6: $level_text='C1'; break;
                        case 7: $level_text='C2'; break;
                        default: $level_text='Level unknown';
                    }

                    $cadena_idioma_nivel = empty($language_to_teach_fullname)
                        ? "$language_to_teach ($level_text)"
                        : "$language_to_teach_fullname ($level_text)";

                    if ($por_internet_o_presencial==1) {
                        $cadena_idioma_nivel .= " - Online";
                        $location_label = "Online";
                    } elseif ($por_internet_o_presencial==2 && is_numeric($local_encuentro)) {
                        $query212="SELECT * FROM locales WHERE id_local=".intval($local_encuentro);
                        $result212=mysqli_query($link,$query212);
                        $fila212=mysqli_fetch_array($result212);
                        $ciudad_establecimiento=$fila212['city_google'];
                        $nombre_establecimiento=$fila212['name_local_google'];
                        $direccion_establecimiento=$fila212['full_address_google'];
                        $cadena_idioma_nivel .= " - Onsite in $ciudad_establecimiento: $nombre_establecimiento";
                        $location_label = "Onsite";
                    } else {
                        $location_label = "Online";
                    }

                    $fechaHoraFormateada2        = obtenerFechaHora($unixtimestart, $zonaHoraria);
                    $fechaHoraFormateada2_teacher = obtenerFechaHora($unixtimestart, $time_shift_teacher);
                    $fechaHoraFormateada2_utc0    = obtenerFechaHora($unixtimestart, 'UTC');

                    $direccion_completa = "";
                    if ($por_internet_o_presencial==2 && isset($fila212)) {
                        $direccion_completa = "Address:<br>$nombre_establecimiento<br>$direccion_establecimiento<br>$ciudad_establecimiento";
                    }

                    // Estado del pago
                    $status_text = "PAID";
                    $status_class = "status-paid";
                    ?>

                    <div class="proposal-card">
                        <!-- distintivo estudiante -->
                        <div class="card-left">
                            <div class="avatar-circle">
                                <img src="<?php echo $path_photo; ?>" alt="<?php echo $teacher_name; ?>">
                                <i class="fas fa-book-open student-badge"></i>
                            </div>
                            <div class="teacher-name"><?php echo $teacher_name; ?></div>
                            <div class="status-badge <?php echo $status_class; ?>"><?php echo $status_text; ?></div>
                        </div>

                        <div class="card-right-content">
                            <div class="card-header">
                                <div class="card-title"><?php echo $cadena_idioma_nivel; ?></div>
                                <div class="date-info"><i class="far fa-calendar-alt"></i> <?php echo $fechaHoraFormateada2; ?></div>
                            </div>

                            <div class="meta-row">
                                <div class="meta-item"><i class="far fa-clock"></i> <?php echo $duration_min; ?> min</div>
                                <div class="meta-item"><i class="fas fa-euro-sign"></i> <?php echo $total_price; ?>&euro;</div>
                                <div class="meta-item"><i class="fas fa-globe"></i> <?php echo $location_label; ?></div>
                            </div>

                            <div class="languages-label">COMMON LANGUAGES</div>
                            <div class="language-tags">
                                <?php
                                if (!empty($common_langs_badges)) {
                                    foreach ($common_langs_badges as $badge) {
                                        echo "<div class=\"lang-tag\">$badge</div>";
                                    }
                                } else {
                                    echo "<div class=\"lang-tag\">No common languages</div>";
                                }
                                ?>
                            </div>

                            <div class="action-buttons">
                                <button class="btn class-name" data-id="<?php echo $id_of_class; ?>">
                                    <i class="fas fa-eye"></i> View Details
                                </button>
                            </div>

                            <div class="class-details" id="details-class<?php echo $id_of_class; ?>">
                                <h6><i class="fas fa-info-circle"></i> Class Details</h6>
                                <p><strong>Description:</strong> <?php echo htmlspecialchars($descriptionofsession); ?></p>
                                <hr>
                                <p><strong>Time & Location:</strong><br>
                                    Start time (my timezone <?php echo $my_timeshift; ?>): <?php echo $fechaHoraFormateada2; ?><br>
                                    Start time (teacher timezone <?php echo $time_shift_teacher; ?>): <?php echo $fechaHoraFormateada2_teacher; ?><br>
                                    Start time (UTC/Greenwich): <?php echo $fechaHoraFormateada2_utc0; ?><br>
                                    Duration: <?php echo $duration_min; ?> min<br>
                                    <?php if ($direccion_completa) echo "<br>$direccion_completa<br>"; ?>
                                </p>
                                <p><strong>Financials:</strong><br>
                                    Total Price: <?php echo $total_price; ?>&euro; (<?php echo $hourly_price; ?>&euro;/h)<br>
                                    Status: <?php echo $status_text; ?>
                                </p>
                                <p><strong>Other:</strong><br>
                                    Lesson ID: #<?php echo $id_of_class; ?><br>
                                    Teacher ID: #<?php echo $id_teacher; ?><br>
                                    Language code: <?php echo $language_to_teach; ?><br>
                                    Created from serie: <?php echo $recurrent; ?>
                                </p>
                            </div>
                        </div>
                    </div>

                    <?php } ?>
                </div>
            </div>
        </div>
    </section>
    <?php require('../templates/footer.php'); ?>
</div>
<script src="../public/js/jquery.min.js"></script>
<script src="../public/js/bootstrap.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.class-name').forEach(function(btn) {
            btn.addEventListener('click', function() {
                var id = this.getAttribute('data-id');
                var d  = document.getElementById('details-class' + id);
                d.style.display = (d.style.display === 'none' || d.style.display === '') ? 'block' : 'none';
            });
        });
    });
</script>
</body>
</html>