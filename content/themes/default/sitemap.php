<?php get_header(); ?>

<div id="main">
	<div class="row">
		<?php get_sidebar(); ?>
		<div class="large-9 columns">
			<div class="results color-<?php echo random_color(current_path()); ?>">
				<div class="title">
					<h1><?php echo title(); ?></h1>
				</div>

				<?php
				$all_results = results('list');
				sort_by('time', $all_results, 'desc');
				if (count($all_results) > 0):
					$all_results = array_chunk($all_results, 20);
					foreach ($all_results as $items):
					?>
					<div class="row">
						<?php $item = array_chunk($items, 4); ?>
						<?php foreach($item as $list): ?>
							<div class="large-4 end columns">
								<ul class="square">
									<?php foreach($list as $list_item): ?>
										<li><a href="<?php echo generate_permalink($list_item['keyword'], $list_item['category']); ?>" rel="nofollow"><?php echo capitalize($list_item['keyword']); ?></a></li>
									<?php endforeach; ?>
								</ul>
							</div>
						<?php endforeach; ?>
					</div>
					<?php
					endforeach;
				endif;
				?>
			</div>
		</div>
	</div>
</div>

<?php get_footer(); ?>