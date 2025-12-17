<?php

if ($files_missing) {
	?>
<table class="bordered">
	<?php
	foreach ($files_missing as $file_id => $file_path) {
		$file_label = $file_labels_import[$file_id];
		?>
	<tr>
		<td class="right">
			<?php echo $file_label?>:
		</td>
		<td class="error strong">
			<?=$file_path?> MISSING
		</td>
	</tr>
		<?php
	}
	?>
	<tr>
		<td class="right error strong">
			Files missing: <?=count($files_missing)?>
		</td>
		<td class="center">
			<form action="upload.php" method="post" enctype="multipart/form-data">
				<input type="hidden" name="data_month" value="<?=$data_month?>">
				<input type="submit" name="submit" value="Upload More Files">
			</form>
		</td>
	</tr>
	<?php
	exit();
} else {
	// not files missing
	?>
<table class="bordered">
	<?php
	foreach ($file_labels_import as $file_id => $file_label) {
		$file_path = $file_paths_import[$file_id];
		?>
	<tr>
		<td class="right strong">
			<?php echo $file_label?>:
		</td>
		<td>
			<?=$file_path?>
		</td>
	</tr>
		<?php
	}
	?>
</table>
	<?php
}
