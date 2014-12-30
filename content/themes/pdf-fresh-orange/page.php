<?php get_header(); ?>
<?php get_sidebar(); ?>

<div id="main">
	<h1 class="title"><?php echo title(); ?></h1>
	<?php echo results('content'); ?>
</div>
<?php get_footer(); ?>