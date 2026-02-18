<?php
require_once('search-parser.php');
require_once('jQueryWidgets.php');
require_once('geoMath.php');

$mapBounds = Array();

?>
	<div>
	<div id='mapContent'>
		<div class='botonera btn4'>
		<span><span
			class='ui-button ui-widget ui-corner-all'
			title='Zoom in'
			onclick='mapZoomIn()'>
			<i class='fas fa-magnifying-glass-plus'></i>
		</span></span><!--
		--><span><span
			class='ui-button ui-widget ui-corner-all'
			title='Zoom out'
			onclick='mapZoomOut()'>
			<i class='fas fa-magnifying-glass-minus'></i>
		</span></span><!--
		--><span><span
			class='ui-button ui-widget ui-corner-all'
			title='Center at me'
			onclick='mapCenter()'>
			<i class='fas fa-crosshairs'></i>
		</span></span><!--
		--><span><span
			class='ui-button ui-widget ui-corner-all'
			title='Show all'
			onclick='mapViewAll()'>
			<i class='fas fa-location-crosshairs'></i>
		</span></span>
		</div>
		<div id="osm-map" class='center'></div>
	</div> <!-- FIN #mapContent -->
<!--	<div class='botonera5' style='font-size: .8em; text-align: center; margin-bottom: .2em;'> -->
	<div class='botonera btn5'>
	<?php
	foreach($geoQueryDist as $value):
		radioWidget(["geoQuery" => "dist"], "$value", "r$value");
		echo "<span><label for='r$value'>$value</label></span>";
	endforeach; ?>
	</div>
	<pre class='hidden'><?php
		// Calculamos las regiones de búsqueda para cada radio:
		$lat = $userLatLng[0];
		$lng = $userLatLng[1];
		$cos_lat = cos(deg2rad($userLatLng[0]));
		foreach($geoQueryDist as $dist):
			[ $dLat, $dLng ] = deltaLatCosLng($dist, $cos_lat);
			$mapBounds[$dist] = [
				[ $lat - $dLat, $lng - $dLng ],
				[ $lat + $dLat, $lng + $dLng ],
			];
			printf("%3dkm: Δθ=%5.3f´ Δλ=%5.3f´\n",
				$dist, $dLat, $dLng);
		endforeach;
	?></pre>
	<fieldset class='hidden' style='background-color:cyan;'>
		<!-- Inputs disabled por ahora, porque no guardan valores de mapa.
		     cambiar por readonly='readonly' para que se transmitan. -->
		<label>N
			<input type='text' id='mapN' name='geoQuery[N]'
				value='<?="{$request['geoQuery']['N']}"?>'
				disabled='disabled'  />
		</label>
		<label>S
			<input type='text' id='mapS' name='geoQuery[S]'
				value='<?="{$request['geoQuery']['S']}"?>'
				disabled='disabled'  />
		</label>
		<label>W
			<input type='text' id='mapW' name='geoQuery[W]'
				value='<?="{$request['geoQuery']['W']}"?>'
				disabled='disabled'  />
		</label>
		<label>E
			<input type='text' id='mapE' name='geoQuery[E]'
				value='<?="{$request['geoQuery']['E']}"?>'
				disabled='disabled'  />
		</label>
	</fieldset>
<script type='text/javascript'>
//<![CDATA[
// Where you want to render the map.
var element = document.getElementById('osm-map');

// Height has to be set. You can do this in CSS too.
//element.style = 'height:215px; width:215px';
element.style = 'height:250px; width:250px';
//element.style = 'height:205px; width:205px;';

// Create Leaflet map on map element.
var map = L.map(element, { zoomSnap: 0.1, zoomControl: false });


// Add OSM tile layer to the Leaflet map.
/* L.tileLayer('http://{s}.tile.osm.org/{z}/{x}/{y}.png', {
    attribution: '©<a href="http://osm.org/copyright">OpenStreetMap</a>'
}).addTo(map); 
var CartoDB_Positron = 
L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
	attribution: '&copy;<a href="https://www.openstreetmap.org/copyright"><abbr title="OpenStreetMap">OSM</abbr></a> c. &copy;<a href="https://carto.com/attributions">CARTO</a>',
	subdomains: 'abcd',
	maxZoom: 20
}).addTo(map);
var CartoDB_Voyager = 
L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
	attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors &copy; <a href="https://carto.com/attributions">CARTO</a>',
	subdomains: 'abcd',
	maxZoom: 20
}).addTo(map);
var Esri_WorldTopoMap = */
L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Topo_Map/MapServer/tile/{z}/{y}/{x}', {
	attribution: '<abbr title="Tiles © Esri — Esri, DeLorme, NAVTEQ, TomTom, Intermap, iPC, USGS, FAO, NPS, NRCAN, GeoBase, Kadaster NL, Ordnance Survey, Esri Japan, METI, Esri China (Hong Kong), and the GIS User Community">Tiles © Esri et al.</abbr>'
}).addTo(map);

// Target's GPS coordinates.
var target = L.latLng('<?= $userLatLng[0] ?>',
	'<?=$userLatLng[1]?>');

// Set map's center to target with zoom 10.
//map.setView(target, 10);
var boundsMin = <?="[[{$mapBounds[5][0][0]}, {$mapBounds[5][0][1]}], " .
		" [{$mapBounds[5][1][0]}, {$mapBounds[5][1][1]}]]"?>;
var bounds = <?php
	$dist = $request['geoQuery']['dist'];
	echo	"[[{$request['geoQuery']['N']}, {$request['geoQuery']['W']}], " .
		" [{$request['geoQuery']['S']}, {$request['geoQuery']['E']}]]";
//	echo	"[[{$mapBounds[$dist][0][0]}, {$mapBounds[$dist][0][1]}], " .
//		" [{$mapBounds[$dist][1][0]}, {$mapBounds[$dist][1][1]}]]";
?>;
//<!--<?php print_r($dist); ?>-->
L.rectangle(boundsMin, {weight: 1, fill: false}).addTo(map);
L.rectangle(bounds, {color: "black", weight: 1, fill: false}).addTo(map);
//setDistanceBoundingBox(<?= $dist ?>);
map.setMinZoom(map.getBoundsZoom(distanceBoundingBox(target, 100)));
setDistanceBoundingBox(100);
L.circle(target, {radius: 1e5, className: 'circDistancia'}).addTo(map);
//Era:
//map.fitBounds(bounds);
// var initialZoom = map.getZoom();
//var minZoom = map.getZoom();
//map.setMinZoom(minZoom);
//var maxBounds = map.getBounds().pad(.001);
//map.setMaxBounds(maxBounds);

// Place a marker on the same location.
//L.marker(target, 'draggable').addTo(map);
const boundingCircle = L.circle(target, {radius: 0,
		className: 'circDistancia maximo'}).addTo(map);
<?php
$dist = $request['geoQuery']['dist'] * 200;
for ($i = 1; $i <= 10; $i++): ?>
	L.circle(target, {radius: <?= $i * $dist;
	?>, className: 'circDistancia'}).addTo(map);
<?php endfor; ?>
L.control.scale({imperial: false}).addTo(map);

function radians(degrees) {
	return degrees * Math.PI / 180.;
}
function degrees(radians) {
	return radians * 180. / Math.PI;
}
function distanceBoundingBox(target, dist) {
	const dRads = 5e-5 * Math.PI * dist;    // km -> rad
	const dLat  = 1e-4 * 90 * dist; // km -> deg
	const dLng  = degrees(Math.asin(Math.sin(dRads)) /
		Math.cos(radians(target.lat)));
	return L.latLngBounds(
		L.latLng(target.lat - dLat, target.lng - dLng),
		L.latLng(target.lat + dLat, target.lng + dLng)
	);
}
function setDistanceBoundingBox(dist) {
	bounds = distanceBoundingBox(target, dist);
	map.fitBounds(bounds);
//	map.setMinZoom(map.getZoom() - .5);
//	map.setMaxBounds(map.getBounds().pad(.04));
	map.setMaxBounds(bounds.pad(.05));
}
function mapZoomIn() { map.zoomIn(); }
function mapZoomOut() { map.zoomOut(); }
function mapCenter() {
	map.flyTo(target);
//	map.panTo(target);
}
function mapViewAll() { map.fitBounds(bounds); }
function scrollToFicha(id) {
	document.getElementById('ficha'+id)
		.scrollIntoView({ behavior: 'smooth'});
}
function mapAddPoint(id, lat, lng, classes) {
	L.circleMarker(L.latLng(lat, lng), {radius: 4, className: classes})
		.on('add', function() {
			this.getElement().id="mk"+id;
			mapObserver
				.observe(document.getElementById("mk"+id));
			fichaObserver
				.observe(document.getElementById("ficha"+id));
		})
		.on('click', function() {
            // --- NUEVO: Buscar la página del usuario y navegar ---
            const params = new URLSearchParams(window.location.search);
            params.set('user_id', id);
            fetch('get-user-page.php?' + params.toString())
                .then(response => response.json())
                .then(data => {
                    if (data.page) {
                        // Cambia la página y añade un hash para saber a quién hacer scroll
                        params.set('page', data.page);
                        params.set('focus_user', id);
                        window.location.search = params.toString();
                    } else {
                        alert('Usuario no encontrado en los resultados actuales.');
                    }
                });
        })
		.addTo(map);
}
/*function mapAddFicha(id) {
	fichaObserver.observe(document.getElementById("ficha"+id));
}*/
// OBSERVADOR DE INTERSECCIÓN DE PUNTOS
const mapObserverCallback = (entries) => {
	entries.forEach((entry) => {
		fichaId = "ficha" + entry.target.id.substring(2);
		const clases = document.getElementById(fichaId).classList;
		if (entry.isIntersecting) {
			document.getElementById('marco').classList
				.add('noBugSVG'); /* Chapuza para bug Chrome */
			clases.remove('outMap');
		}
		else {
			clases.add('outMap');
		}
	});
}
const mapObserver = new IntersectionObserver(
	mapObserverCallback,
	{ root: document.getElementById('osm-map'), threshold: 0.2 }
);

// OBSERVADOR DE INTERSECCIÓN DE FICHAS
const fichaObserverCallback = (entries) => {
	entries.forEach((entry) => {
		mkId = "mk" + entry.target.id.substring(5);
		if (entry.isIntersecting) {
			document.getElementById(mkId).classList
				.add('visibleFicha');
		}
		else {
			document.getElementById(mkId).classList
				.remove('visibleFicha');
		}
	});
}
const fichaObserver = new IntersectionObserver(
	fichaObserverCallback,
	{ root: null, threshold: .2 }
);

//]]>
</script>
</div>

