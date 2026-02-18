<?php
require('../lingua2/files/bd.php');
session_start();

$huso_horario_actual = "Europe/Madrid";
date_default_timezone_set($huso_horario_actual);
$hora_actual_sistema = date('H:i:s');

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
    $gmt_evento = $_POST['gmt'];
    $country99 = $_POST['country99'];
    $city_evento = strip_tags($_POST['city']);
    $tipo_evento = $_POST['tipo_evento']; // online o on-site
    $duracion_minutos = intval($_POST['event_minutes_length']);
    
    // Determinar si es online u on-site
    $online = ($tipo_evento === 'online') ? 1 : 0;
    
    // Calcular precio del evento
    if ($online == 1) {
        $horas = $duracion_minutos / 60;
        $event_price = $horas * 2; // 2€ por hora
    } else {
        $event_price = 0;
    }
    
    // Solo procesar dirección si es on-site
    if ($online == 0) {
        $full_address_evento = strip_tags($_POST['event_address'] ?? '');
        $id_local_num = $_POST['id_local_event'] ?? null;
        
        // Procesar local si existe
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
    } else {
        // Si es online, no necesitamos dirección
        $full_address_evento = "Online Event";
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
    if ($online == 0 && empty($city_evento)) die("Event city field cannot be empty. Go back to the form.");
    if ($online == 0 && empty($full_address_evento)) die("Full address field cannot be empty. Go back to the form.");

    // 9. Verificar que no sea fecha pasada
    $tiempo_corte5 = time() - 24 * 3600;
    if ($unix_time_evento < $tiempo_corte5) {
        die("The event cannot be established in a past date. Go back to the form and select a future date.");
    }

    // 10. Insertar en base de datos
    if ($local_num == -1) {
        $query = "INSERT INTO eventoslista (id_creador, Idioma, event_name, event_desc, unix_start_time, start_time, city, location, country, Codigoevento, id_local, online, event_price, event_minutes_length)
                  VALUES('$identificador2017', '$idioma_evento', '$nombre_evento', '$descrip_evento', '$unix_time_evento', '$event_start_time', '$city_evento', '$full_address_evento', '$country99', '$codigoevento1', NULL, '$online', '$event_price', '$duracion_minutos')";
    } else {
        $query = "INSERT INTO eventoslista (id_creador, Idioma, event_name, event_desc, unix_start_time, start_time, city, location, country, Codigoevento, id_local, online, event_price, event_minutes_length)
                  VALUES('$identificador2017', '$idioma_evento', '$nombre_evento', '$descrip_evento', '$unix_time_evento', '$event_start_time', '$city_evento', '$full_address_evento', '$country99', '$codigoevento1', '$local_num', '$online', '$event_price', '$duracion_minutos')";
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

    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>
    <script src="https://kit.fontawesome.com/bb6243346a.js" crossorigin="anonymous"></script>

    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-139626327-1"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag() { dataLayer.push(arguments); }
        gtag('js', new Date());
        gtag('config', 'UA-139626327-1');
    </script>

    <link rel="stylesheet" href="../user/css/languages.css" media="all" />
    <link rel="stylesheet" type="text/css" href="../public/css/animate.css">
    <link rel="stylesheet" type="text/css" href="../public/css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="../public/css/jquery.mCustomScrollbar.min.css">
    <link rel="stylesheet" type="text/css" href="../public/lib/slick/slick.css">
    <link rel="stylesheet" type="text/css" href="../public/css/style.css">
    <link rel="stylesheet" type="text/css" href="../public/css/responsive.css">
    
    <style>
        a { color: #e65f00; }
        .ui-autocomplete { z-index: 9999 !important; }
        
        /* Estilo del cuadro naranja */
        .huso-box {
            background: linear-gradient(135deg, #f5dcc4 0%, #e8bd92 100%);
            border-left: 5px solid #e65f00;
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            box-shadow: 0 2px 8px rgba(230, 95, 0, 0.1);
        }
        
        .huso-box i {
            color: #e65f00;
            margin-right: 8px;
        }
        
        .huso-box b {
            color: #333;
        }
    </style>

    <script type="text/javascript" src="../public/js/jquery.mCustomScrollbar.js"></script>
    <script type="text/javascript" src="../public/lib/slick/slick.min.js"></script>
    <script type="text/javascript" src="../public/js/scrollbar.js"></script>
    <script type="text/javascript" src="../public/js/script.js"></script>
</head>

<body>

<?php 
// Cargar header
$header_path = __DIR__ . "/../templates/header_simplified.html";
if (file_exists($header_path)) {
    require_once($header_path);
}
?>

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
                                            Huso Horario: <b><?php echo $huso_horario_actual; ?></b> |
                                            Hora actual: <b><?php echo $hora_actual_sistema; ?></b>
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

                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label><b>Event Type:</b></label>
                                                            <select name="tipo_evento" id="tipo_evento" class="form-control" onchange="toggleEventType()">
                                                                <option value="on-site">On-site (Presencial)</option>
                                                                <option value="online">Online</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label><b>Event Length (min):</b></label>
                                                            <input type="number" id="event_length" name="event_minutes_length" class="form-control" value="90" min="1" oninput="calcPrecio()">
                                                        </div>
                                                    </div>
                                                </div>

                                                <label for="start_date" style="margin-bottom:5px;color:dimgrey;">*Start Date:</label>
                                                <input type="date" class="form-control" name="start_date" id="start_date" required />
                                                <br>

                                                <label for="start_hour" style="margin-bottom:5px;color:dimgrey;">*Select Hour:</label>
                                                <select name="start_hour" id="start_hour" class="form-control" required>
                                                    <option value="">Select Hour</option>
                                                    <?php for ($h = 0; $h < 24; $h++): ?>
                                                        <option value="<?= str_pad($h, 2, '0', STR_PAD_LEFT) ?>"><?= str_pad($h, 2, '0', STR_PAD_LEFT) ?></option>
                                                    <?php endfor; ?>
                                                </select>
                                                <br>

                                                <label for="start_minute" style="margin-bottom:5px;color:dimgrey;">*Select Minute:</label>
                                                <select name="start_minute" id="start_minute" class="form-control" required>
                                                    <option value="">Select minute</option>
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
                                                <br>

                                                <label for="gmt" style="margin-bottom:5px;color:dimgrey;">*Select Time Zone</label>
                                                <select name="gmt" id="gmt" class="form-control" required>
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
                                                    <option value="GMT+01:00" selected="selected">(GMT +1:00) Brussels, Copenhagen, Madrid, Paris</option>
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
                                                </div>

                                                <div id="multi_event_block" style="display:none;">
                                                    <label for="language_search" style="margin-bottom: 5px; color: dimgrey;">*Search Language:</label>
                                                    <input type="text" id="language_search" name="language_search" class="form-control" placeholder="Type a language...">
                                                    <input type="hidden" id="language_code" name="language_code">
                                                    <div id="code_display" style="margin-top: 5px; color: green;"></div>
                                                </div>
                                                <br>

                                                <div id="location_fields">
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

                                                    <label for="city" style="margin-bottom:5px;color:dimgrey;">*Nearest city (approximately):</label>
                                                    <input type="text" name="city" id="city" class="form-control" style="background-color:white; margin-bottom:5px;" value="<?php echo $city88; ?>" readonly />
                                                    <br>

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
                                                        <select name="id_local_event" id="id_local_event" class="form-control" style="appearance:listbox">
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
                                                        <textarea rows="12" class="form-control" name="event_address" id="event_address"></textarea>
                                                    <?php } ?>
                                                </div>

                                                <input type="hidden" name="country99" id="country99" maxlength="45" value="<?php echo $country88; ?>" />
                                                <br>

                                                <div class="form-group text-right">
                                                    <span>Event Price: </span>
                                                    <span id="precio_display" style="color:#e65f00; font-weight:bold; font-size:1.2em;">0.00</span>
                                                    <span style="color:#e65f00; font-weight:bold; font-size:1.2em;">€</span>
                                                </div>
                                                <br>

                                                <button type="submit" name="enviar" value="Create event" style="background-color: #e65f00; border: none; color: white; padding: 10px 11px; text-align: center; border-radius: 10px;">
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

<script>
$(function() {
    // Autocomplete para idiomas
    $("#language_search").autocomplete({
        source: "search_languages.php", 
        minLength: 2,
        select: function(event, ui) {
            $("#language_search").val(ui.item.label);
            $("#language_code").val(ui.item.code);
            $("#code_display").text("Selected: " + ui.item.code.toUpperCase());
            return false;
        }
    });

    // Mostrar/Ocultar bloques según tipo de evento
    $('#event_type_main').on('change', function() {
        const val = $(this).val();
        if (val === 'language') {
            $('#language_event_block').show();
            $('#multi_event_block').hide();
            $('#language').prop('required', true);
            $('#language_search').prop('required', false);
            $('#language_code').val('');
        } else if (val === 'multi') {
            $('#language_event_block').hide();
            $('#multi_event_block').show();
            $('#language').prop('required', false);
            $('#language_search').prop('required', true);
            $('#language').val('');
        } else {
            $('#language_event_block, #multi_event_block').hide();
            $('#language').prop('required', false);
            $('#language_search').prop('required', false);
        }
    });
});

// Función para mostrar/ocultar campos según online/on-site
function toggleEventType() {
    var tipo = document.getElementById('tipo_evento').value;
    var locationFields = document.getElementById('location_fields');
    var city = document.getElementById('city');
    var eventAddress = document.getElementById('event_address');
    var localEvent = document.getElementById('id_local_event');
    
    if (tipo === 'online') {
        // Ocultar campos de ubicación
        locationFields.style.display = 'none';
        if (city) city.removeAttribute('required');
        if (eventAddress) eventAddress.removeAttribute('required');
        if (localEvent) localEvent.removeAttribute('required');
    } else {
        // Mostrar campos de ubicación
        locationFields.style.display = 'block';
        if (city) city.setAttribute('required', 'required');
        if (eventAddress) eventAddress.setAttribute('required', 'required');
    }
    
    // Recalcular precio
    calcPrecio();
}

// Cálculo de precio
function calcPrecio() {
    var tipo = document.getElementById('tipo_evento').value;
    var minutos = parseFloat(document.getElementById('event_length').value) || 0;
    var display = document.getElementById('precio_display');

    if (display && tipo === "online" && minutos > 0) {
        var horas = minutos / 60;
        var calculo = horas * 2; // 2€ por hora
        display.innerText = calculo.toFixed(2);
    } else if (display) {
        display.innerText = "0.00";
    }
}

// Validación del formulario
function validate() {
    var eventType = document.getElementById('event_type_main').value;
    var tipoEvento = document.getElementById('tipo_evento').value;
    
    if (!eventType) {
        alert('Please select an event type');
        return false;
    }
    
    if (eventType === 'language') {
        var lang = document.getElementById('language').value;
        if (!lang) {
            alert('Please select a language');
            return false;
        }
    } else {
        var langCode = document.getElementById('language_code').value;
        if (!langCode) {
            alert('Please search and select a language');
            return false;
        }
    }
    
    // Validar dirección solo si es on-site
    if (tipoEvento === 'on-site') {
        var eventAddress = document.getElementById('event_address');
        if (eventAddress && !eventAddress.value.trim()) {
            alert('Please provide the event address');
            return false;
        }
    }
    
    return true;
}
</script>

</body>
</html>