<?php get_header(); ?>
<div id="container">
	<div id="contents" class="left">
		<h1><?php echo title(); ?></h1>
		<?php echo results('content'); ?>
	</div>
	<?php get_sidebar() ;?>
	<div class="clear"></div>
</div>
<?php get_footer(); ?>