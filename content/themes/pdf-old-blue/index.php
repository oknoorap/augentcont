<?php get_header(); ?>
<div id="container">
	<div id="contents" class="left">
		<div itemscope itemtype="http://schema.org/ItemList">
			<h2 itemprop="name">Recent Document</h2>
			<div itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
				<?php $recents = recent(array('type' => 'array', 'limit' => 10));
				foreach($recents as $recent):
					$recent_document = recent_document($recent['id'], array('type' => 'array', 'limit' => 1));

					if (! empty($recent_document)):
					$recent_document = current($recent_document);
					$link = generate_permalink($recent_document['title'], $recent['category']);
					$read = read_permalink($recent_document['id'], $recent['keyword'], $recent['category']);
				?>
					<div itemprop="item" itemscope itemtype="http://schema.org/Thing" class="document">
						<div class="doc-thumb">
							<a itemprop="url" href="<?php echo $read; ?>" rel="nofollow"><img src="<?php echo theme_url(); ?>img/thumbload.gif" width="85" height="113" data-src="<?php echo $recent_document['url']; ?>" alt="<?php echo $recent_document['title']; ?> screnshot preview"></a>
						</div>
						<div itemprop="name" class="doc-title">
							<a href="<?php echo $link; ?>"><?php echo $recent_document['title']; ?></a>
						</div>
						<div itemprop="description" class="doc-description">
							<?php echo $recent_document['description']; ?>
						</div>
						<div class="doc-info">
							<a href="<?php echo $read; ?>" rel="nofollow"><i class="fa fa-book"></i> Read</a> | <i class="fa fa-clock-o"></i> Date: <?php echo date('d M Y', $recent_document['time']); ?>
						</div>
					</div>
				<?php endif; endforeach; ?>
			</div>
		</div>
	</div>
	<?php get_sidebar() ;?>
	<div class="clear"></div>
</div>
<?php get_footer(); ?>