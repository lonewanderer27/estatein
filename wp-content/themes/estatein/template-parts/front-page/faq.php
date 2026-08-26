<?php
/**
 * Frequently Asked Questions — Bootstrap carousel of `faq` cards, 3 per
 * slide on lg+ and 1 per slide on mobile, using the plugin's own
 * estatein_get_faqs(), each card rendered by
 * template-parts/components/faq-card.php.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$estatein_faqs = function_exists( 'estatein_get_faqs' ) ? estatein_get_faqs() : array();

if ( empty( $estatein_faqs ) ) {
	return;
}

$estatein_total        = count( $estatein_faqs );
$estatein_desktop_rows = array_chunk( $estatein_faqs, 3 );
$estatein_desktop_total_slides = count( $estatein_desktop_rows );
?>
<section class="est-section" id="estFaq">
	<div class="container">
		<div class="row align-items-end mb-4 gy-3">
			<div class="col-lg-8">
				<p class="est-eyebrow"><?php estatein_theme_icon( 'sparkle' ); ?><span class="est-eyebrow-dot"></span></p>
				<h2 class="est-section-title">Frequently Asked Questions</h2>
				<p class="est-section-subtitle">Find answers to common questions about Estatein&rsquo;s services, property listings, and the real estate process. We&rsquo;re here to provide clarity and assist you every step of the way.</p>
			</div>
			<div class="col-lg-4 text-lg-end">
				<a href="#" class="btn est-btn-outline">View All FAQ&rsquo;s</a>
			</div>
		</div>

		<div id="estFaqCarouselLg" class="carousel slide est-carousel d-none d-lg-block" data-bs-touch="true">
			<div class="carousel-inner">
				<?php foreach ( $estatein_desktop_rows as $estatein_index => $estatein_row ) : ?>
					<div class="carousel-item <?php echo 0 === $estatein_index ? 'active' : ''; ?>">
						<div class="row g-4">
							<?php foreach ( $estatein_row as $estatein_faq ) : ?>
								<div class="col-lg-4">
									<?php get_template_part( 'template-parts/components/faq-card', null, array( 'faq' => $estatein_faq ) ); ?>
								</div>
							<?php endforeach; ?>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
		</div>

		<div id="estFaqCarouselSm" class="carousel slide est-carousel d-lg-none" data-bs-touch="true">
			<div class="carousel-inner">
				<?php foreach ( $estatein_faqs as $estatein_index => $estatein_faq ) : ?>
					<div class="carousel-item <?php echo 0 === $estatein_index ? 'active' : ''; ?>">
						<?php get_template_part( 'template-parts/components/faq-card', null, array( 'faq' => $estatein_faq ) ); ?>
					</div>
				<?php endforeach; ?>
			</div>
		</div>

		<div class="est-carousel-controls">
			<span class="est-carousel-counter d-none d-lg-inline" data-total="<?php echo esc_attr( $estatein_desktop_total_slides ); ?>">01 of <?php echo esc_html( sprintf( '%02d', $estatein_desktop_total_slides ) ); ?></span>
			<span class="est-carousel-counter d-lg-none" data-total="<?php echo esc_attr( $estatein_total ); ?>">01 of <?php echo esc_html( sprintf( '%02d', $estatein_total ) ); ?></span>

			<div class="d-flex gap-2">
				<button class="est-carousel-arrow d-none d-lg-inline-flex" type="button" data-bs-target="#estFaqCarouselLg" data-bs-slide="prev" aria-label="Previous"><?php estatein_theme_icon( 'arrow-left' ); ?></button>
				<button class="est-carousel-arrow d-none d-lg-inline-flex" type="button" data-bs-target="#estFaqCarouselLg" data-bs-slide="next" aria-label="Next"><?php estatein_theme_icon( 'arrow-right' ); ?></button>

				<button class="est-carousel-arrow d-lg-none" type="button" data-bs-target="#estFaqCarouselSm" data-bs-slide="prev" aria-label="Previous"><?php estatein_theme_icon( 'arrow-left' ); ?></button>
				<button class="est-carousel-arrow d-lg-none" type="button" data-bs-target="#estFaqCarouselSm" data-bs-slide="next" aria-label="Next"><?php estatein_theme_icon( 'arrow-right' ); ?></button>
			</div>
		</div>
	</div>
</section>
