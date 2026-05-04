<?php
// Obtener todos los husos horarios disponibles
$timezones = DateTimeZone::listIdentifiers();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seleccionar Huso Horario con Filtro</title>
    <style>
        #search {
            width: 300px;
            padding: 10px;
            font-size: 16px;
        }
        #timezone-list {
            margin-top: 10px;
            max-height: 200px;
            overflow-y: auto;
            border: 1px solid #ccc;
            width: 320px;
        }
        #timezone-list option {
            padding: 8px;
            cursor: pointer;
        }
        #timezone-list option:hover {
            background-color: #f0f0f0;
        }
    </style>
</head>
<body>
    <h1>Selecciona un Huso Horario</h1>
    <input type="text" id="search" placeholder="Buscar huso horario...">
    <select id="timezone-list" size="10">
        <?php foreach ($timezones as $timezone): ?>
            <option value="<?php echo htmlspecialchars($timezone); ?>">
                <?php echo htmlspecialchars($timezone); ?>
            </option>
        <?php endforeach; ?>
    </select>

    <script>
        // Función para filtrar los husos horarios
        function filterTimezones() {
            const input = document.getElementById('search').value.toUpperCase();
            const options = document.getElementById('timezone-list').options;

            for (let i = 0; i < options.length; i++) {
                const option = options[i];
                if (option.text.toUpperCase().indexOf(input) > -1) {
                    option.style.display = '';
                } else {
                    option.style.display = 'none';
                }
            }
        }

        // Escuchar el evento de entrada en el campo de búsqueda
        document.getElementById('search').addEventListener('input', filterTimezones);
    </script>
</body>
</html>