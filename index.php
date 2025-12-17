<?php
$title = "Index";
// header.php must be BEFORE any other output to browser:
require_once "include/header.php";

require_once "include/mysql_ridership_functions.php";

$last_month = strtotime('-1 month');
$default_data_month = date('Y-m', $last_month);

$data_month = $_POST['data_month'] ?? $_GET['data_month'] ?? $default_data_month;

require_once "include/data_dir.php";	# must be after definition of $data_month

?>
<table>
<form action="view.php" method="get">
	<tr><td align="right">Month for data (YYYY-MM):</td><td><input type="text" name="data_month" value="<?=$data_month?>"></td></tr>
	<tr><td align="right">Your SSG Username:</td><td><input type="text" name="user_name" value=""></td></tr>
	<tr><td colspan="2" align="right"><input type="submit" name="submit" value="Submit"></td></tr>
</form>
</table>

<?php
function all_available_data_month_in_dir() {
	global $data_root;
	$directories = scandir($data_root);
	$answer = [];
	foreach ($directories as $dir) {
		if (! preg_match('/^[.]/', $dir)) {
			$answer[] = $dir;
		}
	}
	return $answer;
}

$data_months_available_db = all_available_data_month_in_db();
$data_months_available_dir = all_available_data_month_in_dir();
?>

<h2>Or, choose a past report to revisit:</h2>
<?php
$data_months = array_merge($data_months_available_db, $data_months_available_dir);
$data_months = array_unique($data_months);
rsort($data_months);
?>
<ul>
<?php
foreach ($data_months as $month) {
	?>
	<li>
		<a href="view.php?data_month=<?=$month?>"><?=$month?></a>
	</li>
	<?php
}
?>
</ul>

</body>
</html>