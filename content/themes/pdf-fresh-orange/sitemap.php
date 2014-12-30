<?php get_header(); ?>
<?php get_sidebar(); ?>

<div id="main">
	<h1 class="title"><?php echo title(); ?></h1>
	<?php
	$items = results('list');
	sort_by('time', $items, 'desc');
	if (count($items) > 0):
		echo '<ul class="square">';
		foreach ($items as $item): ?>
		<li><a href="<?php echo generate_permalink($item['keyword'], $item['category']); ?>" rel="nofollow"><?php echo capitalize($item['keyword']); ?></a></li>
		<?php
		endforeach;
		echo '</ul>';
	endif;
	?>
</div>
<?php get_footer(); ?>