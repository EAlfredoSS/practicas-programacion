<?php
session_start();

require('../templates/header_simplified.html');
require('../files/idiomasequivalencias.php');
require('../files/bd.php');

$identificador2017 = $_SESSION['orden2017'];
$_SESSION['idusuario2019'] = $identificador2017;

// Obtener información de idiomas del usuario actual
$query = "SELECT * FROM mentor2009 WHERE orden='" . $identificador2017 . "'";
$result = mysqli_query($link, $query);
if (!mysqli_num_rows($result)) {
    die("User unregistered. <a href=\"http://www.lingua2.com\">Information</a>");
}

$fila = mysqli_fetch_array($result);
$tipo_form = $fila['Pais'];
$teacher_price = $fila['teacherprice'];

?>

<!DOCTYPE HTML>
<html>
<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-139626327-1"></script>
<script>
    window.dataLayer = window.dataLayer || [];

    function gtag() {
        dataLayer.push(arguments);
    }
    gtag('js', new Date());
    gtag('config', 'UA-139626327-1');
</script>

<head>
    <script src="https://kit.fontawesome.com/bb6243346a.js" crossorigin="anonymous"></script>
    <script type="text/javascript" src="../public/js/jquery.min.js"></script>
    <script type="text/javascript" src="../public/js/popper.js"></script>
    <script type="text/javascript" src="../public/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="../public/js/jquery.mCustomScrollbar.js"></script>
    <script type="text/javascript" src="../public/lib/slick/slick.min.js"></script>
    <script type="text/javascript" src="../public/js/scrollbar.js"></script>
    <script type="text/javascript" src="../public/js/script.js"></script>
    <title>Search| Lingua2</title>

    <!-- Google Fonts Roboto -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" type="text/css" href="../public/css/animate.css">
    <link rel="stylesheet" href="../public/css/bootstrap-4.2.1.css">
    <link rel="stylesheet" type="text/css" href="../public/css/jquery.range.css">
    <link rel="stylesheet" type="text/css" href="../public/css/line-awesome.css">
    <link rel="stylesheet" type="text/css" href="../public/css/slick.css">
    <link rel="stylesheet" type="text/css" href="../public/css/slick-theme.css">
    <link rel="stylesheet" type="text/css" href="../public/css/responsive.css">
    <link rel="stylesheet" type="text/css" href="../public/css/style.css">
    <link rel="stylesheet" href="../public/css/font-awesome.4.7.0.css">
    <link rel="stylesheet" href="../user/css/languages.css" media="all" />
    <link rel="stylesheet" href="search.css" media="all" />
    
    <!-- ESTILOS PERSONALIZADOS -->
    <style>
        :root {
            --primary: #d35400;
            --primary-light: #fdf2e9;
            --success: #27ae60;
            --danger: #e74c3c;
            --text-dark: #2c3e50;
            --text-grey: #7f8c8d;
            --bg-grey: #f4f7f6;
            --border: #ecf0f1;
            --white: #ffffff;
            --shadow: 0 2px 8px rgba(0,0,0,0.08);
        }

        body {
            font-family: 'Roboto', sans-serif;
            background-color: var(--bg-grey);
        }

        /* TARJETA CON BORDE FINO - CAMBIADO DE 3px A 1px */
        .company_profile_info {
            border: 1px solid var(--primary) !important;
            border-radius: 10px !important;
            padding: 20px !important;
            margin-bottom: 20px !important;
            background: var(--white) !important;
            box-shadow: var(--shadow) !important;
            transition: transform 0.2s, box-shadow 0.2s;
            display: flex;
            flex-direction: column;
            height: 100%;
        }

        .company_profile_info:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.12);
        }

        /* Avatar redondo con borde naranja */
        .usr-pic {
            width: 90px;
            height: 90px;
            border-radius: 50% !important;
            border: 3px solid var(--primary) !important;
            overflow: hidden;
            margin: 0 auto 15px;
        }

        .usr-pic img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        /* Nombre y ubicación */
        .profile_info {
            text-align: center;
            flex: 1;
        }

        .profile_info h3 {
            font-size: 18px;
            font-weight: 700;
            color: var(--text-dark);
            margin-bottom: 5px;
        }

        /* NUEVO: ID de usuario al lado del nombre */
        .user-id {
            font-size: 13px;
            font-weight: 400;
            color: #b0b0b0;
            margin-left: 5px;
        }

        .profile_info p {
            font-size: 14px;
            color: var(--text-grey);
            margin-bottom: 15px;
        }

        .profile_info p i {
            color: var(--primary);
        }

        /* Estadísticas */
        .user-stats {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin: 15px 0;
            padding: 12px;
            background: var(--primary-light);
            border-radius: 8px;
        }

        .stat-item {
            text-align: center;
        }

        .stat-value {
            font-size: 20px;
            font-weight: 700;
            color: var(--primary);
        }

        .stat-label {
            font-size: 11px;
            color: var(--text-grey);
            text-transform: uppercase;
        }

        /* BOTONES */
        .button-row {
            display: flex !important;
            gap: 10px !important;
            margin-top: 20px !important;
            width: 100% !important;
        }

        .button-left,
        .button-right {
            flex: 1 !important;
        }

        .button-left a,
        .button-right a {
            width: 100% !important;
            padding: 12px 10px !important;
            border-radius: 8px !important;
            font-weight: 600 !important;
            font-size: 14px !important;
            text-align: center !important;
            text-decoration: none !important;
            display: inline-block !important;
            transition: all 0.2s ease !important;
            border: none !important;
            cursor: pointer !important;
            box-sizing: border-box !important;
            line-height: normal !important;
        }

        /* CAMBIADO: Botón View Profile - azul grisáceo */
        .btn-view {
            background: #5a7a9b !important;
            color: white !important;
        }

        .btn-view:hover {
            background: #6b8caf !important;
            transform: translateY(-2px) !important;
            box-shadow: 0 4px 8px rgba(90, 122, 155, 0.3) !important;
        }

        /* CAMBIADO: Botón Remove - gris neutro */
        .btn-delete {
            background: #95a5a6 !important;
            color: white !important;
        }

        .btn-delete:hover {
            background: #7f8c8d !important;
            transform: translateY(-2px) !important;
            box-shadow: 0 4px 8px rgba(149, 165, 166, 0.3) !important;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .button-row {
                flex-direction: column !important;
            }
        }
    </style>
</head>

<div class="main-section">
    <div class="container">
        <div class="main-section-data">
            <div class="row">

                <?php
                // Columna de la izquierda
                require('./search_dashboard.php');
                ?>

                <!-- Columna principal -->
                <div class="col-lg-9 col-md-7 no-pd">
                    <div class="main-ws-sec">
                        <div class="posts-section">
                            <div id="seccion_teach" class="post-bar">
                                <div class="epi-sec">
                                </div>
                                <div class="container-fluid m-0 p-0" id="user-cards-container">
                                    <?php
                                    require('./search_results.php');
                                    $query = "SELECT m.nombre AS mentor_name, m.orden, m.Ciudad, m.Pais
                                    FROM bookmarkedusers bu
                                    JOIN mentor2009 m ON bu.userwhoissaved = m.orden
                                    WHERE bu.userwhosaves = '" . $identificador2017 . "'
                                    ORDER BY bu.savedtime DESC";

                                    if ($stmt = $link->prepare($query)) {
                                        $stmt->execute();
                                        $result = $stmt->get_result();
                                        if ($result->num_rows > 0) {
                                            while ($row = $result->fetch_assoc()) {
                                                $bookmarked_user_id = $row['orden'];
                                                $nombre_usuar = $row['mentor_name'];
                                                $ciudad = $row['Ciudad'];
                                                $pais = $row['Pais'];

                                                $arr = explode(' ', trim($nombre_usuar));
                                                $nombre_usuar = $arr[0];
                                                $nombre_usuar = ucfirst(substr($nombre_usuar, 0, 13));

                                                $thumb_nombre = $bookmarked_user_id;
                                                $jpg_name = "../uploader/upload_pic/thumb_$thumb_nombre.jpg";
                                                $png_name = "../uploader/upload_pic/thumb_$thumb_nombre.png";
                                                $gif_name = "../uploader/upload_pic/thumb_$thumb_nombre.gif";
                                                $bmp_name = "../uploader/upload_pic/thumb_$thumb_nombre.bmp";

                                                if (file_exists($jpg_name)) {
                                                    $thumb_nombre = $jpg_name;
                                                } else if (file_exists($png_name)) {
                                                    $thumb_nombre = $png_name;
                                                } else if (file_exists($gif_name)) {
                                                    $thumb_nombre = $gif_name;
                                                } else if (file_exists($bmp_name)) {
                                                    $thumb_nombre = $bmp_name;
                                                } else {
                                                    $thumb_nombre = "../uploader/default.jpg";
                                                }

                                                $query_eval = "SELECT COUNT(*) as total FROM comentarios WHERE id_aludido='$bookmarked_user_id' AND censurado=0";
                                                $result_eval = mysqli_query($link, $query_eval);
                                                $row_eval = mysqli_fetch_assoc($result_eval);
                                                $n_comentarios = $row_eval['total'];

                                                $query_pos = "SELECT COUNT(*) as total FROM comentarios WHERE id_aludido='$bookmarked_user_id' AND censurado=0 AND rating=1";
                                                $result_pos = mysqli_query($link, $query_pos);
                                                $row_pos = mysqli_fetch_assoc($result_pos);
                                                $n_comentarios_positivos = $row_pos['total'];

                                                $porcentaje_positivos = ($n_comentarios > 0) ? round($n_comentarios_positivos * 100 / $n_comentarios) : 0;

                                                echo generateUserCard(
                                                    $bookmarked_user_id,
                                                    $nombre_usuar,
                                                    $ciudad,
                                                    $thumb_nombre,
                                                    $n_comentarios,
                                                    $porcentaje_positivos,
                                                    $pais
                                                );
                                            }
                                        } else {
                                            echo '<div class="col-12"><p>You have no bookmarked users yet.</p></div>';
                                        }

                                        $stmt->close();
                                    } else {
                                        echo '<div class="col-12"><p>Error executing query.</p></div>';
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-0 pd-right-none no-pd ">
                    <div class="right-sidebar">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function () {
            var column1 = $('#seccion_teach').clone().attr('id', 'seccion_teach_clone');
            $('#column1').append(column1);
            var column2 = $('#events').clone().attr('id', 'events_clone');
            $('#column2').append(column2);
            var column3 = $('#my_events').clone().attr('id', 'my_events_clone');
            $('#column3').append(column3);

            resize_movil();
            window.addEventListener("resize", function () {
                resize_movil();
            });

            let searchTimer;
            const searchInput = $('#search');
            if (searchInput.length) {
                function filterUserCards(searchText) {
                    if (!searchText || searchText.length < 2) {
                        $('[data-user-card="true"]').show();
                        $('#no-results-message').remove();
                        return;
                    }

                    searchText = searchText.toLowerCase();
                    let visibleCards = 0;
                    $('[data-user-card="true"]').each(function () {
                        const userName = $(this).data('user-name') ? $(this).data('user-name').toLowerCase() : '';
                        const userCity = $(this).data('user-city') ? $(this).data('user-city').toLowerCase() : '';
                        const userCountry = $(this).data('user-country') ? $(this).data('user-country').toLowerCase() : '';

                        if (userName.includes(searchText) ||
                            userCity.includes(searchText) ||
                            userCountry.includes(searchText)) {
                            $(this).show();
                            visibleCards++;
                        } else {
                            $(this).hide();
                        }
                    });

                    if (visibleCards === 0) {
                        if ($('#no-results-message').length === 0) {
                            $('#user-cards-container').append('<div id="no-results-message" class="col-12"><p>No users found matching your search criteria.</p></div>');
                        }
                    } else {
                        $('#no-results-message').remove();
                    }
                }

                searchInput.on('keyup', function () {
                    const searchText = $(this).val().trim();
                    clearTimeout(searchTimer);
                    searchTimer = setTimeout(function () {
                        filterUserCards(searchText);
                    }, 300);
                });

                $('#clear-search, #clear-city').on('click', function (e) {
                    e.preventDefault();
                    searchInput.val('');
                    $('[data-user-card="true"]').show();
                    $('#no-results-message').remove();
                });
            }
        });

        function resize_movil() {
            $("#seccion_teach_clone").css("margin-bottom", "0px");
            $("#events_clone").css("margin-bottom", "0px");

            if (screen.width < 768) {
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
    <script>
        var modal = document.getElementById("myModal");
        var img = document.getElementById("myImg");
        var modalImg = document.getElementById("img01");
        var captionText = document.getElementById("caption");
        img.onclick = function () {
            modal.style.display = "flex";
            modalImg.src = this.src;
            captionText.innerHTML = this.alt;
        }

        var span = document.getElementsByClassName("close")[0];
        span.onclick = function () {
            modal.style.display = "none";
        }
    </script>