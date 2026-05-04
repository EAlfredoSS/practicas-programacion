<?php
// ============================================================
// BACKEND — lógica extraída de old_u.php (no modificar)
// ============================================================
session_start();

require('../files/bd.php');
require('../files/idiomasnivel.php');
require('../funcionesphp/funciones_idiomas_usuario.php');

$identificador_usu_buscado = $_GET['identificador'];
$identificador2017         = $_SESSION['orden2017'];
$_SESSION['idusuario2019'] = $identificador2017;

$tiempo_unix_actual = time();
$query_update_lastaction = "UPDATE mentor2009 SET lastaction = $tiempo_unix_actual WHERE orden = $identificador2017";
mysqli_query($link, $query_update_lastaction);

// ---- Usuario logueado ----
$query234 = "SELECT * FROM mentor2009 WHERE orden='" . $identificador2017 . "'";
$result234 = mysqli_query($link, $query234);
if (!mysqli_num_rows($result234)) die("User unregistered. <a href=\"http://www.lingua2.com\">Information</a>");
$fila234   = mysqli_fetch_array($result234);

$mi_gpslat = $fila234['Gpslat'];
$mi_gpslng = $fila234['Gpslng'];
$mi_email  = $fila234['Email'];

// ---- Usuario buscado ----
$query = "SELECT * FROM mentor2009 WHERE orden='" . $identificador_usu_buscado . "'";
$result = mysqli_query($link, $query);
if (!mysqli_num_rows($result)) die("User unregistered. <a href=\"http://www.lingua2.com\">Information</a>");
$fila = mysqli_fetch_array($result);

$ciudad1          = $fila['Ciudad'];
$id_del_receptor  = $fila['orden'];
$gpslat11         = $fila['Gpslat'];
$gpslng11         = $fila['Gpslng'];
$email_del_usu    = $fila['Email'];
$email_verified   = $fila['Emailverif'];
$availability100  = $fila['Disponibilidadcomentarios'];
$othercomments100 = $fila['Otroscomentarios'];
$fb_ident         = $fila['fbid'];

$zonahoraria= $fila['timeshift'];

function obtenerHora($fechaHora)
{
    $timestamp = strtotime($fechaHora);
    return date("H:i", $timestamp);
}

$fechaHoraFormateada22 = obtenerFechaHora($tiempo_unix_actual, $zonahoraria);
$horaFormateada22      = obtenerHora($fechaHoraFormateada22);

// Foto principal
$foto_nombre = $fila['orden'];
foreach (['jpg','png','gif','bmp'] as $ext) {
    $c = "../uploader/upload_pic/$foto_nombre.$ext";
    if (file_exists($c)) { $foto_nombre = $c; break; }
    if ($ext === 'bmp')  $foto_nombre = "../uploader/default.jpg";
}

// Thumb
$thumb_nombre = $fila['orden'];
foreach (['jpg','png','gif','bmp'] as $ext) {
    $c = "../uploader/upload_pic/thumb_$thumb_nombre.$ext";
    if (file_exists($c)) { $thumb_nombre = $c; break; }
    if ($ext === 'bmp')  $thumb_nombre = "../uploader/default.jpg";
}

$nombre_usuar = $fila["nombre"];
$arr = explode(' ', trim($nombre_usuar));
$nombre_usuar = ucfirst(substr($arr[0], 0, 13));

// Distancia
$distancia_entre_partners = 0;
if ($mi_gpslat && $mi_gpslng && $gpslat11 && $gpslng11) {
    $query333 = "SELECT (acos(sin(radians($mi_gpslat)) * sin(radians($gpslat11)) +
                 cos(radians($mi_gpslat)) * cos(radians($gpslat11)) *
                 cos(radians($mi_gpslng) - radians($gpslng11))) * 6378) AS distanciaPunto1Punto2";
    $result333 = mysqli_query($link, $query333);
    if ($result333 && mysqli_num_rows($result333)) {
        $fila333 = mysqli_fetch_array($result333);
        $distancia_entre_partners = round($fila333['distanciaPunto1Punto2'], 2);
    }
}

// Organización
$org_name_display = '';
$domain1 = substr($email_del_usu, (int) strpos($email_del_usu, '@') + 1);
$query123 = "SELECT organization_name AS org_name, org.organization_id AS org_id
             FROM organization_emails orgem
             INNER JOIN organizations org ON org.organization_id = orgem.organization_id
             WHERE orgem.email_domain='$domain1'";
$result123 = mysqli_query($link, $query123);
if (mysqli_num_rows($result123)) {
    $fila123 = mysqli_fetch_array($result123);
    $organization1    = $fila123['org_name'];
    $organization_id1 = $fila123['org_id'];
    $domain2 = substr($mi_email, (int) strpos($mi_email, '@') + 1);
    $query990 = "SELECT org.organization_id AS org_id
                 FROM organization_emails orgem
                 INNER JOIN organizations org ON org.organization_id = orgem.organization_id
                 WHERE orgem.email_domain='$domain2'";
    $result990 = mysqli_query($link, $query990);
    if (mysqli_num_rows($result990)) {
        $fila990 = mysqli_fetch_array($result990);
        $organization_id2 = $fila990['org_id'];
    }
    if (!empty($organization_id1) && isset($organization_id2) && $organization_id2 == $organization_id1) {
        $org_name_display = $organization1;
    }
}

// Bookmark
$id_contactante           = $identificador2017;
$identificador_contactado = $identificador_usu_buscado;
$query642  = "SELECT * FROM bookmarkedusers WHERE userwhosaves='$id_contactante' AND userwhoissaved='$identificador_contactado'";
$result642 = mysqli_query($link, $query642);
$ya_en_bookmarks = mysqli_num_rows($result642) > 0;

// Couple
$query1  = "SELECT * FROM couples2009antiguos WHERE (user_id_1='$id_contactante' AND user_id_2='$identificador_contactado') OR (user_id_2='$id_contactante' AND user_id_1='$identificador_contactado')";
$result1 = mysqli_query($link, $query1);
$ya_couple = mysqli_num_rows($result1) > 0;

// Evaluaciones
$query_ev_total = "SELECT * FROM comentarios WHERE id_aludido='$identificador_usu_buscado' AND censurado=0 ORDER BY horacreacion DESC";
$result_ev_total = mysqli_query($link, $query_ev_total);
$n_comentarios   = mysqli_num_rows($result_ev_total);

$query_ev_pos = "SELECT * FROM comentarios WHERE id_aludido='$identificador_usu_buscado' AND censurado=0 AND rating=1 ORDER BY horacreacion DESC";
$result_ev_pos = mysqli_query($link, $query_ev_pos);
$n_comentarios_positivos = mysqli_num_rows($result_ev_pos);

$porcentaje_positivos = ($n_comentarios != 0) ? round($n_comentarios_positivos * 100 / $n_comentarios) : 0;

$num_max_ev_mostradas = 3;
$query1010 = "SELECT m.nombre AS nombre1, m.orden AS orden1, m.fotoext AS fotoext1,
              comentarios.comment, comentarios.hora, comentarios.rating
              FROM comentarios
              LEFT JOIN mentor2009 AS m ON m.orden = comentarios.id_autor
              WHERE comentarios.id_aludido='$identificador_usu_buscado' AND comentarios.censurado=0
              ORDER BY comentarios.horacreacion DESC";
$result1010       = mysqli_query($link, $query1010);
$num_evaluaciones = mysqli_num_rows($result1010);

// MY_LANGS
$query_my_langs = "
SELECT my_l.*, l_names.Print_Name AS full_lang_name, l.lang_id AS lang_codigo2letras
FROM my_langs my_l
LEFT JOIN languages_names l_names ON my_l.lang_id=l_names.Id
LEFT JOIN languages1 l ON my_l.lang_id=l.Id
WHERE my_l.id='$identificador_usu_buscado'
ORDER BY my_l.level_id DESC";
$result_my_langs = mysqli_query($link, $query_my_langs);
$num_my_langs    = mysqli_num_rows($result_my_langs);

$my_langs_array = $my_langs_2letters_array = $my_langs_full_name_array = $my_langs_level_array = [];
$my_langs_forshare_array = $my_langs_price_array = $my_langs_typeofexchange_array = [];
$my_langs_priceorexchangetext_array = $my_langs_level_image_array = [];

for ($jjj = 0; $jjj < $num_my_langs; $jjj++) {
    $fila_my_langs = mysqli_fetch_array($result_my_langs);
    array_push($my_langs_full_name_array, $fila_my_langs['full_lang_name']);
    array_push($my_langs_array,           $fila_my_langs['lang_id']);
    array_push($my_langs_2letters_array,  $fila_my_langs['lang_codigo2letras']);
    array_push($my_langs_level_array,     intval($fila_my_langs['level_id']));
    array_push($my_langs_forshare_array,  $fila_my_langs['for_share']);
    array_push($my_langs_price_array,     $fila_my_langs['lang_price']);
}
$duplicate_langs   = array_count_values($my_langs_array);
$lista_idiomas_dup = array();
for ($jjj = 0; $jjj < $num_my_langs; $jjj++) {
    $lang1 = $my_langs_array[$jjj];
    if ($duplicate_langs["$lang1"] == 1) unset($duplicate_langs["$lang1"]);
}
$n_dups = count($duplicate_langs);
$lista_idiomas_dup = array_keys($duplicate_langs);
for ($iiii = 0; $iiii < $n_dups; $iiii++) {
    $nombre_idiomas_array = '';
    $lang2 = $lista_idiomas_dup[$iiii];
    $tmp   = array_count_values($my_langs_array);
    $cnt   = $tmp[$lang2];
    for ($jjjj = 0; $jjjj < $cnt; $jjjj++) {
        $key2 = array_search($lang2, $my_langs_array);
        if ($jjjj < $cnt - 1) { $nombre_idiomas_array .= "$my_langs_full_name_array[$key2] | "; $my_langs_array[$key2] = '_delete_'; }
        else                    { $nombre_idiomas_array .= "$my_langs_full_name_array[$key2]"; }
        $my_langs_full_name_array[$key2] = $nombre_idiomas_array;
    }
}
for ($iii = 0; $iii < count($my_langs_level_array); $iii++) {
    if ($my_langs_forshare_array[$iii] == 0) {
        $my_langs_typeofexchange_array[$iii]      = 'I know this language, but I do not want to exchange or teach it.';
        $my_langs_priceorexchangetext_array[$iii] = '';
    } elseif ($my_langs_price_array[$iii] == null) {
        $my_langs_typeofexchange_array[$iii]      = 'I know this language and I want to exchange it for another user\'s language (exchange free of cost).';
        $my_langs_priceorexchangetext_array[$iii] = 'EXCH.';
    } else {
        $my_langs_typeofexchange_array[$iii]      = 'I know this language and I want to teach it for money.';
        $my_langs_priceorexchangetext_array[$iii] = "$my_langs_price_array[$iii] &#8364;/h";
    }
}
$key3 = array_search('_delete_', $my_langs_array);
while ($key3 !== false) {
    unset($my_langs_array[$key3]); unset($my_langs_full_name_array[$key3]);
    unset($my_langs_level_array[$key3]); unset($my_langs_forshare_array[$key3]);
    unset($my_langs_price_array[$key3]); unset($my_langs_typeofexchange_array[$key3]);
    unset($my_langs_priceorexchangetext_array[$key3]); unset($my_langs_level_image_array[$key3]);
    unset($my_langs_2letters_array[$key3]);
    $key3 = array_search('_delete_', $my_langs_array);
}
$tmp1=$tmp2=$tmp3=$tmp4=$tmp5=$tmp6=$tmp7=$tmp8=$tmp9=[];
$n_lenguas = count($my_langs_array);
for ($i = 0; $i < $n_lenguas; $i++) {
    $tmp1[$i]=array_pop($my_langs_array); $tmp2[$i]=array_pop($my_langs_full_name_array);
    $tmp3[$i]=array_pop($my_langs_level_array); $tmp4[$i]=array_pop($my_langs_forshare_array);
    $tmp5[$i]=array_pop($my_langs_price_array); $tmp6[$i]=array_pop($my_langs_typeofexchange_array);
    $tmp7[$i]=array_pop($my_langs_priceorexchangetext_array);
    $tmp9[$i]=array_pop($my_langs_2letters_array);
}
$my_langs_array                     = array_reverse($tmp1);
$my_langs_full_name_array           = array_reverse($tmp2);
$my_langs_level_array               = array_reverse($tmp3);
$my_langs_forshare_array            = array_reverse($tmp4);
$my_langs_price_array               = array_reverse($tmp5);
$my_langs_typeofexchange_array      = array_reverse($tmp6);
$my_langs_priceorexchangetext_array = array_reverse($tmp7);
$my_langs_2letters_array            = array_reverse($tmp9);

// LEARN_LANGS
$query_learn_langs = "
SELECT learn_l.*, l_names.Print_Name AS full_lang_name, l.lang_id AS lang_codigo2letras
FROM learn_langs learn_l
LEFT JOIN languages_names l_names ON learn_l.lang_id=l_names.Id
LEFT JOIN languages1 l ON learn_l.lang_id=l.Id
WHERE learn_l.id='$identificador_usu_buscado'
ORDER BY learn_l.level_id DESC";
$result_learn_langs = mysqli_query($link, $query_learn_langs);
$num_learn_langs    = mysqli_num_rows($result_learn_langs);

$learn_langs_array = $learn_langs_2letters_array = $learn_langs_full_name_array = $learn_langs_level_array = [];
$learn_langs_forshare_array = $learn_langs_price_array = $learn_langs_typeofexchange_array = [];
$learn_langs_priceorexchangetext_array = $learn_langs_level_image_array = [];

for ($jjj = 0; $jjj < $num_learn_langs; $jjj++) {
    $fila_learn_langs = mysqli_fetch_array($result_learn_langs);
    array_push($learn_langs_full_name_array, $fila_learn_langs['full_lang_name']);
    array_push($learn_langs_array,           $fila_learn_langs['lang_id']);
    array_push($learn_langs_2letters_array,  $fila_learn_langs['lang_codigo2letras']);
    array_push($learn_langs_level_array,     intval($fila_learn_langs['level_id']));
    array_push($learn_langs_forshare_array,  $fila_learn_langs['for_share']);
    array_push($learn_langs_price_array,     $fila_learn_langs['lang_price']);
}
$duplicate_langs   = array_count_values($learn_langs_array);
$lista_idiomas_dup = array();
for ($jjj = 0; $jjj < $num_learn_langs; $jjj++) {
    $lang1 = $learn_langs_array[$jjj];
    if ($duplicate_langs["$lang1"] == 1) unset($duplicate_langs["$lang1"]);
}
$n_dups = count($duplicate_langs);
$lista_idiomas_dup = array_keys($duplicate_langs);
for ($iiii = 0; $iiii < $n_dups; $iiii++) {
    $nombre_idiomas_array = '';
    $lang2 = $lista_idiomas_dup[$iiii];
    $tmp   = array_count_values($learn_langs_array);
    $cnt   = $tmp[$lang2];
    for ($jjjj = 0; $jjjj < $cnt; $jjjj++) {
        $key2 = array_search($lang2, $learn_langs_array);
        if ($jjjj < $cnt - 1) { $nombre_idiomas_array .= "$learn_langs_full_name_array[$key2] | "; $learn_langs_array[$key2] = '_delete_'; }
        else                    { $nombre_idiomas_array .= "$learn_langs_full_name_array[$key2]"; }
        $learn_langs_full_name_array[$key2] = $nombre_idiomas_array;
    }
}
for ($iii = 0; $iii < count($learn_langs_level_array); $iii++) {
    if ($learn_langs_forshare_array[$iii] == 0) {
        $learn_langs_typeofexchange_array[$iii]      = 'I know this language, but I do not want to exchange or teach it.';
        $learn_langs_priceorexchangetext_array[$iii] = '';
    } elseif ($learn_langs_price_array[$iii] == null) {
        $learn_langs_typeofexchange_array[$iii]      = 'I know this language and I want to exchange it for another user\'s language (exchange free of cost).';
        $learn_langs_priceorexchangetext_array[$iii] = 'EXCH.';
    } else {
        $learn_langs_typeofexchange_array[$iii]      = 'I know this language and I want to teach it for money.';
        $learn_langs_priceorexchangetext_array[$iii] = "$learn_langs_price_array[$iii] &#8364;/h";
    }
}
$key3 = array_search('_delete_', $learn_langs_array);
while ($key3 !== false) {
    unset($learn_langs_array[$key3]); unset($learn_langs_full_name_array[$key3]);
    unset($learn_langs_level_array[$key3]); unset($learn_langs_forshare_array[$key3]);
    unset($learn_langs_price_array[$key3]); unset($learn_langs_typeofexchange_array[$key3]);
    unset($learn_langs_priceorexchangetext_array[$key3]); unset($learn_langs_level_image_array[$key3]);
    unset($learn_langs_2letters_array[$key3]);
    $key3 = array_search('_delete_', $learn_langs_array);
}
$tmp1=$tmp2=$tmp3=$tmp4=$tmp5=$tmp6=$tmp7=$tmp8=$tmp9=[];
$n_lenguas = count($learn_langs_array);
for ($i = 0; $i < $n_lenguas; $i++) {
    $tmp1[$i]=array_pop($learn_langs_array); $tmp2[$i]=array_pop($learn_langs_full_name_array);
    $tmp3[$i]=array_pop($learn_langs_level_array); $tmp4[$i]=array_pop($learn_langs_forshare_array);
    $tmp5[$i]=array_pop($learn_langs_price_array); $tmp6[$i]=array_pop($learn_langs_typeofexchange_array);
    $tmp7[$i]=array_pop($learn_langs_priceorexchangetext_array);
    $tmp9[$i]=array_pop($learn_langs_2letters_array);
}
$learn_langs_array                     = array_reverse($tmp1);
$learn_langs_full_name_array           = array_reverse($tmp2);
$learn_langs_level_array               = array_reverse($tmp3);
$learn_langs_forshare_array            = array_reverse($tmp4);
$learn_langs_price_array               = array_reverse($tmp5);
$learn_langs_typeofexchange_array      = array_reverse($tmp6);
$learn_langs_priceorexchangetext_array = array_reverse($tmp7);
$learn_langs_2letters_array            = array_reverse($tmp9);

function renderLevelDots($level) {
    $level = intval($level);
    if ($level <= 0) {
        $html = '<div class="level-dots">';
        for ($i = 1; $i <= 7; $i++) $html .= '<span class="dot" style="background-color:#e0e0e0;"></span>';
        return $html . '</div>';
    }
    $level = min(7, $level);
    $colors = [1=>'#E10016',2=>'#E10016',3=>'#F14400',4=>'#F14400',5=>'#FED700',6=>'#1B9E00',7=>'#1B9E00'];
    $html = '<div class="level-dots">';
    for ($i = 1; $i <= 7; $i++) {
        $color = ($i <= $level) ? $colors[$level] : '#e0e0e0';
        $html .= '<span class="dot" style="background-color:' . $color . ';"></span>';
    }
    return $html . '</div>';
}
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
    <title><?php echo htmlspecialchars($nombre_usuar); ?>'s Profile | Lingua2</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
    <link rel="stylesheet" href="./css/languages.css" media="all">
    <style>
        a { color: #e65f00; }
        body { background-color: #eeeeee !important; }
        .fas, .far, .fal, .fab, .fa { color: #e65f00 !important; }
        header { height: 55px !important; overflow: visible !important; }
        header .header-data { display: flex !important; align-items: center !important; width: 100%; }
        header .fas.fa-home { color: #ffffff !important; }
        .header-home-link { color:white;font-size:14px;text-decoration:none;display:flex;flex-direction:column;align-items:center;justify-content:center;flex-shrink:0;margin-left:auto;padding:0 6px;line-height:1; }
        .header-home-link i { margin-top:14px;color:#ffffff !important;font-size:15px;margin-bottom:-2px; }
        .header-home-link span { color:#ffffff;font-size:17px;font-weight:700;letter-spacing:1px;line-height:1; }
        .col-lg-6, .col-md-7 { padding-left:10px !important;padding-right:10px !important; }
        .posts-section, .post-bar { width:100% !important;overflow:visible !important; }
        .user-data, .post-bar, .suggestions, .widget { margin-bottom:20px !important;border-radius:12px !important;box-shadow:0 4px 12px rgba(0,0,0,0.08) !important;border-left:5px solid #e65f00 !important;background:white; }
        .username-dt { display:flex;justify-content:center;align-items:center;background:linear-gradient(135deg,#e65f00,#c04e00) !important;padding:25px 0 !important;border-radius:12px 12px 0 0 !important; }
        .usr-pic { width:150px !important;height:150px !important;border-radius:50% !important;overflow:hidden !important;border:4px solid white !important;box-shadow:0 4px 15px rgba(0,0,0,0.2) !important;margin:0 auto !important; }
        .usr-pic img { width:100% !important;height:100% !important;object-fit:cover !important;border-radius:50% !important; }
        .user-specs { padding:15px 12px !important; }
        .user-specs h3 { margin-bottom:5px;text-align:center;font-size:20px;font-weight:700; }
        .user-specs h6 { display:flex;align-items:center;gap:6px;margin:8px 0;font-weight:500;font-size:14px; }
        .job_descp h3, .sd-title h3 { font-size:18px !important;font-weight:600 !important;color:#2c3e50 !important;margin:5px 0 15px 0 !important;display:flex !important;align-items:center !important;gap:8px !important; }
        .job_descp h3 i { color:#e65f00 !important;font-size:20px !important; }
        .u-btn-wrap { display:flex;flex-direction:column;align-items:center;gap:10px;padding:14px 12px 8px;width:100%;box-sizing:border-box; }
        .u-action-btn { display:flex;align-items:center;justify-content:center;gap:8px;width:85%;padding:10px 16px;border-radius:8px;font-size:13px;font-weight:600;text-decoration:none;cursor:pointer;border:2px solid;transition:all 0.2s;text-align:center;background:none;box-sizing:border-box;font-family:inherit; }
        .u-action-btn.orange { color:#e65f00;border-color:#e65f00;background:#fff5ee; }
        .u-action-btn.orange:hover { background:#e65f00;color:white; }
        .u-action-btn.orange:hover i { color:white !important; }
        .u-action-btn.green  { color:#27ae60;border-color:#27ae60;background:#e8f5e9; }
        .u-action-btn.green:hover  { background:#27ae60;color:white; }
        .u-action-btn.green:hover i { color:white !important; }
        .u-action-btn.grey   { color:#7f8c8d;border-color:#bdc3c7;background:#f5f5f5; }
        .u-action-btn.grey.disabled { opacity:0.6;cursor:default;pointer-events:none; }
        .suggestions { margin-bottom:20px; }
        .suggestions .sd-title { padding:15px 15px 5px;border-bottom:1px solid #f0f0f0; }
        .suggestions .sd-title h3 { font-size:16px;margin:0; }
        .suggestions .suggestions-list { padding:0 15px 15px; }
        .suggestions .suggestion-usd { padding:8px 0;color:#7f8c8d;font-size:13px; }
        .suggestions .view-more { text-align:center;width:100%;margin-top:10px; }
        .suggestions .view-more a { color:#e65f00;text-decoration:none;font-size:12px;font-weight:700; }
        .suggestion-usd { display:flex;align-items:center;gap:12px;padding:8px 0; }
        .skill-tags { list-style:none !important;padding:0 !important;margin:15px 0 !important;display:grid !important;grid-template-columns:repeat(auto-fill,minmax(100px,1fr)) !important;gap:8px !important;width:100% !important;box-sizing:border-box !important; }
        .skill-tags li { position:relative !important;overflow:visible !important;background:#ffffff !important;border:1px solid #e8e8e8 !important;border-radius:12px !important;min-height:175px !important;box-shadow:0 2px 6px rgba(0,0,0,0.05) !important;transition:box-shadow 0.2s,transform 0.2s !important;box-sizing:border-box !important;display:flex !important;flex-direction:column !important;align-items:stretch !important;z-index:1 !important; }
        .skill-tags li:hover { transform:translateY(-2px) !important;box-shadow:0 6px 14px rgba(230,95,0,0.12) !important;border-color:#e65f00 !important;z-index:9999 !important; }
        .skill-tags li .card-inner-wrapper { width:100% !important;flex:1 !important;display:flex !important;flex-direction:column !important;align-items:center !important;text-decoration:none !important;color:inherit !important;padding:10px 6px 0 6px !important;box-sizing:border-box !important;border-radius:12px !important;overflow:visible !important;cursor:default !important; }
        .skill-tags li .card-inner-wrapper.is-sublang { cursor:pointer !important;opacity:0.80; }
        .skill-tags li .flag-wrapper { width:100% !important;height:38px !important;flex-shrink:0 !important;display:flex !important;align-items:center !important;justify-content:center !important; }
        .skill-tags li .flag-wrapper img { width:38px !important;height:26px !important;object-fit:cover !important;border-radius:4px !important;box-shadow:0 1px 4px rgba(0,0,0,0.12) !important; }
        .skill-tags li .flag-wrapper img.flag-placeholder { background:#e0e0e0 !important;object-fit:none !important;opacity:0.5 !important; }
        .skill-tags li .level-dots { display:flex !important;flex-direction:row !important;flex-wrap:nowrap !important;gap:3px !important;justify-content:center !important;align-items:center !important;height:22px !important;min-height:22px !important;flex-shrink:0 !important;width:100% !important;margin:4px 0 !important; }
        .skill-tags li .level-dots .dot { display:inline-block !important;width:6px !important;height:6px !important;border-radius:50% !important;flex-shrink:0 !important; }
        .skill-tags li .lang-text-container { width:100% !important;min-height:32px !important;display:flex !important;align-items:center !important;justify-content:center !important;text-align:center !important;overflow:visible !important;flex-shrink:0 !important;padding:0 2px !important; }
        .skill-tags li .lang-text-container strong { display:block !important;font-size:11px !important;font-weight:600 !important;color:#1e293b !important;line-height:1.3 !important;white-space:normal !important;word-break:break-word !important;text-align:center !important; }
        .skill-tags li .info-icon-container { position:relative !important;display:flex !important;justify-content:center !important;align-items:center !important;padding:5px 0 !important;overflow:visible !important;flex-shrink:0 !important;cursor:default !important; }
        .skill-tags li .info-icon-container .fa-info-circle { font-size:13px !important;color:#b2b2b2 !important;transition:color 0.2s !important; }
        .skill-tags li .info-icon-container:hover .fa-info-circle { color:#e65f00 !important; }
        .skill-tags li .tooltip-box { display:none;position:absolute !important;bottom:130% !important;left:50% !important;transform:translateX(-50%) !important;width:180px !important;white-space:normal !important;background:#2d3748 !important;color:#fff !important;font-size:11px !important;line-height:1.4 !important;padding:7px 9px !important;border-radius:6px !important;z-index:99999 !important;pointer-events:none !important;box-shadow:0 3px 10px rgba(0,0,0,0.22) !important;text-align:left !important; }
        .skill-tags li .tooltip-box::after { content:'';position:absolute;top:100%;left:50%;transform:translateX(-50%);border:5px solid transparent;border-top-color:#2d3748; }
        .skill-tags li .tooltip-box strong { display:block !important;color:#fff !important;font-size:11px !important;font-weight:700 !important;margin-bottom:3px !important;border-bottom:1px solid rgba(255,255,255,0.2) !important;padding-bottom:3px !important; }
        .skill-tags li .info-icon-container:hover .tooltip-box { display:block; }
        .skill-tags li .lang-price { margin-top:auto !important;width:100% !important;height:32px !important;border-top:1px solid #edf2f7 !important;display:flex !important;align-items:center !important;justify-content:center !important;font-size:11px !important;font-weight:600 !important;color:#64748b !important;background:#fafafa !important;border-radius:0 0 12px 12px !important;flex-shrink:0 !important;box-sizing:border-box !important; }
        .skill-tags li .lang-price-spacer { margin-top:auto !important;height:32px !important;flex-shrink:0 !important; }
        @media(max-width:1100px){ .skill-tags { grid-template-columns:repeat(auto-fill,minmax(90px,1fr)) !important; } }
        @media(max-width:480px) { .skill-tags { grid-template-columns:repeat(3,1fr) !important; } }
        .sublang-text { position:absolute;top:4px;left:4px;font-size:9px;font-weight:500;color:#888888;background:transparent;border:none;padding:0;z-index:5;line-height:1.4; }
        .evaluation-compact { text-align:center;display:flex;flex-direction:column;gap:4px; }
        .evaluation-compact .metric { display:flex;align-items:center;justify-content:center;gap:8px; }
        .evaluation-compact .metric i { font-size:24px;color:#e65f00; }
        .evaluation-compact .metric .percentage { font-size:32px;font-weight:700;color:#e65f00;line-height:1; }
        .evaluation-compact .metric .percentage small { font-size:18px;font-weight:400;color:#95a5a6;margin-left:2px; }
        .evaluation-compact .bar { width:100%;height:6px;background:#ecf0f1;border-radius:3px;overflow:hidden;margin:8px 0; }
        .evaluation-compact .bar-fill { height:100%;background:linear-gradient(90deg,#e65f00,#f39c12);border-radius:3px; }
        .evaluation-compact .total { font-size:13px;font-weight:600;color:#34495e;margin-top:5px; }
        .evaluation-compact .ev-link { display:flex;align-items:center;justify-content:center;gap:8px;margin-top:14px;font-size:14px;font-weight:700;color:#ffffff !important;text-decoration:none;border:2px solid #e65f00;border-radius:8px;padding:12px 20px;background:#e65f00;transition:background 0.2s ease,box-shadow 0.2s ease,transform 0.15s ease;box-shadow:0 3px 12px rgba(230,95,0,0.30);width:100%;box-sizing:border-box;cursor:pointer; }
        .evaluation-compact .ev-link i { color:#fff !important;transition:transform 0.2s ease; }
        .evaluation-compact .ev-link:hover { background:#c04e00;border-color:#c04e00;color:#ffffff !important;box-shadow:0 6px 20px rgba(230,95,0,0.45);transform:translateY(-1px); }
        .evaluation-compact .ev-link:hover i { transform:translateX(4px); }
        .evaluation-item { display:flex;gap:15px;padding:15px 0;border-bottom:1px solid #ecf0f1;position:relative; }
        .evaluation-item:last-child { border-bottom:none; }
        .evaluation-avatar { width:50px;height:50px;border-radius:50%;overflow:hidden;flex-shrink:0; }
        .evaluation-avatar img { width:100%;height:100%;object-fit:cover; }
        .evaluation-content { flex:1; }
        .evaluation-header { display:flex;align-items:center;gap:10px;margin-bottom:5px;flex-wrap:wrap; }
        .evaluation-author { font-weight:600;color:#2c3e50;font-size:14px; }
        .evaluation-date { font-size:12px;color:#95a5a6;display:flex;align-items:center;gap:3px; }
        .evaluation-rating { font-size:11px;font-weight:600;padding:2px 8px;border-radius:12px;text-transform:uppercase; }
        .rating-positive { background:#e8f5e9;color:#27ae60;border:1px solid #27ae60; }
        .rating-neutral  { background:#f5f5f5;color:#7f8c8d;border:1px solid #95a5a6; }
        .rating-negative { background:#fdeded;color:#e74c3c;border:1px solid #e74c3c; }
        .rating-noanswer { background:#fef5e7;color:#f39c12;border:1px solid #f39c12; }
        .evaluation-text { font-size:13px;color:#34495e;line-height:1.5;margin-top:5px;word-break:break-word; }
        .ev-opts { position:relative;display:flex;align-items:flex-start;flex-shrink:0;margin-left:8px; }
        .ev-opts-open { display:flex !important;align-items:center !important;justify-content:center !important;width:28px !important;height:28px !important;border-radius:50% !important;color:#aaa !important;font-size:18px !important;text-decoration:none !important;transition:background 0.15s,color 0.15s !important;line-height:1 !important; }
        .ev-opts-open:hover { background:#f0f0f0 !important;color:#555 !important; }
        .ev-options { display:none;position:absolute;top:30px;right:0;background:#fff;border:1px solid #e0e0e0;border-radius:8px;box-shadow:0 4px 16px rgba(0,0,0,0.13);min-width:170px;z-index:9999;list-style:none !important;padding:4px 0 !important;margin:0 !important; }
        .ev-options.ev-open { display:block !important; }
        .ev-options li { padding:0 !important;margin:0 !important; }
        .ev-options li a { display:block !important;padding:9px 16px !important;font-size:13px !important;color:#333 !important;text-decoration:none !important;transition:background 0.12s !important; }
        .ev-options li a:hover { background:#fff5ee !important;color:#e65f00 !important; }
        .epi-sec { margin:20px 0; }
        .post-bar { padding:25px; }
        .right-sidebar .widget { margin-bottom:25px; }
        .right-sidebar .sd-title { padding:15px 20px 10px;border-bottom:1px solid #eaeef2; }
        .right-sidebar .sd-title h3 { font-size:16px;font-weight:700;margin:0;display:flex;align-items:center;gap:8px; }
        .right-sidebar .jobs-list { padding:15px 20px; }

        /* ── MODAL CSS PROPIO ── */
        #modalBackdrop {
            display:none; position:fixed; top:0; left:0; width:100%; height:100%;
            background:rgba(0,0,0,0.5); z-index:99998;
            opacity:0; transition:opacity 0.3s ease;
        }
        #modalBackdrop.show { opacity:1; }
        #modalContainer {
            display:none; position:fixed; top:0; left:0; width:100%; height:100%;
            z-index:99999; overflow-y:auto; padding:30px 15px; box-sizing:border-box;
        }
        #modalBox {
            background:white; border-radius:6px; width:100%; max-width:600px;
            margin:40px auto 0 auto; box-shadow:0 5px 30px rgba(0,0,0,0.3);
            opacity:0; transform:translateY(-40px);
            transition:opacity 0.3s ease,transform 0.3s ease;
        }
        #modalBox.show { opacity:1; transform:translateY(0); }
        #modalBox .mhdr { padding:15px 20px; border-bottom:1px solid #e5e5e5; display:flex; justify-content:space-between; align-items:center; }
        #modalBox .mhdr h4 { margin:0; font-size:17px; font-weight:600; color:#333; }
        #modalBox .mhdr .mcls { background:none; border:none; font-size:26px; line-height:1; cursor:pointer; color:#999; padding:0 4px; }
        #modalBox .mhdr .mcls:hover { color:#333; }
        #modalBox .mbdy { padding:20px; position:relative; }
        #modalBox .mbdy textarea { width:100%; box-sizing:border-box; border:1px solid #ccc; border-radius:4px; padding:10px; font-size:14px; font-family:inherit; resize:vertical; min-height:120px; transition:border-color 0.2s,box-shadow 0.2s; }
        #modalBox .mbdy textarea:focus { outline:none; border-color:#e65f00; box-shadow:0 0 0 3px rgba(230,95,0,0.15); }
        #charCount { position:absolute; bottom:28px; right:28px; color:#aaa; font-size:12px; margin:0; }
        #modalBox .mftr { padding:12px 20px; border-top:1px solid #e5e5e5; display:flex; justify-content:flex-end; gap:10px; }
        #modalBox .mftr .mcancel { padding:8px 16px; border:1px solid #ccc; border-radius:4px; background:#fff; cursor:pointer; font-size:14px; color:#333; transition:background 0.2s; }
        #modalBox .mftr .mcancel:hover { background:#f5f5f5; }
        #sendMessageBtn { padding:8px 16px; border:none; border-radius:4px; background:rgb(141,119,103); color:white; cursor:pointer; font-size:14px; transition:background 0.2s; }
        #sendMessageBtn:not([disabled]):hover { background:#e65f00; }
        #sendMessageBtn[disabled] { opacity:0.6; cursor:default; }
    </style>
</head>

<body>
    <div class="wrapper">

        <!-- HEADER -->
        <header>
            <div class="container">
                <div class="header-data">
                    <div class="logo">
                        <a href="./me.php" title="Home"><img src="../public/images/logo-blanco.png" alt="Lingua2"></a>
                    </div>
                    <div style="flex:1 1 auto;"></div>
                    <a href="./me.php" class="header-home-link" title="Home">
                        <i class="fas fa-home"></i>
                        <span>Home</span>
                    </a>
                </div>
            </div>
        </header>

        <main>
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
                                                    <img src="<?php echo $foto_nombre; ?>?nocache=<?php echo time(); ?>" alt="<?php echo htmlspecialchars($nombre_usuar); ?>">
                                                </div>
                                            </div>
                                            <div class="user-specs">
                                                <h3><?php echo htmlspecialchars($nombre_usuar); ?></h3>
                                                <span style="color:#686868;font-size:85%;display:block;text-align:center;margin-bottom:6px;">#<?php echo $identificador_usu_buscado; ?></span>
                                                <?php if (!($gpslat11 == 0 && $gpslng11 == 0)): ?>
                                                    <h6><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($ciudad1); ?> area</h6>
                                                <?php endif; ?>
                                                <?php if (!empty($zonahoraria)): ?>
                                                    <h6><i class="fas fa-clock"></i> <?php echo "$horaFormateada22 ($zonahoraria)"; ?></h6>
                                                <?php endif; ?>
                                                <?php if (!($gpslat11 == 0 && $gpslng11 == 0) && !($mi_gpslat == 0 && $mi_gpslng == 0)): ?>
                                                    <h6 style="font-size:90%;"><i class="fas fa-route"></i> <?php echo $distancia_entre_partners; ?> km away from you</h6>
                                                <?php endif; ?>
                                                <?php if ($org_name_display): ?>
                                                    <h6><i class="fas fa-building"></i> <?php echo htmlspecialchars($org_name_display); ?></h6>
                                                <?php endif; ?>
                                            </div>
                                            <div class="u-btn-wrap">
                                                <?php if (!$ya_en_bookmarks): ?>
                                                    <a href="#" class="u-action-btn grey" id="btn-bookmark"
                                                        data-url="../bookmarks/addbookmark.php?idfav=<?php echo $identificador_contactado; ?>">
                                                        <i class="fas fa-bookmark"></i> Add to bookmarks
                                                    </a>
                                                <?php else: ?>
                                                    <span class="u-action-btn grey disabled">
                                                        <i class="fas fa-bookmark"></i> Already bookmarked
                                                    </span>
                                                <?php endif; ?>
                                                <?php if ($ya_couple): ?>
                                                    <form action="../trackerproposals/sent-studentcreateproposal.php" style="width:85%;display:flex;justify-content:center;">
                                                        <input type="hidden" name="tid" value="<?php echo $identificador_contactado; ?>">
                                                        <button type="submit" class="u-action-btn green" style="width:100%;">
                                                            <i class="fas fa-calendar-check"></i> Request a meeting
                                                        </button>
                                                    </form>
                                                <?php else: ?>
                                                    <button class="u-action-btn orange" onclick="abrirModal()">
                                                        <i class="fas fa-envelope"></i> Write a message
                                                    </button>
                                                <?php endif; ?>
                                            </div>
                                            <div style="height:10px;"></div>
                                        </div>
                                    </div>

                                    <div class="suggestions full-width">
                                        <div class="sd-title"><h3><i class="fas fa-lightbulb"></i> Title 3</h3></div>
                                        <div class="suggestions-list">
                                            <div class="suggestion-usd">Placeholder 3</div>
                                            <div class="view-more"><a href="./partners.php">Link 3</a></div>
                                        </div>
                                    </div>
                                    <div class="suggestions full-width">
                                        <div class="sd-title"><h3><i class="fas fa-lightbulb"></i> Title 4</h3></div>
                                        <div class="suggestions-list">
                                            <div class="suggestion-usd">Placeholder 4</div>
                                            <div class="view-more"><a href="./partners.php">Link 4</a></div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- CENTRAL COLUMN -->
                            <div class="col-lg-6 col-md-7 no-pd">
                                <div class="posts-section">
                                    <div id="seccion_teach" class="post-bar">
                                        <div class="epi-sec"></div>

                                        <!-- I KNOW THESE LANGUAGES -->
                                        <div class="job_descp">
                                            <h3><i class="fas fa-graduation-cap"></i> <?php echo htmlspecialchars($nombre_usuar); ?> knows these languages</h3>
                                            <ul class="skill-tags">
                                                <?php for ($iii = 0; $iii < count($my_langs_array); $iii++):
                                                    $idof             = $my_langs_array[$iii];
                                                    $idof_2letras     = $my_langs_2letters_array[$iii];
                                                    $level_idiomaofre = $my_langs_level_array[$iii];
                                                    $full_name        = $my_langs_full_name_array[$iii];
                                                    $price_text       = $my_langs_priceorexchangetext_array[$iii] ?? '';
                                                    $tooltip_ofr      = $my_langs_typeofexchange_array[$iii] ?? '';
                                                    $lang_display_text = strtolower($idof) . ' (' . ($idiomas_nivel[$level_idiomaofre] ?? '*') . ')';
                                                    $query_macro = "SELECT M_Id FROM languages_macrolanguages WHERE I_Id='$idof'";
                                                    $result_macro = mysqli_query($link, $query_macro);
                                                    $fila_macro = mysqli_fetch_array($result_macro);
                                                    $macrolang = $fila_macro ? $fila_macro['M_Id'] : '';
                                                    $is_sublanguage = !empty($macrolang);
                                                    $ruta_bandera = './images/banderasseparadas2024/placeholder.png';
                                                    $codigo2 = trim($idof_2letras);
                                                    $codigo3 = strtolower(trim($idof));
                                                    if (!$is_sublanguage && preg_match('/^[a-z]{2}$/', $codigo2)) {
                                                        if (strtolower($codigo2) === 'no' && !in_array($codigo3, ['nor','nno','nob'])) {
                                                            $ruta_bandera = './images/banderasseparadas2024/placeholder.png';
                                                        } else {
                                                            $ruta_bandera = './images/banderasseparadas2024/' . strtolower($codigo2) . '.png';
                                                        }
                                                    }
                                                    $flag_is_placeholder = ($ruta_bandera === './images/banderasseparadas2024/placeholder.png');
                                                    if ($is_sublanguage) {
                                                        $alert_msg = addslashes("'$idof' is a sublanguage of '$macrolang'. To modify languages, go to your own profile settings.");
                                                        $card_onclick = "onclick=\"alert('$alert_msg'); return false;\"";
                                                        $card_extra_class = 'is-sublang';
                                                    } else { $card_onclick = ''; $card_extra_class = ''; }
                                                ?>
                                                    <li>
                                                        <?php if ($is_sublanguage): ?><span class="sublang-text" title="Sublanguage of <?php echo htmlspecialchars($macrolang); ?>">sub</span><?php endif; ?>
                                                        <div class="card-inner-wrapper <?php echo $card_extra_class; ?>" <?php echo $card_onclick; ?>>
                                                            <div class="flag-wrapper">
                                                                <img src="<?php echo htmlspecialchars($ruta_bandera, ENT_QUOTES); ?>"
                                                                     alt="<?php echo htmlspecialchars($idof, ENT_QUOTES); ?>"
                                                                     onerror="this.onerror=null; this.src='./images/banderasseparadas2024/placeholder.png';"
                                                                     <?php if ($flag_is_placeholder) echo 'class="flag-placeholder"'; ?>>
                                                            </div>
                                                            <?php echo renderLevelDots($level_idiomaofre); ?>
                                                            <div class="lang-text-container"><strong><?php echo htmlspecialchars($lang_display_text, ENT_QUOTES); ?></strong></div>
                                                            <div class="info-icon-container">
                                                                <i class="fas fa-info-circle"></i>
                                                                <span class="tooltip-box">
                                                                    <strong><?php echo htmlspecialchars($full_name, ENT_QUOTES); ?>:</strong>
                                                                    <?php if ($is_sublanguage) echo 'Sublanguage of ' . htmlspecialchars($macrolang, ENT_QUOTES) . '. Click the parent flag to edit.'; else echo htmlspecialchars($tooltip_ofr, ENT_QUOTES); ?>
                                                                </span>
                                                            </div>
                                                            <?php if (!empty($price_text)): ?><div class="lang-price"><?php echo $price_text; ?></div><?php else: ?><div class="lang-price-spacer"></div><?php endif; ?>
                                                        </div>
                                                    </li>
                                                <?php endfor; ?>
                                            </ul>
                                        </div>

                                        <div class="epi-sec"></div>

                                        <!-- I WANT TO LEARN -->
                                        <div class="job_descp">
                                            <h3><i class="fas fa-book-open"></i> <?php echo htmlspecialchars($nombre_usuar); ?> wants to learn</h3>
                                            <ul class="skill-tags">
                                                <?php for ($iii = 0; $iii < count($learn_langs_array); $iii++):
                                                    $idof             = $learn_langs_array[$iii];
                                                    $idof_2letras     = $learn_langs_2letters_array[$iii];
                                                    $level_idiomaofre = $learn_langs_level_array[$iii];
                                                    $full_name        = $learn_langs_full_name_array[$iii];
                                                    $nivel_label      = $idiomas_nivel[$level_idiomaofre] ?? '—';
                                                    $lang_display_text = strtolower($idof) . ' (' . $nivel_label . ')';
                                                    $query_macro2 = "SELECT M_Id FROM languages_macrolanguages WHERE I_Id='$idof'";
                                                    $result_macro2 = mysqli_query($link, $query_macro2);
                                                    $fila_macro2 = mysqli_fetch_array($result_macro2);
                                                    $macrolang = $fila_macro2 ? $fila_macro2['M_Id'] : '';
                                                    $is_sublanguage = !empty($macrolang);
                                                    $ruta_bandera = './images/banderasseparadas2024/placeholder.png';
                                                    $codigo2 = trim($idof_2letras);
                                                    $codigo3 = strtolower(trim($idof));
                                                    if (!$is_sublanguage && preg_match('/^[a-z]{2}$/', $codigo2)) {
                                                        if (strtolower($codigo2) === 'no' && !in_array($codigo3, ['nor','nno','nob'])) {
                                                            $ruta_bandera = './images/banderasseparadas2024/placeholder.png';
                                                        } else {
                                                            $ruta_bandera = './images/banderasseparadas2024/' . strtolower($codigo2) . '.png';
                                                        }
                                                    }
                                                    $flag_is_placeholder = ($ruta_bandera === './images/banderasseparadas2024/placeholder.png');
                                                    if ($is_sublanguage) {
                                                        $alert_msg = addslashes("'$idof' is a sublanguage of '$macrolang'. To modify languages, go to your own profile settings.");
                                                        $card_onclick = "onclick=\"alert('$alert_msg'); return false;\"";
                                                        $card_extra_class = 'is-sublang';
                                                    } else { $card_onclick = ''; $card_extra_class = ''; }
                                                ?>
                                                    <li>
                                                        <?php if ($is_sublanguage): ?><span class="sublang-text" title="Sublanguage of <?php echo htmlspecialchars($macrolang); ?>">sub</span><?php endif; ?>
                                                        <div class="card-inner-wrapper <?php echo $card_extra_class; ?>" <?php echo $card_onclick; ?>>
                                                            <div class="flag-wrapper">
                                                                <img src="<?php echo htmlspecialchars($ruta_bandera, ENT_QUOTES); ?>"
                                                                     alt="<?php echo htmlspecialchars($idof, ENT_QUOTES); ?> flag"
                                                                     onerror="this.onerror=null; this.src='./images/banderasseparadas2024/placeholder.png';"
                                                                     <?php if ($flag_is_placeholder) echo 'class="flag-placeholder"'; ?>>
                                                            </div>
                                                            <?php echo renderLevelDots($level_idiomaofre); ?>
                                                            <div class="lang-text-container"><strong><?php echo htmlspecialchars($lang_display_text, ENT_QUOTES); ?></strong></div>
                                                            <div class="info-icon-container" style="margin-top:auto;">
                                                                <i class="fas fa-info-circle"></i>
                                                                <span class="tooltip-box">
                                                                    <strong><?php echo htmlspecialchars($full_name, ENT_QUOTES); ?>:</strong>
                                                                    <?php if ($is_sublanguage) echo 'Sublanguage of ' . htmlspecialchars($macrolang, ENT_QUOTES) . '. Click the parent flag to edit.'; else echo htmlspecialchars($nivel_label, ENT_QUOTES); ?>
                                                                </span>
                                                            </div>
                                                            <div class="lang-price-spacer"></div>
                                                        </div>
                                                    </li>
                                                <?php endfor; ?>
                                            </ul>
                                        </div>

                                        <div class="epi-sec"></div>

                                        <!-- MORE INFORMATION -->
                                        <div class="job_descp">
                                            <h3><i class="fas fa-info-circle"></i> More information</h3>
                                            <?php if (!empty($availability100)): ?>
                                                <p style="margin-bottom:12px;"><i class="fas fa-clock"></i> <?php echo nl2br(htmlspecialchars($availability100)); ?></p>
                                            <?php endif; ?>
                                            <?php if (!empty($othercomments100)): ?>
                                                <p><i class="fas fa-comment"></i> <?php echo nl2br(htmlspecialchars($othercomments100)); ?></p>
                                            <?php endif; ?>
                                            <?php if (empty($availability100) && empty($othercomments100)): ?>
                                                <p style="color:#7f8c8d;font-style:italic;">No additional information provided.</p>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>

                                <!-- EVALUACIONES -->
                                <div class="suggestions full-width" style="margin-top:20px;">
                                    <div class="sd-title"><h3><i class="fas fa-star"></i> Evaluations</h3></div>
                                    <div class="suggestions-list">
                                        <?php if ($n_comentarios > 0): ?>
                                            <div class="evaluation-compact" style="margin-bottom:20px;padding-bottom:15px;border-bottom:1px solid #ecf0f1;">
                                                <div class="metric">
                                                    <i class="fas fa-star"></i>
                                                    <span class="percentage"><?php echo $porcentaje_positivos; ?><small>%</small></span>
                                                </div>
                                                <div class="bar"><div class="bar-fill" style="width:<?php echo $porcentaje_positivos; ?>%;"></div></div>
                                                <div class="total">Based on <?php echo $n_comentarios; ?> review<?php echo $n_comentarios > 1 ? 's' : ''; ?></div>
                                                <a href="../infouser/evdonepartners.php?u=<?php echo $identificador_usu_buscado; ?>" class="ev-link">
                                                    <i class="fas fa-list"></i>
                                                    View all <?php echo $n_comentarios; ?> evaluation<?php echo $n_comentarios > 1 ? 's' : ''; ?>
                                                    (<?php echo $porcentaje_positivos; ?>% positive)
                                                </a>
                                            </div>
                                            <?php
                                            $num_ev_bucle = min($num_max_ev_mostradas, $num_evaluaciones);
                                            for ($jjj = 0; $jjj < $num_ev_bucle; $jjj++):
                                                $fila1010      = mysqli_fetch_array($result1010);
                                                $comentario_ev = $fila1010['comment'];
                                                $hora_ev       = $fila1010['hora'];
                                                $rating_ev     = $fila1010['rating'];
                                                if ($rating_ev == 1)     { $rt = 'POSITIVE';  $rc = 'rating-positive'; }
                                                elseif ($rating_ev == 2) { $rt = 'NEUTRAL';   $rc = 'rating-neutral'; }
                                                elseif ($rating_ev == 3) { $rt = 'NEGATIVE';  $rc = 'rating-negative'; }
                                                elseif ($rating_ev == 4) { $rt = 'NO ANSWER'; $rc = 'rating-noanswer'; }
                                                else                     { $rt = $rating_ev;  $rc = 'rating-neutral'; }
                                                $autor_ev = $fila1010['nombre1'];
                                                if (!is_null($autor_ev)) { $p = explode(' ', $autor_ev); $autor_ev = ucfirst($p[0]); }
                                                if ($autor_ev == '') $autor_ev = 'User unregistered';
                                                $orden47        = $fila1010['orden1'];
                                                $foto_extension = $fila1010['fotoext1'];
                                                $foto_autor     = "../uploader/upload_pic/thumb_$orden47.$foto_extension";
                                                if (!file_exists($foto_autor)) $foto_autor = "../uploader/default.jpg";
                                            ?>
                                                <div class="evaluation-item">
                                                    <div class="evaluation-avatar"><img src="<?php echo $foto_autor; ?>" alt="<?php echo htmlspecialchars($autor_ev); ?>"></div>
                                                    <div class="evaluation-content">
                                                        <div class="evaluation-header">
                                                            <span class="evaluation-author">
                                                                <?php if ($orden47): ?>
                                                                    <a href="./u.php?identificador=<?php echo $orden47; ?>" style="color:#2c3e50;text-decoration:none;"><?php echo htmlspecialchars($autor_ev); ?></a>
                                                                <?php else: echo htmlspecialchars($autor_ev); endif; ?>
                                                            </span>
                                                            <span class="evaluation-date"><i class="far fa-calendar-alt"></i> <?php echo date('Y-m-d', strtotime($hora_ev)); ?></span>
                                                            <span class="evaluation-rating <?php echo $rc; ?>"><?php echo $rt; ?></span>
                                                        </div>
                                                        <div class="evaluation-text"><?php echo nl2br(htmlspecialchars($comentario_ev)); ?></div>
                                                    </div>
                                                    <div class="ev-opts">
                                                        <a href="#" class="ev-opts-open" onclick="evOptsToggle(event, this);" title="Options"><i class="la la-ellipsis-v"></i></a>
                                                        <ul class="ev-options">
                                                            <?php if ($orden47): ?><li><a href="./u.php?identificador=<?php echo $orden47; ?>">Visit profile</a></li><?php endif; ?>
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
                                    <div id="my_events" class="widget">
                                        <div class="sd-title"><h3><i class="fas fa-lightbulb"></i> Title 1</h3></div>
                                        <div class="jobs-list"><p style="color:#7f8c8d;font-size:13px;">Placeholder 1</p></div>
                                    </div>
                                    <div id="events" class="widget">
                                        <div class="sd-title"><h3><i class="fas fa-lightbulb"></i> Title 2</h3></div>
                                        <div class="jobs-list"><p style="color:#7f8c8d;font-size:13px;">Placeholder 2</p></div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- MODAL: Write a message (CSS propio, sin depender de Bootstrap modal) -->
    <div id="modalBackdrop" onclick="cerrarModal()"></div>
    <div id="modalContainer">
        <div id="modalBox">
            <div class="mhdr">
                <h4>Friendship request and Direct message to user</h4>
                <button class="mcls" onclick="cerrarModal()">&times;</button>
            </div>
            <form name="mensaje_usu" enctype="multipart/form-data"
                  action="./sndmsg.php?id_receptor=<?php echo $id_del_receptor; ?>"
                  method="POST" id="formMensaje">
                <div class="mbdy">
                    <textarea name="mensajedelusuario" maxlength="255" id="textareaID"
                        placeholder="Write your message here..."
                        oninput="checkInput()"></textarea>
                    <p id="charCount">0/255</p>
                </div>
                <div class="mftr">
                    <button type="button" class="mcancel" onclick="cerrarModal()">Close</button>
                    <input type="submit" name="enviar" id="sendMessageBtn" value="Send message" disabled />
                </div>
            </form>
        </div>
    </div>

    <script>
        function abrirModal() {
            var bd  = document.getElementById('modalBackdrop');
            var cnt = document.getElementById('modalContainer');
            var box = document.getElementById('modalBox');
            bd.style.display  = 'block';
            cnt.style.display = 'block';
            document.body.style.overflow = 'hidden';
            bd.offsetHeight; box.offsetHeight;
            bd.classList.add('show');
            box.classList.add('show');
        }
        function cerrarModal() {
            var bd  = document.getElementById('modalBackdrop');
            var cnt = document.getElementById('modalContainer');
            var box = document.getElementById('modalBox');
            bd.classList.remove('show');
            box.classList.remove('show');
            setTimeout(function() {
                bd.style.display  = 'none';
                cnt.style.display = 'none';
                document.body.style.overflow = '';
            }, 300);
        }
        document.addEventListener('keydown', function(e) { if (e.key === 'Escape') cerrarModal(); });

        function evOptsToggle(e, btn) {
            e.preventDefault(); e.stopPropagation();
            var menu = btn.nextElementSibling, was = menu.classList.contains('ev-open');
            document.querySelectorAll('.ev-options.ev-open').forEach(function(m) { m.classList.remove('ev-open'); });
            if (!was) menu.classList.add('ev-open');
        }
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.ev-opts'))
                document.querySelectorAll('.ev-options.ev-open').forEach(function(m) { m.classList.remove('ev-open'); });
        });

        var bBtn = document.getElementById('btn-bookmark');
        if (bBtn) {
            bBtn.addEventListener('click', function(e) {
                e.preventDefault();
                var url = this.getAttribute('data-url');
                $.ajax({
                    url: url, method: 'GET',
                    success: function() {
                        var popup = $('<div></div>').text('The bookmark has been added successfully!').css({
                            'position':'fixed','top':'50%','left':'50%','transform':'translate(-50%,-50%)',
                            'background-color':'green','color':'white','padding':'15px','border-radius':'5px',
                            'box-shadow':'0 0 10px rgba(0,0,0,.2)','font-size':'14px','z-index':'100000'
                        }).hide();
                        $('body').append(popup);
                        popup.fadeIn(300).delay(2000).fadeOut(300);
                        setTimeout(function() { location.reload(); }, 3300);
                    },
                    error: function() { alert('There was an error adding the bookmark.'); }
                });
            });
        }

        function checkInput() {
            var ta  = document.getElementById('textareaID'),
                btn = document.getElementById('sendMessageBtn'),
                cc  = document.getElementById('charCount');
            if (!ta) return;
            if (ta.value.length > 255) ta.value = ta.value.substring(0, 255);
            var l = ta.value.length;
            if (cc)  cc.textContent = l + '/255';
            if (btn) {
                btn.disabled = (ta.value.trim() === '');
                btn.style.background = ta.value.trim() ? '#e65f00' : 'rgb(141,119,103)';
            }
        }

        $(document).ready(function() {
            var c1 = $('#seccion_teach').clone().attr('id', 'seccion_teach_clone');
            $('#column1').append(c1);
            var c2 = $('#events').clone().attr('id', 'events_clone');
            $('#column2').append(c2);
            var c3 = $('#my_events').clone().attr('id', 'my_events_clone');
            $('#column3').append(c3);
            resize_movil();
            window.addEventListener('resize', resize_movil);
        });
        function resize_movil() {
            $('#seccion_teach_clone').css('margin-bottom', '0px');
            $('#events_clone').css('margin-bottom', '0px');
            if (screen.width < 768) {
                $('#seccion_teach').attr('hidden', true);   $('#seccion_teach_clone').attr('hidden', false);
                $('#events').attr('hidden', true);          $('#events_clone').attr('hidden', false);
                $('#my_events').attr('hidden', true);       $('#my_events_clone').attr('hidden', false);
            } else {
                $('#seccion_teach').attr('hidden', false);  $('#seccion_teach_clone').attr('hidden', true);
                $('#events').attr('hidden', false);         $('#events_clone').attr('hidden', true);
                $('#my_events').attr('hidden', false);      $('#my_events_clone').attr('hidden', true);
            }
        }
    </script>

    <?php require('../templates/footer.php'); ?>
</body>
</html>