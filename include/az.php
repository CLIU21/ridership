<?php
session_start();

// echo "<h2>DEBUG: SERVER_NAME = {$_SERVER["SERVER_NAME"]}</h2>\n";

if ($_SERVER["SERVER_NAME"] == "localhost") {
	# bypass signon requirement
	return;
}

if (!$_SESSION["az_user_data"]["userPrincipalName"]) {
	header("Location: https://ridership.cliu.org/az_auth.php");
	die();
}

// Force HTTPS for security

if ($_SERVER["HTTPS"] != "on") {
	$pageURL = "Location: https://";
	if ($_SERVER["SERVER_PORT"] != "80") {
		$pageURL .= $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"];
	} else {
		$pageURL .= $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
	}

	header($pageURL);
}
?>