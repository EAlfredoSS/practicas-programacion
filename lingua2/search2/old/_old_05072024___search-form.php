<?php
require_once('jQueryWidgets.php');
require_once('idiomasequivalencias.php');
?>
<form id='frame'> 
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
<script type='text/javascript'>
//<![CDATA[
// Where you want to render the map.
var element = document.getElementById('osm-map');

// Height has to be set. You can do this in CSS too.
element.style = 'height:220px;';

// Create Leaflet map on map element.
var map = L.map(element);

// Add OSM tile layer to the Leaflet map.
L.tileLayer('http://{s}.tile.osm.org/{z}/{x}/{y}.png', {
    attribution: '(c)<a href="http://osm.org/copyright">OpenStreetMap</a> contributors'
}).addTo(map);

// Target's GPS coordinates.
var target = L.latLng('41.38879', '2.15899');

// Set map's center to target with zoom 14.
map.setView(target, 10);

// Place a marker on the same location.
//L.marker(target, 'draggable').addTo(map);
L.circleMarker(target).addTo(map);
//]]>
</script>
<input class="ui-button ui-widget ui-corner-all" style='width: 100%' type="submit" value="Search" />
</form>
