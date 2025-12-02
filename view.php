<?php
require_once "include/az.php";
require_once "include/header.php";

require_once "include/data_month_required.php";
require_once "include/data_dir_required.php";

require_once "include/file_paths_import.php";
require_once "include/file_paths_export.php";

?>
<h2>Files in that directory:</h2>
<table border="1">
<?php

echo "DEBUG: file_paths_import =<pre>"; print_r($file_paths_import); echo "</pre>\n";

$files = scandir($data_dir, SCANDIR_SORT_ASCENDING);
foreach ($files as $file) {
	if (is_hidden_file($file)) {
		continue;
	}
	$full_path = $data_dir . "/" . $file;
	?>
	<tr>
		<td>
			<!-- <a href="view.php?data_month=<?=$file?>"><?=$file?></a> -->
			<?=$file?>
		</td>
		<td>
			(<?=$full_path?>)
		</td>
		<td>
			<?php
			if (is_import_path($full_path)) {
				echo "Import (uploaded) file";
			} elseif (is_export_path($full_path)) {
				echo "Export (produced) file";
			} else {
				echo "Other";
			}
			?>
		</td>
	</tr>
	<?php
}
?>
</table>

</body>
</html>