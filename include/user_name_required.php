<?php

require_once "user_name.php";

if (! $user_name) {
	$error_message = "no SSG Username passed";
} elseif (! preg_match('/^[A-Z]*$/', $user_name)) {
	$error_message = "invalid SSG Username passed";
} else {
	$error_message = "";
}

if ($error_message) {
	?>
	<h2 class="error">Error: <?=$error_message?></h2>
<table>
	<!-- a form with no "action" tag posts back to the current page -->
	<form method="get">
		<input type="hidden" name="data_month" value="<?=$data_month?>">
		<tr><td align="right">Your SSG Username:</td><td><input type="text" name="user_name" value=""></td></tr>
		<tr><td colspan="2" align="right"><input type="submit" name="submit" value="Submit"></td></tr>
	</form>
</table>
	<?php
	exit();
} else {
	?>
	<h2 class="success">Your SSG Username: <?=$user_name?></h2>
	<?php
}
