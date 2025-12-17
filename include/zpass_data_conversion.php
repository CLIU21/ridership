<?php
require_once "include/time_conversion.php";
require_once "include/array_data_processing.php";

define('SSG_STUDENT_INDEX', 1);

function extract_studentIDs($data) {
	$column_name = 'Student ID';
	$answer = extract_one_column_by_name($data, $column_name);
	$answer = array_unique($answer);
	return $answer;
}

define('ZPASS_LAST_NAME_INDEX', 0);
define('ZPASS_FIRST_NAME_INDEX', 1);
define('ZPASS_CARD_INDEX', 2);
define('ZPASS_DATE_INDEX', 8);
define('ZPASS_STUDENT_INDEX', 17);
define('ZPASS_DISTRICT_INDEX', 19);
define('ZPASS_GRADE_INDEX', 20);

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
