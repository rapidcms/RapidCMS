<?php include_once("rapid.php"); ?>

<!doctype html>
<!--[if lt IE 7 ]> <html class="ie ie6 no-js" lang="en"> <![endif]-->
<!--[if IE 7 ]>	<html class="ie ie7 no-js" lang="en"> <![endif]-->
<!--[if IE 8 ]>	<html class="ie ie8 no-js" lang="en"> <![endif]-->
<!--[if IE 9 ]>	<html class="ie ie9 no-js" lang="en"> <![endif]-->
<!--[if gt IE 9]><!--><html class="no-js" lang="en"><!--<![endif]-->
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<meta name="title" content="">
	<meta name="description" content="">
	<meta name="google-site-verification" content="">
	<meta name="author" content="Jaime A. Rodriguez">
	<meta name="Copyright" content="">
	<meta name="HandheldFriendly" content="true" />
	<meta name="viewport" content="width=device-width, height=device-height, user-scalable=no,target-densityDpi=high-dpi" />

	<link rel="shortcut icon" href="images/favicon.ico">
	<link rel="apple-touch-icon" href="images/apple-touch-icon.png">
	<link rel="stylesheet" href="css/style.css">

	<title>&equiv; RapidCMS &raquo; Administration</title>

	<script data-main="js/main" src="js/modernizr-2.5.2.min.js"></script>
	<!--[if lt IE 9]>
		<script src="js/ie9.js"></script>
	<!--<![endif]-->
	<meta name="robots" content="noindex, nofollow">
	<?php head(); ?>
</head>
<body>
	<div class="wrapper clearfix">
		<h1 class="rapidlogo">&equiv;RapidCMS</h1>
		<h2 class="rapidlogo">Free and simple CMS.</h2>
		
		<?php 
		global $hooks;
			
		$hooks->add_action('admin_header');
		?>

		<section class="g640">