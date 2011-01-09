<?php
function clean_html ($html)
{
	$c = htmlentities(trim($html), ENT_NOQUOTES, "UTF-8", false);
	$c = str_replace("&Acirc;", "", $c);
	$c = str_replace("&lt;","<", $c);
	$c = str_replace("&gt;",">", $c);
	// $c = str_replace("&quot;",'"', $c);
	// $c = str_replace("&amp;",'&', $c);
	
	// Make HTML better in safari & chrome
	$c = str_replace("<b>", "<strong>", $c);
	$c = str_replace("</b>", "</strong>", $c);
	$c = str_replace("<i>", "<em>", $c);
	$c = str_replace("</i>", "</em>", $c);
	$c = str_replace("<s>", "<strike>", $c);
	$c = str_replace("</s>", "</strike>", $c);
	$c = str_replace("<br>", "<br />", $c);
	
	return $c;
}

hooks::apply_filter("update_content", "clean_html", 1);
?>