<?php
/**
 * Site footer: brand + newsletter, link columns, social icons, copyright.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$estatein_contact = function_exists( 'estatein_get_contact_info' ) ? estatein_get_contact_info() : array();
$estatein_socials  = array_filter( array(
	'facebook'  => $estatein_contact['facebook'] ?? '',
	'linkedin'  => $estatein_contact['linkedin'] ?? '',
	'instagram' => $estatein_contact['instagram'] ?? '',
) );
?>
<footer class="est-footer">
	<div class="container">
		<div class="row gy-4">
			<div class="col-lg-3">
				<a class="est-brand est-brand-footer d-inline-flex" href="<?php echo esc_url( home_url( '/' ) ); ?>">
					<?php estatein_site_brand(); ?>
				</a>
				<form class="est-newsletter" action="#" method="post">
					<label for="estNewsletterEmail" class="visually-hidden">Email address</label>
					<span class="est-newsletter-icon"><?php estatein_theme_icon( 'mail' ); ?></span>
					<input type="email" class="form-control est-newsletter-input" id="estNewsletterEmail" name="email" placeholder="Enter your Email" required>
					<button type="submit" class="est-newsletter-submit" aria-label="Subscribe">
						<?php estatein_theme_icon( 'send' ); ?>
					</button>
				</form>
			</div>

			<div class="col-6 col-lg-2">
				<h6 class="est-footer-heading">Home</h6>
				<ul class="est-footer-links">
					<li><a href="<?php echo esc_url( home_url( '/' ) ); ?>#estHero">Hero Section</a></li>
					<li><a href="<?php echo esc_url( home_url( '/' ) ); ?>#estHighlights">Features</a></li>
					<li><a href="<?php echo esc_url( home_url( '/' ) ); ?>#estProperties">Properties</a></li>
					<li><a href="<?php echo esc_url( home_url( '/' ) ); ?>#estTestimonials">Testimonials</a></li>
					<li><a href="<?php echo esc_url( home_url( '/' ) ); ?>#estFaq">FAQ's</a></li>
				</ul>
			</div>

			<div class="col-6 col-lg-2">
				<h6 class="est-footer-heading">About Us</h6>
				<ul class="est-footer-links">
					<li><a href="#">Our Story</a></li>
					<li><a href="#">Our Works</a></li>
					<li><a href="#">How It Works</a></li>
					<li><a href="#">Our Team</a></li>
					<li><a href="#">Our Clients</a></li>
				</ul>
			</div>

			<div class="col-6 col-lg-2">
				<h6 class="est-footer-heading">Properties</h6>
				<ul class="est-footer-links">
					<li><a href="<?php echo esc_url( get_post_type_archive_link( 'property' ) ?: '#' ); ?>">Portfolio</a></li>
					<li><a href="#">Categories</a></li>
				</ul>

				<h6 class="est-footer-heading mt-4">Contact Us</h6>
				<ul class="est-footer-links">
					<li><a href="#">Contact Form</a></li>
					<li><a href="#">Our Offices</a></li>
				</ul>
			</div>

			<div class="col-6 col-lg-3">
				<h6 class="est-footer-heading">Services</h6>
				<ul class="est-footer-links">
					<li><a href="#">Valuation Mastery</a></li>
					<li><a href="#">Strategic Marketing</a></li>
					<li><a href="#">Negotiation Wizardry</a></li>
					<li><a href="#">Closing Success</a></li>
					<li><a href="#">Property Management</a></li>
				</ul>
			</div>
		</div>

		<hr class="est-footer-divider">

		<div class="d-flex flex-column flex-lg-row align-items-center justify-content-between gap-3">
			<p class="mb-0 small est-footer-copy">&copy; <?php echo esc_html( date( 'Y' ) ); ?> <?php bloginfo( 'name' ); ?>. All Rights Reserved.</p>

			<?php if ( ! empty( $estatein_socials ) ) : ?>
				<ul class="est-social-list list-unstyled d-flex gap-2 mb-0">
					<?php foreach ( $estatein_socials as $network => $url ) : ?>
						<li>
							<a href="<?php echo esc_url( $url ); ?>" class="est-social-link" target="_blank" rel="noopener noreferrer" aria-label="<?php echo esc_attr( ucfirst( $network ) ); ?>">
								<?php estatein_theme_icon( $network ); ?>
							</a>
						</li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>

			<a href="#" class="small est-footer-copy text-decoration-underline">Terms &amp; Conditions</a>
		</div>
	</div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
