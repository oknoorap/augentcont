<!DOCTYPE html>
<html>
<head>
   <meta charset="utf-8">
   <title>Index of /<?php echo title(); ?></title>
</head>

<body>
<h1>Index of /<?php echo title(); ?></h1>
<pre>
<hr><img src="<?php echo theme_url(); ?>img/back.gif" alt="[DIR]" /> <a href="<?php echo base_url(); ?>">Parent Directory</a>
</pre>
<table>
<?php foreach(results() as $result): ?>
<tr>
   <td valign="middle" width="25">
      <img src="<?php echo theme_url(); ?>img/file-pdf.png" alt="[<?php echo $result['keyword']; ?>]" />
   </td>
   <td valign="top">
      <a href="<?php echo permalink($result); ?>"><?php echo $result['keyword']; ?></a> [PDF]
   </td>
</tr>
<?php endforeach; ?>
</table>
<hr>
<address>Apache/2.2.3 (Red Hat) Server at <?php echo str_replace('http://', '', base_url() . current_path()); ?> Port 80</address>
</body>
</html>