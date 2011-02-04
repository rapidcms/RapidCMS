<?php include_once("rapid.php"); ?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
        "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>&equiv; RapidCMS &raquo; Administration</title>
<link rel="stylesheet" type="text/css" href="http://yui.yahooapis.com/3.2.0/build/cssreset/reset-min.css" />
<link rel="stylesheet" type="text/css" href="css/main.css" />
<meta name="robots" content="noindex, nofollow">
<?php head(); ?>
</head>
<body>
	<div id="wrapper">
	<h1 class="rapidlogo">&equiv;RapidCMS</h1>
	<h2 class="rapidlogo">Free and simple CMS.</h2>
	<!--
	<ul class="menu">
		<li><a href="<?php echo RAPID_DIR; ?>">Admin Home</a></li>
		<li class="last">
		<?php
			if (isset($_SESSION['rapid_uid']) && $_SESSION['rapid_uuid'] == RAPID_UUID) {
				echo "<a href='?action=logout'>Logout</a></li>";
			} else {
				echo "<a href='" . RAPID_DIR . "'>Login</a></li>";
			}
		?>
	</ul>
	-->
	<?php 
		global $cms;
		$cms->hooks->add_action('admin_header');
	?>