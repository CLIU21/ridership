<?php

// input: matrix; output: array[header-row, all-other-rows]
function header_and_body($data) {
	$header = $data[0];				// first row only
	$body = array_slice($data, 1);	// all rows except first
	return [$header, $body];
}
