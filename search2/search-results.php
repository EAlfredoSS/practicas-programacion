<?php
require_once 'dbConnectionBegin.php';
require_once 'search-parser.php';
require_once 'fichas.php';
//require_once('lista-usuarios.php'); //PROVISIONAL: cambiar por query results
//require_once 'aux/registros.php'; //PROVISIONAL: íd
require_once 'geoMath.php';

$Limit = 60;

$queryString = searchMySQLString();
//echo $queryString;

function showFichas($mapa="") {
	global $request, $db_connection, $queryString, $Limit;
	if ($queryString == null) return;
	$resultado = mysqli_query($db_connection, $queryString);
	$continua = false; // Determina si hay más páginas que mostrar
	$iRegistro = 0;
	$lastDist = 0;
	while ($registro = mysqli_fetch_assoc($resultado)):
		$iRegistro++;
		if ($iRegistro > $Limit): // Quedan registros por mostrar
			$continua = true;
	       		break;
		endif;
		$registro['class'] = isset($registro['level']) ?
			"{$registro['level']}" : "A0";
		$registro['latLng'] = json_decode($registro['latLng'], true);
		$registro['comm'] =
			json_decode($registro['comm'], true);
		$registro['teach'] =
			json_decode($registro['teach'], true);
		$registro['learn'] =
			json_decode($registro['learn'], true);
		$lastDist = $registro['distance'];
//		var_dump($registro);
		muestraFicha($registro);
		if (!in_array('worldwide', $request['geoQuery'], true))
			muestraPunto($registro, $mapa);
	endwhile;
	mysqli_free_result($resultado);
	if ($continua):
		if (isset($request['page'])):
			$request['page'] = $request['page'] + 1;
		else:
			$request['page'] = 1;
		endif;
		$more_div = "page_" . $request['page'];
		$more_url = "?" . http_build_query($request);
		$more_ajax_url = "ajax-search-results" . $more_url;
	?>
	<div id='<?= $more_div ?>'>
		<div class='marcoFicha' style='cursor: pointer'
			onclick='ajax_load_more_fichas("#<?= $more_div ?>",
			"<?= "$more_ajax_url" ?>")'>
		<hr />
		Do you want to download more?
		<a class='hidden' href='<?= $more_url ?>'>More cards</a>
		<hr />
		</div><?php
	// Se actualiza el radio del mapa
		if (!in_array('worldwide', $request['geoQuery'], true) &&
			$lastDist != 0):
			$dist = $lastDist;
		?><script type='text/javascript'>
			boundingCircle.setRadius(<?= $dist * 1000 ?>);
			setDistanceBoundingBox(<?= $dist ?>);
		</script><?php
		endif; ?>
	</div>
<?php
	else:
?>
	<div>
		<div class='marcoFicha'>
			<hr />
			Fin de datos
			<hr />
		</div><?php
	// Se actualiza el radio del mapa
		if (!in_array('worldwide', $request['geoQuery'], true)):
			$dist = $request['geoQuery']['dist'];
		?><script type='text/javascript'>
			boundingCircle.setRadius(<?= $dist * 1000 ?>);
			setDistanceBoundingBox(<?= $dist ?>);
		</script><?php
		endif; ?>
	</div>
<?php	endif;
}

function muestraPunto($registro, $mapa) { 
	global $userLatLng;
	if (!isset($registro['latLng'])) return;
	$lat = $registro['latLng'][0];
	$lng = $registro['latLng'][1];
	if (abs($lng - $userLatLng[1]) > 180.)
		$lng += 360 * ($userLatLng[1] <=> 0);
	$class = isset($registro['class']) ?
		"'{$registro['class']}'" : "''";
?><script type='text/javascript'>mapAddPoint(<?=
	"{$registro['id']},$lat,$lng,$class"
?>);</script>
<?php
}

function searchMySQLString() {
	global $request, $userId, $userLatLng, $Limit;
	$sql = [
		'select'=> array(),
		'from'	=> array(),
		'on'	=> array(),
		'where'	=> array(),
		'groupBy' => array(),
		'having'  => array(),
		'orderBy' => array(),
	];
	// Lo básico
	$sql['select'][] = [ 0, "a.id, a.name AS nombre", ];
	$sql['select'][] = [ 0, "a.area", ];
	$sql['from'][]   = [ 0, "users_v a", ];
	$sql['where'][]  = [ 0, "a.id <> '$userId'", ];
	$sql['orderBy'][] = [ 5, "a.id" ];
	// En algunas lenguas hay diferencias entre sexo del hablante
	// En campo gender: FALSE: mujer; TRUE: varón; NULL: no declarado
	if (in_array('male', $request['gender'], true)):
		if (!in_array('female', $request['gender'], true)):
			$sql['where'][] = [ 4, "gender" ];
		endif;
	else:
		if (!in_array('male', $request['gender'], true)):
			$sql['where'][] = [ 4, "NOT gender" ];
		endif;
	endif;
	// Busco quien enseñe
	$sql['from'][]   = [ 1, "NATURAL JOIN my_langs m", ];
	$sql['where'][]	 = [ 1, "m.lang_id = '{$request['learns']}'", ];
	// Caso prof. profesional
	if ($request['partner'] == 'teacher'):
		$sql['select'][] = [ 1, "m.lang_price AS price", ];
		$sql['where'][]  = [ 1, "m.lang_price IS NOT NULL", ];
	// Caso no profesional
	else:
		$sql['select'][] = [ 2, "l.level_name AS level", ];
		$sql['from'][]  = [ 4, "JOIN my_learn_langs l", ];
		$sql['on'][]    = [ 1, "a.id = l.id", ];
		$sql['where'][] = [ 1, "m.for_share", ];
		$sql['where'][] = [ 1, "l.lang_id = '{$request['teaches']}'", ];
	endif; 
	// Qué quiero enseñar/compartir
	$sql['select'][] = [ 2, "teach", ];
	$sql['from'][]   = [ 2, "NATURAL LEFT JOIN teach_langs_json", ];
	// Qué quiero aprender/practicar/mejorar
	$sql['select'][] = [ 2, "learn", ];
	$sql['from'][]   = [ 2, "NATURAL LEFT JOIN learn_langs_json", ];
	// Tenemos que entendernos, ¿no? -> Lenguas en común
	if (isset($request['noComm'])): // Busco a cualquiera: informar qué hablan
		$sql['select'][] = [ 3, "comm", ];
		$sql['from'][]   = [ 3, "NATURAL JOIN my_langs_json", ];
	else:		// Busco sólo a los que puedo entender: informar en qué
		$sql['select'][] = [ 3, "comm", ];
		$sql['from'][]   = [ 3, "NATURAL JOIN my_comm_langs_json", ];
		$sql['on'][]     = [ 3, "with_id = c.id" ];
	endif;
	if (is_array($userLatLng)):
		$sql['select'][] = [ 3,
			"latLngDist(\n" .
			"\tb.latitude,\n\tb.longitude,\n" .
			"\tc.latitude,\n\tc.longitude\n" .
			") AS distance",
		];
		if (!in_array('worldwide', $request['geoQuery'], true)):
			$sql['select'][] = [ 2, 'b.latLng' ];
		endif;
		$sql['where'][] = [ 0, "c.id = '$userId'", ];
		if (in_array('worldwide', $request['geoQuery'], true)):
       			$sql['from'][] = [ 2,
				"NATURAL LEFT JOIN coordinates b", 
			];
       			$sql['from'][] = [ 5,
				"JOIN coordinates c",
			];
		else:
			$sql['from'][] = [ 2,
				"NATURAL JOIN coordinates b",
			];
			$sql['from'][] = [ 5,
				"JOIN coordinates c",
			];
			$sql['where'][] = [ 3,
				"b.latitude BETWEEN\n" .
		       		"\t{$request['geoQuery']['S']} AND\n" .
				"\t{$request['geoQuery']['N']}",
			];
			$west = normalizeDeg180($request['geoQuery']['W']);
			$east = normalizeDeg180($request['geoQuery']['E']);
			$sql['where'][] = [ 4, "b.longitude" .
				(($west > $east) ? " NOT" : "") . 
				" BETWEEN\n\t$west AND\n\t$east",
			];
			$sql['having'][] = [ 1,
				"distance <= {$request['geoQuery']['dist']}",
			];
			$sql['orderBy'][] = [ 1, "distance", ];
		endif;
	else:
		if (!in_array('worldwide', $request['geoQuery'], true))
			return null;
	endif;

	foreach ($sql as $key => &$value):
		usort ($value, function ($a, $b) {return $a[0] <=> $b[0];});
	endforeach; 
	$sqlStr = "";
	$sqlStr .= concatena_sql("SELECT", ",\n", $sql['select']) . "\n";
	$sqlStr .= concatena_sql("FROM", "\n", $sql['from']) . "\n";
	$sqlStr .= concatena_sql("ON", "\nAND ", $sql['on']) . "\n";
	$sqlStr .= concatena_sql("WHERE", "\nAND ", $sql['where']) . "\n";
	$sqlStr .= concatena_sql("GROUP BY", ",\n", $sql['groupBy']) . "\n";
	$sqlStr .= concatena_sql("HAVING", "\nAND ", $sql['having']) . "\n";
	$sqlStr .= concatena_sql("ORDER BY", ",\n", $sql['orderBy']) . "\n";
	$sqlStr .= "LIMIT " . ($Limit + 1) . "\n";
	if (isset($request['page'])):
		$sqlStr .= "OFFSET " . $request['page'] * $Limit . "\n";
	endif;
//	print_r ($sqlStr);
	return $sqlStr;
}

function concatena_sql($sqlClause, $separador, $elems) {
	$long = sizeof($elems);
	if ($long == 0) return "";
	$valores = array();
	for ($i = 0; $i < $long; $i++)
		$valores[$i] = $elems[$i][1];
	return "$sqlClause\n" . implode($separador, $valores);
}
?>
<script>
window.addEventListener('DOMContentLoaded', function() {
    const params = new URLSearchParams(window.location.search);
    const focusUser = params.get('focus_user');
    if (focusUser) {
        const ficha = document.getElementById('ficha' + focusUser);
        if (ficha) {
            ficha.scrollIntoView({ behavior: 'smooth' });
            ficha.classList.add('highlight-ficha');
            setTimeout(() => ficha.classList.remove('highlight-ficha'), 3000);
        }
    }
});
</script>
<style>
.highlight-ficha {
    box-shadow: 0 0 0 4px #ff9800;
    transition: box-shadow 0.5s;
}
</style>

