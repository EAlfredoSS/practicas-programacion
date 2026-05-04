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
    $basePeople = "1=1";
    $profileType = isset($_GET['partner']) && $_GET['partner'] === 'teacher' ? 'teachers' : (isset($_GET['partner']) && $_GET['partner'] === 'student' ? 'students' : 'all');
    $user_where_clause = $basePeople;

    $ismale   = isset($_GET['male']) ? $_GET['male'] : '';
    $isfemale = isset($_GET['female']) ? $_GET['female'] : '';
    if ($ismale == "on" && $isfemale != "on") {
        $user_where_clause .= " AND m.Sexo='M'";
    } elseif ($isfemale == "on" && $ismale != "on") {
        $user_where_clause .= " AND m.Sexo='F'";
    } elseif ($ismale == "on" && $isfemale == "on") {
        $user_where_clause .= " AND (m.Sexo='M' OR m.Sexo='F')";
    }

    $userislearning = isset($_GET['learns']) ? $_GET['learns'] : '';
    if (!empty($userislearning)) {
        if ($profileType === 'teachers') {
            $user_where_clause .= " AND EXISTS (SELECT 1 FROM my_langs WHERE id=m.orden AND lang_id='" . mysqli_real_escape_string($link, $userislearning) . "')";
        } elseif ($profileType === 'students') {
            $user_where_clause .= " AND EXISTS (SELECT 1 FROM learn_langs WHERE id=m.orden AND lang_id='" . mysqli_real_escape_string($link, $userislearning) . "')";
        } else {
            $user_where_clause .= " AND (EXISTS (SELECT 1 FROM my_langs WHERE id=m.orden AND lang_id='" . mysqli_real_escape_string($link, $userislearning) . "')
                                      OR EXISTS (SELECT 1 FROM learn_langs WHERE id=m.orden AND lang_id='" . mysqli_real_escape_string($link, $userislearning) . "'))";
        }
    }

    $my_langs_array_multidim = array();

    $global_search = isset($_GET['zone']) && $_GET['zone'] == 'on';
    $distancias_permitidas = [1, 5, 10, 20, 50];
    $radio = isset($_GET['distance']) ? (int)$_GET['distance'] : 5;
    $user_filtro_distancia = "";
    if (!$global_search && in_array($radio, $distancias_permitidas)) {
        $user_filtro_distancia = "HAVING distanciaPunto1Punto2<$radio";
    } else if (!$global_search) {
        $user_where_clause = $basePeople . " AND (m.Sexo='M' OR m.Sexo='F')";
        $user_filtro_distancia = "HAVING distanciaPunto1Punto2<150";
    }

    $position_query = "
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

require('../templates/header_simplified.html');
require('../funcionesphp/funciones_idiomas_usuario.php');
require('../files/bd.php');

if (isset($_SESSION['orden2017']) && is_numeric($_SESSION['orden2017'])) {
    $identificador2017 = $_SESSION['orden2017'];
    $_SESSION['idusuario2019'] = $identificador2017;
} else {
    die("You are not logged in.");
}

$query_user1 = "SELECT * FROM mentor2009 WHERE orden = $identificador2017";
$result_user1 = mysqli_query($link, $query_user1);

if (!mysqli_num_rows($result_user1)) {
    die("<br/>.........No user......<br/>");
}

$fila_user1 = mysqli_fetch_array($result_user1);
$latitud1 = $fila_user1['Gpslat'];
$longitud1 = $fila_user1['Gpslng'];

$perPage = 30;
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($page - 1) * $perPage;

$select_price_clause = "m.*, ";
$order_by_sql = "m.lastaction DESC, distanciaPunto1Punto2 ASC";

$show_city_notice = false;

if ($latitud1 == 0 && $longitud1 == 0) {
    $latitud1 = 51.477928;
    $longitud1 = 0;
    $show_city_notice = true;
}

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

$form_submitted = isset($_GET['learns']) || isset($_GET['teaches']) || !empty($_GET['male']) || !empty($_GET['female']) || isset($_GET['orgs']) || !empty($_GET['distance']);
$global_search = isset($_GET['zone']) && $_GET['zone'] == 'on';

if ($form_submitted || $highlight_user > 0) {
    $userislearning = isset($_GET['learns']) ? $_GET['learns'] : '';
    $useristeaching = isset($_GET['teaches']) ? $_GET['teaches'] : '';

    if (!function_exists('map_to_lang_code3')) {
        function map_to_lang_code3($val, $link)
        {
            $v = trim((string)$val);
            if ($v === '') return '';
            $v3 = strtolower($v);
            if ($v3 === 'catalan' || $v3 === 'valenciano' || $v3 === 'valencian') {
                return 'cat';
            }
            if (preg_match('/^[a-z]{3}$/', $v3)) {
                return $v3;
            }
            $v_esc = mysqli_real_escape_string($link, $v);
            $sql1 = "SELECT Id FROM languages_names WHERE Print_Name = '$v_esc' LIMIT 1";
            if ($res1 = mysqli_query($link, $sql1)) {
                if ($row1 = mysqli_fetch_assoc($res1)) return strtolower($row1['Id']);
            }
            $sql2 = "SELECT Id FROM languages_names WHERE LOWER(Print_Name) = LOWER('$v_esc') LIMIT 1";
            if ($res2 = mysqli_query($link, $sql2)) {
                if ($row2 = mysqli_fetch_assoc($res2)) return strtolower($row2['Id']);
            }
            $sql3 = "SELECT Id FROM languages_names WHERE Print_Name LIKE '%$v_esc%' ORDER BY Print_Name LIMIT 1";
            if ($res3 = mysqli_query($link, $sql3)) {
                if ($row3 = mysqli_fetch_assoc($res3)) return strtolower($row3['Id']);
            }
            return $v;
        }
    }

    $userislearning = map_to_lang_code3($userislearning, $link);
    $useristeaching = map_to_lang_code3($useristeaching, $link);

    $minimumlevel_userislearning = isset($_GET['min_level']) ? (int)$_GET['min_level'] : 1;
    $maximumlevel_userislearning = isset($_GET['max_level']) ? (int)$_GET['max_level'] : 7;

    $use_learns_level_filter = isset($_GET['use_learns_level_filter']) && $_GET['use_learns_level_filter'] === 'on';
    $use_teaches_level_filter = isset($_GET['use_teaches_level_filter']) && $_GET['use_teaches_level_filter'] === 'on';

    $learns_min_level  = isset($_GET['min_learns_level'])  ? (int)$_GET['min_learns_level']  : (isset($_GET['learns_min_level'])  ? (int)$_GET['learns_min_level']  : null);
    $learns_max_level  = isset($_GET['max_learns_level'])  ? (int)$_GET['max_learns_level']  : (isset($_GET['learns_max_level'])  ? (int)$_GET['learns_max_level']  : null);
    $teaches_min_level = isset($_GET['min_teaches_level']) ? (int)$_GET['min_teaches_level'] : (isset($_GET['teaches_min_level']) ? (int)$_GET['teaches_min_level'] : null);
    $teaches_max_level = isset($_GET['max_teaches_level']) ? (int)$_GET['max_teaches_level'] : (isset($_GET['teaches_max_level']) ? (int)$_GET['teaches_max_level'] : null);

    if ($learns_min_level === null)  $learns_min_level  = $minimumlevel_userislearning;
    if ($learns_max_level === null)  $learns_max_level  = $maximumlevel_userislearning;

    $organizationslist = isset($_GET['orgs']) && is_array($_GET['orgs']) ? $_GET['orgs'] : [];
    $ismale   = isset($_GET['male'])   ? $_GET['male']   : '';
    $isfemale = isset($_GET['female']) ? $_GET['female'] : '';

    $sexo_query = '';
    if ($ismale === 'on' && $isfemale !== 'on') {
        $sexo_query = "m.Sexo='M'";
    } elseif ($isfemale === 'on' && $ismale !== 'on') {
        $sexo_query = "m.Sexo='F'";
    }

    if (isset($_GET['partner'])) {
        $where_clause = ($_GET['partner'] === 'student') ? "m.Pais<>'teacher' " : "1=1 ";
    } else {
        $where_clause = "1=1 ";
    }
    if (!empty($sexo_query)) $where_clause .= " AND ($sexo_query)";

    $where_orgs = '';
    $n_orgs = count($organizationslist);
    if ($n_orgs > 0) {
        $where_orgs = "AND (";
        for ($jjjj = 0; $jjjj < $n_orgs; $jjjj++) {
            $organizacion = intval($organizationslist[$jjjj]);
            $where_orgs .= " m.id_org=$organizacion";
            if ($jjjj < $n_orgs - 1) $where_orgs .= " OR";
        }
        $where_orgs .= ")";
    }
    if (!empty($where_orgs)) $where_clause .= " $where_orgs";

    $distancias_permitidas = [1, 5, 10, 20, 50, 150];
    $radio = isset($_GET['distance']) ? (int)$_GET['distance'] : 20;
    $filtro_distancia = "";
    if (!$global_search && in_array($radio, $distancias_permitidas)) {
        $filtro_distancia = "HAVING distanciaPunto1Punto2 < $radio";
    }

    if (!empty($useristeaching)) {
        $esc_teaches = mysqli_real_escape_string($link, $useristeaching);
        $level_condition_teaches_fallback = $use_teaches_level_filter
            ? " AND ll.level_id BETWEEN $teaches_min_level AND $teaches_max_level" : "";
        $where_clause .= " AND EXISTS (
            SELECT 1 FROM learn_langs ll
            JOIN languages1 lll ON lll.Id = ll.lang_id
            WHERE ll.id = m.orden
            AND (ll.lang_id = '$esc_teaches' OR lll.lang_id = '$esc_teaches')
            $level_condition_teaches_fallback
        )";
    }

    if (!empty($userislearning)) {
        $esc_learns = mysqli_real_escape_string($link, $userislearning);
        $level_condition_learns_fallback = $use_learns_level_filter
            ? " AND ml.level_id BETWEEN $learns_min_level AND $learns_max_level" : "";
        $where_clause .= " AND EXISTS (
            SELECT 1 FROM my_langs ml
            JOIN languages1 lml ON lml.Id = ml.lang_id
            WHERE ml.id = m.orden
            AND (ml.lang_id = '$esc_learns' OR lml.lang_id = '$esc_learns')
            $level_condition_learns_fallback
        )";
    }

    if (
        isset($_GET['partner']) && $_GET['partner'] === 'teacher' && !empty($userislearning)
        && isset($_GET['use_price_filter']) && $_GET['use_price_filter'] === 'on'
    ) {
        $price_min = isset($_GET['min_price']) ? (int)$_GET['min_price'] : (isset($_GET['price_min']) ? (int)$_GET['price_min'] : 0);
        $price_max = isset($_GET['max_price']) ? (int)$_GET['max_price'] : (isset($_GET['price_max']) ? (int)$_GET['price_max'] : 30);
        if ($price_max <= 0 || $price_max >= 30) $price_max = 1000000;
        $esc_learns = mysqli_real_escape_string($link, $userislearning);
        $where_clause .= " AND EXISTS (
            SELECT 1 FROM my_langs ml JOIN languages1 lml ON lml.Id = ml.lang_id
            WHERE ml.id = m.orden AND ml.for_share <> 0
              AND (ml.lang_id = '$esc_learns' OR lml.lang_id = '$esc_learns')
              AND (IFNULL(ml.lang_price,'') REGEXP '^[0-9]+([.,][0-9]+)?$'
                   AND CAST(REPLACE(ml.lang_price, ',', '.') AS DECIMAL(10,2)) BETWEEN $price_min AND $price_max)
        )";
    } elseif (isset($_GET['partner']) && $_GET['partner'] === 'teacher' && !empty($userislearning)) {
        $esc_learns = mysqli_real_escape_string($link, $userislearning);
        $where_clause .= " AND EXISTS (
            SELECT 1 FROM my_langs ml JOIN languages1 lml ON lml.Id = ml.lang_id
            WHERE ml.id = m.orden AND ml.for_share <> 0
              AND (ml.lang_id = '$esc_learns' OR lml.lang_id = '$esc_learns')
              AND (ml.lang_price IS NULL OR ml.lang_price = '' OR NOT(IFNULL(ml.lang_price,'') REGEXP '^[0-9]+([.,][0-9]+)?$'))
        )";
    }

    if (isset($_GET['partner']) && $_GET['partner'] === 'student' && !empty($userislearning) && !empty($useristeaching)) {
        $where_clause = "m.Pais<>'teacher' ";
        if (!empty($sexo_query)) $where_clause .= " AND ($sexo_query)";
        if (!empty($where_orgs))  $where_clause .= " $where_orgs";
        $esc_learns  = mysqli_real_escape_string($link, $userislearning);
        $esc_teaches = mysqli_real_escape_string($link, $useristeaching);
        $level_condition_teaches = $use_teaches_level_filter ? " AND ll.level_id BETWEEN $teaches_min_level AND $teaches_max_level" : "";
        $where_clause .= " AND EXISTS (SELECT 1 FROM learn_langs ll WHERE ll.id = m.orden AND ll.lang_id = '$esc_teaches' $level_condition_teaches)";
        $level_condition_learns = $use_learns_level_filter ? " AND ml.level_id BETWEEN $learns_min_level AND $learns_max_level" : "";
        $where_clause .= " AND EXISTS (SELECT 1 FROM my_langs ml WHERE ml.id = m.orden AND ml.lang_id = '$esc_learns' $level_condition_learns)";
    }

    if (!isset($_GET['lang-compatibility'])) {
        $user_langs_query  = "SELECT lang_id FROM my_langs WHERE id = $identificador2017";
        $user_langs_result = mysqli_query($link, $user_langs_query);
        $user_langs = [];
        while ($row = mysqli_fetch_assoc($user_langs_result)) $user_langs[] = $row['lang_id'];
        mysqli_free_result($user_langs_result);

        if (!empty($user_langs)) {
            $escaped = array_map(fn($v) => mysqli_real_escape_string($link, $v), $user_langs);
            $user_langs_list = implode("','", $escaped);
            $where_clause .= " AND (
                EXISTS (SELECT 1 FROM my_langs ml WHERE ml.id = m.orden AND ml.lang_id IN ('$user_langs_list'))
                OR EXISTS (SELECT 1 FROM learn_langs ll WHERE ll.id = m.orden AND ll.lang_id IN ('$user_langs_list'))
            )";
        }
    }

    $count_query = "
        SELECT COUNT(*) as total FROM (
            SELECT m.orden,
                (ACOS(SIN(RADIANS(m.Gpslat)) * SIN(RADIANS($latitud1)) +
                    COS(RADIANS(m.Gpslat)) * COS(RADIANS($latitud1)) *
                    COS(RADIANS(m.Gpslng) - RADIANS($longitud1))) * 6378) AS distanciaPunto1Punto2
            FROM mentor2009 m WHERE $where_clause
            " . (!empty($filtro_distancia) ? $filtro_distancia : "") . "
        ) as subquery";

    $count_result = mysqli_query($link, $count_query);
    $total_rows   = mysqli_fetch_assoc($count_result)['total'];
    $total_pages  = ceil($total_rows / $perPage);
    $page   = max(1, min($page, $total_pages > 0 ? $total_pages : 1));
    $offset = ($page - 1) * $perPage;

    $order_by_sql      = "m.lastaction DESC, distanciaPunto1Punto2 ASC";
    $select_price_field = "";
    $orderParam = isset($_GET['orderresultsby']) ? $_GET['orderresultsby'] : '';

    if (in_array($orderParam, ['price_asc', 'price_desc']) && isset($_GET['partner']) && $_GET['partner'] === 'teacher' && !empty($userislearning)) {
        $dir        = ($orderParam === 'price_asc') ? 'ASC' : 'DESC';
        $esc_learns = mysqli_real_escape_string($link, $userislearning);
        $price_subquery = "(SELECT CAST(REPLACE(ml.lang_price, ',', '.') AS DECIMAL(10,2)) FROM my_langs ml JOIN languages1 lml ON lml.Id = ml.lang_id WHERE ml.id = m.orden AND (ml.lang_id = '$esc_learns' OR lml.lang_id = '$esc_learns') AND IFNULL(ml.lang_price,'') REGEXP '^[0-9]+([.,][0-9]+)?$' LIMIT 1)";
        $select_price_field = ", $price_subquery AS price_num";
        $order_by_sql = "$price_subquery $dir, distanciaPunto1Punto2 ASC";
    } elseif ($orderParam === 'more_evals') {
        $order_by_sql = "m.ev_num_diaria DESC, distanciaPunto1Punto2 ASC";
    } elseif ($orderParam === 'less_evals') {
        $order_by_sql = "m.ev_num_diaria ASC, distanciaPunto1Punto2 ASC";
    } elseif ($orderParam === 'best_evals') {
        $order_by_sql = "m.ev_proporc_diaria DESC, distanciaPunto1Punto2 ASC";
    } elseif ($orderParam === 'distance') {
        $order_by_sql = "distanciaPunto1Punto2 ASC";
    } elseif ($orderParam === 'lastlogin') {
        $order_by_sql = "m.lastaction DESC, distanciaPunto1Punto2 ASC";
    } elseif ($orderParam === 'newest') {
        $order_by_sql = "m.orden DESC, distanciaPunto1Punto2 ASC";
    }

    $select_price_clause = "m.*" . $select_price_field . ", ";

    $query = "
        SELECT {$select_price_clause}
            (ACOS(SIN(RADIANS(m.Gpslat)) * SIN(RADIANS($latitud1)) +
                COS(RADIANS(m.Gpslat)) * COS(RADIANS($latitud1)) *
                COS(RADIANS(m.Gpslng) - RADIANS($longitud1))) * 6378) AS distanciaPunto1Punto2
        FROM mentor2009 m
        WHERE $where_clause
        " . (!empty($filtro_distancia) ? $filtro_distancia : "") . "
        ORDER BY $order_by_sql
        LIMIT $perPage OFFSET $offset";
} else {
    $distancias_permitidas_def = [1, 5, 10, 20, 50, 150];
    $radio = isset($_GET['distance']) ? (int)$_GET['distance'] : 20;
    if (!in_array($radio, $distancias_permitidas_def)) $radio = 20;

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
    $total_rows  = mysqli_fetch_assoc($count_result)['total'];
    $total_pages = ceil($total_rows / $perPage);
    $page   = max(1, min($page, $total_pages > 0 ? $total_pages : 1));
    $offset = ($page - 1) * $perPage;

    $query = "
        SELECT {$select_price_clause}
            (ACOS(SIN(RADIANS(m.Gpslat)) * SIN(RADIANS($latitud1)) +
                COS(RADIANS(m.Gpslat)) * COS(RADIANS($latitud1)) *
                COS(RADIANS(m.Gpslng) - RADIANS($longitud1))) * 6378) AS distanciaPunto1Punto2
        FROM mentor2009 m
        WHERE (m.Sexo = 'M' OR m.Sexo = 'F')
        HAVING distanciaPunto1Punto2 < $radio
        ORDER BY $order_by_sql
        LIMIT $perPage OFFSET $offset";
}

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
        array_push($orden_usuarios,   $orden_actual);
        array_push($nameuser,         $fila['nombre']);
        array_push($organiz_id,       $fila['id_org']);
        array_push($distancia111,     round($fila['distanciaPunto1Punto2'], 2));
        array_push($array_num_evalu,  $fila['ev_num_diaria']);
        array_push($array_nota_evalu, $fila['ev_proporc_diaria']);
        array_push($lat_usuarios,     $fila['Gpslat']);
        array_push($lng_usuarios,     $fila['Gpslng']);

        list($my_langs_array_multidim[$orden_actual], $my_langs_full_name_array_multidim[$orden_actual], $my_langs_level_array_multidim[$orden_actual], $my_langs_forshare_array_multidim[$orden_actual], $my_langs_price_array_multidim[$orden_actual], $my_langs_typeofexchange_array_multidim[$orden_actual], $my_langs_priceorexchangetext_array_multidim[$orden_actual], $my_langs_level_image_array_multidim[$orden_actual], $my_langs_2letters_array_multidim[$orden_actual]) = lenguas_que_conoce_usuario($orden_actual, $link);
        list($learn_langs_array_multidim[$orden_actual], $learn_langs_full_name_array_multidim[$orden_actual], $learn_langs_level_array_multidim[$orden_actual], $learn_langs_forshare_array_multidim[$orden_actual], $learn_langs_price_array_multidim[$orden_actual], $learn_langs_typeofexchange_array_multidim[$orden_actual], $learn_langs_priceorexchangetext_array_multidim[$orden_actual], $learn_langs_level_image_array_multidim[$orden_actual], $learn_langs_2letters_array_multidim[$orden_actual]) = lenguas_que_quiere_estudiar_usuario($orden_actual, $link);
    }
}

$nameuser_original = $nameuser;

function renderLevelDots($level)
{
    $level = intval($level);

    // Si nivel es 0 o no tiene valor: devolvemos 7 puntos grises
    if ($level <= 0) {
        $html = '<div class="level-dots">';
        for ($i = 1; $i <= 7; $i++) {
            $html .= '<span class="dot" style="background-color:#e0e0e0;"></span>';
        }
        $html .= '</div>';
        return $html;
    }

    // Nivel válido (1-7): colores Lingua2
    $level = min(7, $level);
    $colors = [
        1 => '#E10016', // Rojo
        2 => '#E10016', // Rojo
        3 => '#F14400', // Naranja
        4 => '#F14400', // Naranja
        5 => '#FED700', // Amarillo
        6 => '#1B9E00', // Verde
        7 => '#1B9E00'  // Verde
    ];

    $html = '<div class="level-dots">';
    for ($i = 1; $i <= 7; $i++) {
        $color = ($i <= $level) ? $colors[$level] : '#e0e0e0';
        $html .= '<span class="dot" style="background-color:' . $color . ';"></span>';
    }
    $html .= '</div>';
    return $html;
}

/**
 * Función mejorada para mostrar evaluaciones en francés con porcentaje en naranja
 * Si no hay evaluaciones, retorna null para no mostrar nada
 */
function getRatingDisplay($num_evalu, $nota_evalu)
{
    // Si no hay evaluaciones, retornar null (no mostrar nada)
    if ($num_evalu == 0) {
        return null;
    }

    // Determinar si $nota_evalu ya es un porcentaje (mayor que 1) o necesita conversión
    if ($nota_evalu > 1) {
        // Ya es un porcentaje (ej: 95), lo usamos directamente
        $percentage = round($nota_evalu);
    } else {
        // Es decimal (ej: 0.95), convertimos a porcentaje
        $percentage = round($nota_evalu * 100);
    }
    
    // Asegurar que nunca pase de 100
    $percentage = min(100, $percentage);

    // Singular/plural en francés
    $evaluation_text = $num_evalu . ' évaluation' . ($num_evalu > 1 ? 's' : '');

    // Formato: "1 évaluation 95%" (todo en una línea)
    return $evaluation_text . ' ' . $percentage . '%';
}

function renderPagination($page, $total_pages, $params)
{
    unset($params['page']);
    $highlight = isset($params['highlight']) ? $params['highlight'] : null;
    if ($highlight) $params['highlight'] = $highlight;
    $qs = http_build_query($params);
    $qs = !empty($qs) ? '&' . $qs : '';

    echo '<div class="pagination">';
    echo $page > 1
        ? "<a href='?page=" . ($page - 1) . "$qs'>« Prev</a>"
        : "<span class='disabled'>« Prev</span>";

    $sp = max(1, $page - 2);
    $ep = min($total_pages, $page + 2);

    if ($sp > 1) {
        echo "<a href='?page=1$qs'>1</a>";
        if ($sp > 2) echo "<span>…</span>";
    }
    for ($i = $sp; $i <= $ep; $i++) {
        echo $i == $page
            ? "<span class='active'>$i</span>"
            : "<a href='?page=$i$qs'>$i</a>";
    }
    if ($ep < $total_pages) {
        if ($ep < $total_pages - 1) echo "<span>…</span>";
        echo "<a href='?page={$total_pages}$qs'>$total_pages</a>";
    }
    echo $page < $total_pages
        ? "<a href='?page=" . ($page + 1) . "$qs'>Next »</a>"
        : "<span class='disabled'>Next »</span>";
    echo '</div>';
}
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="jquery-ui-1.13.3.custom/external/jquery/jquery.js"></script>
    <script src="jquery-ui-1.13.3.custom/jquery-ui.js"></script>
    <script src="https://unpkg.com/leaflet@1.6.0/dist/leaflet.js"></script>
    <link href="https://unpkg.com/leaflet@1.6.0/dist/leaflet.css" rel="stylesheet" />

    <style>
        :root {
            --accent: #e65f00;
            --accent-light: #fdf2e9;
            --text-dark: #2c3e50;
            --text-muted: #7f8c8d;
            --text-soft: #95a5a6;
            --bg-card: #ffffff;
            --bg-subtle: #f8f9fa;
            --border: #ecf0f1;
            --radius: 8px;
            --shadow-sm: 0 1px 3px rgba(0, 0, 0, 0.05);
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 0;
            background-color: #f7fafc;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }

        /* Header centrado */
        header,
        .header,
        .top-bar,
        nav {
            text-align: center;
            width: 100%;
        }

        .city-notice {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            max-width: 1400px;
            margin: 0 auto 12px;
            padding: 10px 20px;
            background: #fff9f0;
            border-left: 3px solid var(--accent);
            border-radius: 0 0 var(--radius) var(--radius);
            font-size: 13px;
            color: var(--text-muted);
        }

        .city-notice i {
            color: var(--accent);
        }

        .city-notice a {
            color: var(--accent);
            text-decoration: none;
        }

        .main-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 15px;
        }

        .content-wrapper {
            display: flex;
            gap: 20px;
            margin-top: 15px;
            align-items: flex-start;
        }

        /* ===== FILTROS SIDEBAR MEJORADO ===== */
        .filters-sidebar {
            width: 280px;
            flex-shrink: 0;
            background: var(--bg-card);
            border-radius: 16px;
            box-shadow: var(--shadow-sm);
            padding: 20px;
            border: 1px solid var(--border);
            transition: all 0.2s ease;
        }

        .filters-sidebar::before {
            content: '';
            display: block;
            width: 40px;
            height: 3px;
            background: var(--accent);
            border-radius: 3px;
            margin: 0 auto 16px auto;
        }

        .sidebar-title {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin-bottom: 20px;
            font-size: 18px;
            font-weight: 600;
            color: var(--text-dark);
            text-align: center;
            padding-bottom: 12px;
            border-bottom: 1px solid var(--border);
        }

        .sidebar-title i {
            color: var(--accent);
            font-size: 18px;
            background: var(--accent-light);
            padding: 8px;
            border-radius: 50%;
            width: 36px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .filters-sidebar form {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .results-content {
            flex: 1;
        }

        .results-header h1 {
            font-size: 22px;
            font-weight: 600;
            color: var(--text-dark);
            margin: 0 0 4px 0;
        }

        .results-info {
            font-size: 13px;
            color: var(--text-muted);
            margin: 0 0 10px 0;
        }

        .pagination {
            display: flex;
            flex-wrap: wrap;
            gap: 4px;
            margin: 10px 0;
        }

        .pagination a,
        .pagination span {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 34px;
            height: 34px;
            padding: 0 10px;
            font-size: 13px;
            text-decoration: none;
            color: var(--text-dark);
            background: white;
            border: 1px solid var(--border);
            border-radius: 4px;
        }

        .pagination a:hover {
            border-color: var(--accent);
            color: var(--accent);
        }

        .pagination .active {
            background: var(--accent);
            color: white;
            border-color: var(--accent);
        }

        /* ===== GRID DE TARJETAS - 3 COLUMNAS EN PC ===== */
        .cards-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-bottom: 16px;
        }

        .user-card {
            width: 100%;
            max-width: 100%;
            min-width: 0;
            display: flex;
            flex-direction: column;
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            box-shadow: var(--shadow-sm);
            overflow: hidden;
            transition: all 0.2s;
            cursor: pointer;
            position: relative;
        }

        .user-card::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 4px;
            background-color: var(--accent);
            border-radius: var(--radius) 0 0 var(--radius);
            transition: width 0.2s ease;
        }

        .user-card:hover::before {
            width: 6px;
        }

        .user-card:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            border-color: var(--accent);
        }

        .user-card.active {
            border: 2px solid var(--accent);
            background: #fffaf5;
        }

        .card-header {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            padding: 16px 16px 12px 20px;
            cursor: pointer;
        }

        .card-avatar {
            flex-shrink: 0;
            width: 56px;
            height: 56px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid var(--accent);
        }

        .card-identity {
            flex: 1;
            min-width: 0;
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .card-name-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            width: 100%;
        }

        .card-name {
            font-size: 16px;
            font-weight: 700;
            color: var(--text-dark);
            margin: 0;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            flex: 1;
            min-width: 0;
        }

        /* ===== ESTILO PARA EVALUACIONES EN NARANJA ===== */
        .rating-text {
            font-size: 13px;
            font-weight: 600;
            color: var(--accent); /* Color naranja */
            white-space: nowrap;
            flex-shrink: 0;
            background-color: #ffdbc1;
            border: 1px solid var(--accent);
            border-radius: 20px;
            padding: 4px 10px;
            margin-right: 20px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .card-distance {
            font-size: 12px;
            color: var(--text-muted);
            margin: 0;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .card-distance i {
            color: var(--accent);
            font-size: 10px;
            width: 14px;
        }

        .card-langs {
            flex: 1;
            padding: 0 16px 12px 20px;
            display: flex;
            flex-direction: column;
            gap: 14px;
        }

        .lang-section {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .lang-label {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            color: var(--text-soft);
        }

        .lang-label i {
            color: var(--accent);
            font-size: 10px;
            width: 14px;
        }

        .lang-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 8px;
            justify-items: center;
            width: 100%;
        }

        .flag-wrapper {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 4px;
            width: 100%;
        }

        .lang-item {
            width: 100%;
            display: flex;
            justify-content: center;
        }

        .lang-item img {
            width: 48px;
            height: 30px;
            object-fit: cover;
            border-radius: 4px;
            border: 1px solid var(--border);
        }

        .level-dots {
            display: flex;
            justify-content: center;
            gap: 2px;
            width: 100%;
        }

        .dot {
            width: 5px;
            height: 5px;
            border-radius: 50%;
        }

        .lang-empty {
            font-size: 12px;
            color: var(--text-soft);
            font-style: italic;
            grid-column: span 4;
            text-align: center;
        }

        .card-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 6px;
            padding: 8px 16px 12px 20px;
            border-top: 1px solid var(--border);
            background: var(--bg-subtle);
            margin-top: auto;
        }

        .card-footer-dist {
            font-size: 12px;
            color: var(--text-muted);
        }

        .card-org-badge {
            font-size: 11px;
            font-weight: 600;
            color: var(--accent);
            background: var(--accent-light);
            padding: 3px 10px;
            border-radius: 20px;
        }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 1100px) {
            .cards-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 700px) {
            .cards-grid {
                grid-template-columns: 1fr;
            }

            .content-wrapper {
                flex-direction: column;
            }

            .filters-sidebar {
                width: 100%;
            }

            .lang-grid {
                grid-template-columns: repeat(3, 1fr);
            }

            .lang-item img {
                width: 42px;
                height: 26px;
            }
        }

        @media (max-width: 480px) {
            .lang-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }
    </style>
</head>

<body>

    <?php if ($show_city_notice): ?>
        <div class="city-notice">
            <i class="fas fa-map-marker-alt"></i>
            <span>You have not indicated your city. <a href="../user/getgpsposition.php">Set it now</a> or we'll show London, UK as default.</span>
        </div>
    <?php endif; ?>

    <?php
    $user_languages_js = [];
    for ($i = 0; $i < $number_of_affected_users; $i++) {
        if (isset($orden_usuarios[$i])) {
            $orden_usu = $orden_usuarios[$i];
            $teaches_langs = isset($my_langs_full_name_array_multidim[$orden_usu]) ? $my_langs_full_name_array_multidim[$orden_usu] : [];
            $learns_langs  = isset($learn_langs_full_name_array_multidim[$orden_usu]) ? $learn_langs_full_name_array_multidim[$orden_usu] : [];
            $teaches_langs = array_map(fn($l) => (strtolower($l) === 'val' || strtolower($l) === 'valencian') ? 'cat' : $l, $teaches_langs);
            $learns_langs  = array_map(fn($l) => (strtolower($l) === 'val' || strtolower($l) === 'valencian') ? 'cat' : $l, $learns_langs);
            $user_languages_js[$orden_usu] = ['teaches' => $teaches_langs, 'learns' => $learns_langs];
        }
    }

    $current_distance = isset($radio) ? $radio : 150;
    $skip_search_form_map_query = true;

    $mapUsers = [];
    if ($form_submitted || $highlight_user > 0) {
        $map_query = "
        SELECT m.orden, m.nombre, m.Gpslat, m.Gpslng,
            (ACOS(SIN(RADIANS(m.Gpslat))*SIN(RADIANS($latitud1))+
             COS(RADIANS(m.Gpslat))*COS(RADIANS($latitud1))*COS(RADIANS(m.Gpslng)-RADIANS($longitud1)))*6378) AS distanciaPunto1Punto2
        FROM mentor2009 m
        WHERE $where_clause AND m.Gpslat<>0 AND m.Gpslng<>0
        " . (!empty($filtro_distancia) ? $filtro_distancia : "") . "
        ORDER BY distanciaPunto1Punto2 ASC";
    } else {
        $map_query = "
        SELECT m.orden, m.nombre, m.Gpslat, m.Gpslng,
            (ACOS(SIN(RADIANS(m.Gpslat))*SIN(RADIANS($latitud1))+
             COS(RADIANS(m.Gpslat))*COS(RADIANS($latitud1))*COS(RADIANS(m.Gpslng)-RADIANS($longitud1)))*6378) AS distanciaPunto1Punto2
        FROM mentor2009 m
        WHERE (m.Sexo='M' OR m.Sexo='F') AND m.Gpslat<>0 AND m.Gpslng<>0
        HAVING distanciaPunto1Punto2 < $radio
        ORDER BY distanciaPunto1Punto2 ASC";
    }

    $mapUsersTemp = [];
    $mapUserIds   = [];

    if ($res = mysqli_query($link, $map_query)) {
        while ($u = mysqli_fetch_assoc($res)) {
            $u_id_map   = (int)$u['orden'];
            $mapUserIds[] = $u_id_map;
            $thumb_base   = "../uploader/upload_pic/thumb_$u_id_map";
            $photo_path   = "";
            foreach (['.jpg', '.png', '.gif', '.bmp'] as $ext) {
                if (file_exists($thumb_base . $ext)) {
                    $photo_path = $thumb_base . $ext;
                    break;
                }
            }
            if ($photo_path === "") $photo_path = "../uploader/default.jpg";
            $mapUsersTemp[$u_id_map] = ['id' => $u_id_map, 'name' => $u['nombre'], 'lat' => (float)$u['Gpslat'], 'lng' => (float)$u['Gpslng'], 'photo' => $photo_path, 'teaches' => [], 'learns' => []];
        }
        mysqli_free_result($res);
    }

    if (!empty($mapUserIds)) {
        $id_list = implode(',', $mapUserIds);
        if ($bt = mysqli_query($link, "SELECT ml.id AS user_id,l.Id AS lang_code FROM my_langs ml JOIN languages_names l ON ml.lang_id=l.Id WHERE ml.id IN ($id_list)")) {
            while ($row = mysqli_fetch_assoc($bt)) {
                $uid = (int)$row['user_id'];
                if (isset($mapUsersTemp[$uid])) $mapUsersTemp[$uid]['teaches'][] = strtolower($row['lang_code']);
            }
            mysqli_free_result($bt);
        }
        if ($bl = mysqli_query($link, "SELECT ll.id AS user_id,l.Id AS lang_code FROM learn_langs ll JOIN languages_names l ON ll.lang_id=l.Id WHERE ll.id IN ($id_list)")) {
            while ($row = mysqli_fetch_assoc($bl)) {
                $uid = (int)$row['user_id'];
                if (isset($mapUsersTemp[$uid])) $mapUsersTemp[$uid]['learns'][] = strtolower($row['lang_code']);
            }
            mysqli_free_result($bl);
        }
    }

    foreach ($mapUsersTemp as &$mUser) {
        $mUser['teaches'] = array_values(array_unique($mUser['teaches']));
        $mUser['learns']  = array_values(array_unique($mUser['learns']));
        $mapUsers[] = $mUser;
    }
    ?>

    <script>
        var allUsersData = <?php echo json_encode($mapUsers); ?>;
        var currentPageUsers = [<?php echo implode(',', $orden_usuarios); ?>];
        var currentPage = <?php echo $page; ?>;
        var totalPages = <?php echo $total_pages; ?>;
        var userLanguagesData = <?php echo json_encode($user_languages_js); ?>;

        function highlightUserCard(userId, fromMap = false) {
            document.querySelectorAll('.user-card').forEach(c => c.classList.remove('active'));
            const card = document.getElementById('user-' + userId);
            if (card) {
                card.classList.add('active');
                card.scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                });
                if (!fromMap) {
                    try {
                        if (typeof highlightMarkerOnMap === 'function') highlightMarkerOnMap(userId);
                        const mf = document.querySelector('iframe[src*="search-form.php"]');
                        if (mf && mf.contentWindow && mf.contentWindow.highlightMarkerOnMap)
                            mf.contentWindow.highlightMarkerOnMap(userId);
                    } catch (e) {}
                }
                return true;
            }
            return false;
        }

        window.addEventListener('message', function(event) {
            if (event.data && event.data.action === 'highlightUser') {
                highlightUserCard(event.data.userId, true);
            }
        });

        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.user-card').forEach(card => {
                card.addEventListener('click', function(e) {
                    if (e.target.closest('.card-header')) return;
                    const userId = this.getAttribute('data-user-id');
                    if (!userId) return;
                    highlightUserCard(parseInt(userId));
                    e.stopPropagation();
                });
            });

            const urlParams = new URLSearchParams(window.location.search);
            const highlightUserId = urlParams.get('highlight');
            if (highlightUserId) {
                setTimeout(() => highlightUserCard(parseInt(highlightUserId)), 200);
            }
        });
    </script>

    <div class="main-container">
        <div class="content-wrapper">

            <aside class="filters-sidebar">
                <div class="sidebar-title">
                    <i class="fas fa-sliders-h"></i>
                    <span>Filter Partners</span>
                </div>
                <?php require_once('search-form.php'); ?>
            </aside>

            <section class="results-content">

                <div class="results-header">
                    <h1>Results</h1>
                    <p class="results-info">
                        Found <?php echo $total_rows; ?> users — page <?php echo $page; ?> of <?php echo $total_pages; ?>
                    </p>
                </div>

                <?php if ($number_of_affected_users > 0 && $total_pages > 1): ?>
                    <?php renderPagination($page, $total_pages, $_GET); ?>
                <?php endif; ?>

                <div class="cards-grid">
                    <?php
                    if ($number_of_affected_users > 0):

                        $lista_de_orgs = [];
                        if ($result_orgs = mysqli_query($link, "SELECT * FROM organizations WHERE 1")) {
                            while ($fo = mysqli_fetch_array($result_orgs))
                                $lista_de_orgs[$fo['organization_id']] = $fo['organization_name'];
                            mysqli_free_result($result_orgs);
                        }

                        for ($i = 0; $i < $number_of_affected_users; $i++):
                            $orden_usu   = $orden_usuarios[$i];
                            $arr         = explode(' ', trim($nameuser_original[$i]));
                            $nombreusu   = ucfirst(substr($arr[0], 0, 13));
                            $organizac   = $lista_de_orgs[$organiz_id[$i]] ?? 'Unknown';
                            $distancia12 = $distancia111[$i];
                            $num_evalu   = $array_num_evalu[$i];
                            $nota_evalu  = $array_nota_evalu[$i];

                            $image_path    = "../uploader/upload_pic/thumb_$orden_usu.jpg";
                            $image_to_show = file_exists($image_path) ? $image_path : "../uploader/default.jpg";
                            $same_org      = ($organiz_id[$i] == $fila_user1['id_org'] && $organiz_id[$i] != 0);

                            $rating_display = getRatingDisplay($num_evalu, $nota_evalu);
                    ?>

                            <div class="user-card" id="user-<?php echo $orden_usu; ?>" data-user-id="<?php echo $orden_usu; ?>">

                                <div class="card-header" onclick="window.open('../user/u.php?identificador=<?php echo $orden_usu; ?>','_blank')">
                                    <img class="card-avatar" src="<?php echo $image_to_show; ?>" alt="">
                                    <div class="card-identity">
                                        <div class="card-name-row">
                                            <p class="card-name"><?php echo $nombreusu; ?></p>
                                            <?php if ($rating_display !== null): ?>
                                                <span class="rating-text">
                                                    <?php echo $rating_display; ?>
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                        <p class="card-distance">
                                            <i class="fas fa-map-marker-alt"></i> <?php echo $distancia12; ?> km
                                        </p>
                                    </div>
                                </div>

                                <div class="card-langs">

                                    <div class="lang-section">
                                        <span class="lang-label">
                                            <i class="fas fa-comment"></i> Speaks
                                        </span>
                                        <div class="lang-grid">
                                            <?php
                                            if (!empty($my_langs_array_multidim[$orden_usu])):
                                                foreach ($my_langs_array_multidim[$orden_usu] as $sss => $lang_code_raw):
                                                    $codig_2   = $my_langs_2letters_array_multidim[$orden_usu][$sss];
                                                    $flag_path = file_exists("./banderasseparadas2024/$codig_2.png")
                                                        ? "./banderasseparadas2024/$codig_2.png"
                                                        : "./banderasseparadas2024/placeholder.png";
                                                    $level_value = $my_langs_level_array_multidim[$orden_usu][$sss];
                                            ?>
                                                    <div class="flag-wrapper">
                                                        <div class="lang-item">
                                                            <img src="<?php echo $flag_path; ?>" alt="">
                                                        </div>
                                                        <?php echo renderLevelDots($level_value); ?>
                                                    </div>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <span class="lang-empty">Not specified</span>
                                            <?php endif; ?>
                                        </div>
                                    </div>

                                    <div class="lang-section">
                                        <span class="lang-label">
                                            <i class="fas fa-graduation-cap"></i> Learns
                                        </span>
                                        <div class="lang-grid">
                                            <?php
                                            if (!empty($learn_langs_array_multidim[$orden_usu])):
                                                foreach ($learn_langs_array_multidim[$orden_usu] as $sss => $lang_code_raw):
                                                    $codig_2   = $learn_langs_2letters_array_multidim[$orden_usu][$sss];
                                                    $flag_path = file_exists("./banderasseparadas2024/$codig_2.png")
                                                        ? "./banderasseparadas2024/$codig_2.png"
                                                        : "./banderasseparadas2024/placeholder.png";
                                                    $level_value = $learn_langs_level_array_multidim[$orden_usu][$sss];
                                            ?>
                                                    <div class="flag-wrapper">
                                                        <div class="lang-item">
                                                            <img src="<?php echo $flag_path; ?>" alt="">
                                                        </div>
                                                        <?php echo renderLevelDots($level_value); ?>
                                                    </div>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <span class="lang-empty">Not specified</span>
                                            <?php endif; ?>
                                        </div>
                                    </div>

                                </div>

                                <div class="card-footer">
                                    <span class="card-footer-dist"><?php echo $distancia12; ?> km</span>
                                    <?php if ($same_org): ?>
                                        <span class="card-org-badge"><?php echo htmlspecialchars($organizac); ?></span>
                                    <?php endif; ?>
                                </div>

                            </div>

                        <?php endfor; ?>

                    <?php else: ?>
                        <p style="grid-column:1/-1; color:var(--text-muted); padding:20px 0; text-align:center;">
                            No users found matching your criteria.
                        </p>
                    <?php endif; ?>
                </div>

                <?php if ($number_of_affected_users > 0 && $total_pages > 1): ?>
                    <?php renderPagination($page, $total_pages, $_GET); ?>
                <?php endif; ?>

            </section>
        </div>
    </div>

    <?php require('../templates/footer.php'); ?>
</body>

</html>