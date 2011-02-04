<?php
session_start();
include_once("rapid.php");
global $cms;

switch ($_POST['action']) {
	case 'update':
		if ($cms->user->logged_in()) {
			if ($_POST['name'] <> "") {
				
				//$r = new rapid();
				$cms->user->load($_POST['name']);

				// Run any filters before updating
				$content = $cms->hooks->add_filter('update_content', $_POST['content']);
				
				// store the contents to database
				$cms->load($_POST['name']);
				$cms->blocks[$_POST['name']]->update($content);
				
				// Run any filters before sending via AJAX
				echo $cms->hooks->add_filter('refresh_content', $content);
			}
		}
		break;
	case 'load':
		if ($cms->user->logged_in()) {
			include_once("rapid.php");
			//$r = new rapid($hooks);
			$cms->load($_POST['name']);
			$content = $cms->hooks->add_filter('load_content', $cms->blocks[$_POST['name']]->get_content());
			echo $content;
		}
		break;
}
?>