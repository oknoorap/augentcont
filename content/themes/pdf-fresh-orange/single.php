<?php get_header(); ?>
<?php get_sidebar(); ?>

<div id="main">
	<div class="breadcrumb"><?php echo breadcrumbs('&gt;'); ?></div>
	<h1 class="title"><?php echo normalize(title(true), true); ?></h1>
	<?php $result = results(); ?>
	<div id="pdf-viewer" data-src="<?php echo $result['url']; ?>"></div>

	<article class="category">
		<div id="related" class="books list">
			<?php $related = related(); ?>
			<?php if(! empty($related)): ?>
			<h2>Related</h2>
			<?php foreach($related as $list):
				$link = generate_permalink($list['title'], get_category());
				$read = read_permalink($list['id']);
			?>
				<article class="pdf">
					<div class="book">
						<h3><a href="<?php echo $link; ?>"><?php echo $list['title']; ?></a></h3>
						<a href="<?php echo $read; ?>" rel="nofollow"><img src="<?php echo theme_url(); ?>assets/img/preview.gif" width="100" height="141" data-src="<?php echo $list['url']; ?>" alt="<?php echo $list['title']; ?> preview" class="thumb"></a>
						<p class="description truncate"><?php echo $list['description']; ?></p>
						<a href="<?php echo $read; ?>" rel="nofollow" class="button">Read</a>
					</div>
				</article>
			<?php endforeach; ?>
			<?php endif; ?>
		</div>
	</article>
</div>
<?php get_footer(); ?>