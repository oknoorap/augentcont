<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title><?php echo title(); ?></title>
	<link rel="shortcut icon" type="image/x-icon" href="<?php echo theme_url(); ?>favicon.ico">
	<link rel="stylesheet" href="<?php echo theme_url(); ?>css/font-awesome.min.css">
	<link rel="stylesheet" href="<?php echo theme_url(); ?>css/normalize.css">
	<link rel="stylesheet" href="<?php echo theme_url(); ?>css/style.css">
	<?php head(); ?>
</head>

<body>
	<div id="main">
		<div id="header">
			<div id="logo">
				<a href="<?php echo base_url(); ?>"><span class="heading"><?php echo domain(); ?></span></a>
				<br />
				<?php echo config('index.title'); ?>
			</div>
		</div>