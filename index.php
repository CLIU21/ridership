<?php
require_once "include/az.php";
require_once "include/header.php";

$last_month = strtotime('-1 month');
$data_month = date('Y-m', $last_month);
?>
<table>
<form action="upload.php" method="get" enctype="multipart/form-data">
	<tr><td align="right">Month for data (YYYY-MM)</td><td><input type="text" name="data_month" value="<?=$data_month?>"></td></tr>
	<tr><td align="right">UserID for Upload</td><td><input type="text" name="user_name" value=""></td></tr>
	<tr><td colspan="2" align="right"><input type="submit" name="submit" value="Submit"></td></tr>
</form>
</table>

</body>
</html>