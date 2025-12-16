<?php
require_once "include/az.php";
require_once "include/header.php";

require_once "include/data_month_required.php";
require_once "include/user_name.php";
require_once "include/data_dir_create.php";

require_once "include/file_paths_import.php";
require_once "include/file_paths_export.php";

require_once "include/mysql_ridership_functions.php";

$allow_delete = $_POST['allow_delete'] ?? $_GET['allow_delete'] ?? "";
if ($allow_delete) {
	?>
<h2 style="color: orange;">
	File deletion allowed: see below
</h2>
	<?php
}

function is_visible_file($basename) {
	return (! is_hidden_file($basename));
}

$files = scandir($data_dir);
$files = array_filter($files, 'is_visible_file');

function full_path($basename) {
	global $data_dir;
	return $data_dir . "/" . $basename;
}

$file_paths = array_map('full_path', $files);

function is_other_path($full_path) {
	if (is_import_path($full_path)) { return false; }
	if (is_done_path($full_path)) { return false; }
	if (is_export_path($full_path)) { return false; }
	if (is_error_path($full_path)) { return false; }
	return true;
}

$import_files = array_filter($file_paths, 'is_import_path');
$done_files = array_filter($file_paths, 'is_done_path');
$export_files = array_filter($file_paths, 'is_export_path');
$error_files = array_filter($file_paths, 'is_error_path');

$other_files = array_filter($file_paths, 'is_other_path');

$uploaded_data = uploaded_data_with_labels($data_month);

$checksum = array_sum($uploaded_data);
if ($checksum) {
	?>
<h2>Data uploaded by user:</h2>
<table class="bordered">
	<?php
	foreach ($uploaded_data as $file_id => $value) {
		$label = $file_labels_import[$file_id];
		?>
	<tr><td style="font-weight: bold;"><?=$label?></td><td><?=$value?></td></tr>
		<?php
	}
	?>
</table>
	<?php
}
# else don't show this section

if ($allow_delete) {
	?>
<!-- a form with no "action" tag posts back to the current page -->
<form method="post">
<?php
}

function show_files_list_tr_td($filename_list, $allow_delete, $allow_download, $header_text) {
	if ($filename_list) {
		$width = 1;
		if ($allow_delete) {
			$width++;
		}
		if ($allow_download) {
			$width++;
		}
		?>
	<tr>
		<td colspan="<?=$width?>" style="font-weight: bold; text-align: center;">
			<?=$header_text?>
		</td>
	</tr>
		<?php
	}
	foreach ($filename_list as $full_path) {
		$basename = basename($full_path);
		?>
	<tr>
		<td>
			<?=$basename?>
		</td>
		<?php
		if ($allow_download) {
			?>
		<td>
			DOWNLOAD:
			<a href="<?=$full_path?>" download>
				<img
					src="./image/down-arrow-svgrepo-com.svg"
					alt="Download"
					title="Download"
				/>
			</a>
		</td>
			<?php
		}
		if ($allow_delete) {
			?>
		<td>
			<span style="color: orange; font-weight: bold;">
				DELETE:
			</span>
		    <input type="checkbox" name="delete[]" value="<?=urlencode($full_path)?>">
		</td>
			<?php
		}
		?>
	</tr>
		<?php
	}
}

if ($import_files or $done_files) {
	?>
<h2>Files uploaded by user, from ZPass and SSG:</h2>
<table class="bordered">
	<?php
	$allow_download = 0;
	show_files_list_tr_td($import_files, $allow_delete, $allow_download, 'Files Available for Import');
	show_files_list_tr_td($done_files, $allow_delete, $allow_download, 'Files Successfully Imported');
	?>
</table>
	<?php
}
# else don't show this section

if ($export_files or $error_files) {
	?>
<h2>Files produced by Ridership system:</h2>
<table class="bordered">
	<?php
	$allow_download = 1;
	show_files_list_tr_td($export_files, $allow_delete, $allow_download, 'Export Files');
	show_files_list_tr_td($error_files, $allow_delete, $allow_download, 'Error Files');
	?>
</table>
	<?php
}
# else don't show this section

if ($other_files) {
	?>
<h2>Other (unknown) files:</h2>
<table class="bordered">
	<?php
	$allow_download = 0;
	show_files_list_tr_td($other_files, $allow_delete, $allow_download, '');
	?>
</table>
	<?php
}
# else don't show this section
?>

<h2>
	<a href="upload.php?data_month=<?=$data_month?>&user_name=<?=$user_name?>">Upload some files</a>
</h2>

<?php
if ($allow_delete) {
	?>
    <input type="submit" name="submit" value="Confirm Delete">
</form>
<?php
} else {
	?>
<h2>
	To upload replacement data:
	<a href="?allow_delete=1&data_month=<?=$data_month?>&user_name=<?=$user_name?>">Allow file deletion</a>
</h2>
	<?php
}
?>

<?php
if ($import_files) {
	?>
<h2><a href="process.php?data_month=<?=$data_month?>&user_name=<?=$user_name?>">Process files</a></h2>
	<?php
}
?>

</body>
</html>