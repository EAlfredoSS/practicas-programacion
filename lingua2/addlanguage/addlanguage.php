<?php
session_start();

$logged_user_id = $_SESSION['orden2017'];

require('../files/bd.php');


$query234 = "SELECT * FROM mentor2009 WHERE orden='" . $logged_user_id . "'"; //seleccionamos todos los campos 

//echo "$query";

$result234 = mysqli_query($link, $query234);
if (!mysqli_num_rows($result234))
    die("User unregistered. <a href=\"http://www.lingua2.com\">Information</a>");
$fila234 = mysqli_fetch_array($result234);



?>

<!DOCTYPE html>
<html>

<head>
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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">

    <title>Add Language | Lingua2</title>
    <link rel="stylesheet" href="./addlanguage.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script type="module" src="./addlanguage.js"></script>
</head>

<body>
    <?php require("../templates/header_simplified.html"); ?>
    <main>
        <div class="main-section">
            <div class="container">
                <div class="main-section-data">
                    <div class="row">
                        <div class="col-lg-3 col-md-4 pd-left-none no-pd"></div>
                        <div class="col-lg-6 col-md-7 no-pd">
                            <div class="main-ws-sec">
                                <div class="top-profiles">
                                    <div class="pf-hd">
                                        <h3>Add language | Lingua2</h3>
                                    </div>
                                    <div class="post-bar" style="margin: 10px 10px 10px 10px; width: 96%">
                                        <div class="post_topbar">
                                            <?php
											
                                            /*var_dump($logged_user_id);
                                            ini_set('display_errors', 1);
                                            error_reporting(E_ALL);*/
											
                                            // Conexión a la base de datos MySQL
                                            

                                            $main_language_id = isset($_GET['lang']) ? $_GET['lang'] : (isset($_POST['lang']) ? $_POST['lang'] : null);
                                            $use_of_language = isset($_GET['use']) ? $_GET['use'] : (isset($_POST['use']) ? $_POST['use'] : null);


                                            // Si los parámetros se reciben por URL correctamente:
                                            // Determinar el paso
                                            $step = 0;

                                            if ($main_language_id && ($use_of_language === 'know' || $use_of_language === 'learn')) {
                                                $query = "SELECT Id, Inverted_name FROM languages_names 
                  WHERE Id NOT IN (SELECT I_Id FROM languages_macrolanguages) 
                  AND Id = ?";

                                                if ($stmt = $link->prepare($query)) {
                                                    $stmt->bind_param("s", $main_language_id);
                                                    $stmt->execute();
                                                    $result = $stmt->get_result();

                                                    if ($result->num_rows > 0) {
                                                        $rowLang = $result->fetch_assoc();
                                                        $main_language_name = $rowLang['Inverted_name'];
                                                        $step = 2;
                                                    } else {
                                                        $step = 0;
                                                    }
                                                    $stmt->close();
                                                }
                                            }

                                            //STEP 2
                                            switch ($step) {
                                                case 0:
                                                default:
                                                    echo "<script>var step = 0;</script>";
                                                    break;
                                                case 1:
                                                    break;
                                                case 2:
                                                    // Mostrar "Nombre (código)" del idioma principal junto a la acción elegida
                                                    $action_text = ($use_of_language === 'know') ? 'know' : 'want to learn';
                                                    $safe_name = isset($main_language_name) ? htmlspecialchars($main_language_name) : '';
                                                    $safe_code = htmlspecialchars($main_language_id);
                                                    echo '<p class="language-summary">I ' . $action_text . ' ' . $safe_name . ' (' . $safe_code . ')</p>';
                                                    echo "<script>var step = 2;</script>";
                                                    break;
                                            }

                                            echo "<script>let useOfLanguage = " . json_encode($use_of_language) . ";</script>";
                                            // echo "<script>console.log(useOfLanguage);</script>";


                                            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                                                $main_language_id = isset($_GET['lang']) ? $_GET['lang'] : (isset($_POST['lang']) ? $_POST['lang'] : null);
                                                //$logged_user_id = 5484;
                                                $level = $_POST['level'];
                                                $price = $_POST['price'] ?? null;
                                                $teachType = $_POST['teachType'] ?? null;
                                                $sublanguages = $_POST['sublanguages'] ?? [];
                                                if ($teachType == 'e' || $teachType == 'tfm') {
                                                    $for_share = 1;
                                                } else {
                                                    $for_share = 0;
                                                }
                                                echo "<script>console.log('" . $use_of_language . "');</script>";

                                                if ($use_of_language == 'know') {
                                                    // STEP6 DELETE
                                                    $delete = $link->prepare("DELETE FROM my_langs WHERE id=? AND (lang_id=? OR sublanguage_of=?)");
                                                    $delete->bind_param("iss", $logged_user_id, $main_language_id, $main_language_id);
                                                    $delete->execute();
                                                    $delete->close();

                                                    $insertSubLang = $link->prepare("INSERT INTO my_langs (id, lang_id, for_share, level_id, lang_price, sublanguage_of) VALUES (?, ?, ?, ?, ?, ?)");
                                                    foreach ($sublanguages as $sublanguage) {
                                                        echo "<script>console.log('" . $use_of_language . "');</script>";
                                                        $insertSubLang->bind_param("isiids", $logged_user_id, $sublanguage, $for_share, $level, $price, $main_language_id);
                                                        $insertSubLang->execute();
                                                    }
                                                    $insertSubLang->close();

                                                    if ($teachType == 'e' || $teachType == 'jc') {
                                                        $insertMainLang = $link->prepare("INSERT INTO my_langs (id, lang_id, for_share, level_id) VALUES (?, ?, ?, ?)");
                                                        $insertMainLang->bind_param("isii", $logged_user_id, $main_language_id, $for_share, $level);
                                                        $insertMainLang->execute();
                                                        $insertMainLang->close();
                                                    } else if ($teachType == 'tfm') {
                                                        $insertMainLangPrice = $link->prepare("INSERT INTO my_langs (id, lang_id, for_share, level_id, lang_price) VALUES (?, ?, ?, ?, ?)");
                                                        $insertMainLangPrice->bind_param("isiid", $logged_user_id, $main_language_id, $for_share, $level, $price);
                                                        $insertMainLangPrice->execute();
                                                        $insertMainLangPrice->close();
                                                    }

                                                    /* // General insert for sharing language
                                                    $insertShareLang = $link->prepare("INSERT INTO my_langs (id, lang_id, for_share, lang_price) VALUES (?, ?, ?, ?)");
                                                    $insertShareLang->bind_param("isii", $logged_user_id, $main_language_id, $for_share, $price);
                                                    $insertShareLang->execute();
                                                    $insertShareLang->close(); */
                                                } else {
                                                    // STEP6 DELETE
                                                    $delete = $link->prepare("DELETE FROM learn_langs WHERE id=? AND (lang_id=? OR sublanguage_of=?)");
                                                    $delete->bind_param("iss", $logged_user_id, $main_language_id, $main_language_id);
                                                    $delete->execute();
                                                    $delete->close();
                                                    //sublanguages
                                                    echo "<script>console.log('use_of_language: " . $use_of_language . "', 'logged_user_id: " . $logged_user_id . "', 'level: " . $level . "', 'main_language_id: " . $main_language_id . "');</script>";
                                                    foreach ($sublanguages as $sublanguage) {
                                                        $insertSubLang = $link->prepare("INSERT INTO learn_langs (id, lang_id, level_id, sublanguage_of) VALUES (?, ?, ?, ?)");
                                                        echo "<script>console.log('" . $sublanguage . "')</script>";
                                                        $insertSubLang->bind_param("isis", $logged_user_id, $sublanguage, $level, $main_language_id);
                                                        $insertSubLang->execute();
                                                        $insertSubLang->close();
                                                    }

                                                    // Insert into learn_langs
                                                    $insertLearnLang = $link->prepare("INSERT INTO learn_langs (id, lang_id, level_id) VALUES (?, ?, ?)");
                                                    $insertLearnLang->bind_param("isi", $logged_user_id, $main_language_id, $level);
                                                    $insertLearnLang->execute();
                                                    $insertLearnLang->close();
                                                }

                                                echo "<script>
                                                // Crear el pop-up personalizado
                                                var div = document.createElement('div');
                                                div.style.position = 'fixed';
                                                div.style.top = '50%';
                                                div.style.left = '50%';
                                                div.style.transform = 'translate(-50%, -50%)';
                                                div.style.backgroundColor = '#4CAF50';
                                                div.style.color = 'white';
                                                div.style.padding = '20px';
                                                div.style.borderRadius = '10px';
                                                div.style.textAlign = 'center';
                                                div.style.zIndex = '9999';
                                                div.innerHTML = '<h3>Language added successfully!</h3>';

                                                // Añadir el div al body
                                                document.body.appendChild(div);

                                                // Redirigir después de mostrar el mensaje por 2 segundos
                                                setTimeout(function() {
                                                    div.style.display = 'none'; // Ocultar el pop-up
                                                    window.location.href = '/user/me.php?nocache=' + new Date().getTime();
                                                }, 1000);
                                            </script>";
                                            }
                                            ?>

                                            <script type="module">
                                                import {
                                                    updateStepView
                                                } from './addlanguage.js';
                                                import {
                                                    loadSublanguages
                                                } from './addlanguage.js';

                                                // Obtener datos de PHP y pasarlos a la función
                                                let step = <?php echo json_encode($step); ?>;
                                                var langId = <?php echo json_encode($main_language_id); ?>;
                                                var useOfLanguage = <?php echo json_encode($use_of_language); ?>;
                                                if (step === 2) {
                                                    loadSublanguages(langId);
                                                }
                                                updateStepView(step);
                                            </script>


                                            <form id="languageForm" method="POST">
                                                <!-- STEP 0 -->
                                                <div id="step0">
                                                    <label for="use">Let us know if you know the language or if you want to practice it:</label><br>
                                                    <select id="use" name="use" style="width: 30%;">
                                                        <option value="" disabled selected>Choose an option</option>
                                                        <option value="know">I know...</option>
                                                        <option value="learn">I want to learn...</option>
                                                    </select>
                                                </div>

                                                <!-- STEP 1 (solo el input, sin consulta PHP) -->
                                                <div id="languageInputContainer" style="display: none;"><br>
                                                    <label for="languageInput">Type the name or code of the
                                                        language (in English):</label>
                                                    <input style="width: 100%;" lowercase list="languageOptions"
                                                        type="text" id="languageInput" name="lang" placeholder="">
                                                    <datalist id="languageOptions"></datalist>
                                                </div>

                                                <!-- STEP 2 (lista de subidiomas dinámica) -->
                                                <div id="sublanguagesContainer">
                                                    <label>Select the sublanguages that you know (if any):</label><br><br>
                                                    <div id="sublanguagesList">

                                                    </div>
                                                </div>

                                                <!-- Nivel de conocimiento -->
                                                <div id="levelSelection" style="display: none;">
                                                    <label for="level">Select the level* of the selected language/sublanguage(s):</label>
                                                    <select required id="level" name="level">
                                                        <option value="1" selected>No knowledge</option>
                                                        <option value="2">A1</option>
                                                        <option value="3">A2</option>
                                                        <option value="4">B1</option>
                                                        <option value="5">B2</option>
                                                        <option value="6">C1</option>
                                                        <option value="7">C2 (Native)</option>
                                                    </select>
													<br>
													<p style="font-size:85%">(*) Check out the <a target="_blank" style="color:#E65F00; "  href="https://europass.europa.eu/en/common-european-framework-reference-language-skills ">information about the level of languages</a> according to Europass.</p>
                                                </div>

                                                <!-- Opción para enseñar -->
                                                <div id="teach" style="display:none;">
                                                    <label for="teachType">How will you use your language in this platform?:</label><br>
                                                    <select id="teachType" name="teachType" style="font-size:85%">
                                                        <option value="" disabled selected>Select an option</option>
                                                        <option value="tfm">Teach it for money or exchange it for other languages</option>
                                                        <option value="e">Exchange it for other languages, but not for money (not recommended)</option>
                                                        <option value="jc">Use it only to communicate, but not for exchange (not recommended)</option>
                                                    </select>
                                                </div>

                                                <!-- Precio (solo para quienes enseñan) -->
                                                <div id="priceInputContainer" style="display: none;">
                                                    <label for="price">Price per hour (&euro;, EURO):</label>
                                                    <input type="number" step="0.01" id="price" name="price" min="1" placeholder="8.5">
                                                </div>
                                                <br>
                                                <div class="button-container">
                                                    <button type="button" id="prevStep">Previous Step</button>
                                                    <button type="button" id="nextStep" disabled>Next Step</button><br><br>
                                                    <button type="submit" id="submit" style="display: none;">Submit</button> 
                                                </div>
                                            </form>
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
    <?php require("../templates/footer.php"); ?>
</body>

</html>