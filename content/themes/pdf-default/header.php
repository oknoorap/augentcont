<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title><?php echo title(); ?></title>
	<link rel="shortcut icon" type="image/x-icon" href="<?php echo theme_url(); ?>favicon.ico">
	<link rel="stylesheet" href="<?php echo theme_url(); ?>public/css/foundation.min.css">
	<link rel="stylesheet" href="<?php echo theme_url(); ?>public/css/font-awesome.min.css">
	<?php if(location('category')): ?>
	<link rel="stylesheet" href="<?php echo theme_url(); ?>public/css/owl.carousel.css">
	<link rel="stylesheet" href="<?php echo theme_url(); ?>public/css/owl.theme.default.min.css">
	<?php endif; ?>
	<link rel="stylesheet" href="<?php echo theme_url(); ?>public/css/style.css">
	<?php head(); ?>
</head>

<body>
	<?php if(location('home')): ?>
		<header id="header">
			<div class="row">
				<div class="small-12 columns">
					<div class="row" id="logo">
						<div class="small-3 columns">
							<a href="<?php echo base_url(); ?>"><img src="<?php echo logo_url('public/img/logo.png'); ?>" width="300" height="80"></a>
						</div>
						<div class="small-9 columns">
							<h1><?php echo config('index.title'); ?></h1>
						</div>
					</div>
					<form method="POST" action="<?php echo base_url(); ?>search">
						<div class="row collapse">
							<div class="small-10 columns">
								<input type="text" name="q" id="q" placeholder="Search documents by keyword">
							</div>
							<div class="small-2 columns">
								<button class="button large-12 columns"><i class="fa fa-search"></i> Search</button>
							</div>
						</div>
					</form>
				</div>
			</div>
		</header>
	<?php else:
		$q = sanitize_query(current_path());
	?>
		<header id="header" class="not-home">
			<div class="row">
				<div class="small-2 columns">
					<a href="<?php echo base_url(); ?>"><img src="<?php echo logo_url('public/img/logo.png'); ?>" width="300" height="80"></a>
				</div>
				<div class="small-10 columns">
					<form method="POST" action="<?php echo base_url(); ?>search">
						<div class="row collapse">
							<div class="small-10 columns">
								<input type="text" name="q" id="q" placeholder="Search documents by keyword" value="<?php echo ($q !=='')? $q: ''; ?>">
								<?php if(location('category') || location('result') || location('single')): ?><input type="hidden" name="cat" value="<?php echo get_category(); ?>"><?php endif; ?>
							</div>
							<div class="small-2 columns">
								<button class="small button"><i class="fa fa-search"></i> Search</button>
							</div>
						</div>
					</form>
				</div>
			</div>
		</header>
	<?php endif; ?>