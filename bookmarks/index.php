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

// NOTA: La función generateUserCard() está en search_results.php
// NO LA DECLARES AQUÍ para evitar el error de redeclaración

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
    
    <!-- ESTILOS PERSONALIZADOS ACTUALIZADOS -->
    <style>
        :root {
            --primary-orange: #d35400;
            --accent-orange: #e67e22;
            --light-orange: #fdf2e9;
            --waiting-grey: #95a5a6;
            --waiting-bg: #f2f3f4;
            --text-dark: #2c3e50;
            --text-grey: #7f8c8d;
            --border-color: #ecf0f1;
            --border-grey: #bdc3c7;
            --bg-grey: #f4f7f6;
            --white: #ffffff;
            --shadow: 0 2px 8px rgba(0,0,0,0.08);
            --red-badge: #e74c3c;
        }

        body {
            font-family: 'Roboto', sans-serif;
            background-color: var(--bg-grey);
        }

        /* ===== TARJETA ===== */
        .company_profile_info {
            background: white;
            border: 1px solid var(--border-grey);
            border-radius: 8px;
            padding: 16px;
            margin-bottom: 20px;
            box-shadow: var(--shadow);
            transition: transform 0.2s, box-shadow 0.2s;
            display: flex;
            flex-direction: column;
            height: 100%;
        }

        .company_profile_info:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.12);
        }

        /* ===== AVATAR ===== */
        .usr-pic {
            width: 75px;
            height: 75px;
            border-radius: 50%;
            border: 1px solid var(--border-grey);
            overflow: hidden;
            margin: 0 auto 12px;
            flex-shrink: 0;
        }

        .usr-pic img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        /* ===== NOMBRE Y UBICACIÓN ===== */
        .profile_info {
            text-align: center;
            flex: 1;
        }

        .profile_info h3 {
            font-size: 15px;
            font-weight: 700;
            color: var(--text-dark);
            margin: 0 0 3px 0;
            line-height: 1.3;
        }

        .user-id {
            font-size: 11px;
            font-weight: 400;
            color: #b0b0b0;
            margin-left: 3px;
        }

        .profile_info p {
            font-size: 12px;
            color: var(--text-grey);
            margin: 0 0 8px 0;
            line-height: 1.3;
        }

        .profile_info p i {
            color: var(--primary-orange);
            margin-right: 3px;
            font-size: 11px;
        }

        /* ===== ESTADÍSTICAS CON LÓGICA DE COLORES ===== */
        .user-stats {
            display: flex;
            justify-content: center;
            gap: 6px;
            margin: 8px 0 10px 0;
            flex-wrap: wrap;
        }

        .stat-item {
            flex: 0 1 auto;
        }

        .stat-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 4px;
            padding: 5px 10px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 12px;
            white-space: nowrap;
            transition: all 0.2s ease;
        }

        /* ESTRELLAS */
        .stat-icon.star.star-zero {
            background: transparent;
            color: var(--primary-orange);
        }
        
        .stat-icon.star.star-positive {
            background: var(--red-badge);
            color: white;
        }
        
        /* CORAZONES */
        .stat-icon.heart.heart-zero {
            background: transparent;
            color: var(--primary-orange);
        }
        
        .stat-icon.heart.heart-positive {
            background: var(--red-badge);
            color: white;
        }

        .stat-number {
            font-weight: 700;
        }

        .stat-icon i {
            font-size: 11px;
        }

        /* ===== BOTONES ===== */
        .action-buttons {
            display: flex;
            gap: 6px;
            margin-top: auto;
            padding-top: 12px;
            width: 100%;
        }

        .btn-card {
            flex: 1 1 0;
            min-width: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 4px;
            padding: 8px 6px;
            border-radius: 7px;
            font-weight: 600;
            font-size: 10px;
            text-align: center;
            text-decoration: none;
            transition: all 0.2s ease;
            cursor: pointer;
            line-height: 1.3;
            min-height: 34px;
            white-space: normal;
        }

        .btn-card i {
            flex-shrink: 0;
            font-size: 10px;
        }

        /* VIEW PROFILE */
        .btn-view-profile {
            background: var(--waiting-grey);
            color: white;
            border: none;
        }

        .btn-view-profile:hover {
            background: #7f8c8d;
            transform: translateY(-1px);
            box-shadow: 0 3px 8px rgba(149,165,166,0.3);
            text-decoration: none;
            color: white;
        }

        /* REMOVE */
        .btn-remove-fav {
            background: transparent;
            color: var(--waiting-grey);
            border: 1px solid var(--border-grey);
        }

        .btn-remove-fav i {
            color: var(--waiting-grey);
        }

        .btn-remove-fav:hover {
            background: var(--waiting-bg);
            color: #636e72;
            border-color: var(--waiting-grey);
            transform: translateY(-1px);
            text-decoration: none;
        }

        /* ===== NOTIFICACIÓN ===== */
        .notification {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: #e77667;
            color: white;
            padding: 15px 25px;
            border-radius: 8px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.2);
            font-size: 16px;
            font-weight: 600;
            z-index: 1000;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .notification.show {
            opacity: 1;
        }

        /* ===== RESPONSIVE ===== */
        @media (min-width: 992px) {
            .col-lg-4 { flex: 0 0 33.333333%; max-width: 33.333333%; }
        }

        @media (min-width: 768px) and (max-width: 991px) {
            .col-md-6 { flex: 0 0 50%; max-width: 50%; }
        }

        @media (max-width: 767px) {
            .col-sm-6 { flex: 0 0 50%; max-width: 50%; }
            .usr-pic { width: 65px; height: 65px; }
            .profile_info h3 { font-size: 13px; }
        }

        @media (max-width: 479px) {
            .col-6 { flex: 0 0 50%; max-width: 50%; }
            .company_profile_info { padding: 10px; }
            .usr-pic { width: 55px; height: 55px; }
            .profile_info h3 { font-size: 12px; }
            .profile_info p { font-size: 10px; }
            .stat-icon { font-size: 10px; padding: 4px 6px; }
            .action-buttons { flex-direction: column; }
            .btn-card { width: 100%; font-size: 10px; min-height: 30px; }
        }
    </style>
</head>

<body>
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
                                    <div class="row">
                                    <?php
                                    // IMPORTANTE: La función generateUserCard() está definida en search_results.php
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
                </div>

                <div class="col-lg-0 pd-right-none no-pd ">
                    <div class="right-sidebar">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

    <!-- SCRIPT ÚNICO CON LA CONFIRMACIÓN -->
    <script>
        function showNotification(notificationElement) {
            notificationElement.style.display = 'block';
            setTimeout(() => {
                notificationElement.classList.add('show');
                setTimeout(() => {
                    notificationElement.classList.remove('show');
                    setTimeout(() => {
                        notificationElement.style.display = 'none';
                        window.location.reload();
                    }, 300);
                }, 2000);
            }, 10);
        }

        // Asegurar que el evento se asigne solo una vez
        if (!window.removeFavListenerAdded) {
            window.removeFavListenerAdded = true;
            
            document.addEventListener('DOMContentLoaded', function () {
                // Eliminar cualquier evento previo y asignar uno nuevo
                document.querySelectorAll('.btn-remove-fav').forEach(button => {
                    // Remover eventos anteriores (por si acaso)
                    button.replaceWith(button.cloneNode(true));
                });
                
                // Asignar eventos a los botones nuevos
                document.querySelectorAll('.btn-remove-fav').forEach(button => {
                    button.addEventListener('click', function (e) {
                        e.preventDefault();

                        // CONFIRMACIÓN CON EL NOMBRE DEL USUARIO
                        const userName = this.getAttribute('data-username');
                        if (!confirm(`Are you sure you want to remove ${userName} from favourites?`)) {
                            return;
                        }

                        const url = this.getAttribute('data-url');
                        const card = this.closest('.company_profile_info');
                        const notification = card.querySelector('.notification');
                        const originalHTML = this.innerHTML;

                        this.style.pointerEvents = 'none';
                        this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> <span>Removing...</span>';

                        fetch(url, { method: 'GET' })
                            .then(response => {
                                if (!response.ok) throw new Error('Server error');
                                return response.text();
                            })
                            .then(() => {
                                if (notification) {
                                    showNotification(notification);
                                } else {
                                    setTimeout(() => window.location.reload(), 500);
                                }
                            })
                            .catch(error => {
                                console.error('Error:', error);
                                this.innerHTML = originalHTML;
                                this.style.pointerEvents = 'auto';
                                alert('Error removing user. Please try again.');
                            });
                    });
                });
            });
        }

        // jQuery existente (sin duplicar eventos)
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
</body>
</html>