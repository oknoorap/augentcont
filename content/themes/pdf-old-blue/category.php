<?php get_header(); ?>
<div id="container">
	<div itemscope itemtype="http://schema.org/ItemList" id="contents" class="left">
		<div class="breadcrumb"><?php echo breadcrumbs('&gt;'); ?></div>
		<h1 itemprop="name" class="title"><?php echo normalize(title(true), true); ?></h1>

		<?php
		$keywords = results();
		sort_by('count', $keywords, 'desc');
		foreach(array_slice($keywords, 0, 5) as $k => $list): ?>
		<h2><a href="<?php echo permalink($list); ?>" rel="nofollow"><?php echo $list['keyword']; ?></a></h2>
		<?php
			$documents = recent_document($list['id'], array('type' => 'array', 'limit' => 3));
			if (! empty($documents)):
			foreach($documents as $doc):
			$link = generate_permalink($doc['title'], get_category());
			$read = read_permalink($doc['id'], $doc['keyword'], get_category());
		?>
			<div itemprop="itemListElement" itemscope itemtype="http://schema.org/Thing" class="document">
				<div class="doc-thumb">
					<a href="<?php echo $read; ?>" rel="nofollow"><img src="<?php echo theme_url(); ?>assets/img/preview.gif" width="85" height="113" data-src="<?php echo $doc['url']; ?>" alt="<?php echo $doc['title']; ?> screnshot preview"></a>
				</div>
				<div class="doc-title" itemprop="name">
					<a href="<?php echo $link; ?>"><?php echo $doc['title']; ?></a>
				</div>
				<div class="doc-description">
					<?php echo $doc['description']; ?>
				</div>
				<div class="doc-info">
					<a href="<?php echo $read; ?>" rel="nofollow"><i class="fa fa-book"></i> Read</a> | <i class="fa fa-clock-o"></i> Date: <?php echo date('d M Y', $doc['time']); ?>
				</div>
			</div>
		<?php endforeach; endif; endforeach; ?>

		<?php
		sort_by('time', $keywords, 'desc');
		$remain = array_slice($keywords, 5, 105);
		if (count($remain) > 0): $remain = array_chunk($remain, 20); ?>
		<h2 class="subtitle">Recent Keywords</h2>
		<?php foreach ($remain as $items): ?>
				<?php $item = array_chunk($items, 4); ?>
				<?php foreach($item as $list): ?>
						<ul class="square">
							<?php foreach($list as $list_item): ?>
								<li><a href="<?php echo permalink($list_item); ?>" rel="nofollow"><?php echo $list_item['keyword']; ?></a></li>
							<?php endforeach; ?>
						</ul>
				<?php endforeach; ?>
		<?php endforeach; endif; ?>
	</div>
	<?php get_sidebar() ;?>
	<div class="clear"></div>
</div>
<?php get_footer(); ?>