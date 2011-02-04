<?php
function logout_button() {
	global $cms;
	
	$html  = "<ul id='rapid_logout'>";
	$html .= "<li><a href='". RAPID_DIR . "'>Admin Section</a></li>";
	$html .= "<li><a href='". RAPID_DIR . "?action=logout' class='last'>Logout</a></li>";
	$html .= "</ul>";
	
	if ($cms->user->logged_in()) {
	echo "$html";
	?>
	<script>
		$(document).ready(function () {
			$('#rapid_logout').css({
				'position':'fixed',
				'right': '15px',
				'top': '5px'
			});
			
			$('#rapid_logout li').css({
				'display': 'inline',
				'margin': '0',
				'margin-right': '10px',
				'border': '0'
			});
		});
	</script>
	<?php	
	}
}
hooks::do_action("head", "logout_button");
?>