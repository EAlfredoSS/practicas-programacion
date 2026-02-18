<?php
    session_start();

    $logged_user_id = $_SESSION['orden2017'];
    //$logged_user_id = 5549;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Language | Lingua2</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>

<?php

    echo "<script>let userId = " . $logged_user_id . "</script>";
    var_dump($logged_user_id);
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
    require('../files/bd.php'); 

    $query = "SELECT DISTINCT ln.Id 
    FROM languages_names ln
    JOIN my_langs ml ON ln.Id = ml.lang_id
    WHERE ml.id = ?
    AND ln.Id NOT IN (SELECT I_Id FROM languages_macrolanguages);";

    if ($stmt = $link->prepare($query)) {
        $stmt->bind_param("i", $logged_user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
    }

    echo "<br>I Know:<br>";
    echo "<table border='1'>";
    echo "<tr><th>ID</th><th>Nombre</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['Id']) . "</td>";
     //   echo "<td>" . htmlspecialchars($row['Inverted_name']) . "</td>";
        echo "<td><button onclick=\"deleteLanguage('" . $row['Id'] . "', $logged_user_id, 'my_langs')\">Delete</button></td>";
        echo "</tr>";
    }
    echo "</table>";


    $query = "SELECT DISTINCT ln.Id 
    FROM languages_names ln
    JOIN learn_langs ll ON ln.Id = ll.lang_id
    WHERE ll.id = ?
    AND ln.Id NOT IN (SELECT I_Id FROM languages_macrolanguages);";

    if ($stmt = $link->prepare($query)) {
        $stmt->bind_param("i", $logged_user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
    }


    // echo "<table>";
    echo "<br>I want to learn:<br>";
    echo "<table border='1'>";
    echo "<tr><th>ID</th><th>Nombre</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['Id']) . "</td>";
       // echo "<td>" . htmlspecialchars($row['Inverted_name']) . "</td>";
        echo "<td><button onclick=\"deleteLanguage('" . $row['Id'] . "', $logged_user_id, 'learn_langs')\">Delete</button></td>";
        echo "</tr>";
    }
    echo "</table>";

    ?>



    <script>
        console.log(userId);

        function deleteLanguage(langId, userId, tabla) {
            fetch('api.php', {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        lang_id: langId,
                        user_id: userId,
                        tabla: tabla // Aquí se incluye el valor de la tabla
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        alert('Idioma eliminado correctamente');
                        location.reload(); // Recargar la página para reflejar los cambios
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => console.error('Error:', error));
        }
    </script>
</body>

</html>