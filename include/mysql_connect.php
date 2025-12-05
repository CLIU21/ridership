<?php

require_once "include/secrets.php";

echo "<br/>Connecting to MySQL ...\n";

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$mysqli = mysqli_connect($mysql_hostname, $mysql_username, $mysql_password, $mysql_database);

// Check connection
if (mysqli_connect_errno()) {
	echo "Failed to connect to MySQL: " . mysqli_connect_error();
	exit();
} else {
	echo "Success.<br/><br/>\n";
}
