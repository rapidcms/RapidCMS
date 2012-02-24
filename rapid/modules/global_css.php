<?php
function global_css_show () {	
	GLOBAL $cms;
	GLOBAL $hooks;

	$show = "/*dynamically added css*/\n";

	echo "<style id='global-css'>";
	echo $hooks->add_filter('add_css', $show);
	echo "</style>";
}

hooks::do_action("head", "global_css_show");
?>