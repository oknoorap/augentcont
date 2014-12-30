<?php get_header(); ?>
<div class="container_12">
    <div class="grid_12">
        <header>
            <h3 style="margin-top: 50px"><?php echo title(); ?></h3>
        </header>
        <section>
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
        </section>
    </div>
</div>
<?php get_footer(); ?>