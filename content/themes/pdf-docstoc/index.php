<?php get_header(); ?>
<div class="container_12 browse-container">
	<?php get_sidebar(); ?>

	<div class="grid_9">
		<div style="clear: both;"></div>
		<div itemscope itemtype="http://schema.org/ItemList" class="search-content">
			<div class="sectionheadercontainer section-spacer" style="margin-top: 0">
				<span itemprop="name" class="sectionheader">Recent Documents</span>
			</div>
			<ul itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem" class="content-row-wrap">
				<?php $recents = recent(array('type' => 'array', 'limit' => 10));
				foreach($recents as $recent):
					$recent_document = recent_document($recent['id'], array('type' => 'array', 'limit' => 1));

					if (! empty($recent_document)):
					$recent_document = current($recent_document);
					$link = generate_permalink($recent_document['title'], $recent['category']);
					$read = read_permalink($recent_document['id'], $recent['keyword'], $recent['category']);
				?>
				<li itemprop="item" itemscope itemtype="http://schema.org/Thing" class="doc-list-item">
					<span class="doc-image">
						<a itemprop="url" href="<?php echo $read; ?>" rel="nofollow"><img src="<?php echo theme_url(); ?>assets/img/preview.gif" width="85" height="113" data-src="<?php echo $recent_document['url']; ?>" alt="<?php echo $recent_document['title']; ?> document preview"></a>
					</span>
					<span class="doc-details">
						<span class="doc-title">
							<a href="<?php echo $link; ?>"><span itemprop="name"><?php echo $recent_document['title']; ?></span></a>
						</span>
						<span itemprop="description" class="doc-description">
							<?php echo $recent_document['description']; ?>
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
							<span>
								<span class="meta-label">Categories: </span>
								<span class="meta-value">
									<a href="<?php echo get_cat_permalink($recent_document['category']); ?>" class="doc-cat-readmore"><?php echo $recent_document['category']; ?></a>
								</span>
							</span>
						</span>
					</span>
					<div style="clear:both;"></div>
				</li>
				<?php endif; endforeach; ?>
			</ul>
			<div style="clear: both;"></div>
		</div>
		<div style="clear:both;"></div>
	</div>
	<div style="clear: both;"></div>
</div>
<?php get_footer(); ?>