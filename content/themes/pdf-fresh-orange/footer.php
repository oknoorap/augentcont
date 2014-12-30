</div>
<footer>
	<div id="footer">
		<div id="footer-wrapper">
			<aside class="pages">
				<h3>Pages</h3>
				<ul>
					<li><a href="<?php echo base_url(); ?>" title="homepage">Home</a></li>
					<li><a href="<?php echo base_url(); ?>p/about" rel="nofollow">About Us</a></li>
					<li><a href="<?php echo base_url(); ?>p/copyrights" rel="nofollow">Copyrights</a></li>
					<li><a href="<?php echo base_url(); ?>p/privacy" rel="nofollow">Privacy Policy</a></li>
					<li><a href="<?php echo base_url(); ?>p/terms" rel="nofollow">Terms of Use</a></li>
					<li><a href="<?php echo base_url(); ?>p/contact" rel="nofollow">Contact Us</a></li>
					<li><a href="<?php echo base_url(); ?>p/faq" rel="nofollow">FAQ</a></li>
				</ul>
			</aside>
		</div>
		<h3>Sitemaps</h3>
		<?php sitemaps(array('prefix' => 'ul', 'parent_class'=>'inline-list', 'item' => 'li', 'echo' => true)); ?>
		<p>Copyright <?php echo date('Y') .' '. domain(); ?> All Rights Reserved.</p>
	</div>
</footer>
<script type="text/javascript" src="<?php echo theme_url(); ?>assets/js/jquery.min.js"></script>
<?php if (location('home') || location('category')): ?>
<script type="text/javascript" src="<?php echo theme_url(); ?>assets/js/owl.carousel.min.js"></script>
<script type="text/javascript">
(function ($) {
$(document).ready(function () {
	$('.owl-carousel').owlCarousel({
		loop:true,
		margin:10,
		nav:true,
		onChanged: function () {
			if (blazy !== undefined) {
				blazy.revalidate();
			} else {
				blazy = new Blazy({selector: 'img'});
			}
		},
		responsive:{
			0:{
				items:1
			},
			600:{
				items:3
			},
			1000:{
				items:5
			}
		}
	});
});
})(jQuery);
</script>
<?php endif; ?>
<?php footer(); ?>
</body>
</html>