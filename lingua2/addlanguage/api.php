<?php
header('Content-Type: application/json');
require('../files/bd.php');

$response = [];

if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $input = json_decode(file_get_contents('php://input'), true);
    error_log(print_r($input, true));  // Will log the received data

    if (isset($input['lang_id'], $input['user_id'], $input['tabla'])) {
        $lang_id = $input['lang_id'];
        $user_id = $input['user_id'];
        $tabla = $input['tabla'];
        if($tabla === 'learn_langs'){
            $query = "DELETE FROM learn_langs WHERE id=? AND (lang_id=? OR sublanguage_of=?) ";
        } else {
            $query = "DELETE FROM my_langs WHERE id=? AND (lang_id=? OR sublanguage_of=?)";
        }

        if ($stmt = $link->prepare($query)) {
            $stmt->bind_param("iss", $user_id, $lang_id, $lang_id);
            $stmt->execute();

            if ($stmt->affected_rows > 0) {
                $response['status'] = 'success';
                $response['message'] = 'Idioma eliminado correctamente';
            } else {
                $response['status'] = 'error';
                $response['message'] = 'No se pudo eliminar el idioma';
            }

            $stmt->close();
        } else {
            $response['status'] = 'error';
            $response['message'] = 'Error en la consulta';
        }
    } else {
        $response['status'] = 'error';
        $response['message'] = 'Faltan parámetros (lang_id, user_id)';
    }

    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}


if (isset($_GET['searchText'])) {
    $searchText = $_GET['searchText'];
    $query = "SELECT Id, Inverted_name
              FROM languages_names
              WHERE Id NOT IN (SELECT I_Id FROM languages_macrolanguages) 
              AND (LOWER(Inverted_name) LIKE LOWER(?) OR LOWER(Id) LIKE LOWER(?))
              ORDER BY
                  CASE
                      WHEN LOWER(Inverted_name) = LOWER(?) OR LOWER(Id) = LOWER(?) THEN 0
                      ELSE 1
                  END, 
                  Id";

    if ($stmt = $link->prepare($query)) {
        $searchParam = "%$searchText%";
        $exactParam = $searchText;
        $stmt->bind_param("ssss", $searchParam, $searchParam, $exactParam, $exactParam);
        $stmt->execute();
        $result = $stmt->get_result();

        $languages = [];
        while ($row = $result->fetch_assoc()) {
            $languages[] = [
                'Id' => $row['Id'],
                'Name' => $row['Inverted_name']
            ];
        }
        $response['languages'] = $languages;
    }
}

if (isset($_GET['lang'])) {
    $main_language_id = $_GET['lang'];

    $query = "SELECT Id, lang_name FROM languages1 WHERE Scope='M' AND Id = ?";
    if ($stmt = $link->prepare($query)) {
        $stmt->bind_param('s', $main_language_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $row = $result->fetch_assoc();
            $response['main_language'] = [
                'Id' => $row['Id'],
                'Name' => $row['lang_name']
            ];

            $subquery = "SELECT lm.I_Id, ln.Inverted_Name 
                         FROM languages_macrolanguages lm
                         LEFT JOIN languages_names ln ON ln.Id = lm.I_Id
                         WHERE lm.M_Id = ? AND ln.Inverted_Name IS NOT NULL";
            if ($stmt2 = $link->prepare($subquery)) {
                $stmt2->bind_param('s', $main_language_id);
                $stmt2->execute();
                $subresult = $stmt2->get_result();
                
                $sublanguages = [];
                while ($subrow = $subresult->fetch_assoc()) {
                    $sublanguages[] = [
                        'Id' => $subrow['I_Id'],
                        'Name' => $subrow['Inverted_Name']
                    ];
                }
                $response['sublanguages'] = $sublanguages;
            }
        }
    }
}

echo json_encode($response);
exit;
