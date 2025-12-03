<?php

namespace FakeDebugHeader;

function header($header) {
	$pieces = explode(' ', $header);
	if (count($pieces) != 2) {
		echo "<h1>DEBUG header($header) ignored [!=2]</h1>\n";
		return;
	}
	list($command, $url) = $pieces;
	if ($command != "Location:") {
		echo "<h1>DEBUG header($header) ignored [2]</h1>\n";
		return;
	}
	echo "<h1>DEBUG header($header) sent: redirecting in 5 seconds</h1>\n";
	?>
<script type="application/javascript">
	const seconds = 5;
	myTimeout = setTimeout(
		function(){
			window.location.replace("<?=$url?>")
		},
		seconds * 1000
		);
	console.log("redirect timeout set for ", seconds, ": timeoutID = ", myTimeout);
</script>
	<?php
}