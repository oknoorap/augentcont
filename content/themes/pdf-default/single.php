<?php get_header(); ?>

<div id="main">
	<div class="row">
		<div class="large-9 columns">
			<div class="breadcrumb"><?php echo breadcrumbs(); ?></div>
			<div class="short-desc">
				<h2>Short Description</h2>
				<p><?php echo capitalize(results('description')); ?></p>
			</div>

			<div class="row">
				<div class="large-12 columns">
					<a href="<?php echo generate_permalink(results('title')); ?>" class="single right search button last"><i class="fa fa-search"></i> Find Similiar</a>
					<a class="single right download success button"><i class="fa fa-download"></i> Download</a>
					<a class="single right read alert button" style="display:none"><i class="fa fa-book"></i> Read</a>
				</div>
			</div>

			<div class="results single color-<?php echo random_color(); ?>">
				<div class="title">
					<h1><?php echo title(true); ?></h1>
				</div>
				<div class="viewer">
					<div id="pdf-viewer"></div>
					<div id="pdf-download" style="display:none">
						<p>Please wait <span class="counter">15</span> to download <strong>&quot;<?php echo results('title'); ?>.PDF&quot;</strong>.</p>
					</div>
				</div>
			</div>
		</div>
		<?php get_sidebar(); ?>
	</div>
</div>

<?php get_footer(); ?>