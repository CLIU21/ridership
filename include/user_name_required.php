<?php

$user_name = $_POST['user_name'] ?? $_GET['user_name'] ?? "";
if (! $user_name) {
	?>
	<h1>Error: no user_name passed</h1>
	Please <a href="index.php">choose an Upload Username for the report</a>
	<?php
	exit();
} elseif (! preg_match('/^[A-Z]*$/', $user_name)) {
	?>
	<h1>Error: Invalid 'user_name'</h1>
	Please <a href="index.php">choose an Upload Username for the report</a>
	<?php
	exit();
}
?>