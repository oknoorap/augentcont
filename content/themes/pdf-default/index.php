<?php get_header(); ?>

<div id="main">
	<div class="row">
		<div class="small-12 columns">
			<h2 class="browse">Browse Documents</h2>
			<?php $results = array_chunk(results(), 2);
			foreach($results as $result): ?>
				<div class="row result">
					<?php foreach($result as $list): ?>
					<div class="small-6 end columns">
						<h2><i class="fa fa-<?php echo $list['icon']; ?>"></i> <a href="<?php echo permalink($list); ?>"><?php echo $list['name']; ?></a></h2>
						<?php echo show_item($list); ?>
					</div>
					<?php endforeach; ?>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</div>

<div id="widget-home">
	<div class="row">
		<div class="large-4 columns">
			<h2 class="recent"><i class="fa fa-sort-amount-desc"></i> Recent Documents</h2>
			<?php recent(); ?>
		</div>
		<div class="large-4 columns">
			<h2 class="popular"><i class="fa fa-thumbs-o-up"></i> Popular Documents</h2>
			<?php popular(); ?>
		</div>
		<div class="large-4 columns">
			<h2 class="picked"><i class="fa fa-heart"></i> Staff-picked Documents</h2>
			<?php random(); ?>
		</div>
	</div>
</div>

<?php get_footer(); ?>