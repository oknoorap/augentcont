<div style="padding-top: 40px;clear: both;"></div>
	<footer>
		<div class="linkSection">
			<div class="container_12">
				<div class="grid_2">
					<h4>Pages</h4>
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
				<div class="grid_10 disc">
					<h4>Disclaimer</h4>
					<p>
						<?php echo domain(); ?> is the best destination to search ebooks and documents. It hosts the best quality and widest selection of professional documents (over 10 million). Search or Browse for any specific document or resource you need for your business. Or explore sorted by categories.
					</p>
					<p>
						By Accessing this site, you agreed with our Terms of use and Privacy Policy. This site powered by <a href="http://php.net" target="_blank" rel="nofollow">PHP</a> and <a href="http://mysql.com" target="_blank" rel="nofollow">MySQL</a>.
					</p>
				</div>
			</div>
			<div style="clear: both;"></div>
		</div>
		<div class="bottom-footer">
			<div class="container_12" style="font-size: 12px">
				<span class="grid_12">
					&copy; <?php echo date('Y') .' '. domain(); ?> All Rights Reserved.
				</span>
				<span class="grid_12 sitemaps">
					<?php sitemaps(array('prefix' => 'div', 'parent_class'=>'inline-page', 'item' => 'span', 'echo' => true)); ?>
				</span>
			</div>
			<div style="clear: both;"></div>
		</div>
	</footer>
</body>
</html>