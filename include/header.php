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
