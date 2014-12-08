<?php get_header(); ?>

<div id="main">
	<div class="row">
		<div class="large-9 columns">
			<div class="results color-<?php echo random_color(); ?>">
				<div class="title">
					<h1><?php echo ucwords(title(true)); ?></h1>
					<div class="breadcrumb"><?php echo breadcrumbs(); ?></div>
				</div>

				<?php foreach(results() as $list): ?>
					<div class="result item">
						<h2><a href="<?php echo generate_permalink($list['title'], get_category()); ?>"><?php echo $list['title']; ?></a></h2>
						<p><?php echo capitalize($list['description']); ?></p>
						<div class="read">ID: <?php echo $list['id']; ?> <a rel="nofollow" href="<?php echo read_permalink($list['id']); ?>"><i class="fa fa-book"></i> Read PDF</a></div>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
		<?php get_sidebar(); ?>
	</div>
</div>

<?php get_footer(); ?>