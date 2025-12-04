<?php
require_once "vendor/autoload.php";

use Shuchkin\SimpleXLSX;

function load_xls($filename) {
	// echo "load_xls($filename)<br/>\n";
	if ( $xlsx = SimpleXLSX::parse($filename) ) {
		return $xlsx->rows();
	} else {
	    echo SimpleXLSX::parseError();
	    return [[]];
	}
}
