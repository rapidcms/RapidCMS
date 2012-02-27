<?php
function generator_show () {	
	GLOBAL $cms;
	GLOBAL $hooks;

	echo $hooks->add_filter('show_generator', "<meta name=\"generator\" content=\"RapidCMS\" />\n");
}

hooks::do_action("head", "generator_show");
?>