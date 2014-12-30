<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<meta charset="utf-8">
	<title><?php echo title(); ?></title>
	<link rel="shortcut icon" type="image/x-icon" href="<?php echo theme_url(); ?>favicon.ico">
	<meta name="viewport" content="width=1100">
	<link href="<?php echo theme_url(); ?>assets/css/bootstrap.css" rel="stylesheet" type="text/css">
	<link href="<?php echo theme_url(); ?>assets/css/layoutmain.css" type="text/css" rel="stylesheet">
	<link href="<?php echo theme_url(); ?>assets/css/premiumbrowse.css" type="text/css" rel="stylesheet">
	<!--[if lt IE 9]>
	<script> document.createElement('header'); document.createElement('nav'); document.createElement('menu'); document.createElement('section'); document.createElement('article'); document.createElement('aside'); document.createElement('footer'); document.createElement('hgroup'); document.createElement('figure'); document.createElement('figcaption'); </script>
	<![endif]-->
	<!--[if gte IE 9]>
	<style type="text/css"> .nav-gradient {filter: none; padding-left: 5px; padding-right: 6px; padding-top: 1px; padding-bottom: 1px; } </style>
	<![endif]-->
	<?php head(); ?>
</head>
<body>
	<nav class="NavContainer">
		<div class="container_12">
			<div class="nav-bottom-container">
				<div class="grid_3 logo-container">
					<br />
					<a href="<?php echo base_url(); ?>">
						<img src="<?php echo logo_url('assets/img/logo.png'); ?>" width="194" height="13">
					</a>
				</div>
				<div class="grid_8">
					<form style="position:relative;" method="POST" action="<?php echo base_url(); ?>search" id="headerSearchForm">
						<?php $q = (location('home')) ? '': sanitize_query(current_path()); ?>
						<input class="search-input-box" maxlength="150" id="appendedPrependedInputSearch" name="q" type="search"  value="<?php echo ($q !=='')? $q: ''; ?>" placeholder="Search documents and resources">
						<?php if(location('category') || location('result') || location('single')): ?><input type="hidden" name="cat" value="<?php echo get_category(); ?>"><?php endif; ?>
						<input name="submit" type="submit" value="&#xe61f;" class="icon icon-search">
					</form>
				</div>
			</div>
		</div>
	</nav>
	<div class="nav-inline"></div>

	<?php if (location('category') || location('result') || location('single')): ?>
	<div id="browse-breadcrumbs" class="browse-breadcrumbs clearfix">
		<div class="container_12">
			<div class="grid_12">
				<?php echo breadcrumbs('<span class="icon-arrow_right"></span>'); ?>
			</div>
		</div>
	</div>
	<?php endif; ?>