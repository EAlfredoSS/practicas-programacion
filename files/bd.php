<?php 
    // Esta línea oculta los avisos naranjas (Warnings/Notices) de PHP 8
    error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING & ~E_DEPRECATED);

    // Datos para tu WAMP local
    $servername = "localhost";
    $username = "root";     // Usuario por defecto de WAMP
    $password = "";         // Contraseña vacía por defecto de WAMP
    $dbname = "lingua2";    // El nombre que vemos en tu captura de phpMyAdmin

    // Crear la conexión
    $link = mysqli_connect($servername, $username, $password, $dbname);

    // Configurar idioma español/utf8
    mysqli_set_charset($link, "utf8");

    // Verificar si ha funcionado
    if (!$link) {
        die("Fallo conexion BD.php: " . mysqli_connect_error());
    } 
?>