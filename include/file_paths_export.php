<?php
function get_month_number($data_dir) {
	// echo "DEBUG: get_month_number($data_dir)<br/>\n";
	$data_month = basename($data_dir);
	$frag = explode('-', $data_month);
	$month = $frag[1];
	return $month;
}

function get_grade_name($grade) {
	$grade_lookup = [
		'EI' => 'etr',
		'SA' => 'tr',
		'COUNTS' => 'COUNTS',
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
	return preg_match('/^[0-9]+_Ridership_(etr|tr|COUNTS)_part_[0-9]+[.]xlsx$/', $basename);
}

function error_file_path($data_dir, $grade, $error_type) {
	$month_number = get_month_number($data_dir);

	if ($grade) {
		return "{$data_dir}/ERROR_{$grade}_{$error_type}.xlsx";
	} else {
		return "{$data_dir}/ERROR_{$error_type}.xlsx";
	}
}

function is_error_path($full_path) {
	$basename = basename($full_path);
	return preg_match('/^ERROR_.*[.]xlsx$/', $basename);
}

