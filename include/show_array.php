<?php
// requires toggle_visibility() JS function, found in ridership.js

function show_array($data, $label='show_array()') {
	echo '<table border="1">';
	$row_num = 0;
    foreach ($data as $row) {
    	echo '<tr>';
    	echo "<td>$row_num</td>";
    	if (is_array($row)) {
	    	if ($row_num == 0) {
		    	foreach ($row as $column_num => $cell) {
			    	echo '<td style="text-align: center">';
			    	echo $column_num;
			    	echo '<br />';
			    	if (is_array($cell)) {
			    		print_r($cell);
			    	} else {
			    		echo $cell;
			    	}
			    	echo '</td>';
		    	}
	    	} else {
		    	foreach ($row as $cell) {
			    	echo '<td>';
			    	if (is_array($cell)) {
			    		print_r($cell);
			    	} else {
			    		echo $cell;
			    	}
			    	echo '</td>';
		    	}
	    	}
    	} else {
	    	if ($row_num == 0) {
		    	echo '<td style="text-align: center">';
		    	echo '(strings)';
		    	echo '<br />';
		    	echo $row;
		    	echo '</td>';
	    	} else {
		    	echo '<td>';
		    	echo $row;
		    	echo '</td>';
	    	}
    	}
    	$row_num++;
    	echo '</tr>';
    }
    echo '</table>';
}

function show_array_hidden($data, $span_id, $label='show_array_hidden()') {
	// requires toggle_visibility() from ridership.js
	$records = count($data) - 1;
	?>
	<div id="<?=$span_id?>_master">
		<div id="<?=$span_id?>_label">
			<h2>
				<?=$label?>:
				(<?=$records?> records)
				<button onclick="toggle_visibility('<?=$span_id?>')">Hide/Show</button>
			</h2>
		</div>
		<div id="<?=$span_id?>" style="display:none">
	<?php
	show_array($data, $label);
	?>
		</div>
	</div>
	<?php
}
