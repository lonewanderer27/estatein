<?php
/**
 * Property gallery hero: title/location/price header, a thumbnail strip,
 * and a Bootstrap carousel of image pairs built from the property's
 * Gallery block content (estatein_get_property_gallery_images()). Expects
 * $args['property'].
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$estatein_property = $args['property'];
$estatein_id       = $estatein_property->ID;
$estatein_price    = get_post_meta( $estatein_id, 'price', true );
$estatein_location = estatein_theme_location_label( $estatein_id );
$estatein_images   = estatein_get_property_gallery_images( $estatein_id );
$estatein_slides   = array_chunk( $estatein_images, 2 );
?>
<section class="est-section pb-4" id="estPropertyGallery">
	<div class="container">
		<div class="row align-items-start justify-content-between gy-2 mb-4">
			<div class="col-lg-8">
				<h1 class="est-property-heading"><?php echo esc_html( $estatein_property->post_title ); ?></h1>
				<?php if ( $estatein_location ) : ?>
					<p class="est-property-location d-flex align-items-center gap-1 mb-0">
						<?php estatein_theme_icon( 'building' ); ?>
						<?php echo esc_html( $estatein_location ); ?>
					</p>
				<?php endif; ?>
			</div>
			<div class="col-lg-4 text-lg-end">
				<span class="est-property-price-label d-block">Price</span>
				<span class="est-property-hero-price"><?php echo $estatein_price ? '$' . esc_html( number_format( (float) $estatein_price ) ) : '—'; ?></span>
			</div>
		</div>

		<?php if ( ! empty( $estatein_slides ) ) : ?>
			<div id="estGalleryCarousel" class="carousel slide est-carousel est-gallery-carousel" data-bs-touch="true">
				<div class="est-gallery-card">
				<?php if ( count( $estatein_images ) > 1 ) : ?>
					<div class="est-gallery-thumbs">
						<?php foreach ( $estatein_images as $estatein_index => $estatein_image ) : ?>
							<button
								type="button"
								data-bs-target="#estGalleryCarousel"
								data-bs-slide-to="<?php echo esc_attr( (int) floor( $estatein_index / 2 ) ); ?>"
								class="<?php echo floor( $estatein_index / 2 ) === 0 ? 'active' : ''; ?>"
								aria-label="View photo <?php echo esc_attr( $estatein_index + 1 ); ?>"
							><img src="<?php echo esc_url( $estatein_image ); ?>" alt="" /></button>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>

				<div class="carousel-inner">
					<?php foreach ( $estatein_slides as $estatein_slide_index => $estatein_pair ) : ?>
						<div class="carousel-item <?php echo 0 === $estatein_slide_index ? 'active' : ''; ?>">
							<div class="row g-3">
								<?php foreach ( $estatein_pair as $estatein_photo ) : ?>
									<div class="col-12 col-sm-6">
										<img src="<?php echo esc_url( $estatein_photo ); ?>" alt="" class="est-gallery-photo" />
									</div>
								<?php endforeach; ?>
							</div>
						</div>
					<?php endforeach; ?>
				</div>

				<?php if ( count( $estatein_slides ) > 1 ) : ?>
					<div class="est-gallery-controls">
						<button class="est-carousel-arrow" type="button" data-bs-target="#estGalleryCarousel" data-bs-slide="prev" aria-label="Previous"><?php estatein_theme_icon( 'arrow-left' ); ?></button>
						<div class="est-carousel-dots">
							<?php foreach ( $estatein_slides as $estatein_slide_index => $estatein_pair ) : ?>
								<button
									type="button"
									data-bs-target="#estGalleryCarousel"
									data-bs-slide-to="<?php echo esc_attr( $estatein_slide_index ); ?>"
									class="est-gallery-dot <?php echo 0 === $estatein_slide_index ? 'active' : ''; ?>"
									aria-label="Slide <?php echo esc_attr( $estatein_slide_index + 1 ); ?>"
								></button>
							<?php endforeach; ?>
						</div>
						<button class="est-carousel-arrow" type="button" data-bs-target="#estGalleryCarousel" data-bs-slide="next" aria-label="Next"><?php estatein_theme_icon( 'arrow-right' ); ?></button>
					</div>
				<?php endif; ?>
				</div>
			</div>
		<?php else : ?>
			<div class="est-gallery-placeholder" aria-hidden="true"><?php estatein_theme_icon( 'building' ); ?></div>
		<?php endif; ?>
	</div>
</section>
