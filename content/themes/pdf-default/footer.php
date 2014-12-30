
<footer id="footer">
	<div class="row" id="page">
		<div class="small-12 columns">
			<ul class="inline-page">
				<li><a href="<?php echo base_url(); ?>" title="homepage">Home</a></li>
				<li><a href="<?php echo base_url(); ?>p/about" rel="nofollow">About Us</a></li>
				<li><a href="<?php echo base_url(); ?>p/copyrights" rel="nofollow">Copyrights</a></li>
				<li><a href="<?php echo base_url(); ?>p/privacy" rel="nofollow">Privacy Policy</a></li>
				<li><a href="<?php echo base_url(); ?>p/terms" rel="nofollow">Terms of Use</a></li>
				<li><a href="<?php echo base_url(); ?>p/contact" rel="nofollow">Contact Us</a></li>
				<li><a href="<?php echo base_url(); ?>p/faq" rel="nofollow">FAQ</a></li>
			</ul>
		</div>
	</div>

	<div class="row" id="sitemap">
		<div class="small-12 columns">
			<h3>Sitemaps by Alphabet</h3>
			<?php sitemaps(array('prefix' => 'ul', 'parent_class'=>'inline-page', 'item' => 'li', 'echo' => true)); ?>
		</div>
	</div>

	<div class="row" id="copyright">
		<div class="small-12 columns">
			<p>
			By Accessing this site, you agreed with our Terms of use and Privacy Policy.<br />
			This site powered by <a href="http://php.net" target="_blank" rel="nofollow">PHP</a> and <a href="http://mysql.com" target="_blank" rel="nofollow">MySQL</a><br />
			Copyrighted &copy; <?php echo date('Y'); ?> <a href="<?php echo base_url(); ?>"><?php echo domain(); ?></a>
			</p>
		</div>
	</div>
</footer>

<?php if(location('category') || location('single')): ?>
<script type="text/javascript" src="<?php echo theme_url(); ?>public/js/jquery.min.js"></script>
<?php endif; ?>
<?php if (location('category')): ?>
<script type="text/javascript" src="<?php echo theme_url(); ?>public/js/owl.carousel.min.js"></script>
<script type="text/javascript">
var owl = $('.owl-carousel');
owl.owlCarousel({
	margin: 10,
	loop: true,
	responsive: {
		0: {
			items: 1
		},
		600: {
			items: 1
		},
		1000: {
			items: 1
		}
	}
});
</script>
<?php endif; ?>
<?php footer(); ?>
</body>
</html>