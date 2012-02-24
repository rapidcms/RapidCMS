<?php 
	session_start();
	
	require_once("includes/config.php");
	require_once("includes/class.hooks.php");
	require_once("includes/class.rapid.php");
	
	$hooks = new hooks;
	$cms = new rapid;
	
	function head() {
		global $cms;
		global $hooks;
	
		include("js/init.php");

		$hooks->add_action('head');
	}

	function logged_in () {
		if ($_SESSION['rapid_uid'] <> 0) {
			return true;
		} else {
			return false;
		}
	}

	
?>