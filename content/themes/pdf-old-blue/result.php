<?php get_header(); ?>
<div id="container">
	<div id="contents" class="left">
		<div class="breadcrumb"><?php echo breadcrumbs('&gt;'); ?></div>

		<div itemscope itemtype="http://schema.org/ItemList">
			<h1 itemprop="name" class="title"><?php echo ptitle(); ?></h1>
			<a href="<?php echo download_url(title(true)); ?>" rel="nofollow" class="big-btn">Download <?php echo title(true); ?> PDF</a>

			<?php spinner(); ?>

			<div itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
			<?php foreach(results() as $list):
			$link = generate_permalink($list['title'], get_category());
			$read = read_permalink($list['id']);
			?>
				<div itemprop="item" itemscope itemtype="http://schema.org/Thing" class="document">
					<div class="doc-thumb">
						<a href="<?php echo $read; ?>" rel="nofollow"><img src="<?php echo theme_url(); ?>img/thumbload.gif" width="85" height="113" data-src="<?php echo $list['url']; ?>" alt="<?php echo $list['title']; ?> screnshot preview"></a>
					</div>
					<div itemprop="name" class="doc-title">
						<a href="<?php echo $link; ?>"><?php echo $list['title']; ?></a>
					</div>
					<div itemprop="description" class="doc-description">
						<?php echo $list['description']; ?>
					</div>
					<div class="doc-info">
						<a itemprop="url" href="<?php echo $read; ?>" rel="nofollow"><i class="fa fa-book"></i> Read</a> | <a href="<?php echo download_url($list['title']); ?>" rel="nofollow"><i class="fa fa-download"></i> Download</a> | <i class="fa fa-clock-o"></i> Date: <?php echo date('d M Y', $list['time']); ?>
					</div>
				</div>
			<?php endforeach; ?>
			</div>
		</div>
	</div>
	<?php get_sidebar() ;?>
	<div class="clear"></div>
</div>
<?php get_footer(); ?>