<?php get_header(); ?>
<?php get_sidebar(); ?>
<div id="main">
	<section class="recent featured">
		<h2>Recent Documents</h2>
		<div class="books owl-carousel">
			<?php
			$recents = recent(array('type' => 'array', 'limit' => 10));
			foreach($recents as $recent):
				$recent_document = recent_document($recent['id'], array('type' => 'array', 'limit' => 1));
				$recent_document = $recent_document[0];
				$read = read_permalink($recent_document['id'], $recent['keyword'], $recent['category']);
			?>
			<div class="item">
				<a href="<?php echo $read; ?>" rel="nofollow"> <img src="<?php echo theme_url(); ?>assets/img/preview.gif" width="110" height="155" data-src="<?php echo $recent_document['url']; ?>" alt="<?php echo $recent_document['title']; ?> preview"></a>
				<br />
				<h3> <a href="<?php echo $read; ?>"> <?php echo $recent_document['title']; ?></a> </h3>
			</div>
			<?php endforeach; ?>
		</div>
	</section>

	<section class="featured">
		<h2>Editors' picks</h2>
		<div class="books owl-carousel">
			<?php $picked = random(array('type' => 'array', 'limit' => 10));
			foreach($picked as $recent):
				$recent_document = recent_document($recent['keyword_id'], array('type' => 'array', 'limit' => 1));
				$recent_document = $recent_document[0];
				$read = read_permalink($recent_document['id'], $recent['keyword'], $recent['category']);
			?>
			<div class="item">
				<a href="<?php echo $read; ?>" rel="nofollow"> <img src="<?php echo theme_url(); ?>assets/img/preview.gif" width="110" height="155" data-src="<?php echo $recent_document['url']; ?>" alt="<?php echo $recent_document['title']; ?> preview"></a>
				<br />
					<h3> <a href="<?php echo $read; ?>"> <?php echo $recent_document['title']; ?></a> </h3>
			</div>
			<?php endforeach; ?>
		</div>
	</section>
</div>

<?php get_footer(); ?>