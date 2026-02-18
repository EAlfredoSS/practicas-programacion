<?php
require_once 'dbConnectionBegin.php';
require_once 'search-parser.php';

$Limit = 60;

// Recoge el id de usuario y los filtros desde GET o POST
$user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : 0;
$request = $_GET;

if (!$user_id) {
    echo json_encode(['error' => 'No user_id provided']);
    exit;
}

// Genera la query de búsqueda igual que en search-results.php
$queryString = searchMySQLString();
if (!$queryString) {
    echo json_encode(['error' => 'No query']);
    exit;
}

$resultado = mysqli_query($db_connection, $queryString);
$page = 0;
$found = false;
$i = 0;
while ($registro = mysqli_fetch_assoc($resultado)) {
    if ($registro['id'] == $user_id) {
        $found = true;
        break;
    }
    $i++;
}
mysqli_free_result($resultado);

if ($found) {
    $page = floor($i / $Limit) + 1; // Paginación 1-indexada
    echo json_encode(['page' => $page]);
} else {
    echo json_encode(['error' => 'User not found']);
} 