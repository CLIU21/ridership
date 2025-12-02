<?php
$file_labels_import = [
	'ZPASS' => 'Zpass File',
	'EI_IPE' => 'Early Intervention IPE data',
	'SA_IPE' => 'School-Age IPE data',
];

$file_paths_import = [];
foreach ($file_labels_import as $file_id => $file_label) {
	$real_name = "$data_dir/$file_id.xlsx";
	$file_paths_import[$file_id] = $real_name;
}

?>