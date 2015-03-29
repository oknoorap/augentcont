<?php
require 'includes/helpers.php';

if (isset($_GET['file'])):
	$file = $_GET['file'];
	$file_title = title_case(normalize($file));
else:
	header("Location: index.php");
	die();
endif;
?>
<!DOCTYPE html>
<html>
<head>
<title>Download <?php echo $file_title; ?></title>
<noscript>
<meta http-equiv="refresh" content="2;dl.php?r=<?php echo base64_encode($file_title); ?>" />
</noscript>
<style>
body { font-family: arial; font-size: 14px; }
#main { text-align: center; padding-top: 100px; }
h1 { font-size: 25px; }
</style>
</head>

<body>
<div id="main">
<h1>Downloading <?php echo $file_title; ?></h1>
<p>Please wait <span class="count">2</span> seconds, retrieving best server location...</p>
</div>
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
<script type="text/javascript">
var count = 2, interval = null;
(function ($) {
  $(document).ready(function () {
     window.interval = setInterval(function () {
        if (count === 0) { window.location = "dl.php?r=<?php echo base64_encode($file_title); ?>"; clearInterval(window.interval); }
        else { $('.count').html(count); count--; }
     }, 1000);
  });
})(jQuery);
</script>
</body>
</html>