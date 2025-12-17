<?php
require_once "include/time_conversion.php";
require_once "include/array_data_processing.php";

function extract_studentIDs($data) {
	$column_name = 'Student ID';
	$answer = extract_one_column_by_name($data, $column_name);
	$answer = array_unique($answer);
	return $answer;
}

function extract_relevant_columns($data) {
	$column_headers = [
		'Last Name',
		'First Name',
		'Card No',
		'Date',
		'PA Secure ID',
		'Home District',
		'Grade (EI or SP)',
	];

	return keep_columns_by_headers($column_headers, $data);
}
