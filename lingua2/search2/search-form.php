<?php

require_once('jQueryWidgets.php');
require_once('idiomasequivalencias.php');
require('../files/bd.php');

$organization_id_array = array();
$organization_name_array = array();

$query_organizations = "SELECT * FROM organizations WHERE 1 ORDER BY organization_name DESC";
$result_organizations = mysqli_query($link, $query_organizations);

//if (!mysqli_num_rows($result_organizations))

$number_of_organizations = mysqli_num_rows($result_organizations);

for ($iiii = 0; $iiii < $number_of_organizations; $iiii++) {
    $fila_organizations = mysqli_fetch_array($result_organizations);
    array_push($organization_id_array, $fila_organizations['organization_id']);
    array_push($organization_name_array, $fila_organizations['organization_name']);
}

//print_r($organization_id_array);
//print_r($organization_name_array);

// Helper function to show full language name in input, even if URL has code (e.g. 'eng' -> 'English')
function get_lang_name_by_code($val, $idiomas_equiv) {
    if (empty($val)) return '';
    // If it's already long (probably a name), return as is
    if (strlen($val) > 3 && $val !== 'val') return $val;
    
    $code = strtolower($val);
    // Direct lookup
    if (isset($idiomas_equiv[$code])) {
        return $idiomas_equiv[$code];
    }
    // Case insensitive search just in case
    foreach ($idiomas_equiv as $k => $v) {
        if (strtolower($k) === $code) return $v;
    }
    // Fallback: return original
    return $val;
}

// Obtener el radio de búsqueda de la URL o usar el valor predeterminado (alineado con backend: 20km)
$current_distance = isset($_GET['distance']) ? (int)$_GET['distance'] : 20;

?>

<header>
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster/dist/MarkerCluster.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster/dist/MarkerCluster.Default.css" />
    <link rel="stylesheet" href="style-search.css">
    <script src="https://unpkg.com/leaflet.markercluster/dist/leaflet.markercluster.js"></script>
</header>
<style>
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.5);
            opacity: 0;
            transform: scale(0.7);
            transition: opacity 0.4s ease, transform 0.4s cubic-bezier(.4,2,.3,1);
        }
        .modal-content {
            background-color: #fff;
            margin: 2% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            border-radius: 10px;
            position: relative;
        }
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }
        /* Icono-redondo-mapa */
        .leaflet-marker-icon.rounded-marker {
            border-radius: 50% !important;  
            overflow: hidden;             
        }
        /* Estilo para el botón de expandir mapa */
        .expand-map-button {
            background-color: white;
            border: 2px solid rgba(0,0,0,0.2);
            border-radius: 4px;
            padding: 5px 10px;
            cursor: pointer;
            font-weight: bold;
            box-shadow: 0 1px 5px rgba(0,0,0,0.65);
        }
        .expand-map-button:hover {
            background-color: #e65f00;
        }
        /* Asegurarse de que el mapa tenga altura */
        #osm-map {
            height: 220px !important;
            width: 100%;
            margin-bottom: 10px;
            border: 1px solid #ccc;
        }
        .close-map-button {
            background-color: #e65f00 !important;
            color: white !important;
            border-radius: 4px;
            transition: all 0.3s ease;
        }
        .close-map-button:hover {
            background-color: #ff7f2a !important;
            box-shadow: 0 0 5px rgba(0,0,0,0.3);
        }
        .modal-content {
            position: relative;
        }
        .close {
            position: absolute;
            right: 25px;
            top: 10px;
            color: #000;
            font-size: 35px;
            font-weight: bold;
            cursor: pointer;
            z-index: 1000;
        }
        .close:hover {
            color: #e65f00;
        }
        input[name="learns"], input[name="learns"], select[name="learns"], select[name="learns"],
        .ui-slider, .ui-slider-horizontal, [id^="slider"], [id^="slider1"], [id^="slider2"] {
            width: 100% !important;
            min-width: 0;
            max-width: 100%;
            box-sizing: border-box;
            margin-left: 0 !important;
            margin-right: 0 !important;
            padding-left: 0 !important;
            padding-right: 0 !important;
        }

        .slider-labels {
            width: 100%;
            margin-left: 0;
            margin-right: 0;
        }

        /* Forzar que los labels y divs que contienen sliders ocupen todo el ancho */
        label > #slider1,
        label > #slider2,
        label > [id^="slider"],
        label > .ui-slider,
        label > .ui-slider-horizontal {
            display: block;
            width: 100% !important;
            margin: 0 !important;
            padding: 0 !important;
            box-sizing: border-box;
        }

        /* Si el label es el problema, fuerza el label a ocupar el 100% */
        label {
            width: 100%;
            display: block;
        }

        /* Refuerza el ancho de los sliders */
        #slider1, #slider2, [id^="slider"], .ui-slider, .ui-slider-horizontal {
            width: 100% !important;
            min-width: 0;
            max-width: 100%;
            margin: 0 !important;
            padding: 0 !important;
            box-sizing: border-box;
        }
        /* Estilos específicos para inputs de texto para que se vean 'chulos' y redondeados */
        #learn-input, #student-learn-input, #student-teach-input {
            padding: 10px !important;
            border-radius: 8px !important;
            border: 1px solid #ddd !important;
            background-color: white !important;
            margin-bottom: 10px !important;
            box-shadow: none;
            transition: all 0.2s ease;
        }
        #learn-input:focus, #student-learn-input:focus, #student-teach-input:focus {
            border-color: #ff5722 !important;
            box-shadow: 0 0 0 2px rgba(255, 87, 34, 0.1) !important;
            outline: none !important;
        }
        /* Forzar fondo transparente para eliminar el rosa */
        .student-dialog, .teacher-dialog, fieldset {
            background-color: transparent !important;
        }
        /* Custom Tooltip Styles */
        .tooltip-container {
            position: relative;
            display: inline-block;
            cursor: help;
            margin-left: 5px;
            vertical-align: middle;
        }

        .tooltip-container .tooltip-text {
            visibility: hidden;
            width: 220px;
            background-color: black;
            color: #fff;
            text-align: center;
            padding: 8px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: normal;
            line-height: 1.4;

            /* Position the tooltip text - above the icon */
            position: absolute;
            z-index: 1001; /* High z-index to sit on top of other elements */
            bottom: 135%;
            left: 50%;
            margin-left: -110px; /* Center the tooltip (half of width) */

            /* Fade in effect */
            opacity: 0;
            transition: opacity 0.3s;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }

        /* Tooltip arrow */
        .tooltip-container .tooltip-text::after {
            content: "";
            position: absolute;
            top: 100%;
            left: 50%;
            margin-left: -5px;
            border-width: 5px;
            border-style: solid;
            border-color: black transparent transparent transparent;
        }

        /* Show the tooltip text when you mouse over the tooltip container */
        .tooltip-container:hover .tooltip-text {
            visibility: visible;
            opacity: 1;
        }
        
        /* FIX: Search Button Hover - White Text & No Jumping */
        input[type="submit"].ui-button:hover {
            color: #ffffff !important;
            transform: none !important; /* Prevent layout shift/growing */
            background-color: #ff7f2a; /* Slightly lighter orange for feedback */
            box-shadow: 0 4px 8px rgba(0,0,0,0.2); /* Shadow instead of size change */
        }
        
        /* FIX: Clear Search - Better Aesthetics */
        .clear-search-btn {
            display: inline-block;
            padding: 4px 12px;
            background-color: #f1f1f1;
            color: #666;
            text-decoration: none !important;
            border-radius: 15px;
            font-size: 11px;
            border: 1px solid #ddd;
            transition: all 0.2s;
        }
        .clear-search-btn:hover {
            background-color: #ddd;
            color: #333;
        }

        /* FIX: Dropdowns - Prevent Cutoff */
        select {
            white-space: nowrap;
            overflow: hidden; 
            text-overflow: ellipsis;
            padding-right: 20px; /* Space for arrow */
        }
        /* Fix for options in chrome/firefox sometimes being cut */
        option {
            padding: 4px;
        }
    </style>
<form id='search-form' method="get" action="search2/index_paginated.php">
<h1>Search for your partner</h1>
<div style="text-align:right; margin-bottom:5px;">
    <a href="index_paginated.php" class="clear-search-btn">Clear search</a>
</div>
<input class="ui-button ui-widget ui-corner-all" style="width:100%;" type="submit" value="Search" />
<input type="hidden" name="min_level" id="min_level" value="">
<input type="hidden" name="max_level" id="max_level" value="">
<!-- Nuevos: niveles específicos para aprende y enseña -->
<input type="hidden" name="learns_min_level" id="learns_min_level" value="">
<input type="hidden" name="learns_max_level" id="learns_max_level" value="">
<input type="hidden" name="teaches_min_level" id="teaches_min_level" value="">
<input type="hidden" name="teaches_max_level" id="teaches_max_level" value="">
<hr>
    <fieldset>
        <legend>Order results by...</legend>
        <div>
            <select name='orderresultsby'>
                <!--<option value='auto'>Auto</option>-->
                <option value='distance' <?php if(isset($_GET['orderresultsby']) && $_GET['orderresultsby']=='distance') echo 'selected'; ?>>Distance from me</option>
                <option value='lastlogin' <?php if(!isset($_GET['orderresultsby']) || $_GET['orderresultsby']=='' || $_GET['orderresultsby']=='lastlogin') echo 'selected'; ?>>Last login</option>
                <option value='newest' <?php if(isset($_GET['orderresultsby']) && $_GET['orderresultsby']=='newest') echo 'selected'; ?>>Newest users</option>
                <option value='more_evals' <?php if(isset($_GET['orderresultsby']) && $_GET['orderresultsby']=='more_evals') echo 'selected'; ?>>More evaluations</option>
                <option value='less_evals' <?php if(isset($_GET['orderresultsby']) && $_GET['orderresultsby']=='less_evals') echo 'selected'; ?>>Less evaluations</option>
                <option value='best_evals' <?php if(isset($_GET['orderresultsby']) && $_GET['orderresultsby']=='best_evals') echo 'selected'; ?>>Best evaluations</option>
                <option value='price_asc' <?php if(isset($_GET['orderresultsby']) && $_GET['orderresultsby']=='price_asc') echo 'selected'; ?>>Price (only teachers) asc.</option>
                <option value='price_desc' <?php if(isset($_GET['orderresultsby']) && $_GET['orderresultsby']=='price_desc') echo 'selected'; ?>>Price (only teachers) desc.</option>
            </select>
        </div>
        <hr>
    </fieldset>

    <!--<div class='ctrlgrpv'> -->
    <fieldset id='language-partner'>
        <legend for='language-partner'>I'm looking for:</legend>
        <div>
            <label class="container">Professional teachers
                <input type='radio' name='partner' id='radio-teachers' value='teacher' <?php if(!isset($_GET['partner']) || $_GET['partner']=='teacher') echo "checked='checked'"; ?> />
                <span class="checkmark"></span>
            </label>
            <label class="container">Students
                <input type='radio' name='partner' id='radio-students' value='student' <?php if(isset($_GET['partner']) && $_GET['partner']=='student') echo "checked='checked'"; ?> />
                <span class="checkmark"></span>
            </label>
        </div>
        <hr>
    </fieldset>
<!-- Fieldsets Enseñar o aprender Idiomas -->
<fieldset style='display:none' id="lang-learn">
    <legend>Language I want to learn:</legend>
    <div>
        <input type="text" name="learns" id="learn-input" list="idiomas-list" placeholder="Type to search..." value="<?php echo isset($_GET['learns']) ? htmlspecialchars(get_lang_name_by_code($_GET['learns'], $idiomas_equiv)) : ''; ?>">
        <datalist id="idiomas-list">
            <?php listIterator($idiomas_equiv, 'asOptionDatalist'); ?>
        </datalist>
    </div>
    
    <hr>
    <!-- Checkbox para activar el filtro por precio (solo profesores) -->
    <div class='teacher-dialog' id="price-filter-container" style="display:none;">
        <label>
            <input type="checkbox" id="price-filter-checkbox" name="use_price_filter" <?php if(isset($_GET['use_price_filter']) && $_GET['use_price_filter']=='on') echo 'checked="checked"'; ?>> Do you want to filter by price?
            <span class="checkmark"></span>
        </label>
    </div>

    <div class='teacher-dialog' id="price-bar-container" style="display:none;">
        <div>
        <div>
            <label>Price range (€/h)
                <?php 
                $defPriceMin = 8;
                $defPriceMax = 20;
                // Check for 'min_price' (new) or 'price_min' (legacy)
                if (isset($_GET['min_price'])) $defPriceMin = (int)$_GET['min_price'];
                elseif (isset($_GET['price_min'])) $defPriceMin = (int)$_GET['price_min'];

                // If max_price is 0 (from empty 30+) or >= 30, set slider visual to 30.
                // If it is actual number (e.g. 20), use it.
                $maxPriceParam = 0;
                if (isset($_GET['max_price'])) $maxPriceParam = (int)$_GET['max_price'];
                elseif (isset($_GET['price_max'])) $maxPriceParam = (int)$_GET['price_max'];
                
                if ($maxPriceParam > 0 && $maxPriceParam < 30) {
                     $defPriceMax = $maxPriceParam;
                } else {
                     // 0 or >=30 treated as Max (30) for slider visual
                     $defPriceMax = 30;
                }

                // Generates inputs 'min_price' and 'max_price'
                rangeSlider('slider', 'price', $defPriceMin, $defPriceMax); 
                ?>
            </label>
        </div>
        </div>
    </div>
    <hr>
</fieldset>

</script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const input = document.getElementById('learn-input');
    const datalist = document.getElementById('idiomas-list');
    const priceContainer = document.getElementById('price-filter-container');

    function refreshPriceUI() {
        if (!input || !datalist || !priceContainer) return;
        const inputValue = input.value.toLowerCase().trim();
        
        // Special case for Catalan/Valencian
        if (inputValue === 'catalan' || inputValue === 'valenciano' || inputValue === 'valencian') {
            priceContainer.style.display = 'block';
            return;
        }

        const options = Array.from(datalist.options).map(opt => opt.value.toLowerCase());
        // Show checkbox if value matches a valid language
        if (inputValue !== '' && (options.includes(inputValue) || options.some(opt => opt === inputValue))) {
            priceContainer.style.display = 'block';
        } else {
            // Only hide if it's NOT checked. If it is checked (from server), keep it visible? 
            // Better behavior: consistency with input validity.
            // If input is invalid language, we usually shouldn't filter by price.
            // But let's check existence.
            if (inputValue === '') {
                 priceContainer.style.display = 'none';
            } else {
                 // If text is there but not in list... strict? 
                 // Let's stick to strict match logic to be safe, but allow existing check to force show?
                 // If 'use_price_filter' is on, we definitely want it shown.
                 const isChecked = document.getElementById('price-filter-checkbox').checked;
                 if (isChecked) {
                     priceContainer.style.display = 'block';
                 } else {
                     priceContainer.style.display = options.includes(inputValue) ? 'block' : 'none';
                 }
            }
        }
    }

    input.addEventListener('input', refreshPriceUI);
    input.addEventListener('input', refreshPriceUI);
    // Run on load to persist state
    refreshPriceUI();

    // NEW: Force price SLIDER visibility if checkbox is checked (server state)
    const priceCheckbox = document.getElementById('price-filter-checkbox');
    const priceBarContainer = document.getElementById('price-bar-container');
    if (priceCheckbox && priceBarContainer) {
        // Toggle slider on change
        priceCheckbox.addEventListener('change', function() {
            priceBarContainer.style.display = priceCheckbox.checked ? 'block' : 'none';
        });
        // Init on load
        if (priceCheckbox.checked) {
            priceBarContainer.style.display = 'block';
        }
    }
});
</script>


<fieldset id="teacher-level" class='student-dialog'>
    <legend>Language I want to learn:</legend>
    <input type="text" id="student-learn-input" name="learns" list="idiomas-list" placeholder="Type to search..." value="<?php echo isset($_GET['learns']) ? htmlspecialchars(get_lang_name_by_code($_GET['learns'], $idiomas_equiv)) : ''; ?>">
    <hr>
    <!-- Checkbox para buscar por nivel -->
    <div id="student-buscar-por-nivel-container" style="display:none;">
        <label>
            <input type="checkbox" id="student-buscar-por-nivel-checkbox" name="use_learns_level_filter" <?php if(isset($_GET['use_learns_level_filter']) && $_GET['use_learns_level_filter']=='on') echo 'checked="checked"'; ?>> Do you want to filter by level?
        </label>
    </div>
    <div id="student-level-sliders" style="display:none;">
        <label>My partner's level
            <?php
            require_once('niveles.php');
            // Calculate defaults for Partner/Student Level (Slider 1)
            // Backend maps 'min_learns_level' -> learns_min_level
            $defMin1 = 0; 
            $defMax1 = -2; // Default logic equivalent (starts at B2 if array len 7?)
            
            // Check for persisted values (either new specific name or old generic name)
            $valMin1 = isset($_GET['min_learns_level']) ? $_GET['min_learns_level'] : (isset($_GET['learns_min_level']) ? $_GET['learns_min_level'] : null);
            $valMax1 = isset($_GET['max_learns_level']) ? $_GET['max_learns_level'] : (isset($_GET['learns_max_level']) ? $_GET['learns_max_level'] : null);

            $level_keys = array_keys($lista_niveles);
            // Map Value "1"(A1) -> Index 0. 
            if ($valMin1 !== null) {
                // Find index of value. $lista_niveles keys are "1","2"... values "A1","A2"
                // The slider returns the KEY (e.g. "1").
                // $lista_niveles keys are indices? No. Keys are "1", "2".
                // Array keys: 0=>"1", 1=>"2".
                $idx = array_search((string)$valMin1, $level_keys);
                if ($idx !== false) $defMin1 = $idx;
            }
            if ($valMax1 !== null) {
                $idx = array_search((string)$valMax1, $level_keys);
                if ($idx !== false) $defMax1 = $idx;
            }

            // Slider 1: 'learns_level' -> creates min_learns_level / max_learns_level
            selectSlider('slider1', 'learns_level', $lista_niveles, $defMin1, $defMax1);
            ?>
            <select name="min_learns_level_select" id="slider1Min" style="display:none"></select>
            <select name="max_learns_level_select" id="slider1Max" style="display:none"></select>
        </label>
        <hr>
    </div>
</fieldset>

<fieldset id='lang-teach' class='student-dialog'>
    <legend>Language I want to teach:</legend>
    <input type="text" id="student-teach-input" name="teaches" list="idiomas-list" placeholder="Type to search..." value="<?php echo isset($_GET['teaches']) ? htmlspecialchars(get_lang_name_by_code($_GET['teaches'], $idiomas_equiv)) : ''; ?>">
    <hr>
    <!-- Checkbox para buscar por nivel en teaches -->
    <div id="student-buscar-por-nivel-teach-container" style="display:none;">
        <label>
            <input type="checkbox" id="student-buscar-por-nivel-teach-checkbox" name="use_teaches_level_filter" <?php if(isset($_GET['use_teaches_level_filter']) && $_GET['use_teaches_level_filter']=='on') echo 'checked="checked"'; ?>> Do you want to filter by level?
        </label>
    </div>
    <div id="teach-level-slider" style="display:none;">
        <label>My level
            <?php
            require_once('niveles.php');
            // Calculate defaults for My/Teacher Level (Slider 2)
            $defMin2 = 0; 
            $defMax2 = -2; 
            
            $valMin2 = isset($_GET['min_teaches_level']) ? $_GET['min_teaches_level'] : (isset($_GET['teaches_min_level']) ? $_GET['teaches_min_level'] : null);
            $valMax2 = isset($_GET['max_teaches_level']) ? $_GET['max_teaches_level'] : (isset($_GET['teaches_max_level']) ? $_GET['teaches_max_level'] : null);

            $level_keys = array_keys($lista_niveles);
            
            if ($valMin2 !== null) {
                $idx = array_search((string)$valMin2, $level_keys);
                if ($idx !== false) $defMin2 = $idx;
            }
            if ($valMax2 !== null) {
                $idx = array_search((string)$valMax2, $level_keys);
                if ($idx !== false) $defMax2 = $idx;
            }

            // Slider 2: 'teaches_level' -> creates min_teaches_level / max_teaches_level
            selectSlider('slider2', 'teaches_level', $lista_niveles, $defMin2, $defMax2);
            ?>
        </label>
        <hr>
    </div>
</fieldset>

<script>
document.addEventListener('DOMContentLoaded', function() {

    function setupCheckbox(inputId, datalistId, checkboxContainerId, sliderContainerId, checkboxId) {
        const input = document.getElementById(inputId);
        const datalist = document.getElementById(datalistId);
        const checkboxContainer = document.getElementById(checkboxContainerId);
        const sliderContainer = document.getElementById(sliderContainerId);
        const checkbox = document.getElementById(checkboxId);

        if (!input || !datalist || !checkboxContainer || !sliderContainer || !checkbox) return;

        function checkInputMatch() {
            const options = Array.from(datalist.options).map(opt => opt.value.toLowerCase());
            // Check formatted match or loose match
            if (input.value !== '' && (options.includes(input.value.toLowerCase()) || options.some(opt => opt === input.value.toLowerCase()))) {
                checkboxContainer.style.display = 'block';
            } else {
                checkboxContainer.style.display = 'none';
                // If container is hidden, should we hide slider too? Maybe, but let's leave slider logic to checkbox state.
            }
        }

        // Mostrar/ocultar checkbox según el idioma escrito
        input.addEventListener('input', checkInputMatch);

        // Mostrar/ocultar slider al marcar el checkbox
        checkbox.addEventListener('change', function() {
            if (checkbox.checked) {
                sliderContainer.style.display = 'block';
            } else {
                sliderContainer.style.display = 'none';
            }
        });

        // Init state on load
        checkInputMatch();
        if (checkbox.checked) {
            sliderContainer.style.display = 'block';
            checkboxContainer.style.display = 'block'; // Force container visible if checked, regardless of text match (safety)
        }
    }


    // Para "I want to learn" en student
    setupCheckbox(
        'student-learn-input', 
        'idiomas-list', 
        'student-buscar-por-nivel-container', 
        'student-level-sliders', 
        'student-buscar-por-nivel-checkbox'
    );

    // Para "I want to teach" en student
    setupCheckbox(
        'student-teach-input', 
        'idiomas-list', 
        'student-buscar-por-nivel-teach-container', 
        'teach-level-slider', 
        'student-buscar-por-nivel-teach-checkbox'
    );

});
</script>



    <fieldset>
        <legend>Compatibility: 
            <div class="tooltip-container">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" style="height: 0.9em; vertical-align: -0.125em;" fill="#888">
                    <!--!Font Awesome Free 6.5.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.-->
                    <path d="M256 512A256 256 0 1 0 256 0a256 256 0 1 0 0 512zM216 336h24V272H216c-13.3 0-24-10.7-24-24s10.7-24 24-24h48c13.3 0 24 10.7 24 24v88h8c13.3 0 24 10.7 24 24s-10.7 24-24 24H216c-13.3 0-24-10.7-24-24s10.7-24 24-24zm40-208a32 32 0 1 1 0 64 32 32 0 1 1 0-64z"/>
                </svg>
                <span class="tooltip-text">Check this to view users even if you don't share a common language.</span>
            </div>
        </legend>
        <label class="container">Even if we cannot understand each other
            <input type="checkbox" name='lang-compatibility' <?php if(!isset($_GET['distance']) || isset($_GET['lang-compatibility'])) echo 'checked="checked"'; ?>>
            <span class="checkmark"></span>
        </label>
        <hr>
    </fieldset>

    <fieldset id='experience-level'>
        <legend for='experience-level'>Organization</legend>
        <div id='controlgroup'>
            <?php
            // Get the current user's email
            $user_email_query = "SELECT email FROM mentor2009 WHERE orden = $identificador2017";
            $user_email_result = mysqli_query($link, $user_email_query);
            $user_email_row = mysqli_fetch_assoc($user_email_result);
            $user_email = $user_email_row['email'] ?? '';
            
            // Get domain from email
            $email_domain = '';
            if (!empty($user_email) && strpos($user_email, '@') !== false) {
                $email_parts = explode('@', $user_email);
                $email_domain = $email_parts[1];
            }
            
            // Check if domain exists in organization_emails
            $org_id = null;
            $org_name = '';
            if (!empty($email_domain)) {
                $org_query = "SELECT organization_id FROM organization_emails WHERE email_domain = '$email_domain'";
                $org_result = mysqli_query($link, $org_query);
                if (mysqli_num_rows($org_result) > 0) {
                    $org_row = mysqli_fetch_assoc($org_result);
                    $org_id = $org_row['organization_id'];
                    
                    // Get organization name
                    $org_name_query = "SELECT organization_name FROM organizations WHERE organization_id = $org_id";
                    $org_name_result = mysqli_query($link, $org_name_query);
                    if (mysqli_num_rows($org_name_result) > 0) {
                        $org_name_row = mysqli_fetch_assoc($org_name_result);
                        $org_name = $org_name_row['organization_name'];
                    }
                }
            }
            
            if ($org_id) {
                // User has an organization
                echo "<div style='margin-bottom: 10px;'>";

                echo "<label class='container'><strong> $org_name </strong>";
                echo "<input type='checkbox' name='orgs[]' value='$org_id'>";
                echo "<span class='checkmark'></span>";
                echo "</label>";
                echo "</div>";
            } else {
                // User doesn't have an organization, hide the section
                echo "<p style='font-size: 90%;'>You are not associated with any organization.</p>";
            }
            ?>
        </div>
        <hr>
    </fieldset>

    <script type="text/javascript">
            const radioTeachers = document.getElementById('radio-teachers');
            const radioStudents = document.getElementById('radio-students');
            const langTeach = document.getElementById('lang-teach');
            const langLearn = document.getElementById('lang-learn');
            const teacherLevel = document.getElementById('teacher-level');

            function setDisabledForFieldset(fs, disabled) {
                if (!fs) return;
                const inputs = fs.querySelectorAll('input, select, textarea, button');
                inputs.forEach(el => { el.disabled = !!disabled; });
            }

            function handleRadioChange() {
                if (radioTeachers.checked) {
                    langTeach.style.display = 'none';
                    langLearn.style.display = 'block';
                    teacherLevel.style.display = 'none';

                    // Enable only teacher (looking for pro) inputs
                    setDisabledForFieldset(langLearn, false);
                    setDisabledForFieldset(langTeach, true);
                    setDisabledForFieldset(teacherLevel, true);
                } else if (radioStudents.checked) {
                    langTeach.style.display = 'block';
                    teacherLevel.style.display = 'block';
                    langLearn.style.display = 'none';

                    // Enable only student exchange inputs
                    setDisabledForFieldset(langLearn, true);
                    setDisabledForFieldset(langTeach, false);
                    setDisabledForFieldset(teacherLevel, false);
                }
            }

            radioTeachers.addEventListener('change', handleRadioChange);
            radioStudents.addEventListener('change', handleRadioChange);
            handleRadioChange();

            // Capturar los niveles de los sliders al enviar el formulario
            document.getElementById('search-form').addEventListener('submit', function(e) {
                // Slider de "Mi pareja" (nivel que quiero que tenga en el idioma que YO quiero aprender)
                var s1 = $('#slider1');
                if (s1.length) {
                    var vals1 = s1.slider('option', 'values');
                    var min1, max1;
                    if (Array.isArray(vals1)) { min1 = vals1[0]; max1 = vals1[1]; }
                    else {
                        var v1 = s1.slider('option', 'value');
                        if (typeof v1 === 'undefined') { v1 = s1.slider('value'); }
                        min1 = v1; max1 = v1;
                    }
                    // Rellenar compatibilidad con backend existente
                    document.getElementById('min_level').value = min1;
                    document.getElementById('max_level').value = max1;
                    // Nuevos campos explícitos
                    document.getElementById('learns_min_level').value = min1;
                    document.getElementById('learns_max_level').value = max1;
                }

                // Slider de "Mi nivel" (en el idioma que YO enseño)
                var s2 = $('#slider2');
                if (s2.length) {
                    var vals2 = s2.slider('option', 'values');
                    var min2, max2;
                    if (Array.isArray(vals2)) { min2 = vals2[0]; max2 = vals2[1]; }
                    else {
                        var v2 = s2.slider('option', 'value');
                        if (typeof v2 === 'undefined') { v2 = s2.slider('value'); }
                        min2 = v2; max2 = v2;
                    }
                    document.getElementById('teaches_min_level').value = min2;
                    document.getElementById('teaches_max_level').value = max2;
                }
            });
        </script>
            <script type="text/javascript">
document.addEventListener('DOMContentLoaded', function () {
    // Radios y secciones
    const radioTeachers = document.getElementById('radio-teachers');
    const radioStudents = document.getElementById('radio-students');
    const langTeach = document.getElementById('lang-teach');
    const langLearn = document.getElementById('lang-learn');
    const teacherLevel = document.getElementById('teacher-level');

    function handleRadioChange() {
        if (radioTeachers && radioTeachers.checked) {
            if (langTeach) langTeach.style.display = 'none';
            if (langLearn) langLearn.style.display = 'block';
            if (teacherLevel) teacherLevel.style.display = 'none';
        } else if (radioStudents && radioStudents.checked) {
            if (langTeach) langTeach.style.display = 'block';
            if (teacherLevel) teacherLevel.style.display = 'block';
            if (langLearn) langLearn.style.display = 'none';
        }
    }

    if (radioTeachers) radioTeachers.addEventListener('change', handleRadioChange);
    if (radioStudents) radioStudents.addEventListener('change', handleRadioChange);
    handleRadioChange();

    // -------------------- BLOQUE LEARN --------------------
    // (Language I want to learn) -> slider SOLO aparece si checkbox 'buscar por nivel' está marcado
    const checkboxLearn = document.getElementById('buscar-por-nivel-checkbox');
    const sliderLearn = document.getElementById('student-level-sliders');
    const checkboxContainerLearn = document.getElementById('buscar-por-nivel-container');

    // Mostrar el checkbox SOLO cuando se haya escrito un idioma en students
    const learnInput = document.querySelector('#teacher-level input[name="learns"]');
    function refreshLearnUI() {
        const hasLang = !!(learnInput && learnInput.value.trim() !== '');
        if (checkboxContainerLearn) checkboxContainerLearn.style.display = hasLang ? '' : 'none';
        if (!hasLang) {
            if (checkboxLearn) checkboxLearn.checked = false;
            if (sliderLearn) sliderLearn.style.display = 'none';
        }
    }
    if (learnInput) learnInput.addEventListener('input', refreshLearnUI);
    refreshLearnUI();

    function updateLearnSection() {
        if (!sliderLearn || !checkboxLearn) return;
        // SOLO mostrar slider si el checkbox está marcado
        sliderLearn.style.display = checkboxLearn.checked ? '' : 'none';
    }

    if (checkboxLearn) checkboxLearn.addEventListener('change', updateLearnSection);
    // No añadimos listener 'input' que muestre el slider al escribir
    updateLearnSection();

    // -------------------- BLOQUE TEACH --------------------
    // (Language I want to teach) -> slider SOLO aparece si checkbox 'buscar por nivel' está marcado
    const checkboxTeach = document.getElementById('buscar-por-nivel-teach-checkbox');
    const sliderTeach = document.getElementById('teach-level-slider');
    const checkboxContainerTeach = document.getElementById('buscar-por-nivel-teach-container');

    // Mostrar el checkbox SOLO cuando se haya escrito un idioma en students (teach)
    const teachInput = document.querySelector('#lang-teach input[name="teaches"]');
    function refreshTeachUI() {
        const hasLang = !!(teachInput && teachInput.value.trim() !== '');
        if (checkboxContainerTeach) checkboxContainerTeach.style.display = hasLang ? '' : 'none';
        if (!hasLang) {
            if (checkboxTeach) checkboxTeach.checked = false;
            if (sliderTeach) sliderTeach.style.display = 'none';
        }
    }
    if (teachInput) teachInput.addEventListener('input', refreshTeachUI);
    refreshTeachUI();

    function updateTeachSection() {
        if (!sliderTeach || !checkboxTeach) return;
        // SOLO mostrar slider si el checkbox está marcado
        sliderTeach.style.display = checkboxTeach.checked ? '' : 'none';
    }

    if (checkboxTeach) checkboxTeach.addEventListener('change', updateTeachSection);
    // No añadimos listener 'input' que muestre el slider al escribir
    updateTeachSection();

    // -------------------- SLIDER AL ENVIAR --------------------
    const searchForm = document.getElementById('search-form');
    if (searchForm) {
        searchForm.addEventListener('submit', function (e) {
            if (radioStudents && radioStudents.checked) {
                var minSelect = document.getElementById('slider1Min');
                var maxSelect = document.getElementById('slider1Max');
                if (minSelect && maxSelect && typeof $ === 'function' && $("#slider1").slider) {
                    minSelect.value = $("#slider1").slider("values", 0);
                    maxSelect.value = $("#slider1").slider("values", 1);
                    if (document.getElementById('min_level')) document.getElementById('min_level').value = minSelect.value;
                    if (document.getElementById('max_level')) document.getElementById('max_level').value = maxSelect.value;
                }
            }
        });
    }
});
</script>
    </fieldset>

    <fieldset id='gender'>
        <legend for='gender'>Gender</legend>
        <label class='container'>Male
            <input type='checkbox' name='male' checked='checked' />
            <span class="checkmark"></span>
        </label>
        <label class='container'>Female
            <input type='checkbox' name='female' checked='checked' />
            <span class="checkmark"></span>
        </label>
        <hr>
        <!-- <script>$( "#gender" ).controlgroup({"direction": "vertical"});</script> -->
    </fieldset>

    <fieldset>
    <legend>Map</legend>
    <label class='container'>Global Search:
        <input type='checkbox' name='zone' id="global-search-checkbox" onclick="handleGlobalSearchChange()" <?php if(isset($_GET['zone']) && $_GET['zone']=='on') echo "checked='checked'"; ?> />
        <span class="checkmark"></span>
    </label>
    <div id="osm-map" style="height:220px; width:100%; margin-bottom:10px; border:1px solid #ccc; position:relative; <?php if(isset($_GET['zone']) && $_GET['zone']=='on') echo 'display:none;'; ?>"></div>
    <div id="mapModal" class="modal">
        <div class="modal-content">
            <span class="close" id="close-map-modal">&times;</span>
            <div id="large-map" style="height: 650px; margin-top: 10px; width: 100%; position: relative;"></div>
        </div>
        <input type="hidden" id="visible-users-input" name="visible_users" value="">
    </div>
    <hr id="map-hr" style="<?php if(isset($_GET['zone']) && $_GET['zone']=='on') echo 'display:none;'; ?>">
</fieldset>

    <fieldset id="distance-fieldset" style="<?php if(isset($_GET['zone']) && $_GET['zone']=='on') echo 'display:none;'; ?>">
        <legend>Distance radius:</legend>
        <select name="distance" id="distance-select" onchange="updateRadius();">
            <?php
            $distances = [1, 5, 10, 20, 50];
            
            // Usar el valor de la URL o el predeterminado
            foreach ($distances as $distance) {
                $selected = ($distance == $current_distance) ? 'selected="selected"' : '';
                echo "<option value='$distance' $selected>{$distance} km</option>";
            }
            ?>
        </select>
        <hr>
    </fieldset>

    <input class="ui-button ui-widget ui-corner-all" style='width: 100%' type="submit" value="Search" />

    <?php
    // we extract the information to show the users in the map
    $number_of_affected_users_map = 0;
    $orden_usuarios_map = array();
    $nameuser_map = array(); // Renamed to avoid clobbering global $nameuser if possible, but keep inconsistent for now or reuse? Let's use local if loop uses it.
    // Actually, keep $nameuser to match existing code usage inside the loop below, but initialize it.
    // To be safe against overwriting index_paginated's variable (though it has a backup), we should ideally rename, 
    // but the loop at line 1083 uses $nameuser. If we rename here, we must rename there.
    // For minimal risk change, I will just keep it but ensure we don't clobber if we skip.
    // BUT wait, search-form.php logic CLOBBERS it currently. If I skip, I might NOT clobber it, which changes behavior.
    // If index_paginated relies on it being clobbered ... unlikely.
    // I'll initialize a separate array for map names if I can, but let's stick to the conditional logic first.
    
    // Initialize standard map arrays
    $lat_map = array();
    $lng_map = array();
    $fotos_map = array();
    
    if (!isset($skip_search_form_map_query) || !$skip_search_form_map_query) {

        if (isset($where_clause) && !empty($where_clause)) {
             // Si búsqueda filtrada
             // Asegurarnos de tener las variables de distancia necesarias si vengono de index_paginated
             $filtro_mapa = isset($filtro_distancia) ? $filtro_distancia : "";
             $lat_ref = isset($latitud1) ? $latitud1 : 0; // Fallback, aunque deberían estar
             $lng_ref = isset($longitud1) ? $longitud1 : 0;
             
             // Si latitud1 no está definida (raro si viene de index_paginated), no podemos calcular distancia
             if ($lat_ref == 0 && $lng_ref == 0) {
                  $query_map = "SELECT * FROM mentor2009 m WHERE $where_clause AND (m.Gpslat<>0 AND m.Gpslng<>0)";
             } else {
                 // Replicamos la lógica de distancia
                  $query_map = "
                    SELECT m.*,
                    (ACOS(SIN(RADIANS(m.Gpslat)) * SIN(RADIANS($lat_ref)) +
                        COS(RADIANS(m.Gpslat)) * COS(RADIANS($lat_ref)) *
                        COS(RADIANS(m.Gpslng) - RADIANS($lng_ref))) * 6378) AS distanciaPunto1Punto2
                    FROM mentor2009 m
                    WHERE $where_clause
                    AND (m.Gpslat<>0 AND m.Gpslng<>0)
                    $filtro_mapa
                  ";
             }
        } else {
            // Default load
            $query_map = "SELECT * FROM mentor2009 WHERE (Gpslat<>0 AND Gpslng<>0 )";
        }
        $result_map = mysqli_query($link, $query_map);
        if (!$result_map) {
            // die(".........No results......"); // Avoid die, just 0 results
        } else {
            $number_of_affected_users_map = mysqli_num_rows($result_map);
            
            // Clean arrays before filling if we are running the query (local usage)
            $nameuser = array(); 

            for ($iiii = 0; $iiii < $number_of_affected_users_map; $iiii++) {
                $fila_map = mysqli_fetch_array($result_map);
                array_push($orden_usuarios_map, $fila_map['orden']);
                //array_push($teacher_or_student, $fila_map['Pais']);
                array_push($lat_map, $fila_map['Gpslat']);
                array_push($lng_map, $fila_map['Gpslng']);
                array_push($nameuser, $fila_map['nombre']); // Guardamos el nombre del usuario
                $thumb_nombre = $orden_usuarios_map[$iiii];
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
                array_push($fotos_map, $thumb_nombre);
            }
        }
    }
    ?>
    
    <script type="text/javascript">
        // Variables globales
        var element = document.getElementById('osm-map');
        var currentSmallCircle = null;
        var currentLargeCircle = null;
        var smallMap, largeMap;
        var markers, largeMarkers;
        var globalSearchCheckbox = document.getElementById('global-search-checkbox');
        var mapContainer = document.getElementById('osm-map');
        var distanceFieldset = document.getElementById('distance-fieldset');
        var expandMapButton = document.getElementById('expand-map-button');
        var DEFAULT_RADIUS = <?php echo $current_distance; ?>; // Usar el radio actual como predeterminado
        var allMarkers = {}; // Objeto para almacenar todos los marcadores por ID de usuario

        // Asegurarse de que el mapa tenga altura
        if (element) {
            element.style.height = '220px';
            element.style.width = '100%';
            element.style.border = '1px solid #ccc';
        }

        // Función para crear grupos de clusters
        function createMarkerClusterGroup() {
            return L.markerClusterGroup({
                maxClusterRadius: 30,
                spiderfyOnMaxZoom: true,
                showCoverageOnHover: false,
                zoomToBoundsOnClick: true,
                iconCreateFunction: function (cluster) {
                    var childCount = cluster.getChildCount();
                    return L.divIcon({
                        html: '<div><span>' + childCount + '</span></div>',
                        className: 'marker-cluster-custom',
                        iconSize: L.point(40, 40)
                    });
                }
            });
        }

        // Función para crear marcadores
        function crearMarcador(lat, lng, foto, nombreUsuario, idUsuario, markerGroup, isLargeMap = false, userData = null) {
            // Obtenemos los datos de idiomas del usuario directamente del objeto user si es posible
            let teaches = [];
            let learns = [];
            
            if (userData) {
                teaches = userData.teaches || [];
                learns = userData.learns || [];
            } else {
                 // Fallback para compatibilidad (usando window.parent si fuera necesario, aunque ya no debería)
                 // const langData = window.parent.userLanguagesData && window.parent.userLanguagesData[idUsuario] || {};
                 // teaches = langData.teaches || [];
                 // learns = langData.learns || [];
            }

            // Contenido del popup (igual para ambos mapas)
            const popupContent = `
                <b>${nombreUsuario}</b><br>
                <a href="#" onclick="var w = window.open('../user/u.php?identificador=${idUsuario}', '_blank'); if (w) { w.opener = null; } return false;" 
                style="text-decoration: underline; float: none;">
                    View profile
                </a><br>
                <b>Speaks:</b> ${teaches.map(lang => lang.substring(0, 3).toLowerCase()).join(', ')}<br>
                <b>Learns:</b> ${learns.map(lang => lang.substring(0, 3).toLowerCase()).join(', ')}
            `;
            
            // Tamaño del icono según el mapa
            var iconSize = isLargeMap ? [40, 40] : [30, 30];
            var maxWidth = isLargeMap ? 300 : 150;
            
            var marker = L.marker([lat, lng], {
                icon: L.icon({
                    iconUrl: foto,
                    iconSize: iconSize,
                    className: 'rounded-marker'
                }),
                userId: idUsuario,
                userName: nombreUsuario
            }).bindPopup(popupContent, {maxWidth: maxWidth})
            .on('click', function(){
                // Enviar un mensaje al padre para navegar a la página del usuario y resaltarlo
                window.parent.postMessage({
                    action: 'highlightUser',
                    userId: idUsuario,
                    userName: nombreUsuario
                }, '*');
            });

            markerGroup.addLayer(marker);
            
            // Guardar referencia al marcador
            allMarkers[idUsuario] = marker;
            
            return marker;
        }

        // Función para verificar si un marcador está dentro del círculo
        function isMarkerInCircle(markerOrLatLng, circle) {
            if (!circle) return true;

            var latLng;
            if (markerOrLatLng.getLatLng) {
                latLng = markerOrLatLng.getLatLng();
            } else {
                latLng = markerOrLatLng;
            }

            return circle.getLatLng().distanceTo(latLng) <= circle.getRadius();
        }

        // Función simplificada para crear círculos
        function crearCirculo(lat, lng, radio, color, mapa) {
            // Eliminar círculo existente si hay uno
            if (mapa === smallMap && currentSmallCircle) {
                smallMap.removeLayer(currentSmallCircle);
                currentSmallCircle = null;
            } else if (mapa === largeMap && currentLargeCircle) {
                largeMap.removeLayer(currentLargeCircle);
                currentLargeCircle = null;
            }
            
            // Asegurarse de que el radio es un número
            radio = parseFloat(radio);
            
            // Crear nuevo círculo
            var circle = L.circle([lat, lng], {
                color: color,
                fillColor: 'rgba(255, 102, 0, 0.6)',
                fillOpacity: 0.4,
                radius: radio * 1000
            }).addTo(mapa);
            
            // Ajustar vista al círculo
            mapa.fitBounds(circle.getBounds());
            
            console.log("Círculo creado con radio: " + radio + "km");
            
            // Actualizar variable global
            if (mapa === smallMap) {
                currentSmallCircle = circle;
            } else if (mapa === largeMap) {
                currentLargeCircle = circle;
            }
            
            return circle;
        }

        // Función para actualizar el radio
        function updateRadius() {
            if (!smallMap) return;
            
            var distance = document.getElementById('distance-select').value || DEFAULT_RADIUS;
            console.log("Actualizando radio a: " + distance + "km");
            
            // Actualizar círculo en mapa pequeño
            crearCirculo(
                <?php echo $latitud1; ?>,
                <?php echo $longitud1; ?>,
                distance,
                'orange',
                smallMap
            );
            
            // Actualizar círculo en mapa grande si existe
            if (largeMap) {
                crearCirculo(
                    <?php echo $latitud1; ?>,
                    <?php echo $longitud1; ?>,
                    distance,
                    'orange',
                    largeMap
                );
            }
            
            // Actualizar marcadores
            actualizarMarcadores();
        }
        
        // Función para actualizar los marcadores según el radio actual
        function actualizarMarcadores() {
            if (!smallMap || !markers) return;
            
            // Limpiar marcadores existentes
            markers.clearLayers();
            allMarkers = {};
            
            // Obtener los usuarios de la página actual desde la ventana padre
            var currentPageUsers = [];
            try {
                if (window.parent && window.parent.currentPageUsers) {
                    currentPageUsers = window.parent.currentPageUsers;
                }
            } catch (e) {
                console.error("Error al acceder a currentPageUsers:", e);
            }
            
            // Obtener todos los usuarios desde la ventana padre
            var allUsersData = [];
            try {
                if (window.parent && window.parent.allUsersData) {
                    allUsersData = window.parent.allUsersData;
                }
            } catch (e) {
                console.error("Error al acceder a allUsersData:", e);
            }
            
            // Si no tenemos datos de la ventana padre, usar los datos del PHP
            if (allUsersData.length === 0) {
                <?php
                // Removed redundant loop. Localization data is now injected directly into allUsersData in index_paginated.php
                ?>
            }
            
            // Añadir marcadores para todos los usuarios
            allUsersData.forEach(function(user) {
                var punto = L.latLng(user.lat, user.lng);
                
                // Solo añadir si está dentro del círculo
                if (currentSmallCircle && isMarkerInCircle(punto, currentSmallCircle)) {
                    // Usar foto confiable del backend si está disponible; si no, fallback a default
                    var fotoUsuario = (user.photo && typeof user.photo === 'string' && user.photo.length > 0)
                        ? user.photo
                        : "../uploader/default.jpg";
                    
                    // Verificar si el usuario está en la página actual para resaltarlo
                    var isOnCurrentPage = currentPageUsers.includes(parseInt(user.id));
                    
                    // Crear el marcador
                    var marker = crearMarcador(
                        user.lat, 
                        user.lng, 
                        fotoUsuario, 
                        user.name, 
                        user.id, 
                        markers,
                        false,
                        user // Pass FULL user object
                    );
                    
                    // Si está en la página actual, resaltar el marcador
                    if (isOnCurrentPage) {
                        marker.setZIndexOffset(1000); // Poner encima de otros marcadores
                        // También podríamos cambiar el estilo del icono para resaltarlo
                    }
                }
            });
            
            // Actualizar marcadores en el mapa grande si existe
            if (largeMap && largeMarkers) {
                actualizarMarcadoresGrandes();
            }
            
            // Actualizar lista de usuarios visibles
            updateVisibleUsers();
        }
        
        // Función para actualizar los marcadores en el mapa grande
        function actualizarMarcadoresGrandes() {
            if (!largeMap || !largeMarkers) return;
            
            // Limpiar marcadores existentes
            largeMarkers.clearLayers();
            
            // Copiar los marcadores del mapa pequeño al grande
            markers.eachLayer(function(marker) {
                var latlng = marker.getLatLng();
                var userId = marker.options.userId;
                var userName = marker.options.userName;
                var icon = marker.options.icon;
                
                // Crear un nuevo marcador en el mapa grande
                var largeMarker = L.marker(latlng, {
                    icon: L.icon({
                        iconUrl: icon.options.iconUrl,
                        iconSize: [40, 40],
                        className: 'rounded-marker'
                    }),
                    userId: userId,
                    userName: userName
                }).bindPopup(marker.getPopup().getContent(), {maxWidth: 300})
                .on('click', function(){
                    window.parent.postMessage({
                        action: 'highlightUser',
                        userId: userId,
                        userName: userName
                    }, '*');
                });

                largeMarkers.addLayer(largeMarker);
            });
        }

        // Función para actualizar la visibilidad de los usuarios
        function updateVisibleUsers() {
            if (!markers || !smallMap) return;

            const visibleIds = [];
            markers.eachLayer(function(marker) {
                if (marker.options && marker.options.userId) {
                    visibleIds.push(marker.options.userId);
                }
            });

            const input = document.getElementById('visible-users-input');
            if (input) input.value = visibleIds.join(',');
        }

        // Función para resaltar un marcador en el mapa
        function highlightMarkerOnMap(userId) {
            // Verificar si tenemos el marcador
            if (allMarkers[userId]) {
                // Centrar el mapa en el marcador
                smallMap.setView(allMarkers[userId].getLatLng(), 12);
                
                // Abrir el popup del marcador
                allMarkers[userId].openPopup();
                
                return true;
            }
            return false;
        }

        // Función para crear el mapa pequeño
        function createSmallMap() {
            if (!element) return null;
            
            // Inicializar mapa
            smallMap = L.map(element);
            
            // Añadir capa de mosaicos
            L.tileLayer('https://{s}.tile.osm.org/{z}/{x}/{y}.png', {
                attribution: '(c) <a href="http://osm.org/copyright">OpenStreetMap</a> contributors'
            }).addTo(smallMap);

            // Establecer vista inicial
            var target = L.latLng(<?php echo "$latitud1"; ?>, <?php echo "$longitud1"; ?>);
            smallMap.setView(target, 8);

            // Añadir botón de expandir mapa
            addExpandMapControl(smallMap);

            // Crear grupo de marcadores
            markers = createMarkerClusterGroup();
            smallMap.addLayer(markers);

            // Crear círculo inicial con radio predeterminado
            crearCirculo(
                <?php echo $latitud1; ?>,
                <?php echo $longitud1; ?>,
                DEFAULT_RADIUS,
                'orange',
                smallMap
            );
            
            // Añadir marcadores iniciales
            actualizarMarcadores();
            
            // Configurar eventos
            smallMap.on('moveend zoomend', updateVisibleUsers);

            return smallMap;
        }

        function addExpandMapControl(map) {
            // Crear un control personalizado para el botón de expandir mapa
            var expandMapControl = L.Control.extend({
                options: {
                    position: 'topright'
                },
                onAdd: function(map) {
                    var container = L.DomUtil.create('div', 'leaflet-bar leaflet-control');
                    var button = L.DomUtil.create('a', 'expand-map-button', container);
                    //button.innerHTML = `<svg width="32" height="32" viewBox="0 0 44 44" style="vertical-align:middle;"><polyline points="10,34 34,10" style="fill:none;stroke:#e65f00;stroke-width:4"/><polyline points="10,10 34,34" style="fill:none;stroke:#e65f00;stroke-width:4"/></svg>`;
                    var img = L.DomUtil.create('img', 'expand-map-icon', button);
                    img.src = 'img/felchaamplia.png'; // Ruta del icono proporcionado
                    img.style.width = '20px';
                    img.style.height = '20px';
                    img.style.display= 'block';
                    img.style.color = '#e65f00';
                    img.style.position = 'relative';
                    img.style.top = '50%';
                    img.style.left = '50%';
                    img.style.transform = 'translate(-50%, -50%)';
                    button.href = '#';
                    button.title = 'Expandir mapa';
                    button.style.padding = '0';
                    button.style.display = 'block';
                    button.style.textDecoration = 'none';
                    button.style.background = 'white';
                    button.style.height = '32px';
                    button.style.width = '32px';
                    button.style.borderRadius = '6px';
                    button.style.boxShadow = '0 1px 5px rgba(230,95,0,0.3)';
                    L.DomEvent.on(button, 'click', function(e) {
                        L.DomEvent.stopPropagation(e);
                        L.DomEvent.preventDefault(e);
                        var modal = document.getElementById("mapModal");
                        // Obtener posición y tamaño del mapa pequeño
                        var mapRect = document.getElementById('osm-map').getBoundingClientRect();
                        var winW = window.innerWidth;
                        var winH = window.innerHeight;
                        // Calcular el punto central relativo del mapa pequeño
                        var originX = ((mapRect.left + mapRect.width / 2) / winW) * 100;
                        var originY = ((mapRect.top + mapRect.height / 2) / winH) * 100;
                        modal.style.transformOrigin = originX + '% ' + originY + '%';
                        modal.style.display = "block";
                        setTimeout(function() {
                            modal.style.opacity = 1;
                            modal.style.transform = "scale(1)";
                        }, 10);
                        initLargeMap();
                    });
                    return container;
                }
            });
            map.addControl(new expandMapControl());
        }

        // Función para crear el mapa grande como copia exacta del pequeño
        function initLargeMap() {
            // Verificar que el mapa pequeño existe
            if (!smallMap) return;
            
            // Obtener el centro y zoom actuales del mapa pequeño
            var currentCenter = smallMap.getCenter();
            var currentZoom = smallMap.getZoom();
            var currentRadius = document.getElementById('distance-select').value || DEFAULT_RADIUS;
            
            // Eliminar mapa grande existente si lo hay
            if (largeMap) {
                largeMap.remove();
            }

            // Crear nuevo mapa grande con la misma vista que el pequeño
            largeMap = L.map('large-map');
            L.tileLayer('https://{s}.tile.osm.org/{z}/{x}/{y}.png', {
                attribution: '(c) OpenStreetMap contributors'
            }).addTo(largeMap);
            
            // Establecer exactamente la misma vista que el mapa pequeño
            largeMap.setView(currentCenter, currentZoom);

            // Crear grupo de marcadores
            largeMarkers = createMarkerClusterGroup();
            largeMap.addLayer(largeMarkers);
            
            // Crear círculo con el mismo radio que el mapa pequeño
            currentLargeCircle = L.circle(currentCenter, {
                color: 'orange',
                fillColor: 'rgba(255, 102, 0, 0.6)',
                fillOpacity: 0.4,
                radius: currentRadius * 1000
            }).addTo(largeMap);
            
            // Añadir los mismos marcadores que están visibles en el mapa pequeño
            actualizarMarcadoresGrandes();
            
            // Forzar redibujado del mapa grande
            setTimeout(function() {
                largeMap.invalidateSize();
            }, 100);
        }

        // Manejo del checkbox de búsqueda global
        function handleGlobalSearchChange() {
            var cb = document.getElementById('global-search-checkbox');
            var mapDiv = document.getElementById('osm-map');
            var distanceFieldset = document.getElementById('distance-fieldset');
            
            if (cb && cb.checked) {
                // Modo búsqueda global - OCULTAR el mapa
                if (mapDiv) mapDiv.style.display = 'none';
                if (distanceFieldset) distanceFieldset.style.display = 'none';
                
                // Eliminar círculos si existen
                if (currentSmallCircle && smallMap) {
                    smallMap.removeLayer(currentSmallCircle);
                    currentSmallCircle = null;
                }
                if (currentLargeCircle && largeMap) {
                    largeMap.removeLayer(currentLargeCircle);
                    currentLargeCircle = null;
                }
            } else {
                // Modo normal - MOSTRAR el mapa
                if (mapDiv) mapDiv.style.display = 'block';
                if (distanceFieldset) distanceFieldset.style.display = 'block';
                
                // Restaurar radio
                updateRadius();
            }
        }

        // Inicialización cuando el DOM esté listo
        // Asegurarse de que el evento onchange se asigne correctamente al checkbox
document.addEventListener('DOMContentLoaded', function() {
    // Crear mapa pequeño
    if (element) {
        smallMap = createSmallMap();
    }
    
    // Configurar evento para cerrar el modal
    var closeBtn = document.getElementById('close-map-modal');
    if (closeBtn) {
        closeBtn.addEventListener('click', function(e) {
            e.preventDefault();
            document.getElementById("mapModal").style.display = "none";
            
            // Destruir el mapa grande para liberar memoria
            if (largeMap) {
                largeMap.remove();
                largeMap = null;
            }
        });
    }
    
    // Añadir evento de cierre al botón de cerrar en la parte inferior
    var closeBottomBtn = document.getElementById('close-map-button-bottom');
    if (closeBottomBtn) {
        closeBottomBtn.addEventListener('click', function(e) {
            e.preventDefault();
            var modal = document.getElementById('mapModal');
            if (modal) {
                modal.style.display = "none";
            }
        });
    }
    
    // Añadir evento de cierre al hacer clic fuera del contenido del modal
    window.onclick = function(event) {
        var modal = document.getElementById('mapModal');
        if (event.target == modal) {
            modal.style.display = "none";
            
            // Destruir el mapa grande si existe
            if (largeMap) {
                largeMap.remove();
                largeMap = null;
            }
        }
    };
    
    // Configurar evento para el checkbox de búsqueda global
    // Configurar evento para el checkbox de búsqueda global
    // Se gestiona mediante onclick en el HTML para asegurar que siempre funciona
    // Pero ejecutamos la función una vez al inicio para establecer el estado correcto
    handleGlobalSearchChange();
    
    // Configurar evento para el selector de distancia
    var distanceSelect = document.getElementById('distance-select');
    if (distanceSelect) {
        distanceSelect.onchange = updateRadius;
    }
    
    // Escuchar mensajes de la página padre
    window.addEventListener('message', function(event) {
        if (event.data && event.data.action === 'mapHighlightUser') {
            const userId = event.data.userId;
            highlightMarkerOnMap(userId);
        }
    });
});

        // Estilos CSS
        var style = document.createElement('style');
        style.innerHTML = `
            .marker-cluster-custom {
                background: #e65f00 ;
                border-radius: 50%;
                text-align: center;
                color: white;
                font-weight: bold;
                font-size: 14px;
                line-height: 40px;
            }
            .marker-cluster-custom div {
                background: #e65f00 ;
                border-radius: 50%;
                width: 100%;
                height: 100%;
            }
            .user-card.active {
                border: 2px solid orange !important;
                box-shadow: 0 0 10px rgba(255,165,0,0.5) !important;
                background-color: #FFFACD !important;
            }
            .close {
                position: absolute;
                right: 25px;
                top: 0;
                color: #000;
                font-size: 35px;
                font-weight: bold;
                cursor: pointer;
            }
            .close:hover {
                color: red;
            }
            .leaflet-container a {
                color:#e65f00 !important;
            }
        `;
        document.head.appendChild(style);
    </script>
</form>
<script type="text/javascript">
            // Mostrar/ocultar checkbox y barra de precio según idioma y checkbox
            function togglePriceUI() {
                var input = document.querySelector('#lang-learn input[name="learns"]');
                var priceBar = document.getElementById('price-bar-container');
                var priceFilterBox = document.getElementById('price-filter-container');
                var priceFilterCheckbox = document.getElementById('price-filter-checkbox');
                var hasLang = input && input.value.trim() !== '';

                if (priceFilterBox) {
                    priceFilterBox.style.display = hasLang ? '' : 'none';
                }

                if (priceBar) {
                    if (hasLang && priceFilterCheckbox && priceFilterCheckbox.checked) {
                        priceBar.style.display = '';
                        // Inicializa valores del slider a los inputs si el slider existe
                        if (window.$ && $('#slider').length) {
                            var vals = $('#slider').slider('option', 'values');
                            var pmn = document.getElementById('price_min');
                            var pmx = document.getElementById('price_max');
                            if (pmn) pmn.value = vals[0];
                            if (pmx) pmx.value = vals[1];
                        }
                    } else {
                        priceBar.style.display = 'none';
                        // limpiar valores si no está activo
                        var pmn = document.getElementById('price_min');
                        var pmx = document.getElementById('price_max');
                        if (pmn) pmn.value = '';
                        if (pmx) pmx.value = '';
                    }
                }
            }

            var inputIdioma = document.querySelector('#lang-learn input[name="learns"]');
            if (inputIdioma) {
                inputIdioma.addEventListener('input', togglePriceUI);
                // Inicializar estado al cargar
                togglePriceUI();
            }
            var priceFilterCheckbox = document.getElementById('price-filter-checkbox');
            if (priceFilterCheckbox) {
                priceFilterCheckbox.addEventListener('change', togglePriceUI);
            }

        // Actualizar los inputs ocultos de precio según el slider SOLO si el checkbox está marcado
        document.addEventListener('DOMContentLoaded', function() {
            if (window.$ && $('#slider').length) {
                $('#slider').on('slidechange', function(event, ui) {
                    var cb = document.getElementById('price-filter-checkbox');
                    if (cb && cb.checked) {
                        document.getElementById('price_min').value = ui.values[0];
                        document.getElementById('price_max').value = ui.values[1];
                    }
                });
                // Inicializar con los valores actuales solo si el checkbox está marcado
                var sliderVals = $('#slider').slider('option', 'values');
                var cb = document.getElementById('price-filter-checkbox');
                if (cb && cb.checked) {
                    document.getElementById('price_min').value = sliderVals[0];
                    document.getElementById('price_max').value = sliderVals[1];
                } else {
                    document.getElementById('price_min').value = '';
                    document.getElementById('price_max').value = '';
                }
            }
        });
// Mapeo nombre -> código de idioma
var idiomasNombreACodigo = {};
var validCodes = {};
<?php
foreach ($idiomas_equiv as $codigo => $nombre) {
    // Escapamos comillas para JS
    $nombre_js = addslashes($nombre);
    $codigo_js = addslashes($codigo);
    echo "idiomasNombreACodigo['$nombre_js'] = '$codigo_js';\n";
    echo "validCodes['" . strtolower($codigo_js) . "'] = true;\n";
}
?>

function normalizaIdiomaNombre(str) {
    return (str || '')
        .toString()
        .toLowerCase()
        .replace(/\s*;.*$/, '') // quitar todo a partir de ; (p.ej., "Spanish; Castilian" -> "Spanish")
        .replace(/[^a-z]/g, '')   // dejar solo letras
        .trim();
}

function reemplazarNombrePorCodigo(inputId) {
    var input = document.querySelector('input[name="' + inputId + '"]');
    if (!input || !input.value) return;
    var val = input.value;
    
    // Excepción especial: catalan y valenciano siempre devuelven 'cat'
    var valLower = val.toLowerCase().trim();
    if (valLower === 'catalan' || valLower === 'valenciano' || valLower === 'valencian') {
        input.value = 'cat';
        return;
    }
    
    // 0) Si ya es un código válido, no hacer nada (evita re-mapping erróneo, ej: eng -> lsc por 'eng' en 'Llengua...')
    if (validCodes[valLower]) {
        return;
    }
    
    // 1) Coincidencia exacta
    if (idiomasNombreACodigo[val]) { input.value = idiomasNombreACodigo[val]; return; }

    // 2) Búsqueda flexible por nombre normalizado ("Spanish" -> "Spanish; Castilian")
    var normVal = normalizaIdiomaNombre(val);
    for (var key in idiomasNombreACodigo) {
        if (!Object.prototype.hasOwnProperty.call(idiomasNombreACodigo, key)) continue;
        var normKey = normalizaIdiomaNombre(key);
        if (normKey === normVal) {
            input.value = idiomasNombreACodigo[key];
            return;
        }
    }

    // 3) Fallback: si contiene el nombre buscado
    for (var key2 in idiomasNombreACodigo) {
        if (!Object.prototype.hasOwnProperty.call(idiomasNombreACodigo, key2)) continue;
        if (key2.toLowerCase().indexOf(val.toLowerCase()) !== -1) {
            input.value = idiomasNombreACodigo[key2];
            return;
        }
    }
}

// Al enviar el formulario, reemplazar los valores de los inputs de idioma
var searchForm = document.getElementById('search-form');
if (searchForm) {
    searchForm.addEventListener('submit', function(e) {
        reemplazarNombrePorCodigo('learns');
        reemplazarNombrePorCodigo('teaches');
        // Si tienes más campos de idioma, añade aquí
    });
}

document.addEventListener('DOMContentLoaded', function() {
    // Seleccionar automáticamente 'Price (only teachers) asc.' si se marca el filtro de precio y está en modo teacher
    var priceFilterCheckbox = document.getElementById('price-filter-checkbox');
    var orderSelect = document.querySelector("select[name='orderresultsby']");
    var radioTeachers = document.getElementById('radio-teachers');
    if (priceFilterCheckbox && orderSelect && radioTeachers) {
        priceFilterCheckbox.addEventListener('change', function() {
            if (priceFilterCheckbox.checked && radioTeachers.checked) {
                orderSelect.value = 'price_asc';
            }
            // Mostrar/ocultar el rango cuando se cambia el checkbox
            var priceBar = document.getElementById('price-bar-container');
            if (priceBar) priceBar.style.display = priceFilterCheckbox.checked ? '' : 'none';
        });
        // Se elimina la asignación automática al cargar para no sobrescribir la selección del usuario
    }

    // Aplicar el valor que vino del servidor para mantener la selección tras submit
    if (orderSelect && '<?php echo isset($_GET['orderresultsby'])? addslashes($_GET['orderresultsby']) : ''; ?>' !== '') {
        orderSelect.value = '<?php echo isset($_GET['orderresultsby'])? addslashes($_GET['orderresultsby']) : ''; ?>';
    } else {
        // Only set default if NO mapping exists and value is empty.
        // If orderSelect.value is already 'distance' (logic from PHP), DO NOT Change it.
        // Actually, PHP handles 'selected'. If value is '', it means nothing selected?
        // But the first option is usually selected if nothing else.
        // We only want to force 'lastlogin' if valid GET is NOT present.
        // PHP Default is already lastlogin.
        
        // Let's just Trust PHP.
        // if (orderSelect && (orderSelect.value === '' || orderSelect.value === 'distance')) orderSelect.value = 'lastlogin';
    }

    // Function to handle global search visibility
    window.handleGlobalSearchChange = function() {
             var isChecked = document.getElementById('global-search-checkbox').checked;
             var mapDiv = document.getElementById('osm-map');
             var distFs = document.getElementById('distance-fieldset');
             var mapHr = document.getElementById('map-hr');
             
             if (isChecked) {
                 if(mapDiv) mapDiv.style.display = 'none';
                 if(distFs) distFs.style.display = 'none';
                 if(mapHr) mapHr.style.display = 'none';
             } else {
                 if(mapDiv) mapDiv.style.display = 'block';
                 if(distFs) distFs.style.display = 'block';
                 if(mapHr) mapHr.style.display = 'block';
             }
    };
    // Initialize on load
    handleGlobalSearchChange();

    // Persistir estado del checkbox y de los inputs del rango
    var priceBar = document.getElementById('price-bar-container');
    var priceFilterBox = document.getElementById('price-filter-container');
    var learnInput = document.querySelector('#lang-learn input[name="learns"]');
    // Mostrar el contenedor del checkbox solo si el partner es teacher y existe un idioma seleccionado
    var partnerTeacher = (document.querySelector('input[name="partner"][value="teacher"]') && document.querySelector('input[name="partner"][value="teacher"]').checked) || (window.location.search.indexOf('partner=teacher') !== -1);
    var hasLang = (learnInput && learnInput.value.trim() !== '') || <?php echo (!empty($_GET['learns'])? 'true' : 'false'); ?>;
    if (priceFilterBox) {
        priceFilterBox.style.display = (partnerTeacher && hasLang) ? '' : 'none';
    }
    if (priceBar) {
        var cb = document.getElementById('price-filter-checkbox');
        if (cb && cb.checked) {
            priceBar.style.display = '';
        } else {
            priceBar.style.display = 'none';
        }
    }

    // Rellenar los inputs del slider desde $_GET si existen (seguridad extra)
    var pmin = '<?php echo isset($_GET['price_min'])? intval($_GET['price_min']) : ''; ?>';
    var pmax = '<?php echo isset($_GET['price_max'])? intval($_GET['price_max']) : ''; ?>';
    if (pmin !== '') document.getElementById('price_min').value = pmin;
    if (pmax !== '') document.getElementById('price_max').value = pmax;
});
</script>
