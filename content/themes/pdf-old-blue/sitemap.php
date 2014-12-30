<?php get_header(); ?>
<div id="container">
	<div id="contents" class="left">
		<h1><?php echo title(); ?></h1>
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
	<?php get_sidebar() ;?>
	<div class="clear"></div>
</div>
<?php get_footer(); ?>