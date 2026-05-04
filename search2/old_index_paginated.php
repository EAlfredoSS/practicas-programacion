<?php

// Corrected index_paginated.php file
/* error_reporting(E_ALL);
ini_set('display_errors', 1); */

session_start();

// --- Lógica para manejar el parámetro 'highlight' ---
$highlight_user = isset($_GET['highlight']) ? intval($_GET['highlight']) : 0;

if ($highlight_user > 0 && (!isset($_GET['page']) || intval($_GET['page']) <= 0)) {
    require_once('../files/bd.php');
    $identificador2017 = isset($_SESSION['orden2017']) ? $_SESSION['orden2017'] : 0;
    $query_user1 = "SELECT * FROM mentor2009 WHERE orden = $identificador2017";
    $result_user1 = mysqli_query($link, $query_user1);
    $fila_user1 = mysqli_fetch_array($result_user1);
    $latitud1 = $fila_user1['Gpslat'];
    $longitud1 = $fila_user1['Gpslng'];
    $perPage = 30;
    // Asegurar defaults para filtros usados más abajo
    $basePeople = "1=1";
    // Determinar perfil buscado
    $profileType = isset($_GET['partner']) && $_GET['partner'] === 'teacher' ? 'teachers' : (isset($_GET['partner']) && $_GET['partner'] === 'student' ? 'students' : 'all');
    $user_where_clause = $basePeople;

    // Filtros de sexo
    $ismale   = isset($_GET['male']) ? $_GET['male'] : '';
    $isfemale = isset($_GET['female']) ? $_GET['female'] : '';
    if ($ismale == "on" && $isfemale != "on") {
        $user_where_clause .= " AND m.Sexo='M'";
    } elseif ($isfemale == "on" && $ismale != "on") {
        $user_where_clause .= " AND m.Sexo='F'";
    } elseif ($ismale == "on" && $isfemale == "on") {
        $user_where_clause .= " AND (m.Sexo='M' OR m.Sexo='F')";
    }

    // Idiomas
    $userislearning = isset($_GET['learns']) ? $_GET['learns'] : '';
    if (!empty($userislearning)) {
        if ($profileType === 'teachers') {
            $user_where_clause .= " AND EXISTS (SELECT 1 FROM my_langs WHERE id=m.orden AND lang_id='".mysqli_real_escape_string($link,$userislearning)."')";
        } elseif ($profileType === 'students') {
            $user_where_clause .= " AND EXISTS (SELECT 1 FROM learn_langs WHERE id=m.orden AND lang_id='".mysqli_real_escape_string($link,$userislearning)."')";
        } else {
            $user_where_clause .= " AND (EXISTS (SELECT 1 FROM my_langs WHERE id=m.orden AND lang_id='".mysqli_real_escape_string($link,$userislearning)."')
                                      OR EXISTS (SELECT 1 FROM learn_langs WHERE id=m.orden AND lang_id='".mysqli_real_escape_string($link,$userislearning)."'))";
        }
    }

    $my_langs_array_multidim = array(); 

    // Distancia
    $global_search = isset($_GET['zone']) && $_GET['zone']=='on';
    $distancias_permitidas=[1,5,10,20,50];
    $radio = isset($_GET['distance'])?(int)$_GET['distance']:5;
    $user_filtro_distancia="";
    if(!$global_search && in_array($radio,$distancias_permitidas)){
        $user_filtro_distancia="HAVING distanciaPunto1Punto2<$radio";
    } else if(!$global_search){
        $user_where_clause=$basePeople." AND (m.Sexo='M' OR m.Sexo='F')";
        $user_filtro_distancia="HAVING distanciaPunto1Punto2<150";
    }

    $position_query="
        SELECT m.orden,(ACOS(SIN(RADIANS(m.Gpslat))*SIN(RADIANS($latitud1))+
        COS(RADIANS(m.Gpslat))*COS(RADIANS($latitud1))*COS(RADIANS(m.Gpslng)-RADIANS($longitud1)))*6378) AS distanciaPunto1Punto2
        FROM mentor2009 m WHERE $user_where_clause $user_filtro_distancia
    ";
    $position_result = mysqli_query($link, $position_query);
    $user_position = -1;
    $current_index = 0;
    if ($position_result) {
        while ($row = mysqli_fetch_assoc($position_result)) {
            if (intval($row['orden']) === $highlight_user) {
                $user_position = $current_index;
                break;
            }
            $current_index++;
        }
        mysqli_free_result($position_result);
    }
    if ($user_position !== -1) {
        $target_page = floor($user_position / $perPage) + 1;
        $current_params = $_GET;
        $current_params['page'] = $target_page;
        $redirect_url = '?' . http_build_query($current_params);
        header("Location: $redirect_url");
        exit();
    }
}
// --- Fin de la lógica para manejar el parámetro 'highlight' ---

require('../templates/header_simplified.html');
require('../funcionesphp/funciones_idiomas_usuario.php');
require('../files/bd.php');

// --- Inicio del cambio para manejar la sesión ---
// Verificar si 'orden2017' está seteado en la sesión y es numérico
if (isset($_SESSION['orden2017']) && is_numeric($_SESSION['orden2017'])) {
    $identificador2017 = $_SESSION['orden2017'];
    $_SESSION['idusuario2019'] = $identificador2017;
} else {
    // Si no está seteado o no es numérico, el usuario no está logueado
    // Aquí puedes redirigir a la página de login si lo prefieres,
    // o simplemente terminar la ejecución con el mensaje.
    die("You are not logged in.");
}
// --- Fin del cambio ---


// Debug query if needed
// $debug_query = "SELECT orden, nombre FROM mentor2009 WHERE orden IN (5010, 4701, 4484, 4721, 1600, 903)";
// $debug_result = mysqli_query($link, $debug_query);

$query_user1 = "SELECT * FROM mentor2009 WHERE orden = $identificador2017";
$result_user1 = mysqli_query($link, $query_user1);

if (!mysqli_num_rows($result_user1)) {
    // Aunque ya verificamos si está logueado, esta comprobación asegura
    // que el ID de usuario de la sesión realmente existe en la base de datos.
    die("<br/>.........No user......<br/>");
}

$fila_user1 = mysqli_fetch_array($result_user1);
$latitud1 = $fila_user1['Gpslat'];
$longitud1 = $fila_user1['Gpslng'];

// Configuración de paginación
$perPage = 30;
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($page - 1) * $perPage;

// Definir por defecto la cláusula SELECT para evitar variable indefinida
$select_price_clause = "m.*, ";

// Asegurar un ORDER BY por defecto para evitar variable indefinida y errores SQL
$order_by_sql = "m.lastaction DESC, distanciaPunto1Punto2 ASC";

if ($latitud1 == 0 && $longitud1 == 0) {
    $latitud1 = 51.477928;
    $longitud1 = 0;
    ?>
    <br /><br />
    <div class="alert alert-danger" align="center">
        You have not indicated your city. Insert your city now
        <a style="text-decoration: underline;" href="<?php echo "../user/getgpsposition.php"; ?>">here</a>,
        otherwise you will see London, UK as your default city.
    </div>
    <br /><br />
    <?php
}

// Initialize arrays
$orden_usuarios = array();
$nameuser = array();
$organiz_id = array();
$distancia111 = array();
$array_num_evalu = array();
$array_nota_evalu = array();
$lat_usuarios = array();
$lng_usuarios = array();

$my_langs_array_multidim = array();
$my_langs_full_name_array_multidim = array();
$my_langs_level_array_multidim = array();
$my_langs_forshare_array_multidim = array();
$my_langs_price_array_multidim = array();
$my_langs_typeofexchange_array_multidim = array();
$my_langs_priceorexchangetext_array_multidim = array();
$my_langs_level_image_array_multidim = array();
$my_langs_2letters_array_multidim = array();

$learn_langs_array_multidim = array();
$learn_langs_full_name_array_multidim = array();
$learn_langs_level_array_multidim = array();
$learn_langs_forshare_array_multidim = array();
$learn_langs_price_array_multidim = array();
$learn_langs_typeofexchange_array_multidim = array();
$learn_langs_priceorexchangetext_array_multidim = array();
$learn_langs_level_image_array_multidim = array();
$learn_langs_2letters_array_multidim = array();

$form_submitted = isset($_GET['learns']) || isset($_GET['teaches']) || !empty($_GET['male']) || !empty($_GET['female']) || isset($_GET['orgs']) || !empty($_GET['distance']); // Añadir más parámetros para detectar si se usaron filtros
$global_search = isset($_GET['zone']) && $_GET['zone'] == 'on';

// Build the query based on form submission (Logic for displaying the current page)
// Esta parte ya la tienes, solo hay que asegurar que $where_clause y $filtro_distancia
// se construyan de la misma manera que se hizo para encontrar la posición del usuario
// si no se estaba manejando un 'highlight' sin página.

// Si no se está manejando un 'highlight' sin página, o si se redireccionó,
// la ejecución continúa aquí para cargar la página actual ($page).

if ($form_submitted || $highlight_user > 0) { // Usar los filtros si se enviaron o si hay un usuario a resaltar
    $userislearning = isset($_GET['learns']) ? $_GET['learns'] : '';
    $useristeaching = isset($_GET['teaches']) ? $_GET['teaches'] : '';
    // Normalizar nombres como 'English' a códigos de 3 letras (p.ej., 'eng') usando languages_names
    if (!function_exists('map_to_lang_code3')) {
        function map_to_lang_code3($val, $link) {
            $v = trim((string)$val);
            if ($v === '') return '';
            $v3 = strtolower($v);
            
            // Excepción especial: catalan y valenciano siempre devuelven 'cat'
            if ($v3 === 'catalan' || $v3 === 'valenciano' || $v3 === 'valencian') {
                return 'cat';
            }
            
            if (preg_match('/^[a-z]{3}$/', $v3)) {
                return $v3;
            }
            $v_esc = mysqli_real_escape_string($link, $v);
            // Intento 1: coincidencia exacta por Print_Name
            $sql1 = "SELECT Id FROM languages_names WHERE Print_Name = '$v_esc' LIMIT 1";
            if ($res1 = mysqli_query($link, $sql1)) {
                if ($row1 = mysqli_fetch_assoc($res1)) {
                    return strtolower($row1['Id']);
                }
            }
            // Intento 2: coincidencia insensible a mayúsculas
            $sql2 = "SELECT Id FROM languages_names WHERE LOWER(Print_Name) = LOWER('$v_esc') LIMIT 1";
            if ($res2 = mysqli_query($link, $sql2)) {
                if ($row2 = mysqli_fetch_assoc($res2)) {
                    return strtolower($row2['Id']);
                }
            }
            // Intento 3: LIKE por si el nombre contiene variantes
            $sql3 = "SELECT Id FROM languages_names WHERE Print_Name LIKE '%$v_esc%' ORDER BY Print_Name LIMIT 1";
            if ($res3 = mysqli_query($link, $sql3)) {
                if ($row3 = mysqli_fetch_assoc($res3)) {
                    return strtolower($row3['Id']);
                }
            }
            // Si no encontramos mapeo, devolvemos el valor tal cual para no romper otros flujos
            return $v;
        }
    }
    $userislearning = map_to_lang_code3($userislearning, $link);
    $useristeaching = map_to_lang_code3($useristeaching, $link);
    // Compat range sliders previously used
    $minimumlevel_userislearning = isset($_GET['min_level']) ? (int)$_GET['min_level'] : 1;
    $maximumlevel_userislearning = isset($_GET['max_level']) ? (int)$_GET['max_level'] : 7;
    // Flag variables for level filtering (explicit checkbox state)
    $use_learns_level_filter = isset($_GET['use_learns_level_filter']) && $_GET['use_learns_level_filter'] === 'on';
    $use_teaches_level_filter = isset($_GET['use_teaches_level_filter']) && $_GET['use_teaches_level_filter'] === 'on';

    // New explicit ranges per flow (optional)
    // Support both old generic names (compatibility) and new specific slider names
    $learns_min_level   = isset($_GET['min_learns_level']) ? (int)$_GET['min_learns_level'] : (isset($_GET['learns_min_level']) ? (int)$_GET['learns_min_level'] : null);
    $learns_max_level   = isset($_GET['max_learns_level']) ? (int)$_GET['max_learns_level'] : (isset($_GET['learns_max_level']) ? (int)$_GET['learns_max_level'] : null);
    $teaches_min_level  = isset($_GET['min_teaches_level']) ? (int)$_GET['min_teaches_level'] : (isset($_GET['teaches_min_level']) ? (int)$_GET['teaches_min_level'] : null);
    $teaches_max_level  = isset($_GET['max_teaches_level']) ? (int)$_GET['max_teaches_level'] : (isset($_GET['teaches_max_level']) ? (int)$_GET['teaches_max_level'] : null);
    // Backward compatibility fallbacks
    if ($learns_min_level === null) $learns_min_level = $minimumlevel_userislearning;
    if ($learns_max_level === null) $learns_max_level = $maximumlevel_userislearning;

    $organizationslist = isset($_GET['orgs']) && is_array($_GET['orgs']) ? $_GET['orgs'] : [];
    $ismale = isset($_GET['male']) ? $_GET['male'] : '';
    $isfemale = isset($_GET['female']) ? $_GET['female'] : '';

    // Filtro de sexo: si solo uno está marcado, filtramos; si ambos o ninguno, no filtramos
    $sexo_query = '';
    if ($ismale === 'on' && $isfemale !== 'on') {
        $sexo_query = "m.Sexo='M'";
    } elseif ($isfemale === 'on' && $ismale !== 'on') {
        $sexo_query = "m.Sexo='F'";
    } else {
        // ambos marcados o ninguno: sin filtro de sexo
        $sexo_query = '';
    }

    // Filtrar por tipo de usuario según el valor de 'partner'
    // Nota: al buscar profesores, no limitamos por m.Pais='teacher' para no excluir perfiles que enseñan pero no están marcados como 'teacher'.
    if (isset($_GET['partner'])) {
        if ($_GET['partner'] === 'teacher') {
            $where_clause = "1=1 "; // sin restricción por m.Pais
        } elseif ($_GET['partner'] === 'student') {
            $where_clause = "m.Pais<>'teacher' ";
        } else {
            $where_clause = "1=1 ";
        }
    } else {
        $where_clause = "1=1 ";
    }
    if (!empty($sexo_query)) {
        $where_clause .= " AND ($sexo_query)";
    }

    $where_orgs = '';
    $n_orgs = count($organizationslist);
    if ($n_orgs > 0) {
        $where_orgs = "AND (";
        for ($jjjj = 0; $jjjj < $n_orgs; $jjjj++) {
            $organizacion = intval($organizationslist[$jjjj]); // Asegurarse de que sea entero
            $where_orgs .= " m.id_org=$organizacion";
            if ($jjjj < $n_orgs - 1) {
                $where_orgs .= " OR";
            }
        }
        $where_orgs .= ")";
    }
    if (!empty($where_orgs)) {
        $where_clause .= " $where_orgs";
    }

    $distancias_permitidas = [1, 5, 10, 20, 50, 150];
    // Usar 150 km por defecto para ampliar resultados si no se envió distancia
    $radio = isset($_GET['distance']) ? (int) $_GET['distance'] : 20;

    $filtro_distancia = "";
     // Usar filtro distancia solo si NO es búsqueda global
    if (!$global_search && in_array($radio, $distancias_permitidas)) {
        $filtro_distancia = "HAVING distanciaPunto1Punto2 < $radio";
    }


    if (!empty($useristeaching)) {
        $esc_teaches = mysqli_real_escape_string($link, $useristeaching);
        // 'teaches' = lo que YO puedo enseñar; el candidato debe querer APRENDERLO => learn_langs del candidato
        $level_condition_teaches_fallback = "";
        if ($use_teaches_level_filter) {
             $level_condition_teaches_fallback = " AND ll.level_id BETWEEN $teaches_min_level AND $teaches_max_level";
        }
        $where_clause .= " AND EXISTS (
            SELECT 1 FROM learn_langs ll
            JOIN languages1 lll ON lll.Id = ll.lang_id
            WHERE ll.id = m.orden
            AND (ll.lang_id = '$esc_teaches' OR lll.lang_id = '$esc_teaches')
            $level_condition_teaches_fallback
        )";
    }


    // Nota: el filtro de precio para partner=teacher se añade más abajo, fuera de este bloque duplicado que se ha eliminado.
    // Nota: el filtro de precio para partner=teacher se añade más abajo, fuera de este bloque duplicado que se ha eliminado.
    if (!empty($userislearning)) {
        $esc_learns = mysqli_real_escape_string($link, $userislearning);
        // 'learns' = lo que YO quiero aprender; el candidato debe ENSEÑARLO/HABLARLO => my_langs del candidato
        $level_condition_learns_fallback = "";
        if ($use_learns_level_filter) {
             $level_condition_learns_fallback = " AND ml.level_id BETWEEN $learns_min_level AND $learns_max_level";
        }
        $where_clause .= " AND EXISTS (
            SELECT 1 FROM my_langs ml
            JOIN languages1 lml ON lml.Id = ml.lang_id
            WHERE ml.id = m.orden
            AND (ml.lang_id = '$esc_learns' OR lml.lang_id = '$esc_learns')
            $level_condition_learns_fallback
        )";
    }

    // Filtro por precio cuando se buscan profesores:
    // - Checkbox ON: solo usuarios con precio válido dentro del rango (por idioma o perfil); EXCLUYE sin precio.
    // - Checkbox OFF: solo usuarios SIN precio (ni por idioma ni perfil) para el idioma seleccionado.
    if (isset($_GET['partner']) && $_GET['partner'] === 'teacher' && !empty($userislearning)
        && isset($_GET['use_price_filter']) && $_GET['use_price_filter'] === 'on') {
        $price_min = isset($_GET['min_price']) ? (int)$_GET['min_price'] : (int) $_GET['price_min'];
        $price_max = isset($_GET['max_price']) ? (int)$_GET['max_price'] : (int) $_GET['price_max'];
        
        // Handle "30+" case: Inputs sending empty string become 0. Slider max is 30.
        // If 0 (empty) or >= 30, treat as "No Upper Limit" (Infinity).
        if ($price_max <= 0 || $price_max >= 30) {
            $price_max = 1000000;
        }
        $esc_learns = mysqli_real_escape_string($link, $userislearning);
        // Requerimos que el candidato enseñe el idioma seleccionado y tenga precio válido en rango (por idioma o perfil)
        $where_clause .= " AND EXISTS (
            SELECT 1
            FROM my_langs ml
            JOIN languages1 lml ON lml.Id = ml.lang_id
            WHERE ml.id = m.orden
              AND ml.for_share <> 0
              AND (ml.lang_id = '$esc_learns' OR lml.lang_id = '$esc_learns')
              AND (
                  IFNULL(ml.lang_price,'') REGEXP '^[0-9]+([.,][0-9]+)?$'
                  AND CAST(REPLACE(ml.lang_price, ',', '.') AS DECIMAL(10,2)) BETWEEN $price_min AND $price_max
              )
        )";
    } elseif (isset($_GET['partner']) && $_GET['partner'] === 'teacher' && !empty($userislearning)) {
        // Checkbox OFF: mostrar SOLO usuarios SIN precio (por idioma ni perfil) para el idioma seleccionado
        $esc_learns = mysqli_real_escape_string($link, $userislearning);
        $where_clause .= " AND EXISTS (
            SELECT 1
            FROM my_langs ml
            JOIN languages1 lml ON lml.Id = ml.lang_id
            WHERE ml.id = m.orden
              AND ml.for_share <> 0
              AND (ml.lang_id = '$esc_learns' OR lml.lang_id = '$esc_learns')
              AND (ml.lang_price IS NULL OR ml.lang_price = '' OR NOT(IFNULL(ml.lang_price,'') REGEXP '^[0-9]+([.,][0-9]+)?$'))
        )";
    }

    // Filtro para Students: candidato quiere aprender lo que YO enseño (learn_langs) Y enseña/habla lo que YO quiero aprender (my_langs)
    if (isset($_GET['partner']) && $_GET['partner'] === 'student' && !empty($userislearning) && !empty($useristeaching)) {
        $where_clause = "m.Pais<>'teacher' ";
        if (!empty($sexo_query)) {
            $where_clause .= " AND ($sexo_query)";
        }
        if (!empty($where_orgs)) {
            $where_clause .= " $where_orgs";
        }
        $esc_learns = mysqli_real_escape_string($link, $userislearning);
        $esc_teaches = mysqli_real_escape_string($link, $useristeaching);
        
        // 1. Candidato debe querer aprender lo que yo enseño (learn_langs del candidato)
        // If filter checkbox is ON, apply level filter. Else, just language match.
        $level_condition_teaches = "";
        if ($use_teaches_level_filter) {
             $level_condition_teaches = " AND ll.level_id BETWEEN $teaches_min_level AND $teaches_max_level";
        }
        $where_clause .= " AND EXISTS (SELECT 1 FROM learn_langs ll WHERE ll.id = m.orden AND ll.lang_id = '$esc_teaches' $level_condition_teaches)";

        // 2. Candidato debe enseñar/hablar lo que yo quiero aprender (my_langs del candidato)
        // If filter checkbox is ON, apply level filter. Else, just language match.
        $level_condition_learns = "";
        if ($use_learns_level_filter) {
             // Ensure my_langs has 'level_id' column or check levels table logic? 
             // my_langs typically has level_id. 
             $level_condition_learns = " AND ml.level_id BETWEEN $learns_min_level AND $learns_max_level";
        }
        $where_clause .= " AND EXISTS (SELECT 1 FROM my_langs ml WHERE ml.id = m.orden AND ml.lang_id = '$esc_learns' $level_condition_learns)";
    }

    // Filtro de compatibilidad de idiomas
    if (!isset($_GET['lang-compatibility'])) {
        $user_langs_query = "SELECT lang_id FROM my_langs WHERE id = $identificador2017";
        $user_langs_result = mysqli_query($link, $user_langs_query);
        $user_langs = [];
        while ($row = mysqli_fetch_assoc($user_langs_result)) {
            $user_langs[] = $row['lang_id'];
        }
        mysqli_free_result($user_langs_result);

        if (!empty($user_langs)) {
            // Escapar correctamente cada valor usando el enlace a la BD
            $escaped = array_map(function($v) use ($link) { return mysqli_real_escape_string($link, $v); }, $user_langs);
            $user_langs_list = implode("','", $escaped);
            // Compatibilidad: candidato habla alguno de mis idiomas OR candidato quiere aprender alguno de mis idiomas
            $where_clause .= " AND (
                EXISTS (
                    SELECT 1 FROM my_langs ml
                    WHERE ml.id = m.orden
                    AND ml.lang_id IN ('$user_langs_list')
                )
                OR EXISTS (
                    SELECT 1 FROM learn_langs ll
                    WHERE ll.id = m.orden
                    AND ll.lang_id IN ('$user_langs_list')
                )
            )";
        }
    }

    // Count total results for pagination (using the constructed where_clause and filtro_distancia)
    $count_query = "
        SELECT COUNT(*) as total FROM (
            SELECT m.orden,
                (ACOS(SIN(RADIANS(m.Gpslat)) * SIN(RADIANS($latitud1)) +
                    COS(RADIANS(m.Gpslat)) * COS(RADIANS($latitud1)) *
                    COS(RADIANS(m.Gpslng) - RADIANS($longitud1))) * 6378) AS distanciaPunto1Punto2
            FROM mentor2009 m
            WHERE $where_clause
            " . (!empty($filtro_distancia) ? $filtro_distancia : "") . "
        ) as subquery";

    $count_result = mysqli_query($link, $count_query);
    $total_rows = mysqli_fetch_assoc($count_result)['total'];
    $total_pages = ceil($total_rows / $perPage);
    // Asegurarse de que la página actual no exceda el total de páginas
    $page = max(1, min($page, $total_pages > 0 ? $total_pages : 1));
    $offset = ($page - 1) * $perPage;


    // Main query with pagination (using the constructed where_clause and filtro_distancia)
    // --- QUERY ORIGINAL COMENTADA PARA REFERENCIA ---
    /*
    $query = "
        SELECT m.*,
            (ACOS(SIN(RADIANS(m.Gpslat)) * SIN(RADIANS($latitud1)) +
                COS(RADIANS(m.Gpslat)) * COS(RADIANS($latitud1)) *
                COS(RADIANS(m.Gpslng) - RADIANS($longitud1))) * 6378) AS distanciaPunto1Punto2
        FROM mentor2009 m
        WHERE $where_clause
        " . (!empty($filtro_distancia) ? $filtro_distancia : "") . "
        ORDER BY distanciaPunto1Punto2 ASC
        LIMIT $perPage OFFSET $offset";
    */

    // --- QUERY ADAPTADA PARA FILTRO DE PRECIO E IDIOMA ROBUSTO ---
    // --- QUERY ANTERIOR COMENTADA PARA REFERENCIA ---
    /*
    $query = ...
    */

    // Construir la query principal reutilizando $where_clause y el $filtro_distancia para evitar inconsistencias

    // Determinar orden (por defecto por distancia)
    $order_by_sql = "m.lastaction DESC, distanciaPunto1Punto2 ASC";
    $select_price_field = "";
    $orderParam = isset($_GET['orderresultsby']) ? $_GET['orderresultsby'] : '';

    // Orden por precio (solo teachers + idioma seleccionado)
    if (in_array($orderParam, ['price_asc','price_desc']) && isset($_GET['partner']) && $_GET['partner'] === 'teacher' && !empty($userislearning)) {
        $dir = $orderParam === 'price_asc' ? 'ASC' : 'DESC';
        $esc_learns = mysqli_real_escape_string($link, $userislearning);
        // Subconsulta: precio específico por idioma si existe y es numérico
        $price_subquery = "(SELECT CAST(REPLACE(ml.lang_price, ',', '.') AS DECIMAL(10,2)) FROM my_langs ml JOIN languages1 lml ON lml.Id = ml.lang_id WHERE ml.id = m.orden AND (ml.lang_id = '$esc_learns' OR lml.lang_id = '$esc_learns') AND IFNULL(ml.lang_price,'') REGEXP '^[0-9]+([.,][0-9]+)?$' LIMIT 1)";
        // Eliminado fallback a m.teacherprice. Solo precio del idioma.
        $price_expr = "$price_subquery";
        $select_price_field = ", $price_expr AS price_num";
        $order_by_sql = "$price_expr $dir, distanciaPunto1Punto2 ASC";
    }
    // Orden por número de evaluaciones (más/menos)
    elseif ($orderParam === 'more_evals') {
        $order_by_sql = "m.ev_num_diaria DESC, distanciaPunto1Punto2 ASC";
    } elseif ($orderParam === 'less_evals') {
        $order_by_sql = "m.ev_num_diaria ASC, distanciaPunto1Punto2 ASC";
    }
    // Orden por mejor valoración (proporción de evaluaciones)
    elseif ($orderParam === 'best_evals') {
        $order_by_sql = "m.ev_proporc_diaria DESC, distanciaPunto1Punto2 ASC";
    }
    // Orden por distancia
    elseif ($orderParam === 'distance') {
        $order_by_sql = "distanciaPunto1Punto2 ASC";
    }
    // Último login / más reciente
    elseif ($orderParam === 'lastlogin') {
        $order_by_sql = "m.lastaction DESC, distanciaPunto1Punto2 ASC";
    } elseif ($orderParam === 'newest') {
        // No hay campo explícito conocido; usar orden DESC como proxy de usuarios nuevos
        $order_by_sql = "m.orden DESC, distanciaPunto1Punto2 ASC";
    }

    // Preparar correctamente la lista de selección evitando comas duplicadas
    $select_price_clause = "m.*";
    if ($select_price_field != '') {
        $select_price_clause .= $select_price_field; // $select_price_field incluye la coma inicial
    }
    $select_price_clause .= ", ";

    $query = "
        SELECT " . $select_price_clause . "
            (ACOS(SIN(RADIANS(m.Gpslat)) * SIN(RADIANS($latitud1)) +
                COS(RADIANS(m.Gpslat)) * COS(RADIANS($latitud1)) *
                COS(RADIANS(m.Gpslng) - RADIANS($longitud1))) * 6378) AS distanciaPunto1Punto2
        FROM mentor2009 m
        WHERE $where_clause
        " . (!empty($filtro_distancia) ? $filtro_distancia : "") . "
        ORDER BY $order_by_sql
        LIMIT $perPage OFFSET $offset";

} else {
    // Default query with pagination (when no form is submitted and no highlight is active without page)
    // Count total results for pagination
    // Alineamos la distancia por defecto con el mapa: 150 km cuando no hay filtros
    $distancias_permitidas_def = [1,5,10,20,50,150];
    $radio = isset($_GET['distance']) ? (int)$_GET['distance'] : 20;
    if (!in_array($radio, $distancias_permitidas_def)) { $radio = 20; }

    $count_query = "
        SELECT COUNT(*) as total FROM (
            SELECT m.orden,
                (ACOS(SIN(RADIANS(m.Gpslat)) * SIN(RADIANS($latitud1)) +
                    COS(RADIANS(m.Gpslat)) * COS(RADIANS($latitud1)) *
                    COS(RADIANS(m.Gpslng) - RADIANS($longitud1))) * 6378) AS distanciaPunto1Punto2
            FROM mentor2009 m
            WHERE (m.Sexo = 'M' OR m.Sexo = 'F')
            HAVING distanciaPunto1Punto2 < $radio
        ) as subquery";

    $count_result = mysqli_query($link, $count_query);
    $total_rows = mysqli_fetch_assoc($count_result)['total'];
    $total_pages = ceil($total_rows / $perPage);
     // Asegurarse de que la página actual no exceda el total de páginas
    $page = max(1, min($page, $total_pages > 0 ? $total_pages : 1));
    $offset = ($page - 1) * $perPage;

    // Reutilizar $select_price_clause en la consulta por defecto
    $query = "
        SELECT " . $select_price_clause . "
            (ACOS(SIN(RADIANS(m.Gpslat)) * SIN(RADIANS($latitud1)) +
                COS(RADIANS(m.Gpslat)) * COS(RADIANS($latitud1)) *
                COS(RADIANS(m.Gpslng) - RADIANS($longitud1))) * 6378) AS distanciaPunto1Punto2
        FROM mentor2009 m
     WHERE (m.Sexo = 'M' OR m.Sexo = 'F')
        HAVING distanciaPunto1Punto2 < $radio
        ORDER BY $order_by_sql
        LIMIT $perPage OFFSET $offset";
}

/*$precioMin = isset($_GET['min_price']) ? intval($_GET['min_price']) : 0;
$precioMax = isset($_GET['max_price']) ? intval($_GET['max_price']) : 999;
		 $query = "
			SELECT m.*,
			   (ACOS(
					SIN(RADIANS(m.Gpslat)) * SIN(RADIANS($latitud1)) +
					COS(RADIANS(m.Gpslat)) * COS(RADIANS($latitud1)) *
					COS(RADIANS(m.Gpslng) - RADIANS($longitud1))
				) * 6378) AS distanciaPunto1Punto2
		FROM mentor2009 m, my_langs l
		WHERE (m.Sexo = 'M' OR m.Sexo = 'F')
		
		  AND l.lang_price BETWEEN $precioMin AND $precioMax
		HAVING distanciaPunto1Punto2 < 150
		ORDER BY distanciaPunto1Punto2 ASC
		LIMIT $perPage OFFSET $offset";
		}
*/		
// Debug query if needed (enable by adding &debug_sql=1 to the URL)
if (isset($_GET['debug_sql']) && $_GET['debug_sql'] == '1') {
    echo "<!-- SQL: " . htmlspecialchars($query) . " -->";
}

$result = mysqli_query($link, $query);

if (!$result || !mysqli_num_rows($result)) {
    echo "<br/>.........No results......<br/>";
    $number_of_affected_users = 0;
} else {
    $number_of_affected_users = mysqli_num_rows($result);

    while ($fila = mysqli_fetch_array($result)) {
        $orden_actual = $fila['orden'];
        array_push($orden_usuarios, $orden_actual);
        array_push($nameuser, $fila['nombre']);
        array_push($organiz_id, $fila['id_org']);
        array_push($distancia111, round($fila['distanciaPunto1Punto2'], 2));
        array_push($array_num_evalu, $fila['ev_num_diaria']);
        array_push($array_nota_evalu, $fila['ev_proporc_diaria']);
        array_push($lat_usuarios, $fila['Gpslat']);
        array_push($lng_usuarios, $fila['Gpslng']);
		
		
		
        list($my_langs_array_multidim[$orden_actual], $my_langs_full_name_array_multidim[$orden_actual], $my_langs_level_array_multidim[$orden_actual], $my_langs_forshare_array_multidim[$orden_actual], $my_langs_price_array_multidim[$orden_actual], $my_langs_typeofexchange_array_multidim[$orden_actual], $my_langs_priceorexchangetext_array_multidim[$orden_actual], $my_langs_level_image_array_multidim[$orden_actual], $my_langs_2letters_array_multidim[$orden_actual]) = lenguas_que_conoce_usuario($orden_actual, $link);
        list($learn_langs_array_multidim[$orden_actual], $learn_langs_full_name_array_multidim[$orden_actual], $learn_langs_level_array_multidim[$orden_actual], $learn_langs_forshare_array_multidim[$orden_actual], $learn_langs_price_array_multidim[$orden_actual], $learn_langs_typeofexchange_array_multidim[$orden_actual], $learn_langs_priceorexchangetext_array_multidim[$orden_actual], $learn_langs_level_image_array_multidim[$orden_actual], $learn_langs_2letters_array_multidim[$orden_actual]) = lenguas_que_quiere_estudiar_usuario($orden_actual, $link);
    }
	// print_r (lenguas_que_conoce_usuario($orden_actual, $link));
}
 
$nameuser_original = $nameuser;
// Function to render pagination controls
function renderPagination($page, $total_pages, $params)
{
    // Eliminar el parámetro de página actual para no duplicarlo en los enlaces
    unset($params['page']);
    
    // Mantener el parámetro highlight si existe para que se propague
    $highlight = isset($params['highlight']) ? $params['highlight'] : null;
    if ($highlight) {
        $params['highlight'] = $highlight;
    }
    
    $query_string = http_build_query($params);
    $query_string = !empty($query_string) ? '&' . $query_string : '';

    echo '<div class="pagination">';

    // Previous page link
    if ($page > 1) {
        echo "<a href='?page=" . ($page - 1) . $query_string . "'>« Previous</a>";
    } else {
        echo "<span class='disabled'>« Previous</span>";
    }

    // Page numbers
    $start_page = max(1, $page - 2);
    $end_page = min($total_pages, $page + 2);

    if ($start_page > 1) {
        echo "<a href='?page=1" . $query_string . "'>1</a>";
        if ($start_page > 2) {
            echo "<span>...</span>";
        }
    }

    for ($i = $start_page; $i <= $end_page; $i++) {
        if ($i == $page) {
            echo "<span class='active'>$i</span>";
        } else {
            echo "<a href='?page=$i" . $query_string . "'>$i</a>";
        }
    }

    if ($end_page < $total_pages) {
        if ($end_page < $total_pages - 1) {
            echo "<span>...</span>";
        }
        echo "<a href='?page=$total_pages" . $query_string . "'>$total_pages</a>";
    }

    // Next page link
    if ($page < $total_pages) {
        echo "<a href='?page=" . ($page + 1) . $query_string . "'>Next »</a>";
    } else {
        echo "<span class='disabled'>Next »</span>";
    }

    echo '</div>';
}

// Obtener la página en la que se encuentra un usuario específico
// Eliminar la función getUserPage y la redirección automática
// Simplificar la función highlightUserCard para solo resaltar usuarios en la página actual

// Si hay un parámetro highlight, verificar en qué página está el usuario
// Esta lógica se movió al principio del archivo.
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Search for your partner | Lingua2</title>
    <link rel="stylesheet" href="jquery-ui-1.13.3.custom/jquery-ui.css" />
    <link rel="stylesheet" href="/resources/demos/style.css" />
    <link rel="stylesheet" href="widgets.css" />
    <link rel="stylesheet" href="lingua2general.css?v=<?php echo time(); ?>" />
    <link rel="stylesheet" href="estilo.css?v=<?php echo time(); ?>" />
    <link rel="stylesheet" type="text/css" href="../public/css/style.css?v=<?php echo time(); ?>">
    <script src="jquery-ui-1.13.3.custom/external/jquery/jquery.js"></script>
    <script src="jquery-ui-1.13.3.custom/jquery-ui.js"></script>
    <script src="https://unpkg.com/leaflet@1.6.0/dist/leaflet.js"></script>
    <link href="https://unpkg.com/leaflet@1.6.0/dist/leaflet.css" rel="stylesheet" />
    <style>
        .pagination {
            margin: 20px 0;
            text-align: center;
        }

        .pagination a,
        .pagination span {
            display: inline-block;
            padding: 8px 16px;
            text-decoration: none;
            color: #000;
            background-color: #f1f1f1;
            border-radius: 5px;
            margin: 0 4px;
        }

        .pagination a:hover {
            background-color: #ddd;
        }

        .pagination .active {
            background-color: #e65f00;
            color: white;
        }

        .pagination .disabled {
            color: #aaa;
            cursor: not-allowed;
        }
        
        /* FIX: Remove underlines from pagination */
        .pagination a, .pagination span {
            text-decoration: none !important;
        }
        .pagination a:hover {
            text-decoration: none !important;
        }

        /* Estilos para las tarjetas de usuario */
        .user-card {
            transition: all 0.3s ease;
        }

        .user-card.active {
            border: 2px solid orange !important;
            box-shadow: 0 0 10px rgba(255,165,0,0.5) !important;
            background-color: #FFFACD !important;
        }

        /* Estilo para hacer que la imagen y el nombre sean clicables */
        .user-card > div:first-child {
            cursor: pointer;
        }

        /* FIX: Only underline the specific user-name-highlight span, not everything */
        /* Remove generic underline rule */
        .user-card > div:first-child:hover {
            text-decoration: none; 
            color: #e65f00;
        }
        /* Underline only the name */
        .user-card > div:first-child:hover .user-name-highlight {
            text-decoration: underline;
        }

        /* Estilo para el icono de ranking */
        .ranking-icon {
            transition: fill 0.3s ease;
            margin-right: 4px;
        }

        /* Removed .user-card:hover .ranking-icon rules */
        
        /* Force hover color to be a very light orange/white as requested */
        .user-card:hover {
            background-color: #fff8e1 !important; /* Lighter orange/cornsilk */
            transform: translateY(-5px);
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.2);
        }
    </style>
    <script>
        // Script to reset page to 1 when search form is submitted
        document.addEventListener('DOMContentLoaded', function () {
            const searchForm = document.getElementById('search-form');
            if (searchForm) {
                searchForm.addEventListener('submit', function (event) {
                    // Ocultar el mapa al hacer submit
                    var mapDiv = document.getElementById('map');
                    if (mapDiv) mapDiv.style.display = 'none';
                    var mapIframe = document.querySelector('iframe[src*="search-form.php"]');
                    if (mapIframe) mapIframe.style.display = 'none';
                    // Obtener la URL actual para no perder otros parámetros
                    const currentUrl = new URL(window.location.href);
                    const params = currentUrl.searchParams;

                    // Eliminar el parámetro 'page' si existe, para que siempre empiece en 1
                    params.delete('page');
                    // Eliminar el parámetro 'highlight' si existe
                     params.delete('highlight');


                    // Construir la nueva URL con los parámetros actualizados y la acción del formulario
                    const formAction = searchForm.getAttribute('action');
                    const newUrl = new URL(formAction, window.location.origin);

                    // Copiar parámetros del formulario a la nueva URL
                    const formData = new FormData(searchForm);
                     for (const [key, value] of formData.entries()) {
                        // No añadir inputs hidden vacíos creados para checkboxes
                        if (!(searchForm.querySelector(`input[name="${key}"][type="hidden"]`) && value === '')) {
                             newUrl.searchParams.append(key, value);
                        }
                    }

                    // Reiniciar completamente la búsqueda: no arrastrar parámetros anteriores de la URL

                    // Redireccionar a la nueva URL
                    window.location.href = newUrl.toString();

                    // Prevenir el envío por defecto del formulario
                    event.preventDefault();
                });
            }
             // Resaltar usuario si el parámetro 'highlight' está en la URL al cargar la página
             const urlParams = new URLSearchParams(window.location.search);
             const highlightUserId = urlParams.get('highlight');
             if (highlightUserId) {
                 // Usar un pequeño retraso para asegurar que las tarjetas se han renderizado
                 setTimeout(() => {
                     highlightUserCard(parseInt(highlightUserId));
                 }, 200); // Ajusta el retraso si es necesario
             }
        });
    </script>
</head>

<body>
<script>
// Global variable to store all users data for the map, matching the current filtered results (all pages)
var allUsersData = <?php
    $mapUsers = array();
    if ($form_submitted || $highlight_user > 0) {
        // Usar los mismos filtros que en resultados, sin LIMIT
        $map_query = "
            SELECT m.orden, m.nombre, m.Gpslat, m.Gpslng,
                (ACOS(SIN(RADIANS(m.Gpslat)) * SIN(RADIANS($latitud1)) +
                    COS(RADIANS(m.Gpslat)) * COS(RADIANS($latitud1)) *
                    COS(RADIANS(m.Gpslng) - RADIANS($longitud1))) * 6378) AS distanciaPunto1Punto2
            FROM mentor2009 m
            WHERE $where_clause
              AND m.Gpslat <> 0 AND m.Gpslng <> 0
            " . (!empty($filtro_distancia) ? $filtro_distancia : "") . "
            ORDER BY distanciaPunto1Punto2 ASC";
    } else {
        // Búsqueda por defecto (sin filtros): mismo criterio que la lista
        $map_query = "
            SELECT m.orden, m.nombre, m.Gpslat, m.Gpslng,
                (ACOS(SIN(RADIANS(m.Gpslat)) * SIN(RADIANS($latitud1)) +
                    COS(RADIANS(m.Gpslat)) * COS(RADIANS($latitud1)) *
                    COS(RADIANS(m.Gpslng) - RADIANS($longitud1))) * 6378) AS distanciaPunto1Punto2
            FROM mentor2009 m
            WHERE (m.Sexo = 'M' OR m.Sexo = 'F')
              AND m.Gpslat <> 0 AND m.Gpslng <> 0
            HAVING distanciaPunto1Punto2 < $radio
            ORDER BY distanciaPunto1Punto2 ASC";
    }

    // BULK FETCH STRATEGY FOR MAP LANGUAGES (Optimized)
    // 1. Collect all Map User IDs
    $mapUsersTemp = [];
    $mapUserIds = [];
    
    if ($res = mysqli_query($link, $map_query)) {
        while ($u = mysqli_fetch_assoc($res)) {
            $u_id_map = (int)$u['orden'];
            $mapUserIds[] = $u_id_map;
            
             // Resolver la foto existente (jpg/png/gif/bmp) o usar default
            $thumb_base = "../uploader/upload_pic/thumb_" . $u_id_map;
            $photo_path = "";
            foreach (['.jpg','.png','.gif','.bmp'] as $ext) {
                if (file_exists($thumb_base . $ext)) { $photo_path = $thumb_base . $ext; break; }
            }
            if ($photo_path === "") { $photo_path = "../uploader/default.jpg"; }

            $mapUsersTemp[$u_id_map] = array(
                'id' => $u_id_map,
                'name' => $u['nombre'],
                'lat' => (float)$u['Gpslat'],
                'lng' => (float)$u['Gpslng'],
                'photo' => $photo_path,
                'teaches' => [], // Will be populated in bulk
                'learns' => []
            );
        }
        mysqli_free_result($res);
    }
    
    // 2. Bulk Fetch Languages if we have users
    if (!empty($mapUserIds)) {
        $id_list = implode(',', $mapUserIds);
        
        // Teaches (my_langs)
        // Group by user, distinct codes. Using simple query + PHP processing to ensure uniqueness
        $q_t = "SELECT ml.id AS user_id, l.Id AS lang_code 
                FROM my_langs ml 
                JOIN languages_names l ON ml.lang_id = l.Id 
                WHERE ml.id IN ($id_list)";
        
        if ($bulk_res_t = mysqli_query($link, $q_t)) {
            while ($row = mysqli_fetch_assoc($bulk_res_t)) {
                $uid = (int)$row['user_id'];
                $code = strtolower($row['lang_code']);
                // Note: User requested NO 'val'->'cat' conversion. We stick to DB value.
                if (isset($mapUsersTemp[$uid])) {
                     $mapUsersTemp[$uid]['teaches'][] = $code;
                }
            }
            mysqli_free_result($bulk_res_t);
        }

        // Learns (learn_langs)
        $q_l = "SELECT ll.id AS user_id, l.Id AS lang_code 
                FROM learn_langs ll 
                JOIN languages_names l ON ll.lang_id = l.Id 
                WHERE ll.id IN ($id_list)";
        
        if ($bulk_res_l = mysqli_query($link, $q_l)) {
            while ($row = mysqli_fetch_assoc($bulk_res_l)) {
                $uid = (int)$row['user_id'];
                $code = strtolower($row['lang_code']);
                if (isset($mapUsersTemp[$uid])) {
                     $mapUsersTemp[$uid]['learns'][] = $code;
                }
            }
            mysqli_free_result($bulk_res_l);
        }
    }

    // 3. Finalize and Deduplicate
    foreach ($mapUsersTemp as &$mUser) {
        $mUser['teaches'] = array_values(array_unique($mUser['teaches']));
        $mUser['learns'] = array_values(array_unique($mUser['learns']));
        $mapUsers[] = $mUser;
    }
     // $mapUsersTemp = null; // Free memory
    echo json_encode($mapUsers);
?>;

// Current page users (for highlighting in the map)
// This array is used by the map's JS to know which markers to potentially highlight
var currentPageUsers = [<?php echo implode(',', $orden_usuarios); ?>];

// Current page number
var currentPage = <?php echo $page; ?>;

// Total pages
var totalPages = <?php echo $total_pages; ?>;


// Función para resaltar una tarjeta de usuario
function highlightUserCard(userId, fromMap = false) {
    // Quitar resaltado de todas las fichas
    document.querySelectorAll('.user-card').forEach(card => {
        card.classList.remove('active');
    });

    // Resaltar la ficha correspondiente
    const userCard = document.getElementById('user-' + userId);
    if (userCard) {
        userCard.classList.add('active');
        // Desplazarse a la vista de la tarjeta
        userCard.scrollIntoView({ behavior: 'smooth', block: 'center'});

        if (!fromMap) {
            // Notificar al mapa para resaltar el marcador (funciona tanto con iframe como embebido)
            try {
                // Caso 1: mismo documento (search-form embebido)
                if (typeof highlightMarkerOnMap === 'function') {
                    highlightMarkerOnMap(userId);
                }
                // Caso 2: mapa dentro de un iframe
                const mapIframe = document.querySelector('iframe[src*="search-form.php"]');
                if (mapIframe && mapIframe.contentWindow && mapIframe.contentWindow.highlightMarkerOnMap) {
                    mapIframe.contentWindow.highlightMarkerOnMap(userId);
                }
            } catch (e) {
                console.error('Error al comunicarse con el mapa:', e);
            }
        }

        return true; // Usuario encontrado y resaltado en la página actual
    }

    // Si no encontramos la tarjeta en la página actual, no hacemos nada aquí.
    // La lógica PHP ya se encargó de redirigir si el usuario estaba en otra página.
    // Si llegamos aquí y la tarjeta no existe, es que el usuario no está en la página actual
    // O hubo un error. La lógica PHP debería haber redirigido en el primer caso.
    console.log("User card for ID " + userId + " not found on the current page.");
    return false;
}


// Escuchar mensajes del iframe del mapa
window.addEventListener('message', function(event) {
    // Verificar el origen del mensaje si es posible para mayor seguridad
    // if (event.origin !== 'your-expected-origin') return;

    if (event.data && event.data.action === 'highlightUser') {
        const userId = event.data.userId;
        // Modificación: Intentar resaltar en la página actual sin recargar
        const found = highlightUserCard(userId, true);
        
        if (!found) {
             console.log("Usuario " + userId + " no encontrado en la página actual. Recarga desactivada por preferencia.");
        }
    }
});

    // Añadir eventos de clic a las tarjetas de usuario
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.user-card').forEach(card => {
            // Asegurarse de no duplicar el evento si ya hay uno en el div > first-child
            card.addEventListener('click', function(e) {
                 // Si el clic fue en el primer div (que tiene su propio evento para abrir perfil), no hacemos nada más aquí.
                 if (e.target.closest('.user-card > div:first-child')) {
                     return;
                 }

                const userId = this.getAttribute('data-user-id');
                if (userId) {
                    // Cuando se hace clic en la tarjeta, también queremos resaltarla Y notificar al mapa.
                    // Ya está implementado, pero aseguramos la comunicación bidireccional.
                    highlightUserCard(parseInt(userId));

                     // Comunicarse con el iframe del mapa para que resalte el marcador
                    try {
                         const mapIframe = document.querySelector('iframe[src*="search-form.php"]');
                         if (mapIframe && mapIframe.contentWindow && mapIframe.contentWindow.highlightMarkerOnMap) {
                             mapIframe.contentWindow.highlightMarkerOnMap(parseInt(userId));
                         }
                     } catch (e) {
                         console.error('Error al comunicarse con el mapa desde la tarjeta:', e);
                     }
                     
                     // Evitar que el evento llegue al document y deseleccione la tarjeta inmediatamente
                     e.stopPropagation();
                }
            });
        });

        // Deseleccionar tarjetas al hacer clic fuera de cualquier tarjeta
        document.addEventListener('click', function(event) {
            const clickedCard = event.target.closest('.user-card');
            if (!clickedCard) {
                document.querySelectorAll('.user-card').forEach(card => {
                    card.classList.remove('active');
                });
            }
        });
    });
</script>
    <div class='marco ui-widget-content' style="display: flex;">
        <nav style="width:20%;text-align:left;padding: 13px;">
            <?php
            $user_languages_js = [];
            for ($i = 0; $i < $number_of_affected_users; $i++) {
                if (isset($orden_usuarios[$i])) {
                    $orden_usu = $orden_usuarios[$i];

                    $teaches_langs = isset($my_langs_full_name_array_multidim[$orden_usu]) ? $my_langs_full_name_array_multidim[$orden_usu] : [];
                    $learns_langs = isset($learn_langs_full_name_array_multidim[$orden_usu]) ? $learn_langs_full_name_array_multidim[$orden_usu] : [];
                    
                    // Fix 'val' -> 'cat' for map
                    $teaches_langs = array_map(function($l) { return (strtolower($l) == 'val' || strtolower($l) == 'valencian') ? 'cat' : $l; }, $teaches_langs);
                    $learns_langs = array_map(function($l) { return (strtolower($l) == 'val' || strtolower($l) == 'valencian') ? 'cat' : $l; }, $learns_langs);

                    $user_languages_js[$orden_usu] = [
                        'teaches' => $teaches_langs,
                        'learns' => $learns_langs
                    ];
                }
            }

            echo "<script>var userLanguagesData = " . json_encode($user_languages_js) . ";</script>";
            
            // Fix: Define $current_distance for search-form.php to avoid JS error
            $current_distance = isset($radio) ? $radio : 150;
            $skip_search_form_map_query = true; // Optimization: data is provided by index_paginated.php via allUsersData
            ?>
            <?php require_once('search-form.php'); ?>
        </nav>
        <section>
            <h1>Resultados</h1>
            <div class="results-info">
                <p>Found <?php echo $total_rows; ?> users. Showing page <?php echo $page; ?> of
                    <?php echo $total_pages; ?>.
                </p>
            </div>

            <!-- Pagination controls at the TOP of the page -->
            <?php if ($number_of_affected_users > 0 && $total_pages > 1): ?>
                <?php renderPagination($page, $total_pages, $_GET); ?>
            <?php endif; ?>

            <div class='marco-fichas'>
                <?php
                if ($number_of_affected_users > 0) {
                    $lista_niveles_mostrar = array(
                        "0" => "?",
                        "1" => "zero",
                        "2" => "A1",
                        "3" => "A2",
                        "4" => "B1",
                        "5" => "B2",
                        "6" => "C1",
                        "7" => "C2",
                    );

                    $lista_de_orgs = array();
                    $query_orgs = "SELECT * FROM organizations WHERE 1";
                    $result_orgs = mysqli_query($link, $query_orgs);
                     if ($result_orgs) {
                        $number_of_orgs = mysqli_num_rows($result_orgs);

                        for ($iiii = 0; $iiii < $number_of_orgs; $iiii++) {
                            $fila_orgs = mysqli_fetch_array($result_orgs);
                            $o_id = $fila_orgs['organization_id'];
                            $o_name = $fila_orgs['organization_name'];
                            $lista_de_orgs[$o_id] = "$o_name";
                        }
                        mysqli_free_result($result_orgs);
                    }


                    for ($i = 0; $i < $number_of_affected_users; $i++):
                        $orden_usu = $orden_usuarios[$i];
                        $nombreusu = $nameuser_original[$i];                        
						
						$myvalue = $nombreusu;
						$arr = explode(' ', trim($myvalue));
						$nombreusu = $arr[0];

						$nombreusu = ucfirst(substr($nombreusu, 0, 13));
						
						$organizac = isset($lista_de_orgs[$organiz_id[$i]]) ? $lista_de_orgs[$organiz_id[$i]] : "Unknown";
						$distancia12 = $distancia111[$i];
                        $num_evalu = $array_num_evalu[$i];
                        $nota_evalu = $array_nota_evalu[$i];
                        $lat_usuario = $lat_usuarios[$i];
                        $lng_usuario = $lng_usuarios[$i];
                        ?>

                        <div class='ficha user-card' id='user-<?php echo $orden_usu; ?>' data-lat='<?php echo $lat_usuario; ?>'
                            data-lng='<?php echo $lng_usuario; ?>' data-user-id='<?php echo $orden_usu; ?>' data-user-name='<?php echo htmlspecialchars($nombreusu); ?>'>
                            <div onclick="var w = window.open('../user/u.php?identificador=<?php echo $orden_usu; ?>', '_blank'); if (w) { w.opener = null; }">
                                <?php
                                $image_path = "../uploader/upload_pic/thumb_$orden_usu.jpg";
                                $default_image_path = "../uploader/default.jpg";
                                $image_to_show = file_exists($image_path) ? $image_path : $default_image_path;
                                echo "<img src='$image_to_show' />";
                                ?>
                                <span>
                                    <span class="user-name-highlight" style="font-weight:bold;"><?php echo $nombreusu; ?></span> #<?php echo $orden_usu; ?>
                                    <span style="margin-left:18px;">
                                    <?php if ($num_evalu > 0): ?>
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640" style="height: 1em; vertical-align: -0.125em;" fill="#888" class="ranking-icon"><!--!Font Awesome Free v7.1.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path d="M353.8 118.1L330.2 70.3C326.3 62 314.1 61.7 309.8 70.3L286.2 118.1L233.9 125.6C224.6 127 220.6 138.5 227.5 145.4L265.5 182.4L256.5 234.5C255.1 243.8 264.7 251 273.3 246.7L320.2 221.9L366.8 246.3C375.4 250.6 385.1 243.4 383.6 234.1L374.6 182L412.6 145.4C419.4 138.6 415.5 127.1 406.2 125.6L353.9 118.1zM288 320C261.5 320 240 341.5 240 368L240 528C240 554.5 261.5 576 288 576L352 576C378.5 576 400 554.5 400 528L400 368C400 341.5 378.5 320 352 320L288 320zM80 384C53.5 384 32 405.5 32 432L32 528C32 554.5 53.5 576 80 576L144 576C170.5 576 192 554.5 192 528L192 432C192 405.5 170.5 384 144 384L80 384zM448 496L448 528C448 554.5 469.5 576 496 576L560 576C586.5 576 608 554.5 608 528L608 496C608 469.5 586.5 448 560 448L496 448C469.5 448 448 469.5 448 496z"/></svg><?php echo $num_evalu; ?> (<?php echo round($nota_evalu * 100); ?>%)
                                    <?php endif; ?>
                                    <div style="font-size:13px;color:#888;margin-top:2px;">
                                    <?php echo $distancia12; ?> km
                                    <?php if ($organiz_id[$i] == $fila_user1['id_org'] && $organiz_id[$i] != 0): ?>
                                        <br>
                                        <br>
                                        &nbsp;<?php echo htmlspecialchars($organizac); ?>
                                    <?php endif; ?>
                                    
                                    </div>
                                    
                                </span>
                               
                            </div>
                            <div class="user-speaks">
                                <span>Speaks:</span>
                                <?php
                                if (isset($my_langs_array_multidim[$orden_usu]) && is_array($my_langs_array_multidim[$orden_usu])) {
                                    for ($sss = 0; $sss < count($my_langs_array_multidim[$orden_usu]); $sss++) {
                                        ?>
                                        <div>
                                            <?php
                                            $codig_dos_letras = $my_langs_2letters_array_multidim[$orden_usu][$sss];
                                            $bandera_path = "./banderasseparadas2024/$codig_dos_letras.png";
                                            if (!file_exists($bandera_path)) {
                                                $bandera_path = "./banderasseparadas2024/placeholder.png";
                                            }

                                            // Obtener las 3 primeras letras del idioma
                                            $lang_full_name = $my_langs_full_name_array_multidim[$orden_usu][$sss];
                                            $lang_short = $my_langs_array_multidim[$orden_usu] [$sss];				//empty($lang_full_name) ? strtoupper(substr($lang_full_name, 0, 3)) : '?';
                                            if (strtolower($lang_short) == 'val') { $lang_short = 'cat'; }

                                            ?>
                                            <img src="<?php echo $bandera_path; ?>" />
                                            <img src="../user/images/language_levels/<?php echo $my_langs_level_image_array_multidim[$orden_usu][$sss]; ?>"
                                                alt="Nivel <?php echo $my_langs_level_array_multidim[$orden_usu][$sss]; ?>" />
                                            <span><?php echo $lang_short; ?></span>
                                        </div>
                                        <?php
                                    }
                                } else {
                                    echo "<div>No languages specified</div>";
                                }
                                ?>
                            </div>

                            <div class="user-learning">
                                <span>Learns:</span>
                                <?php
                                if (isset($learn_langs_array_multidim[$orden_usu]) && is_array($learn_langs_array_multidim[$orden_usu])) {
                                    for ($sss = 0; $sss < count($learn_langs_array_multidim[$orden_usu]); $sss++) {
                                        ?>
                                        <div>
                                            <?php
                                            $codig_dos_letras = $learn_langs_2letters_array_multidim[$orden_usu][$sss];
                                            $bandera_path = "./banderasseparadas2024/$codig_dos_letras.png";
                                            if (!file_exists($bandera_path)) {
                                                $bandera_path = "./banderasseparadas2024/placeholder.png";
                                            }

                                            // Obtener las 3 primeras letras del idioma
                                            $lang_full_name = $learn_langs_full_name_array_multidim[$orden_usu][$sss];
                                             $lang_short = $learn_langs_array_multidim[$orden_usu] [$sss];             //!empty($lang_full_name) ? strtoupper(substr($lang_full_name, 0, 3)) : '?';
                                             if (strtolower($lang_short) == 'val') { $lang_short = 'cat'; }
                                            ?>
                                            <img src="<?php echo $bandera_path; ?>" />
                                            <img src="../user/images/language_levels/<?php echo $learn_langs_level_image_array_multidim[$orden_usu][$sss]; ?>"
                                                alt="" />
                                            <span><?php echo $lang_short; ?></span>
                                        </div>
                                        <?php
                                    }
                                } else {
                                    echo "<div>No languages specified</div>";
                                }
                                ?>
                            </div>

                            <div><?php echo " ";?></div>
                            <div><?php echo " ";?></div>
                            <div><?php echo " ";?></div>
                            <div><?php echo " ";?></div>
                        </div>

                    <?php endfor;
                } else {
                    echo "<p>No users found matching your criteria.</p>";
                }
                ?>
            </div>

            <!-- Pagination links at the BOTTOM of the page -->
            <?php if ($number_of_affected_users > 0 && $total_pages > 1): ?>
                <?php renderPagination($page, $total_pages, $_GET); ?>
            <?php endif; ?>
        </section>
    </div>
    <?php require('../templates/footer.php'); ?>
<script>
    // ... Existing JavaScript code ...

    // La función highlightUserCard se ha modificado arriba en el bloque de código grande.
    // Asegúrate de que esa versión es la que se utiliza.
 
    // Ya tenemos el evento para escuchar mensajes del iframe y manejar el 'highlightUser'
    // en el bloque de script grande.


</script>
</body>

</html>