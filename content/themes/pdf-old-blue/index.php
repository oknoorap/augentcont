<?php get_header(); ?>
<div id="container">
	<div id="contents" class="left">
		<h2>Latest added documents</h2>
		<?php $recents = recent(array('type' => 'array', 'limit' => 10));
		foreach($recents as $recent):
			$recent_document = recent_document($recent['id'], array('type' => 'array', 'limit' => 1));
			$recent_document = $recent_document[0];
			$link = generate_permalink($recent_document['title'], $recent['category']);
			$read = read_permalink($recent_document['id'], $recent['keyword'], $recent['category']);
		?>
			<div class="document">
				<div class="doc-thumb">
					<a href="<?php echo $read; ?>" rel="nofollow"><img src="<?php echo theme_url(); ?>img/thumbload.gif" width="85" height="113" data-src="<?php echo $recent_document['url']; ?>" alt="<?php echo $recent_document['title']; ?> screnshot preview"></a>
				</div>
				<div class="doc-title">
					<a href="<?php echo $link; ?>"><?php echo $recent_document['title']; ?></a>
				</div>
				<div class="doc-description">
					<?php echo $recent_document['description']; ?>
				</div>
				<div class="doc-info">
					<a href="<?php echo $read; ?>" rel="nofollow"><i class="fa fa-book"></i> Read</a> | <i class="fa fa-clock-o"></i> Date: <?php echo date('d M Y', $recent_document['time']); ?>
				</div>
			</div>
	
		<?php endforeach; ?>
	</div>
	<?php get_sidebar() ;?>
	<div class="clear"></div>
</div>
<?php get_footer(); ?>