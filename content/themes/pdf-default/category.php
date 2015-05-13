<?php get_header(); ?>

<div id="main">
	<div class="row">
		<div class="small-12 columns">
			<h1 class="title"><?php echo ptitle(); ?></h1>
			<div class="breadcrumb"><?php echo breadcrumbs('&gt;'); ?></div>

			<div id="popular-in">
				<h2 class="subtitle">Popular in <?php echo title(true); ?></h2>
				<div class="owl-carousel">
				<?php
					$all_results = results();
					sort_by('count', $all_results, 'desc');
					foreach(array_slice($all_results, 0, 5) as $k => $list):
						$items = recent_document($list['id'], array('parent_class'=>'', 'echo'=>false));
						if (! empty($items)):
					?>
					<div class="item item-<?php echo $k; ?>" itemscope itemtype="http://schema.org/ItemList">
						<h2 itemprop="name"><a href="<?php echo permalink($list); ?>" rel="nofollow"><?php echo $list['keyword']; ?></a></h2>
						<?php echo $items; ?>
					</div>
				<?php endif; endforeach; ?>
				</div>
			</div>

			<?php sort_by('time', $all_results, 'desc'); $remain = array_slice($all_results, 5, 105);
			if (count($remain) > 0): $remain = array_chunk($remain, 20); ?>
			<h2 class="subtitle">Recent Keywords</h2>
			<?php
				foreach ($remain as $items):
			?>
				<div class="row">
					<?php $item = array_chunk($items, 4); ?>
					<?php foreach($item as $list): ?>
						<div class="large-4 end columns">
							<ul class="square">
								<?php foreach($list as $list_item): ?>
									<li><a href="<?php echo permalink($list_item); ?>" rel="nofollow"><?php echo $list_item['keyword']; ?></a></li>
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

<?php get_footer(); ?>