<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>Download <?php echo title(true); ?> [PDF]</title>
	<style>
		.download {
			padding: 5px 10px;
			background: #2ecc71;
			color: #ffffff;
			font-weight: bold;
			text-decoration: none;
			display: block;
			width: 100px;
			text-align: center;
			margin: 10px 0;
		}
	</style>
</head>

<body>
<h1><?php echo title(); ?></h1>
<pre>
<hr>
<strong>Description</strong>: <?php echo results('description') . "\n"; ?>
<strong>URL</strong>: <?php echo results('url'); ?>
<a href="#" class="download" onclick="startDownload(this);return false;">Download</a>
<div id="start-dl" style="display:none">
Please wait <span id="counter">15</span> to download PDF..., <a href="<?php echo current_url(); ?>">back to pdf</a>
</div>
</pre>

<script>
var time = 15,
dl = $('start-dl'),
counter = $('counter'),
counterHtml = counter.innerHTML;

function $ (id) {
	return document.getElementById(id);
}

function startDownload (e) {
	e.style.display = 'none';
	dl.style.display = 'block';
	window.st = setInterval(function () {
		if (time === 0) {
			clearTimeout(window.st);
			window.location = "<?php echo results('url'); ?>";
		}
		counter.innerHTML = time;
		time--;
	}, 1000);
}
</script>
</body>
</html>