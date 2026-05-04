<?php
session_start();
require("../files/bd.php");

$identificador2017 = isset($_GET['u']) ? (int)$_GET['u'] : 0;
if (!$identificador2017) die('User not specified.');

$q = "SELECT * FROM mentor2009 WHERE orden = '$identificador2017'";
$r = mysqli_query($link, $q);
if (!mysqli_num_rows($r)) die('User does not exist.');
$fila = mysqli_fetch_assoc($r);
$nombre_corto = explode(' ', $fila['nombre'])[0];

// limpiar parejas rotas
$ids_parejas = array();

$q1 = "SELECT user_id_2 FROM couples2009antiguos WHERE user_id_1 = '$identificador2017'";
$r1 = mysqli_query($link, $q1);
while ($row = mysqli_fetch_assoc($r1)) {
    if (!is_null($row['user_id_2'])) $ids_parejas[] = $row['user_id_2'];
}

$q2 = "SELECT user_id_1 FROM couples2009antiguos WHERE user_id_2 = '$identificador2017'";
$r2 = mysqli_query($link, $q2);
while ($row = mysqli_fetch_assoc($r2)) {
    if (!is_null($row['user_id_1'])) $ids_parejas[] = $row['user_id_1'];
}

if (!empty($ids_parejas)) {
    foreach ($ids_parejas as $pid) {
        $chk = mysqli_query($link, "SELECT orden FROM mentor2009 WHERE orden = '$pid'");
        if (mysqli_num_rows($chk) == 0) {
            mysqli_query($link, "DELETE FROM couples2009antiguos WHERE user_id_1 = '$pid' AND user_id_2 = '$identificador2017'");
            mysqli_query($link, "DELETE FROM couples2009antiguos WHERE user_id_2 = '$pid' AND user_id_1 = '$identificador2017'");
        }
    }
}

// evaluaciones
$evals = "
    SELECT m.nombre AS nombre1, m.orden AS orden1, m.fotoext AS fotoext1,
           c.comment, c.hora, c.rating
    FROM comentarios c
    LEFT JOIN mentor2009 m ON m.orden = c.id_autor
    WHERE c.id_aludido = '$identificador2017' AND c.censurado = 0
    ORDER BY c.horacreacion DESC
";
$res_ev = mysqli_query($link, $evals);
$total_ev = mysqli_num_rows($res_ev);

$tot = mysqli_fetch_assoc(mysqli_query($link, "SELECT COUNT(*) as total FROM comentarios WHERE id_aludido = '$identificador2017' AND censurado = 0"));
$n_comentarios = $tot['total'];

$pos = mysqli_fetch_assoc(mysqli_query($link, "SELECT COUNT(*) as total FROM comentarios WHERE id_aludido = '$identificador2017' AND censurado = 0 AND rating = 1"));
$n_pos = $pos['total'];
$porcentaje = ($n_comentarios > 0) ? round($n_pos * 100 / $n_comentarios) : 0;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-139626327-1"></script>
    <script>
        window.dataLayer = window.dataLayer || [];

        function gtag() {
            dataLayer.push(arguments);
        }
        gtag('js', new Date());
        gtag('config', 'UA-139626327-1');
    </script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Evaluations of <?= htmlspecialchars($nombre_corto) ?> | Lingua2</title>
    <style>
        *,
        *::before,
        *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            background: #f7f8fa;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            color: #2c3e50;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .ev-page {
            background-color: #f7f8fa;
            border: 1px solid #d35400;
            border-left-width: 10px;
            border-radius: 10px;
            flex: 1;
            width: 100%;
            max-width: 800px;
            margin: 0 auto;
            padding: 15px 20px 56px;
            box-shadow: 0 18px 30px rgba(0, 0, 0, 0.05), 0 2px 4px rgba(0, 0, 0, 0.03);
        }

        .ev-header {
            margin-bottom: 30px;
            margin-top: 5px;
        }

        .ev-header h1 {
            font-size: 60px;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 20px;
        }

        .ev-meta {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        .ev-meta-count {
            font-size: 13px;
            color: #5a6e7c;
        }

        .ev-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            background: #fff4ee;
            color: #d35400;
            border: 1px solid #f5c4aa;
            border-radius: 20px;
            padding: 2px 10px;
            font-size: 12px;
            font-weight: 700;
        }

        .ev-empty {
            text-align: center;
            padding: 50px 20px;
            color: #95a5a6;
            font-size: 15px;
        }

        .ev-empty-icon {
            font-size: 38px;
            margin-bottom: 12px;
            display: block;
        }

        .ev-card {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.05), 0 1px 3px rgba(0, 0, 0, 0.04);
            padding: 20px 22px;
            margin-bottom: 14px;
            transition: box-shadow 0.2s;
        }

        .ev-card:hover {
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.08);
        }

        .ev-card-top {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 12px;
        }

        .ev-avatar {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            object-fit: cover;
            flex-shrink: 0;
            border: 2px solid #f0f0f0;
        }

        .ev-author-info {
            flex: 1;
            min-width: 0;
        }

        .ev-author-name {
            font-size: 15px;
            font-weight: 700;
            color: #2c3e50;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .ev-date {
            font-size: 12px;
            color: #95a5a6;
            margin-top: 2px;
        }

        .ev-menu-wrap {
            position: relative;
        }

        .ev-menu-btn {
            background: none;
            border: none;
            cursor: pointer;
            color: #bdc3c7;
            padding: 4px 6px;
            border-radius: 6px;
            font-size: 18px;
            line-height: 1;
            transition: background 0.15s, color 0.15s;
        }

        .ev-menu-btn:hover {
            background: #f0f0f0;
            color: #7f8c8d;
        }

        .ev-dropdown {
            display: none;
            position: absolute;
            right: 0;
            top: calc(100% + 4px);
            background: #fff;
            border: 1px solid #e8ecef;
            border-radius: 12px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
            min-width: 180px;
            z-index: 100;
            overflow: hidden;
        }

        .ev-dropdown.open {
            display: block;
        }

        .ev-dropdown a {
            display: block;
            padding: 11px 16px;
            font-size: 13px;
            font-weight: 500;
            color: #2c3e50;
            text-decoration: none;
            transition: background 0.15s, color 0.15s;
        }

        .ev-dropdown a:hover {
            background: #d35400;
            color: #fff;
        }

        .ev-rating {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.3px;
            text-transform: uppercase;
            margin-bottom: 10px;
        }

        .ev-rating.positive {
            background: #eafaf1;
            color: #1e8449;
        }

        .ev-rating.neutral {
            background: #f4f6f7;
            color: #5d6d7e;
        }

        .ev-rating.negative {
            background: #fdedec;
            color: #c0392b;
        }

        .ev-rating.no-answer {
            background: #fef9e7;
            color: #d68910;
        }

        .ev-comment {
            font-size: 14px;
            color: #4a5568;
            line-height: 1.6;
        }

        @media (max-width: 480px) {
            .ev-page {
                padding: 0px 14px 40px;
            }

            .ev-header h1 {
                font-size: 28px;
            }

            .ev-card {
                padding: 14px;
            }

            .ev-avatar {
                width: 42px;
                height: 42px;
            }

            .ev-author-name {
                font-size: 14px;
            }

            .ev-date {
                font-size: 11px;
            }
        }
    </style>
</head>

<body>

    <?php require("../templates/header_simplified.html"); ?>

    <main>
        <div class="ev-page">
            <div class="ev-header">
                <h1>Evaluations of <?= htmlspecialchars($nombre_corto) ?></h1>
                <div class="ev-meta">
                    <span class="ev-meta-count"><?= $n_comentarios ?> evaluation<?= $n_comentarios != 1 ? 's' : '' ?> received</span>
                    <?php if ($n_comentarios > 0): ?>
                        <span class="ev-badge">&#9733; <?= $porcentaje ?>% positive</span>
                    <?php endif; ?>
                </div>
            </div>

            <?php if ($total_ev == 0): ?>
                <div class="ev-empty">
                    <span class="ev-empty-icon">💬</span>
                    No evaluations yet.
                </div>
            <?php else: ?>
                <?php while ($row = mysqli_fetch_assoc($res_ev)):
                    $comentario = $row['comment'];
                    $hora = $row['hora'];
                    $rating = (int)$row['rating'];
                    $autor_id = $row['orden1'];
                    $foto_ext = $row['fotoext1'];
                    $autor_nombre = $row['nombre1'];

                    $map = [
                        1 => ['label' => 'Positive', 'class' => 'positive'],
                        2 => ['label' => 'Neutral',  'class' => 'neutral'],
                        3 => ['label' => 'Negative', 'class' => 'negative'],
                        4 => ['label' => 'No answer', 'class' => 'no-answer'],
                    ];
                    $r = isset($map[$rating]) ? $map[$rating] : ['label' => 'Unknown', 'class' => 'neutral'];

                    if (!is_null($autor_nombre)) {
                        $parts = explode(" ", $autor_nombre);
                        $autor_nombre = ucfirst($parts[0]);
                    }
                    if (!$autor_nombre) $autor_nombre = "User unregistered";

                    $avatar = "../uploader/upload_pic/thumb_$autor_id.$foto_ext";
                    if (!file_exists($avatar)) $avatar = "../uploader/default.jpg";
                ?>
                    <div class="ev-card">
                        <div class="ev-card-top">
                            <img class="ev-avatar" src="<?= htmlspecialchars($avatar) ?>" alt="<?= htmlspecialchars($autor_nombre) ?>">
                            <div class="ev-author-info">
                                <div class="ev-author-name"><?= htmlspecialchars($autor_nombre) ?></div>
                                <div class="ev-date">Evaluated on <?= htmlspecialchars($hora) ?></div>
                            </div>
                            <div class="ev-menu-wrap">
                                <button class="ev-menu-btn" onclick="toggleMenu(this)" type="button">&#8942;</button>
                                <div class="ev-dropdown">
                                    <a href="../user/u.php?identificador=<?= $autor_id ?>">Visit profile</a>
                                </div>
                            </div>
                        </div>
                        <div class="ev-rating <?= $r['class'] ?>">
                            <?= $r['dot'] ?> <?= $r['label'] ?>
                        </div>
                        <?php if ($comentario): ?>
                            <p class="ev-comment"><?= htmlspecialchars($comentario) ?></p>
                        <?php endif; ?>
                    </div>
                <?php endwhile; ?>
            <?php endif; ?>
        </div>
    </main>

    <?php require('../templates/footer.php'); ?>

    <script>
        function toggleMenu(btn) {
            var dropdown = btn.nextElementSibling;
            var open = dropdown.classList.contains('open');
            document.querySelectorAll('.ev-dropdown.open').forEach(function(d) {
                d.classList.remove('open');
            });
            if (!open) dropdown.classList.add('open');
        }
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.ev-menu-wrap')) {
                document.querySelectorAll('.ev-dropdown.open').forEach(function(d) {
                    d.classList.remove('open');
                });
            }
        });
    </script>

</body>

</html>