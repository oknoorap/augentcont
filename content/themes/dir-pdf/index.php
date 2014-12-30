<!DOCTYPE html>
<html>
<head>
   <meta charset="utf-8">
   <title>Index of /<?php echo title(); ?></title>
   <?php head(); ?>
</head>

<body>
<h1>Index of /<?php echo config('index.title'); ?></h1>
<pre>
<hr><img src="<?php echo theme_url(); ?>img/back.gif" alt="[DIR]" /> <a href="<?php echo base_url(); ?>">Parent Directory</a>

<?php foreach(results() as $result): ?>
<img src="<?php echo theme_url(); ?>img/folder.gif" alt="[<?php echo $result['name']; ?>]" /> <a href="<?php echo permalink($result); ?>"><?php echo $result['name']; ?></a>
<?php endforeach; ?>
<hr></pre>
<address>Apache/2.2.3 (Red Hat) Server at <?php echo str_replace('http://', '', base_url()); ?> Port 80</address>
<?php footer(); ?>
</body>
</html>