<?php
session_start();
$mi_identificador = $_SESSION['orden2017'];

require('../templates/header_simplified.html');
require('../files/bd.php');
require('../funcionesphp/funciones_idiomas_usuario.php');

// Verificar que la función obtenerFechaHora existe (por si acaso)
if (!function_exists('obtenerFechaHora')) {
    function obtenerFechaHora($timestamp, $timezone) {
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
$tiempoUnix = time();

// Consulta principal: próximas clases como profesor (propuestas aceptadas, no canceladas, fecha futura)
$query = "
    SELECT t.*, m.*
    FROM tracker t
    INNER JOIN mentor2009 m ON t.id_user_teacher = m.orden
    WHERE t.id_user_teacher = " . intval($mi_identificador) . "
      AND t.proposal_accepted_teacher = 2
      AND t.cancelled = 0
      AND t.end_time_unix > $tiempoUnix
    ORDER BY t.start_time_unix ASC
";

$result = mysqli_query($link, $query);
$n_next_lessons = mysqli_num_rows($result);

// Contadores para las pestañas
// Propuestas recibidas pendientes (proposal_accepted_teacher = 0)
$query109 = "SELECT * FROM tracker WHERE id_user_teacher = " . intval($mi_identificador) . " AND proposal_accepted_teacher = 0 AND cancelled = 0 AND start_time_unix > $tiempoUnix";
$result109 = mysqli_query($link, $query109);
$n_received_proposals = mysqli_num_rows($result109);

// Clases pasadas con fondos por liberar (pagadas, no liberadas, ya finalizadas)
$query110 = "SELECT * FROM tracker WHERE id_user_teacher = " . intval($mi_identificador) . " AND proposal_accepted_teacher = 2 AND cancelled = 0 AND paid = 1 AND releasefunds = 0 AND end_time_unix <= $tiempoUnix";
$result110 = mysqli_query($link, $query110);
$n_past_lessons_not_released = mysqli_num_rows($result110);
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

        .proposal-card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.08);
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
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.12);
        }

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
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
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
            background-color: #f2f4f5;
            color: #7a7d81;
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 12px;
            font-weight: 600;
            border: 1px solid #dcdde1;
        }

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

        .class-details {
            background: linear-gradient(135deg, #fafafa 0%, #f5f5f5 100%);
            padding: 20px;
            border-radius: 8px;
            border: 1px solid #e0e0e0;
            margin-top: 20px;
            display: none;
            box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.05);
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

        @media (max-width: 991px) {
            .forum-links {
                position: relative;
                width: 140%;
                margin-left: -20%;
                margin-top: -8.7%;
            }
        }

        @media (max-width: 768px) {
            .proposal-card {
                flex-direction: column;
                padding: 20px;
            }

            .card-left, .card-middle, .card-right {
                width: 100%;
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
        }
    </style>
</head>
<body>
<div class="wrapper">
    <section class="forum-sec">
        <div class="container">
            <div class="forum-links">
                <ul>
                    <li class="active"><a href="./received-futureclasses.php">Next lessons as teacher (<?php echo $n_next_lessons; ?>)</a></li>
                    <li><a href="./received-pendingproposals.php">Received proposals as teacher (<?php echo $n_received_proposals; ?>)</a></li>
                    <li><a href="./received-pendingreleasefunds.php">Pending fund releases (<?php echo $n_past_lessons_not_released; ?>)</a></li>
                </ul>
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

                        // Idioma que se va a enseñar (nombre completo y nivel del estudiante)
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
                        } elseif ($por_internet_o_presencial == 2 && is_numeric($local_encuentro)) {
                            $query212 = "SELECT * FROM locales WHERE id_local = " . intval($local_encuentro);
                            $result212 = mysqli_query($link, $query212);
                            $fila212 = mysqli_fetch_array($result212);
                            $ciudad_establecimiento = $fila212['city_google'];
                            $nombre_establecimiento = $fila212['name_local_google'];
                            $cadena_idioma_nivel .= " - Onsite in $ciudad_establecimiento: $nombre_establecimiento";
                        }

                        // Fechas formateadas
                        $fechaHoraFormateada2 = obtenerFechaHora($unixtimestart, $zonaHoraria);
                        $fechaHoraFormateada2_student = obtenerFechaHora($unixtimestart, $time_shift_student);

                        // Dirección completa
                        $direccion_completa = "";
                        if ($por_internet_o_presencial == 2 && isset($fila212)) {
                            $direccion_establecimiento = $fila212['full_address_google'];
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

                        $location_label = ($por_internet_o_presencial == 2) ? "Onsite" : "Online";

                        // Nombre del estudiante
                        $query77 = "SELECT nombre FROM mentor2009 WHERE orden = " . intval($id_student);
                        $result77 = mysqli_query($link, $query77);
                        $fila77 = mysqli_fetch_array($result77);
                        $student_name = ucfirst(explode(" ", trim($fila77['nombre']))[0]);
                        ?>
                        <div class="proposal-card">
                            <div class="card-left">
                                <div class="avatar-circle">
                                    <img src="<?php echo $path_photo; ?>" alt="Student" style="width:100%; height:100%; border-radius:100%; object-fit:cover;">
                                    <i class="fas fa-star star-badge"></i>
                                </div>
                                <div class="status-badge <?php echo $status_class; ?>"><?php echo $status_text; ?></div>
                            </div>
                            <div class="card-middle">
                                <div class="card-title"><?php echo $cadena_idioma_nivel; ?></div>
                                <div class="meta-row">
                                    <div class="meta-item"><i class="far fa-clock"></i> <?php echo $duration_min; ?> min</div>
                                    <div class="meta-item"><i class="fas fa-dollar-sign"></i> <?php echo $total_price; ?>&euro;</div>
                                    <div class="meta-item"><i class="fas fa-globe"></i> <?php echo $location_label; ?></div>
                                </div>
                                <div class="languages-label">LANGUAGES OFFERED</div>
                                <div class="language-tags">
                                    <?php
                                    if (is_array($my_langs_full_name_array_multidim["$id_student"])) {
                                        foreach ($my_langs_full_name_array_multidim["$id_student"] as $lang) {
                                            if (!empty($lang)) {
                                                echo "<div class=\"lang-tag\">$lang</div>";
                                            }
                                        }
                                    }
                                    ?>
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
                                </div>
                            </div>
                            <div class="card-right">
                                <div class="date-info"><i class="far fa-clock"></i> <?php echo $fechaHoraFormateada2; ?></div>
                                <div class="action-buttons">
                                    <button class="btn class-name" data-id="<?php echo $id_of_class; ?>"><i class="fas fa-eye"></i> View Details</button>
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