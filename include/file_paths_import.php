<?php
$file_labels_import = [
	'ZPASS' => 'Zpass File',
	'EI_IEP' => 'Early Intervention IPE data',
	'SA_IEP' => 'School-Age IPE data',
];

$file_paths_import = [];
foreach ($file_labels_import as $file_id => $file_label) {
	$real_name = "$data_dir/$file_id.xlsx";
	$file_paths_import[$file_id] = $real_name;
}

$file_paths_import_done = [];
foreach ($file_labels_import as $file_id => $file_label) {
	$real_name = "$data_dir/{$file_id}_done.xlsx";
	$file_paths_import_done[$file_id] = $real_name;
}

$files_missing = [];
foreach ($file_labels_import as $file_id => $file_label) {
	$file_path = $file_paths_import[$file_id] ?? "";
	$file_path_done = $file_paths_import_done[$file_id] ?? "";
	if ($file_path && file_exists($file_path)) {
		continue;
	} elseif ($file_path_done && file_exists($file_path_done)) {
		continue;
	} else {
		$files_missing[$file_id] = $file_path;
	}
}

function is_hidden_file($basename) {
	return preg_match('/^[.]/', $basename);
}

function is_import_path($full_path) {
	global $file_paths_import;
	return in_array($full_path, $file_paths_import);
}

function is_done_path($full_path) {
	global $file_paths_import_done;
	return in_array($full_path, $file_paths_import_done);
}

