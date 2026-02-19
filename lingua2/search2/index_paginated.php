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
            $user_where_clause .= " AND EXISTS (SELECT 1 FROM my_langs WHERE id=m.orden AND lang_id='".mysqli_real_escape_string($link,$userislearning)."')";
        } elseif ($profileType === 'students') {
            $user_where_clause .= " AND EXISTS (SELECT 1 FROM learn_langs WHERE id=m.orden AND lang_id='".mysqli_real_escape_string($link,$userislearning)."')";
        } else {
            $user_where_clause .= " AND (EXISTS (SELECT 1 FROM my_langs WHERE id=m.orden AND lang_id='".mysqli_real_escape_string($link,$userislearning)."')
                                      OR EXISTS (SELECT 1 FROM learn_langs WHERE id=m.orden AND lang_id='".mysqli_real_escape_string($link,$userislearning)."'))";
        }
    }

    $my_langs_array_multidim = array(); 

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

// Variable para controlar si mostrar el aviso de ciudad
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
        function map_to_lang_code3($val, $link) {
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
                if ($row1 = mysqli_fetch_assoc($res1)) {
                    return strtolower($row1['Id']);
                }
            }
            
            $sql2 = "SELECT Id FROM languages_names WHERE LOWER(Print_Name) = LOWER('$v_esc') LIMIT 1";
            if ($res2 = mysqli_query($link, $sql2)) {
                if ($row2 = mysqli_fetch_assoc($res2)) {
                    return strtolower($row2['Id']);
                }
            }
            
            $sql3 = "SELECT Id FROM languages_names WHERE Print_Name LIKE '%$v_esc%' ORDER BY Print_Name LIMIT 1";
            if ($res3 = mysqli_query($link, $sql3)) {
                if ($row3 = mysqli_fetch_assoc($res3)) {
                    return strtolower($row3['Id']);
                }
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

    $learns_min_level   = isset($_GET['min_learns_level']) ? (int)$_GET['min_learns_level'] : (isset($_GET['learns_min_level']) ? (int)$_GET['learns_min_level'] : null);
    $learns_max_level   = isset($_GET['max_learns_level']) ? (int)$_GET['max_learns_level'] : (isset($_GET['learns_max_level']) ? (int)$_GET['learns_max_level'] : null);
    $teaches_min_level  = isset($_GET['min_teaches_level']) ? (int)$_GET['min_teaches_level'] : (isset($_GET['teaches_min_level']) ? (int)$_GET['teaches_min_level'] : null);
    $teaches_max_level  = isset($_GET['max_teaches_level']) ? (int)$_GET['max_teaches_level'] : (isset($_GET['teaches_max_level']) ? (int)$_GET['teaches_max_level'] : null);
    
    if ($learns_min_level === null) $learns_min_level = $minimumlevel_userislearning;
    if ($learns_max_level === null) $learns_max_level = $maximumlevel_userislearning;

    $organizationslist = isset($_GET['orgs']) && is_array($_GET['orgs']) ? $_GET['orgs'] : [];
    $ismale = isset($_GET['male']) ? $_GET['male'] : '';
    $isfemale = isset($_GET['female']) ? $_GET['female'] : '';

    $sexo_query = '';
    if ($ismale === 'on' && $isfemale !== 'on') {
        $sexo_query = "m.Sexo='M'";
    } elseif ($isfemale === 'on' && $ismale !== 'on') {
        $sexo_query = "m.Sexo='F'";
    } else {
        $sexo_query = '';
    }

    if (isset($_GET['partner'])) {
        if ($_GET['partner'] === 'teacher') {
            $where_clause = "1=1 ";
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
            $organizacion = intval($organizationslist[$jjjj]);
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
    $radio = isset($_GET['distance']) ? (int) $_GET['distance'] : 20;

    $filtro_distancia = "";
    if (!$global_search && in_array($radio, $distancias_permitidas)) {
        $filtro_distancia = "HAVING distanciaPunto1Punto2 < $radio";
    }

    if (!empty($useristeaching)) {
        $esc_teaches = mysqli_real_escape_string($link, $useristeaching);
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

    if (!empty($userislearning)) {
        $esc_learns = mysqli_real_escape_string($link, $userislearning);
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

    if (isset($_GET['partner']) && $_GET['partner'] === 'teacher' && !empty($userislearning)
        && isset($_GET['use_price_filter']) && $_GET['use_price_filter'] === 'on') {
        $price_min = isset($_GET['min_price']) ? (int)$_GET['min_price'] : (int) $_GET['price_min'];
        $price_max = isset($_GET['max_price']) ? (int)$_GET['max_price'] : (int) $_GET['price_max'];
        
        if ($price_max <= 0 || $price_max >= 30) {
            $price_max = 1000000;
        }
        $esc_learns = mysqli_real_escape_string($link, $userislearning);
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
        
        $level_condition_teaches = "";
        if ($use_teaches_level_filter) {
             $level_condition_teaches = " AND ll.level_id BETWEEN $teaches_min_level AND $teaches_max_level";
        }
        $where_clause .= " AND EXISTS (SELECT 1 FROM learn_langs ll WHERE ll.id = m.orden AND ll.lang_id = '$esc_teaches' $level_condition_teaches)";

        $level_condition_learns = "";
        if ($use_learns_level_filter) {
             $level_condition_learns = " AND ml.level_id BETWEEN $learns_min_level AND $learns_max_level";
        }
        $where_clause .= " AND EXISTS (SELECT 1 FROM my_langs ml WHERE ml.id = m.orden AND ml.lang_id = '$esc_learns' $level_condition_learns)";
    }

    if (!isset($_GET['lang-compatibility'])) {
        $user_langs_query = "SELECT lang_id FROM my_langs WHERE id = $identificador2017";
        $user_langs_result = mysqli_query($link, $user_langs_query);
        $user_langs = [];
        while ($row = mysqli_fetch_assoc($user_langs_result)) {
            $user_langs[] = $row['lang_id'];
        }
        mysqli_free_result($user_langs_result);

        if (!empty($user_langs)) {
            $escaped = array_map(function($v) use ($link) { return mysqli_real_escape_string($link, $v); }, $user_langs);
            $user_langs_list = implode("','", $escaped);
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
    $page = max(1, min($page, $total_pages > 0 ? $total_pages : 1));
    $offset = ($page - 1) * $perPage;

    $order_by_sql = "m.lastaction DESC, distanciaPunto1Punto2 ASC";
    $select_price_field = "";
    $orderParam = isset($_GET['orderresultsby']) ? $_GET['orderresultsby'] : '';

    if (in_array($orderParam, ['price_asc','price_desc']) && isset($_GET['partner']) && $_GET['partner'] === 'teacher' && !empty($userislearning)) {
        $dir = $orderParam === 'price_asc' ? 'ASC' : 'DESC';
        $esc_learns = mysqli_real_escape_string($link, $userislearning);
        $price_subquery = "(SELECT CAST(REPLACE(ml.lang_price, ',', '.') AS DECIMAL(10,2)) FROM my_langs ml JOIN languages1 lml ON lml.Id = ml.lang_id WHERE ml.id = m.orden AND (ml.lang_id = '$esc_learns' OR lml.lang_id = '$esc_learns') AND IFNULL(ml.lang_price,'') REGEXP '^[0-9]+([.,][0-9]+)?$' LIMIT 1)";
        $price_expr = "$price_subquery";
        $select_price_field = ", $price_expr AS price_num";
        $order_by_sql = "$price_expr $dir, distanciaPunto1Punto2 ASC";
    }
    elseif ($orderParam === 'more_evals') {
        $order_by_sql = "m.ev_num_diaria DESC, distanciaPunto1Punto2 ASC";
    } elseif ($orderParam === 'less_evals') {
        $order_by_sql = "m.ev_num_diaria ASC, distanciaPunto1Punto2 ASC";
    }
    elseif ($orderParam === 'best_evals') {
        $order_by_sql = "m.ev_proporc_diaria DESC, distanciaPunto1Punto2 ASC";
    }
    elseif ($orderParam === 'distance') {
        $order_by_sql = "distanciaPunto1Punto2 ASC";
    }
    elseif ($orderParam === 'lastlogin') {
        $order_by_sql = "m.lastaction DESC, distanciaPunto1Punto2 ASC";
    } elseif ($orderParam === 'newest') {
        $order_by_sql = "m.orden DESC, distanciaPunto1Punto2 ASC";
    }

    $select_price_clause = "m.*";
    if ($select_price_field != '') {
        $select_price_clause .= $select_price_field;
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
    $page = max(1, min($page, $total_pages > 0 ? $total_pages : 1));
    $offset = ($page - 1) * $perPage;

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
}
 
$nameuser_original = $nameuser;

function renderPagination($page, $total_pages, $params)
{
    unset($params['page']);
    
    $highlight = isset($params['highlight']) ? $params['highlight'] : null;
    if ($highlight) {
        $params['highlight'] = $highlight;
    }
    
    $query_string = http_build_query($params);
    $query_string = !empty($query_string) ? '&' . $query_string : '';

    echo '<div class="pagination">';

    if ($page > 1) {
        echo "<a href='?page=" . ($page - 1) . $query_string . "'>« Previous</a>";
    } else {
        echo "<span class='disabled'>« Previous</span>";
    }

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

    if ($page < $total_pages) {
        echo "<a href='?page=" . ($page + 1) . $query_string . "'>Next »</a>";
    } else {
        echo "<span class='disabled'>Next »</span>";
    }

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
    <script src="jquery-ui-1.13.3.custom/external/jquery/jquery.js"></script>
    <script src="jquery-ui-1.13.3.custom/jquery-ui.js"></script>
    <script src="https://unpkg.com/leaflet@1.6.0/dist/leaflet.js"></script>
    <link href="https://unpkg.com/leaflet@1.6.0/dist/leaflet.css" rel="stylesheet" />
    
    <!-- ESTILOS MEJORADOS - CAMBIOS PEQUEÑOS -->
    <style>
        /* ===== NOTIFICACIÓN DE CIUDAD ===== */
        .city-notice {
            background: linear-gradient(135deg, #fff9e6 0%, #fff4d6 100%);
            border-left: 3px solid #e65f00;
            padding: 10px 20px;
            margin: 0 auto 15px auto;
            max-width: 1400px;
            font-size: 14px;
            color: #7f8c8d;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
            border-radius: 0 0 6px 6px;
        }
        
        .city-notice i {
            color: #e65f00;
            margin-right: 10px;
            font-size: 16px;
        }
        
        .city-notice a {
            color: #e67e22;
            font-weight: 600;
            text-decoration: none;
            margin: 0 4px;
            border-bottom: 1px solid transparent;
            transition: border-bottom 0.2s;
        }
        
        .city-notice a:hover {
            border-bottom: 1px solid #e67e22;
        }

        /* ===== CONTENEDOR PRINCIPAL CENTRADO ===== */
        .main-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 15px;
        }

        .content-wrapper {
            display: flex;
            gap: 20px;
            margin-top: 15px;
        }

        /* ===== BARRA LATERAL DE FILTROS ===== */
        .filters-sidebar {
            width: 250px;
            flex-shrink: 0;
            background: white;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
            padding: 15px;
            height: fit-content;
        }

        /* ===== CONTENIDO PRINCIPAL ===== */
        .results-content {
            flex: 1;
            min-width: 0;
        }

        .results-header h1 {
            font-size: 24px;
            font-weight: 600;
            color: #2c3e50;
            margin: 0 0 5px 0;
        }

        .results-info {
            color: #7f8c8d;
            font-size: 14px;
            margin-bottom: 10px;
        }

        /* ===== PAGINACIÓN COMPACTA ===== */
        .pagination {
            margin: 10px 0 15px 0;
            text-align: center;
        }

        .pagination a,
        .pagination span {
            display: inline-block;
            padding: 6px 12px;
            text-decoration: none !important;
            color: #2c3e50;
            background-color: #f5f5f5;
            border: 1px solid #e0e0e0;
            border-radius: 5px;
            margin: 0 2px;
            font-size: 13px;
            transition: all 0.2s;
        }

        .pagination a:hover {
            background-color: #fff4e5;
            border-color: #e65f00;
            color: #e65f00;
        }

        .pagination .active {
            background-color: #e65f00;
            color: white;
            border-color: #e65f00;
        }

        .pagination .disabled {
            color: #aaa;
            border-color: #e0e0e0;
            cursor: not-allowed;
            background-color: #f5f5f5;
        }

        /* ===== GRID DE TARJETAS ===== */
        .cards-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
            margin: 15px 0;
        }

        /* ===== TARJETA DE USUARIO MEJORADA ===== */
        .user-card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
            border: 1px solid #ecf0f1;
            overflow: hidden;
            transition: all 0.2s ease;
            cursor: pointer;
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        .user-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            border-color: #ffe0b2;
        }

        .user-card.active {
            border: 2px solid #e65f00 !important;
            box-shadow: 0 0 0 2px rgba(230, 95, 0, 0.1) !important;
            background-color: #fffaf2 !important;
        }

        /* Cabecera de tarjeta */
        .user-card > div:first-child {
            padding: 15px 15px 8px 15px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .user-card img[src*="thumb"] {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #e65f00;
        }

        .user-card > div:first-child span {
            flex: 1;
        }

        .user-name-highlight {
            font-weight: 700;
            color: #2c3e50;
            font-size: 15px;
        }

        .ranking-icon {
            margin-right: 3px;
        }

        /* Cuerpo de tarjeta */
        .user-speaks, .user-learning {
            padding: 0 15px 8px 15px;
        }

        .user-speaks span, .user-learning span {
            font-size: 11px;
            text-transform: uppercase;
            color: #7f8c8d;
            letter-spacing: 0.5px;
            display: block;
            margin-bottom: 4px;
            font-weight: 600;
        }

        .user-speaks div, .user-learning div {
            display: flex;
            align-items: center;
            gap: 4px;
            background: #f8f9fa;
            padding: 3px 8px;
            border-radius: 16px;
            font-size: 11px;
            border: 1px solid #ecf0f1;
            margin-bottom: 4px;
            width: fit-content;
        }

        .user-speaks img, .user-learning img {
            width: 16px;
            height: 16px;
        }

        /* Footer de tarjeta */
        .user-card > div:last-child {
            padding: 8px 15px 12px 15px;
            border-top: 1px solid #ecf0f1;
            margin-top: auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #fafafa;
        }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 1200px) {
            .cards-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 768px) {
            .content-wrapper {
                flex-direction: column;
            }
            
            .filters-sidebar {
                width: 100%;
                margin-bottom: 15px;
            }
            
            .cards-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
    
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const searchForm = document.getElementById('search-form');
            if (searchForm) {
                searchForm.addEventListener('submit', function (event) {
                    var mapDiv = document.getElementById('map');
                    if (mapDiv) mapDiv.style.display = 'none';
                    var mapIframe = document.querySelector('iframe[src*="search-form.php"]');
                    if (mapIframe) mapIframe.style.display = 'none';
                    
                    const currentUrl = new URL(window.location.href);
                    const params = currentUrl.searchParams;

                    params.delete('page');
                    params.delete('highlight');

                    const formAction = searchForm.getAttribute('action');
                    const newUrl = new URL(formAction, window.location.origin);

                    const formData = new FormData(searchForm);
                    for (const [key, value] of formData.entries()) {
                        if (!(searchForm.querySelector(`input[name="${key}"][type="hidden"]`) && value === '')) {
                             newUrl.searchParams.append(key, value);
                        }
                    }

                    window.location.href = newUrl.toString();
                    event.preventDefault();
                });
            }
            
            const urlParams = new URLSearchParams(window.location.search);
            const highlightUserId = urlParams.get('highlight');
            if (highlightUserId) {
                setTimeout(() => {
                    highlightUserCard(parseInt(highlightUserId));
                }, 200);
            }
        });
    </script>
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
        $learns_langs = isset($learn_langs_full_name_array_multidim[$orden_usu]) ? $learn_langs_full_name_array_multidim[$orden_usu] : [];
        
        $teaches_langs = array_map(function($l) { return (strtolower($l) == 'val' || strtolower($l) == 'valencian') ? 'cat' : $l; }, $teaches_langs);
        $learns_langs = array_map(function($l) { return (strtolower($l) == 'val' || strtolower($l) == 'valencian') ? 'cat' : $l; }, $learns_langs);

        $user_languages_js[$orden_usu] = [
            'teaches' => $teaches_langs,
            'learns' => $learns_langs
        ];
    }
}

$current_distance = isset($radio) ? $radio : 150;
$skip_search_form_map_query = true;

$mapUsers = array();
if ($form_submitted || $highlight_user > 0) {
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

$mapUsersTemp = [];
$mapUserIds = [];

if ($res = mysqli_query($link, $map_query)) {
    while ($u = mysqli_fetch_assoc($res)) {
        $u_id_map = (int)$u['orden'];
        $mapUserIds[] = $u_id_map;
        
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
            'teaches' => [],
            'learns' => []
        );
    }
    mysqli_free_result($res);
}

if (!empty($mapUserIds)) {
    $id_list = implode(',', $mapUserIds);
    
    $q_t = "SELECT ml.id AS user_id, l.Id AS lang_code 
            FROM my_langs ml 
            JOIN languages_names l ON ml.lang_id = l.Id 
            WHERE ml.id IN ($id_list)";
    
    if ($bulk_res_t = mysqli_query($link, $q_t)) {
        while ($row = mysqli_fetch_assoc($bulk_res_t)) {
            $uid = (int)$row['user_id'];
            $code = strtolower($row['lang_code']);
            if (isset($mapUsersTemp[$uid])) {
                 $mapUsersTemp[$uid]['teaches'][] = $code;
            }
        }
        mysqli_free_result($bulk_res_t);
    }

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

foreach ($mapUsersTemp as &$mUser) {
    $mUser['teaches'] = array_values(array_unique($mUser['teaches']));
    $mUser['learns'] = array_values(array_unique($mUser['learns']));
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
    document.querySelectorAll('.user-card').forEach(card => {
        card.classList.remove('active');
    });

    const userCard = document.getElementById('user-' + userId);
    if (userCard) {
        userCard.classList.add('active');
        userCard.scrollIntoView({ behavior: 'smooth', block: 'center'});

        if (!fromMap) {
            try {
                if (typeof highlightMarkerOnMap === 'function') {
                    highlightMarkerOnMap(userId);
                }
                const mapIframe = document.querySelector('iframe[src*="search-form.php"]');
                if (mapIframe && mapIframe.contentWindow && mapIframe.contentWindow.highlightMarkerOnMap) {
                    mapIframe.contentWindow.highlightMarkerOnMap(userId);
                }
            } catch (e) {
                console.error('Error al comunicarse con el mapa:', e);
            }
        }
        return true;
    }
    console.log("User card for ID " + userId + " not found on the current page.");
    return false;
}

window.addEventListener('message', function(event) {
    if (event.data && event.data.action === 'highlightUser') {
        const userId = event.data.userId;
        const found = highlightUserCard(userId, true);
        if (!found) {
            console.log("Usuario " + userId + " no encontrado en la página actual.");
        }
    }
});

document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.user-card').forEach(card => {
        card.addEventListener('click', function(e) {
            if (e.target.closest('.user-card > div:first-child')) {
                return;
            }
            const userId = this.getAttribute('data-user-id');
            if (userId) {
                highlightUserCard(parseInt(userId));
                try {
                    const mapIframe = document.querySelector('iframe[src*="search-form.php"]');
                    if (mapIframe && mapIframe.contentWindow && mapIframe.contentWindow.highlightMarkerOnMap) {
                        mapIframe.contentWindow.highlightMarkerOnMap(parseInt(userId));
                    }
                } catch (e) {
                    console.error('Error al comunicarse con el mapa:', e);
                }
                e.stopPropagation();
            }
        });
    });

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

<!-- ===== CONTENEDOR PRINCIPAL CENTRADO ===== -->
<div class="main-container">
    <div class="content-wrapper">
        <!-- Filtros -->
        <div class="filters-sidebar">
            <?php require_once('search-form.php'); ?>
        </div>
        
        <!-- Resultados -->
        <div class="results-content">
            <div class="results-header">
                <h1>Resultados</h1>
                <div class="results-info">
                    <p>Found <?php echo $total_rows; ?> users. Showing page <?php echo $page; ?> of <?php echo $total_pages; ?>.</p>
                </div>
            </div>

            <?php if ($number_of_affected_users > 0 && $total_pages > 1): ?>
                <?php renderPagination($page, $total_pages, $_GET); ?>
            <?php endif; ?>

            <div class="cards-grid">
                <?php
                if ($number_of_affected_users > 0) {
                    $lista_niveles_mostrar = array(
                        "0" => "?", "1" => "zero", "2" => "A1", "3" => "A2",
                        "4" => "B1", "5" => "B2", "6" => "C1", "7" => "C2",
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
                                    <span class="user-name-highlight"><?php echo $nombreusu; ?></span> #<?php echo $orden_usu; ?>
                                    <span style="margin-left:18px;">
                                    <?php if ($num_evalu > 0): ?>
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640" style="height: 1em; vertical-align: -0.125em;" fill="#888" class="ranking-icon"><path d="M353.8 118.1L330.2 70.3C326.3 62 314.1 61.7 309.8 70.3L286.2 118.1L233.9 125.6C224.6 127 220.6 138.5 227.5 145.4L265.5 182.4L256.5 234.5C255.1 243.8 264.7 251 273.3 246.7L320.2 221.9L366.8 246.3C375.4 250.6 385.1 243.4 383.6 234.1L374.6 182L412.6 145.4C419.4 138.6 415.5 127.1 406.2 125.6L353.9 118.1zM288 320C261.5 320 240 341.5 240 368L240 528C240 554.5 261.5 576 288 576L352 576C378.5 576 400 554.5 400 528L400 368C400 341.5 378.5 320 352 320L288 320zM80 384C53.5 384 32 405.5 32 432L32 528C32 554.5 53.5 576 80 576L144 576C170.5 576 192 554.5 192 528L192 432C192 405.5 170.5 384 144 384L80 384zM448 496L448 528C448 554.5 469.5 576 496 576L560 576C586.5 576 608 554.5 608 528L608 496C608 469.5 586.5 448 560 448L496 448C469.5 448 448 469.5 448 496z"/></svg><?php echo $num_evalu; ?> (<?php echo round($nota_evalu * 100); ?>%)
                                    <?php endif; ?>
                                    <div style="font-size:13px;color:#888;margin-top:2px;">
                                    <?php echo $distancia12; ?> km
                                    <?php if ($organiz_id[$i] == $fila_user1['id_org'] && $organiz_id[$i] != 0): ?>
                                        <br><br>&nbsp;<?php echo htmlspecialchars($organizac); ?>
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

                                            $lang_full_name = $my_langs_full_name_array_multidim[$orden_usu][$sss];
                                            $lang_short = $my_langs_array_multidim[$orden_usu][$sss];
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

                                            $lang_full_name = $learn_langs_full_name_array_multidim[$orden_usu][$sss];
                                            $lang_short = $learn_langs_array_multidim[$orden_usu][$sss];
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

                            <div>
                                <div style="font-size:13px;color:#888;">
                                    <?php echo $distancia12; ?> km
                                </div>
                                <?php if ($organiz_id[$i] == $fila_user1['id_org'] && $organiz_id[$i] != 0): ?>
                                    <div style="font-size:11px;color:#e65f00;background:#fff4e5;padding:3px 8px;border-radius:20px;font-weight:600;">
                                        <?php echo htmlspecialchars($organizac); ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                    <?php endfor;
                } else {
                    echo "<p>No users found matching your criteria.</p>";
                }
                ?>
            </div>

            <?php if ($number_of_affected_users > 0 && $total_pages > 1): ?>
                <?php renderPagination($page, $total_pages, $_GET); ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require('../templates/footer.php'); ?>
</body>
</html>