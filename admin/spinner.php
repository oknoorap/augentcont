<br />
<div class="panel callout">
	<p><strong>For better spin, please set minimum word spinner more than 3 words e.g {word 1|word 2|word 3|word 4}</strong></p>
	<p>Available syntax: <code>{site_name}</code>, <code>{domain}</code>, <code>{site_url}</code>, <code>{keyword}</code>, <code>{word1|word2|word3|....}</code> and <code>{..|generate_permalink}</code></p>
</div>
<dl class="tabs">
	<?php for($i = 1; $i <= 10; $i++): ?>
	<dd <?php echo ($i === 1)? 'class="active"':''; ?>><a class="nav-panel" rel="panel-<?php echo $i; ?>"><i class="fa fa-file-text-o"></i> Spin #<?php echo $i; ?></a></dd>
	<?php endfor; ?>
</dl>
<div class="tabs-content">
	<?php
	for($i = 1; $i <= 10; $i++):
		$json = read_file('../content/spinner/'. $i .'.spinner');
		$json = json_decode($json, TRUE);
		$content = htmlentities($json['content']);
	?>
	<div class="content <?php echo ($i === 1) ? 'active': ''; ?>" id="panel-<?php echo $i; ?>">
		<form method="POST" enctype="multipart/form-data" action="">
			<textarea class="editor" name="content"><?php echo $content; ?></textarea>
			<input type="hidden" name="name" value="<?php echo $i; ?>">
			<input type="hidden" name="type" value="spinner">
			<br />
			<p>
				<button name="submit" class="success"><i class="fa fa-save"></i> Save Changes</button>
			</p>
		</form>
	</div>
	<?php endfor; ?>
</div>
<script>var usingSpinner = '<?php echo (config('using.spinner')) ? 'true': 'false'; ?>';</script>