<dl class="tabs">
	<dd class="active"><a class="nav-panel" rel="panel-1"><i class="fa fa-file-text-o"></i> About</a></dd>
	<dd><a class="nav-panel" rel="panel-2"><i class="fa fa-file-text-o"></i> Copyrights</a></dd>
	<dd><a class="nav-panel" rel="panel-3"><i class="fa fa-file-text-o"></i> Privacy Policy</a></dd>
	<dd><a class="nav-panel" rel="panel-4"><i class="fa fa-file-text-o"></i> Terms of Use</a></dd>
	<dd><a class="nav-panel" rel="panel-5"><i class="fa fa-file-text-o"></i> Contact Us</a></dd>
	<dd><a class="nav-panel" rel="panel-6"><i class="fa fa-file-text-o"></i> FAQ</a></dd>
</dl>
<div class="tabs-content">
	<?php
	$page_name = array(
		'about', 'copyrights', 'privacy', 'terms', 'contact', 'faq'
	);
	for($i = 1; $i < 7; $i++):
		$json = read_file('../content/pages/'. $page_name[$i - 1] .'.txt');
		$json = json_decode($json, TRUE);
		$title = htmlentities($json['title']);
		$content = htmlentities($json['content']);
	?>
	<div class="content <?php echo ($i === 1) ? 'active': ''; ?>" id="panel-<?php echo $i; ?>">
		<form method="POST" enctype="multipart/form-data" action="">
			<input type="text" name="title" placeholder="Page Title" value="<?php echo $title; ?>">
			<textarea class="editor" name="content"><?php echo $content; ?></textarea>
			<input type="hidden" name="name" value="<?php echo $page_name[$i - 1]; ?>">
			<input type="hidden" name="type" value="page">
			<br />
			<p>
				<button name="submit" class="success"><i class="fa fa-save"></i> Save Changes</button>
			</p>
		</form>
	</div>
	<?php endfor; ?>
</div>
<script>var usingSpinner = '<?php echo (config('using.spinner')) ? 'true': 'false'; ?>';</script>