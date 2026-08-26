<?php
/**
 * Closing call-to-action band. Placeholder copy — not backed by a post
 * type or setting yet.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$estatein_properties_url = get_post_type_archive_link( 'property' );
?>
<section class="est-cta">
	<div class="container">
		<div class="row align-items-center gy-4">
			<div class="col-lg-8">
				<h2 class="est-cta-title">Start Your Real Estate Journey Today</h2>
				<p class="est-cta-subtitle mb-0">Your dream property is just a click away. Whether you&rsquo;re looking for a new home, a strategic investment, or expert real estate advice, Estatein is here to assist you every step of the way. Take the first step towards your real estate goals and explore our available properties or get in touch with our team for personalized assistance.</p>
			</div>
			<div class="col-lg-4 text-lg-end">
				<a href="<?php echo esc_url( $estatein_properties_url ? $estatein_properties_url : '#' ); ?>" class="btn btn-primary est-btn-primary">Explore Properties</a>
			</div>
		</div>
	</div>
</section>
