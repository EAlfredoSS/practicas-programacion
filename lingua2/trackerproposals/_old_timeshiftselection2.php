<?php
// Obtener todos los husos horarios disponibles
$timezones = DateTimeZone::listIdentifiers();

// Verificar si se ha enviado el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $selectedTimezone = $_POST['timezone'];
    echo "Has seleccionado el huso horario: " . htmlspecialchars($selectedTimezone);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seleccionar Huso Horario</title>
</head>
<body>
    <h1>Selecciona un Huso Horario</h1>
    <form method="POST" action="">
        <label for="timezone">Huso Horario:</label>
        <select name="timezone" id="timezone">
            <?php foreach ($timezones as $timezone): ?>
                <option value="<?php echo htmlspecialchars($timezone); ?>">
                    <?php echo htmlspecialchars($timezone); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <br><br>
        <input type="submit" value="Seleccionar">
    </form>
</body>
</html>