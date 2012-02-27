<?php

function logout_add_css ($show) {
	$added =  '#rapid_logout {	position: fixed;right: 15px;top: 5px}#rapid_logout li {display: inline;margin: 0;margin-right: 10px;border: 0}';
	return $show . $added;
}

function logout_button() {
	global $cms;
	$html  = "<script>jQuery(function ($) {";
	$html .= "$('body').append(\"";
	$html .= "<ul id='rapid_logout'>";
	$html .= "<li><a href='". RAPID_DIR . "'>Admin Section</a></li>";
	$html .= "<li><a href='". RAPID_DIR . "?action=logout' class='last'>Logout</a></li>";
	$html .= "</ul>";
	$html .= "\");});</script>";
	
	if ($cms->user->logged_in()) {
		echo "$html";
	}
}

hooks::do_action("head", "logout_button");
hooks::apply_filter("add_css", "logout_add_css", 1);
?>