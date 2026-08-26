<?php
/**
 * Services page hero — intro copy followed by the same 4 `highlight` cards
 * used on the homepage (template-parts/front-page/highlights.php), reusing
 * the same query and template-parts/components/highlight-card.php.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$estatein_highlights = get_posts( array(
	'post_type'      => 'highlight',
	'posts_per_page' => 4,
	'orderby'        => 'menu_order',
	'order'          => 'ASC',
) );
?>
<section class="est-section" id="estServicesHero">
	<div class="container">
		<div class="row mb-4 mb-lg-5">
			<div class="col-lg-8">
				<h1 class="est-section-title">Elevate Your Real Estate Experience</h1>
				<p class="est-section-subtitle">Welcome to Estatein, where your real estate aspirations meet expert guidance. Explore our comprehensive range of services, each designed to cater to your unique needs and dreams.</p>
			</div>
		</div>

		<?php if ( ! empty( $estatein_highlights ) ) : ?>
			<div class="row g-3 g-lg-4">
				<?php foreach ( $estatein_highlights as $estatein_highlight ) : ?>
					<div class="col-6 col-lg-3">
						<?php get_template_part( 'template-parts/components/highlight-card', null, array( 'highlight' => $estatein_highlight ) ); ?>
					</div>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</div>
</section>
