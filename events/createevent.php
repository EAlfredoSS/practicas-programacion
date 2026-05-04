<?php
require('../files/bd.php');
session_start();

// ============================================================================
// DETECCIÓN DE HUSO HORARIO (AUTOMÁTICA DESDE EL NAVEGADOR)
// ============================================================================
// Valor por defecto (por si JavaScript no detecta)
$huso_horario_actual = "Europe/Madrid";

// Leer cookie de JavaScript (si existe)
if (isset($_COOKIE['huso_usuario'])) {
    $huso_horario_actual = $_COOKIE['huso_usuario'];
}

// Establecer zona horaria de PHP
date_default_timezone_set($huso_horario_actual);
$hora_actual_sistema = date('H:i:s');

// ============================================================================
// FUNCIÓN PARA CONVERTIR HUSO HORARIO A FORMATO GMT
// ============================================================================
function husoAGMT($huso)
{
    try {
        $timezone = new DateTimeZone($huso);
        $dateTime = new DateTime('now', $timezone);
        $offset = $timezone->getOffset($dateTime);

        $hours = intdiv($offset, 3600);
        $minutes = abs(intdiv(($offset % 3600), 60));

        $signo = $hours >= 0 ? '+' : '-';
        $horas_formato = str_pad(abs($hours), 2, '0', STR_PAD_LEFT);
        $minutos_formato = str_pad($minutes, 2, '0', STR_PAD_LEFT);

        return "GMT" . $signo . $horas_formato . ":" . $minutos_formato;
    } catch (Exception $e) {
        return "GMT+01:00"; // Valor por defecto
    }
}

$gmt_por_defecto = husoAGMT($huso_horario_actual);

if (!isset($_SESSION['orden2017'])) {
    die("You must be logged in in order to use this functionality.");
}
$identificador2017 = $_SESSION['orden2017'];

// ============================================================================
// PROCESAMIENTO DEL FORMULARIO (SOLO SI SE ENVIÓ)
// ============================================================================
if (!empty($_POST['enviar'])) {

    // 1. Capturar y sanitizar datos básicos
    $nombre_evento = strip_tags($_POST['event_name']);
    $descrip_evento = strip_tags($_POST['event_desc']);

    // 2. Capturar idioma según tipo de evento
    if ($_POST['event_type_main'] === 'language') {
        $idioma_evento = strip_tags($_POST['language']);
    } else {
        $idioma_evento = strip_tags($_POST['language_code']);
    }

    // 3. Validar que idioma no esté vacío
    if (empty($idioma_evento)) {
        die("Error: Please select a valid language or event type.");
    }

    // 4. Capturar resto de campos
    $hora_evento = $_POST['start_hour'];
    $minuto_evento = $_POST['start_minute'];
    $fecha_evento = $_POST['start_date'];
    $fecha_min = date('Y-m-d');
    $fecha_max = date('Y-m-d', strtotime('+15 days'));

    if ($fecha_evento < $fecha_min) {
        die("Error: Event date cannot be in the past. Please go back and select a future date within the next 15 days.");
    }

    if ($fecha_evento > $fecha_max) {
        die("Error: Event date cannot be more than 15 days from now. Please go back and select a date within the next 15 days (until " . date('d/m/Y', strtotime('+15 days')) . ").");
    }
    $gmt_evento = $_POST['gmt_hidden'] ?? $_POST['gmt'];
    $country99 = $_POST['country99'];
    $city_evento = strip_tags($_POST['city']);
    $full_address_evento = strip_tags($_POST['event_address'] ?? '');
    $id_local_num = $_POST['id_local_event'] ?? null;
    $duracion_evento = $_POST['event_minutes_length'] ?? 90;

    if ($duracion_evento < 30) {
        die("Event duration cannot be less than 30 minutes. Go back to the form.");
    }

    // 5. Procesar local si existe
    if (!empty($id_local_num)) {
        $query = "SELECT lc.full_address_google, lc.name_local_google 
                  FROM locales lc 
                  WHERE lc.id_local = $id_local_num";
        $result = mysqli_query($link, $query);
        $num_rows_locals = mysqli_num_rows($result);

        if ($num_rows_locals) {
            $fila = mysqli_fetch_array($result);
            $local_name = $fila['name_local_google'];
            $full_addr = $fila['full_address_google'];
            $full_address_evento = $local_name . " - " . $full_addr;
        }
        $local_num = $id_local_num;
    } else {
        $local_num = -1;
    }

    // 6. Crear timestamp del evento
    $event_start_time = "$fecha_evento $hora_evento:$minuto_evento:00 $gmt_evento";
    $unix_time_evento = strtotime($event_start_time);

    if (!is_numeric($unix_time_evento)) {
        die("Error in the date format");
    }

    // 7. Generar código del evento
    $time111 = time();
    $timecod = $time111 + 150;
    $timecod = md5("$timecod", false);
    $timecod = substr($timecod, 0, 19);
    $codigoevento1 = md5("$timecod" . "$time111", false);
    $codigoevento1 = substr($codigoevento1, 0, 39);

    // 8. Validaciones
    if (empty($nombre_evento)) die("Event name cannot be empty. Go back to the form.");
    if (empty($descrip_evento)) die("Event description cannot be empty. Go back to the form.");
    if (empty($idioma_evento)) die("Language cannot be empty. Go back to the form.");
    if (empty($hora_evento)) die("Event time hour field cannot be empty. Go back to the form.");
    if (empty($minuto_evento)) die("Event time minute field cannot be empty. Go back to the form.");
    if (empty($fecha_evento)) die("Event data field cannot be empty. Go back to the form.");
    if (empty($gmt_evento)) die("Event GMT field cannot be empty. Go back to the form.");
    if (empty($country99)) die("Event country field cannot be empty. Go back to the form.");
    if (empty($city_evento)) die("Event city field cannot be empty. Go back to the form.");
    if (empty($full_address_evento)) die("Full address field cannot be empty. Go back to the form.");

    // 9. Verificar que no sea fecha pasada
    $tiempo_corte5 = time() - 24 * 3600;
    if ($unix_time_evento < $tiempo_corte5) {
        die("The event cannot be established in a past date. Go back to the form and select a future date.");
    }

    // 10. Insertar en base de datos
    if ($local_num == -1) {
        $query = "INSERT INTO eventoslista (id_creador, Idioma, event_name, event_desc, unix_start_time, start_time, city, location, country, Codigoevento, id_local)
                  VALUES('$identificador2017', '$idioma_evento', '$nombre_evento', '$descrip_evento', '$unix_time_evento', '$event_start_time', '$city_evento', '$full_address_evento', '$country99', '$codigoevento1', NULL)";
    } else {
        $query = "INSERT INTO eventoslista (id_creador, Idioma, event_name, event_desc, unix_start_time, start_time, city, location, country, Codigoevento, id_local)
                  VALUES('$identificador2017', '$idioma_evento', '$nombre_evento', '$descrip_evento', '$unix_time_evento', '$event_start_time', '$city_evento', '$full_address_evento', '$country99', '$codigoevento1', '$local_num')";
    }

    $result = mysqli_query($link, $query);
    $boolean1 = mysqli_affected_rows($link);

    if (!$boolean1) {
        die('\n<br>\n\nThere was an error and your application was not submitted');
    }

    // 11. Obtener ID del evento insertado
    $last_id = mysqli_insert_id($link);
    if (!$last_id) {
        die("Error 956. Contact webmaster");
    }

    // 12. Redireccionar
?>
    <script>
        window.location.replace("./event_success.php?evid=<?php echo $last_id; ?>");
    </script>
<?php
    exit;
}

// ============================================================================
// CÓDIGO PARA MOSTRAR EL FORMULARIO (Solo si NO se envió)
// ============================================================================

// Verificar sesión
if (!isset($identificador2017)) {
    die("You must be logged in in order to use this functionality.");
}

// Verificar límite de eventos futuros
$tiempo_corte5 = time() - 24 * 3600;
$query = "SELECT * FROM eventoslista 
          WHERE id_creador='$identificador2017' 
          AND unix_start_time>'$tiempo_corte5' 
          AND Createdfromid IS NULL
          ORDER BY unix_start_time ASC";
$result = mysqli_query($link, $query);
$nuevos = mysqli_num_rows($result);

if ($nuevos > 5) {
    die('<br/><br/><p>The maximum amount of future events that you can have is 6.</p>');
}

// Extraer coordenadas GPS del usuario
$query_23 = "SELECT Gpslat, Gpslng FROM mentor2009 WHERE orden='$identificador2017'";
$result_23 = mysqli_query($link, $query_23);
$nuevos_23 = mysqli_num_rows($result_23);

if (!$nuevos_23) {
    die('User does not exist or you disconnected. Login from the Homepage');
}

$fila_23 = mysqli_fetch_array($result_23);
$lat11 = $fila_23['Gpslat'];
$lng11 = $fila_23['Gpslng'];

if ($lat11 == 0 && $lng11 == 0) {
    die("</br></br></br>You haven't added your location. To use this functionality you need to add your location first. Click <a href=\"../user/getgpsposition.php\">here</a>.");
}

// Datos para el formulario
$query_eventtypes = "SELECT eventtypeid, eventtypecode, eventtypename FROM eventtypeother ORDER BY eventtypeid";
$result_eventtypes = mysqli_query($link, $query_eventtypes);
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title>Create Event | Lingua2</title>

    <!-- CSS de Bootstrap -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
    <!-- jQuery UI CSS -->
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
    <!-- CSS locales existentes -->
    <link rel="stylesheet" type="text/css" href="../public/css/animate.css">
    <link rel="stylesheet" type="text/css" href="../public/css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="../public/css/jquery.mCustomScrollbar.min.css">
    <link rel="stylesheet" type="text/css" href="../public/lib/slick/slick.css">
    <link rel="stylesheet" type="text/css" href="../public/css/style.css">
    <link rel="stylesheet" type="text/css" href="../public/css/responsive.css">
    <!-- Font Awesome -->
    <script src="https://kit.fontawesome.com/bb6243346a.js" crossorigin="anonymous"></script>

    <style>
        /* Estilos específicos de createevent.php */
        a {
            color: #e65f00;
        }

        .ui-autocomplete {
            position: absolute;
            z-index: 9999 !important;
            max-height: 200px;
            overflow-y: auto;
            overflow-x: hidden;
            background: white;
            border: 1px solid #ccc;
            list-style: none;
            padding: 0;
            margin: 0;
            font-family: Arial, sans-serif;
        }

        .ui-menu-item {
            padding: 10px 12px;
            cursor: pointer;
            border-bottom: 1px solid #f0f0f0;
        }

        .ui-menu-item:hover,
        .ui-menu-item.ui-state-active,
        .ui-menu-item.ui-state-focus {
            background-color: #e65f00 !important;
            color: white !important;
        }

        .ui-helper-hidden-accessible {
            display: none;
        }

        #code_display {
            margin-top: 8px;
            padding: 8px;
            background-color: #f0f9ff;
            border-radius: 4px;
            font-size: 14px;
        }

        #code_display i {
            color: #10b981;
            margin-right: 5px;
        }

        /* Estilos para GMT */
        #gmt-container {
            margin-bottom: 15px;
        }

        .gmt-wrapper {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        #gmt {
            width: 90%;
            background-color: #f5f5f5;
            transition: background-color 0.3s;
        }

        #edit-gmt {
            background-color: #e65f00;
            border: none;
            color: white;
            padding: 8px 15px;
            border-radius: 5px;
            cursor: pointer;
            width: 10%;
            min-width: 60px;
            transition: background-color 0.3s;
        }

        #edit-gmt.save-mode {
            background-color: #28a745;
        }

        /* Estilo para la caja de información del huso */
        .huso-box {
            margin-bottom: 15px;
            padding: 10px;
            background-color: #f8f9fa;
            border-left: 4px solid #e65f00;
            border-radius: 4px;
        }

        /* Precio */
        #precio_display {
            color: #e65f00;
            font-weight: bold;
            font-size: 1.2em;
        }
    </style>
</head>

<body>

    <?php require_once("../templates/header_simplified.html"); ?>

    <div class="main-section">
        <div class="container">
            <div class="main-section-data">
                <div class="row" style="justify-content:center;">
                    <div class="col-lg-6 col-md-8 no-pd">
                        <div class="main-ws-sec">

                            <h1 style="color:dimgrey;font-size: 40px;">Create Event</h1>
                            <hr>
                            <br>

                            <div class="posts-section mb-4">
                                <div class="posty">
                                    <div class="post-bar no-margin p-3">
                                        <div class="job-description">
                                            <div class="huso-box">
                                                <i class="fas fa-info-circle"></i>
                                                Time Zone: <b><?php echo $huso_horario_actual; ?></b> |
                                                Current Time: <b><?php echo $hora_actual_sistema; ?></b> |
                                                GMT: <b><?php echo $gmt_por_defecto; ?></b>
                                            </div>
                                            <p style="font-size:12px">This is how it works:</p>
                                            <p style="font-size:12px">If you <b>are a participant</b>, you can find interesting events in your city. Check the event and write your comment if you are taking part in it.</p>
                                            <p style="font-size:12px">If you <b>want to promote an international event</b> in your city, you can set up an event to create a language exchange group or to promote your products or establishments.</p>
                                            <p style="font-size:12px">If you <b>are a professional teacher</b> you can set up an event to find customers. You can offer the participants one-on-one classes or group classes.</p>
                                            <p style="font-size:12px">When you <a href="./createevent.php">create an event</a> you will have the chance to invite our Lingua2 users living in your city</p>
                                            <p style="font-size:10px">Fields marked with an asterisk (*) are required</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="posts-section">
                                <div class="posty">
                                    <div class="post-bar no-margin p-3">
                                        <div class="job-description">

                                            <form name="formevent" enctype="multipart/form-data" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST" onsubmit="return validate();">

                                                <div class="form-group">
                                                    <label for="event_name" style="margin-bottom:5px;color:dimgrey;">*Event Name:</label>
                                                    <input type="text" class="form-control" name="event_name" id="event_name" maxlength="50" placeholder="Insert your event name" required />
                                                    <br>

                                                    <label for="event_desc" style="margin-bottom:5px;color:dimgrey;">*Event Description and contact phone:</label>
                                                    <textarea rows="6" class="form-control" placeholder="Give as many details as you can" maxlength="255" name="event_desc" id="event_desc" required></textarea>
                                                    <br>

                                                    <div class="form-group">
                                                        <label style="margin-bottom:10px;color:dimgrey;"><b>*Event Type:</b></label>
                                                        <div>
                                                            <label style="font-weight:normal; margin-right:20px;">
                                                                <input type="radio" name="tipo_evento" value="on-site" checked onchange="toggleLocationFields()"> On-site (Presencial)
                                                            </label>
                                                            <label style="font-weight:normal;">
                                                                <input type="radio" name="tipo_evento" value="online" onchange="toggleLocationFields()"> Online
                                                            </label>
                                                        </div>
                                                    </div>

                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div class="form-group">
                                                                <label><b>Event Length (min):</b></label>
                                                                <input type="number"
                                                                    id="event_length"
                                                                    name="event_minutes_length"
                                                                    class="form-control"
                                                                    value="90"
                                                                    min="30"
                                                                    oninput="calcPrecio()"
                                                                    onblur="corregirDuracion()">
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <label for="start_date" style="margin-bottom:5px;color:dimgrey;">*Start Date (next 15 days only):</label>
                                                    <input type="date"
                                                        class="form-control"
                                                        name="start_date"
                                                        id="start_date"
                                                        min="<?php echo date('Y-m-d'); ?>"
                                                        max="<?php echo date('Y-m-d', strtotime('+15 days')); ?>"
                                                        required />
                                                    <br>

                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <label for="start_hour" style="margin-bottom:5px;color:dimgrey;">*Select Hour:</label>
                                                            <select name="start_hour" id="start_hour" class="form-control" required>
                                                                <option value="">Hour</option>
                                                                <?php for ($h = 0; $h < 24; $h++): ?>
                                                                    <option value="<?= str_pad($h, 2, '0', STR_PAD_LEFT) ?>"><?= str_pad($h, 2, '0', STR_PAD_LEFT) ?></option>
                                                                <?php endfor; ?>
                                                            </select>
                                                        </div>

                                                        <div class="col-md-6">
                                                            <label for="start_minute" style="margin-bottom:5px;color:dimgrey;">*Select Minute:</label>
                                                            <select name="start_minute" id="start_minute" class="form-control" required>
                                                                <option value="">Min</option>
                                                                <option value="00">00</option>
                                                                <option value="05">05</option>
                                                                <option value="10">10</option>
                                                                <option value="15">15</option>
                                                                <option value="20">20</option>
                                                                <option value="25">25</option>
                                                                <option value="30">30</option>
                                                                <option value="35">35</option>
                                                                <option value="40">40</option>
                                                                <option value="45">45</option>
                                                                <option value="50">50</option>
                                                                <option value="55">55</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <br>

                                                    <!-- GMT CON BLOQUEO Y BOTÓN EDIT -->
                                                    <div id="gmt-container">
                                                        <label for="gmt" style="margin-bottom:5px;color:dimgrey;">*Select Time Zone</label>
                                                        <div class="gmt-wrapper">
                                                            <select name="gmt" id="gmt" class="form-control" disabled required>
                                                                <option value="GMT-12:00">(GMT -12:00) Eniwetok, Kwajalein</option>
                                                                <option value="GMT-11:00">(GMT -11:00) Midway Island, Samoa</option>
                                                                <option value="GMT-10:00">(GMT -10:00) Hawaii</option>
                                                                <option value="GMT-09:30">(GMT -9:30) Taiohae</option>
                                                                <option value="GMT-09:00">(GMT -9:00) Alaska</option>
                                                                <option value="GMT-08:00">(GMT -8:00) Pacific Time (US &amp; Canada)</option>
                                                                <option value="GMT-07:00">(GMT -7:00) Mountain Time (US &amp; Canada)</option>
                                                                <option value="GMT-06:00">(GMT -6:00) Central Time (US &amp; Canada), Mexico City</option>
                                                                <option value="GMT-05:00">(GMT -5:00) Eastern Time (US &amp; Canada), Bogota, Lima</option>
                                                                <option value="GMT-04:30">(GMT -4:30) Caracas</option>
                                                                <option value="GMT-04:00">(GMT -4:00) Atlantic Time (Canada), Caracas, La Paz</option>
                                                                <option value="GMT-03:30">(GMT -3:30) Newfoundland</option>
                                                                <option value="GMT-03:00">(GMT -3:00) Brazil, Buenos Aires, Georgetown</option>
                                                                <option value="GMT-02:00">(GMT -2:00) Mid-Atlantic</option>
                                                                <option value="GMT-01:00">(GMT -1:00) Azores, Cape Verde Islands</option>
                                                                <option value="GMT+00:00">(GMT) Western Europe Time, London, Lisbon, Casablanca</option>
                                                                <option value="GMT+01:00">(GMT +1:00) Brussels, Copenhagen, Madrid, Paris</option>
                                                                <option value="GMT+02:00">(GMT +2:00) Kaliningrad, South Africa</option>
                                                                <option value="GMT+03:00">(GMT +3:00) Baghdad, Riyadh, Moscow, St. Petersburg</option>
                                                                <option value="GMT+03:30">(GMT +3:30) Tehran</option>
                                                                <option value="GMT+04:00">(GMT +4:00) Abu Dhabi, Muscat, Baku, Tbilisi</option>
                                                                <option value="GMT+04:30">(GMT +4:30) Kabul</option>
                                                                <option value="GMT+05:00">(GMT +5:00) Ekaterinburg, Islamabad, Karachi, Tashkent</option>
                                                                <option value="GMT+05:30">(GMT +5:30) Bombay, Calcutta, Madras, New Delhi</option>
                                                                <option value="GMT+05:45">(GMT +5:45) Kathmandu, Pokhara</option>
                                                                <option value="GMT+06:00">(GMT +6:00) Almaty, Dhaka, Colombo</option>
                                                                <option value="GMT+06:30">(GMT +6:30) Yangon, Mandalay</option>
                                                                <option value="GMT+07:00">(GMT +7:00) Bangkok, Hanoi, Jakarta</option>
                                                                <option value="GMT+08:00">(GMT +8:00) Beijing, Perth, Singapore, Hong Kong</option>
                                                                <option value="GMT+08:45">(GMT +8:45) Eucla</option>
                                                                <option value="GMT+09:00">(GMT +9:00) Tokyo, Seoul, Osaka, Sapporo, Yakutsk</option>
                                                                <option value="GMT+09:30">(GMT +9:30) Adelaide, Darwin</option>
                                                                <option value="GMT+10:00">(GMT +10:00) Eastern Australia, Guam, Vladivostok</option>
                                                                <option value="GMT+10:30">(GMT +10:30) Lord Howe Island</option>
                                                                <option value="GMT+11:00">(GMT +11:00) Magadan, Solomon Islands, New Caledonia</option>
                                                                <option value="GMT+11:30">(GMT +11:30) Norfolk Island</option>
                                                                <option value="GMT+12:00">(GMT +12:00) Auckland, Wellington, Fiji, Kamchatka</option>
                                                                <option value="GMT+12:45">(GMT +12:45) Chatham Islands</option>
                                                                <option value="GMT+13:00">(GMT +13:00) Apia, Nukualofa</option>
                                                                <option value="GMT+14:00">(GMT +14:00) Line Islands, Tokelau</option>
                                                            </select>
                                                            <button type="button" id="edit-gmt">Edit</button>
                                                        </div>
                                                        <input type="hidden" name="gmt_hidden" id="gmt_hidden" value="<?php echo $gmt_por_defecto; ?>">
                                                    </div>
                                                    <br>

                                                    <label for="event_type_main" style="margin-bottom: 5px; color: dimgrey;">*Select Event Type:</label>
                                                    <select id="event_type_main" name="event_type_main" class="form-control" required>
                                                        <option value="">-- Select --</option>
                                                        <option value="language">Language event</option>
                                                        <option value="multi">Multilanguage or other event type</option>
                                                    </select>
                                                    <br>

                                                    <div id="language_event_block" style="display:none;">
                                                        <label for="language">*Language Event Type:</label>
                                                        <select id="language" name="language" class="form-control">
                                                            <?php while ($row = mysqli_fetch_assoc($result_eventtypes)): ?>
                                                                <option value="<?= $row['eventtypecode'] ?>"><?= $row['eventtypename'] ?></option>
                                                            <?php endwhile; ?>
                                                        </select>
                                                        <br>
                                                    </div>

                                                    <div id="multi_event_block" style="display:none;">
                                                        <label for="language_search" style="margin-bottom: 5px; color: dimgrey;">*Search Language:</label>
                                                        <input type="text" id="language_search" name="language_search" class="form-control" placeholder="Type a language...">
                                                        <input type="hidden" id="language_code" name="language_code">
                                                        <div id="code_display" style="margin-top: 5px; color: green;"></div>
                                                        <br>
                                                    </div>

                                                    <?php
                                                    // Buscar ciudad más cercana
                                                    $latitud1 = $lat11;
                                                    $longitud1 = $lng11;

                                                    $query = "SELECT gc.city_ascii, gc.country,
                                                          (acos(sin(radians(gc.lat)) * sin(radians($latitud1)) + 
                                                          cos(radians(gc.lat)) * cos(radians($latitud1)) * 
                                                          cos(radians(gc.lng) - radians($longitud1))) * 6378) AS distanciaPunto1Punto2
                                                          FROM gpscities gc
                                                          WHERE 1
                                                          ORDER BY distanciaPunto1Punto2 
                                                          LIMIT 1";

                                                    $result = mysqli_query($link, $query);
                                                    if (!mysqli_num_rows($result)) {
                                                        echo "</br>Error 506. Contact webmaster.";
                                                    }
                                                    $fila = mysqli_fetch_array($result);
                                                    $city88 = $fila['city_ascii'];
                                                    $country88 = $fila['country'];
                                                    ?>

                                                    <div id="city_block">
                                                        <label for="city" style="margin-bottom:5px;color:dimgrey;">*Nearest city (approximately):</label>
                                                        <input type="text" name="city" id="city" class="form-control" style="background-color:white; margin-bottom:5px;" value="<?php echo $city88; ?>" readonly />
                                                        <br>
                                                    </div>

                                                    <div class="form-group text-right">
                                                        <span>Event Price: </span>
                                                        <span id="precio_display" style="color:#e65f00; font-weight:bold; font-size:1.2em;">0</span>
                                                        <span style="color:#e65f00; font-weight:bold; font-size:1.2em;">€</span>
                                                    </div>

                                                    <div id="address_block">
                                                        <a href="../user/getgpsposition.php" style="font-size: 70%;">Not your city? Update your location</a>
                                                        <br><br>
                                                        <?php
                                                        // Buscar locales cercanos
                                                        $query = "SELECT lc.id_local, lc.full_address_google, lc.country_google, lc.city_google, lc.name_local_google,
                                                          (acos(sin(radians(lc.lat)) * sin(radians($latitud1)) + 
                                                          cos(radians(lc.lat)) * cos(radians($latitud1)) * 
                                                          cos(radians(lc.lng) - radians($longitud1))) * 6378) AS distanciaPunto1Punto2
                                                          FROM locales lc
                                                          HAVING distanciaPunto1Punto2 < 20
                                                          ORDER BY distanciaPunto1Punto2 ASC
                                                          LIMIT 1000";

                                                        $result = mysqli_query($link, $query);
                                                        $num_rows_locals = mysqli_num_rows($result);

                                                        if ($num_rows_locals) {
                                                        ?>
                                                            <label for="id_local_event" style="margin-bottom:5px;color:dimgrey;">*Full Address of the event</label>
                                                            <select name="id_local_event" class="form-control" style="appearance:listbox">
                                                                <?php
                                                                while ($fila = mysqli_fetch_array($result)) {
                                                                    $local_id = $fila['id_local'];
                                                                    $full_addr = $fila['full_address_google'];
                                                                    $dist = number_format($fila['distanciaPunto1Punto2'], 2);
                                                                    $name_local = $fila['name_local_google'];
                                                                ?>
                                                                    <option value="<?php echo $local_id; ?>">
                                                                        <?php echo $dist . " Km - " . $name_local . " - " . $full_addr; ?>
                                                                    </option>
                                                                <?php } ?>
                                                            </select>
                                                        <?php } else { ?>
                                                            <label for="event_address" style="margin-bottom:5px;color:dimgrey;">*Full Address of the event</label>
                                                            <textarea rows="12" class="form-control" name="event_address" id="event_address" required></textarea>
                                                        <?php } ?>
                                                    </div>

                                                    <input type="hidden" name="country99" id="country99" maxlength="45" value="<?php echo $country88; ?>" />
                                                    <br><br>

                                                    <button type="submit" name="enviar" value="Create event" style="background-color: #e65f00; border: none; color: white; padding: 5px 15px; text-align: center; border-radius: 10px;">
                                                        Create new event
                                                    </button>
                                                </div>
                                            </form>

                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- SCRIPTS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js" integrity="sha256-lSjKY0/srUM9BE3dPm+c4fBo1dky2v27Gdjm2uoZaL0=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="../public/js/jquery.mCustomScrollbar.js"></script>
    <script type="text/javascript" src="../public/lib/slick/slick.min.js"></script>
    <script type="text/javascript" src="../public/js/scrollbar.js"></script>
    <script type="text/javascript" src="../public/js/script.js"></script>

    <!-- Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-139626327-1"></script>
    <script>
        window.dataLayer = window.dataLayer || [];

        function gtag() {
            dataLayer.push(arguments);
        }
        gtag('js', new Date());
        gtag('config', 'UA-139626327-1');
    </script>

    <!-- DETECCIÓN DE HUSO HORARIO CON RECARGA AUTOMÁTICA -->
    <script>
        (function() {
            try {
                var husoActual = Intl.DateTimeFormat().resolvedOptions().timeZone;

                // Función para leer cookie
                function getCookie(name) {
                    var value = "; " + document.cookie;
                    var parts = value.split("; " + name + "=");
                    if (parts.length == 2) return parts.pop().split(";").shift();
                }

                var husoAnterior = getCookie("huso_usuario");

                // Si no hay cookie o es diferente, guardar y recargar UNA SOLA VEZ
                if (!husoAnterior) {
                    document.cookie = "huso_usuario=" + husoActual + "; path=/; max-age=86400";
                    console.log("Huso guardado:", husoActual);
                } else if (husoAnterior !== husoActual) {
                    document.cookie = "huso_usuario=" + husoActual + "; path=/; max-age=86400";
                    console.log("Huso cambiado de", husoAnterior, "a", husoActual, "- Recargando...");
                    location.reload(); // ← RECARGA AUTOMÁTICA (solo una vez)
                } else {
                    console.log("Huso detectado:", husoActual, "(sin cambios)");
                }
            } catch (e) {
                console.log("No se pudo detectar huso:", e);
            }
        })();
    </script>

    <!-- SCRIPT PRINCIPAL -->
    <script>
        $(document).ready(function() {
            console.log("=== INICIALIZACIÓN CREATE EVENT ===");

            // Verificar jQuery UI
            if (typeof $.ui === 'undefined' || typeof $.fn.autocomplete === 'undefined') {
                console.error("ERROR: jQuery UI no está cargado");
                alert("Error: jQuery UI no se cargó. Por favor, recarga la página.");
            } else {
                console.log("✓ jQuery UI cargado (versión: " + $.ui.version + ")");

                // AUTOCOMPLETE
                $("#language_search").autocomplete({
                    source: function(request, response) {
                        $.ajax({
                            url: "search_languages.php",
                            dataType: "json",
                            data: {
                                term: request.term
                            },
                            success: function(data) {
                                response(data);
                            },
                            error: function() {
                                response([]);
                            }
                        });
                    },
                    minLength: 0,
                    select: function(event, ui) {
                        $("#language_search").val(ui.item.label);
                        $("#language_code").val(ui.item.code);
                        $("#code_display").html('<i class="fas fa-check-circle"></i> Selected: <strong>' + ui.item.code.toUpperCase() + '</strong>');
                        return false;
                    },
                    change: function(event, ui) {
                        if (!ui.item) {
                            $("#language_code").val('');
                            $("#code_display").text('');
                        }
                    }
                });
            }

            // Mostrar/Ocultar bloques según tipo de evento
            $('#event_type_main').on('change', function() {
                var val = $(this).val();
                if (val === 'language') {
                    $('#language_event_block').hide();
                    $('#multi_event_block').show();
                    $('#language').prop('required', false);
                    $('#language_search').prop('required', true);
                } else if (val === 'multi') {
                    $('#language_event_block').show();
                    $('#multi_event_block').hide();
                    $('#language').prop('required', true);
                    $('#language_search').prop('required', false);
                }
            });

            // CONFIGURACIÓN GMT
            var gmtPorDefecto = "<?php echo $gmt_por_defecto; ?>";

            // Seleccionar GMT por defecto
            if (gmtPorDefecto) {
                $('#gmt option').each(function() {
                    if ($(this).val() === gmtPorDefecto) {
                        $(this).prop('selected', true);
                        $('#gmt_hidden').val(gmtPorDefecto);
                    }
                });
            }

            // Botón Edit/Save
            $('#edit-gmt').on('click', function() {
                var $gmtSelect = $('#gmt');
                var $editButton = $(this);

                if ($gmtSelect.prop('disabled')) {
                    $gmtSelect.prop('disabled', false).css('background-color', 'white');
                    $editButton.text('Save').addClass('save-mode');

                    setTimeout(function() {
                        $gmtSelect.focus();
                        if (typeof $gmtSelect[0].showPicker === 'function') {
                            $gmtSelect[0].showPicker();
                        } else {
                            $gmtSelect.prop('size', 8);
                        }
                    }, 100);
                } else {
                    $gmtSelect.prop('disabled', true).css('background-color', '#f5f5f5').prop('size', null);
                    $editButton.text('Edit').removeClass('save-mode');
                    $('#gmt_hidden').val($gmtSelect.val());
                    alert('GMT saved: ' + $gmtSelect.val());
                }
            });

            // Sincronizar campo hidden
            $('#gmt').on('change', function() {
                if (!$('#gmt').prop('disabled')) {
                    $('#gmt_hidden').val($(this).val());
                }
            });

            // Inicializar
            calcPrecio();
            toggleLocationFields();
            console.log("=== INICIALIZACIÓN COMPLETADA ===\n");
        });

        // Funciones auxiliares
        function calcPrecio() {
            var tipoRadio = document.querySelector('input[name="tipo_evento"]:checked');
            var minutos = document.getElementById('event_length');
            var display = document.getElementById('precio_display');

            if (tipoRadio && minutos && display) {
                if (tipoRadio.value === "online") {
                    var mins = parseFloat(minutos.value) || 0;
                    display.innerText = mins > 0 ? (mins * 0.05).toFixed(2) : "0.00";
                } else {
                    display.innerText = "0.00";
                }
            }
        }

        function corregirDuracion() {
            var input = document.getElementById('event_length');
            var duracion = parseInt(input.value);

            if (isNaN(duracion) || duracion < 30) {
                var anterior = input.value || 'vacío';
                input.value = 30;
                alert('⚠️ Duration corrected from ' + anterior + ' to 30 minutes');
            }
        }

        function validate() {
            var eventType = document.getElementById('event_type_main').value;
            if (!eventType) {
                alert('Please select an event type');
                return false;
            }

            if (eventType === 'language') {
                if (!document.getElementById('language').value) {
                    alert('Please select a language');
                    return false;
                }
            } else if (eventType === 'multi') {
                if (!document.getElementById('language_code').value) {
                    alert('Please search and select a language');
                    return false;
                }
            }

            var duracion = document.getElementById('event_length').value;
            if (duracion < 30) {
                alert('Event duration cannot be less than 30 minutes');
                document.getElementById('event_length').value = 30;
                document.getElementById('event_length').focus();
                return false;
            }

            return true;
        }

        function toggleLocationFields() {
            var online = document.querySelector('input[name="tipo_evento"][value="online"]').checked;
            var cityBlock = document.getElementById('city_block');
            var addressBlock = document.getElementById('address_block');

            if (online) {
                cityBlock.style.display = 'none';
                addressBlock.style.display = 'none';
                document.getElementById('city')?.removeAttribute('required');
                document.getElementById('event_address')?.removeAttribute('required');
            } else {
                cityBlock.style.display = 'block';
                addressBlock.style.display = 'block';
                document.getElementById('event_address')?.setAttribute('required', 'required');
            }
            calcPrecio();
        }
    </script>

</body>

</html>