<!DOCTYPE html><html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>jQuery UI Range slider</title>
<!--  <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.3/themes/base/jquery-ui.css" /> -->
  <link rel="stylesheet" href="jquery-ui-1.13.3.custom/jquery-ui.css" />
  <link rel="stylesheet" href="/resources/demos/style.css" />
  <link rel="stylesheet" href="widgets.css" />
  <link rel="stylesheet" href="lingua2general.css" />
<style>
/* .hidden { display: none; background: lightgray; padding: .5em; } */
.hidden { display: none; background: yellow; border-style: dashed; border-color: red; border-width: 1px; }
.student-dialog { background-color: pink; }
.teacher-dialog { background-color: cyan; }
.center { text-align: center; }
#frame { font-size: .9em; /*width: 240px;*/ }
select { width: 100%; height: 32px; }

.ui-slider-range { background: #e65f00; }
.ui-slider { font-size: .7em; }
.ui-slider .ui-slider-handle { background: #e65f00; }
// #slider-range .ui-slider-handle { width: .8em; }
.ui-slider .ui-corner-all { border-radius: 1em; }

#amount { font-weight: bold; color: #e65f00; }
#slider-range .ui-slider-range { background: #e65f00; }
#slider-range { font-size: .7em; }
#slider-range .ui-slider-handle { background: #e65f00; }
// #slider-range .ui-slider-handle { width: .8em; }
#slider-range .ui-corner-all { border-radius: 1em; }
</style>
<!-- <script src="https://code.jquery.com/jquery-3.7.1.js"></script> -->
<script src="jquery-ui-1.13.3.custom/external/jquery/jquery.js"></script>
<!-- <script src="https://code.jquery.com/ui/1.13.3/jquery-ui.js"></script> -->
<script src="jquery-ui-1.13.3.custom/jquery-ui.js"></script>
<script src="https://unpkg.com/leaflet@1.6.0/dist/leaflet.js"></script>
<link href="https://unpkg.com/leaflet@1.6.0/dist/leaflet.css" rel="stylesheet" />
<?php
require_once('jQueryWidgets.php');
require_once('idiomasequivalencias.php');
?>
</head>
<body>
 
<div class='container ui-widget-content'>
<nav>
<form id='frame'> 
<script>
	$( function() {
		$( "#slider-range" ).slider({
			range: true,
			min: 1,
			max: 30,
			step: 1,
			values: [ 8, 20 ],
			slide: function( event, ui ) {
				$( "#minPrice" ).val( ui.values[ 0 ] );
				$( "#maxPrice" ).val( ui.values[1] == $("#slider-range").slider("option", "max") ? "" : ui.values[1] );
				document.getElementById("amount").textContent =
					ui.values[0] +
					(ui.values[1] == $("#slider-range").slider("option", "max") ? " or more" : " – " + ui.values[1]);
			}
		});
		document.getElementById("amount").textContent=( $( "#slider-range" ).slider( "values", 0 ) + " – " + $( "#slider-range" ).slider( "values", 1 ));
		$( "#minPrice" ).val( $( "#slider-range" ).slider( "values", 0 ));
		$( "#maxPrice" ).val( $( "#slider-range" ).slider( "values", 1 ));
	});
</script>
<legend class='center'>Price range (€)<br /><span id='amount'></span></legend>
 
<div id="slider-range"></div>
 
<div class='hidden'>
	<label for="minPrice">from:</label>
	<input type="text" name="minPrice" id="minPrice" />
	<label for="maxPrice">to:</label>
	<input type="text" name="maxPrice" id="maxPrice" />
</div>
<!--<div class='ctrlgrpv'> -->
<fieldset id='language-partner'>
	<legend for='language-partner'>I'm looking for:</legend>
	<div>
	<label class="container">Professional teachers
		<input type='radio' name='partner' value='teacher' checked='checked' />
		<span class="checkmark"></span>
	</label>
	<label class="container">Students
		<input type='radio' name='partner' value='student' />
		<span class="checkmark"></span>
	</label>
	</div>
</fieldset>
<!-- <script>$( "#language-partner" ).controlgroup({"direction": "vertical"});</script>
-->
<!-- </div> -->
<fieldset><legend>Language I want to learn</legend>
	<div>
<!--		<label >Language I want to learn: -->
		<select name='learns'>
			<option value=''>(Select one)</option>
			<?php listIterator($idiomas_equiv, 'asOption'); ?>
		</select>
<!--	</label> -->
	</div>
	<div class='teacher-dialog'>
		<div>
			<label>Price range (€/h)
			<?php rangeSlider('slider', 'precio'); ?>
			</label>
		</div>
	</div>
</fieldset>
<fieldset class='student-dialog'>
	<legend>Language I want to teach:</legend>
	<select name='teaches'>
		<option value=''>(Select one)</option>
		<?php listIterator($idiomas_equiv, 'asOption'); ?>
	</select>
	<label>Student's level
	<?php
		require_once('niveles.php');
		selectSlider('slider1', 'level', $lista_niveles, 0, -2);
	?>
	</label>
</fieldset>
<fieldset id='experience-level'>
	<legend for='experience-level'>Experience level</legend>
	<div id='controlgroup'>
	<select id='exp-level' name='experience-level'>
		<option value="">(Any)</option>
		<option value="3">3 years</option>
		<option value="4">4 years</option>
		<option value="5">5 years or more</option>
	</select>
	</div>
<!--	<script>$( "#controlgroup" ).controlgroup({"direction": "vertical"});</script> -->
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
<!--	<script>$( "#gender" ).controlgroup({"direction": "vertical"});</script> -->
</fieldset>
<fieldset>
	<label class='container'>In zone:
		<input type='checkbox' name='zone' />
		<span class="checkmark"></span>
	</label>
	<div id="osm-map"></div>
</fieldset>
<script>
// Where you want to render the map.
var element = document.getElementById('osm-map');

// Height has to be set. You can do this in CSS too.
element.style = 'height:220px;';

// Create Leaflet map on map element.
var map = L.map(element);

// Add OSM tile layer to the Leaflet map.
L.tileLayer('http://{s}.tile.osm.org/{z}/{x}/{y}.png', {
    attribution: '&copy; <a href="http://osm.org/copyright">OpenStreetMap</a> contributors'
}).addTo(map);

// Target's GPS coordinates.
var target = L.latLng('41.38879', '2.15899');

// Set map's center to target with zoom 14.
map.setView(target, 10);

// Place a marker on the same location.
//L.marker(target, 'draggable').addTo(map);
L.circleMarker(target).addTo(map);
</script>
<input class="ui-button ui-widget ui-corner-all" style='width: 100%' type="submit" value="Search" />
</form>
</nav>
<section class='marco-fichas'>
<h3>Resultados</h3>
<?php for ($i = 1; $i <= 30; $i++): ?>
	<div class='ficha'>
	<div><?php echo "$i. "; ?>Aquí se coloca la sección principal.</div>
	<div>Texto corto</div>
	</div>
<?php 	endfor; ?>
</section>
<section>
<?php // phpInfo( 32 ); ?> 
</section>
</div>
 
<?php //phpInfo( 32 );> 
// enableCheckboxRadio();
?>
</body></html>
