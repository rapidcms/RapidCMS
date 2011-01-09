<?php
session_start();

// make sure we are logged in.
if ($_SESSION['uid'] <> "")
{
	include_once("rapid.php");
	$r = new rapid($hooks);
	$r->load($_POST['name']);

	// Run any filters before updating
	$content = $hooks->add_filter('update_content', $_POST['content']);
	
	// store the contents to database
	$r->blocks[$_POST['name']]->update($content);
	
	// Run any filters before sending via AJAX
	echo $hooks->add_filter('refresh_content', $content);
}
?>