<nav id="categories">
	<ul>
		<li>
			<a href="#">Categories</a>
			<ul>
				<?php
				foreach(get_categories() as $list): ?>
				<li>
					<a href="<?php echo permalink($list); ?>"><?php echo $list['name']; ?></a>
				</li>
				<?php endforeach; ?>
			</ul>
		</li>
	</ul>
</nav>