<?php
// mi código
// require('../files/bd.php'); 

session_start();
$identificador2017 = isset($_SESSION['orden2017']) ? $_SESSION['orden2017'] : 'Prueba_User';

// --- PUNTO E: Datos de Huso Horario ---
$huso_horario_actual = "Europe/Madrid";
date_default_timezone_set($huso_horario_actual);
$hora_actual_sistema = date('H:i:s');

// --- LÓGICA PARA PROCESAR EL FORMULARIO (PUNTOS B, C, D, E) ---
if (isset($_POST['enviar'])) {
    $timestamp_evento = $_POST['timeunix_start'];
    $nombre_evento = $_POST['event_name'];
    
    // // ********** PUNTO B: RECOGIDA DE VALOR ONLINE **********
    $es_online = $_POST['online']; // '1' para Online, '0' para On-site
    
    // // ********** PUNTO B: CÁLCULO DE PRECIO FINAL (PHP) **********
    $minutos = $_POST['event_minutes_length'];
    $event_price = 0;
    if ($es_online == '1') {
        // 2€ por cada 60 minutos (proporcional)
        $event_price = ($minutos / 60) * 2;
    }
    // ************************************************************

    echo "<script>alert('Evento: $nombre_evento \\nModo Online: $es_online \\nPrecio Final: $event_price €');</script>";
}
?>

<head>
    <script src="https://kit.fontawesome.com/bb6243346a.js" crossorigin="anonymous"></script>
    <title>Create Event | Lingua2</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
    <style>
        .huso-box { background-color: #ecccb1; border-left: 5px solid #e65f00; padding: 15px; margin-bottom: 20px; border-radius: 4px; }
        .precio-tag { color: #e65f00; font-weight: bold; font-size: 1.2em; }
        
        /* // ********** PUNTO B: ESTILO PARA OCULTAR ********** */
        .hidden-field { display: none; }
    </style>
</head>

<body>
    <div class="main-section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-7">
                    <div class="main-ws-sec">
                        <h1 style="color:dimgrey;font-size: 40px;">Create Event</h1>
                        <hr>

                        <div class="huso-box">
                            Huso Horario: <b><?php echo $huso_horario_actual; ?></b> | Hora: <b><?php echo $hora_actual_sistema; ?></b>
                        </div>

                        <div class="post-bar p-4" style="background: white; border-radius: 8px; border: 1px solid #ddd;">
                            <form name="formevent" METHOD="POST" ACTION="#">

                                <div class="form-group">
                                    <label><b>*Event Modality:</b></label><br>
                                    <div class="custom-control custom-radio custom-control-inline">
                                        <input type="radio" id="radio_onsite" name="online" value="0" class="custom-control-input" checked onclick="toggleModality(false)">
                                        <label class="custom-control-label" for="radio_onsite">On-site [x]</label>
                                    </div>
                                    <div class="custom-control custom-radio custom-control-inline">
                                        <input type="radio" id="radio_online" name="online" value="1" class="custom-control-input" onclick="toggleModality(true)">
                                        <label class="custom-control-label" for="radio_online">Online [ ]</label>
                                    </div>
                                    <small id="info-coste" class="form-text text-muted hidden-field">El coste de creación es de 2€ por hora.</small>
                                </div>

                                <div class="form-group">
                                    <label><b>*Event Name:</b></label>
                                    <input type="text" class="form-control" name="event_name" required>
                                </div>

                                <div class="form-group">
                                    <label><b>*Event Length (min):</b></label>
                                    <input type="number" id="event_length" name="event_minutes_length" class="form-control" value="90" min="1" oninput="calcPrecio()">
                                </div>

                                <div class="form-group text-right">
                                    <span>Event Price: </span><span id="precio_display" class="precio-tag">0</span><span class="precio-tag">€</span>
                                </div>

                                <div id="campos_presenciales">
                                    <div class="form-group">
                                        <label><b>*Nearest City:</b></label>
                                        <input type="text" class="form-control" name="event_city">
                                    </div>
                                    <div class="form-group">
                                        <label><b>*Full Address:</b></label>
                                        <textarea rows="2" class="form-control" name="event_address"></textarea>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label><b>*Start Date and Time (Punto D):</b></label>
                                    <input type="datetime-local" id="fecha_input" class="form-control" required onchange="calcUnix()">
                                    <input type="hidden" name="timeunix_start" id="timeunix_start">
                                </div>

                                <button type="submit" name="enviar" class="btn btn-block" style="background-color: #e65f00; color: white; font-weight: bold; padding: 12px;">
                                    CREATE NEW EVENT
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function calcUnix() {
            var val = document.getElementById('fecha_input').value;
            if (val) {
                var unix = Math.floor(new Date(val).getTime() / 1000);
                document.getElementById('timeunix_start').value = unix;
            }
        }

        // // ********** PUNTO B: LÓGICA MOSTRAR/OCULTAR Y PRECIO **********
        function toggleModality(isOnline) {
            var campos = document.getElementById('campos_presenciales');
            var infoCoste = document.getElementById('info-coste');
            
            if (isOnline) {
                campos.classList.add('hidden-field');
                infoCoste.classList.remove('hidden-field');
            } else {
                campos.classList.remove('hidden-field');
                infoCoste.classList.add('hidden-field');
            }
            calcPrecio();
        }

        function calcPrecio() {
            var isOnline = document.getElementById('radio_online').checked;
            var minutos = document.getElementById('event_length').value;
            var display = document.getElementById('precio_display');

            if (isOnline) {
                // REGLA: 2€ por cada 60 minutos -> (minutos / 60) * 2
                var precio = (minutos / 60) * 2;
                display.innerText = precio.toFixed(2);
            } else {
                display.innerText = "0";
            }
        }

        window.onload = calcPrecio;
    </script>
</body>