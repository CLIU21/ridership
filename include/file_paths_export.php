<?php
function get_month_number($data_dir) {
	// echo "DEBUG: get_month_number($data_dir)<br/>\n";
	$data_month = basename($data_dir);
	echo "DEBUG: data_month = $data_month<br/>\n";
	return "10";
}

function get_grade_name($grade) {
	echo "DEBUG: get_grade_name($grade)<br/>\n";
	$grade_lookup = [
		'EI' => 'etr',
		'SA' => 'tr',
	];
	$grade_name = $grade_lookup[$grade] ?? "";
	if (! $grade_name) {
		throw new Exception("INVALID GRADE '$grade'");
	}
	return $grade_name;
}

function export_file_path($data_dir, $grade, $i) {
	$month_number = get_month_number($data_dir);
	$grade_name = get_grade_name($grade);
	$i += 1;

	return "{$data_dir}/{$month_number}_Ridership_{$grade_name}_part_{$i}.xlsx";
}

function is_export_path($full_path) {
	$basename = basename($full_path);
	return preg_match('/^[0-9]+_Ridership_(etr|tr)_part_[0-9]+.xlsx$/', $basename);
}
