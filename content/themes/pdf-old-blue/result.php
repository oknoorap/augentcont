<?php get_header(); ?>
<div id="container">
	<div itemscope itemtype="http://schema.org/ItemList" id="contents" class="left">
		<div class="breadcrumb"><?php echo breadcrumbs('&gt;'); ?></div>
		<h1 itemprop="name" class="title"><?php echo normalize(title(true), true); ?></h1>
		
		<?php spinner(); ?>
		<?php foreach(results() as $list):
		$link = generate_permalink($list['title'], get_category());
		$read = read_permalink($list['id']);
		?>
			<div itemprop="itemListElement" itemscope itemtype="http://schema.org/Thing" class="document">
				<div class="doc-thumb">
					<a href="<?php echo $read; ?>" rel="nofollow"><img src="<?php echo theme_url(); ?>img/thumbload.gif" width="85" height="113" data-src="<?php echo $list['url']; ?>" alt="<?php echo $list['title']; ?> screnshot preview"></a>
				</div>
				<div itemprop="name" class="doc-title">
					<a href="<?php echo $link; ?>"><?php echo $list['title']; ?></a>
				</div>
				<div class="doc-description">
					<?php echo $list['description']; ?>
				</div>
				<div class="doc-info">
					<a href="<?php echo $read; ?>" rel="nofollow"><i class="fa fa-book"></i> Read</a> | <i class="fa fa-clock-o"></i> Date: <?php echo date('d M Y', $list['time']); ?>
				</div>
			</div>
		<?php endforeach; ?>
	</div>
	<?php get_sidebar() ;?>
	<div class="clear"></div>
</div>
<?php get_footer(); ?>