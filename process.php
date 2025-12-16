<?php
// header.php must be BEFORE any other output to browser:
require_once "include/header.php";

// NOTE: we're doing a student ID translation for one student,
// (3153276528 -> 5623149936   # Changed per Robin Miller's instruction; PAID changed)
// and it's unclear whether this needs to happen *prior* to
// looking each student up in the Medicare files or *afterwards*.
// Currently, we aren't doing it at all, pending the next student
// who has this issue.

# Changed per Robin Miller's instruction; PAID changed
$student_id_replacements = [
	3153276528 => 5623149936,
];

require_once "include/data_month_required.php";
require_once "include/user_name_required.php";
require_once "include/data_dir_required.php";

require_once "include/file_paths_import.php";
require_once "include/file_paths_export.php";

require_once "include/show_array.php";
require_once "include/split_data_at_row_count.php";

require_once "include/excel_write.php";

require_once "include/zpass_constants.php";

require_once "include/mysql_ridership_functions.php";
require_once "include/send_count_email.php";

require_once "include/show_missing_files_and_exit.php";
?>
<h2>Processing ...</h2>
<?php
echo "<hr />\n";

$zpass = query_ridership_records($data_month);
show_array_hidden($zpass, 'zpass', 'zpass ALL RECORDS');

$zpass_split_error = zpass_error_no_grade($data_month);
show_array_hidden($zpass_split_error, 'zpass_err', 'zpass ERROR no PASID or Grade (EI/SA)');
$split_error_file = error_file_path($data_dir, '', "no_PASID_or_grade");
export_data_as_excel_or_delete($zpass_split_error, $split_error_file, 'error: no PASID or Grade');

$zpass_counts_for_email = zpass_counts_for_email($data_month);
show_array_hidden($zpass_counts_for_email, 'zpass_counts', 'zpass counts for email');
$counts_for_email_file = export_file_path($data_dir, "COUNTS", 0);
export_data_as_excel_or_delete($zpass_counts_for_email, $counts_for_email_file, 'counts for email');
// send_count_email($counts_for_email_file);

foreach (['EI', 'SA'] as $grade) {
	echo "<hr />\n";
	echo "<h2>grade = '$grade':</h2>\n";

	$zpass_students = query_iep_records($data_month, $grade);
	show_array_hidden($zpass_students, "students_{$grade}", "students $grade");

	$zpass_filtered_error = zpass_error_no_ID($data_month, $grade);
	show_array_hidden($zpass_filtered_error, "zpass_{$grade}_err", "zpass $grade ERROR no PASID");
	$filtered_error_file = error_file_path($data_dir, $grade, "no_PASID");
	export_data_as_excel_or_delete($zpass_filtered_error, $filtered_error_file, 'error: no PASID');

	$zpass_with_id_found_error = zpass_error_ID_not_found($data_month, $grade);
	show_array_hidden($zpass_with_id_found_error, "zpass_{$grade}_not_found", "zpass $grade PASID not found in IEP data");
	$id_found_error_file = error_file_path($data_dir, $grade, "PASID_not_in_IEP_data");
	export_data_as_excel_or_delete($zpass_with_id_found_error, $id_found_error_file, 'error: PASID not in IEP data');

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
		export_data_as_excel_or_delete($batch, $filename, 'Sheet1');
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