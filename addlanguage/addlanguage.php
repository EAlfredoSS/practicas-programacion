<?php
session_start();

if (empty($_SESSION['orden2017'])) {
    header('Location: /index.php');
    exit;
}

$logged_user_id = (int) $_SESSION['orden2017'];

require('../files/bd.php');

$stmt_user = $link->prepare("SELECT orden FROM mentor2009 WHERE orden = ?");
$stmt_user->bind_param("i", $logged_user_id);
$stmt_user->execute();
$result_user = $stmt_user->get_result();
if (!$result_user->num_rows) {
    $stmt_user->close();
    die('User unregistered. <a href="http://www.lingua2.com">Information</a>');
}
$stmt_user->close();

$main_language_id = isset($_GET['lang']) ? trim($_GET['lang'])
                  : (isset($_POST['lang']) ? trim($_POST['lang']) : null);
$use_of_language  = isset($_GET['use'])  ? trim($_GET['use'])
                  : (isset($_POST['use'])  ? trim($_POST['use'])  : null);

if (!in_array($use_of_language, ['know', 'learn'], true)) {
    $use_of_language = null;
}

$step               = 0;
$main_language_name = '';

if ($main_language_id && $use_of_language) {
    $stmt_lang = $link->prepare(
        "SELECT Id, Inverted_name FROM languages_names
         WHERE Id NOT IN (SELECT I_Id FROM languages_macrolanguages)
         AND Id = ?"
    );
    if ($stmt_lang) {
        $stmt_lang->bind_param("s", $main_language_id);
        $stmt_lang->execute();
        $result_lang = $stmt_lang->get_result();
        if ($result_lang->num_rows > 0) {
            $rowLang            = $result_lang->fetch_assoc();
            $main_language_name = $rowLang['Inverted_name'];
            $step               = 2;
        }
        $stmt_lang->close();
    }
}

$form_success = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $step === 2) {

    $level        = isset($_POST['level'])     ? (int)   $_POST['level']    : 1;
    $price        = isset($_POST['price'])     ? (float) $_POST['price']    : null;
    $teachType    = isset($_POST['teachType']) ? trim($_POST['teachType'])  : null;
    $sublanguages = (isset($_POST['sublanguages']) && is_array($_POST['sublanguages']))
                    ? $_POST['sublanguages'] : [];

    if (!in_array($teachType, ['tfm', 'e', 'jc'], true)) {
        $teachType = null;
    }

    $for_share = ($teachType === 'e' || $teachType === 'tfm') ? 1 : 0;

    if ($use_of_language === 'know') {

        $del = $link->prepare("DELETE FROM my_langs WHERE id = ? AND (lang_id = ? OR sublanguage_of = ?)");
        $del->bind_param("iss", $logged_user_id, $main_language_id, $main_language_id);
        $del->execute();
        $del->close();

        if (!empty($sublanguages)) {
            $ins_sub = $link->prepare(
                "INSERT INTO my_langs (id, lang_id, for_share, level_id, lang_price, sublanguage_of)
                 VALUES (?, ?, ?, ?, ?, ?)"
            );
            foreach ($sublanguages as $sub) {
                $sub = trim($sub);
                if ($sub === '') continue;
                $ins_sub->bind_param("isiids", $logged_user_id, $sub, $for_share, $level, $price, $main_language_id);
                $ins_sub->execute();
            }
            $ins_sub->close();
        }

        if ($teachType === 'e' || $teachType === 'jc') {
            $ins = $link->prepare("INSERT INTO my_langs (id, lang_id, for_share, level_id) VALUES (?, ?, ?, ?)");
            $ins->bind_param("isii", $logged_user_id, $main_language_id, $for_share, $level);
            $ins->execute();
            $ins->close();
        } elseif ($teachType === 'tfm') {
            $ins = $link->prepare("INSERT INTO my_langs (id, lang_id, for_share, level_id, lang_price) VALUES (?, ?, ?, ?, ?)");
            $ins->bind_param("isiid", $logged_user_id, $main_language_id, $for_share, $level, $price);
            $ins->execute();
            $ins->close();
        }

    } else {

        $del = $link->prepare("DELETE FROM learn_langs WHERE id = ? AND (lang_id = ? OR sublanguage_of = ?)");
        $del->bind_param("iss", $logged_user_id, $main_language_id, $main_language_id);
        $del->execute();
        $del->close();

        foreach ($sublanguages as $sub) {
            $sub = trim($sub);
            if ($sub === '') continue;
            $ins = $link->prepare("INSERT INTO learn_langs (id, lang_id, level_id, sublanguage_of) VALUES (?, ?, ?, ?)");
            $ins->bind_param("isis", $logged_user_id, $sub, $level, $main_language_id);
            $ins->execute();
            $ins->close();
        }

        $ins = $link->prepare("INSERT INTO learn_langs (id, lang_id, level_id) VALUES (?, ?, ?)");
        $ins->bind_param("isi", $logged_user_id, $main_language_id, $level);
        $ins->execute();
        $ins->close();
    }

    $form_success = true;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-139626327-1"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){ dataLayer.push(arguments); }
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

    <style>
        *, *::before, *::after { box-sizing: border-box; }

        body {
            margin: 0;
            background-color: #f4f7f6;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            color: #2c3e50;
        }

        .al-page { min-height: 100vh; display: flex; flex-direction: column; }

        .al-content {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 1rem;
            width: 100%;
        }

        .language-card {
            background: #ffffff;
            border-radius: 24px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.03), 0 2px 4px rgba(0,0,0,0.05);
            padding: 2rem;
            width: 100%;
            max-width: 560px;
            margin: 0 auto;
        }

        .al-title {
            font-size: 1.75rem;
            font-weight: 700;
            color: #2c3e50;
            margin: 0 0 1.25rem 0;
            padding: 0;
            line-height: 1.2;
            letter-spacing: -0.2px;
        }

        .al-pill {
            display: inline-block;
            background: #fff4ee;
            color: #d35400;
            border: 1px solid #f5c4aa;
            border-radius: 40px;
            padding: 0.3rem 1rem;
            font-size: 0.8rem;
            font-weight: 600;
            margin-bottom: 1.5rem;
        }

        .al-form-block { margin-bottom: 1.5rem; }

        .al-form-block label {
            display: block;
            font-size: 0.9rem;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 0.5rem;
        }

        /* Inputs normales */
        .al-form-block input[type="text"],
        .al-form-block input[type="number"] {
            display: block;
            width: 100%;
            padding: 0.7rem 0.9rem;
            font-size: 0.9rem;
            color: #2c3e50;
            background-color: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            transition: border-color 0.2s, box-shadow 0.2s;
            font-family: inherit;
        }

        .al-form-block input:focus {
            outline: none;
            border-color: #d35400;
            box-shadow: 0 0 0 3px rgba(211, 84, 0, 0.08);
        }

        /* ── CUSTOM DROPDOWN ─────────────────────────────────────── */
        .al-select-wrapper {
            position: relative;
            user-select: none;
        }

        /* El "botón" visible que reemplaza al <select> */
        .al-select-trigger {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0.7rem 0.9rem;
            font-size: 0.9rem;
            color: #2c3e50;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            cursor: pointer;
            transition: border-color 0.2s, box-shadow 0.2s;
            font-family: inherit;
        }

        .al-select-trigger:hover {
            border-color: #d35400;
        }

        .al-select-trigger.open {
            border-color: #d35400;
            box-shadow: 0 0 0 3px rgba(211, 84, 0, 0.08);
            border-radius: 12px 12px 0 0;
        }

        /* Flecha SVG */
        .al-select-arrow {
            width: 16px;
            height: 16px;
            flex-shrink: 0;
            transition: transform 0.2s;
            color: #95a5a6;
        }

        .al-select-trigger.open .al-select-arrow {
            transform: rotate(180deg);
            color: #d35400;
        }

        /* Color del placeholder */
        .al-select-trigger.placeholder {
            color: #9ca3af;
        }

        /* Lista desplegable */
        .al-select-list {
            display: none;
            position: absolute;
            top: 100%;
            left: 0; right: 0;
            background: #ffffff;
            border: 1px solid #d35400;
            border-top: none;
            border-radius: 0 0 12px 12px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.08);
            z-index: 200;
            max-height: 220px;
            overflow-y: auto;
            padding: 4px 0;
        }

        .al-select-list.open { display: block; }

        .al-select-list li {
            list-style: none;
            padding: 0.65rem 0.9rem;
            font-size: 0.9rem;
            color: #2c3e50;
            cursor: pointer;
            transition: background 0.15s, color 0.15s;
        }

        .al-select-list li:hover,
        .al-select-list li.selected {
            background: #d35400;
            color: #ffffff;
        }

        /* Scrollbar naranja sutil en la lista */
        .al-select-list::-webkit-scrollbar { width: 4px; }
        .al-select-list::-webkit-scrollbar-thumb { background: #e8a87c; border-radius: 4px; }

        /* ── FIN CUSTOM DROPDOWN ─────────────────────────────────── */

        .al-hint {
            font-size: 0.75rem;
            color: #7f8c8d;
            margin-top: 0.5rem;
        }
        .al-hint a { color: #d35400; text-decoration: none; }
        .al-hint a:hover { text-decoration: underline; }

        .al-buttons {
            display: flex;
            gap: 0.75rem;
            flex-wrap: wrap;
            margin-top: 1rem;
        }

        .al-buttons button {
            padding: 0.7rem 1.5rem;
            font-size: 0.9rem;
            font-weight: 600;
            border: none;
            border-radius: 40px;
            cursor: pointer;
            transition: background 0.2s, transform 0.1s;
            font-family: inherit;
        }

        #prevStep {
            background-color: #eef2f5;
            color: #3a546d;
            border: 1px solid #e2e8f0;
        }
        #prevStep:hover { background-color: #e2e8f0; }

        #nextStep {
            background-color: #d35400;
            color: #ffffff;
        }
        #nextStep:hover:not(:disabled) {
            background-color: #b84600;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(211, 84, 0, 0.2);
        }
        #nextStep:disabled {
            background-color: #e2b48c;
            cursor: not-allowed;
        }

        #submit { background-color: #27ae60; color: white; }
        #submit:hover {
            background-color: #219652;
            transform: translateY(-1px);
        }

        /* overlay éxito */
        #success-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.4);
            z-index: 9999;
            justify-content: center;
            align-items: center;
        }
        #success-overlay.visible { display: flex; }
        #success-box {
            background: #fff;
            border-radius: 28px;
            padding: 2rem 2.5rem;
            text-align: center;
            box-shadow: 0 20px 35px rgba(0,0,0,0.2);
            animation: popIn 0.25s ease;
        }
        @keyframes popIn {
            from { transform: scale(0.92); opacity: 0; }
            to   { transform: scale(1);   opacity: 1; }
        }
        .success-check {
            width: 54px; height: 54px;
            background: #27ae60;
            border-radius: 50%;
            color: white; font-size: 28px;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 1rem;
        }
        #success-box h3 { font-size: 1.2rem; font-weight: 700; margin: 0 0 0.3rem; color: #2c3e50; }
        #success-box p  { font-size: 0.85rem; color: #5a6e7c; margin: 0; }

        @media (max-width: 580px) {
            .language-card { padding: 1.5rem; }
            .al-title { font-size: 1.5rem; }
            .al-buttons button { padding: 0.6rem 1.2rem; }
        }
    </style>
</head>

<body>
<div class="al-page">

    <?php require("../templates/header_simplified.html"); ?>

    <main>
        <div class="al-content">
            <div class="language-card">

                <h3 class="al-title">Add language | Lingua2</h3>

                <?php
                if ($step === 2) {
                    $action_text = ($use_of_language === 'know') ? 'know' : 'want to learn';
                    $safe_name   = htmlspecialchars($main_language_name);
                    $safe_code   = htmlspecialchars($main_language_id);
                    echo '<p class="al-pill">I ' . $action_text . ' <strong>' . $safe_name . '</strong> (' . $safe_code . ')</p>';
                }
                ?>

                <script>
                    var step          = <?= json_encode($step) ?>;
                    var useOfLanguage = <?= json_encode($use_of_language) ?>;
                </script>

                <form id="languageForm" method="POST">

                    <!-- Paso 0: elegir "know / learn" — CUSTOM DROPDOWN -->
                    <div id="step0" class="al-form-block">
                        <label>Let us know if you know the language or if you want to practice it:</label>

                        <!-- Hidden input que recibe el valor real para el POST -->
                        <input type="hidden" id="use" name="use" value="">

                        <div class="al-select-wrapper">
                            <div class="al-select-trigger placeholder" data-target="use" tabindex="0">
                                <span class="al-select-label">Choose an option</span>
                                <svg class="al-select-arrow" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <ul class="al-select-list" data-target="use">
                                <li data-value="know">I know...</li>
                                <li data-value="learn">I want to learn...</li>
                            </ul>
                        </div>
                    </div>

                    <!-- Paso 1: buscador de idioma -->
                    <div id="languageInputContainer" class="al-form-block" style="display:none;">
                        <label for="languageInput">Type the name or code of the language (in English):</label>
                        <input list="languageOptions" type="text" id="languageInput" name="lang" placeholder="">
                        <datalist id="languageOptions"></datalist>
                    </div>

                    <!-- Paso 2: sublenguajes -->
                    <div id="sublanguagesContainer" class="al-form-block">
                        <label>Select the sublanguages that you know (if any):</label>
                        <div id="sublanguagesList"></div>
                    </div>

                    <!-- Nivel de conocimiento — CUSTOM DROPDOWN -->
                    <div id="levelSelection" class="al-form-block" style="display:none;">
                        <label>Select the level* of the selected language/sublanguage(s):</label>

                        <input type="hidden" id="level" name="level" value="1">

                        <div class="al-select-wrapper">
                            <div class="al-select-trigger" data-target="level" tabindex="0">
                                <span class="al-select-label">No knowledge</span>
                                <svg class="al-select-arrow" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <ul class="al-select-list" data-target="level">
                                <li data-value="1" class="selected">No knowledge</li>
                                <li data-value="2">A1</li>
                                <li data-value="3">A2</li>
                                <li data-value="4">B1</li>
                                <li data-value="5">B2</li>
                                <li data-value="6">C1</li>
                                <li data-value="7">C2 (Native)</li>
                            </ul>
                        </div>

                        <p class="al-hint">(*) Check out the <a href="https://europass.europa.eu/en/common-european-framework-reference-language-skills" target="_blank">information about the level of languages</a> according to Europass.</p>
                    </div>

                    <!-- Opción para enseñar — CUSTOM DROPDOWN -->
                    <div id="teach" class="al-form-block" style="display:none;">
                        <label>How will you use your language in this platform?:</label>

                        <input type="hidden" id="teachType" name="teachType" value="">

                        <div class="al-select-wrapper">
                            <div class="al-select-trigger placeholder" data-target="teachType" tabindex="0">
                                <span class="al-select-label">Select an option</span>
                                <svg class="al-select-arrow" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <ul class="al-select-list" data-target="teachType">
                                <li data-value="tfm">Teach it for money or exchange it for other languages</li>
                                <li data-value="e">Exchange it for other languages, but not for money (not recommended)</li>
                                <li data-value="jc">Use it only to communicate, but not for exchange (not recommended)</li>
                            </ul>
                        </div>
                    </div>

                    <!-- Precio -->
                    <div id="priceInputContainer" class="al-form-block" style="display:none;">
                        <label for="price">Price per hour (&euro;, EURO):</label>
                        <input type="number" step="0.01" id="price" name="price" min="1" placeholder="8.5">
                    </div>

                    <div class="al-buttons">
                        <button type="button" id="prevStep">Previous Step</button>
                        <button type="button" id="nextStep" disabled>Next Step</button>
                        <button type="submit" id="submit" style="display:none;">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <?php require("../templates/footer.php"); ?>

</div>

<!-- ── CUSTOM DROPDOWN SCRIPT ─────────────────────────────────────────────── -->
<script>
(function () {
    function initDropdowns() {
        document.querySelectorAll('.al-select-wrapper').forEach(function (wrapper) {
            var trigger = wrapper.querySelector('.al-select-trigger');
            var list    = wrapper.querySelector('.al-select-list');
            var targetId = trigger.getAttribute('data-target');
            var hidden   = document.getElementById(targetId);

            // Abrir / cerrar
            trigger.addEventListener('click', function (e) {
                e.stopPropagation();
                var isOpen = list.classList.contains('open');
                // Cerrar todos los demás
                document.querySelectorAll('.al-select-list.open').forEach(function (l) {
                    l.classList.remove('open');
                    l.previousElementSibling.classList.remove('open');
                });
                if (!isOpen) {
                    list.classList.add('open');
                    trigger.classList.add('open');
                }
            });

            // Seleccionar opción
            list.querySelectorAll('li').forEach(function (li) {
                li.addEventListener('click', function () {
                    var val   = this.getAttribute('data-value');
                    var label = this.textContent;

                    // Actualizar hidden input (lo que llega al PHP por POST)
                    hidden.value = val;

                    // Actualizar texto visible del trigger
                    trigger.querySelector('.al-select-label').textContent = label;
                    trigger.classList.remove('placeholder');

                    // Marcar seleccionado
                    list.querySelectorAll('li').forEach(function (l) { l.classList.remove('selected'); });
                    this.classList.add('selected');

                    // Cerrar
                    list.classList.remove('open');
                    trigger.classList.remove('open');

                    // Disparar evento 'change' en el hidden para que addlanguage.js lo detecte si escucha
                    hidden.dispatchEvent(new Event('change', { bubbles: true }));
                });
            });
        });
    }

    // Cerrar al hacer clic fuera
    document.addEventListener('click', function () {
        document.querySelectorAll('.al-select-list.open').forEach(function (l) {
            l.classList.remove('open');
            l.previousElementSibling.classList.remove('open');
        });
    });

    // Inicializar
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initDropdowns);
    } else {
        initDropdowns();
    }
})();
</script>

<script type="module">
    import { updateStepView } from './addlanguage.js';
    import { loadSublanguages } from './addlanguage.js';

    let step          = <?= json_encode($step) ?>;
    var langId        = <?= json_encode($main_language_id) ?>;
    var useOfLanguage = <?= json_encode($use_of_language) ?>;

    if (step === 2) {
        loadSublanguages(langId);
    }
    updateStepView(step);
</script>

<?php if ($form_success): ?>
<div id="success-overlay" class="visible">
    <div id="success-box">
        <div class="success-check">✓</div>
        <h3>Language added successfully!</h3>
        <p>Redirecting you to your profile&hellip;</p>
    </div>
</div>
<script>
    setTimeout(function () {
        window.location.href = '/user/me.php?nocache=' + Date.now();
    }, 1400);
</script>
<?php endif; ?>

</body>
</html>