<?php
session_start();
//$mi_identificador = $_SESSION['orden2017'];  // lo saco de require listofqueries

require('../templates/header_simplified.html');
require('../files/bd.php');
require('../funcionesphp/funciones_idiomas_usuario.php');
require('./listofqueries.php');

// Verificar que la función obtenerFechaHora existe (por si acaso)
if (!function_exists('obtenerFechaHora')) {
    function obtenerFechaHora($timestamp, $timezone)
    {
        $dt = new DateTime("@$timestamp");
        $dt->setTimezone(new DateTimeZone($timezone));
        return $dt->format('d/m/Y H:i');
    }
}

// Obtener time shift del profesor
$query77 = "SELECT timeshift FROM mentor2009 WHERE orden = " . intval($mi_identificador);
$result77 = mysqli_query($link, $query77);
if (!mysqli_num_rows($result77)) {
    die("User unregistered 1.");
}
$fila77 = mysqli_fetch_array($result77);
$my_timeshift = $fila77['timeshift'];
$zonaHoraria = $my_timeshift;

// Consulta principal: próximas clases como profesor (propuestas aceptadas, no canceladas, fecha futura)
$query = $QUERY_TEACHER_FUTURE_LESSONS;
$result = mysqli_query($link, $query);
$n_next_lessons = mysqli_num_rows($result); 
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
            --icon-grey: #999;
            --row-bg: #fafafa;
        }

        body {
            font-family: 'Roboto', sans-serif;
            background-color: var(--bg-grey);
            margin: 0;
            padding: 0;
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
            box-shadow: 0 2px 5px rgba(230, 126, 34, 0.15);
            text-decoration: none;
        }

        .btn-dashboard:hover {
            background: #fdf2e9 !important;
            color: #d35400 !important;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(230, 126, 34, 0.3);
        }

        .btn-dashboard i {
            transition: transform 0.3s ease;
        }

        .btn-dashboard:hover i {
            transform: scale(1.1);
        }

        .proposal-card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.08);
            margin-bottom: 20px;
            display: flex;
            flex-wrap: wrap;
            padding: 25px;
            border-left: 5px solid var(--primary-orange);
            border-top: 2px solid var(--primary-orange);
            transition: transform 0.2s, box-shadow 0.2s;
            position: relative; 
        }

        .proposal-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.12);
        }

        /* Columna izquierda: foto + nombre + estado */
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
            border: 3.5px solid;
            border-color: #cdcdcd;  /* ← mismo color que los iconos */
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: visible;
            box-shadow: 0 2px 8px rgba(211, 84, 0, 0.2);
            background-color: var(--row-bg);
        }

        .avatar-circle img {
            width: 100%;
            height: 100%;
            border-radius: 100%;
            object-fit: cover;
        }

        .star-badge {
            position: absolute;
            bottom: 0;
            right: -5px;
            color: var(--icon-grey);   /* ← gris en vez de dorado */
            font-size: 16px;
            background: white;
            border-radius: 50%;
            padding: 3px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .student-name {
            margin-top: 10px;
            font-size: 16px;
            font-weight: 600;
            color: var(--text-dark);
            text-align: center;
            word-break: break-word;
            max-width: 110px;
        }

        .status-badge {
            margin-top: 8px;
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

        /* Columna derecha: todo el contenido */
        .card-right-content {
            flex: 1;
            min-width: 280px;
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 10px;
            flex-wrap: wrap;
            gap: 10px;
        }

        .card-title {
            font-size: 21px;
            font-weight: bold;
            color: var(--text-dark);
            line-height: 1.3;
        }

        .date-info {
            font-size: 14px;
            color: var(--text-grey);
            font-weight: 500;
            white-space: nowrap;
        }

        .date-info i {
            margin-right: 6px;
            color: var(--icon-grey);   /* ← gris */
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
            color: var(--icon-grey);   /* ← gris para todos los iconos de la fila */
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
            margin-bottom: 20px;
        }

        .lang-tag {
            background-color: var(--row-bg);   /* ← gris muy claro */
            color: #7a7d81;
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 12px;
            font-weight: 600;
            border: 1px solid #dcdde1;
            box-shadow: 0 1px 2px rgba(0,0,0,0.05);
        }

        .action-buttons {
            display: flex;
            gap: 10px;
            margin-bottom: 10px;
            justify-content: flex-end;
        }

        /* ── BOTÓN VIEW DETAILS NARANJA (igual que en el otro archivo) ── */
        .btn {
            border: 2px solid #d35400 !important;
            background: #d35400;
            padding: 10px 20px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
            color: white;
            text-transform: uppercase;
            text-decoration: none;
            border: none;
        }

        .btn:hover {
            background: #fdf2e9;
            color: #d35400;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(230, 126, 34, 0.3);
        }

        .btn i {
            transition: transform 0.3s ease;
        }

        .btn:hover i {
            transform: scale(1.1);
        }

        .class-details {
            background: linear-gradient(135deg, var(--row-bg) 0%, #f5f5f5 100%);
            padding: 20px;
            border-radius: 8px;
            border: 1px solid #e0e0e0;
            margin-top: 15px;
            display: none;
            box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.05);
            width: 100%;
        }

        .class-details h6 {
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

        .empty-state {
            background: white;
            border-radius: 12px;
            box-shadow: 0 3px 15px rgba(0, 0, 0, 0.08);
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

        @media (max-width: 768px) {
            .proposal-card {
                flex-direction: column;
                padding: 20px;
            }

            .card-left {
                flex-direction: row;
                gap: 15px;
                align-items: center;
                margin-bottom: 15px;
                width: 100%;
            }

            .student-name {
                margin-top: 0;
                text-align: left;
                max-width: none;
            }

            .status-badge {
                margin-top: 0;
            }

            .card-header {
                flex-direction: column;
                align-items: flex-start;
            }

            .date-info {
                white-space: normal;
            }
            
            .action-buttons {
                justify-content: center;
            }
        }
    </style>
</head>

<body>
    <div class="wrapper">
        <section class="forum-sec">
            <div class="container">
                <div style="text-align: right; padding: 10px 0;">
                    <a href="../trackerproposals/dashboard.php" class="btn-dashboard">Return to Dashboard</a>
                </div>
            </div>
        </section>

        <section class="forum-page">
            <div class="container">
                <div class="forum-questions-sec" style="width: 100%">
                    <div class="forum-questions">
                        <?php
                        if (!$n_next_lessons) {
                            echo '
                            <div class="empty-state">
                                <div class="empty-state-icon">
                                    <i class="fas fa-calendar"></i>
                                </div>
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

                        // Idiomas del profesor
                        list(
                            $my_langs_array_multidim["$mi_identificador"],
                            $my_langs_full_name_array_multidim["$mi_identificador"],
                            $my_langs_level_array_multidim["$mi_identificador"],
                            $my_langs_forshare_array_multidim["$mi_identificador"],
                            $my_langs_price_array_multidim["$mi_identificador"],
                            $my_langs_typeofexchange_array_multidim["$mi_identificador"],
                            $my_langs_priceorexchangetext_array_multidim["$mi_identificador"],
                            $my_langs_level_image_array_multidim["$mi_identificador"],
                            $my_langs_2letters_array_multidim["$mi_identificador"]
                        ) = lenguas_que_conoce_usuario($mi_identificador, $link);

                        list(
                            $learn_langs_array_multidim["$mi_identificador"],
                            $learn_langs_full_name_array_multidim["$mi_identificador"],
                            $learn_langs_level_array_multidim["$mi_identificador"],
                            $learn_langs_forshare_array_multidim["$mi_identificador"],
                            $learn_langs_price_array_multidim["$mi_identificador"],
                            $learn_langs_typeofexchange_array_multidim["$mi_identificador"],
                            $learn_langs_priceorexchangetext_array_multidim["$mi_identificador"],
                            $learn_langs_level_image_array_multidim["$mi_identificador"],
                            $learn_langs_2letters_array_multidim["$mi_identificador"]
                        ) = lenguas_que_quiere_estudiar_usuario($mi_identificador, $link);

                        while ($fila = mysqli_fetch_array($result)) {
                            $id_of_class = $fila['id_tracking'];
                            $id_student = $fila['id_user_student'];
                            $duration_min = $fila['session_lenght_minutes'];
                            $language_to_teach = $fila['language_taught'];
                            $hourly_price = $fila['hourly_rate_original'];
                            $total_price = round($fila['price_session_total'], 2);
                            $descriptionofsession = $fila['description_session'];
                            $session_paid = $fila['paid'];
                            $session_releasefunds = $fila['releasefunds'];
                            $fee_percentage = $fila['price_fee_percentage'];
                            $amount_received_by_teacher = $total_price * (100 - $fee_percentage) / 100;
                            $por_internet_o_presencial = $fila['onlineonsite'];
                            $local_encuentro = $fila['id_local'];
                            $unixtimestart = $fila['start_time_unix'];
                            $recurrent = $fila['created_from_recurrent'] ? 'Yes' : 'No';

                            // Huso horario del estudiante
                            $query99 = "SELECT timeshift FROM mentor2009 WHERE orden = " . intval($id_student);
                            $result99 = mysqli_query($link, $query99);
                            $fila99 = mysqli_fetch_array($result99);
                            $time_shift_student = $fila99['timeshift'];

                            // Foto del estudiante
                            $extension = $fila['fotoext'];
                            $path_photo = "../uploader/upload_pic/thumb_$id_student.$extension";
                            if (!file_exists($path_photo)) {
                                $path_photo = "../uploader/default.jpg";
                            }

                            // Idiomas del estudiante
                            list(
                                $my_langs_array_multidim["$id_student"],
                                $my_langs_full_name_array_multidim["$id_student"],
                                $my_langs_level_array_multidim["$id_student"],
                                $my_langs_forshare_array_multidim["$id_student"],
                                $my_langs_price_array_multidim["$id_student"],
                                $my_langs_typeofexchange_array_multidim["$id_student"],
                                $my_langs_priceorexchangetext_array_multidim["$id_student"],
                                $my_langs_level_image_array_multidim["$id_student"],
                                $my_langs_2letters_array_multidim["$id_student"]
                            ) = lenguas_que_conoce_usuario($id_student, $link);

                            list(
                                $learn_langs_array_multidim["$id_student"],
                                $learn_langs_full_name_array_multidim["$id_student"],
                                $learn_langs_level_array_multidim["$id_student"],
                                $learn_langs_forshare_array_multidim["$id_student"],
                                $learn_langs_price_array_multidim["$id_student"],
                                $learn_langs_typeofexchange_array_multidim["$id_student"],
                                $learn_langs_priceorexchangetext_array_multidim["$id_student"],
                                $learn_langs_level_image_array_multidim["$id_student"],
                                $learn_langs_2letters_array_multidim["$id_student"]
                            ) = lenguas_que_quiere_estudiar_usuario($id_student, $link);

                            // *** CÁLCULO DE IDIOMAS COMUNES (SOLO ESTOS SE MUESTRAN) ***
                            $idiomas_comunes = array_intersect($my_langs_array_multidim["$id_student"], $my_langs_array_multidim["$mi_identificador"]);
                            $idiomas_comunes = array_values(array_filter($idiomas_comunes));

                            $nombre_idioma = '';
                            $common_langs_badges = [];
                            for ($rr = 0; $rr < count($idiomas_comunes); $rr++) {
                                $key_search = array_search($idiomas_comunes[$rr], $my_langs_array_multidim["$id_student"]);
                                if ($key_search !== false) {
                                    $lang_name = $my_langs_full_name_array_multidim["$id_student"][$key_search];
                                    $level_code = $my_langs_level_array_multidim["$id_student"][$key_search];
                                    switch ($level_code) {
                                        case 0: $level_aux = 'Level unknown'; break;
                                        case 1: $level_aux = 'Beginner'; break;
                                        case 2: $level_aux = 'A1'; break;
                                        case 3: $level_aux = 'A2'; break;
                                        case 4: $level_aux = 'B1'; break;
                                        case 5: $level_aux = 'B2'; break;
                                        case 6: $level_aux = 'C1'; break;
                                        case 7: $level_aux = 'C2'; break;
                                        default: $level_aux = '';
                                    }
                                    $nombre_idioma .= " " . $lang_name . " (" . $level_aux . ") ";
                                    $common_langs_badges[] = $lang_name . " (" . $level_aux . ")";
                                }
                            }
                            $nombre_idioma = trim($nombre_idioma);
                            if (empty($nombre_idioma)) {
                                $nombre_idioma = 'No common languages';
                            }

                            // Idioma que se va a enseñar
                            $key_search2 = array_search($language_to_teach, $my_langs_array_multidim["$mi_identificador"]);
                            $language_to_teach_fullname = $my_langs_full_name_array_multidim["$mi_identificador"][$key_search2] ?? $language_to_teach;

                            $key_search3 = array_search($language_to_teach, $learn_langs_array_multidim["$id_student"]);
                            $level_language_to_teach = $learn_langs_level_array_multidim["$id_student"][$key_search3] ?? 0;
                            switch ($level_language_to_teach) {
                                case 0: $level_text = 'Level unknown'; break;
                                case 1: $level_text = 'Beginner'; break;
                                case 2: $level_text = 'A1'; break;
                                case 3: $level_text = 'A2'; break;
                                case 4: $level_text = 'B1'; break;
                                case 5: $level_text = 'B2'; break;
                                case 6: $level_text = 'C1'; break;
                                case 7: $level_text = 'C2'; break;
                                default: $level_text = '';
                            }

                            $cadena_idioma_nivel = "$language_to_teach_fullname ($level_text)";

                            // Online / Onsite
                            if ($por_internet_o_presencial == 1) {
                                $cadena_idioma_nivel .= " - Online";
                                $location_label = "Online";
                            } elseif ($por_internet_o_presencial == 2 && is_numeric($local_encuentro)) {
                                $query212 = "SELECT * FROM locales WHERE id_local = " . intval($local_encuentro);
                                $result212 = mysqli_query($link, $query212);
                                $fila212 = mysqli_fetch_array($result212);
                                $ciudad_establecimiento = $fila212['city_google'];
                                $nombre_establecimiento = $fila212['name_local_google'];
                                $direccion_establecimiento = $fila212['full_address_google'];
                                $cadena_idioma_nivel .= " - Onsite in $ciudad_establecimiento: $nombre_establecimiento";
                                $location_label = "Onsite";
                            } else {
                                $location_label = "Online";
                            }

                            // Fechas formateadas
                            $fechaHoraFormateada2 = obtenerFechaHora($unixtimestart, $zonaHoraria);
                            $fechaHoraFormateada2_student = obtenerFechaHora($unixtimestart, $time_shift_student);

                            // Dirección completa
                            $direccion_completa = "";
                            if ($por_internet_o_presencial == 2 && isset($fila212)) {
                                $direccion_completa = "Address:<br>$nombre_establecimiento<br>$direccion_establecimiento<br>$ciudad_establecimiento";
                            }

                            // Estado
                            $status_text = "SCHEDULED";
                            $status_class = "status-pending";
                            if ($session_paid == 0) {
                                $status_text = "WAITING DEPOSIT";
                                $status_class = "status-waiting";
                            } elseif ($session_releasefunds == 0 && $session_paid == 1) {
                                $status_text = "DEPOSIT PAID";
                                $status_class = "status-confirmed";
                            } elseif ($session_releasefunds == 1) {
                                $status_text = "FUNDS RELEASED";
                                $status_class = "status-confirmed";
                            }

                            // Nombre del estudiante
                            $query77 = "SELECT nombre FROM mentor2009 WHERE orden = " . intval($id_student);
                            $result77 = mysqli_query($link, $query77);
                            $fila77 = mysqli_fetch_array($result77);
                            $student_name = ucfirst(explode(" ", trim($fila77['nombre']))[0]);
                        ?>
                            <div class="proposal-card">
                                <div class="card-left">
                                    <div class="avatar-circle">
                                        <img src="<?php echo $path_photo; ?>" alt="<?php echo $student_name; ?>">
                                        <i class="fas fa-graduation-cap star-badge"></i>
                                    </div>
                                    <div class="student-name"><?php echo $student_name; ?></div>
                                    <div class="status-badge <?php echo $status_class; ?>"><?php echo $status_text; ?></div>
                                </div>
                                <div class="card-right-content">
                                    <div class="card-header">
                                        <div class="card-title"><?php echo $cadena_idioma_nivel; ?></div>
                                        <div class="date-info"><i class="far fa-calendar-alt"></i> <?php echo $fechaHoraFormateada2; ?></div>
                                    </div>
                                    <div class="meta-row">
                                        <div class="meta-item"><i class="far fa-clock"></i> <?php echo $duration_min; ?> min</div>
                                        <div class="meta-item"><i class="fas fa-dollar-sign"></i> <?php echo $total_price; ?>&euro;</div>
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
                                        <button class="btn class-name" data-id="<?php echo $id_of_class; ?>"><i class="fas fa-eye"></i> View Details</button>
                                    </div>
                                    <div class="class-details" id="details-class<?php echo $id_of_class; ?>">
                                        <h6><i class="fas fa-info-circle"></i> Class Details</h6>
                                        <p><strong>Description:</strong> <?php echo htmlspecialchars($descriptionofsession); ?></p>
                                        <hr>
                                        <p><strong>Time & Location:</strong><br>
                                            Start Time (My timezone <?php echo $my_timeshift; ?>): <?php echo $fechaHoraFormateada2; ?><br>
                                            Start Time (Student timezone <?php echo $time_shift_student; ?>): <?php echo $fechaHoraFormateada2_student; ?><br>
                                            Duration: <?php echo $duration_min; ?> min<br>
                                            <?php if ($direccion_completa) echo "<br>$direccion_completa<br>"; ?>
                                        </p>
                                        <p><strong>Financials:</strong><br>
                                            Total Price: <?php echo $total_price; ?>&euro; (<?php echo $hourly_price; ?>&euro;/h)<br>
                                            Net Amount: <?php echo number_format($amount_received_by_teacher, 2); ?>&euro;<br>
                                            Status: <?php echo $status_text; ?>
                                        </p>
                                        <p><strong>Other:</strong><br>
                                            Lesson ID: #<?php echo $id_of_class; ?><br>
                                            Student ID: #<?php echo $id_student; ?><br>
                                            Created from serie: <?php echo $recurrent; ?>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        <?php
                        }
                        ?>
                    </div>
                </div>
            </div>
        </section>
        <?php require('../templates/footer.php'); ?>
    </div>
    <script type="text/javascript" src="../public/js/jquery.min.js"></script>
    <script type="text/javascript" src="../public/js/bootstrap.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.class-name').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    var classId = this.getAttribute('data-id');
                    var details = document.getElementById('details-class' + classId);
                    if (details.style.display === 'none' || details.style.display === '') {
                        details.style.display = 'block';
                    } else {
                        details.style.display = 'none';
                    }
                });
            });
        });
    </script>
</body>

</html>