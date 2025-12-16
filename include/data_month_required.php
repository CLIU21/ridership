<?php

$data_month = $_POST['data_month'] ?? $_GET['data_month'] ?? "";
if (! $data_month) {
	$error_message = "no data_month passed";
} elseif (! preg_match('/^[12][0-9][0-9][0-9]-[0-9][0-9]$/', $data_month)) {
	$error_message = "Invalid 'data_month' passed";
} else {
	$error_message = "";
}

if ($error_message) {
	?>
	<h2 class="error">Error: <?=$error_message?></h2>
	Please <a href="index.php">choose a data month for the report</a>
	<?php
	exit();
} else {
	?>
	<h2 class="success">Month for data: <?=$data_month?></h2>
	<?php
}
