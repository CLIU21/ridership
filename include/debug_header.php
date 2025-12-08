<?php

namespace FakeDebugHeader;

function header($header) {
	$pieces = explode(' ', $header);
	if (count($pieces) != 2) {
		echo "<h1>DEBUG header() ignored [!=2]</h1>\n";
		echo "<h2>DEBUG $header</h2>\n";
		return;
	}
	list($command, $url) = $pieces;
	if ($command != "Location:") {
		echo "<h1>DEBUG header() ignored [2]</h1>\n";
		echo "<h2>DEBUG $header</h2>\n";
		return;
	}
	$seconds = 30;
	echo "<h1>DEBUG header() sent: redirecting in $seconds seconds</h1>\n";
	echo "<h2>DEBUG $header</h2>\n";
	?>
<script type="application/javascript">
	const seconds = <?=$seconds?>;
	const url = "<?=$url?>";
	myTimeout = setTimeout(
		function(){
			window.location.replace(url)
		},
		seconds * 1000
		);
	console.log("redirect timeout set for ", seconds, ": timeoutID = ", myTimeout);
</script>
<a href="<?=$url?>">Redirect Now</a>
	<?php
	exit("... wait for redirect");
}
