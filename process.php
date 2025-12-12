<?php
// NOTE: we're doing a student ID translation for one student,
// (3153276528 -> 5623149936   # Changed per Robin Miller's instruction; PAID changed)
// and it's unclear whether this needs to happen *prior* to
// looking each student up in the Medicare files or *afterwards*.

require_once "include/az.php";
require_once "include/header.php";

require_once "include/data_month_required.php";
require_once "include/user_name_required.php";
require_once "include/data_dir_required.php";

require_once "include/file_paths_import.php";
require_once "include/file_paths_export.php";

require_once "include/array_data_processing.php";
require_once "include/show_array.php";

require_once "include/time_conversion.php";
require_once "include/zpass_data_conversion.php";

require_once "include/excel_read.php";
require_once "include/excel_write.php";

require_once "include/zpass_constants.php";

require_once "include/show_missing_files_and_exit.php";

require_once "include/mysql_ridership_functions.php";

?>
<h2>Processing ...</h2>
<?php
echo "<hr />\n";

$zpass = load_xls($file_paths_import['ZPASS']);
// echo 'zpass_data: ' . count($zpass) . "<br/>\n";
$zpass = extract_relevant_columns($zpass);
show_array_hidden($zpass, 'zpass', 'zpass ALL RECORDS');

$overwrite = false;
insert_ridership_records($data_month, $zpass, $overwrite);

$zpass_split_error = zpass_error_no_grade($data_month);

show_array_hidden($zpass_split_error, 'zpass_err', 'zpass ERROR no Grade (EI/SA)');

# Changed per Robin Miller's instruction; PAID changed
$student_id_replacements = [
	3153276528 => 5623149936,
];

foreach (['EI', 'SA'] as $grade) {
	echo "<hr />\n";
	echo "<h2>grade = '$grade':</h2>\n";

	$zpass_students_data = load_xls($file_paths_import["{$grade}_IPE"]);
	$zpass_students = extract_studentIDs($zpass_students_data);
	show_array_hidden($zpass_students, "students_{$grade}", "students $grade");
	insert_iep_records($data_month, $grade, $zpass_students, $overwrite);

	$zpass_filtered_error = zpass_error_no_ID($data_month, $grade);
	show_array_hidden($zpass_filtered_error, "zpass_{$grade}_err", "zpass $grade ERROR no ID");

	$zpass_with_id_found_error = zpass_error_ID_not_found($data_month, $grade);
	show_array_hidden($zpass_with_id_found_error, "zpass_{$grade}_not_found", "zpass $grade ID not found in student list");

	$zpass_split_id_day = zpass_grouped_by_ID_and_day($data_month, $grade);

	show_array_hidden($zpass_split_id_day, "zpass_{$grade}_split", "zpass $grade split by ID and day");

	$constants_local = array_merge($zpass_constants['global'], $zpass_constants[$grade]);
	$zpass_output_all = zpass_data_for_export($data_month, $grade, $constants_local);
	show_array_hidden($zpass_output_all, "zpass_{$grade}_output", "zpass $grade for output");

	$max_rows = 1000;
	$zpass_output_split = split_data_at_row_count($zpass_output_all, $max_rows);
	foreach ($zpass_output_split as $i => $batch) {
		show_array_hidden($batch, "zpass_{$grade}_output_{$i}", "zpass $grade for output #$i");
	}

	foreach ($zpass_output_split as $i => $batch) {
		$filename = export_file_path($data_dir, $grade, $i);
		export_data_as_excel($batch, $filename, 'Sheet Name Goes Here');
	}
}

?>
<hr />
<h2>
	All files created successfully.
	Click here to
	<a href="view.php?data_month=<?=$data_month?>&user_name=<?=$user_name?>">download</a>
	the files.
</h2>
<hr />

</body>
</html>