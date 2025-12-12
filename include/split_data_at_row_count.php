<?php
require_once "include/split_header_and_body.php";

// input: matrix; output: array[matrix]
function split_data_at_row_count($data, $max_rows) {
	echo "DEBUG: split_data_at_row_count(" . count($data) . ", " . $max_rows . ")<br />\n";

	$output = [];
	if (count($data) <= $max_rows) {
		array_push($output, $data);
	} else {
		list($header, $body) = header_and_body($data);
		$array_of_body_chunks = array_chunk($body, $max_rows);
		foreach ($array_of_body_chunks as $one_body_chunk) {
			array_unshift($one_body_chunk, $header);
			array_push($output, $one_body_chunk);
		}
	}
	return $output;
}
