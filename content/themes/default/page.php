<?php get_header(); ?>

<div id="main">
	<div class="row">
		<div class="large-12 columns">
			<h1><?php echo title(); ?></h1>
			<?php echo results('content'); ?>
		</div>
	</div>
</div>

<?php get_footer(); ?>