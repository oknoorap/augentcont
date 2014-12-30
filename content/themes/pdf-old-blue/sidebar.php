<div id="sidebar" class="right">
	<div class="box">
		<form method="POST" action="<?php echo base_url(); ?>search">
			<?php $q = (location('home')) ? '': sanitize_query(current_path()); ?>
			<input type="text" name="q" id="q" placeholder="Search documents by keyword" value="<?php echo ($q !=='')? $q: ''; ?>">
			<?php if(location('category') || location('result') || location('single')): ?><input type="hidden" name="cat" value="<?php echo get_category(); ?>"><?php endif; ?>
			<input id="search-btn" type="submit" name="submit" value="search" />
		</form>
	</div>

	<div class="box-border">
		<strong>Categories</strong>
		<table>
			<tbody>
				<?php $categories = array_chunk(get_categories(), 2);
				foreach($categories as $result): ?>
					<tr>
						<?php foreach($result as $list): ?>
						<td><a href="<?php echo permalink($list); ?>"><?php echo $list['name']; ?></a></td>
						<?php endforeach; ?>
						<?php if (count($result)%2 === 1): ?>
						<td>&nbsp;</td>
						<?php endif; ?>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	</div>

	<div class="box-border">
		<div class="tab">
			<ul id="tab">
				<li class="selected"><a href="#" id="atab-popular">Popular documents</a></li>
				<li><a href="#" id="atab-picked">Recent Uploaded</a></li>
			</ul>
		</div>
		<div class="tab-contents">
			<div class="tab-content" id="tab-popular">
				<?php popular(); ?>
			</div>
			<div class="tab-content" id="tab-picked" style="display: none">
				<?php recent(); ?>
			</div>
		</div>
	</div>
</div>