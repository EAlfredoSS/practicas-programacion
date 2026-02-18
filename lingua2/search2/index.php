<?php
session_start();
require('../templates/header_simplified.html');
require('../funcionesphp/funciones_idiomas_usuario_vs_original.php');
require('../files/bd.php');

// Robust session check with fallbacks
$identificador2017 = 0;
if (isset($_SESSION['orden2017']) && is_numeric($_SESSION['orden2017'])) {
    $identificador2017 = (int) $_SESSION['orden2017'];
} elseif (isset($_SESSION['userid1']) && is_numeric($_SESSION['userid1'])) {
    $identificador2017 = (int) $_SESSION['userid1'];
    $_SESSION['orden2017'] = $identificador2017;
} elseif (isset($_COOKIE['orden2017']) && is_numeric($_COOKIE['orden2017'])) {
    $identificador2017 = (int) $_COOKIE['orden2017'];
    $_SESSION['orden2017'] = $identificador2017;
}

if ($identificador2017 <= 0) {
    header('Location: /index_login.php');
    exit();
}

$_SESSION['idusuario2019'] = $identificador2017;

$query_user1 = "SELECT * FROM mentor2009 WHERE orden = $identificador2017";
$result_user1 = mysqli_query($link, $query_user1);

if (!mysqli_num_rows($result_user1)) {
    die("<br/>.........No user......<br/>");
}

$fila_user1 = mysqli_fetch_array($result_user1);
$latitud1 = $fila_user1['Gpslat'];
$longitud1 = $fila_user1['Gpslng'];

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

$number_of_affected_users = 0;
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


$form_submitted = isset($_GET['learns']) || isset($_GET['teaches']);

if ($form_submitted) {
    $userislearning = $_GET['learns'];
    $useristeaching = $_GET['teaches'];
    $minimumlevel_userislearning = $_GET['min_level'];
    $maximumlevel_userislearning = $_GET['max_level'];
    $minimumlevel_useristeaching = $_GET['min_level'];
    $maximumlevel_useristeaching = $_GET['max_level'];



    $organizationslist = isset($_GET['orgs']) && is_array($_GET['orgs']) ? $_GET['orgs'] : [];
    $ismale = isset($_GET['male']) ? $_GET['male'] : '';
    $isfemale = isset($_GET['female']) ? $_GET['female'] : '';


    $sexo_query = '';
    if ($ismale == 'on') $sexo_query = "Sexo='M'";
    if ($isfemale == 'on') $sexo_query = "Sexo='F'";
    if ($ismale == 'on' && $isfemale == 'on') $sexo_query = "Sexo='M' OR Sexo='F'";

    $where_clause = "m.Pais<>'teacher' ";
    if(!empty($sexo_query)){
        $where_clause .= " AND ($sexo_query)";
    }

    $where_orgs = '';
    $n_orgs = count($organizationslist);
    if ($n_orgs > 0) {
        $where_orgs = "AND (";
        for ($jjjj = 0; $jjjj < $n_orgs; $jjjj++) {
            $organizacion = $organizationslist[$jjjj];
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

    $distancias_permitidas = [1, 5, 10, 20, 50];
    $radio = isset($_GET['distance']) ? (int)$_GET['distance'] : 5;

    if ($radio == 0 || !in_array($radio, $distancias_permitidas)) {
        $filtro_distancia = "";
    } else {
        $filtro_distancia = "HAVING distanciaPunto1Punto2 < $radio";
    }
    
    if (!empty($useristeaching)) {
        $where_clause .= " AND EXISTS (
            SELECT 1 FROM my_langs 
            WHERE id = m.orden 
            AND lang_id = '$useristeaching'
            AND level_id BETWEEN $minimumlevel_useristeaching AND $maximumlevel_useristeaching
        )";
    }

    if (!empty($userislearning)) {
        $where_clause .= " AND EXISTS (
            SELECT 1 FROM learn_langs 
            WHERE id = m.orden 
            AND lang_id = '$userislearning'
            AND level_id BETWEEN $minimumlevel_userislearning AND $maximumlevel_userislearning
        )";
    }

    $query = "SELECT m.*,(ACOS(SIN(RADIANS(m.Gpslat)) * SIN(RADIANS($latitud1)) + COS(RADIANS(m.Gpslat)) * COS(RADIANS($latitud1)) * COS(RADIANS(m.Gpslng) - RADIANS($longitud1))) * 6378) AS distanciaPunto1Punto2 FROM mentor2009 m WHERE $where_clause $filtro_distancia ORDER BY distanciaPunto1Punto2 ASC";

} else {
    $barcelona_lat = 41.4089827;
    $barcelona_lng = 2.185913;

    $query = "SELECT m.*,(ACOS(SIN(RADIANS(m.Gpslat)) * SIN(RADIANS($barcelona_lat)) + COS(RADIANS(m.Gpslat)) * COS(RADIANS($barcelona_lat)) * COS(RADIANS(m.Gpslng) - RADIANS($barcelona_lng))) * 6378) AS distanciaPunto1Punto2 FROM mentor2009 m WHERE m.Pais<>'teacher' AND (m.Sexo='M' OR m.Sexo='F') HAVING distanciaPunto1Punto2 < 150 ORDER BY distanciaPunto1Punto2 ASC";
}

echo "$query: $query";

$result = mysqli_query($link, $query);

if (!$result || !mysqli_num_rows($result))
    die("<br/>.........No results......<br/>");

$number_of_affected_users = mysqli_num_rows($result);
echo "<br># of users: $number_of_affected_users";

$orden_usuarios = array();
$nameuser = array();
$organiz_id = array();
$distancia111 = array();
$array_num_evalu = array();
$array_nota_evalu = array();
$lat_usuarios = array();
$lng_usuarios = array();

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

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Search for your partner | Lingua2</title>
    <link rel="stylesheet" href="jquery-ui-1.13.3.custom/jquery-ui.css" />
    <link rel="stylesheet" href="/resources/demos/style.css" />
    <link rel="stylesheet" href="widgets.css" />
    <link rel="stylesheet" href="lingua2general.css" />
    <link rel="stylesheet" href="estilo.css" />
    <link rel="stylesheet" type="text/css" href="../public/css/style.css">
    <script src="jquery-ui-1.13.3.custom/external/jquery/jquery.js"></script>
    <script src="jquery-ui-1.13.3.custom/jquery-ui.js"></script>
    <script src="https://unpkg.com/leaflet@1.6.0/dist/leaflet.js"></script>
    <link href="https://unpkg.com/leaflet@1.6.0/dist/leaflet.css" rel="stylesheet" />
</head>

<body>
    <div class='marco ui-widget-content' style="display: flex;">
        <nav style="width:20%;text-align:left;padding: 13px;">
            <?php 
            $user_languages_js = [];
            for ($i = 0; $i < $number_of_affected_users; $i++) {
                $orden_usu = $orden_usuarios[$i];
                
                $user_languages_js[$orden_usu] = [
                    'teaches' => isset($my_langs_full_name_array_multidim[$orden_usu]) ? 
                                 $my_langs_full_name_array_multidim[$orden_usu] : [],
                    'learns' => isset($learn_langs_full_name_array_multidim[$orden_usu]) ? 
                                $learn_langs_full_name_array_multidim[$orden_usu] : []
                ];

            }
            
            echo "<script>var userLanguagesData = ".json_encode($user_languages_js).";</script>";
            require_once('search-form.php'); ?>
        </nav>
        <section>
            <h1>Resultados</h1>
            <div class='marco-fichas'>
                <?php
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
                $number_of_orgs = mysqli_num_rows($result_orgs);

                for ($iiii = 0; $iiii < $number_of_orgs; $iiii++) {
                    $fila_orgs = mysqli_fetch_array($result_orgs);
                    $o_id = $fila_orgs['organization_id'];
                    $o_name = $fila_orgs['organization_name'];
                    $lista_de_orgs[$o_id] = "$o_name";
                }

                for ($i = 0; $i < $number_of_affected_users; $i++):
                    $orden_usu = $orden_usuarios[$i];
                    $nombreusu = $nameuser[$i];
                    $organizac = isset($lista_de_orgs[$organiz_id[$i]]) ? $lista_de_orgs[$organiz_id[$i]] : "Unknown";
                    $distancia12 = $distancia111[$i];
                    $num_evalu = $array_num_evalu[$i];
                    $nota_evalu = $array_nota_evalu[$i];
                    $lat_usuario = $lat_usuarios[$i];
                    $lng_usuario = $lng_usuarios[$i];
                    ?>

                    <div class='ficha user-card' id='user-<?php echo $orden_usu; ?>' data-lat='<?php echo $lat_usuario; ?>'
                        data-lng='<?php echo $lng_usuario; ?>'>
                        <div>
                            <?php
                            $image_path = "../uploader/upload_pic/thumb_$orden_usu.jpg";
                            $default_image_path = "../uploader/default.jpg";
                            $image_to_show = file_exists($image_path) ? $image_path : $default_image_path;
                            echo "<img src='$image_to_show' />";
                            echo "$i. $nombreusu #$orden_usu";
                            ?>
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
                                        $bandera_path = "./banderasseparadas2024/blank.png";
                                    }
                                    ?>
                                    <img src="<?php echo $bandera_path; ?>" />
                                    <img src="../user/images/language_levels/<?php echo $my_langs_level_image_array_multidim[$orden_usu][$sss]; ?>" 
                                         alt="Nivel <?php echo $my_langs_level_array_multidim[$orden_usu][$sss]; ?>" />
                                    <span><?php echo $my_langs_full_name_array_multidim[$orden_usu][$sss]; ?></span>
                                </div>
                            <?php 
                                }
                            } else {
                                echo "<div>No languages specified</div>";
                            }
                            ?>
                        </div> 
                        
                        <div class="user-learning">
                            <span>Learning:</span>
                            <?php 
                            if (isset($learn_langs_array_multidim[$orden_usu]) && is_array($learn_langs_array_multidim[$orden_usu])) {
                                for ($sss = 0; $sss < count($learn_langs_array_multidim[$orden_usu]); $sss++) { 
                            ?>
                                <div>
                                    <?php 
                                    $codig_dos_letras = $learn_langs_2letters_array_multidim[$orden_usu][$sss];
                                    $bandera_path = "./banderasseparadas2024/$codig_dos_letras.png";
                                    if (!file_exists($bandera_path)) { 
                                        $bandera_path = "./banderasseparadas2024/blank.png";
                                    }
                                    ?>
                                    <img src="<?php echo $bandera_path; ?>" />
                                    <img src="../user/images/language_levels/<?php echo $learn_langs_level_image_array_multidim[$orden_usu][$sss]; ?>" alt="" />
                                    <span><?php echo $learn_langs_full_name_array_multidim[$orden_usu][$sss]; ?></span>
                                </div>
                            <?php 
                                }
                            } else {
                                echo "<div>No languages specified</div>";
                            }
                            ?>
                        </div> 
                        
                        <div><?php echo "Org: $organizac"; ?></div>
                        <div><?php echo "$distancia12 km from me"; ?></div>
                        <div><?php echo "Número de evaluaciones: $num_evalu"; ?></div>
                        <div><?php echo "Nota evaluación: $nota_evalu"; ?></div>
                    </div> 

                <?php endfor; ?>
            </div>
        </section>
    </div> 
    <?php
    require('../templates/footer.php')
        ?>
</body>
</html>