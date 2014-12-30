<div class="grid_3">
	<ul class="content-type-nav">
		<li class="active">
			<a>
				All Content
				<span><?php echo get_count(); ?></span>
			</a>
		</li>
		<?php
		foreach(get_categories() as $list): ?>
		<li>
			<a href="<?php echo permalink($list); ?>">
				<?php echo $list['name']; ?> 
				<span><?php echo get_count($list['id']); ?></span>
			</a>
		</li>
		<?php endforeach; ?>
	</ul>
	<div class="filter-menu dark-anchor">
		<span class="filter-header">Popular Documents</span>
		<span class="filter-box">
			<?php popular(array('parent_class' => 'pad-content')); ?>
		</span>
	</div>
</div>