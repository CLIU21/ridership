<?php
$title = "Upload";
// header.php must be BEFORE any other output to browser:
require_once "include/header.php";

require_once "include/data_month_required.php";
require_once "include/user_name.php";
require_once "include/data_dir_create.php";

require_once "include/file_paths_import.php";
require_once "include/error_msg_file_upload.php";

require_once "include/show_array.php";
require_once "include/mysql_ridership_functions.php";
require_once "include/zpass_data_conversion.php";
require_once "include/excel_read.php";

// echo "_GET:"; echo "<pre>"; print_r($_GET); echo "</pre>";
// echo "_POST:"; echo "<pre>"; print_r($_POST); echo "</pre>";
// echo "_FILES:"; echo "<pre>"; print_r($_FILES); echo "</pre>";

# handle uploaded files:
?>
<table class="bordered">
<?php
foreach ($file_labels_import as $file_id => $file_label) {
	?>
	<tr>
	<?php
	$File = $_FILES[$file_id] ?? ['error' => 4];
	$error = $File['error'];
	if ($error == 4) {
		# file not passed: not actually an error
		continue;
	}
	elseif ($error) {
		?>
		<th><?=$file_id?>:</th>
		<td class="error strong" colspan="3">
			Error #<?=$error?>:
			<?=$error_msg_file_upload[$error]?>
		</td>
		<?php
	} else {
		$tmp_name = $File['tmp_name'];
		$orig_name = $File['name'];
		$real_name = $file_paths_import[$file_id];
		?>
		<th><?=$file_id?>:</th>
		<td>
			<?=$orig_name?>
		</td>
		<td>
			<?=$real_name?>
		</td>
		<?php
		if (move_uploaded_file($tmp_name, $real_name)) {
			?>
		<td class="success">
			MOVED
		</td>
			<?php
		} else {
			?>
		<td class="error strong">
			ERROR!
		</td>
			<?php
		}
		?>
	</tr>
	<?php
	}
}
?>
</table>
<?php

# import uploaded files:
foreach ($file_labels_import as $file_id => $file_label) {
	$file_path = $file_paths_import[$file_id] ?? "";
	$file_path_done = $file_paths_import_done[$file_id] ?? "";
	if ($file_path && file_exists($file_path)) {
		switch ($file_id) {
			case 'ZPASS':
				$zpass = load_xls($file_path);
				show_array_hidden($zpass, 'zpass_raw', 'zpass ALL RECORDS - RAW');
				$zpass = extract_relevant_columns($zpass);
				show_array_hidden($zpass, 'zpass', 'zpass ALL RECORDS');
				$overwrite = true;
				insert_ridership_records_w_header($data_month, $zpass, $overwrite);
				break;

			case 'EI_IEP':
				$grade = 'EI';
				$zpass_students_data = load_xls($file_path);
				$zpass_students = extract_studentIDs($zpass_students_data);
				show_array_hidden($zpass_students, "students_{$grade}", "students $grade");
				$overwrite = true;
				insert_iep_records_w_header($data_month, $grade, $zpass_students, $overwrite);
				break;

			case 'SA_IEP':
				$grade = 'SA';
				$zpass_students_data = load_xls($file_path);
				$zpass_students = extract_studentIDs($zpass_students_data);
				show_array_hidden($zpass_students, "students_{$grade}", "students $grade");
				$overwrite = true;
				insert_iep_records_w_header($data_month, $grade, $zpass_students, $overwrite);
				break;

			default:
				echo "<h2>Error: Unknown file_id '$file_id'</h2>\n";
				exit('halt for error');
				break;
		}
		$success = rename($file_path, $file_path_done);
		if (! $success) {
			echo "<h2>Error: file rename failed: "
				. error_get_last()['message']
				. "</h2>\n";
		}
	}
}

# wait until here so prior section uploads are included:
$uploaded_data = uploaded_data_with_labels($data_month);

$files_needed = [];
?>
<br />
<table class="bordered">
	<tr>
		<th class="strong right">
			Data Type
		</th>
		<th class="strong">
			Imported<br/>Records
		</th>
		<th class="strong">
			Filename
		</th>
	</tr>
	<!-- a form with no "action" tag posts back to the current page -->
	<form method="post" enctype="multipart/form-data">
		<input type="hidden" name="data_month" value="<?=$data_month?>">
		<input type="hidden" name="user_name" value="<?=$user_name?>">
		<?php
		foreach ($file_labels_import as $file_id => $file_label) {
			?>
		<tr>
			<td class="strong right">
				<?php echo $file_label?>
			</td>
			<td class="right">
				<?php echo $uploaded_data[$file_id]?>
			</td>
			<td>
				<?php
				$file_path = $file_paths_import[$file_id] ?? "";
				$file_path_done = $file_paths_import_done[$file_id] ?? "";

				$file_path_existing = "";
				if ($file_path && file_exists($file_path)) {
					$file_path_existing = $file_path;
				} elseif ($file_path_done && file_exists($file_path_done)) {
					$file_path_existing = $file_path_done;
				}

				if ($file_path_existing) {
					?>
				<span class="strong"><?=$file_path_existing?></span>
					<?php
				} else {
					?>
				<input type="file" name="<?php echo $file_id?>" value="">
					<?php
					$files_needed[] = $file_id;
				}
				?>
			</td>
		</tr>
			<?php
		}
		if ($files_needed) {
			?>
		<tr>
			<td class="error strong right">
				Files needed: <?=count($files_needed)?>
			</td>
			<td colspan="2" class="center">
				<input type="submit" name="submit" value="Upload">
			</td>
		</tr>
			<?php
		}
		?>
	</form>
	<?php
	if (! $files_needed) {
		$process = $_GET['process'] ?? "";
		if (! $process) {
			?>
	<form action="process.php" method="get">
		<input type="hidden" name="data_month" value="<?=$data_month?>">
		<input type="hidden" name="user_name" value="<?=$user_name?>">
		<input type="hidden" name="process" value="process">
		<tr>
			<td class="success strong right">
				All files ready
			</td>
			<td colspan="2" class="center">
				<input type="submit" name="submit" value="Process">
			</td>
		</tr>
	</form>
			<?php
		}
	}
	?>
</table>

</body>
</html>