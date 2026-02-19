<?php
// Inicializar el array
$idiomas_equiv = array();

// Verificar si ya existe la conexión a la base de datos
if (!isset($link) || !$link) {
    // Si no existe, incluir el archivo de conexión
    require_once('../files/bd.php');
}

// Consulta a la tabla languages_names
$query_languages = "SELECT Id, Print_Name FROM languages_names WHERE 1 ORDER BY Print_Name";
$result_languages = mysqli_query($link, $query_languages);

// Verificar si la consulta fue exitosa
if ($result_languages && mysqli_num_rows($result_languages) > 0) {
    // Llenar el array con los resultados de la consulta
    while ($row = mysqli_fetch_assoc($result_languages)) {
        $idiomas_equiv[$row['Id']] = $row['Print_Name'];
    }
} else {
    // Si la consulta falla o no hay resultados, usar el array original como respaldo
    $idiomas_equiv = array(	 
        "af"=>"Afrikaans",
        "sq"=>"Albanian",
        "am"=>"Amharic",
        "ar"=>"Arabic",
        "hy"=>"Armenian",
        "az"=>"Azerbaijani",
        "eu"=>"Basque",
        "be"=>"Belarusian",
        "bn"=>"Bengali",
        "bh"=>"Bihari",
        "bs"=>"Bosnian",
        "br"=>"Breton",
        "bg"=>"Bulgarian",
        "km"=>"Cambodian",
        "ca"=>"Catalan",
        "zh"=>"Chinese",
        "co"=>"Corsican",
        "hr"=>"Croatian",
        "cs"=>"Czech",
        "da"=>"Danish",
        "nl"=>"Dutch",
        "en"=>"English",
        "eo"=>"Esperanto",
        "et"=>"Estonian",
        "fo"=>"Faroese",
        "tl"=>"Filipino",
        "fi"=>"Finnish",
        "fr"=>"French",
        "fy"=>"Frisian",
        "gl"=>"Galician",
        "ka"=>"Georgian",
        "de"=>"German",
        "el"=>"Greek",
        "gn"=>"Guarani",
        "gu"=>"Gujarati",
        "iw"=>"Hebrew",
        "hi"=>"Hindi",
        "hu"=>"Hungarian",
        "is"=>"Icelandic",
        "id"=>"Indonesian",
        "ga"=>"Irish",
        "it"=>"Italian",
        "ja"=>"Japanese",
        "jw"=>"Javanese",
        "kk"=>"Kazakh",
        "ko"=>"Korean",
        "ku"=>"Kurdish",
        "ky"=>"Kyrgyz",
        "lo"=>"Laothian",
        "la"=>"Latin",
        "lv"=>"Latvian",
        "ln"=>"Lingala",
        "lt"=>"Lithuanian",
        "mk"=>"Macedonian",
        "ms"=>"Malay",
        "ml"=>"Malayalam",
        "mt"=>"Maltese",
        "mi"=>"Maori",
        "mr"=>"Marathi",
        "mn"=>"Mongolian",
        "ne"=>"Nepali",
        "no"=>"Norwegian",
        "nn"=>"Norwegian",
        "oc"=>"Occitan",
        "or"=>"Oriya",
        "ps"=>"Pashto",
        "fa"=>"Persian",
        "pl"=>"Polish",
        "pt"=>"Portuguese",
        "pa"=>"Punjabi",
        "qu"=>"Quechua",
        "ro"=>"Romanian",
        "rm"=>"Romansh",
        "ru"=>"Russian",
        "gd"=>"Sc.Gaelic",
        "sr"=>"Serbian",
        "sh"=>"Serbocroat.",
        "st"=>"Sesotho",
        "sn"=>"Shona",
        "sd"=>"Sindhi",
        "si"=>"Sinhalese",
        "sk"=>"Slovak",
        "sl"=>"Slovenian",
        "so"=>"Somali",
        "es"=>"Spanish",
        "su"=>"Sundanese",
        "sw"=>"Swahili",
        "sv"=>"Swedish",
        "tg"=>"Tajik",
        "ta"=>"Tamil",
        "tt"=>"Tatar",
        "te"=>"Telugu",
        "th"=>"Thai",
        "ti"=>"Tigrinya",
        "to"=>"Tonga",
        "tr"=>"Turkish",
        "tk"=>"Turkmen",
        "tw"=>"Twi",
        "ug"=>"Uighur",
        "uk"=>"Ukrainian",
        "ur"=>"Urdu",
        "uz"=>"Uzbek",
        "vi"=>"Vietnamese",
        "cy"=>"Welsh",
        "xh"=>"Xhosa",
        "yi"=>"Yiddish",
        "yo"=>"Yoruba",
        "zu"=>"Zulu"
    );
}

/* listIterator($idiomas_equiv, 'asOption');
listIterator($idiomas_equiv, 'asJson', ",");
echo "\n";
 */		
?>