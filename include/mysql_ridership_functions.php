<?php
require_once "include/mysql_connect.php";

function all_available_data_month_in_db() {
	global $mysqli;

	$sql = "SELECT DISTINCT data_month FROM iep_data
			UNION
			SELECT DISTINCT data_month FROM ridership_data";
	$stmt = $mysqli->prepare($sql);
	$stmt->execute();
	$result = $stmt->get_result();
	$answer = [];
	while($row = mysqli_fetch_assoc($result)) {
		$answer[] = $row['data_month'];
	}

	return $answer;
}

function uploaded_data_with_labels($data_month) {
	$uploaded_data = [
		'EI_IEP' => count_iep_records($data_month, 'EI', 1),
		'SA_IEP' => count_iep_records($data_month, 'SA', 1),
		'ZPASS' => count_ridership_records($data_month, 1),
	];
	return $uploaded_data;
}

function count_iep_records($data_month, $service_type, $active_status) {
	global $mysqli;

	$sql = "SELECT COUNT(*) as num FROM iep_data WHERE data_month = ? and service_type = ? and is_active = ?";
	$stmt = $mysqli->prepare($sql);
	$stmt->bind_param("ssi", $data_month, $service_type, $active_status);
	$stmt->execute();
	$result = $stmt->get_result();
	$row = $result->fetch_assoc();
	$num = $row['num'];
	return $num;
}

function query_iep_records($data_month, $service_type) {
	global $mysqli;

	$sql = "SELECT COUNT(*) as num
			FROM iep_data
			WHERE data_month = ? and service_type = ? and is_active = 1";
	$stmt = $mysqli->prepare($sql);
	$stmt->bind_param("ss", $data_month, $service_type);
	$stmt->execute();
	$result = $stmt->get_result();
	$header = column_names_for_result($result);
	$body = $result->fetch_all(MYSQLI_NUM);
	$answer = array_merge([$header], $body);

	return $answer;
}

function delete_iep_records($data_month, $service_type, $active_status) {
	global $mysqli;

	$sql = "DELETE FROM iep_data WHERE data_month = ? and is_active = ? and service_type = ?";
	$stmt = $mysqli->prepare($sql);
	$stmt->bind_param("sis", $data_month, $active_status, $service_type);
	$stmt->execute();
	if ($mysqli->affected_rows < 0) {
		echo "Rows: {$mysqli->affected_rows}<br/>\n";
		echo "Error: {$mysqli->error}<br/>\n";
		exit();
	}
	// echo "... delete {$mysqli->affected_rows} rows<br/>\n";
	$total_deletes = $mysqli->affected_rows;
	return $total_deletes;
}

function insert_iep_records($data_month, $service_type, $student_array) {
	global $mysqli;

	$sql = "INSERT INTO iep_data (data_month, service_type, student_id, is_active) VALUES (?, ?, ?, 0)";
	$stmt = $mysqli->prepare($sql);
	$total_inserts = 0;
	$student_id = 0;
	$stmt->bind_param("sss", $data_month, $service_type, $student_id);
	foreach ($student_array as $student_id) {
		$stmt->execute();	// $student_id variable is bound by reference
		if ($mysqli->affected_rows < 0) {
			echo "Rows: {$mysqli->affected_rows}<br/>\n";
			echo "Error: {$mysqli->error}<br/>\n";
			exit();
		}
		// echo "... insert {$mysqli->affected_rows} rows<br/>\n";
		$total_inserts += $mysqli->affected_rows;
	}
	return $total_inserts;
}

function activate_iep_records($data_month, $service_type) {
	global $mysqli;

	$sql = "UPDATE iep_data SET is_active = 1 WHERE data_month = ? and is_active = 0 and service_type = ?";
	$stmt = $mysqli->prepare($sql);
	$stmt->bind_param("ss", $data_month, $service_type);
	$stmt->execute();
	$total_updates = $mysqli->affected_rows;

	return $total_updates;
}

function insert_iep_records_w_overwrite($data_month, $service_type, $student_array, $overwrite=False) {
	if ($overwrite) {
		$total_deletes = 0;
		foreach ([0, 1] as $active_status) {
			$total_deletes += delete_iep_records($data_month, $service_type, $active_status);
		}
		echo "Deleted $total_deletes IEP records of type '$service_type'.<br />\n";
	} else {
		foreach ([0, 1] as $active_status) {
			$num = count_iep_records($data_month, $service_type, $active_status);
			echo "Found $num IEP records with active status $active_status<br/>\n";
			if ($num) {
				exit('halt');
			}
		}
	}

	$total_inserts = insert_iep_records($data_month, $service_type, $student_array);
	echo "Inserted $total_inserts IEP records of type '$service_type'.<br />\n";

	$total_updates = activate_iep_records($data_month, $service_type);
	echo "Updated $total_inserts IEP records of type '$service_type'.<br />\n";

	if ($total_inserts != $total_updates) {
		echo "<h2>ERROR: Inserted/Updated IEP values are different!</h2>\n";
		exit();
	}
}

function column_names_for_result($result) {
	$finfo = mysqli_fetch_fields($result);
	$column_names = [];
    foreach ($finfo as $val) {
        $column_names[] = $val->name;
    }
    return $column_names;
}

function count_ridership_records($data_month, $active_status) {
	global $mysqli;

	$sql = "SELECT COUNT(*) as num FROM ridership_data WHERE data_month = ? and is_active = ?";
	$stmt = $mysqli->prepare($sql);
	$stmt->bind_param("si", $data_month, $active_status);
	$stmt->execute();
	$result = $stmt->get_result();
	$row = $result->fetch_assoc();
	$num = $row['num'];

	return $num;
}

function query_ridership_records($data_month) {
	global $mysqli;

	$sql = "SELECT COUNT(*) as num
			FROM ridership_data
			WHERE data_month = ? and is_active = 1";
	$stmt = $mysqli->prepare($sql);
	$stmt->bind_param("s", $data_month);
	$stmt->execute();
	$result = $stmt->get_result();
	$header = column_names_for_result($result);
	$body = $result->fetch_all(MYSQLI_NUM);
	$answer = array_merge([$header], $body);

	return $answer;
}

function delete_ridership_records($data_month, $active_status) {
	global $mysqli;

	$sql = "DELETE FROM ridership_data WHERE data_month = ? and is_active = ?";
	$stmt = $mysqli->prepare($sql);
	$stmt->bind_param("si", $data_month, $active_status);
	$stmt->execute();
	if ($mysqli->affected_rows < 0) {
		echo "Rows: {$mysqli->affected_rows}<br/>\n";
		echo "Error: {$mysqli->error}<br/>\n";
		exit();
	}
	// echo "... delete {$mysqli->affected_rows} rows<br/>\n";
	$total_deletes = $mysqli->affected_rows;
	return $total_deletes;
}

function insert_ridership_records($data_month, $ridership_body) {
	global $mysqli;

	$sql = "INSERT INTO ridership_data (data_month, last_name, first_name, card_number, scan_date, scan_day, scan_time, scan_hours, student_id, district, service_type, is_active) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 0)";
	$stmt = $mysqli->prepare($sql);
	$total_inserts = 0;
	list($last_name, $first_name, $card_number, $date, $day, $time, $hours, $student_id, $district, $grade) = array_fill(0, 10, Null);
	$stmt->bind_param("sssssssdsss", $data_month, $last_name, $first_name, $card_number, $date, $day, $time, $hours, $student_id, $district, $grade);
	foreach ($ridership_body as $row) {
		// echo "row: "; print_r($row); echo "<br/>\n";
		list($last_name, $first_name, $card_number, $date, $student_id, $district, $grade) = array_values($row);
		// translate SP to SA, but change nothing else:
		$grade = ($grade == "SP") ? "SA" : $grade;
		list($day, $time) = explode(' ', $date);
		$hours = convert_time_to_hours($time);
		// echo "data: last_name:$last_name, first_name:$first_name, card_number:$card_number, date:$date, student_id:$student_id, district:$district, grade:$grade<br/>\n";
		// echo "data: $last_name, $first_name, $card_number, $date, $student_id, $district, $grade<br/>\n";
		$stmt->execute();	// each variable is bound by reference
		if ($mysqli->affected_rows < 0) {
			echo "Rows: {$mysqli->affected_rows}<br/>\n";
			echo "Error: {$mysqli->error}<br/>\n";
			exit();
		}
		// echo "... insert {$mysqli->affected_rows} rows<br/>\n";
		$total_inserts += $mysqli->affected_rows;
	}
	return $total_inserts;
}

function activate_ridership_records($data_month) {
	global $mysqli;

	$sql = "UPDATE ridership_data SET is_active = 1 WHERE data_month = ? and is_active = 0";
	$stmt = $mysqli->prepare($sql);
	$stmt->bind_param("s", $data_month);
	$stmt->execute();
	$total_updates = $mysqli->affected_rows;

	return $total_updates;
}

function insert_ridership_records_w_header($data_month, $ridership_data_w_header, $overwrite=False) {
	list($ridership_header, $ridership_body) = header_and_body($ridership_data_w_header);

	//	Header should contain:
	$expected_header = [
		0 => 'Last Name',
		1 => 'First Name',
		2 => 'Card No',
		8 => 'Date',
		17 => 'PA Secure ID',
		19 => 'Home District',
		20 => 'Grade (EI or SP)',
	];
	if ($ridership_header != $expected_header) {
		echo "ERROR: header is incorrect!<br/>\n";
		echo "actual: "; print_r($ridership_header); echo "<br/>\n";
		echo "expected: "; print_r($expected_header); echo "<br/>\n";
		exit();
	}

	if ($overwrite) {
		$total_deletes = 0;
		foreach ([0, 1] as $active_status) {
			$total_deletes += delete_ridership_records($data_month, $active_status);
		}
		echo "Deleted $total_deletes Ridership records.<br />\n";
	} else {
		foreach ([0, 1] as $active_status) {
			$num = count_ridership_records($data_month, $active_status);
			echo "Found $num Ridership records with active status $active_status<br/>\n";
			if ($num) {
				exit('halt');
			}
		}
	}

	$total_inserts = insert_ridership_records($data_month, $ridership_body);
	echo "Inserted $total_inserts Ridership records.<br />\n";

	$total_updates = activate_ridership_records($data_month);
	echo "Updated $total_inserts Ridership records.<br />\n";

	if ($total_inserts != $total_updates) {
		echo "<h2>ERROR: Inserted/Updated Ridership values are different!</h2>\n";
		exit();
	}
}

function zpass_error_no_grade($data_month) {
	global $mysqli;

	$sql = "SELECT last_name, first_name, card_number, scan_date, student_id, district, service_type
			FROM ridership_data
			WHERE is_active = 1
			AND data_month = ?
			AND service_type = ''";		# ... not in ['EI', 'SP', 'SA']
	$stmt = $mysqli->prepare($sql);
	$stmt->bind_param("s", $data_month);
	$stmt->execute();
	$result = $stmt->get_result();
	$header = column_names_for_result($result);
	$body = $result->fetch_all(MYSQLI_NUM);
	$answer = array_merge([$header], $body);

	return $answer;
}

function zpass_error_no_ID($data_month, $service_type) {
	global $mysqli;

	$sql = "SELECT last_name, first_name, card_number, scan_date, student_id, district, service_type
			FROM ridership_data
			WHERE is_active = 1
			AND data_month = ?
			AND service_type = ?
			AND student_id = ''";
	$stmt = $mysqli->prepare($sql);
	$stmt->bind_param("ss", $data_month, $service_type);
	$stmt->execute();
	$result = $stmt->get_result();
	$header = column_names_for_result($result);
	$body = $result->fetch_all(MYSQLI_NUM);
	$answer = array_merge([$header], $body);

	return $answer;
}

function zpass_error_ID_not_found($data_month, $service_type) {
	global $mysqli;

	// diff_hours is also available if desired
	$sql = "SELECT last_name, first_name, scan_day
			, service_name, service_code, record_count
			, student_id, district, service_type
			FROM ridership_data_view_check_iep
			WHERE has_iep = 0
			AND data_month = ?
			AND service_type = ?";
	$stmt = $mysqli->prepare($sql);
	$stmt->bind_param("ss", $data_month, $service_type);
	$stmt->execute();
	$result = $stmt->get_result();
	$header = column_names_for_result($result);
	$body = $result->fetch_all(MYSQLI_NUM);
	$answer = array_merge([$header], $body);

	return $answer;
}

function zpass_grouped_by_ID_and_day($data_month, $service_type) {
	global $mysqli;

	// district is also available if desired
	$sql = "SELECT last_name, first_name, student_id, service_type
				, scan_day, '---' as 'hours', diff_hours as 'elapsed', record_count
				, service_name, service_code
			FROM ridership_data_view
			WHERE data_month = ?
			AND service_type = ?";
	$stmt = $mysqli->prepare($sql);
	$stmt->bind_param("ss", $data_month, $service_type);
	$stmt->execute();
	$result = $stmt->get_result();
	$header = column_names_for_result($result);
	$body = $result->fetch_all(MYSQLI_NUM);
	$answer = array_merge([$header], $body);

	return $answer;
}

function zpass_counts_for_email($data_month) {
	global $mysqli;

	$sql = "SELECT data_month, service_type, service_name, service_code
			, students_count, trip_multiplier
			, (students_count * trip_multiplier) as effective_trips
				FROM (
				SELECT data_month, service_type, service_name, service_code
				, count(student_id) AS students_count
				, IF(service_name = 'RoundTrip', 2, 1) AS trip_multiplier
				FROM ridership_data_view_trips as R
				WHERE data_month = ?
				GROUP BY data_month, service_type, service_name, service_code
			) AS C
			";
	$stmt = $mysqli->prepare($sql);
	$stmt->bind_param("s", $data_month);
	$stmt->execute();
	$result = $stmt->get_result();
	$header = column_names_for_result($result);
	$body = $result->fetch_all(MYSQLI_NUM);
	$answer = array_merge([$header], $body);

	return $answer;
}

function zpass_data_for_export($data_month, $service_type, $constants) {
	global $mysqli;

	// district is also available if desired
	$sql = "SELECT ? as 'District CD'
				, student_id as 'Student ID'
				, ? as 'Provider ID'
				, scan_day as 'Service Date'
				, '' as 'Make-Up Date'
				, '' as 'Start Time'
				, '' as 'End Time'
				, ? as 'Service Type'
				, service_code as 'Service Code'
				, '' as 'Group Size'
				, '' as 'Therapy Method'
				, '' as 'Therapy Method2'
				, ? as 'Diagnosis Code'
				, '' as 'Place of Service CD'
				, '' as 'Place of Service Description'
				, '' as 'School CD'
				, '' as 'Progress'
				, '' as 'Therapy Notes'
				, ? as 'Entered by ID'
				, ? as 'Entered Date'
				, '' as 'Approved?'
				, '' as 'Approver ID'
				, '' as 'Approved Date'
				, '' as 'Reference Number'
			FROM ridership_data_view
			WHERE data_month = ?
			AND service_type = ?";
	$stmt = $mysqli->prepare($sql);
	$stmt->bind_param(
		"ssssssss", 
		$constants['district_code'],
		$constants['uploaded_by'],
		$constants['service_type'],
		$constants['diagnosis_code'],
		$constants['uploaded_by'],
		$constants['timestamp'],
		$data_month,
		$service_type
	);
	$stmt->execute();
	$result = $stmt->get_result();
	$header = column_names_for_result($result);
	$body = $result->fetch_all(MYSQLI_NUM);
	$answer = array_merge([$header], $body);

	return $answer;
}
