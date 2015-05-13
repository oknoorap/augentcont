<?php get_header(); ?>
<?php get_sidebar(); ?>

<div id="main">
	<div class="breadcrumb"><?php echo breadcrumbs('&gt;'); ?></div>
	<div itemscope itemtype="http://schema.org/ItemList">
		<h1 itemprop="name" class="title"><?php echo ptitle(); ?></h1>

		<article class="category">
			<?php spinner(); ?>
			<a href="<?php echo download_url(title(true)); ?>" rel="nofollow" class="download-pdf">Download <?php echo title(true); ?> PDF</a>

			<div itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem" class="books list">
				<?php foreach(results() as $list):
				$link = generate_permalink($list['title'], get_category());
				$read = read_permalink($list['id']);
				?>
				<article class="pdf" itemprop="item" itemscope itemtype="http://schema.org/Thing">
					<div class="book">
						<h3 itemprop="name"><a href="<?php echo $link; ?>"><?php echo $list['title']; ?></a></h3>
						<a itemprop="url" href="<?php echo $read; ?>" rel="nofollow"><img src="<?php echo theme_url(); ?>assets/img/preview.gif" width="100" height="141" data-src="<?php echo $list['url']; ?>" alt="<?php echo $list['title']; ?> preview" class="thumb"></a>
						<p itemprop="description" class="description truncate"><?php echo $list['description']; ?></p>
						<a href="<?php echo $read; ?>" rel="nofollow" class="button">Read</a> <a href="<?php echo download_url($list['title']); ?>" rel="nofollow" class="button">Download</a>
					</div>
				</article>
				<?php endforeach; ?>

			</div>
		</article>
	</div>
</div>
<?php get_footer(); ?>