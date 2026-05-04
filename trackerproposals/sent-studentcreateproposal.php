<?php

session_start();

// ===== PRODUCCIÓN - SIN MODO DESARROLLO =====
// Todos los datos vienen exclusivamente de la base de datos

require('../templates/header_simplified.html');
require('../files/bd.php');
require('../funcionesphp/funciones_idiomas_usuario.php');

// Verificar que el usuario está logueado
if (!isset($_SESSION['orden2017']) || !is_numeric($_SESSION['orden2017'])) {
    die("You are not logged in.");
}

$my_id_orden = $_SESSION['orden2017'];

// Obtener el ID del profesor de la URL o POST
$teacher_id_orden = isset($_GET['tid']) ? $_GET['tid'] : (isset($_POST['tid']) ? $_POST['tid'] : 0);

if (empty($teacher_id_orden)) {
    die("Teacher ID is required.");
}

// Verificar que el alumno y el profesor son pareja en couples2009antiguos
$query101 = "SELECT n_pareja FROM couples2009antiguos WHERE user_id_2='$my_id_orden' AND user_id_1='$teacher_id_orden'";
$result101 = mysqli_query($link, $query101);
$fila101 = mysqli_fetch_array($result101);
$resultado101 = $fila101['n_pareja'];

$query102 = "SELECT n_pareja FROM couples2009antiguos WHERE user_id_1='$my_id_orden' AND user_id_2='$teacher_id_orden' ";
$result102 = mysqli_query($link, $query102);
$fila102 = mysqli_fetch_array($result102);
$resultado102 = $fila102['n_pareja'];

if (empty($resultado101) and empty($resultado102)) {
    die("<div style='text-align: center; margin-top: 50px; padding: 20px; background-color: #f8d7da; color: #721c24; border-radius: 8px; max-width: 600px; margin-left: auto; margin-right: auto;'>
        <h3>❌ Access Forbidden</h3>
        <p>You are not partnered with this teacher. You can only create proposals with your language exchange partners.</p>
        <a href='../user/me.php' style='display: inline-block; margin-top: 15px; padding: 10px 20px; background-color: #e65f00; color: white; text-decoration: none; border-radius: 5px;'>Return to homepage</a>
    </div>");
}

// Obtener datos del alumno (time shift y ubicación)
$query77 = "SELECT timeshift, Gpslat, Gpslng FROM mentor2009 WHERE orden='$my_id_orden' ";
$result77 = mysqli_query($link, $query77);
if (!mysqli_num_rows($result77)) {
    die("You need to log in.");
}
$fila77 = mysqli_fetch_array($result77);

$my_timeshift = $fila77['timeshift'];
$latitud1 = $fila77['Gpslat'];
$longitud1 = $fila77['Gpslng'];

// Obtener datos del profesor incluyendo evaluaciones
$query88 = "SELECT nombre, fotoext, timeshift, Gpslat, Gpslng, ev_num_diaria, ev_proporc_diaria FROM mentor2009 WHERE orden='$teacher_id_orden' ";
$result88 = mysqli_query($link, $query88);
if (!mysqli_num_rows($result88)) {
    die("Teacher does not exist.");
}
$fila88 = mysqli_fetch_array($result88);

$latitud2 = $fila88['Gpslat'];
$longitud2 = $fila88['Gpslng'];
$teacher_timeshift = $fila88['timeshift'];

// Obtener evaluaciones del profesor directamente de mentor2009
$num_evaluations = (int)$fila88['ev_num_diaria'];
$nota_evalu = $fila88['ev_proporc_diaria'];

// Calcular el porcentaje correctamente
if ($nota_evalu > 1) {
    $percentage_positive = round($nota_evalu);
} else {
    $percentage_positive = round($nota_evalu * 100);
}
$percentage_positive = min(100, $percentage_positive);

// Lógica mejorada para la foto del profesor
$extension_teacher = $fila88['fotoext'];
$path_photo = "../uploader/upload_pic/thumb_$teacher_id_orden.$extension_teacher";

if (!file_exists($path_photo)) {
    $possible_extensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp'];
    $found = false;
    foreach ($possible_extensions as $ext) {
        $temp_path = "../uploader/upload_pic/thumb_$teacher_id_orden.$ext";
        if (file_exists($temp_path)) {
            $path_photo = $temp_path;
            $found = true;
            break;
        }
    }
    if (!$found) {
        $path_photo = "../uploader/default.jpg";
    }
}

$t_name = $fila88['nombre'];
$palabras = explode(" ", $t_name);
$t_name = ucfirst($palabras[0]);

// Obtener idiomas del profesor
$idiomas_profe_array_multidim = array();
$idiomas_profe_full_name_array_multidim = array();
$idiomas_profe_forshare_array_multidim = array();
$idiomas_profe_price_array_multidim = array();

list(
    $idiomas_profe_array_multidim[$my_id_orden],
    $idiomas_profe_full_name_array_multidim[$my_id_orden],
    $dummy,
    $idiomas_profe_forshare_array_multidim[$my_id_orden],
    $idiomas_profe_price_array_multidim[$my_id_orden],
    $dummy2,
    $dummy3,
    $dummy4,
    $dummy5
) = lenguas_que_conoce_usuario($teacher_id_orden, $link);

// Check if form is submitted
if (isset($_POST['start_date'], $_POST['start_time'], $_POST['duration'], $_POST['language_to_learn'])) {

    $number_repetitions = (int)$_POST['repetitions'];
    $recurrence_type = $_POST['recurrence'];
    $description = mysqli_real_escape_string($link, $_POST['description_of_sessions']);

    if ($number_repetitions < 2) {
        $is_recurrent = 0;
        $recurrence_type = 'none';
    } else {
        $is_recurrent = 1;

        switch ($recurrence_type) {
            case "every_week":
                $interval_recurrence = "P7D";
                break;
            case "every_2_weeks":
                $interval_recurrence = "P14D";
                break;
            case "every_4_weeks":
                $interval_recurrence = "P28D";
                break;
            default:
                $interval_recurrence = "P7D";
        }
    }

    $startDate = $_POST['start_date'];
    $startTime = $_POST['start_time'];
    $duration = (int) $_POST['duration'];

    $online_u_offline = (int)$_POST['presencial_u_online'];

    if ($online_u_offline == 1) {
        $local_del_encuentro = null;
    } else {
        $local_del_encuentro = (int)$_POST['id_local_event'];
    }

    // Obtener el precio por hora del idioma seleccionado
    $language_taught = mysqli_real_escape_string($link, $_POST['language_to_learn']);
    $query881 = "SELECT lang_price FROM my_langs WHERE id='$teacher_id_orden' AND lang_id='$language_taught'";
    $result881 = mysqli_query($link, $query881);

    if (!mysqli_num_rows($result881)) {
        die("Language price error.");
    }
    $fila881 = mysqli_fetch_array($result881);
    $hourly_rate = $fila881['lang_price'];

    if (empty($hourly_rate) || is_null($hourly_rate)) {
        $hourly_rate = 0;
    }

    $total_session_price = $hourly_rate * $duration / 60;

    $dateParts = explode('/', $startDate);
    $startDate1 = $dateParts[1] . '/' . $dateParts[0] . '/' . $dateParts[2];

    $zona = new DateTimeZone($my_timeshift);

    $startDate = new DateTime($startDate1 . " " . $startTime, $zona);
    $endDate = new DateTime($startDate1 . " " . $startTime, $zona);
    $endDate->add(new DateInterval("PT{$duration}M"));

    // Inserción de clases en tracker (posiblemente múltiples por repetición)
    for ($iii = 0; $iii < $number_repetitions; $iii++) {

        $date_start_insert_db = $startDate->format('Y-m-d H:i:s');
        $date_end_insert_db = $endDate->format('Y-m-d H:i:s');

        $unixtime_start_insert_db = $startDate->getTimestamp();
        $unixtime_end_insert_db = $endDate->getTimestamp();

        $language_taught_escaped = mysqli_real_escape_string($link, $language_taught);
        $description_escaped = mysqli_real_escape_string($link, $description);

        $local_value = is_null($local_del_encuentro) ? 'NULL' : $local_del_encuentro;

        $query = "
        INSERT INTO tracker (
            created_from_recurrent, 
            id_user_teacher, 
            id_user_student, 
            time_shift_student, 
            start_time_unix, 
            end_time_unix, 
            date_start_local, 
            date_end_local, 
            session_lenght_minutes, 
            language_taught, 
            hourly_rate_original, 
            price_session_total, 
            description_session, 
            created_timestamp, 
            id_local, 
            onlineonsite
        ) VALUES (
            '$is_recurrent', 
            '$teacher_id_orden', 
            '$my_id_orden', 
            '$my_timeshift', 
            '$unixtime_start_insert_db', 
            '$unixtime_end_insert_db', 
            '$date_start_insert_db', 
            '$date_end_insert_db', 
            '$duration', 
            '$language_taught_escaped', 
            '$hourly_rate', 
            '$total_session_price', 
            '$description_escaped', 
            NOW(), 
            $local_value, 
            '$online_u_offline'
        )";

        if (!mysqli_query($link, $query)) {
            echo "Error 4653. Contact webmaster: " . mysqli_error($link);
            exit;
        }

        if ($is_recurrent == 1) {
            $startDate->add(new DateInterval($interval_recurrence));
            $endDate->add(new DateInterval($interval_recurrence));
        }
    }

    // Mensaje de éxito
    echo "<div style='text-align: center; margin-top: 50px; padding: 30px; background-color: #d4edda; color: #155724; border-radius: 8px; max-width: 600px; margin-left: auto; margin-right: auto;'>
        <h3>✅ Success!</h3>
        <p style='font-size: 18px; margin: 20px 0;'>The lesson(s) have been proposed to your partner. Now he/she will need to accept them. You will receive a notification in the Lingua2 app.</p>
        <a href='../user/me.php' style='display: inline-block; margin-top: 10px; padding: 12px 30px; background-color: #e65f00; color: white; text-decoration: none; border-radius: 5px; font-weight: bold;'>Return to homepage</a>
    </div>";

    exit(0);
}

// Determinar el tipo de reunión (online u onsite)
$type_of_meeting = 'online meeting';
$type_of_meeting_id = "1";

if (!($latitud1 == 0 && $longitud1 == 0) && !($latitud2 == 0 && $longitud2 == 0)) {
    $type_of_meeting = 'ONSITE';
    $type_of_meeting_id = "2";

    // Buscar locales cercanos para reuniones presenciales
    $query333 = "
        SELECT 
        lc.id_local, lc.full_address_google, lc.country_google, lc.city_google, lc.name_local_google,
        (acos(sin(radians(lc.lat)) * sin(radians($latitud1)) + 
        cos(radians(lc.lat)) * cos(radians($latitud1)) * 
        cos(radians(lc.lng) - radians($longitud1))) * 6378) AS distanciaPunto1Punto2,
        (acos(sin(radians(lc.lat)) * sin(radians($latitud2)) + 
        cos(radians(lc.lat)) * cos(radians($latitud2)) * 
        cos(radians(lc.lng) - radians($longitud2))) * 6378) AS distanciaPunto1Punto2_teacher
        FROM locales lc
        HAVING distanciaPunto1Punto2 < 10 AND distanciaPunto1Punto2_teacher < 10
        ORDER BY distanciaPunto1Punto2 ASC
        LIMIT 1000
    ";
    $result333 = mysqli_query($link, $query333);
    $num_rows_locals = mysqli_num_rows($result333);
}

?>

<!DOCTYPE html>
<html>

<head>
    <title>Create class</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-ui/1.13.2/themes/base/jquery-ui.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>
</head>
<style>
    /* ===== RESET TOTAL PARA HEADER FULL-WIDTH ===== */
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    html,
    body {
        margin: 0;
        padding: 0;
        width: 100%;
        overflow-x: hidden;
    }

    body {
        font-family: 'Roboto', sans-serif;
        background-color: var(--bg-grey);
    }

    /* Ajuste del header para que llegue arriba */
    .forum-sec {
        display: flex;
        background-color: #fff;
        width: 100%;
        margin-top: 0;
        position: relative;
        top: 0;
        left: 0;
    }

    .forum-links {
        background-color: #fff;
        padding: 10px 0;
        width: 100%;
        margin-left: 0;
        margin-top: 0;
    }

    .forum-links ul {
        list-style-type: none;
        display: flex;
        margin-left: 30px;
        gap: 20px;
        padding: 0;
    }

    .forum-links ul li {
        text-align: center;
        margin-right: 20px;
        border-bottom: 2px solid transparent;
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

    .forum-links-btn {
        display: none;
    }

    .forum-links ul li.active {
        border-color: #e65f00;
    }

    .forum-links ul li.active a {
        color: #e65f00;
    }

    /* Ajuste responsive para el header */
    @media screen and (max-width: 991px) {
        .forum-links {
            width: 100%;
            margin-left: 0;
            margin-top: 0;
            padding: 10px 20px;
        }

        .forum-links ul {
            margin-left: 0;
            padding-left: 20px;
            flex-wrap: wrap;
        }
    }

    @media screen and (max-width: 768px) {
        .forum-links-btn {
            display: block;
        }
    }

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

    main {
        padding: 0;
    }

    h3 {
        font-size: 18px;
        font-weight: 600;
        margin-bottom: 15px;
    }

    .cuerpo {
        display: flex;
        padding-left: 7px;
        margin: 10px 20%;
        width: 60%;
    }

    .contenedor1 {
        width: 70%;
        padding: 20px 0px;
    }

    .usr-question {
        background-color: #fff;
        padding: 25px;
        border-radius: 8px;
        box-shadow: 0 3px 10px rgba(0, 0, 0, 0.08);
        margin-bottom: 20px;
        border-left: 5px solid var(--primary-orange);
        transition: transform 0.2s, box-shadow 0.2s;
    }

    .usr-question:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.12);
    }

    /* ===== CALENDARIO MEJORADO CON MÁS ESPACIADO ===== */
    .date-input-wrapper {
        position: relative;
        width: 100%;
    }

    .date-input-wrapper i {
        position: absolute;
        left: 15px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--accent-orange);
        font-size: 16px;
        z-index: 1;
        pointer-events: none;
    }

    .modern-input.date-field {
        padding-left: 45px;
        background-color: white;
        cursor: pointer;
    }

    .modern-input.date-field:hover {
        border-color: var(--accent-orange);
    }

    .modern-input.date-field:focus {
        border-color: var(--primary-orange);
        box-shadow: 0 0 0 3px rgba(230, 126, 34, 0.1);
    }

    /* Estilo mejorado del datepicker - MÁS ESPACIADO */
    .ui-datepicker {
        background: #ffffff;
        border: 2px solid var(--primary-orange);
        border-radius: 16px;
        padding: 20px;
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
        font-family: 'Roboto', sans-serif;
        width: 300px;
        margin-top: 8px;
    }

    .ui-datepicker-header {
        background: transparent;
        border: none;
        padding: 10px 0 20px 0;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .ui-datepicker-title {
        font-size: 18px;
        font-weight: 700;
        color: var(--primary-orange);
        text-align: center;
        margin: 0 15px;
        padding: 5px 0;
    }

    .ui-datepicker-prev,
    .ui-datepicker-next {
        background: var(--light-orange) !important;
        border: 1px solid var(--accent-orange);
        border-radius: 8px !important;
        width: 36px;
        height: 36px;
        cursor: pointer;
        transition: all 0.2s ease;
        position: relative;
        margin: 0 5px;
    }

    .ui-datepicker-prev {
        margin-right: 10px;
    }

    .ui-datepicker-next {
        margin-left: 10px;
    }

    .ui-datepicker-prev:hover,
    .ui-datepicker-next:hover {
        background: var(--accent-orange) !important;
        border-color: var(--primary-orange);
        transform: scale(1.05);
    }

    .ui-datepicker-prev span,
    .ui-datepicker-next span {
        filter: brightness(0) saturate(100%) invert(40%) sepia(96%) saturate(495%) hue-rotate(350deg) brightness(92%) contrast(92%);
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
    }

    .ui-datepicker-prev:hover span,
    .ui-datepicker-next:hover span {
        filter: brightness(0) invert(1);
    }

    .ui-datepicker-calendar th {
        font-size: 13px;
        font-weight: 700;
        color: var(--text-grey);
        padding: 12px 5px 8px 5px;
        text-transform: uppercase;
    }

    .ui-datepicker-calendar td {
        padding: 4px;
    }

    .ui-datepicker-calendar td a {
        display: block;
        text-align: center;
        padding: 10px 5px;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 500;
        color: var(--text-dark);
        background: white;
        border: 1px solid transparent;
        transition: all 0.2s ease;
    }

    .ui-datepicker-calendar td a:hover {
        background: var(--light-orange);
        border-color: var(--accent-orange);
        transform: scale(1.05);
    }

    .ui-datepicker-today a {
        background: var(--light-orange) !important;
        border: 1px solid var(--accent-orange) !important;
        color: var(--primary-orange) !important;
        font-weight: 700;
    }

    .ui-datepicker-current-day a {
        background: var(--primary-orange) !important;
        color: white !important;
        border-color: var(--primary-orange) !important;
        font-weight: 700;
        box-shadow: 0 2px 8px rgba(211, 84, 0, 0.3);
    }

    .ui-datepicker-unselectable span {
        color: #ccc;
        padding: 10px 5px;
        display: block;
        text-align: center;
        font-size: 14px;
    }

    /* Espaciado adicional para el título del mes */
    .ui-datepicker .ui-datepicker-title select {
        font-size: 14px;
        padding: 5px 8px;
        margin: 0 3px;
        border: 1px solid var(--border-color);
        border-radius: 6px;
        color: var(--text-dark);
        background: white;
    }

    /* ===== FIN CALENDARIO MEJORADO ===== */

    .form-group {
        margin-bottom: 20px;
    }

    .form-group h5 {
        font-size: 14px;
        font-weight: 600;
        color: var(--text-dark);
        margin-bottom: 10px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .form-group h5 i {
        color: var(--accent-orange);
    }

    .modern-select {
        width: 100%;
        padding: 12px 15px;
        border: 2px solid #e0e0e0;
        border-radius: 8px;
        font-size: 14px;
        color: var(--text-dark);
        background-color: #fff;
        cursor: pointer;
        transition: all 0.3s ease;
        appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%23999'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 15px center;
        background-size: 16px;
    }

    .modern-select:hover {
        border-color: var(--accent-orange);
    }

    .modern-select:focus {
        outline: none;
        border-color: var(--primary-orange);
        box-shadow: 0 0 0 3px rgba(230, 126, 34, 0.1);
    }

    .modern-input {
        width: 100%;
        padding: 12px 15px;
        border: 2px solid #e0e0e0;
        border-radius: 8px;
        font-size: 14px;
        transition: all 0.3s ease;
    }

    .modern-input:focus {
        outline: none;
        border-color: var(--primary-orange);
        box-shadow: 0 0 0 3px rgba(230, 126, 34, 0.1);
    }

    .time-selector-group {
        display: grid;
        grid-template-columns: 1fr 1fr 1fr;
        gap: 10px;
    }

    /* ===== DESPLEGABLES NARANJAS ===== */
    .time-select {
        padding: 12px;
        border: 2px solid #e0e0e0;
        border-radius: 8px;
        font-size: 14px;
        background-color: #fff;
        cursor: pointer;
        transition: all 0.3s ease;
        text-align: center;
        appearance: none;
        -webkit-appearance: none;
        -moz-appearance: none;
        /* Flecha personalizada naranja (opcional) */
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%23e67e22'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 12px center;
        background-size: 14px;
        padding-right: 35px;
    }

    .time-select:hover {
        border-color: var(--accent-orange); /* #e67e22 */
    }

    .time-select:focus {
        outline: none !important;
        border-color: var(--primary-orange) !important; /* #d35400 */
        box-shadow: 0 0 0 3px rgba(230, 126, 34, 0.2) !important;
    }

    /* Opciones dentro del desplegable */
    .time-select option {
        background-color: #fff;
        color: var(--text-dark);
        padding: 10px;
    }

    /* Opción seleccionada o al pasar el ratón (en navegadores que lo soporten) */
    .time-select option:checked,
    .time-select option:hover {
        background: var(--light-orange) !important; /* #fdf2e9 */
        color: var(--primary-orange) !important;
    }

    .modern-textarea {
        width: 100%;
        padding: 15px;
        border: 2px solid #e0e0e0;
        border-radius: 8px;
        font-size: 14px;
        min-height: 100px;
        resize: vertical;
        transition: all 0.3s ease;
        font-family: 'Roboto', sans-serif;
    }

    .modern-textarea:focus {
        outline: none;
        border-color: var(--primary-orange);
        box-shadow: 0 0 0 3px rgba(230, 126, 34, 0.1);
    }

    .char-counter {
        text-align: right;
        font-size: 12px;
        color: #999;
        margin-top: 5px;
    }

    .recurrence-box {
        background: linear-gradient(135deg, #fafafa 0%, #f5f5f5 100%);
        padding: 20px;
        border-radius: 8px;
        border: 1px solid #e0e0e0;
        margin-top: 15px;
    }

    .location-info {
        background: linear-gradient(135deg, #fafafa 0%, #f5f5f5 100%);
        padding: 15px;
        border-radius: 8px;
        border: 1px solid #e0e0e0;
        font-size: 14px;
        color: var(--text-grey);
    }

    .location-info i {
        color: var(--accent-orange);
        margin-right: 8px;
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

    .btn-primary {
        background: linear-gradient(135deg, #fdf2e9 0%, #fce8d6 100%);
        color: #d35400;
        border: 2px solid #e67e22;
    }

    .btn-outline {
        background: white;
        color: var(--text-grey);
        border: 2px solid #e0e0e0;
        text-transform: none;
        box-shadow: none;
    }

    .btn-outline:hover {
        background: #f5f5f5;
        color: var(--text-dark);
        border-color: var(--text-grey);
        transform: translateY(-1px);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .contenedor2 {
        width: 40%;
        padding: 20px;
    }

    .widget {
        float: left;
        width: 100%;
        background-color: #fff;
        border-left: none;
        border-right: none;
        border-bottom: none;
        border-radius: 8px;
        box-shadow: 0 3px 10px rgba(0, 0, 0, 0.08);
        margin-bottom: 20px;
        overflow: hidden;
    }

    /* Widget de evaluaciones - Versión ultra compacta SIN CABECERA */
    .widget-feat {
        padding: 10px 12px !important;
        border-bottom: 1px solid var(--border-color);
    }

    .evaluation-compact {
        text-align: center;
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .evaluation-compact .metric {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
    }

    .evaluation-compact .metric i {
        font-size: 18px;
        color: var(--accent-orange);
    }

    .evaluation-compact .metric .percentage {
        font-size: 20px;
        font-weight: 700;
        color: var(--primary-orange);
        line-height: 1;
    }

    .evaluation-compact .metric .percentage small {
        font-size: 13px;
        font-weight: 400;
        color: var(--text-grey);
        margin-left: 2px;
    }

    .evaluation-compact .bar {
        width: 100%;
        height: 4px;
        background-color: var(--border-color);
        border-radius: 2px;
        overflow: hidden;
        margin: 2px 0;
    }

    .evaluation-compact .bar-fill {
        height: 100%;
        background: linear-gradient(90deg, var(--accent-orange), var(--primary-orange));
        border-radius: 2px;
        transition: width 0.3s ease;
    }

    .evaluation-compact .total {
        font-size: 11px;
        font-weight: 700;
        color: var(--text-dark);
        margin-top: 2px;
    }

    .evaluation-compact .no-data {
        font-size: 13px;
        color: var(--text-grey);
        font-style: italic;
        padding: 5px 0;
    }

    .title-wd {
        width: 100%;
        color: var(--text-dark);
        font-size: 16px;
        font-weight: 600;
        border-bottom: 1px solid var(--border-color);
        padding: 15px 20px;
        margin: 0;
        background: linear-gradient(135deg, #fafafa 0%, #f5f5f5 100%);
    }

    .widget-user ul {
        padding: 15px;
        list-style: none;
        margin: 0;
    }

    .widget-user li {
        display: flex;
        align-items: center;
        padding: 10px 0;
        border-bottom: 1px solid var(--border-color);
    }

    .widget-user li:last-child {
        border-bottom: none;
    }

    .usr-ms-img {
        width: 40px;
        height: 40px;
        margin-right: 15px;
        border-radius: 100%;
        border: 2px solid var(--primary-orange);
        overflow: hidden;
    }

    .usr-ms-img img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .usr-mg-info {
        flex-grow: 1;
    }

    .usr-mg-info h3 {
        margin: 0;
        font-size: 14px;
        font-weight: 600;
        color: var(--text-dark);
    }

    .usr-mg-info p {
        font-size: 12px;
        color: var(--text-grey);
        margin: 2px 0 0;
    }

    .widget-user li span {
        margin-left: auto;
        font-size: 13px;
        font-weight: 600;
        color: var(--primary-orange);
    }

    .usr-msg-details {
        display: flex;
        align-items: center;
        width: 100%;
    }

    #preview-container {
        padding: 25px;
        background: linear-gradient(135deg, #fafafa 0%, #f5f5f5 100%);
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        margin-top: 20px;
        display: none;
        box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.05);
    }

    #preview-container h3 {
        color: var(--primary-orange);
        font-size: 16px;
        font-weight: bold;
        margin-bottom: 20px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    #preview-list {
        list-style-type: none;
        padding: 0;
        margin-bottom: 20px;
    }

    #preview-list li {
        padding: 10px 0;
        border-bottom: 1px dashed #e0e0e0;
        color: var(--text-dark);
        font-size: 14px;
    }

    #preview-list li:last-child {
        border-bottom: none;
    }

    #preview-list strong {
        color: var(--primary-orange);
        font-weight: 600;
    }

    .preview-actions {
        display: flex;
        gap: 10px;
        margin-top: 20px;
    }

    hr {
        border: none;
        border-top: 2px solid #e0e0e0;
        margin: 20px 0;
    }

    /* Tooltips */
    #teacher-info-tooltip,
    #info-tooltip {
        display: none;
        position: absolute;
        max-width: 250px;
        background-color: var(--text-dark);
        color: #fff;
        padding: 15px;
        border-radius: 8px;
        left: 43%;
        transform: translateX(-50%);
        font-size: 12px;
        line-height: 1.6;
        z-index: 1000;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        margin-top: 10px; /* Pequeña separación para facilitar el hover */
        pointer-events: auto; /* Permite que el ratón interactúe con el tooltip */
    }

    i {
        cursor: pointer;
    }

    .empty-state {
        background: white;
        border-radius: 8px;
        box-shadow: 0 3px 10px rgba(0, 0, 0, 0.08);
        padding: 40px;
        text-align: center;
        margin: 20px 0;
        border-left: 5px solid var(--primary-orange);
    }

    .empty-state i {
        font-size: 48px;
        color: var(--accent-orange);
        margin-bottom: 15px;
    }

    .empty-state h3 {
        color: var(--text-dark);
        font-size: 18px;
        font-weight: 600;
        margin-bottom: 10px;
    }

    .empty-state p {
        color: var(--text-grey);
        font-size: 14px;
    }

    @media screen and (max-width: 991px) {
        .cuerpo {
            width: 80%;
            margin: 10px auto;
        }
    }

    @media screen and (max-width: 768px) {
        .cuerpo {
            flex-direction: column;
            width: 90%;
        }

        .contenedor1,
        .contenedor2 {
            width: 100%;
        }

        .time-selector-group {
            grid-template-columns: 1fr;
        }

        .forum-links ul {
            flex-wrap: wrap;
            padding: 0 20px;
        }

        .preview-actions {
            flex-direction: column;
        }
    }
</style>

<body>
    <section class="forum-sec">
        <div class="container">
            <div class="forum-links">
                <ul>
                    <li class="active"><a href="#" title="">Create meeting proposal for your partner</a></li>
                    <li><a href="./received-futureclasses.php" title="">Lessons as teacher</a></li>
                    <li><a href="./sent-futureclasses.php" title="">Lessons as student</a></li>
                </ul>
            </div>
            <div class="forum-links-btn">
                <a href="#" title=""><i class="fa fa-bars"></i></a>
            </div>
        </div>
    </section>

    <section class="cuerpo">
        <div class="contenedor1">
            <div class="usr-question">

                <h3><i class="fas fa-calendar-plus" style="color: var(--accent-orange); margin-right: 8px;"></i> Make a meeting proposal to your partner</h3>

                <form id="class-form" method="post" action="<?php echo $_SERVER['PHP_SELF'] . "?tid=$teacher_id_orden"; ?>">

                    <div class="form-group">
                        <h5><i class="fas fa-language"></i> Select language to practice</h5>
                        <select id="language_to_learn" name="language_to_learn" class="modern-select" required>
                            <option value="" disabled selected>Choose a language...</option>
                            <?php
                            if (!empty($idiomas_profe_array_multidim[$my_id_orden])) {
                                for ($uu = 0; $uu < count($idiomas_profe_array_multidim[$my_id_orden]); $uu++) {
                                    if ($idiomas_profe_forshare_array_multidim[$my_id_orden][$uu] == 1) {
                                        $price_text = !is_null($idiomas_profe_price_array_multidim[$my_id_orden][$uu])
                                            ? ' (' . $idiomas_profe_price_array_multidim[$my_id_orden][$uu] . '€/hour)'
                                            : ' (free language exchange)';
                            ?>
                                        <option value="<?php echo $idiomas_profe_array_multidim[$my_id_orden][$uu]; ?>">
                                            <?php echo $idiomas_profe_full_name_array_multidim[$my_id_orden][$uu] . $price_text; ?>
                                        </option>
                            <?php
                                    }
                                }
                            }
                            ?>
                        </select>
                    </div>

                    <!-- Tooltips ocultos -->
                    <div id="info-tooltip">
                        <strong>My time shift:</strong><br>
                        <?php echo $my_timeshift; ?><br>
                        <?php
                        date_default_timezone_set($my_timeshift);
                        echo 'Local time: ' . date('H:i d/m/Y');
                        ?>
                    </div>

                    <div id="teacher-info-tooltip">
                        <strong>Teacher's time shift:</strong><br>
                        <?php echo $teacher_timeshift; ?><br>
                        <?php
                        date_default_timezone_set($teacher_timeshift);
                        echo 'Local time: ' . date('H:i d/m/Y');
                        ?>
                    </div>

                    <div class="form-group">
                        <h5>
                            <i class="fas fa-calendar"></i> Starting local date (<?php echo $my_timeshift; ?>)
                            <i style="color:#b2b2b2; font-size:16px; margin-left: 5px;" class="fas fa-info-circle" onmouseover="showInfo()" onmouseout="hideInfo()"></i>
                        </h5>
                        <div class="date-input-wrapper">
                            <i class="fas fa-calendar-alt"></i>
                            <input type="text" id="start_date" name="start_date" class="modern-input date-field" placeholder="DD/MM/YYYY" required autocomplete="off">
                        </div>
                    </div>

                    <div class="form-group">
                        <h5>
                            <i class="fas fa-clock"></i> Starting local time
                            <i style="color:#b2b2b2; font-size:16px; margin-left: 5px;" class="fas fa-info-circle" onmouseover="showTeacherInfo()" onmouseout="hideTeacherInfo()"></i>
                        </h5>
                        <div class="time-selector-group">
                            <select id="start_hour" name="start_hour" class="time-select" required>
                                <option value="" disabled selected>Hour</option>
                                <?php for ($i = 1; $i <= 12; $i++): ?>
                                    <option value="<?php echo $i; ?>"><?php echo str_pad($i, 2, "0", STR_PAD_LEFT); ?></option>
                                <?php endfor; ?>
                            </select>
                            <select id="start_minute" name="start_minute" class="time-select" required>
                                <option value="" disabled selected>Min</option>
                                <?php for ($i = 0; $i < 60; $i += 5): ?>
                                    <option value="<?php echo str_pad($i, 2, "0", STR_PAD_LEFT); ?>">
                                        <?php echo str_pad($i, 2, "0", STR_PAD_LEFT); ?>
                                    </option>
                                <?php endfor; ?>
                            </select>
                            <select id="start_am_pm" name="start_am_pm" class="time-select" required>
                                <option value="" disabled selected>AM/PM</option>
                                <option value="AM">AM</option>
                                <option value="PM">PM</option>
                            </select>
                        </div>
                        <input type="hidden" id="start_time" name="start_time">
                    </div>

                    <div class="form-group">
                        <h5><i class="fas fa-hourglass-half"></i> Length of the class (minutes)</h5>
                        <input type="number" id="duration" name="duration" class="modern-input" min="1" max="300" value="60" required>
                    </div>

                    <div class="form-group">
                        <h5><i class="fas fa-redo-alt"></i> Number of repetitions</h5>
                        <input type="number" id="repetitions" name="repetitions" class="modern-input" min="1" max="10" value="1">
                    </div>

                    <div id="recurrence-container" class="recurrence-box" style="display: none;">
                        <h5><i class="fas fa-calendar-week"></i> Recurrence frequency</h5>
                        <select name="recurrence" class="modern-select">
                            <option value="every_week">Every week</option>
                            <option value="every_2_weeks">Every two weeks</option>
                            <option value="every_4_weeks">Every four weeks</option>
                        </select>
                    </div>

                    <?php
                    if (isset($num_rows_locals) && $num_rows_locals > 0): ?>
                        <div class="form-group">
                            <h5><i class="fas fa-map-marker-alt"></i> Meeting location</h5>
                            <select name="id_local_event" class="modern-select" required>
                                <option value="" disabled selected>Select a location...</option>
                                <?php while ($fila333 = mysqli_fetch_array($result333)):
                                    $local_id = $fila333['id_local'];
                                    $dist = number_format($fila333['distanciaPunto1Punto2'], 2);
                                    $dist_teacher = number_format($fila333['distanciaPunto1Punto2_teacher'], 2);
                                    $name_local = $fila333['name_local_google'];
                                    $full_addr = $fila333['full_address_google'];
                                ?>
                                    <option value="<?php echo $local_id; ?>">
                                        <?php echo "$dist km from me · $dist_teacher km from teacher · $name_local - $full_addr"; ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                    <?php
                    elseif (isset($num_rows_locals) && $num_rows_locals == 0):
                        echo '<div class="location-info"><i class="fas fa-wifi"></i> No nearby locations found. Class will be online.</div>';
                        $type_of_meeting_id = "1";
                    endif;
                    ?>

                    <input type="hidden" name="presencial_u_online" value="<?php echo $type_of_meeting_id; ?>">

                    <hr>

                    <div class="form-group">
                        <h5><i class="fas fa-pencil-alt"></i> Topics to practice</h5>
                        <textarea id="description_of_sessions" name="description_of_sessions" class="modern-textarea" maxlength="255" placeholder="I want to practice conditional sentences..."></textarea>
                        <div class="char-counter">
                            <span id="charCount">0</span>/255 characters
                        </div>
                    </div>

                    <input class="btn btn-primary" type="submit" name="submit1" value="See proposed classes" style="margin-top: 20px;">
                </form>
            </div>

            <div id="preview-container">
                <h3><i class="fas fa-eye"></i> Class Preview</h3>
                <ul id="preview-list"></ul>
                <div class="preview-actions">
                    <button id="confirm-submit" class="btn btn-primary">Send proposal to my partner</button>
                    <button id="cancel-submit" class="btn btn-outline">Cancel</button>
                </div>
            </div>
        </div>

        <div class="contenedor2">

            <div class="widget widget-user">
                <h3 class="title-wd"><i class="fas fa-user" style="margin-right: 8px;"></i> Teacher</h3>
                <ul>
                    <li>
                        <div class="usr-msg-details">
                            <div class="usr-ms-img">
                                <img src="<?php echo $path_photo; ?>" alt="<?php echo $t_name; ?>">
                            </div>
                            <div class="usr-mg-info">
                                <h3><?php echo $t_name; ?></h3>
                                <p>Language partner</p>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>

            <div class="widget widget-feat">
                <div class="evaluation-compact">
                    <?php if ($num_evaluations > 0): ?>
                        <div class="metric">
                            <i class="fas fa-star"></i>
                            <span class="percentage"><?php echo $percentage_positive; ?><small>%</small></span>
                        </div>
                        <div class="bar">
                            <div class="bar-fill" style="width: <?php echo $percentage_positive; ?>%;"></div>
                        </div>
                        <div class="total">Based on <?php echo $num_evaluations; ?> reviews</div>
                    <?php else: ?>
                        <div class="no-data">No evaluations yet</div>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    </section>

    <?php require('../templates/footer.php'); ?>

    <script>
        $(document).ready(function() {
            $("#start_date").datepicker({
                dateFormat: "dd/mm/yy",
                minDate: 0,
                maxDate: "+60d",
                showAnim: "fadeIn",
                firstDay: 1,
                showButtonPanel: false,
                beforeShow: function(input, inst) {
                    setTimeout(function() {
                        inst.dpDiv.css({
                            top: $(input).offset().top + $(input).outerHeight() + 10,
                            left: $(input).offset().left
                        });
                    }, 10);
                }
            });

            $("#start_date").on("focus", function() {
                $(this).css("border-color", "var(--primary-orange)");
            }).on("blur", function() {
                $(this).css("border-color", "#e0e0e0");
            });

            $("#start_date, #start_hour, #start_minute, #start_am_pm, #duration").on("change", function() {
                var startHour = $("#start_hour").val();
                var startMinute = $("#start_minute").val();
                var startAMPM = $("#start_am_pm").val();

                if (startAMPM === "PM" && startHour < 12) {
                    startHour = parseInt(startHour) + 12;
                } else if (startAMPM === "AM" && startHour == 12) {
                    startHour = 0;
                }

                var formattedStartTime = startHour + ":" + startMinute;
                $("#start_time").val(formattedStartTime);
            });

            $("#repetitions").on("change", function() {
                if ($(this).val() > 1) {
                    $("#recurrence-container").slideDown(300);
                } else {
                    $("#recurrence-container").slideUp(300);
                }
            });

            function checkInput() {
                var mensaje = document.getElementById("description_of_sessions");
                var charCount = document.getElementById("charCount");
                var mensajeLength = mensaje.value.length;

                if (mensajeLength > 255) {
                    mensaje.value = mensaje.value.substring(0, 255);
                    mensajeLength = 255;
                }

                charCount.textContent = mensajeLength;
            }

            document.getElementById("description_of_sessions").addEventListener("input", checkInput);

            $("input[name='submit1']").on("click", function(event) {
                event.preventDefault();

                var startDate = $("#start_date").datepicker("getDate");
                var startHour = parseInt($("#start_hour").val());
                var startMinute = parseInt($("#start_minute").val());
                var startAMPM = $("#start_am_pm").val();
                var duration = parseInt($("#duration").val());
                var repetitions = parseInt($("#repetitions").val());
                var recurrence = $("select[name='recurrence']").val();
                var description = $("#description_of_sessions").val();
                var language = $("#language_to_learn option:selected").text();
                var teacherTimeShift = "<?php echo $teacher_timeshift; ?>";
                var studentTimeShift = "<?php echo $my_timeshift; ?>";

                if (!startDate || isNaN(startHour) || isNaN(startMinute) || !startAMPM || !duration || !description || !language) {
                    alert("Please fill in all fields before continuing.");
                    return;
                }

                var formattedStartHour = startHour;
                if (startAMPM === "PM" && startHour < 12) {
                    formattedStartHour += 12;
                } else if (startAMPM === "AM" && startHour === 12) {
                    formattedStartHour = 0;
                }

                var previewContent = `
            <li><strong>Language that i want to learn:</strong> ${language}</li>
            <li><strong>My time zone:</strong> ${studentTimeShift}</li>
            <li><strong>Partner's time zone:</strong> ${teacherTimeShift}</li>
            <li><strong>Length of the lesson(s):</strong> ${duration} minutes</li>
            <li><strong>Description:</strong> ${description}</li>
            <li style="border-bottom: none;"><strong>Date and time of the lesson(s):</strong></li>
        `;

                previewContent += "<ul style='list-style: none; padding-left: 1rem; margin-top: 0.5rem;'>";

                for (var i = 0; i < repetitions; i++) {
                    var currentDate = new Date(startDate);
                    if (i > 0) {
                        if (recurrence === "every_week") {
                            currentDate.setDate(currentDate.getDate() + 7 * i);
                        } else if (recurrence === "every_2_weeks") {
                            currentDate.setDate(currentDate.getDate() + 14 * i);
                        } else if (recurrence === "every_4_weeks") {
                            currentDate.setDate(currentDate.getDate() + 28 * i);
                        }
                    }

                    var formattedDate = currentDate.toLocaleDateString("es-ES");

                    var endHour = formattedStartHour;
                    var endMinute = startMinute + duration;

                    if (endMinute >= 60) {
                        endHour += Math.floor(endMinute / 60);
                        endMinute = endMinute % 60;
                    }

                    var displayStartHour = formattedStartHour % 12 || 12;
                    var displayEndHour = endHour % 12 || 12;
                    var endAMPM = endHour >= 12 ? "PM" : "AM";

                    previewContent += `
            <li style='padding: 0.5rem 0; border-bottom: 1px dashed #e0e0e0;'>
                <strong>Lesson ${i + 1}:</strong> ${formattedDate} · ${displayStartHour}:${String(startMinute).padStart(2, "0")} ${startAMPM} - ${displayEndHour}:${String(endMinute).padStart(2, "0")} ${endAMPM}
            </li>`;
                }

                previewContent += "</ul>";

                $("#preview-list").html(previewContent);
                $("#preview-container").slideDown(400);
                $(".usr-question").slideUp(400);
            });

            $("#confirm-submit").on("click", function() {
                $("#class-form").submit();

                let popup = $("<div></div>")
                    .text("A notification has been sent to your partner with your proposal.")
                    .css({
                        "position": "fixed",
                        "bottom": "20px",
                        "right": "20px",
                        "background": "#27ae60",
                        "color": "white",
                        "padding": "15px 25px",
                        "border-radius": "8px",
                        "box-shadow": "0 4px 12px rgba(0,0,0,0.15)",
                        "z-index": "1000",
                        "font-weight": "500",
                        "animation": "slideIn 0.3s ease"
                    })
                    .hide();

                $("body").append(popup);
                popup.fadeIn(300).delay(5000).fadeOut(300);
            });

            $("#cancel-submit").on("click", function() {
                $("#preview-container").slideUp(400);
                $(".usr-question").slideDown(400);
            });

            // ===== GESTIÓN MEJORADA DE TOOLTIPS (SIN PARPADEO) =====
            var infoTimeout, teacherTimeout;

            window.showInfo = function() {
                clearTimeout(infoTimeout);
                $('#info-tooltip').stop(true, true).fadeIn(200);
            };

            window.hideInfo = function() {
                infoTimeout = setTimeout(function() {
                    $('#info-tooltip').stop(true, true).fadeOut(200);
                }, 300); // Retraso para permitir mover el ratón al tooltip
            };

            window.showTeacherInfo = function() {
                clearTimeout(teacherTimeout);
                $('#teacher-info-tooltip').stop(true, true).fadeIn(200);
            };

            window.hideTeacherInfo = function() {
                teacherTimeout = setTimeout(function() {
                    $('#teacher-info-tooltip').stop(true, true).fadeOut(200);
                }, 300);
            };

            // Mantener el tooltip visible si el ratón está sobre él
            $('#info-tooltip').on('mouseenter', function() {
                clearTimeout(infoTimeout);
            }).on('mouseleave', function() {
                hideInfo();
            });

            $('#teacher-info-tooltip').on('mouseenter', function() {
                clearTimeout(teacherTimeout);
            }).on('mouseleave', function() {
                hideTeacherInfo();
            });
        });
    </script>

    <style>
        @keyframes slideIn {
            from {
                transform: translateX(100%);
                opacity: 0;
            }

            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
    </style>
</body>

</html>