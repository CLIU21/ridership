<?php

$user_name = $_POST['user_name'] ?? $_GET['user_name'] ?? "";
if (! $user_name) {
	?>
	<h2 style='color:red'>Error: no user_name passed</h2>
	Please <a href="index.php?data_month=<?=$data_month?>">choose an Upload Username for the report</a>
	<?php
	exit();
} elseif (! preg_match('/^[A-Z]*$/', $user_name)) {
	?>
	<h2 style='color:red'>Error: Invalid 'user_name'</h2>
	Please <a href="index.php?data_month=<?=$data_month?>">choose an Upload Username for the report</a>
	<?php
	exit();
} else {
	?>
	<h2 style='color:green'>UserID for Upload: <?=$user_name?></h2>
	<?php
}
?>