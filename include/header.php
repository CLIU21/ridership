<?php
// az.php must be BEFORE any other output to browser:
require_once "include/az.php";

// $title should be set before including this file
$title = $title ?? "Unknown";
// $default_data_month may be set before including this file
$data_month = $_POST['data_month'] ?? $_GET['data_month'] ?? $default_data_month ?? "";
$user_name = $_POST['user_name'] ?? $_GET['user_name'] ?? "";

?><!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>
		Ridership - <?=$title?>
	</title>
	<script src="./js/ridership.js" type="text/javascript"></script>
	<link href="./css/ridership.css" rel="stylesheet" />
</head>
<body>
<h1>Ridership Report: <?=$title?></h1>
<?php
$section_pages = [
	"Index" => "index.php",
	"View" => "view.php",
	"Upload" => "upload.php",
	"Process" => "process.php",
];
$sections = array_keys($section_pages);
$this_page_index = array_search($title, $sections);

$query_string_items = [
	"data_month={$data_month}",
	"user_name={$user_name}",
];
$query_string = join("&", $query_string_items);

?>
<div class="breadcrumb_container">
<?php
foreach ($sections as $page_index => $section_title) {
	$url = $section_pages[$section_title] . "?" . $query_string;
	if ($page_index < $this_page_index) {
		$time = "past";
	} elseif ($page_index == $this_page_index) {
		$time = "present";
	} else {
		// page_index > $this_page_index
		$time = "future";
	}
	?>
	<div class="<?=$time?> breadcrumb">
		<a href="<?=$url?>">
			<?=$section_title?>
		</a>
	</div>
	<?php
}
?>
</div>
<br />
