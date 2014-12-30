<?php get_header(); ?>
<div itemscope itemtype="http://schema.org/ItemList" id="main" class="container_12 browse-container">
	<?php get_sidebar(); ?>

	<div class="grid_9">
		<div class="grid_9 alpha">
			<h1 itemprop="name" class="browse-title"><?php echo normalize(title(true), true); ?></h1>
		</div>
		<div style="clear: both;"></div>
		<?php
		$all_results = results();
		sort_by('count', $all_results, 'desc');
		foreach(array_slice($all_results, 0, 5) as $k => $list):
			$items = recent_document($list['id'], array('echo'=>false, 'type' => 'array', 'limit' => 10));
			if (! empty($items)):
		?>
			<div class="search-content">
				<div class="sectionheadercontainer section-spacer">
					<span class="sectionheader"><a href="<?php echo permalink($list); ?>" rel="nofollow"><?php echo $list['keyword']; ?></a></span>
				</div>
				<ul itemprop="itemListElement" itemscope itemtype="http://schema.org/Thing" class="content-row-wrap">
				<?php
				foreach($items as $doc):
					$link = generate_permalink($doc['title'], get_category());
					$read = read_permalink($doc['id'], $doc['keyword'], $doc['category']);
				?>
					<li class="doc-list-item">
						<span class="doc-image">
							<a href="<?php echo $read; ?>" rel="nofollow"><img src="<?php echo theme_url(); ?>assets/img/preview.gif" width="85" height="113" data-src="<?php echo $doc['url']; ?>" alt="<?php echo $doc['title']; ?> document preview"></a>
						</span>
						<span class="doc-details">
							<span class="doc-title">
								<a href="<?php echo $link; ?>" itemprop="name"><?php echo $doc['title']; ?></a>
							</span>
							<span class="doc-description">
								<?php echo $doc['description']; ?>
							</span>
							<span id="iconPlusExpand0" class="doc-footer ">
								<span>
									<span class="doc-flag certified">
										<span class="icon-certified"></span>
										CERTIFIED
									</span>
								</span>
								<span class="meta-label icon-docType-pdf icon"></span>
								<span class="meta-value">pdf</span>
							</span>
						</span>
						<div style="clear:both;"></div>
					</li>
				<?php endforeach; ?>
				</ul>
				<div style="clear: both;"></div>
			</div>
			<div style="clear:both;"></div>
		<?php
			endif;
		endforeach;
		?>

		<?php
		sort_by('time', $all_results, 'desc');
		$remain = array_slice($all_results, 5, 105);
		if (count($remain) > 0): $remain = array_chunk($remain, 20); ?>
		<h2 class="subtitle">Recent Keywords</h2>
		<?php foreach ($remain as $items): ?>
				<?php $item = array_chunk($items, 4); ?>
				<ul class="square">
				<?php foreach($item as $list): ?>
					<?php foreach($list as $list_item): ?>
						<li><a href="<?php echo permalink($list_item); ?>" rel="nofollow"><?php echo $list_item['keyword']; ?></a></li>
					<?php endforeach; ?>
				<?php endforeach; ?>
				</ul>
		<?php endforeach; endif; ?>
	</div>
	<div style="clear: both;"></div>
</div>
<?php get_footer(); ?>