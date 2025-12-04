<?php

if ($files_missing) {
	?>
<table>
	<?php
	foreach ($files_missing as $file_id => $file_path) {
		$file_label = $file_labels_import[$file_id];
		?>
	<tr>
		<td align="right">
			<?php echo $file_label?>:
		</td>
		<td>
			<span style='font-weight:bold; color:red'><?=$file_path?> MISSING</span>
		</td>
	</tr>
		<?php
	}
	?>
	<tr>
		<td align="right">
			<span style="font-weight: bold; color: red;">Files missing: <?=count($files_missing)?></span>
		</td>
		<td>
			<form action="upload.php" method="post" enctype="multipart/form-data">
				<input type="hidden" name="data_month" value="<?=$data_month?>">
				<input type="submit" name="submit" value="Upload More Files">
			</form>
		</td>
	</tr>
	<?php
	exit();
} else {
	?>
<table>
	<?php
	foreach ($file_labels_import as $file_id => $file_label) {
		$file_path = $file_paths_import[$file_id];
		?>
	<tr>
		<td align="right">
			<?php echo $file_label?>
		</td>
		<td>
			<span style='font-weight:bold'><?=$file_path?></span>
		</td>
	</tr>
		<?php
	}
	?>
</table>
	<?php
}
