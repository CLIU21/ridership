<?php
require_once "include/az.php";
require_once "include/header.php";

require_once "include/data_month_required.php";
require_once "include/data_dir_required.php";

?>
<h2>Files in that directory:</h2>
<table border="1">
<?php

echo "DEBUG: file_paths_import =<pre>"; print_r($file_paths_import); echo "</pre>\n";

$files = scandir($data_dir, SCANDIR_SORT_ASCENDING);
foreach ($files as $file) {
	if (preg_match('/^[.]/', $file)) {
		continue;
	}
		?>
	<tr>
		<td>
		<!-- <a href="view.php?data_month=<?=$file?>"><?=$file?></a> -->
		<?=$file?>
		</td>
		<?php
	}
	</tr>
	<?php
}
?>
</table>

</body>
</html>