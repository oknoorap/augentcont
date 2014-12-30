<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<meta charset="utf-8">
	<title><?php echo title(); ?></title>
	<link rel="shortcut icon" type="image/x-icon" href="<?php echo theme_url(); ?>favicon.ico">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link href="<?php echo theme_url(); ?>assets/css/style.css" rel="stylesheet">
	<?php if (location('home') || location('category')): ?>
	<link href="<?php echo theme_url(); ?>assets/css/owl.carousel.css" rel="stylesheet">
	<link href="<?php echo theme_url(); ?>assets/css/owl.theme.default.min.css" rel="stylesheet">
	<?php endif; ?>
	<script src="<?php echo theme_url(); ?>assets/js/modernizr.js"></script>
	<?php head(); ?>
</head>
<body>
	<header>
		<p>
			<a href="<?php echo base_url(); ?>">
				<img src="<?php echo logo_url('assets/img/logo.png'); ?>" width="290" height="40">
			</a>
		</p>
	</header>
	<div class="wrapper">
		<nav id="search">
			<form method="POST" action="<?php echo base_url(); ?>search">
				<?php $q = (location('home')) ? '': sanitize_query(current_path()); ?>
				<input id="search_input" type="search" name="q" placeholder="Search for a documents" class="acInput" value="<?php echo ($q !=='')? $q: ''; ?>">
				<?php if(location('category') || location('result') || location('single')): ?><input type="hidden" name="cat" value="<?php echo get_category(); ?>"><?php endif; ?>
				<button type="submit" name="submit">Go!</button>
			</form>
		</nav>