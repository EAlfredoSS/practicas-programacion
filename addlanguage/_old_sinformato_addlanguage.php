<?php
    session_start();

    $logged_user_id = $_SESSION['orden2017'];
	
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Language | Lingua2</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script type="module" src="./_old_addlanguage.js"></script>
</head>

<body>

<?php
    var_dump($logged_user_id);
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
    // Conexión a la base de datos MySQL
    require('../files/bd.php');

    $main_language_id = isset($_GET['lang']) ? $_GET['lang'] : (isset($_POST['lang']) ? $_POST['lang'] : null);
    $use_of_language =  isset($_GET['use']) ? $_GET['use'] : (isset($_POST['use']) ? $_POST['use'] : null);


    // Si los parámetros se reciben por URL correctamente:
    // Determinar el paso
    $step = 0;

    if ($main_language_id && ($use_of_language  === 'know' || $use_of_language === 'learn')) {
        $query = "SELECT Id, Inverted_name FROM languages_names 
                  WHERE Id NOT IN (SELECT I_Id FROM languages_macrolanguages) 
                  AND Id = ?";

        if ($stmt = $link->prepare($query)) {
            $stmt->bind_param("s", $main_language_id);
            $stmt->execute();
            $result = $stmt->get_result();

            $step = ($result->num_rows > 0) ? 2 : 0;
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
            echo "Selected language: " . $main_language_id . "<br>Selected use: " . $use_of_language . "<br></p>";
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
                $insertSubLang->bind_param("isiiis", $logged_user_id, $sublanguage, $for_share, $level, $price, $main_language_id);
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
                $insertMainLangPrice->bind_param("isiii", $logged_user_id, $main_language_id, $for_share, $level, $price);
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
                echo  "<script>console.log('".$sublanguage."')</script>";
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

        echo "<script>alert('Language added successfully!'); window.location.href = './_old_addlanguage.php';</script>";
    }
    ?>

    <script type="module">
        import {
            updateStepView
        } from './_old_addlanguage.js';
        import {
            loadSublanguages
        } from './_old_addlanguage.js';

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
            <label for="use">Select an option:</label>
            <select id="use" name="use">
                <option value="know">I know...</option>
                <option value="learn">I want to learn...</option>
            </select>
        </div>

        <!-- STEP 1 (solo el input, sin consulta PHP) -->
        <div id="languageInputContainer" style="display: none;">
            <label for="languageInput">Type the name or code of the language:</label>
            <input style="width: 25%" lowercase list="languageOptions" type="text" id="languageInput" name="lang" placeholder="">
            <datalist id="languageOptions"></datalist>
        </div>

        <!-- STEP 2 (lista de subidiomas dinámica) -->
        <div id="sublanguagesContainer">
            <label>Select sublanguages:</label>
            <div id="sublanguagesList">

            </div>
        </div>

        <!-- Nivel de conocimiento -->
        <div id="levelSelection" style="display: none;">
            <label for="level">Select your level:</label>
            <select required id="level" name="level">
                <option value="1" selected>No knowledge</option>
                <option value="2">A1</option>
                <option value="3">A2</option>
                <option value="4">B1</option>
                <option value="5">B2</option>
                <option value="6">C1</option>
                <option value="7">C2</option>
            </select>
        </div>

        <!-- Opción para enseñar -->
        <div id="teach" style="display:none;">
            <label for="teachType">Use for this language:</label>
            <select id="teachType" name="teachType">
                <option value="" disabled selected>Select an option</option>
                <option value="tfm">Teach for money</option>
                <option value="e">Exchange</option>
                <option value="jc">Just communicate</option>
            </select>
        </div>

        <!-- Precio (solo para quienes enseñan) -->
        <div id="priceInputContainer" style="display: none;">
            <label for="price">Price per hour (€):</label>
            <input type="number" id="price" name="price" min="0">
        </div>
        <button type="button" id="prevStep">Previous Step</button>
        <button type="button" id="nextStep">Next Step</button><br><br>
        <button type="submit" id="submit" style="display: none;">Submit</button>
    </form>

</body>
</html>