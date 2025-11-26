<?php

$data_month = $_POST['data_month'] ?? $_GET['data_month'] ?? "";
if (! $data_month) {
	?>
	<h1>Error: no data_month passed</h1>
	Please <a href="index.php">choose a data month for the report</a>
	<?php
	exit();
} elseif (! preg_match('/^[12][0-9][0-9][0-9]-[0-9][0-9]$/', $data_month)) {
	?>
	<h1>Error: Invalid 'data_month'</h1>
	Please <a href="index.php">choose a data month for the report</a>
	<?php
	exit();
}
?>