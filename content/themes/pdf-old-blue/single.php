<?php get_header(); ?>
<div id="container">
	<div id="contents" class="left">
		<div class="breadcrumb"><?php echo breadcrumbs('&gt;'); ?></div>
		<h1 class="title single"><?php echo normalize(title(true), true); ?></h1>
		<?php $result = results(); ?>
		<div class="info">Date: <?php echo date('d M Y', $result['time']); ?></div>
		<a href="<?php echo download_url(title(true)); ?>" rel="nofollow" class="big-btn">Download <?php echo title(true); ?> PDF</a>
		<div id="pdf-viewer" data-src="<?php echo $result['url']; ?>"></div>

		<div id="related">
			<?php $related = related(); ?>
			<?php if(! empty($related)): ?>
			<h3>Related</h3>
			<ul class="square">
			<?php foreach($related as $doc): ?>
				<li><a href="<?php echo read_permalink($doc['id'], $doc['keyword'], get_category()); ?>" title="<?php echo normalize($doc['title'], true); ?>" rel="nofollow"><?php echo normalize($doc['title'], true); ?></a></li>
			<?php endforeach; ?>
			</ul>
			<?php endif; ?>
		</div>
	</div>
	<?php get_sidebar() ;?>
	<div class="clear"></div>
</div>
<?php get_footer(); ?>