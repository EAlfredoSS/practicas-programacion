<?php
session_start();
$mi_identificador = $_SESSION['orden2017'];

require('../templates/header_simplified.html');
require('../files/bd.php');
require('../funcionesphp/funciones_idiomas_usuario.php');

// Query to get friends (contacts)
$query = "SELECT DISTINCT m.orden, m.nombre, m.Ciudad, m.timeshift, m.fotoext, m.ev_num_diaria, m.ev_proporc_diaria 
       FROM mentor2009 m 
       INNER JOIN couples2009antiguos c 
       ON m.orden = c.user_id_1 
       WHERE c.user_id_2 = ? AND c.contactado = 1
       UNION 
       SELECT DISTINCT m.orden, m.nombre, m.Ciudad, m.timeshift, m.fotoext, m.ev_num_diaria, m.ev_proporc_diaria 
       FROM mentor2009 m 
       INNER JOIN couples2009antiguos c 
       ON m.orden = c.user_id_2 
       WHERE c.user_id_1 = ? AND c.contactado = 1";

$stmt = mysqli_prepare($link, $query);
mysqli_stmt_bind_param($stmt, "ii", $mi_identificador, $mi_identificador);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$num_friends = mysqli_num_rows($result);

// Arrays for languages
$my_langs_array_multidim = array(array());
$my_langs_full_name_array_multidim = array(array());
$my_langs_level_array_multidim = array(array());
$my_langs_forshare_array_multidim = array(array());
$my_langs_price_array_multidim = array(array());
$my_langs_typeofexchange_array_multidim = array(array());
$my_langs_priceorexchangetext_array_multidim = array(array());
$my_langs_level_image_array_multidim = array(array());
$my_langs_2letters_array_multidim = array(array());

$learn_langs_array_multidim = array(array());
$learn_langs_full_name_array_multidim = array(array());
$learn_langs_level_array_multidim = array(array());
$learn_langs_forshare_array_multidim = array(array());
$learn_langs_price_array_multidim = array(array());
$learn_langs_typeofexchange_array_multidim = array(array());
$learn_langs_priceorexchangetext_array_multidim = array(array());
$learn_langs_level_image_array_multidim = array(array());
$learn_langs_2letters_array_multidim = array(array());
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Proposal - Select Friend | Lingua2</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <style>
        :root {
            --primary-orange: #d35400;
            --accent-orange: #e67e22;
            --light-orange: #fdf2e9;
            --confirmed-green: #27ae60;
            --waiting-grey: #95a5a6;
            --waiting-bg: #f2f3f4;
            --text-dark: #2c3e50;
            --text-grey: #7f8c8d;
            --bg-grey: #f4f7f6;
            --shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        }

        body {
            font-family: 'Roboto', sans-serif;
            background-color: var(--bg-grey);
            margin: 0;
            padding: 0;
        }

        /* Ajustes responsive para el header (evita que el botón Home se desplace mal) */
        @media (max-width: 768px) {

            .main-header .container,
            .header-nav {
                flex-wrap: wrap;
                justify-content: center;
                text-align: center;
            }

            .header-nav ul {
                flex-direction: column;
                gap: 10px;
                margin: 10px 0;
            }

            .header-nav li {
                margin: 0;
            }
        }

        /* Page Header – con fondo blanco y sombra */
        .page-header {
            padding: 30px 0;
            margin-bottom: 30px;
        }

        .page-title {
            font-size: clamp(32px, 8vw, 60px);
            font-weight: bold;
            color: var(--text-dark);
            margin-bottom: 10px;
        }

        .page-title i {
            color: var(--primary-orange);
            margin-right: 12px;
        }

        .page-subtitle {
            margin-top: 20px;
            font-size: clamp(16px, 4vw, 20px);
            color: var(--text-grey);
        }

        .page-subtitle i {
            color: var(--accent-orange);
            margin-right: 8px;
        }

        /* Friend Card */
        .friend-card {
            background: white;
            border-radius: 8px;
            box-shadow: var(--shadow);
            margin-bottom: 20px;
            display: flex;
            flex-wrap: wrap;
            padding: 20px;
            border-left: 5px solid var(--primary-orange);
            transition: transform 0.2s, box-shadow 0.2s;
            cursor: pointer;
        }

        .friend-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
        }

        /* Left */
        .card-left {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-right: 20px;
            min-width: 80px;
        }

        .avatar-circle {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            background: #ECECEC;
            border: 3px solid #a5a5a5;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        .avatar-circle img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 50%;
        }

        .avatar-circle i {
            font-size: 30px;
            color: linear-gradient(135deg, #fdf2e9, #fce8d6);
        }

        .badge-friends {
            margin-top: 10px;
            font-size: 11px;
            font-weight: bold;
            padding: 4px 12px;
            border-radius: 12px;
            background-color: #eafaf1;
            color: #27ae60;
            border: 1px solid #27ae60;
            text-transform: uppercase;
        }

        /* Middle */
        .card-middle {
            flex: 1;
            padding-right: 15px;
        }

        .card-title {
            font-size: 18px;
            font-weight: bold;
            color: var(--text-dark);
            margin-bottom: 10px;
        }

        .meta-row {
            display: flex;
            gap: 20px;
            font-size: 14px;
            color: var(--text-dark);
            margin-bottom: 12px;
            flex-wrap: wrap;
        }

        .meta-item {
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .meta-item i {
            color: var(--accent-orange);
        }

        .language-tags {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
            margin-top: 5px;
        }

        .lang-tag {
            background-color: var(--light-orange);
            color: var(--primary-orange);
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 12px;
            font-weight: 600;
            border: 1px solid #f5c4aa;
        }

        .languages-label {
            font-size: 10px;
            color: #aaa;
            text-transform: uppercase;
            margin: 8px 0 5px 0;
            font-weight: bold;
            letter-spacing: 0.5px;
        }

        /* Right - Botón centrado y más grande */
        .card-right {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            min-width: 180px;
        }

        .btn-create {
            border: 2px solid #e67e22;
            background: linear-gradient(135deg, #fdf2e9, #fce8d6);
            padding: 12px 24px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 15px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
            color: #d35400;
            text-transform: uppercase;
            width: 100%;
            justify-content: center;
            text-decoration: none;
            transition: all 0.2s;
            text-align: center;
        }

        .btn-create:hover {
            background: linear-gradient(135deg, #e67e22, #d35400);
            color: white;
            text-decoration: none;
        }

        /* Empty State */
        .empty-state {
            background: white;
            border-radius: 8px;
            box-shadow: var(--shadow);
            padding: 50px 30px;
            text-align: center;
            max-width: 500px;
            margin: 40px auto;
            border-top: 4px solid var(--primary-orange);
        }

        .empty-state-icon {
            width: 70px;
            height: 70px;
            background: linear-gradient(135deg, #fdf2e9, #fce8d6);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
        }

        .empty-state-icon i {
            font-size: 30px;
            color: var(--primary-orange);
        }

        .empty-state h3 {
            color: var(--text-dark);
            font-size: 22px;
            margin-bottom: 10px;
        }

        .empty-state p {
            color: var(--text-grey);
            font-size: 15px;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .friend-card {
                flex-direction: column;
            }

            .card-left,
            .card-middle,
            .card-right {
                width: 100%;
                align-items: center;
                margin-right: 0;
                margin-bottom: 12px;
                text-align: center;
            }

            /* La etiqueta "Friend" se mantiene debajo del avatar */
            .card-left {
                flex-direction: column;
                justify-content: center;
                gap: 8px;
            }

            .card-right {
                align-items: center;
                justify-content: center;
            }

            .btn-create {
                width: 100%;
                max-width: 280px;
                margin: 0 auto;
            }

            .page-title {
                text-align: center;
            }

            .page-subtitle {
                text-align: center;
            }

            .meta-row {
                justify-content: center;
            }

            .language-tags {
                justify-content: center;
            }
        }
    </style>
</head>

<body>
    <div class="wrapper">
        <!-- Page Header (título y subtítulo con iconos) -->
        <div class="page-header">
            <div class="container">
                <h1 class="page-title"><i class="fas fa-file-alt"></i> Create New Proposal</h1>
                <p class="page-subtitle"><i class="fas fa-user-friends"></i> Select a friend to create a new lesson proposal</p>
            </div>
        </div>

        <section class="forum-page">
            <div class="container">
                <div class="forum-questions-sec" style="width: 100%">
                    <div class="forum-questions">

                        <?php if ($num_friends == 0): ?>
                            <div class="empty-state">
                                <div class="empty-state-icon">
                                    <i class="fas fa-user-friends"></i>
                                </div>
                                <h3>No Friends Found</h3>
                                <p>You don't have any contacts yet. Start connecting with other users to create lesson proposals.</p>
                            </div>
                        <?php else: ?>
                            <?php while ($friend = mysqli_fetch_array($result)):
                                $friend_id = $friend['orden'];
                                $friend_name = $friend['nombre'];
                                $friend_city = $friend['Ciudad'];
                                $friend_timezone = $friend['timeshift'];
                                $friend_extension = $friend['fotoext'];

                                // Get friend's photo
                                $path_photo = "../uploader/upload_pic/thumb_$friend_id" . "." . "$friend_extension";
                                if (!file_exists($path_photo)) {
                                    $path_photo = "../uploader/default.jpg";
                                }

                                // Get friend's languages
                                list(
                                    $my_langs_array_multidim["$friend_id"],
                                    $my_langs_full_name_array_multidim["$friend_id"],
                                    $my_langs_level_array_multidim["$friend_id"],
                                    $my_langs_forshare_array_multidim["$friend_id"],
                                    $my_langs_price_array_multidim["$friend_id"],
                                    $my_langs_typeofexchange_array_multidim["$friend_id"],
                                    $my_langs_priceorexchangetext_array_multidim["$friend_id"],
                                    $my_langs_level_image_array_multidim["$friend_id"],
                                    $my_langs_2letters_array_multidim["$friend_id"]
                                )
                                    = lenguas_que_conoce_usuario($friend_id, $link);
                            ?>

                                <div class="friend-card" onclick="window.location.href='../user/u.php?identificador=<?php echo $friend_id; ?>'">
                                    <!-- LEFT -->
                                    <div class="card-left">
                                        <div class="avatar-circle">
                                            <img src="<?php echo $path_photo; ?>" alt="<?php echo $friend_name; ?>">
                                        </div>
                                        <div class="badge-friends">
                                            Friend
                                        </div>
                                    </div>

                                    <!-- MIDDLE -->
                                    <div class="card-middle">
                                        <div class="card-title">
                                            <?php echo ucfirst(explode(" ", $friend_name)[0]); ?>
                                        </div>

                                        <div class="meta-row">
                                            <span class="meta-item"><i class="fas fa-map-marker-alt"></i> <?php echo $friend_city ? $friend_city : 'Location not specified'; ?></span>
                                        </div>

                                        <div class="languages-label">LANGUAGES OFFERED</div>
                                        <div class="language-tags">
                                            <?php
                                            $friend_langs = $my_langs_full_name_array_multidim["$friend_id"];
                                            if (is_array($friend_langs)) {
                                                foreach ($friend_langs as $idx => $lang) {
                                                    if (!empty($lang)) {
                                                        echo "<span class=\"lang-tag\">$lang</span>";
                                                    }
                                                }
                                            }
                                            ?>
                                        </div>
                                    </div>

                                    <!-- RIGHT: Botón centrado y más grande -->
                                    <div class="card-right">
                                        <a href="sent-studentcreateproposal.php?tid=<?php echo $friend_id; ?>"
                                            class="btn-create"
                                            onclick="event.stopPropagation();">
                                            <i class="fas fa-paper-plane"></i> Create Proposal
                                        </a>
                                    </div>
                                </div>

                            <?php endwhile; ?>
                        <?php endif; ?>

                    </div>
                </div>
            </div>
        </section>

        <?php require('../templates/footer.php'); ?>
    </div>

    <script src="../public/js/jquery.min.js"></script>
</body>

</html>
<?php mysqli_close($link); ?>