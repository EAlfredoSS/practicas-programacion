<?php
// Obtener todos los husos horarios disponibles
$timezones = DateTimeZone::listIdentifiers();

// Organizar los husos horarios por continente
$timezonesByContinent = [];

foreach ($timezones as $timezone) {
    $parts = explode('/', $timezone);
    if (count($parts) == 2) {
        $continente = $parts[0];
        $ciudad = $parts[1];

        // Agrupar los husos horarios por continente
        if (!isset($timezonesByContinent[$continente])) {
            $timezonesByContinent[$continente] = [];
        }
        $timezonesByContinent[$continente][] = $timezone;
    }
}

// Si hay una solicitud para obtener la hora, procesarla
if (isset($_GET['timezone'])) {
    $timezone = $_GET['timezone'];
    date_default_timezone_set($timezone);
    echo date('H:i:s');
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seleccionar Huso Horario</title>
    <script>
        // Lista de husos horarios agrupados por continente
        const timezonesByContinent = <?php echo json_encode($timezonesByContinent); ?>;

        // Actualizar el select de continentes
        function actualizarContinentes() {
            const continenteSelect = document.getElementById('continente');
            continenteSelect.innerHTML = '<option value="">Seleccione un continente</option>';
            for (const continente in timezonesByContinent) {
                const option = document.createElement('option');
                option.value = continente;
                option.textContent = continente;
                continenteSelect.appendChild(option);
            }
        }

        // Actualizar las ciudades según el continente seleccionado
        function actualizarCiudades() {
            const continente = document.getElementById('continente').value;
            const ciudadSelect = document.getElementById('ciudad');
            ciudadSelect.innerHTML = '';  // Limpiar ciudades previas

            if (continente && timezonesByContinent[continente]) {
                for (const ciudad of timezonesByContinent[continente]) {
                    const option = document.createElement('option');
                    option.value = ciudad;
                    option.textContent = ciudad.replace(/_/g, " ");
                    ciudadSelect.appendChild(option);
                }
            }
        }

        // Mostrar la hora en el huso horario seleccionado
        function mostrarHora() {
            const continente = document.getElementById('continente').value;
            const ciudad = document.getElementById('ciudad').value;
            if (continente && ciudad) {
                fetch(`?timezone=${ciudad}`)
                    .then(response => response.text())
                    .then(data => {
                        document.getElementById('hora').textContent = `Hora en ${ciudad.replace(/_/g, " ")}: ${data}`;
                    })
                    .catch(error => console.error('Error al obtener la hora:', error));
            }
        }

        // Cargar los continentes cuando se cargue la página
        window.onload = actualizarContinentes;
    </script>
</head>
<body>
    <h1>Selecciona un Huso Horario</h1>
    
    <label for="continente">Continente:</label>
    <select id="continente" onchange="actualizarCiudades()">
        <option value="">Seleccione un continente</option>
    </select>
    
    <br><br>
    
    <label for="ciudad">Ciudad:</label>
    <select id="ciudad" onchange="mostrarHora()">
        <option value="">Seleccione una ciudad</option>
    </select>
    
    <p id="hora"></p>
</body>
</html>