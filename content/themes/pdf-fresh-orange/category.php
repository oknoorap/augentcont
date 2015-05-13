<?php get_header(); ?>
<?php get_sidebar(); ?>

<div id="main">
	<div class="breadcrumb"><?php echo breadcrumbs('&gt;'); ?></div>
	<h1 class="title"><?php echo ptitle(); ?></h1>

	<?php
	$all_results = results();
	sort_by('count', $all_results, 'desc');
	foreach(array_slice($all_results, 0, 5) as $k => $list):
		$items = recent_document($list['id'], array('echo'=>false, 'type' => 'array', 'limit' => 10));
		if (! empty($items)):
	?>
	<section itemscope itemtype="http://schema.org/ItemList" class="recent featured">
		<h2 itemprop="name"><a href="<?php echo permalink($list); ?>" rel="nofollow"><?php echo $list['keyword']; ?></a></h2>
		<div itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem" class="books owl-carousel">
			<?php
			foreach($items as $recent):
				$link = generate_permalink($recent['title'], $recent['category']);
				$read = read_permalink($recent['id'], $recent['keyword'], $recent['category']);
			?>
			<div class="item" itemprop="item" itemscope itemtype="http://schema.org/Thing">
				<a itemprop="url" href="<?php echo $read; ?>" rel="nofollow"> <img src="<?php echo theme_url(); ?>assets/img/preview.gif" width="110" height="155" data-src="<?php echo $recent['url']; ?>" alt="<?php echo $recent['title']; ?> preview"></a>
				<br />
				<h3 itemprop="name"><a href="<?php echo $read; ?>"><?php echo $recent['title']; ?></a></h3>
			</div>
			<?php endforeach;?>
		</div>
	</section>
	<?php endif; endforeach; ?>

	<?php sort_by('time', $all_results, 'desc'); $remain = array_slice($all_results, 5, 105);
	if (count($remain) > 0): $remain = array_chunk($remain, 20); ?>
	<h2 class="subtitle">Recent Keywords</h2>
	<?php
		foreach ($remain as $items):
	?>
		<ul class="square">
			<?php $item = array_chunk($items, 4); ?>
			<?php foreach($item as $list): ?>
				<?php foreach($list as $list_item): ?>
					<li><a href="<?php echo permalink($list_item); ?>" rel="nofollow"><?php echo $list_item['keyword']; ?></a></li>
				<?php endforeach; ?>
			<?php endforeach; ?>
		</ul>
	<?php endforeach; endif;
	?>
</div>
<?php get_footer(); ?>