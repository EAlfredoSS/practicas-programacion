<?php
session_start();

require('../files/bd.php');
require('../files/idiomasnivel.php');
require('../funcionesphp/funciones_idiomas_usuario.php');

$identificador2017 = $_SESSION['orden2017'];
$_SESSION['idusuario2019'] = $identificador2017;

$tiempo_unix_actual = time();
$query_update_lastaction = "UPDATE mentor2009 SET lastaction = $tiempo_unix_actual WHERE orden = $identificador2017";
$result_update_lastaction = mysqli_query($link, $query_update_lastaction);

// ============================================================
// FUNCIONES AUXILIARES
// ============================================================

function detectarFoto($base_path, $tipo = 'foto')
{
    $extensiones = ['jpg', 'png', 'gif', 'bmp'];
    foreach ($extensiones as $ext) {
        $ruta = "../uploader/upload_pic/" . ($tipo == 'thumb' ? 'thumb_' : '') . $base_path . "." . $ext;
        if (file_exists($ruta)) {
            return $ruta;
        }
    }
    return "../uploader/default.jpg";
}

function renderLevelDots($level)
{
    $level = intval($level);
    if ($level <= 0) {
        $html = '<div class="level-dots">';
        for ($i = 1; $i <= 7; $i++) {
            $html .= '<span class="dot" style="background-color:#e0e0e0;"></span>';
        }
        $html .= '</div>';
        return $html;
    }
    $level = min(7, $level);
    $colors = [
        1 => '#E10016',
        2 => '#E10016',
        3 => '#F14400',
        4 => '#F14400',
        5 => '#FED700',
        6 => '#1B9E00',
        7 => '#1B9E00'
    ];
    $html = '<div class="level-dots">';
    for ($i = 1; $i <= 7; $i++) {
        $color = ($i <= $level) ? $colors[$level] : '#e0e0e0';
        $html .= '<span class="dot" style="background-color:' . $color . ';"></span>';
    }
    $html .= '</div>';
    return $html;
}

// =====================================================================
// getFlagPath — busca la bandera en las rutas conocidas, fallback a placeholder
// (IDÉNTICA A LA DEL SEARCH)
// =====================================================================
function getFlagPath($code2, $code3) {
    $code2 = trim($code2);
    $code3 = strtolower(trim($code3));

    // Rutas posibles donde pueden estar las banderas (en orden de preferencia)
    $bases = [
        './banderasseparadas2024/',
        './images/banderasseparadas2024/',
        '../public/banderasseparadas2024/',
    ];
    // Placeholders en orden de preferencia
    $placeholders = [
        './banderasseparadas2024/placeholder.png',
        './images/placeholder.png',
        './images/banderasseparadas2024/placeholder.png',
    ];
    $placeholder = '';
    foreach ($placeholders as $p) {
        if (file_exists($p)) { $placeholder = $p; break; }
    }
    if ($placeholder === '') $placeholder = './images/placeholder.png';

    if (!preg_match('/^[a-z]{2}$/i', $code2)) {
        return $placeholder;
    }

    // Excepción 'no' (noruego): solo válido para nor/nno/nob
    if (strtolower($code2) === 'no' && !in_array($code3, ['nor', 'nno', 'nob'])) {
        return $placeholder;
    }

    foreach ($bases as $base) {
        $flag_file = $base . strtolower($code2) . '.png';
        if (file_exists($flag_file)) {
            return $flag_file;
        }
    }

    return $placeholder;
}

function obtenerHora($fechaHora)
{
    $timestamp = strtotime($fechaHora);
    return date("H:i", $timestamp);
}

// ============================================================
// DATOS DEL USUARIO
// ============================================================

$query = "SELECT * FROM mentor2009 WHERE orden='" . $identificador2017 . "'";
$result = mysqli_query($link, $query);
if (!mysqli_num_rows($result)) {
    die("User unregistered. <a href=\"http://www.lingua2.com\">Information</a>");
}
$fila = mysqli_fetch_array($result);

$ciudad1          = $fila['Ciudad'];
$gpslat11         = $fila['Gpslat'];
$gpslng11         = $fila['Gpslng'];
$email_del_usu    = $fila['Email'];
$email_verified   = $fila['Emailverif'];
$availability100  = $fila['Disponibilidadcomentarios'];
$othercomments100 = $fila['Otroscomentarios'];
$foto_nombre      = detectarFoto($fila['orden'], 'foto');
$thumb_nombre     = detectarFoto($fila['orden'], 'thumb');
$zonaHoraria      = $fila['timeshift'];
$is_teacher       = $fila['Pais'];

$nombre_usuar = $fila["nombre"];
$arr          = explode(' ', trim($nombre_usuar));
$nombre_usuar = ucfirst(substr($arr[0], 0, 13));

$fechaHoraFormateada2 = obtenerFechaHora($tiempo_unix_actual, $zonaHoraria);
$horaFormateada2      = obtenerHora($fechaHoraFormateada2);

$query77 = "SELECT * FROM mentor2009 WHERE orden='$identificador2017'";
$result77 = mysqli_query($link, $query77);
if (!mysqli_num_rows($result77)) die("User unregistered 1.");
$fila77 = mysqli_fetch_array($result77);

$latitud1             = $fila77['Gpslat'];
$longitud1            = $fila77['Gpslng'];
$id_de_la_org_del_usu = $fila77['id_org'];

if ($id_de_la_org_del_usu == 0) {
    $domain1 = substr($email_del_usu, (int) strpos($email_del_usu, '@') + 1);
    $query123456 = "
        SELECT org.organization_id AS id_de_la_org
        FROM organization_emails orgem 
        INNER JOIN organizations org ON org.organization_id = orgem.organization_id
        WHERE orgem.email_domain='$domain1'";
    $result123456 = mysqli_query($link, $query123456);
    if (mysqli_num_rows($result123456)) {
        $fila123456       = mysqli_fetch_array($result123456);
        $organization_id1 = $fila123456['id_de_la_org'];
        $query_update_org = "UPDATE mentor2009 SET id_org = $organization_id1 WHERE orden = $identificador2017";
        mysqli_query($link, $query_update_org);
    }
}

// ============================================================
// IDIOMAS QUE CONOCE (my_langs)
// ============================================================

$query_my_langs = "
    SELECT my_l.*, l_names.Print_Name AS full_lang_name, l.lang_id AS lang_codigo2letras
    FROM my_langs my_l
    LEFT JOIN languages_names l_names ON my_l.lang_id=l_names.Id
    LEFT JOIN languages1 l ON my_l.lang_id=l.Id
    WHERE my_l.id='$identificador2017' 
    ORDER BY my_l.level_id DESC";
$result_my_langs = mysqli_query($link, $query_my_langs);
$num_my_langs    = mysqli_num_rows($result_my_langs);

$my_langs_array = $my_langs_2letters_array = $my_langs_full_name_array = $my_langs_level_array = [];
$my_langs_forshare_array = $my_langs_price_array = $my_langs_typeofexchange_array = [];
$my_langs_priceorexchangetext_array = $my_langs_level_image_array = [];
$my_langs_macrolang_array = [];

for ($jjj = 0; $jjj < $num_my_langs; $jjj++) {
    $fila_my_langs = mysqli_fetch_array($result_my_langs);
    array_push($my_langs_full_name_array, $fila_my_langs['full_lang_name']);
    array_push($my_langs_array,           $fila_my_langs['lang_id']);
    array_push($my_langs_2letters_array,  $fila_my_langs['lang_codigo2letras']);
    array_push($my_langs_level_array,     intval($fila_my_langs['level_id']));
    array_push($my_langs_forshare_array,  $fila_my_langs['for_share']);
    array_push($my_langs_price_array,     $fila_my_langs['lang_price']);

    $lang_id_tmp = $fila_my_langs['lang_id'];
    $query_macro = "SELECT M_Id FROM languages_macrolanguages WHERE I_Id='$lang_id_tmp'";
    $result_macro = mysqli_query($link, $query_macro);
    $fila_macro = mysqli_fetch_array($result_macro);
    array_push($my_langs_macrolang_array, $fila_macro ? $fila_macro['M_Id'] : '');
}

// Detectar y fusionar entradas duplicadas (por ej. spa con dos nombres)
$duplicate_langs   = array_count_values($my_langs_array);
$lista_idiomas_dup = array();

for ($jjj = 0; $jjj < $num_my_langs; $jjj++) {
    $lang1 = $my_langs_array[$jjj];
    if ($duplicate_langs["$lang1"] == 1) {
        unset($duplicate_langs["$lang1"]);
    }
}

$n_dups            = count($duplicate_langs);
$lista_idiomas_dup = array_keys($duplicate_langs);

for ($iiii = 0; $iiii < $n_dups; $iiii++) {
    $nombre_idiomas_array = '';
    $lang2 = $lista_idiomas_dup[$iiii];
    $tmp   = array_count_values($my_langs_array);
    $cnt   = $tmp[$lang2];

    for ($jjjj = 0; $jjjj < $cnt; $jjjj++) {
        $key2 = array_search($lang2, $my_langs_array);
        if ($jjjj < $cnt - 1) {
            $nombre_idiomas_array .= "$my_langs_full_name_array[$key2] | ";
            $my_langs_array[$key2] = '_delete_';
        } else {
            $nombre_idiomas_array .= "$my_langs_full_name_array[$key2]";
        }
        $my_langs_full_name_array[$key2] = $nombre_idiomas_array;
    }
}

for ($iii = 0; $iii < count($my_langs_level_array); $iii++) {
    switch ($my_langs_level_array[$iii]) {
        case 0:  $my_langs_level_image_array[$iii] = 'no_data.png'; break;
        case 1:  $my_langs_level_image_array[$iii] = 'zero_knowledge.png'; break;
        case 2:  $my_langs_level_image_array[$iii] = 'a1.png'; break;
        case 3:  $my_langs_level_image_array[$iii] = 'a2.png'; break;
        case 4:  $my_langs_level_image_array[$iii] = 'b1.png'; break;
        case 5:  $my_langs_level_image_array[$iii] = 'b2.png'; break;
        case 6:  $my_langs_level_image_array[$iii] = 'c1.png'; break;
        case 7:  $my_langs_level_image_array[$iii] = 'c2.png'; break;
        default: $my_langs_level_image_array[$iii] = 'no_data.png'; break;
    }
}

for ($iii = 0; $iii < count($my_langs_array); $iii++) {
    if ($my_langs_forshare_array[$iii] == 0) {
        $my_langs_typeofexchange_array[$iii]      = 'I know this language, but I do not want to exchange or teach it.';
        $my_langs_priceorexchangetext_array[$iii] = '';
    } elseif ($my_langs_price_array[$iii] == null) {
        $my_langs_typeofexchange_array[$iii]      = 'I know this language and I want to exchange it for another user\'s language (exchange free of cost).';
        $my_langs_priceorexchangetext_array[$iii] = 'EXCH.';
    } else {
        $my_langs_typeofexchange_array[$iii]      = 'I know this language and I want to teach it for money.';
        $my_langs_priceorexchangetext_array[$iii] = $my_langs_price_array[$iii] . ' &#8364;/h';
    }
}

$key3 = array_search('_delete_', $my_langs_array);
while ($key3 !== false) {
    unset($my_langs_array[$key3]);
    unset($my_langs_full_name_array[$key3]);
    unset($my_langs_level_array[$key3]);
    unset($my_langs_forshare_array[$key3]);
    unset($my_langs_price_array[$key3]);
    unset($my_langs_typeofexchange_array[$key3]);
    unset($my_langs_priceorexchangetext_array[$key3]);
    unset($my_langs_level_image_array[$key3]);
    unset($my_langs_2letters_array[$key3]);
    unset($my_langs_macrolang_array[$key3]);
    $key3 = array_search('_delete_', $my_langs_array);
}

$tmp1_array = $tmp2_array = $tmp3_array = $tmp4_array = $tmp5_array = [];
$tmp6_array = $tmp7_array = $tmp8_array = $tmp9_array = $tmp10_array = [];
$n_lenguas = count($my_langs_array);

for ($i = 0; $i < $n_lenguas; $i++) {
    $tmp1_array[$i]  = array_pop($my_langs_array);
    $tmp2_array[$i]  = array_pop($my_langs_full_name_array);
    $tmp3_array[$i]  = array_pop($my_langs_level_array);
    $tmp4_array[$i]  = array_pop($my_langs_forshare_array);
    $tmp5_array[$i]  = array_pop($my_langs_price_array);
    $tmp6_array[$i]  = array_pop($my_langs_typeofexchange_array);
    $tmp7_array[$i]  = array_pop($my_langs_priceorexchangetext_array);
    $tmp8_array[$i]  = array_pop($my_langs_level_image_array);
    $tmp9_array[$i]  = array_pop($my_langs_2letters_array);
    $tmp10_array[$i] = array_pop($my_langs_macrolang_array);
}

$my_langs_array                     = array_reverse($tmp1_array);
$my_langs_full_name_array           = array_reverse($tmp2_array);
$my_langs_level_array               = array_reverse($tmp3_array);
$my_langs_forshare_array            = array_reverse($tmp4_array);
$my_langs_price_array               = array_reverse($tmp5_array);
$my_langs_typeofexchange_array      = array_reverse($tmp6_array);
$my_langs_priceorexchangetext_array = array_reverse($tmp7_array);
$my_langs_level_image_array         = array_reverse($tmp8_array);
$my_langs_2letters_array            = array_reverse($tmp9_array);
$my_langs_macrolang_array           = array_reverse($tmp10_array);

// ============================================================
// IDIOMAS QUE QUIERE APRENDER (learn_langs)
// ============================================================

$query_learn_langs = "
    SELECT learn_l.*, l_names.Print_Name AS full_lang_name, l.lang_id AS lang_codigo2letras
    FROM learn_langs learn_l
    LEFT JOIN languages_names l_names ON learn_l.lang_id=l_names.Id
    LEFT JOIN languages1 l ON learn_l.lang_id=l.Id
    WHERE learn_l.id='$identificador2017' 
    ORDER BY learn_l.level_id DESC";
$result_learn_langs = mysqli_query($link, $query_learn_langs);
$num_learn_langs    = mysqli_num_rows($result_learn_langs);

$learn_langs_array = $learn_langs_2letters_array = $learn_langs_full_name_array = $learn_langs_level_array = [];
$learn_langs_forshare_array = $learn_langs_price_array = $learn_langs_typeofexchange_array = [];
$learn_langs_priceorexchangetext_array = $learn_langs_level_image_array = [];
$learn_langs_macrolang_array = [];

for ($jjj = 0; $jjj < $num_learn_langs; $jjj++) {
    $fila_learn_langs = mysqli_fetch_array($result_learn_langs);
    array_push($learn_langs_full_name_array, $fila_learn_langs['full_lang_name']);
    array_push($learn_langs_array,           $fila_learn_langs['lang_id']);
    array_push($learn_langs_2letters_array,  $fila_learn_langs['lang_codigo2letras']);
    array_push($learn_langs_level_array,     intval($fila_learn_langs['level_id']));
    array_push($learn_langs_forshare_array,  $fila_learn_langs['for_share']);
    array_push($learn_langs_price_array,     $fila_learn_langs['lang_price']);

    $lang_id_tmp = $fila_learn_langs['lang_id'];
    $query_macro = "SELECT M_Id FROM languages_macrolanguages WHERE I_Id='$lang_id_tmp'";
    $result_macro = mysqli_query($link, $query_macro);
    $fila_macro = mysqli_fetch_array($result_macro);
    array_push($learn_langs_macrolang_array, $fila_macro ? $fila_macro['M_Id'] : '');
}

$duplicate_langs   = array_count_values($learn_langs_array);
$lista_idiomas_dup = array();

for ($jjj = 0; $jjj < $num_learn_langs; $jjj++) {
    $lang1 = $learn_langs_array[$jjj];
    if ($duplicate_langs["$lang1"] == 1) {
        unset($duplicate_langs["$lang1"]);
    }
}

$n_dups            = count($duplicate_langs);
$lista_idiomas_dup = array_keys($duplicate_langs);

for ($iiii = 0; $iiii < $n_dups; $iiii++) {
    $nombre_idiomas_array = '';
    $lang2 = $lista_idiomas_dup[$iiii];
    $tmp   = array_count_values($learn_langs_array);
    $cnt   = $tmp[$lang2];

    for ($jjjj = 0; $jjjj < $cnt; $jjjj++) {
        $key2 = array_search($lang2, $learn_langs_array);
        if ($jjjj < $cnt - 1) {
            $nombre_idiomas_array .= "$learn_langs_full_name_array[$key2] | ";
            $learn_langs_array[$key2] = '_delete_';
        } else {
            $nombre_idiomas_array .= "$learn_langs_full_name_array[$key2]";
        }
        $learn_langs_full_name_array[$key2] = $nombre_idiomas_array;
    }
}

for ($iii = 0; $iii < count($learn_langs_level_array); $iii++) {
    switch ($learn_langs_level_array[$iii]) {
        case 0:  $learn_langs_level_image_array[$iii] = 'no_data.png'; break;
        case 1:  $learn_langs_level_image_array[$iii] = 'zero_knowledge.png'; break;
        case 2:  $learn_langs_level_image_array[$iii] = 'a1.png'; break;
        case 3:  $learn_langs_level_image_array[$iii] = 'a2.png'; break;
        case 4:  $learn_langs_level_image_array[$iii] = 'b1.png'; break;
        case 5:  $learn_langs_level_image_array[$iii] = 'b2.png'; break;
        case 6:  $learn_langs_level_image_array[$iii] = 'c1.png'; break;
        case 7:  $learn_langs_level_image_array[$iii] = 'c2.png'; break;
        default: $learn_langs_level_image_array[$iii] = 'no_data.png'; break;
    }
}

$key3 = array_search('_delete_', $learn_langs_array);
while ($key3 !== false) {
    unset($learn_langs_array[$key3]);
    unset($learn_langs_full_name_array[$key3]);
    unset($learn_langs_level_array[$key3]);
    unset($learn_langs_forshare_array[$key3]);
    unset($learn_langs_price_array[$key3]);
    unset($learn_langs_typeofexchange_array[$key3]);
    unset($learn_langs_priceorexchangetext_array[$key3]);
    unset($learn_langs_level_image_array[$key3]);
    unset($learn_langs_2letters_array[$key3]);
    unset($learn_langs_macrolang_array[$key3]);
    $key3 = array_search('_delete_', $learn_langs_array);
}

$tmp1_array = $tmp2_array = $tmp3_array = $tmp4_array = $tmp5_array = [];
$tmp6_array = $tmp7_array = $tmp8_array = $tmp9_array = $tmp10_array = [];
$n_lenguas = count($learn_langs_array);

for ($i = 0; $i < $n_lenguas; $i++) {
    $tmp1_array[$i]  = array_pop($learn_langs_array);
    $tmp2_array[$i]  = array_pop($learn_langs_full_name_array);
    $tmp3_array[$i]  = array_pop($learn_langs_level_array);
    $tmp4_array[$i]  = array_pop($learn_langs_forshare_array);
    $tmp5_array[$i]  = array_pop($learn_langs_price_array);
    $tmp6_array[$i]  = array_pop($learn_langs_typeofexchange_array);
    $tmp7_array[$i]  = array_pop($learn_langs_priceorexchangetext_array);
    $tmp8_array[$i]  = array_pop($learn_langs_level_image_array);
    $tmp9_array[$i]  = array_pop($learn_langs_2letters_array);
    $tmp10_array[$i] = array_pop($learn_langs_macrolang_array);
}

$learn_langs_array                     = array_reverse($tmp1_array);
$learn_langs_full_name_array           = array_reverse($tmp2_array);
$learn_langs_level_array               = array_reverse($tmp3_array);
$learn_langs_forshare_array            = array_reverse($tmp4_array);
$learn_langs_price_array               = array_reverse($tmp5_array);
$learn_langs_typeofexchange_array      = array_reverse($tmp6_array);
$learn_langs_priceorexchangetext_array = array_reverse($tmp7_array);
$learn_langs_level_image_array         = array_reverse($tmp8_array);
$learn_langs_2letters_array            = array_reverse($tmp9_array);
$learn_langs_macrolang_array           = array_reverse($tmp10_array);

// ============================================================
// PROFESORES / USUARIOS CERCANOS (sidebar)
// ============================================================

$orden99 = $distancia99 = $precioprof = $nombre_usuario_short = $path_photo = $ciudad97 = "";
$orden99_2 = $distancia99_2 = $nombre_usuario_short2 = $path_photo2 = "";

if ($is_teacher != 'teacher') {
    $query = "
        SELECT m.orden,
        (acos(sin(radians(" . floatval($latitud1) . ")) * sin(radians(m.Gpslat)) + 
        cos(radians(" . floatval($latitud1) . ")) * cos(radians(m.Gpslat)) * 
        cos(radians(m.Gpslng) - radians(" . floatval($longitud1) . "))) * 6378) 
        AS distanciaPunto1Punto2, m.teacherprice, m.nombre, m.fotoext
        FROM mentor2009 m
        WHERE orden <> '$identificador2017' AND Pais = 'teacher'
        HAVING distanciaPunto1Punto2 < 50
        ORDER BY RAND() LIMIT 1";
    $result = mysqli_query($link, $query);

    if (!mysqli_num_rows($result)) {
        $query = "
            SELECT m.orden,
            (acos(sin(radians(" . floatval($latitud1) . ")) * sin(radians(m.Gpslat)) + 
            cos(radians(" . floatval($latitud1) . ")) * cos(radians(m.Gpslat)) * 
            cos(radians(m.Gpslng) - radians(" . floatval($longitud1) . "))) * 6378) 
            AS distanciaPunto1Punto2, m.teacherprice, m.nombre, m.fotoext
            FROM mentor2009 m
            WHERE orden <> '$identificador2017' AND Pais = 'teacher'
            ORDER BY RAND() LIMIT 1";
        $result = mysqli_query($link, $query);
    }

    if (mysqli_num_rows($result)) {
        $fila                 = mysqli_fetch_array($result);
        $orden99              = $fila['orden'];
        $distancia99          = round($fila['distanciaPunto1Punto2'], 2);
        $precioprof           = $fila['teacherprice'];
        $nombre_usuario       = $fila['nombre'];
        $vector1              = preg_split('/\s+/', $nombre_usuario);
        $nombre_usuario_short = ucfirst($vector1[0]);
        $extension            = $fila['fotoext'];
        $path_photo           = "../uploader/upload_pic/thumb_$orden99." . $extension;
        if (!file_exists($path_photo)) {
            $path_photo = "../uploader/default.jpg";
        }
    }
}

$query2 = "
    SELECT m.orden,
    (acos(sin(radians(" . floatval($latitud1) . ")) * sin(radians(m.Gpslat)) + 
    cos(radians(" . floatval($latitud1) . ")) * cos(radians(m.Gpslat)) * 
    cos(radians(m.Gpslng) - radians(" . floatval($longitud1) . "))) * 6378) 
    AS distanciaPunto1Punto2, m.nombre, m.Ciudad, m.fotoext
    FROM mentor2009 m
    WHERE orden <> '$identificador2017' AND Pais <> 'teacher'
    HAVING distanciaPunto1Punto2 < 50
    ORDER BY RAND() LIMIT 1";
$result2 = mysqli_query($link, $query2);

if (!mysqli_num_rows($result2)) {
    $query2 = "
        SELECT m.orden,
        (acos(sin(radians(" . floatval($latitud1) . ")) * sin(radians(m.Gpslat)) + 
        cos(radians(" . floatval($latitud1) . ")) * cos(radians(m.Gpslat)) * 
        cos(radians(m.Gpslng) - radians(" . floatval($longitud1) . "))) * 6378) 
        AS distanciaPunto1Punto2, m.nombre, m.Ciudad, m.fotoext
        FROM mentor2009 m
        WHERE orden <> '$identificador2017' AND Pais <> 'teacher'
        ORDER BY RAND() LIMIT 1";
    $result2 = mysqli_query($link, $query2);
}

if (mysqli_num_rows($result2)) {
    $fila2                 = mysqli_fetch_array($result2);
    $orden99_2             = $fila2['orden'];
    $distancia99_2         = round($fila2['distanciaPunto1Punto2'], 2);
    $nombre_usuario2       = $fila2['nombre'];
    $ciudad97              = $fila2['Ciudad'];
    $vector2               = preg_split('/\s+/', $nombre_usuario2);
    $nombre_usuario_short2 = ucfirst($vector2[0]);
    $extension2            = $fila2['fotoext'];
    $path_photo2           = "../uploader/upload_pic/thumb_$orden99_2." . $extension2;
    if (!file_exists($path_photo2)) {
        $path_photo2 = "../uploader/default.jpg";
    }
}

// ============================================================
// EVALUACIONES
// ============================================================

$query1 = "SELECT * FROM comentarios WHERE (id_aludido='$identificador2017') AND censurado=0 ORDER BY horacreacion DESC";
$result1 = mysqli_query($link, $query1);
$n_comentarios = mysqli_num_rows($result1);

$query432 = "SELECT * FROM comentarios WHERE (id_aludido='$identificador2017') AND censurado=0 AND rating=1 ORDER BY horacreacion DESC";
$result432 = mysqli_query($link, $query432);
$n_comentarios_positivos = mysqli_num_rows($result432);

$porcentaje_positivos = ($n_comentarios != 0) ? round($n_comentarios_positivos * 100 / $n_comentarios) : 0;

$num_max_ev_mostradas = 3;
$query1010 = "
    SELECT m.nombre AS nombre1, m.orden AS orden1, m.fotoext AS fotoext1,
           comentarios.comment, comentarios.hora, comentarios.rating
    FROM comentarios
    LEFT JOIN mentor2009 AS m ON m.orden = comentarios.id_autor
    WHERE comentarios.id_aludido='$identificador2017' AND comentarios.censurado=0
    ORDER BY comentarios.horacreacion DESC";
$result1010 = mysqli_query($link, $query1010);
$num_evaluaciones = mysqli_num_rows($result1010);

// ============================================================
// ALERTAS (nav)
// ============================================================

$sql3 = "SELECT count(*) FROM messages WHERE `to` = '$identificador2017' AND `from` <> '$identificador2017' AND `to_viewed` = '0' AND `to_deleted` = '0' ORDER BY `created` DESC";
$result3 = mysqli_query($link, $sql3);
$fila3 = mysqli_fetch_array($result3);
$nuevos_emails = $fila3[0];

$query_vote = "SELECT * FROM couples2009antiguos WHERE (voted_1=0 AND user_id_1='$identificador2017') AND contactado=1";
$result_vote = mysqli_query($link, $query_vote);
$nuevos_vote1 = mysqli_num_rows($result_vote);

$query_vote = "SELECT * FROM couples2009antiguos WHERE (voted_2=0 AND user_id_2='$identificador2017') AND (contactado=0 OR contactado=1)";
$result_vote = mysqli_query($link, $query_vote);
$nuevos_vote2 = mysqli_num_rows($result_vote);

$nuevos_vote_total = $nuevos_vote1 + $nuevos_vote2;
$total_n_alertas   = $nuevos_emails + $nuevos_vote_total;
?>
<!DOCTYPE HTML>
<html>

<head>
    <script src="https://kit.fontawesome.com/bb6243346a.js" crossorigin="anonymous"></script>
    <script type="text/javascript" src="../public/js/jquery.min.js"></script>
    <script type="text/javascript" src="../public/js/popper.js"></script>
    <script type="text/javascript" src="../public/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="../public/js/jquery.mCustomScrollbar.min.js"></script>
    <script type="text/javascript" src="../public/lib/slick/slick.min.js"></script>
    <script type="text/javascript" src="../public/js/scrollbar.js"></script>
    <script type="text/javascript" src="../public/js/script.js"></script>
    <title>Personal Dashboard | Lingua2</title>

    <script async src="https://www.googletagmanager.com/gtag/js?id=G-NYB9FFBL5J"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag() { dataLayer.push(arguments); }
        gtag('js', new Date());
        gtag('config', 'G-NYB9FFBL5J');
    </script>
    <script>
        (function(w, d, s, l, i) {
            w[l] = w[l] || [];
            w[l].push({ 'gtm.start': new Date().getTime(), event: 'gtm.js' });
            var f = d.getElementsByTagName(s)[0], j = d.createElement(s),
                dl = l != 'dataLayer' ? '&l=' + l : '';
            j.async = true;
            j.src = 'https://www.googletagmanager.com/gtm.js?id=' + i + dl;
            f.parentNode.insertBefore(j, f);
        })(window, document, 'script', 'dataLayer', 'GTM-TSHHJ2LL');
    </script>

    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="" />
    <meta name="keywords" content="" />
    <link rel="stylesheet" type="text/css" href="../public/css/animate.css">
    <link rel="stylesheet" type="text/css" href="../public/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="../public/css/line-awesome.css">
    <link rel="stylesheet" type="text/css" href="../public/css/line-awesome-font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="../public/css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="../public/css/jquery.mCustomScrollbar.min.css">
    <link rel="stylesheet" type="text/css" href="../public/lib/slick/slick.css">
    <link rel="stylesheet" type="text/css" href="../public/lib/slick/slick-theme.css">
    <link rel="stylesheet" type="text/css" href="../public/css/style.css">
    <link rel="stylesheet" type="text/css" href="../public/css/responsive.css">
    <link rel="stylesheet" href="./css/languages.css" media="all" />

    <style>
        a { color: #e65f00; }

        /* HEADER NAV */
        header nav ul li > a {
            display: flex !important; flex-direction: column !important;
            align-items: center !important; justify-content: center !important;
            font-size: 15px !important; line-height: 1 !important;
            padding: 15px 1px 0px 1px !important; text-align: center !important; height: 30px !important;
        }
        header nav ul li > a > span {
            display: flex !important; align-items: center !important; justify-content: center !important;
            width: 40px !important; height: 10px !important; flex-shrink: 0 !important; margin-bottom: 2px !important;
        }
        header nav ul li > a > span i,
        header nav ul li > a > span .fa,
        header nav ul li > a > span .fas,
        header nav ul li > a > span .far {
            font-size: 18px !important; display: block !important; color: white !important; line-height: 1 !important;
        }
        header .fas, header .far, header .fal, header .fab, header .fa,
        header nav ul li a span i { color: white !important; }

        /* USER ACCOUNT DROPDOWN */
        #l2-user-account-wrap {
            position: relative !important; display: inline-flex !important;
            align-items: center !important; margin-left: auto !important; flex-shrink: 0 !important; width: 10px;
        }
        #l2-user-trigger {
            display: inline-flex !important; align-items: center !important; gap: 8px !important;
            cursor: pointer !important; background: transparent !important; border: none !important;
            padding: 11px 150px !important; border-radius: 6px !important; text-decoration: none !important;
            color: #ffffff !important; font-weight: 600 !important; font-size: 14px !important;
            white-space: nowrap !important; pointer-events: auto !important; z-index: 1 !important;
        }
        @media (max-width: 768px) { #l2-user-trigger { padding: 11px 8px !important; } }
        #l2-user-trigger img {
            width: 34px !important; height: 34px !important; border-radius: 50% !important;
            border: 2px solid rgba(255, 255, 255, 0.85) !important; object-fit: cover !important; flex-shrink: 0 !important;
        }
        #l2-user-trigger .l2-name { color: #ffffff !important; font-weight: 600 !important; font-size: 14px !important; }
        #l2-user-trigger .l2-arrow { font-size: 10px !important; color: #ffffff !important; transition: transform 0.2s !important; }
        #l2-user-trigger.is-open .l2-arrow { transform: rotate(180deg) !important; }
        #l2-dropdown-panel {
            display: none; position: fixed !important; z-index: 999999 !important;
            width: 300px !important; background: #ffffff !important; border-radius: 0 !important;
            box-shadow: 0 4px 24px rgba(0,0,0,0.18) !important; border: 1px solid #e0e0e0 !important; overflow: hidden !important;
        }
        @keyframes l2DropIn {
            from { opacity: 0; transform: translateY(-6px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        #l2-dropdown-panel.l2-open { display: block !important; animation: l2DropIn 0.15s ease !important; }
        #l2-dropdown-panel .l2-settings-title {
            display: block !important; font-size: 16px !important; font-weight: 700 !important;
            color: #111111 !important; padding: 16px 20px 10px !important; margin: 0 !important;
        }
        #l2-dropdown-panel ul.l2-links { list-style: none !important; padding: 0 !important; margin: 0 0 4px 0 !important; }
        #l2-dropdown-panel ul.l2-links li { padding: 0 !important; margin: 0 !important; }
        #l2-dropdown-panel ul.l2-links li a {
            display: block !important; padding: 9px 20px !important; font-size: 14px !important;
            font-weight: 400 !important; color: #111111 !important; text-decoration: none !important;
            background: transparent !important; border: none !important; transition: background 0.12s !important;
        }
        #l2-dropdown-panel ul.l2-links li a:hover { background: #f5f5f5 !important; color: #111111 !important; }
        #l2-dropdown-panel .l2-logout-row {
            border-top: 1px solid #e0e0e0 !important; padding: 14px 20px !important; text-align: center !important;
        }
        #l2-dropdown-panel .l2-logout-row a {
            display: block !important; font-size: 16px !important; font-weight: 600 !important;
            color: #111111 !important; text-decoration: none !important;
        }
        #l2-dropdown-panel .l2-logout-row a:hover { color: #e65f00 !important; }

        /* LAYOUT */
        .col-lg-6, .col-md-7 { padding-left: 10px !important; padding-right: 10px !important; }
        .posts-section, .post-bar { width: 100% !important; overflow: visible !important; }

        /* LANGUAGE SKILL CARDS */
        .skill-tags {
            list-style: none !important; padding: 0 !important; margin: 15px 0 !important;
            display: grid !important; grid-template-columns: repeat(auto-fill, minmax(100px, 1fr)) !important;
            gap: 8px !important; width: 100% !important; box-sizing: border-box !important;
        }
        .skill-tags li {
            position: relative !important; overflow: visible !important; background: #ffffff !important;
            border: 1px solid #e8e8e8 !important; border-radius: 12px !important; min-height: 175px !important;
            box-shadow: 0 2px 6px rgba(0,0,0,0.05) !important; transition: box-shadow 0.2s, transform 0.2s !important;
            box-sizing: border-box !important; display: flex !important; flex-direction: column !important;
            align-items: stretch !important; z-index: 1 !important;
        }
        .skill-tags li:hover {
            transform: translateY(-2px) !important; box-shadow: 0 6px 14px rgba(230,95,0,0.12) !important;
            border-color: #e65f00 !important; z-index: 9999 !important;
        }
        .skill-tags li.low-level-border { border: 2px solid rgba(225, 0, 22, 0.55) !important; }
        .bolita-accion {
            position: absolute; top: -5px; right: -5px; width: 11px; height: 11px;
            background: #e10016; border: 2px solid #fff; border-radius: 50%; z-index: 10;
        }
        .skill-tags li .card-inner-wrapper {
            width: 100% !important; flex: 1 !important; display: flex !important; flex-direction: column !important;
            align-items: center !important; text-decoration: none !important; color: inherit !important;
            padding: 10px 6px 0 6px !important; box-sizing: border-box !important; border-radius: 12px !important;
            overflow: visible !important; cursor: pointer !important;
        }
        .skill-tags li .card-inner-wrapper.is-sublang { cursor: default !important; opacity: 0.88; }
        .skill-tags li .flag-wrapper {
            width: 100% !important; height: 38px !important; flex-shrink: 0 !important;
            display: flex !important; align-items: center !important; justify-content: center !important;
        }
        .skill-tags li .flag-wrapper img {
            width: 38px !important; height: 26px !important; object-fit: cover !important;
            border-radius: 4px !important; box-shadow: 0 1px 4px rgba(0,0,0,0.12) !important;
        }
        .skill-tags li .flag-wrapper img.flag-placeholder {
            background: #e0e0e0 !important; object-fit: none !important; opacity: 0.5 !important;
        }
        .skill-tags li .level-dots {
            display: flex !important; flex-direction: row !important; flex-wrap: nowrap !important;
            gap: 3px !important; justify-content: center !important; align-items: center !important;
            height: 22px !important; min-height: 22px !important; flex-shrink: 0 !important;
            width: 100% !important; margin: 4px 0 !important;
        }
        .skill-tags li .level-dots .dot {
            display: inline-block !important; width: 6px !important; height: 6px !important;
            border-radius: 50% !important; flex-shrink: 0 !important;
        }
        .skill-tags li .lang-text-container {
            width: 100% !important; min-height: 32px !important;
            display: flex !important; align-items: center !important; justify-content: center !important;
            text-align: center !important; overflow: visible !important; flex-shrink: 0 !important; padding: 0 2px !important;
        }
        .skill-tags li .lang-text-container strong {
            display: block !important; font-size: 11px !important; font-weight: 600 !important;
            color: #1e293b !important; line-height: 1.3 !important; white-space: normal !important;
            word-break: break-word !important; text-align: center !important;
        }
        .skill-tags li .info-icon-container {
            position: relative !important; display: flex !important; justify-content: center !important;
            align-items: center !important; padding: 5px 0 !important; overflow: visible !important;
            flex-shrink: 0 !important; cursor: default !important;
        }
        .skill-tags li .info-icon-container .fa-info-circle { font-size: 13px !important; color: #b2b2b2 !important; transition: color 0.2s !important; }
        .skill-tags li .info-icon-container:hover .fa-info-circle { color: #e65f00 !important; }
        .skill-tags li .tooltip-box {
            display: none; position: absolute !important; bottom: 130% !important; left: 50% !important;
            transform: translateX(-50%) !important; width: 180px !important; white-space: normal !important;
            background: #2d3748 !important; color: #fff !important; font-size: 11px !important;
            line-height: 1.4 !important; padding: 7px 9px !important; border-radius: 6px !important;
            z-index: 99999 !important; pointer-events: none !important;
            box-shadow: 0 3px 10px rgba(0,0,0,0.22) !important; text-align: left !important;
        }
        .skill-tags li .tooltip-box::after {
            content: ''; position: absolute; top: 100%; left: 50%; transform: translateX(-50%);
            border: 5px solid transparent; border-top-color: #2d3748;
        }
        .skill-tags li .tooltip-box strong {
            display: block !important; color: #fff !important; font-size: 11px !important;
            font-weight: 700 !important; margin-bottom: 3px !important;
            border-bottom: 1px solid rgba(255,255,255,0.2) !important; padding-bottom: 3px !important;
        }
        .skill-tags li .info-icon-container:hover .tooltip-box { display: block; }
        .skill-tags li .lang-price {
            margin-top: auto !important; width: 100% !important; height: 32px !important;
            border-top: 1px solid #edf2f7 !important; display: flex !important; align-items: center !important;
            justify-content: center !important; font-size: 11px !important; font-weight: 600 !important;
            color: #64748b !important; background: #fafafa !important; border-radius: 0 0 12px 12px !important;
            flex-shrink: 0 !important; box-sizing: border-box !important;
        }
        .skill-tags li .lang-price-spacer { margin-top: auto !important; height: 32px !important; flex-shrink: 0 !important; }
        .skill-tags li.add-btn {
            display: flex !important; justify-content: center !important; align-items: center !important;
            background: #fffcf9 !important; border: 1px dashed #e65f00 !important; box-shadow: none !important; min-height: 175px !important;
        }
        .skill-tags li.add-btn:hover { background: #fff5ee !important; }
        .skill-tags li.add-btn a {
            display: flex !important; align-items: center !important; justify-content: center !important;
            width: 100% !important; height: 100% !important; font-size: 30px !important;
            color: #f56600 !important; text-decoration: none !important; transition: transform 0.2s !important;
        }
        .skill-tags li.add-btn a:hover { transform: scale(1.15) !important; }
        @media (max-width: 1100px) { .skill-tags { grid-template-columns: repeat(auto-fill, minmax(90px, 1fr)) !important; } }
        @media (max-width: 480px)  { .skill-tags { grid-template-columns: repeat(3, 1fr) !important; } }

        /* SUBLANG BADGE */
        .sublang-text {
            position: absolute; top: 4px; left: 4px; font-size: 9px; font-weight: 500;
            color: #888888; background: transparent; border: none; padding: 0; z-index: 5; line-height: 1.4;
        }

        /* MAIN CARD BLOCKS */
        .user-data, .post-bar, .suggestions, .widget {
            margin-bottom: 20px !important; border-radius: 12px !important;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08) !important; border-left: 5px solid #e65f00 !important; background: white;
        }
        .username-dt {
            display: flex; justify-content: center; align-items: center;
            background: linear-gradient(135deg, #e65f00, #c04e00) !important;
            padding: 25px 0 !important; border-radius: 12px 12px 0 0 !important;
        }
        .usr-pic {
            width: 150px !important; height: 150px !important; border-radius: 50% !important;
            overflow: hidden !important; border: 4px solid white !important;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2) !important; margin: 0 auto !important;
        }
        .usr-pic img { width: 100% !important; height: 100% !important; object-fit: cover !important; border-radius: 50% !important; }
        .user-specs { padding: 15px 12px !important; }
        .user-specs h3 { margin-bottom: 5px; text-align: center; font-size: 20px; font-weight: 700; }
        .user-specs span { display: flex; align-items: center; gap: 6px; margin: 8px 0; font-size: 14px; }
        .fas, .far, .fal, .fab, .fa { color: #e65f00 !important; }
        .job_descp h3, .sd-title h3 {
            font-size: 18px !important; font-weight: 600 !important; color: #2c3e50 !important;
            margin: 5px 0 15px 0 !important; display: flex !important; align-items: center !important; gap: 8px !important;
        }
        .job_descp h3 i { color: #e65f00 !important; font-size: 20px !important; }

        /* SUGGESTIONS / SIDEBAR WIDGETS */
        .suggestions { margin-bottom: 20px; }
        .suggestions .sd-title { padding: 15px 15px 5px; border-bottom: 1px solid #f0f0f0; }
        .suggestions .sd-title h3 { font-size: 16px; margin: 0; }
        .suggestions .suggestions-list { padding: 0 15px 15px; }
        .suggestions .view-more { text-align: center; width: 100%; margin-top: 10px; }
        .suggestions .view-more a { color: #e65f00; text-decoration: none; font-size: 12px; font-weight: 700; }
        .suggestion-usd { display: flex; align-items: center; gap: 12px; padding: 8px 0; }
        .suggestion-usd img { width: 35px; height: 35px; border-radius: 50%; object-fit: cover; }
        .suggestion-usd .sgt-text h4 { font-size: 14px; font-weight: 600; color: #2c3e50; margin: 0 0 4px 0; }
        .suggestion-usd .sgt-text p { font-size: 12px; color: #7f8c8d; margin: 0; }

        /* EVALUACIONES */
        .evaluation-compact { text-align: center; display: flex; flex-direction: column; gap: 4px; }
        .evaluation-compact .metric { display: flex; align-items: center; justify-content: center; gap: 8px; }
        .evaluation-compact .metric i { font-size: 24px; color: #e65f00; }
        .evaluation-compact .metric .percentage { font-size: 32px; font-weight: 700; color: #e65f00; line-height: 1; }
        .evaluation-compact .metric .percentage small { font-size: 18px; font-weight: 400; color: #95a5a6; margin-left: 2px; }
        .evaluation-compact .bar { width: 100%; height: 6px; background-color: #ecf0f1; border-radius: 3px; overflow: hidden; margin: 8px 0; }
        .evaluation-compact .bar-fill { height: 100%; background: linear-gradient(90deg, #e65f00, #f39c12); border-radius: 3px; }
        .evaluation-compact .total { font-size: 13px; font-weight: 600; color: #34495e; margin-top: 5px; }

        /* BOTÓN MEJORADO "VIEW ALL EVALUATIONS" */
        .evaluation-compact .ev-link {
            display: flex; align-items: center; justify-content: center; gap: 8px;
            margin-top: 14px; font-size: 14px; font-weight: 700; color: #ffffff !important;
            text-decoration: none; border: 2px solid #e65f00; border-radius: 8px; padding: 12px 20px;
            background: #e65f00;
            transition: background 0.2s ease, color 0.2s ease, box-shadow 0.2s ease, transform 0.15s ease;
            box-shadow: 0 2px 8px rgba(230, 95, 0, 0.18); width: 100%; box-sizing: border-box; cursor: pointer;
        }
        .evaluation-compact .ev-link i { transition: transform 0.2s ease; }
        .evaluation-compact .ev-link:hover {
            background: #e65f00; color: #ffffff !important;
            box-shadow: 0 5px 16px rgba(230, 95, 0, 0.38); transform: translateY(-1px);
        }
         .evaluation-compact .ev-link i{ color: #ffffff !important;}
        .evaluation-compact .ev-link:hover i { color: #ffffff !important; transform: translateX(4px); }
        .evaluation-compact .ev-link:active { transform: translateY(0); }

        .evaluation-item { display: flex; gap: 15px; padding: 15px 0; border-bottom: 1px solid #ecf0f1; position: relative; }
        .evaluation-item:last-child { border-bottom: none; }
        .evaluation-avatar { width: 50px; height: 50px; border-radius: 50%; overflow: hidden; flex-shrink: 0; }
        .evaluation-avatar img { width: 100%; height: 100%; object-fit: cover; }
        .evaluation-content { flex: 1; }
        .evaluation-header { display: flex; align-items: center; gap: 10px; margin-bottom: 5px; flex-wrap: wrap; }
        .evaluation-author { font-weight: 600; color: #2c3e50; font-size: 14px; }
        .evaluation-date { font-size: 12px; color: #95a5a6; display: flex; align-items: center; gap: 3px; }
        .evaluation-rating { font-size: 11px; font-weight: 600; padding: 2px 8px; border-radius: 12px; text-transform: uppercase; }
        .rating-positive { background-color: #e8f5e9; color: #27ae60; border: 1px solid #27ae60; }
        .rating-neutral  { background-color: #f5f5f5;  color: #7f8c8d; border: 1px solid #95a5a6; }
        .rating-negative { background-color: #fdeded;  color: #e74c3c; border: 1px solid #e74c3c; }
        .rating-noanswer { background-color: #fef5e7;  color: #f39c12; border: 1px solid #f39c12; }
        .evaluation-text { font-size: 13px; color: #34495e; line-height: 1.5; margin-top: 5px; word-break: break-word; }
        .ev-opts { position: relative; display: flex; align-items: flex-start; flex-shrink: 0; margin-left: 8px; }
        .ev-opts-open {
            display: flex !important; align-items: center !important; justify-content: center !important;
            width: 28px !important; height: 28px !important; border-radius: 50% !important; color: #aaa !important;
            font-size: 18px !important; text-decoration: none !important;
            transition: background 0.15s, color 0.15s !important; line-height: 1 !important;
        }
        .ev-opts-open:hover { background: #f0f0f0 !important; color: #555 !important; }
        .ev-options {
            display: none; position: absolute; top: 30px; right: 0; background: #fff;
            border: 1px solid #e0e0e0; border-radius: 8px; box-shadow: 0 4px 16px rgba(0,0,0,0.13);
            min-width: 170px; z-index: 9999; list-style: none !important; padding: 4px 0 !important; margin: 0 !important;
        }
        .ev-options.ev-open { display: block !important; }
        .ev-options li { padding: 0 !important; margin: 0 !important; }
        .ev-options li a {
            display: block !important; padding: 9px 16px !important; font-size: 13px !important;
            color: #333 !important; text-decoration: none !important; transition: background 0.12s !important;
        }
        .ev-options li a:hover { background: #fff5ee !important; color: #e65f00 !important; }
        .epi-sec { margin: 20px 0; }
        .post-bar { padding: 25px; }

        /* RIGHT SIDEBAR */
        .right-sidebar .widget { margin-bottom: 25px; }
        .right-sidebar .sd-title { padding: 15px 20px 10px; border-bottom: 1px solid #eaeef2; }
        .right-sidebar .sd-title h3 { font-size: 16px; font-weight: 700; margin: 0; display: flex; align-items: center; gap: 8px; }
        .right-sidebar .jobs-list { padding: 15px 20px; }
        .right-sidebar .job-info { margin-bottom: 18px; padding-bottom: 15px; border-bottom: 1px solid #f0f2f4; }
        .right-sidebar .job-info:last-child { border-bottom: none; margin-bottom: 0; padding-bottom: 0; }
        .right-sidebar .job-details h3 { font-size: 15px; font-weight: 600; margin: 0 0 5px 0; color: #2c3e50; }
        .right-sidebar .job-details p { font-size: 13px; color: #7f8c8d; margin: 2px 0; line-height: 1.5; }
        .right-sidebar .hr-rate { margin-top: 8px; margin-right: 20px; text-align: right; }
        .right-sidebar .hr-rate span {
            background-color: #fdf2e9; color: #e65f00; font-weight: 700; font-size: 14px;
            padding: 5px 12px; border-radius: 20px; display: inline-block;
        }
        .right-sidebar .view-more { text-align: center; margin-top: 15px; padding-top: 10px; border-top: 1px dashed #e0e4e8; }
        .right-sidebar .view-more a {
            color: #e65f00; font-weight: 600; font-size: 13px; text-decoration: none;
            display: inline-block; padding: 6px 18px; border: 1px solid #e65f00; border-radius: 25px; transition: all 0.2s;
        }
        .right-sidebar .view-more a:hover { background-color: #e65f00; color: white; }
        .right-sidebar .suggestion-usd { padding: 12px 0; border-bottom: 1px solid #f0f2f4; }
        .right-sidebar .suggestion-usd:last-child { border-bottom: none; }
    </style>
</head>

<body>
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-TSHHJ2LL"
        height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>

    <div class="wrapper">
        <header>
            <div class="container">
                <div class="header-data">
                    <div class="logo">
                        <a href="./me.php" title=""><img src="../public/images/logo-blanco.png" alt=""></a>
                    </div>
                    <div class="search-bar" style="width: 160px; margin-left: 20px;">
                        <form action="./u.php?">
                            <input type="text" name="identificador" placeholder="User id # (e.g., 2735)">
                            <button type="submit"><i class="la la-search"></i></button>
                        </form>
                    </div>
                    <nav>
                        <ul>
                            <li>
                                <a href="../search2/index_paginated.php" title="">
                                    <span><i class="fa fa-users" aria-hidden="true"></i></span> Partners
                                </a>
                                <ul>
                                    <li><a href="../search2/index_paginated.php" title="">Search for new partners</a></li>
                                    <li><a href="../bookmarks" title="">Favourite users</a></li>
                                    <li><a href="../bookmarks/friends.php" title="">Friends</a></li>
                                </ul>
                            </li>
                            <li>
                                <a href="../pms" title="">
                                    <span><i class="fa fa-solid fa-bolt" aria-hidden="true"></i></span> Alerts (<?php echo $nuevos_emails; ?>)
                                </a>
                            </li>
                            <li>
                                <a href="../trackerproposals/dashboard.php" title="">
                                    <span><i class='fas fa-chalkboard-teacher'></i></span> Lessons
                                </a>
                                <ul>
                                    <li><a href="../trackerproposals/received-futureclasses.php" title="">As a teacher</a></li>
                                    <li><a href="../trackerproposals/sent-futureclasses.php" title="">As a student</a></li>
                                </ul>
                            </li>
                            <li>
                                <a href="../chat" title="">
                                    <span><i class="fas fa-comments" aria-hidden="true"></i></span> Chat
                                </a>
                            </li>
                            <li>
                                <a href="../infouser/pendingactions.php" title="">
                                    <span><i class="fas fa-user-check" aria-hidden="true"></i></span> Actions (<?php echo $nuevos_vote_total; ?>)
                                </a>
                            </li>
                            <li>
                                <a href="../events/showallupcomingevents.php" title="">
                                    <span><i class="fas fa-globe-africa" aria-hidden="true"></i></span> Events
                                </a>
                            </li>
                        </ul>
                    </nav>

                    <div class="menu-btn">
                        <a href="#" title="">
                            <i class="fa fa-bars"></i>
                            <div style="color:white;font-weight:bold;float:left;position:relative;top:15px;right:-55px;width:27px;height:27px;line-height:30px;border-radius:50%;font-size:15px;text-align:center;background:red"><?php echo $total_n_alertas; ?></div>
                        </a>
                    </div>

                    <div id="l2-user-account-wrap">
                        <a id="l2-user-trigger" href="#" onclick="l2ToggleDropdown(event); return false;">
                            <img src="<?php echo $thumb_nombre; ?>?nocache=<?php echo time(); ?>" alt="<?php echo $nombre_usuar; ?>">
                            <span class="l2-name"><?php echo $nombre_usuar; ?></span>
                            <i class="fa fa-caret-down l2-arrow"></i>
                        </a>
                    </div>

                    <div id="l2-dropdown-panel">
                        <span class="l2-settings-title">Settings</span>
                        <ul class="l2-links">
                            <li><a href="../addlanguage/addlanguage.php">Add languages</a></li>
                            <li><a href="../addlanguage/deletelanguage.php">Delete languages</a></li>
                            <li><a href="./getgpsposition.php">Update GPS location</a></li>
                            <li><a href="./timeshift.php">Update timeshift</a></li>
                            <li><a href="../updatephoto">Change profile photo</a></li>
                            <li><a href="../updateinfo">Edit profile information</a></li>
                            <?php if ($is_teacher != 'teacher') { ?>
                                <li><a href="../updateinfo/insert_level_language.php">Edit level of requested languages</a></li>
                            <?php } ?>
                            <li><a href="../updateinfo/passwordreset.php">Reset password</a></li>
                            <li><a href="../recoveryandunregistration/deleterequest.php">Unregister</a></li>
                        </ul>
                        <div class="l2-logout-row">
                            <a href="./logout.php">Logout</a>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <main>
            <?php if (!$email_verified) { ?>
                <div class="alert alert-danger" align="center">
                    To see all the contents you need to validate your email address. If you did not receive any email click <a style="text-decoration:underline;" href="./verify_email.php">here</a>.
                </div>
            <?php } ?>

            <?php if ($gpslat11 == 0 and $gpslng11 == 0) { ?>
                <div class="alert alert-danger" align="center">
                    <strong>Important!</strong> Provide your location in order to continue. <strong><a href="./getgpsposition.php" style="text-decoration:underline;">Add location</a></strong>
                </div>
            <?php } elseif ($zonaHoraria == 'Antarctica/Casey') { ?>
                <div class="alert alert-danger" align="center">
                    <strong>Important!</strong> Provide time zone in order to continue. <strong><a href="./timeshift.php" style="text-decoration:underline;">Add time zone</a></strong>
                </div>
            <?php } elseif ($is_teacher != 'teacher') {
                $query110 = "SELECT couples2009antiguos.user_id_1 FROM couples2009antiguos INNER JOIN mentor2009 ON mentor2009.orden = couples2009antiguos.user_id_1 WHERE Email='" . $email_del_usu . "'";
                $result110 = mysqli_query($link, $query110);
                $n_veces_contactante = mysqli_num_rows($result110);
                if (!$n_veces_contactante) { ?>
                    <div class="alert alert-warning" align="center">
                        You didn't contact any user yet. Start <a href="./partners.php" style="text-decoration:underline;">looking for a language partner</a> now.
                    </div>
                <?php }
            } elseif ($is_teacher == 'teacher') {
                $query17 = "SELECT * FROM eventoslista WHERE id_creador=$identificador2017";
                $result17 = mysqli_query($link, $query17);
                $n_ev = mysqli_num_rows($result17);
                if (!$n_ev) { ?>
                    <div class="alert alert-warning" align="center">
                        You haven't created any event yet. <strong><a href="../events/showallupcomingevents.php" style="text-decoration:underline;">Create an event</a> now in order to find customers.</strong>
                    </div>
            <?php }
            } ?>

            <div class="main-section">
                <div class="container">
                    <div class="main-section-data">
                        <div class="row">

                            <!-- LEFT SIDEBAR -->
                            <div class="col-lg-3 col-md-4 pd-left-none no-pd">
                                <div class="main-left-sidebar no-margin">
                                    <div class="user-data full-width">
                                        <div class="user-profile">
                                            <?php
                                            $size = getimagesize("$foto_nombre");
                                            $profile_photo_height_for_photo = $size ? 80 + ($size[1] * (110 / $size[0])) : 190;
                                            ?>
                                            <div class="username-dt" style="height: <?php echo $profile_photo_height_for_photo; ?>px;">
                                                <div class="usr-pic">
                                                    <img src="<?php echo $foto_nombre; ?>?nocache=<?php echo time(); ?>">
                                                </div>
                                            </div>
                                            <div class="user-specs">
                                                <h3><?php
                                                    echo "$nombre_usuar <span style=\"color: #686868;font-size: 70%\">#$identificador2017</span>";
                                                ?></h3>
                                                <?php if (!$email_verified) { ?>
                                                    <span><a style="font-size:12px;font-weight:bold;color:#e65f00;" href="./verify_email.php">Verify your email</a></span>
                                                <?php } ?>
                                                <?php if (!($gpslat11 == 0 && $gpslng11 == 0)) { ?>
                                                    <span><i class="fas fa-map-marker-alt"></i> <?php echo $ciudad1; ?> area</span>
                                                <?php } ?>
                                                <span><i class="fas fa-clock"></i> Local time: <?php echo $horaFormateada2; ?></span>
                                                <?php
                                                $domain1 = substr($email_del_usu, (int) strpos($email_del_usu, '@') + 1);
                                                $query123 = "SELECT organization_name AS org_name FROM organization_emails orgem INNER JOIN organizations org ON org.organization_id = orgem.organization_id WHERE orgem.email_domain='$domain1'";
                                                $result123 = mysqli_query($link, $query123);
                                                if (mysqli_num_rows($result123)) {
                                                    $fila123 = mysqli_fetch_array($result123);
                                                    echo '<p style="color:#e65f00;margin-top:10px;"><i class="fas fa-building"></i> ' . $fila123['org_name'] . '</p>';
                                                }
                                                ?>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Translation Jobs -->
                                    <div class="suggestions full-width">
                                        <div class="sd-title">
                                            <h3><i class="fas fa-briefcase"></i> Translation Jobs for you</h3>
                                        </div>
                                        <div class="suggestions-list">
                                            <div class="suggestion-usd">
                                                <img src="<?php echo $path_photo ?: "../uploader/default.jpg"; ?>" height="35px" width="35px">
                                                <div class="sgt-text">
                                                    <h4>English to Spanish<br><?php echo $nombre_usuario_short; ?></h4>
                                                    <p>450words - <?php echo $precioprof ? $precioprof . "€" : "35€"; ?> (0.078&euro;/w.)</p>
                                                </div>
                                            </div>
                                            <div class="view-more">
                                                <a href="../testingprototypes/translationjob.php">View Job</a>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Long-term Jobs -->
                                    <div class="suggestions full-width">
                                        <div class="sd-title">
                                            <h3><i class="fas fa-building"></i> Long-term Jobs for you</h3>
                                        </div>
                                        <div class="suggestions-list">
                                            <div class="suggestion-usd">
                                                <img src="<?php echo $path_photo2 ?: "../uploader/default.jpg"; ?>" height="35px" width="35px">
                                                <div class="sgt-text">
                                                    <h4>Hewlett Packard <?php echo $nombre_usuario_short2; ?></h4>
                                                    <p>Business Analyst</p>
                                                    <p style="font-style:italic;font-size:85%;">Sant Cugat (Spain)<?php echo $ciudad97 ? " - " . $ciudad97 : ""; ?></p>
                                                </div>
                                            </div>
                                            <div class="view-more">
                                                <a href="../testingprototypes/longtermjob.php">View Job</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- CENTRAL COLUMN -->
                            <div class="col-lg-6 col-md-7 no-pd">
                                <div class="posts-section">
                                    <div id="seccion_teach" class="post-bar">
                                        <div class="epi-sec"></div>

                                        <!-- I know these languages -->
                                        <div class="job_descp">
                                            <h3><i class="fas fa-graduation-cap"></i> I know these languages</h3>
                                            <ul class="skill-tags">
                                                <?php for ($iii = 0; $iii < count($my_langs_array); $iii++):
                                                    $idof             = $my_langs_array[$iii];
                                                    $idof_2letras     = $my_langs_2letters_array[$iii];
                                                    $level_idiomaofre = $my_langs_level_array[$iii];
                                                    $full_name        = $my_langs_full_name_array[$iii];
                                                    $price_text       = $my_langs_priceorexchangetext_array[$iii] ?? '';
                                                    $tooltip_ofr      = $my_langs_typeofexchange_array[$iii] ?? '';
                                                    $macrolang        = $my_langs_macrolang_array[$iii] ?? '';
                                                    $lang_display_text = strtolower($idof) . ' (' . ($idiomas_nivel[$level_idiomaofre] ?? '*') . ')';
                                                    $is_sublanguage   = !empty($macrolang);
                                                    $necesita_accion  = ($level_idiomaofre <= 1);

                                                    // Obtener ruta de bandera según el search
                                                    $ruta_bandera = getFlagPath($idof_2letras, $idof);
                                                    
                                                    // FORZAR PLACEHOLDER PARA SUBIDIOMAS
                                                    if ($is_sublanguage) {
                                                        $ruta_bandera = './images/placeholder.png';
                                                    }
                                                    
                                                    $flag_is_placeholder = ($ruta_bandera === './images/placeholder.png' || strpos($ruta_bandera, 'placeholder') !== false);

                                                    if ($is_sublanguage) {
                                                        $alert_msg = addslashes("'$idof' is a sublanguage of '$macrolang'. You cannot modify or delete a sublanguage directly. You need to click on the '$macrolang' flag to make modifications.");
                                                        $card_onclick = "onclick=\"alert('$alert_msg'); return false;\"";
                                                        $card_href = '#';
                                                        $card_class = 'is-sublang';
                                                    } else {
                                                        $card_onclick = '';
                                                        $card_href = "../addlanguage/addlanguage.php?lang=" . urlencode($idof) . "&use=know";
                                                        $card_class = '';
                                                    }
                                                ?>
                                                    <li class="<?php echo $necesita_accion ? 'low-level-border' : ''; ?>">
                                                        <?php if ($necesita_accion): ?>
                                                            <span class="bolita-accion"></span>
                                                        <?php endif; ?>
                                                        <?php if ($is_sublanguage): ?>
                                                            <span class="sublang-text" title="Sublanguage of <?php echo htmlspecialchars($macrolang); ?>">sub</span>
                                                        <?php endif; ?>
                                                        <a class="card-inner-wrapper <?php echo $card_class; ?>"
                                                           href="<?php echo $card_href; ?>"
                                                           <?php echo $card_onclick; ?>
                                                           style="text-decoration:none;color:inherit;">
                                                            <div class="flag-wrapper">
                                                                <img src="<?php echo htmlspecialchars($ruta_bandera, ENT_QUOTES); ?>"
                                                                     alt="<?php echo htmlspecialchars($idof, ENT_QUOTES); ?>"
                                                                     <?php if ($flag_is_placeholder) echo 'class="flag-placeholder"'; ?>>
                                                            </div>
                                                            <?php echo renderLevelDots($level_idiomaofre); ?>
                                                            <div class="lang-text-container">
                                                                <strong><?php echo htmlspecialchars($lang_display_text, ENT_QUOTES); ?></strong>
                                                            </div>
                                                            <div class="info-icon-container">
                                                                <i class="fas fa-info-circle"></i>
                                                                <span class="tooltip-box">
                                                                    <strong><?php echo htmlspecialchars($full_name, ENT_QUOTES); ?>:</strong>
                                                                    <?php echo $is_sublanguage ? 'Sublanguage of ' . htmlspecialchars($macrolang, ENT_QUOTES) . '. Click the parent flag to edit.' : htmlspecialchars($tooltip_ofr, ENT_QUOTES); ?>
                                                                </span>
                                                            </div>
                                                            <?php if (!empty($price_text)): ?>
                                                                <div class="lang-price"><?php echo $price_text; ?></div>
                                                            <?php else: ?>
                                                                <div class="lang-price-spacer"></div>
                                                            <?php endif; ?>
                                                        </a>
                                                    </li>
                                                <?php endfor; ?>
                                                <li class="add-btn">
                                                    <a href="../addlanguage/addlanguage.php" title="Add language">
                                                        <i class="fas fa-plus-circle"></i>
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>

                                        <div class="epi-sec"></div>

                                        <!-- I want to learn these languages -->
                                        <div class="job_descp">
                                            <h3><i class="fas fa-book-open"></i> I want to learn these languages</h3>
                                            <ul class="skill-tags">
                                                <?php for ($iii = 0; $iii < count($learn_langs_array); $iii++):
                                                    $idof             = $learn_langs_array[$iii];
                                                    $idof_2letras     = $learn_langs_2letters_array[$iii];
                                                    $level_idiomaofre = $learn_langs_level_array[$iii];
                                                    $full_name        = $learn_langs_full_name_array[$iii];
                                                    $nivel_label      = $idiomas_nivel[$level_idiomaofre] ?? '—';
                                                    $macrolang        = $learn_langs_macrolang_array[$iii] ?? '';
                                                    $lang_display_text = strtolower($idof) . ' (' . $nivel_label . ')';
                                                    $is_sublanguage   = !empty($macrolang);
                                                    $necesita_accion  = ($level_idiomaofre <= 1);

                                                    // Obtener ruta de bandera según el search
                                                    $ruta_bandera = getFlagPath($idof_2letras, $idof);
                                                    
                                                    // FORZAR PLACEHOLDER PARA SUBIDIOMAS
                                                    if ($is_sublanguage) {
                                                        $ruta_bandera = './images/placeholder.png';
                                                    }
                                                    
                                                    $flag_is_placeholder = ($ruta_bandera === './images/placeholder.png' || strpos($ruta_bandera, 'placeholder') !== false);

                                                    if ($is_sublanguage) {
                                                        $alert_msg = addslashes("'$idof' is a sublanguage of '$macrolang'. You cannot modify or delete a sublanguage directly. You need to click on the '$macrolang' flag to make modifications.");
                                                        $card_onclick = "onclick=\"alert('$alert_msg'); return false;\"";
                                                        $card_href = '#';
                                                        $card_class = 'is-sublang';
                                                    } else {
                                                        $card_onclick = '';
                                                        $card_href = "../addlanguage/addlanguage.php?lang=" . urlencode($idof) . "&use=learn";
                                                        $card_class = '';
                                                    }
                                                ?>
                                                    <li class="<?php echo $necesita_accion ? 'low-level-border' : ''; ?>">
                                                        <?php if ($necesita_accion): ?>
                                                            <span class="bolita-accion"></span>
                                                        <?php endif; ?>
                                                        <?php if ($is_sublanguage): ?>
                                                            <span class="sublang-text" title="Sublanguage of <?php echo htmlspecialchars($macrolang); ?>">sub</span>
                                                        <?php endif; ?>
                                                        <a class="card-inner-wrapper <?php echo $card_class; ?>"
                                                           href="<?php echo $card_href; ?>"
                                                           <?php echo $card_onclick; ?>
                                                           style="text-decoration:none;color:inherit;">
                                                            <div class="flag-wrapper">
                                                                <img src="<?php echo htmlspecialchars($ruta_bandera, ENT_QUOTES); ?>"
                                                                     alt="<?php echo htmlspecialchars($idof, ENT_QUOTES); ?> flag"
                                                                     <?php if ($flag_is_placeholder) echo 'class="flag-placeholder"'; ?>>
                                                            </div>
                                                            <?php echo renderLevelDots($level_idiomaofre); ?>
                                                            <div class="lang-text-container">
                                                                <strong><?php echo htmlspecialchars($lang_display_text, ENT_QUOTES); ?></strong>
                                                            </div>
                                                            <div class="info-icon-container" style="margin-top:auto;">
                                                                <i class="fas fa-info-circle"></i>
                                                                <span class="tooltip-box">
                                                                    <strong><?php echo htmlspecialchars($full_name, ENT_QUOTES); ?>:</strong>
                                                                    <?php echo $is_sublanguage ? 'Sublanguage of ' . htmlspecialchars($macrolang, ENT_QUOTES) . '. Click the parent flag to edit.' : htmlspecialchars($nivel_label, ENT_QUOTES); ?>
                                                                </span>
                                                            </div>
                                                            <div class="lang-price-spacer"></div>
                                                        </a>
                                                    </li>
                                                <?php endfor; ?>
                                                <li class="add-btn">
                                                    <a href="../addlanguage/addlanguage.php" title="Add language">
                                                        <i class="fas fa-plus-circle"></i>
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>

                                        <div class="epi-sec"></div>

                                        <!-- More information -->
                                        <div class="job_descp">
                                            <h3><i class="fas fa-info-circle"></i> More information</h3>
                                            <?php if (!empty($availability100)): ?>
                                                <p style="margin-bottom:12px;"><i class="fas fa-clock"></i> <?php echo nl2br(htmlspecialchars($availability100)); ?></p>
                                            <?php endif; ?>
                                            <?php if (!empty($othercomments100)): ?>
                                                <p><i class="fas fa-comment"></i> <?php echo nl2br(htmlspecialchars($othercomments100)); ?></p>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>

                                <!-- Evaluations -->
                                <div class="suggestions full-width" style="margin-top:20px;">
                                    <div class="sd-title">
                                        <h3><i class="fas fa-star"></i> Evaluations</h3>
                                    </div>
                                    <div class="suggestions-list">
                                        <?php if ($n_comentarios > 0): ?>
                                            <div class="evaluation-compact" style="margin-bottom:20px;padding-bottom:15px;border-bottom:1px solid #ecf0f1;">
                                                <div class="metric">
                                                    <i class="fas fa-star"></i>
                                                    <span class="percentage"><?php echo $porcentaje_positivos; ?><small>%</small></span>
                                                </div>
                                                <div class="bar">
                                                    <div class="bar-fill" style="width:<?php echo $porcentaje_positivos; ?>%;"></div>
                                                </div>
                                                <div class="total">
                                                    Based on <?php echo $n_comentarios; ?> review<?php echo $n_comentarios > 1 ? 's' : ''; ?>
                                                </div>
                                                <a href="../infouser/evdone.php" class="ev-link">
                                                    <i class="fas fa-list"></i>
                                                    View all evaluations
                                                </a>
                                            </div>

                                            <?php
                                            $num_ev_bucle = min($num_max_ev_mostradas, $num_evaluaciones);
                                            for ($jjj = 0; $jjj < $num_ev_bucle; $jjj++):
                                                $fila1010      = mysqli_fetch_array($result1010);
                                                $comentario_ev = $fila1010['comment'];
                                                $hora_ev       = $fila1010['hora'];
                                                $rating_ev     = $fila1010['rating'];

                                                if ($rating_ev == 1)      { $rating_text = "POSITIVE";  $rating_class = "rating-positive"; }
                                                elseif ($rating_ev == 2)  { $rating_text = "NEUTRAL";   $rating_class = "rating-neutral"; }
                                                elseif ($rating_ev == 3)  { $rating_text = "NEGATIVE";  $rating_class = "rating-negative"; }
                                                elseif ($rating_ev == 4)  { $rating_text = "NO ANSWER"; $rating_class = "rating-noanswer"; }
                                                else                      { $rating_text = $rating_ev;  $rating_class = "rating-neutral"; }

                                                $autor_ev = $fila1010['nombre1'];
                                                if (!is_null($autor_ev)) {
                                                    $palabras = explode(" ", $autor_ev);
                                                    $autor_ev = ucfirst($palabras[0]);
                                                }
                                                if ($autor_ev == '') $autor_ev = "User unregistered";

                                                $orden47        = $fila1010['orden1'];
                                                $foto_extension = $fila1010['fotoext1'];
                                                $foto_autor     = "../uploader/upload_pic/thumb_$orden47.$foto_extension";
                                                if (!file_exists($foto_autor)) $foto_autor = "../uploader/default.jpg";
                                            ?>
                                                <div class="evaluation-item">
                                                    <div class="evaluation-avatar">
                                                        <img src="<?php echo $foto_autor; ?>" alt="<?php echo htmlspecialchars($autor_ev); ?>">
                                                    </div>
                                                    <div class="evaluation-content">
                                                        <div class="evaluation-header">
                                                            <span class="evaluation-author">
                                                                <?php if ($orden47): ?>
                                                                    <a href="./u.php?identificador=<?php echo $orden47; ?>" style="color:#2c3e50;text-decoration:none;"><?php echo htmlspecialchars($autor_ev); ?></a>
                                                                <?php else: ?>
                                                                    <?php echo htmlspecialchars($autor_ev); ?>
                                                                <?php endif; ?>
                                                            </span>
                                                            <span class="evaluation-date"><i class="far fa-calendar-alt"></i> <?php echo date("Y-m-d", strtotime($hora_ev)); ?></span>
                                                            <span class="evaluation-rating <?php echo $rating_class; ?>"><?php echo $rating_text; ?></span>
                                                        </div>
                                                        <div class="evaluation-text">
                                                            <?php echo nl2br(htmlspecialchars($comentario_ev)); ?>
                                                        </div>
                                                    </div>
                                                    <div class="ev-opts">
                                                        <a href="#" class="ev-opts-open" onclick="evOptsToggle(event, this);" title="Options">
                                                            <i class="la la-ellipsis-v"></i>
                                                        </a>
                                                        <ul class="ev-options">
                                                            <?php if ($orden47): ?>
                                                                <li><a href="./u.php?identificador=<?php echo $orden47; ?>">Visit profile</a></li>
                                                            <?php endif; ?>
                                                            <li><a href="./reportabuse.php">Report abuse</a></li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            <?php endfor; ?>

                                            

                                        <?php else: ?>
                                            <div style="text-align:center;padding:20px 0;">
                                                <i class="fas fa-star" style="font-size:24px;opacity:0.3;margin-bottom:10px;display:block;"></i>
                                                <p style="color:#7f8c8d;">No evaluations yet</p>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>

                            <!-- RIGHT SIDEBAR -->
                            <div class="col-lg-3 pd-right-none no-pd">
                                <div class="right-sidebar">
                                    <!-- My Events -->
                                    <div id="my_events" class="widget widget-jobs">
                                        <div class="sd-title">
                                            <h3><i class="fas fa-calendar-alt"></i> My Events</h3>
                                        </div>
                                        <div class="jobs-list">
                                            <?php
                                            require('../files/idiomasequivalencias.php');
                                            $tiempo_corte    = time() - 24 * 3600;
                                            $max_events_shown = 5;
                                            $query = "SELECT * FROM eventoslista WHERE unix_start_time>'$tiempo_corte' AND id_creador='$identificador2017' AND Yaborrado='0' ORDER BY unix_start_time ASC";
                                            $result = mysqli_query($link, $query);
                                            $nuevos = mysqli_num_rows($result);

                                            if (!$nuevos) {
                                                echo '<div class="job-info"><p>You have not created any event. <a href="../events/createevent.php">Create one event</a> now.</p></div>';
                                            } else {
                                                $max_events_shown_2 = min($max_events_shown, $nuevos);
                                                for ($i = 0; $i < $max_events_shown_2; $i++) {
                                                    $fila            = mysqli_fetch_array($result);
                                                    $id_event1       = $fila['Id'];
                                                    $ciudad_abbr     = substr($fila['city'], 0, 14);
                                                    $lengua1         = isset($idiomas_equiv[$fila['Idioma']]) ? $idiomas_equiv[$fila['Idioma']] : $fila['Idioma'];
                                                    $lengua1         = substr($lengua1, 0, 14);
                                                    $fecha1          = substr($fila['start_time'], 0, 10);
                                                    $dayOfWeek_corto = substr(date("l", strtotime($fecha1)), 0, 3);
                                                    $ev_broadcasted  = $fila['Broadcasted'];
                                                    $es_replica_ev   = $fila['Createdfromid'];
                                                ?>
                                                    <div class="job-details sgt-text suggestion-usd" style="display:flex;justify-content:space-between;align-items:center;padding:15px 0;border-bottom:1px solid #eee;">
                                                        <div class="text-content">
                                                            <a href="../events/eventdetails.php?idev=<?php echo $id_event1; ?>">
                                                                <h4 style="margin:0;font-size:16px;color:#333;font-weight:bold;"><?php echo $fecha1 . " (" . $dayOfWeek_corto . ")"; ?></h4>
                                                            </a>
                                                            <p style="margin:5px 0 0 0;color:#888;">In <?php echo $ciudad_abbr; ?> (<?php echo $lengua1; ?>)</p>
                                                            <?php if (!$ev_broadcasted || is_null($es_replica_ev)): ?>
                                                                <div style="font-size:12px;margin-top:8px;">
                                                                    <?php if (!$ev_broadcasted) echo "<a href='../events/radaruserevent.php?evid=$id_event1' style='color:#ff5e00;'>Promote</a> · "; ?>
                                                                    <?php if (is_null($es_replica_ev)) echo "<a href='../events/makerecurrentshowinfo.php?idev=$id_event1' style='color:#ff5e00;'>Make weekly</a> · "; ?>
                                                                    <a href="../events/cancelevent.php?idev=<?php echo $id_event1; ?>" style="color:#ff5e00;">Cancel</a>
                                                                </div>
                                                            <?php endif; ?>
                                                        </div>
                                                        <div class="hr-rate">
                                                            <a href="../events/eventdetails.php?idev=<?php echo $id_event1; ?>" style="display:flex;align-items:center;justify-content:center;width:35px;height:35px;background:#fff5f0;border-radius:50%;color:#ff5e00;text-decoration:none;">
                                                                <i class="la la-chevron-right" style="font-weight:bold;"></i>
                                                            </a>
                                                        </div>
                                                    </div>
                                                <?php }
                                            } ?>
                                        </div>
                                    </div>

                                    <!-- Events worldwide -->
                                    <div id="events" class="widget widget-jobs">
                                        <div class="sd-title">
                                            <h3><i class="fas fa-globe"></i> Events worldwide</h3>
                                        </div>
                                        <div class="jobs-list">
                                            <div class="job-info">
                                                <?php
                                                $tiempo_corte    = time() - 24 * 3600;
                                                $max_events_shown = 5;
                                                $query = "SELECT * FROM eventoslista WHERE unix_start_time>'$tiempo_corte' AND Yaborrado='0' ORDER BY unix_start_time ASC";
                                                $result = mysqli_query($link, $query);
                                                $nuevos = mysqli_num_rows($result);

                                                if (!$nuevos) echo "No events at the moment. <a href=\"../events/createevent.php\">Create one event</a> now.";

                                                $max_events_shown_2 = min($max_events_shown, $nuevos);
                                                for ($i = 0; $i < $max_events_shown_2; $i++) {
                                                    $fila            = mysqli_fetch_array($result);
                                                    $ev_broadcasted  = $fila['Broadcasted'];
                                                    $ciudad_abbr     = substr($fila['city'], 0, 14);
                                                    $lengua1         = isset($idiomas_equiv[$fila['Idioma']]) ? $idiomas_equiv[$fila['Idioma']] : $fila['Idioma'];
                                                    $lengua1         = substr($lengua1, 0, 14);
                                                    $fecha1          = substr($fila['start_time'], 0, 10);
                                                    $dayOfWeek_corto = substr(date("l", strtotime($fecha1)), 0, 3);
                                                ?>
                                                    <div class="job-details sgt-text suggestion-usd" style="display:flex;justify-content:space-between;align-items:center;padding:15px 0;border-bottom:1px solid #eee;">
                                                        <div class="text-content">
                                                            <a href="../events/eventdetails.php?idev=<?php echo $fila['Id']; ?>">
                                                                <h4 style="margin:0;font-size:16px;color:#333;"><?php echo $fecha1 . " (" . $dayOfWeek_corto . ")"; ?></h4>
                                                            </a>
                                                            <p style="margin:5px 0 0 0;color:#888;">
                                                                In <?php echo $ciudad_abbr; ?> (<?php echo $lengua1; ?>)
                                                                <?php if ($ev_broadcasted): ?>
                                                                    <img src="../images/recommended.png" alt="Promoted" height="15" style="vertical-align:middle;margin-left:5px;" />
                                                                <?php endif; ?>
                                                            </p>
                                                        </div>
                                                        <div class="hr-rate">
                                                            <a href="../events/eventdetails.php?idev=<?php echo $fila['Id']; ?>" style="display:flex;align-items:center;justify-content:center;width:35px;height:35px;background:#fff5f0;border-radius:50%;color:#ff5e00;text-decoration:none;">
                                                                <i class="la la-chevron-right" style="font-weight:bold;"></i>
                                                            </a>
                                                        </div>
                                                    </div>
                                                <?php } ?>
                                                <?php if ($nuevos >= $max_events_shown): ?>
                                                    <div class="view-more" style="padding-top:15px;text-align:center;">
                                                        <a href="../events/showallupcomingevents.php" style="color:#ff5e00;font-weight:bold;">View More</a>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Most Viewed Jobs -->
                                    <div class="widget">
                                        <div class="sd-title">
                                            <h3><i class="fas fa-chart-line"></i> Most Viewed Jobs</h3>
                                        </div>
                                        <div class="jobs-list">
                                            <div class="job-info">
                                                <div class="job-details">
                                                    <h3>Controller Junior</h3>
                                                    <p>Deloitte (Remote job)</p>
                                                </div>
                                                <div class="hr-rate"><span>22,000&euro;/year</span></div>
                                            </div>
                                            <div class="job-info">
                                                <div class="job-details">
                                                    <h3>Senior UI / UX Designer</h3>
                                                    <p>Lintech (Barcelona)</p>
                                                </div>
                                                <div class="hr-rate"><span>21&euro;/h</span></div>
                                            </div>
                                            <div class="job-info">
                                                <div class="job-details">
                                                    <h3>Junior SEO Designer</h3>
                                                    <p>HCL (Remote job)</p>
                                                </div>
                                                <div class="hr-rate"><span>16&euro;/h</span></div>
                                            </div>
                                            <div class="view-more">
                                                <a href="../testingprototypes/mostviewedjobs.php">Apply Now</a>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Looking for Freelancers -->
                                    <div class="widget">
                                        <div class="sd-title">
                                            <h3><i class="fas fa-briefcase"></i> Looking for Freelancers?</h3>
                                        </div>
                                        <div class="jobs-list">
                                            <div class="job-info">
                                                <div class="job-details">
                                                    <p>Translations, voice over,</p>
                                                    <p>subtitles, long-term job offers...</p>
                                                </div>
                                            </div>
                                            <div class="view-more">
                                                <a href="../testingprototypes/publishjob.php">Publish Now</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        // USER ACCOUNT DROPDOWN
        (function() {
            var panel = document.getElementById('l2-dropdown-panel');
            if (panel) document.body.appendChild(panel);
        })();

        function l2PositionPanel() {
            var trigger = document.getElementById('l2-user-trigger');
            var panel   = document.getElementById('l2-dropdown-panel');
            if (!trigger || !panel) return;
            var rect = trigger.getBoundingClientRect();
            panel.style.top   = (rect.bottom + 4) + 'px';
            panel.style.right = (window.innerWidth - rect.right) + 'px';
            panel.style.left  = 'auto';
        }

        function l2ToggleDropdown(e) {
            e.preventDefault();
            e.stopPropagation();
            var trigger = document.getElementById('l2-user-trigger');
            var panel   = document.getElementById('l2-dropdown-panel');
            if (!trigger || !panel) return;
            var isOpen = panel.classList.contains('l2-open');
            document.querySelectorAll('.ev-options.ev-open').forEach(function(m) { m.classList.remove('ev-open'); });
            if (isOpen) {
                panel.classList.remove('l2-open');
                trigger.classList.remove('is-open');
            } else {
                l2PositionPanel();
                panel.classList.add('l2-open');
                trigger.classList.add('is-open');
            }
        }

        // EVALUATION 3-DOTS MENU
        function evOptsToggle(e, btn) {
            e.preventDefault();
            e.stopPropagation();
            var l2panel = document.getElementById('l2-dropdown-panel');
            var l2trig  = document.getElementById('l2-user-trigger');
            if (l2panel) l2panel.classList.remove('l2-open');
            if (l2trig)  l2trig.classList.remove('is-open');
            var menu    = btn.nextElementSibling;
            var wasOpen = menu.classList.contains('ev-open');
            document.querySelectorAll('.ev-options.ev-open').forEach(function(m) { m.classList.remove('ev-open'); });
            if (!wasOpen) menu.classList.add('ev-open');
        }

        document.addEventListener('click', function(e) {
            var trigger = document.getElementById('l2-user-trigger');
            var panel   = document.getElementById('l2-dropdown-panel');
            if (panel && trigger && !trigger.contains(e.target) && !panel.contains(e.target)) {
                panel.classList.remove('l2-open');
                trigger.classList.remove('is-open');
            }
            if (!e.target.closest('.ev-opts')) {
                document.querySelectorAll('.ev-options.ev-open').forEach(function(m) { m.classList.remove('ev-open'); });
            }
        });

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                var panel   = document.getElementById('l2-dropdown-panel');
                var trigger = document.getElementById('l2-user-trigger');
                if (panel)   panel.classList.remove('l2-open');
                if (trigger) trigger.classList.remove('is-open');
                document.querySelectorAll('.ev-options.ev-open').forEach(function(m) { m.classList.remove('ev-open'); });
            }
        });

        window.addEventListener('resize', function() {
            var panel = document.getElementById('l2-dropdown-panel');
            if (panel && panel.classList.contains('l2-open')) l2PositionPanel();
        });

        // MOBILE RESPONSIVE CLONING
        $(document).ready(function() {
            var column1 = $('#seccion_teach').clone().attr('id', 'seccion_teach_clone');
            $('#column1').append(column1);
            var column2 = $('#events').clone().attr('id', 'events_clone');
            $('#column2').append(column2);
            var column3 = $('#my_events').clone().attr('id', 'my_events_clone');
            $('#column3').append(column3);
            resize_movil();
            window.addEventListener("resize", function() { resize_movil(); });
        });

        function resize_movil() {
            $("#seccion_teach_clone").css("margin-bottom", "0px");
            $("#events_clone").css("margin-bottom", "0px");
            if (screen.width < 768) {
                $("#seccion_teach").attr("hidden", true);
                $("#seccion_teach_clone").attr("hidden", false);
                $("#events").attr("hidden", true);
                $("#events_clone").attr("hidden", false);
                $("#my_events").attr("hidden", true);
                $("#my_events_clone").attr("hidden", false);
            } else {
                $("#seccion_teach").attr("hidden", false);
                $("#seccion_teach_clone").attr("hidden", true);
                $("#events").attr("hidden", false);
                $("#events_clone").attr("hidden", true);
                $("#my_events").attr("hidden", false);
                $("#my_events_clone").attr("hidden", true);
            }
        }
    </script>

    <?php require('../templates/footer.php'); ?>
</body>

</html>