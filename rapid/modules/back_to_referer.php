<?php
function request_uri() {
	$is_admin_page = false;
	
	$admin_page[] = 'http://' . $_SERVER['HTTP_HOST'] . RAPID_DIR;
	$admin_page[] = 'http://' . $_SERVER['HTTP_HOST'] . RAPID_DIR . '/';
	$admin_page[] = 'http://' . $_SERVER['HTTP_HOST'] . RAPID_DIR . '/index.php';
	$admin_page[] = 'http://' . $_SERVER['HTTP_HOST'] . RAPID_DIR . '/?action=logout';
	
	$referrer = preg_replace('/\?.*/', '', $_SERVER['HTTP_REFERER']);
	
	if (isset($referrer)) {
		foreach ($admin_page as $p) {
			if ($referrer == $p) {
				$is_admin_page = true;
			}
		}
		
		if (!$is_admin_page) {
			$_SESSION['referer'] = $_SERVER['HTTP_REFERER'];
		}
	}
}

function admin_header_referer () {
	global $cms;
	
	if (isset($_SESSION['referer']) && $cms->user->logged_in()) {
		if ($_SESSION['referer'] <> "http://" . $_SERVER['HTTP_HOST'] . RAPID_DIR . "/index.php") {
			?>
			<h2>Live Edit</h2>
			<p>Go back to the <a href="<?php echo $_SESSION['referer']; ?>">page</a> to edit live</p>
			<?php
		}
	}
}

hooks::do_action("admin_header", "request_uri");
hooks::do_action("admin_sidebar", "admin_header_referer");