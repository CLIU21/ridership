<?php
require_once "vendor/autoload.php";

use \avadim\FastExcelWriter\Excel;

function export_data_as_excel($data, $filename, $sheetname='Sheet1') {
	$excel = Excel::create([$sheetname]);
	$sheet = $excel->sheet();
	foreach ($data as $row) {
		$rowOptions = [
			'height' => 20,
		];
		$sheet->writeRow($row, $rowOptions);
	}
	$excel->save($filename);
	?>
	<h2 class="success">Saved Excel data to '<?=$filename?>'</h2>
	<?php
}

function export_data_as_excel_or_delete($data, $filename, $sheetname='Sheet1') {
	if (count($data) > 1) {
		export_data_as_excel($data, $filename, $sheetname);
	} else {
		if (! file_exists($filename)) {
			return;
		}
		unlink($filename);
		if (! file_exists($filename)) {
			?>
			<h2 class="warning">
				No data sent;
				file <?=$filename?> deleted.
			</h2>
			<?php
		} else {
			?>
			<h2 class="warning">
				No data sent;
				file <?=$filename?> deletion
				<span class="error">failed</span>
			</h2>
			<?php
		}
	}
}
