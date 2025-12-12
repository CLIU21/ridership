<?php
require_once "include/split_header_and_body.php";

// input: matrix; output: array
function extract_one_column($data, $column_id) {
	$output = array_map(
		fn($row) => $row[$column_id],
		$data,
	);
	return $output;
}

// input: matrix; output: matrix
function remove_columns_by_indexes($column_list, $data) {
	$filter_column_remove = function ($value, $key) use ($column_list) {
		return (! in_array($key, $column_list));
	};
	$filter_rows = fn($row) => array_filter($row, $filter_column_remove, ARRAY_FILTER_USE_BOTH);
	return array_map($filter_rows, $data);
}

// input: matrix; output: matrix
function keep_columns_by_indexes($column_list, $data) {
	$filter_columns_keep = function ($value, $key) use ($column_list) {
		return in_array($key, $column_list);
	};
	$filter_rows = fn($row) => array_filter($row, $filter_columns_keep, ARRAY_FILTER_USE_BOTH);
	return array_map($filter_rows, $data);
}

// input: matrix; output: array['ok' -> matrix, 'error' -> matrix]
function split_rows_by_column_data_present($data, $column_index, $allow_list) {
	# returns 'ok' and 'error' sections for column $column_index value being present or absent in allow_list
	list($header, $body) = header_and_body($data);
	$answer = [];

	foreach (['ok', 'error'] as $bucket) {
		$answer[$bucket] = [$header];
	}

	foreach ($body as $row) {
		$value = $row[$column_index];
		$found = in_array($value, $allow_list);
		$bucket = ($found ? 'ok' : 'error');
		array_push($answer[$bucket], $row);
	}

	return $answer;
}

// input: matrix; output: array['ok' -> matrix, 'error' -> matrix]
function filter_data_by_column_not_blank($column_id, $data) {
	list($header, $body) = header_and_body($data);
	$answer = [
		'ok' => [$header],
		'error' => [$header],
	];
	foreach ($body as $row) {
		$value = $row[$column_id];
		$group = ($value !== "") ? 'ok' : 'error';
		array_push($answer[$group], $row);
	}
	return $answer;
}

// input: matrix; output: array[value -> matrix, ...]
function split_rows_by_column_data_value($data, $column_index) {
	# returns separate sections for each value in column $column_index
	list($header, $body) = header_and_body($data);
	$answer = [];
	foreach ($data as $row) {
		$value = $row[$column_index];
		if (! isset($answer[$value])) {
			$answer[$value] = [$header];
		}
		array_push($answer[$value], $row);
	}

	return $answer;
}
