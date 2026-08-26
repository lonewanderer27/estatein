<?php
/**
 * Testimonials — Bootstrap carousel of `testimonial` cards, 3 per slide on
 * lg+ and 1 per slide on mobile, each card rendered by
 * template-parts/components/testimonial-card.php.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$estatein_testimonials = get_posts( array(
	'post_type'      => 'testimonial',
	'posts_per_page' => -1,
	'orderby'        => 'date',
	'order'          => 'DESC',
	'meta_query'     => array(
		'relation' => 'OR',
		array(
			'key'     => 'company',
			'compare' => 'NOT EXISTS',
		),
		array(
			'key'     => 'company',
			'value'   => '',
		),
	),
) );

if ( empty( $estatein_testimonials ) ) {
	return;
}

$estatein_total        = count( $estatein_testimonials );
$estatein_desktop_rows = array_chunk( $estatein_testimonials, 3 );
$estatein_desktop_total_slides = count( $estatein_desktop_rows );

$estatein_desktop_arrow_class = 'est-carousel-arrow d-none ' . ( $estatein_desktop_total_slides > 1 ? 'd-lg-inline-flex' : '' );
$estatein_mobile_arrow_class  = 'est-carousel-arrow ' . ( $estatein_total > 1 ? 'd-lg-none' : 'd-none' );
?>
<section class="est-section est-section-alt" id="estTestimonials">
	<div class="container">
		<div class="row align-items-end mb-4 gy-3">
			<div class="col-lg-8">
				<p class="est-eyebrow"><?php estatein_theme_icon( 'sparkle' ); ?><span class="est-eyebrow-dot"></span></p>
				<h2 class="est-section-title">What Our Clients Say</h2>
				<p class="est-section-subtitle">Read the success stories and heartfelt testimonials from our valued clients. Discover why they chose Estatein for their real estate needs.</p>
			</div>
			<div class="col-lg-4 text-lg-end">
				<a href="#" class="btn est-btn-outline">View All Testimonials</a>
			</div>
		</div>

		<div id="estTestimonialsCarouselLg" class="carousel slide est-carousel d-none d-lg-block" data-bs-touch="true">
			<div class="carousel-inner">
				<?php foreach ( $estatein_desktop_rows as $estatein_index => $estatein_row ) : ?>
					<div class="carousel-item <?php echo 0 === $estatein_index ? 'active' : ''; ?>">
						<div class="row g-4">
							<?php foreach ( $estatein_row as $estatein_testimonial ) : ?>
								<div class="col-lg-4">
									<?php get_template_part( 'template-parts/components/testimonial-card', null, array( 'testimonial' => $estatein_testimonial ) ); ?>
								</div>
							<?php endforeach; ?>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
		</div>

		<div id="estTestimonialsCarouselSm" class="carousel slide est-carousel d-lg-none" data-bs-touch="true">
			<div class="carousel-inner">
				<?php foreach ( $estatein_testimonials as $estatein_index => $estatein_testimonial ) : ?>
					<div class="carousel-item <?php echo 0 === $estatein_index ? 'active' : ''; ?>">
						<?php get_template_part( 'template-parts/components/testimonial-card', null, array( 'testimonial' => $estatein_testimonial ) ); ?>
					</div>
				<?php endforeach; ?>
			</div>
		</div>

		<div class="est-carousel-controls">
			<span class="est-carousel-counter d-none d-lg-inline" data-total="<?php echo esc_attr( $estatein_desktop_total_slides ); ?>">01 of <?php echo esc_html( sprintf( '%02d', $estatein_desktop_total_slides ) ); ?></span>
			<span class="est-carousel-counter d-lg-none" data-total="<?php echo esc_attr( $estatein_total ); ?>">01 of <?php echo esc_html( sprintf( '%02d', $estatein_total ) ); ?></span>

			<div class="d-flex gap-2">
				<button class="<?php echo esc_attr( $estatein_desktop_arrow_class ); ?>" type="button" data-bs-target="#estTestimonialsCarouselLg" data-bs-slide="prev" aria-label="Previous"><?php estatein_theme_icon( 'arrow-left' ); ?></button>
				<button class="<?php echo esc_attr( $estatein_desktop_arrow_class ); ?>" type="button" data-bs-target="#estTestimonialsCarouselLg" data-bs-slide="next" aria-label="Next"><?php estatein_theme_icon( 'arrow-right' ); ?></button>

				<button class="<?php echo esc_attr( $estatein_mobile_arrow_class ); ?>" type="button" data-bs-target="#estTestimonialsCarouselSm" data-bs-slide="prev" aria-label="Previous"><?php estatein_theme_icon( 'arrow-left' ); ?></button>
				<button class="<?php echo esc_attr( $estatein_mobile_arrow_class ); ?>" type="button" data-bs-target="#estTestimonialsCarouselSm" data-bs-slide="next" aria-label="Next"><?php estatein_theme_icon( 'arrow-right' ); ?></button>
			</div>
		</div>
	</div>
</section>
