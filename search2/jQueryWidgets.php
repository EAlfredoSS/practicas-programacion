<?php

/*
 *  Funciones auxiliares
 */

function listIterator($array, $func, $separator="") {
	foreach ($array as $key => $value):
		$func($key, $value);
		if (next($array)) echo "$separator";
	endforeach;
}

function asOption($key, $value) {
	echo "<option value='$key'>$value</option>\n";
}

function asOptionDatalist($key, $value) {
	echo "<option value=\"" . htmlspecialchars($value) . "\"></option>\n";
}

function asJson($key, $value) {
	echo "$key:'$value'";
}


function enableCheckboxRadio() {
?>
<script type='text/javascript'>
//<![CDATA[
	$( function() {
		$( "input" ).checkboxradio();
	} );
//]]>
</script>
<?php }

/*
 * RANGESLIDER : crea un slider basado en cuadro de selección
 */ 
function rangeSlider($id, $name, $defMin=8, $defMax=20) { 
?>
<div class='center' id='<?php echo $id; ?>Interval'></div> 
<div id="<?php echo "$id"; ?>"></div>
 
<div class='hidden'>
	<label for="<?php echo $id; ?>Min">from:</label>
	<input type="number" name="min_<?php echo $name; ?>" id="<?php echo $id; ?>Min" value="<?php echo $defMin; ?>" />
	<label for="<?php echo $id; ?>Max">to:</label>
	<input type="number" name="max_<?php echo $name; ?>" id="<?php echo $id; ?>Max" value="<?php echo $defMax; ?>" />
</div>
<script type='text/javascript'>
//<![CDATA[
	$( function() {
		$( "#<?php echo $id; ?>" ).slider({
			range: true,
			min: 1,
			max: 30,
			step: 1,
			values: [ <?php echo $defMin; ?>, <?php echo $defMax; ?> ],
			slide: function( event, ui ) {
				$( "#<?php echo $id; ?>Min" ).val( ui.values[ 0 ] );
				$( "#<?php echo $id; ?>Max" ).val( ui.values[1] == $("#<?php echo $id; ?>").slider("option", "max") ? "" : ui.values[1] );
				   document.getElementById("<?php echo $id; ?>Interval").textContent =
					   ui.values[0] +
					   (ui.values[1] == $("#<?php echo $id; ?>").slider("option", "max") ?  " – " + " 30+" : " – " + ui.values[1]);
			}
		});
		var minVal = $( "#<?php echo $id; ?>" ).slider( "values", 0 );
		var maxVal = $( "#<?php echo $id; ?>" ).slider( "values", 1 );
		document.getElementById("<?php echo $id; ?>Interval").textContent = minVal + (maxVal == $("#<?php echo $id; ?>").slider("option", "max") ? " – " + " 30+" : " – " + maxVal);
		$( "#<?php echo $id; ?>Min" ).val( $( "#<?php echo $id; ?>" ).slider( "values", 0 ));
		$( "#<?php echo $id; ?>Max" ).val( $( "#<?php echo $id; ?>" ).slider( "values", 1 ) == $("#<?php echo $id; ?>").slider("option", "max") ? "" : $( "#<?php echo $id; ?>" ).slider( "values", 1 ) );
	});
//]]>
</script>
<?php }

/*
 * SELECTSLIDER : crea un slider basado en cuadro de selección
 */ 
function selectSlider($id, $name, $values, $defMin = 0, $defMax = 0) {
?>
<div class='center' id='<?php echo $id; ?>Interval'></div>
<div id="<?php echo "$id"; ?>"></div>

<div class='hidden'>
	<label>from:
	<select name="min_<?php echo $name; ?>" id="<?php echo $id; ?>Min">
		<?php listIterator($values, 'asOption'); ?>
	</select>
	</label>
	<label>to:
	<select name="max_<?php echo $name; ?>" id="<?php echo $id; ?>Max">
		<?php listIterator($values, 'asOption'); ?>
	</select>
	</label>
</div>
<script type='text/javascript'>
//<![CDATA[
	$( function() {
		$val_min = 1
		$val_max = $val_min + $("#<?php echo $id; ?>Min").children('option').length - 1;
		$defMin = <?php echo "$defMin"; ?>;
		$defMax = <?php echo "$defMax"; ?>;
		$ini_min = $val_min + $defMin;
		$ini_max = $defMax <= 0 ?
			$val_max + $defMax : $val_min + $defMax;
		$( "#<?php echo $id; ?>" ).slider({
			range: true,
			min: $val_min,
			max: $val_max,
			step: 1,
			values: [$ini_min, $ini_max],
			slide: function( event, ui ) {
				$( "#<?php echo $id; ?>Min" ).val( ui.values[0] );
				$( "#<?php echo $id; ?>Max" ).val( ui.values[1] );
				document.getElementById("<?php echo $id; ?>Interval").textContent=(
					$( "#<?php echo $id; ?>Min option:selected" ).html() + 
					" to " + 
					$( "#<?php echo $id; ?>Max option:selected" ).html()
				);
			}
		});
		$( "#<?php echo $id; ?>Min" ).val( $( "#<?php echo $id; ?>" ).slider( "values", 0 ));
		$( "#<?php echo $id; ?>Max" ).val( $( "#<?php echo $id; ?>" ).slider( "values", 1 ));
		document.getElementById("<?php echo $id; ?>Interval").textContent=(
				$( "#<?php echo $id; ?>Min option:selected" ).html() + 
				" to " + 
				$( "#<?php echo $id; ?>Max option:selected" ).html()
		);
	});
//]]>
</script>
<?php } ?>
