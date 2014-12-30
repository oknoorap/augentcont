	<div id="footer">
		<div id="footer-navigation">
			<div id="sitemap">
				<h3>Sitemaps</h3>
				<?php sitemaps(array('prefix' => 'ul', 'parent_class'=>'inline-page', 'item' => 'li', 'echo' => true)); ?>
			</div>
			<div id="footer-menu" class="left">
				<ul>
					<li><a href="<?php echo base_url(); ?>" title="homepage">Home</a></li>
					<li><a href="<?php echo base_url(); ?>p/about" rel="nofollow">About Us</a></li>
					<li><a href="<?php echo base_url(); ?>p/copyrights" rel="nofollow">Copyrights</a></li>
					<li><a href="<?php echo base_url(); ?>p/privacy" rel="nofollow">Privacy Policy</a></li>
					<li><a href="<?php echo base_url(); ?>p/terms" rel="nofollow">Terms of Use</a></li>
					<li><a href="<?php echo base_url(); ?>p/contact" rel="nofollow">Contact Us</a></li>
					<li><a href="<?php echo base_url(); ?>p/faq" rel="nofollow">FAQ</a></li>
				</ul>
			</div>
			<div id="copyright" class="right">
				Copyright <?php echo date('Y') .' '. domain(); ?> All Rights Reserved.
			</div>
			<div class="clear"></div>
		</div>
	</div>
</div>
<script type="text/javascript">
(function() {var a=document.getElementById("atab-popular"),b=document.getElementById("atab-picked"),c=document.getElementById("tab-popular"),d=document.getElementById("tab-picked");a.onclick=function(){a.parentNode.className="selected";b.parentNode.className="";c.style.display="block";d.style.display="none";return!1};b.onclick=function(){b.parentNode.className="selected";a.parentNode.className="";d.style.display="block";c.style.display="none";return!1};
})();
</script>
<?php footer(); ?>
</body>
</html>