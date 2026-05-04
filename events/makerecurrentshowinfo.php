<?php
require('../files/bd.php');
session_start();
$identificador2017 = $_SESSION['orden2017'];

$id_evento = $_GET['idev'];

if (!isset($identificador2017)) {
    die("You must be logged in in order to see this page");
}

$query = "SELECT * FROM eventoslista WHERE Id='$id_evento'";
$result = mysqli_query($link, $query);
if (!mysqli_num_rows($result)) {
    die('<br/>error event!!!<br/>');
}

$fila = mysqli_fetch_array($result);
$id_creador = $fila['id_creador'];

require('../files/idiomasequivalencias.php');
$lengua1 = $idiomas_equiv["{$fila['Idioma']}"];
$lengua1 = substr($lengua1, 0, 14);

$nombre_ev = $fila['event_name'];
$descr_ev = $fila['event_desc'];
$hora_inicio_gmt = $fila['start_time'];
$hora_inicio_unix = $fila['unix_start_time'];
$ciudad_ev = $fila['city'];
$location_ev = $fila['location'];
$country_ev1 = $fila['country'];
$broadc = $fila['Broadcasted'];
$es_replica = $fila['Createdfromid'];
$codigo_evento1 = $fila['Codigoevento'];
$identificador_creador_evento = $fila['id_creador'];

if ($identificador_creador_evento != $identificador2017)
    die("You only can make your own events recurrent, not the events created by other users.");

if (!is_null($es_replica))
    die('This event belongs to a serie of events. You can only make an event recurrent from the original one.');

$query2 = "SELECT * FROM eventoslista WHERE Createdfromid='$id_evento'";
$result2 = mysqli_query($link, $query2);
if (mysqli_num_rows($result2)) {
    die('<br/>Error: This event has already been made recurrent.<br/>');
}

// cálculo de fechas
$zona_gmt_array = explode('GMT', $hora_inicio_gmt);
$zona_gmt = $zona_gmt_array[1];
$zona_gmt_h_m_array = explode(':', $zona_gmt);
$zona_gmt_horas = $zona_gmt_h_m_array[0];
$zona_gmt_minutos = $zona_gmt_h_m_array[1];

if ($zona_gmt_horas >= 0) {
    $dif_segundos_gmt = $zona_gmt_horas * 3600 + $zona_gmt_minutos * 60;
} else {
    $dif_segundos_gmt = $zona_gmt_horas * 3600 - $zona_gmt_minutos * 60;
}

$time_weekly = $hora_inicio_unix + $dif_segundos_gmt;

$fechas = [];
for ($j = 0; $j < 52; $j++) {
    $time_weekly += 7 * 24 * 3600;
    $time_weekly_formateada = strftime("%d %b %G %R (%a.)", $time_weekly);
    $fechas[] = $time_weekly_formateada . " GMT" . $zona_gmt;
}

// parámetros codificados
$id_evento_codif = $id_evento * 49891 - 49;
$codigo_evento1 = substr($codigo_evento1, 10, 20);
$codigo_evento1 = md5($codigo_evento1);
$codigo_evento1 = substr($codigo_evento1, 10, 20);

// cálculo del precio
$query5 = "SELECT country, gdp_percapita_2017 FROM gps_gdp_by_country WHERE country='$country_ev1'";
$result5 = mysqli_query($link, $query5);
if (!mysqli_num_rows($result5)) {
    $factor = 0.2;
} else {
    $fila_5 = mysqli_fetch_array($result5);
    $factor = $fila_5['gdp_percapita_2017'] / 105280;
}

$price_per_event = round(0.36 * $factor, 2);
$price_total = round($price_per_event * 52, 2);
$_SESSION['price'] = $price_total;
$_SESSION['title'] = "Weekly event";
$_SESSION['photo'] = "/calender-2389150_1280.png";
$_SESSION['rediok'] = "/events/makerecurrentexe.php?cod1=" . $id_evento_codif . "&cod2=" . $codigo_evento1;
$_SESSION['redifail'] = "/events/makerecurrentshowinfo.php?idev=" . $id_evento;

// encriptación
$itemid1 = 'event_make_weekly';
$itemname1 = "Event #$id_evento make weekly in $ciudad_ev";
$productname1 = "Event make weekly in $ciudad_ev";
$itemdescription1 = "Your event in $ciudad_ev will be made weekly for a year.";
$internalcodename1 = "$id_evento_codif|||||$codigo_evento1";
$amountprice1 = $price_total;

$key = 'Vm95YWNyZWFydW5hQ2w0dmVwNHJhNHBhc2FybG9zZTNudG9z';
$itemid1 = base64_encode(openssl_encrypt($itemid1, 'AES-256-CBC', $key));
$itemname1 = base64_encode(openssl_encrypt($itemname1, 'AES-256-CBC', $key));
$productname1 = base64_encode(openssl_encrypt($productname1, 'AES-256-CBC', $key));
$itemdescription1 = base64_encode(openssl_encrypt($itemdescription1, 'AES-256-CBC', $key));
$internalcodename1 = base64_encode(openssl_encrypt($internalcodename1, 'AES-256-CBC', $key));
$amountprice1 = base64_encode(openssl_encrypt($amountprice1, 'AES-256-CBC', $key));
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Make your event weekly</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-orange: #d35400;
            --accent-orange:  #e67e22;
            --light-orange:   #fdf2e9;
            --text-dark:      #2c3e50;
            --text-grey:      #7f8c8d;
            --bg-grey:        #f4f7f6;
            --border:         #e0e0e0;
        }
        * { margin:0; padding:0; box-sizing:border-box; }

        body {
            font-family: 'Roboto', sans-serif;
            background: var(--bg-grey);
            margin: 0;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        .main-wrapper {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .card {
            background: white;
            border-radius: 14px;
            box-shadow: 0 6px 24px rgba(0,0,0,0.09);
            width: 100%;
            max-width: 720px;
            border-top: 4px solid var(--primary-orange);
            overflow: hidden;
        }

        .card-header {
            padding: 16px 22px;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .header-icon {
            width: 22px; height: 22px;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }
        .header-icon i { font-size: 18px; color: var(--primary-orange); }
        .card-header h2 { font-size: 18px; font-weight: 700; color: var(--text-dark); }
        .card-header p  { font-size: 12px; color: var(--text-grey); margin-top: 2px; }

        .card-body { padding: 20px 22px; }

        .event-link {
            font-size: 15px;
            margin-bottom: 12px;
            color: var(--text-dark);
        }
        .event-link a {
            color: var(--primary-orange);
            text-decoration: none;
            font-weight: 700;
        }
        .event-link a:hover { text-decoration: underline; }

        .intro-text {
            font-size: 14px;
            color: var(--text-grey);
            margin-bottom: 18px;
        }

        .dates-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 6px;
            margin-bottom: 18px;
            max-height: 450px;
            overflow-y: auto;
            padding-right: 5px;
        }
        .date-item {
            background: #f8f9fa;
            border-radius: 6px;
            padding: 6px 10px;
            font-size: 11px;
            color: var(--text-dark);
            border: 1px solid var(--border);
        }

        .price-section {
            background: var(--light-orange);
            border-radius: 8px;
            padding: 14px 18px;
            margin-bottom: 16px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .price-label {
            font-size: 16px;
            font-weight: 700;
            color: var(--text-dark);
        }
        .price-amount {
            font-size: 30px;
            font-weight: 800;
            color: var(--primary-orange);
        }
        .price-amount small { font-size: 14px; margin-left: 2px; }

        .btn-pay {
            width: 100%;
            background: #d35400;
            border: 2px solid #d35400;
            color: white;
            padding: 12px 18px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 0.4px;
        }
        .btn-pay:hover {
            background: #fdf2e9;
            color: #d35400;
            transform: translateY(-2px);
            box-shadow: 0 4px 14px rgba(211,84,0,0.3);
        }
        .btn-pay i { font-size: 14px; transition: transform 0.3s; }
        .btn-pay:hover i { transform: scale(1.1); }

        .back-link {
            display: block;
            text-align: center;
            margin-top: 14px;
            font-size: 13px;
            color: var(--text-grey);
            text-decoration: none;
        }
        .back-link:hover { color: var(--primary-orange); }

        @media (max-width: 500px) {
            .dates-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>

<?php require("../templates/header_simplified.html"); ?>

<div class="main-wrapper">
    <div class="card">
        <div class="card-header">
            <div class="header-icon"><i class="fas fa-calendar-alt"></i></div>
            <div>
                <h2>Make your event recurrent weekly for one year</h2>
                <p>52 weeks &middot; one event per week</p>
            </div>
        </div>

        <div class="card-body">
            <div class="event-link">
                Event: <a href="./eventdetails.php?idev=<?php echo $id_evento; ?>"><?php echo htmlspecialchars($nombre_ev); ?></a>
            </div>

            <div class="intro-text">
                We will create a copy of your event for the following dates:
            </div>

            <div class="dates-grid">
                <?php
                $ind = 0;
                foreach ($fechas as $fecha) {
                    $ind++;
                    echo "<div class=\"date-item\">$ind. $fecha</div>";
                }
                ?>
            </div>

            <div class="price-section">
                <span class="price-label">Total price for 52 weekly events</span>
                <span class="price-amount"><?php echo number_format($price_total, 2); ?><small>€</small></span>
            </div>

            <form name="payment_event_weekly" action="../payments/index.php" enctype="multipart/form-data" method="POST">
                <input type="hidden" name="itemid"          value="<?php echo $itemid1; ?>">
                <input type="hidden" name="itemname"         value="<?php echo $itemname1; ?>">
                <input type="hidden" name="productname"      value="<?php echo $productname1; ?>">
                <input type="hidden" name="itemdescription"  value="<?php echo $itemdescription1; ?>">
                <input type="hidden" name="internalcodename" value="<?php echo $internalcodename1; ?>">
                <input type="hidden" name="amountprice"      value="<?php echo $amountprice1; ?>">
                <button type="submit" class="btn-pay">
                    <i class="fas fa-lock"></i> Continue to payment
                </button>
            </form>

            <a href="javascript:history.back()" class="back-link">
                <i class="fas fa-arrow-left"></i> Go back
            </a>
        </div>
    </div>
</div>

</body>
</html>