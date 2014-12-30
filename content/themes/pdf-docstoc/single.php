<?php get_header(); ?>
<div id="main" class="container_12 browse-container">
	<?php get_sidebar(); ?>

	<div class="grid_9">
		<div class="grid_9 alpha">
			<h1 class="browse-title"><?php echo normalize(title(true), true); ?></h1>
		</div>
		<div style="clear: both;"></div>
		<?php $result = results(); ?>
		<div class="content">
			<div id="pdf-viewer" data-src="<?php echo $result['url']; ?>"></div>
		</div>
		<?php $related = related(); ?>
		<?php if(! empty($related)): ?>
		<div class="search-content">
			<div class="sectionheadercontainer section-spacer">
				<span class="sectionheader">Related</span>
			</div>
		<ul class="content-row-wrap">
		<?php foreach($related as $list):
			$link = generate_permalink($list['title'], get_category());
			$read = read_permalink($list['id']);
		?>
			<li class="doc-list-item">
					<span class="doc-image">
						<a href="<?php echo $read; ?>" rel="nofollow"><img src="<?php echo theme_url(); ?>assets/img/preview.gif" width="85" height="113" data-src="<?php echo $list['url']; ?>" alt="<?php echo $list['title']; ?> screnshot preview"></a>
					</span>
					<span class="doc-details">
						<span class="doc-title">
							<a href="<?php echo $link; ?>" itemprop="name"><?php echo $list['title']; ?></a>
						</span>
						<span class="doc-description">
							<?php echo $list['description']; ?>
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
		</div>
		<?php endif; ?>
	</div>
	<div style="clear: both;"></div>
</div>
<?php get_footer(); ?>