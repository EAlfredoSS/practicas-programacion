<?php
require_once('jQueryWidgets.php');
require_once('idiomasequivalencias.php');
require('../files/bd.php');

$organization_id_array= array();
$organization_name_array= array();

 
$query_organizations = "SELECT * FROM organizations WHERE 1 ORDER BY organization_name DESC";
$result_organizations = mysqli_query($link, $query_organizations);

//if (!mysqli_num_rows($result_organizations))

$number_of_organizations=mysqli_num_rows($result_organizations);


for($iiii=0; $iiii < $number_of_organizations ; $iiii++     )
{
	$fila_organizations = mysqli_fetch_array($result_organizations);
	
	array_push($organization_id_array, $fila_organizations['organization_id'] );
	array_push($organization_name_array, $fila_organizations['organization_name'] );
	
}

//print_r($organization_id_array);
//print_r($organization_name_array);


?>
<form id='frame'> 


<fieldset><legend>Order results by...</legend>
	<div>
		<select name='orderresultsby'>
			<option value='auto'>Auto</option>
			<option value=''>Distance from me</option>
			<option value='lastlogin'>Last login</option>
			<option value=''>Newest users</option>
			<option value=''>More evaluations</option>
			<option value=''>Less evaluations</option>
			<option value=''>Best evaluations</option>
			<option value=''>Price (only teachers) asc. </option>
			<option value=''>Price (only teachers) desc. </option>
		</select>
	</div>
</fieldset>


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
	<legend for='experience-level'>Organizations*</legend>
	<div id='controlgroup'>
	
	<p style="font-size: 70%">*Select multiple elements using CTRL key</p>
	
	<select id='exp-level' name='orgs[]' style="height: 150px;" multiple>
	
<?php	
		for($iiii=0; $iiii < $number_of_organizations ; $iiii++   )
		{
				$orga_id=array_pop($organization_id_array);
				$orga_name=array_pop($organization_name_array);
		
?>	
				<option value="<?php	echo "$orga_id";	?>"><?php	echo "$orga_name";	?></option>

<?php	}	?>		
		
		
		 
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





<?php
// we extract the information to show the users in the map

$query_map = "SELECT * FROM mentor2009 WHERE (Gpslat<>0 AND Gpslng<>0 )";

$result_map = mysqli_query($link, $query_map);
if (!mysqli_num_rows($result_map))
    die(".........No results......");

$number_of_affected_users_map=mysqli_num_rows($result_map);


$orden_usuarios_map=array();

$nameuser=array();
$teacher_or_student=array();

$lat_map=array();
$lng_map=array();



for($iiii=0; $iiii < $number_of_affected_users_map ; $iiii++     )
{
	$fila_map = mysqli_fetch_array($result_map);
	
	array_push($orden_usuarios_map, $fila_map['orden']);
	array_push($teacher_or_student, $fila_map['Pais']);
	array_push($lat_map, $fila_map['Gpslat']);
	array_push($lng_map, $fila_map['Gpslng']);	

}

?>





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
//var target = L.latLng('41.38879', '2.15899');
var target = L.latLng('<?php echo "$latitud1"; ?>', '<?php echo "$longitud1"; ?>');

// Set map's center to target with zoom 14.
map.setView(target, 8);

// Place a marker on the same location.
//L.marker(target, 'draggable').addTo(map);


//L.circleMarker(target).addTo(map);


//]]>

//añadimos circulo

L.circle([<?php echo "$latitud1"; ?>, <?php echo "$longitud1"; ?>], 30000,{color:'#e65f00', opacity:1, fillColor: '#e65f00', fillOpacity:.15}).addTo(map);
//L.circle([41.38879, 2.15899], 30000,{color:'#e65f00', opacity:1, fillColor: '#e65f00', fillOpacity:.15}).addTo(map);
//L.circle([41.38879, 2.15899], 30000,{color:'red', opacity:1, fillColor: '#e65f00', fillOpacity:.15}).addTo(map);

var studentIcon = L.icon({
    iconUrl: 'marker_map_images/red_student.png',
    //shadowUrl: 'leaf-shadow.png',

    iconSize:     [3, 3], // size of the icon
    //shadowSize:   [50, 64], // size of the shadow
    //iconAnchor:   [22, 94], // point of the icon which will correspond to marker's location
    //shadowAnchor: [4, 62],  // the same for the shadow
    //popupAnchor:  [-3, -76] // point from which the popup should open relative to the iconAnchor
});

var teacherIcon = L.icon({
    iconUrl: 'marker_map_images/green_teacher.png',
    //shadowUrl: 'leaf-shadow.png',

    iconSize:     [3, 3], // size of the icon
    //shadowSize:   [50, 64], // size of the shadow
    //iconAnchor:   [22, 94], // point of the icon which will correspond to marker's location
    //shadowAnchor: [4, 62],  // the same for the shadow
    //popupAnchor:  [-3, -76] // point from which the popup should open relative to the iconAnchor
});


//L.marker([41.38879, 2.15899]).addTo(map);

//L.marker([41.38879, 2.15899], {icon: studentIcon}).addTo(map);


<?php

for($iiii=0; $iiii < $number_of_affected_users_map ; $iiii++     )
{
	$latitud_mapa=array_pop($lat_map);
	$longitud_mapa=array_pop($lng_map);
	$teach_stud=array_pop($teacher_or_student);
	
	if($teach_stud!='teacher')
	{
?>
	L.marker([<?php echo "$latitud_mapa"; ?>, <?php echo "$longitud_mapa"; ?>], {icon: studentIcon}).addTo(map);
<?php	
	} 	
	else
	{
?>
	L.marker([<?php echo "$latitud_mapa"; ?>, <?php echo "$longitud_mapa"; ?>], {icon: teacherIcon}).addTo(map);
<?php
	}
?>
	
<?php	
}

?>


</script>
<input class="ui-button ui-widget ui-corner-all" style='width: 100%' type="submit" value="Search" />
</form>
