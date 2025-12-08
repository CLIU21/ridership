<?php
require_once "include/mysql_connect.php";

function insert_iep_records($data_month, $service_type, $student_array, $overwrite=False) {
	global $mysqli;

	if ($overwrite) {
		$total_deletes = 0;
		$sql = "DELETE FROM iep_data WHERE data_month = ? and is_active = ? and service_type = ?";
		$stmt = $mysqli->prepare($sql);
		$active_status = 0;
		$stmt->bind_param("sis", $data_month, $active_status, $service_type);
		foreach ([0, 1] as $active_status) {
			$stmt->execute();
			// echo "DEBUG: {$mysqli->info}<br/>\n";
			if ($mysqli->affected_rows < 0) {
				echo "Rows: {$mysqli->affected_rows}<br/>\n";
				echo "Error: {$mysqli->error}<br/>\n";
				exit();
			}
			// echo "... delete {$mysqli->affected_rows} rows<br/>\n";
			$total_deletes += $mysqli->affected_rows;
		}
		echo "Deleted $total_deletes IEP records of type '$service_type'.<br />\n";
		// $stmt->close();
	} else {
		$sql = "SELECT COUNT(*) as num FROM iep_data WHERE data_month = ? and is_active = ? and service_type = ?";
		$stmt = $mysqli->prepare($sql);
		$active_status = 0;
		$stmt->bind_param("sis", $data_month, $active_status, $service_type);
		foreach ([0, 1] as $active_status) {
			$stmt->execute();
			// echo "DEBUG: {$mysqli->info}<br/>\n";
			$result = $stmt->get_result();
			$row = $result->fetch_assoc();
			$num = $row['num'];
			echo "Found $num IEP records with active status $active_status<br/>\n";
			if ($num) {
				exit('halt');
			}
		}
	}

	$sql = "INSERT INTO iep_data (data_month, service_type, student_id, is_active) VALUES (?, ?, ?, 0)";
	$stmt = $mysqli->prepare($sql);
	$total_inserts = 0;
	$student_id = 0;
	$stmt->bind_param("sss", $data_month, $service_type, $student_id);
	foreach ($student_array as $student_id) {
		$stmt->execute();	// $student_id variable is bound by reference
		// echo "DEBUG: {$mysqli->info}<br/>\n";
		if ($mysqli->affected_rows < 0) {
			echo "Rows: {$mysqli->affected_rows}<br/>\n";
			echo "Error: {$mysqli->error}<br/>\n";
			exit();
		}
		// echo "... insert {$mysqli->affected_rows} rows<br/>\n";
		$total_inserts += $mysqli->affected_rows;
	}
	echo "Inserted $total_inserts IEP records of type '$service_type'.<br />\n";
	// $stmt->close();

	$sql = "UPDATE iep_data SET is_active = 1 WHERE data_month = ? and is_active = 0 and service_type = ?";
	$stmt = $mysqli->prepare($sql);
	$stmt->bind_param("ss", $data_month, $service_type);
	$stmt->execute();
	// echo "DEBUG: {$mysqli->info}<br/>\n";
	$total_updates = $mysqli->affected_rows;
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

function insert_ridership_records($data_month, $ridership_data, $overwrite=False) {
	global $mysqli;

	list($header, $body) = header_and_body($ridership_data);

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
	if ($header != $expected_header) {
		echo "ERROR: header is incorrect!<br/>\n";
		echo "actual: "; print_r($header); echo "<br/>\n";
		echo "expected: "; print_r($expected_header); echo "<br/>\n";
		exit();
	}

	if ($overwrite) {
		$total_deletes = 0;
		$sql = "DELETE FROM ridership_data WHERE data_month = ? and is_active = ?";
		$stmt = $mysqli->prepare($sql);
		$active_status = 0;
		$stmt->bind_param("si", $data_month, $active_status);
		foreach ([0, 1] as $active_status) {
			$stmt->execute();
			// echo "DEBUG: {$mysqli->info}<br/>\n";
			if ($mysqli->affected_rows < 0) {
				echo "Rows: {$mysqli->affected_rows}<br/>\n";
				echo "Error: {$mysqli->error}<br/>\n";
				exit();
			}
			// echo "... delete {$mysqli->affected_rows} rows<br/>\n";
			$total_deletes += $mysqli->affected_rows;
		}
		echo "Deleted $total_deletes Ridership records.<br />\n";
		// $stmt->close();
	} else {
		$sql = "SELECT COUNT(*) as num FROM ridership_data WHERE data_month = ? and is_active = ?";
		$stmt = $mysqli->prepare($sql);
		$active_status = 0;
		$stmt->bind_param("si", $data_month, $active_status);
		foreach ([0, 1] as $active_status) {
			$stmt->execute();
			// echo "DEBUG: {$mysqli->info}<br/>\n";
			$result = $stmt->get_result();
			$row = $result->fetch_assoc();
			$num = $row['num'];
			echo "Found $num Ridership records with active status $active_status<br/>\n";
			if ($num) {
				exit('halt');
			}
		}
	}

	echo "DEBUG: inserting " . count($body) . " ridership records:<br/>\n";
	$sql = "INSERT INTO ridership_data (data_month, last_name, first_name, card_number, scan_date, scan_day, scan_time, scan_hours, student_id, district, service_type, is_active) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 0)";
	$stmt = $mysqli->prepare($sql);
	$total_inserts = 0;
	list($last_name, $first_name, $card_number, $date, $day, $time, $hours, $student_id, $district, $grade) = array_fill(0, 10, Null);
	$stmt->bind_param("sssssssdsss", $data_month, $last_name, $first_name, $card_number, $date, $day, $time, $hours, $student_id, $district, $grade);
	foreach ($body as $row) {
		// echo "row: "; print_r($row); echo "<br/>\n";
		list($last_name, $first_name, $card_number, $date, $student_id, $district, $grade) = array_values($row);
		// translate SP to SA, but change nothing else:
		$grade = ($grade == "SP") ? "SA" : $grade;
		list($day, $time) = explode(' ', $date);
		$hours = convert_time_to_hours($time);
		// echo "data: last_name:$last_name, first_name:$first_name, card_number:$card_number, date:$date, student_id:$student_id, district:$district, grade:$grade<br/>\n";
		// echo "data: $last_name, $first_name, $card_number, $date, $student_id, $district, $grade<br/>\n";
		$stmt->execute();	// each variable is bound by reference
		// echo "DEBUG: {$mysqli->info}<br/>\n";
		if ($mysqli->affected_rows < 0) {
			echo "Rows: {$mysqli->affected_rows}<br/>\n";
			echo "Error: {$mysqli->error}<br/>\n";
			exit();
		}
		// echo "... insert {$mysqli->affected_rows} rows<br/>\n";
		$total_inserts += $mysqli->affected_rows;
	}
	echo "Inserted $total_inserts Ridership records.<br />\n";
	// $stmt->close();

	$sql = "UPDATE ridership_data SET is_active = 1 WHERE data_month = ? and is_active = 0";
	$stmt = $mysqli->prepare($sql);
	$stmt->bind_param("s", $data_month);
	$stmt->execute();
	// echo "DEBUG: {$mysqli->info}<br/>\n";
	$total_updates = $mysqli->affected_rows;
	echo "Updated $total_inserts Ridership records.<br />\n";

	if ($total_inserts != $total_updates) {
		echo "<h2>ERROR: Inserted/Updated Ridership values are different!</h2>\n";
		exit();
	}
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
	echo "DEBUG: {$mysqli->info}<br/>\n";
	$result = $stmt->get_result();
	$header = column_names_for_result($result);
	$body = $result->fetch_all(MYSQLI_NUM);
	$answer = array_merge([$header], $body);

	return $answer;
}

// $mysqli->autocommit(FALSE); //turn on transactions
// $mysqli->autocommit(TRUE); //turn off transactions + commit queued queries
// try { bla bla bla } catch(Exception $e) { $mysqli->rollback(); throw $e; }

// $result = mysqli_query($mysqli, "SELECT * FROM contacts ORDER BY id DESC");

// $stmt = $mysqli->prepare("INSERT INTO contacts (name,age,email) VALUES(?, ?, ?)");
// $stmt->bind_param("sis", $name, $age, $email);
// $stmt->execute();
// $result = $stmt->get_result();
// ONE ROW:
// $row = $result->fetch_assoc() - Fetch an associative array
// $row = $result->fetch_row() - Fetch a numeric array
// $row = $result->fetch_object() - Fetch an object array
// ALL:
// $arr = $result->fetch_all(MYSQLI_ASSOC) - Fetch an associative array
// $arr = $result->fetch_all(MYSQLI_NUM) - Fetch a numeric array

// $stmt = $mysqli->prepare("UPDATE contacts SET name=?, age=?, email=? WHERE id=?");
// $stmt->bind_param("sisi", $name, $age, $email, $id);
// $stmt->execute();

// $stmt = $mysqli->prepare("DELETE FROM contacts WHERE id=?");
// $stmt->bind_param("i", $id);
// $stmt->execute();

// if($stmt->affected_rows === 0) exit('No rows updated');
// $stmt->close();
