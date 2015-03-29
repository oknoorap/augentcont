<?php
require 'includes/helpers.php';
$config = build_config('config.php');
$cpa_link = $config['cpa_url'];

if (isset($_GET)):
#----
if (isset($_GET['r'])):
##---

$link = base64_decode($_GET['r']);
$link = substr($link, 0, 25) . '..PDF';
$link = urlencode($link);
$link = str_replace('{q}', $link, $cpa_link);
header("Location: " . $link);
die();
##---
endif;
else:
header("Location: index.php");
endif;
