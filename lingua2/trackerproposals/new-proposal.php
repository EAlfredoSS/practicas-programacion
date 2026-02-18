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
    <title>New Proposal - Select Friend</title>
    
<style>
:root {
    --primary-orange: #d35400;
    --accent-orange: #e67e22;
    --light-orange: #fdf2e9;
    --text-dark: #2c3e50;
    --text-grey: #7f8c8d;
    --border-color: #ecf0f1;
    --bg-grey: #f4f7f6;
}

body {
    font-family: 'Roboto', sans-serif;
    background-color: var(--bg-grey);
}

.page-header {
    background-color: #fff;
    padding: 30px 0;
    margin-bottom: 30px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
}

.page-title {
    font-size: 28px;
    font-weight: bold;
    color: var(--text-dark);
    margin-bottom: 10px;
}

.page-subtitle {
    font-size: 16px;
    color: var(--text-grey);
}

.friend-card {
    background: white;
    border-radius: 4px;
    box-shadow: 0 2px 5px rgba(0,0,0,0.05);
    margin-bottom: 20px;
    display: flex;
    padding: 20px;
    position: relative;
    border-left: 5px solid var(--primary-orange);
    transition: transform 0.2s, box-shadow 0.2s;
    cursor: pointer;
}

.friend-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 10px rgba(0,0,0,0.1);
}

.friend-avatar {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    border: 3px solid var(--primary-orange);
    margin-right: 20px;
    flex-shrink: 0;
}

.friend-avatar img {
    width: 100%;
    height: 100%;
    border-radius: 50%;
    object-fit: cover;
}

.friend-info {
    flex-grow: 1;
}

.friend-name {
    font-size: 20px;
    font-weight: bold;
    color: var(--text-dark);
    margin-bottom: 5px;
}

.friend-location {
    font-size: 14px;
    color: var(--text-grey);
    margin-bottom: 10px;
}

.friend-location i {
    margin-right: 5px;
    color: var(--accent-orange);
}

.languages-label {
    font-size: 10px;
    color: #aaa;
    text-transform: uppercase;
    margin-bottom: 5px;
    font-weight: bold;
}

.language-tags {
    display: flex;
    gap: 5px;
    flex-wrap: wrap;
}

.lang-tag {
    background-color: var(--light-orange);
    color: var(--primary-orange);
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 12px;
    font-weight: 500;
}

.friend-actions {
    display: flex;
    align-items: center;
}

.btn-create-proposal {
    background-color: var(--primary-orange);
    color: white;
    padding: 10px 20px;
    border: none;
    border-radius: 4px;
    font-size: 14px;
    font-weight: 600;
    text-decoration: none;
    transition: background-color 0.3s;
}

.btn-create-proposal:hover {
    background-color: var(--accent-orange);
    color: white;
    text-decoration: none;
}

.no-friends {
    text-align: center;
    padding: 60px 20px;
    background: white;
    border-radius: 4px;
    box-shadow: 0 2px 5px rgba(0,0,0,0.05);
}

.no-friends i {
    font-size: 64px;
    color: var(--text-grey);
    margin-bottom: 20px;
}

.no-friends h3 {
    color: var(--text-dark);
    margin-bottom: 10px;
}

.no-friends p {
    color: var(--text-grey);
}

@media (max-width: 768px) {
    .friend-card {
        flex-direction: column;
    }
    
    .friend-avatar {
        margin-bottom: 15px;
    }
    
    .friend-actions {
        margin-top: 15px;
    }
}
</style>
</head>

<body>
    <div class="wrapper">
        <div class="page-header">
            <div class="container">
                <h1 class="page-title">Create New Proposal</h1>
                <p class="page-subtitle">Select a friend to create a new lesson proposal</p>
            </div>
        </div>

        <section class="forum-page">
            <div class="container">
                <?php if ($num_friends == 0): ?>
                    <div class="no-friends">
                        <i class="fas fa-user-friends"></i>
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
                        list($my_langs_array_multidim["$friend_id"], $my_langs_full_name_array_multidim["$friend_id"], 
                        $my_langs_level_array_multidim["$friend_id"], 
                        $my_langs_forshare_array_multidim["$friend_id"], 
                        $my_langs_price_array_multidim["$friend_id"], $my_langs_typeofexchange_array_multidim["$friend_id"], 
                        $my_langs_priceorexchangetext_array_multidim["$friend_id"], $my_langs_level_image_array_multidim["$friend_id"], 
                        $my_langs_2letters_array_multidim["$friend_id"])
                        = lenguas_que_conoce_usuario($friend_id, $link);
                    ?>
                    
                    <div class="friend-card" onclick="window.location.href='../user/u.php?identificador=<?php echo $friend_id; ?>'">
                        <div class="friend-avatar">
                            <img src="<?php echo $path_photo; ?>" alt="<?php echo $friend_name; ?>">
                        </div>
                        
                        <div class="friend-info">
                            <div class="friend-name"><?php echo ucfirst(explode(" ", $friend_name)[0]); ?></div>
                            <div class="friend-location">
                                <i class="fas fa-map-marker-alt"></i>
                                <?php echo $friend_city ? $friend_city : 'Location not specified'; ?>
                            </div>
                            
                            <div class="languages-label">LANGUAGES OFFERED</div>
                            <div class="language-tags">
                                <?php 
                                $friend_langs = $my_langs_full_name_array_multidim["$friend_id"];
                                if (is_array($friend_langs)) {
                                    foreach($friend_langs as $idx => $lang) {
                                        if(!empty($lang)) {
                                            echo "<div class=\"lang-tag\">$lang</div>";
                                        }
                                    }
                                }
                                ?>
                            </div>
                        </div>
                        
                        <div class="friend-actions">
                            <a href="sent-studentcreateproposal.php?tid=<?php echo $friend_id; ?>" 
                               class="btn-create-proposal" 
                               onclick="event.stopPropagation();">
                                Create Proposal
                            </a>
                        </div>
                    </div>
                    
                    <?php endwhile; ?>
                <?php endif; ?>
            </div>
        </section>
    </div>

    <?php require('../templates/footer.php'); ?>
</body>
</html>
