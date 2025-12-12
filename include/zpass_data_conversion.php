<?php
require_once "include/time_conversion.php";
require_once "include/array_data_processing.php";

define('SSG_STUDENT_INDEX', 1);

function extract_studentIDs($data) {
	$answer = extract_one_column($data, SSG_STUDENT_INDEX);
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
	$column_list = [
		ZPASS_LAST_NAME_INDEX,
		ZPASS_FIRST_NAME_INDEX,
		ZPASS_CARD_INDEX,
		ZPASS_DATE_INDEX,
		ZPASS_STUDENT_INDEX,
		ZPASS_DISTRICT_INDEX,
		ZPASS_GRADE_INDEX,
	];

	return keep_columns_by_indexes($column_list, $data);
}

function replace_student_ids($data, $student_id_replacements) {
	$fix_row = function($row) use ($student_id_replacements) {
		$value = $row[ZPASS_STUDENT_INDEX];
		if (isset($student_id_replacements[$value])) {
			$value = $student_id_replacements[$value];
			$row[ZPASS_STUDENT_INDEX] = $value;
		}
		return $row;
	};
	return array_map(
		$fix_row,
		$data,
	);
}
