<?php get_header(); ?>

<div id="main">
	<div class="row">
		<div class="large-9 columns">
			<div itemscope itemtype="http://schema.org/ItemList" class="results color-<?php echo random_color(); ?>">
				<div class="title">
					<h1 itemprop="name"><?php echo title(true); ?></h1>
					<div class="breadcrumb"><?php echo breadcrumbs(); ?></div>
				</div>

				<div class="content">
					<?php spinner(); ?>
					<a rel="nofollow" href="<?php echo download_url(title(true)); ?>" class="single large-12 columns success button"><i class="fa fa-download"></i> Download <?php echo title(true); ?> PDF</a>
				</div>


				<?php foreach(results() as $list): ?>
					<div itemprop="itemListElement" itemscope itemtype="http://schema.org/Thing" class="result item">
						<h2 itemprop="name"><a href="<?php echo generate_permalink($list['title'], get_category()); ?>"><?php echo $list['title']; ?></a></h2>
						<p><?php echo capitalize($list['description']); ?></p>
						<div class="read">ID: <?php echo $list['id']; ?> <a rel="nofollow" href="<?php echo read_permalink($list['id']); ?>"><i class="fa fa-book"></i> Read PDF</a> <a rel="nofollow" href="<?php echo download_url($list['title']); ?>"><i class="fa fa-download"></i> Download</a></div>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
		<?php get_sidebar(); ?>
	</div>
</div>

<?php get_footer(); ?>