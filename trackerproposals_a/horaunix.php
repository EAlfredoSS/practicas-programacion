<?php

function obtenerFechaHora($tiempoUnix, $zonaHoraria) {
    try {
        // Crear un objeto DateTime con el tiempo Unix en UTC0
        $fechaHora = new DateTime("@$tiempoUnix");
        $fechaHora->setTimezone(new DateTimeZone($zonaHoraria)); // Ajustar a la zona horaria
        
        // Formatear la fecha y hora en el formato 'YYYY-MM-DD HH:MM'
        return $fechaHora->format('Y-m-d H:i');
    } catch (Exception $e) {
        // Si hay un error, devolver un mensaje de error
        return "Error: " . $e->getMessage();
    }
}

// Ejemplo de uso
$tiempoUnix = time(); // Hora Unix en UTC0 (ejemplo)
$zonaHoraria = 'America/Argentina/Buenos_Aires'; // Zona horaria de ejemplo

$fechaHoraFormateada = obtenerFechaHora($tiempoUnix, $zonaHoraria);
echo "La fecha y hora correspondiente a la hora Unix $tiempoUnix en la zona horaria $zonaHoraria es: $fechaHoraFormateada";

?>