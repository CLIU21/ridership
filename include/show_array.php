<?php
// requires toggle_visibility() JS function, found in ridership.js

function show_cell($cell) {
	if (is_array($cell)) {
		$cell = print_r($cell, true);
	}
	return $cell;
}

function show_array($data, $label='show_array()') {
	?>
<table class="bordered">
	<?php
	$row_num = 0;
    foreach ($data as $row) {
		?>
	<tr>
		<td><?=$row_num?></td>
		<?php
    	if (is_array($row)) {
	    	if ($row_num == 0) {
		    	foreach ($row as $cell) {
					?>
		<th>
			<?=show_cell($cell)?>
		</th>
					<?php
		    	}
	    	} else {
		    	foreach ($row as $cell) {
					?>
		<td>
			<?=show_cell($cell)?>
		</td>
					<?php
		    	}
	    	}
    	} else {
	    	if ($row_num == 0) {
				?>
		<td style="text-align: center">
			<?=$row?>
		</td>
				<?php
	    	} else {
				?>
		<td><?=$row?></td>
				<?php
	    	}
    	}
    	$row_num++;
		?>
	</tr>
		<?php
    }
	?>
</table>
	<?php
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
