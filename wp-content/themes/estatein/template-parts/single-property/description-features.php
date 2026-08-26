<?php
/**
 * Description + Key Features and Amenities — two cards side by side.
 * Expects $args['property'].
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$estatein_property  = $args['property'];
$estatein_id        = $estatein_property->ID;
$estatein_bedrooms  = get_post_meta( $estatein_id, 'bedrooms', true );
$estatein_bathrooms = get_post_meta( $estatein_id, 'bathrooms', true );
$estatein_area      = get_post_meta( $estatein_id, 'area_sqft', true );
$estatein_amenities = function_exists( 'estatein_get_property_amenities' ) ? estatein_get_property_amenities( $estatein_id ) : array();
?>
<section class="est-section pt-4" id="estPropertyOverview">
	<div class="container">
		<div class="row g-4">
			<div class="col-lg-6">
				<div class="est-info-card h-100">
					<h2 class="est-info-card-title">Description</h2>
					<p class="est-info-card-text"><?php echo esc_html( wp_strip_all_tags( $estatein_property->post_content ) ); ?></p>

					<ul class="est-property-specs list-unstyled mb-0">
						<?php if ( $estatein_bedrooms ) : ?>
							<li>
								<span class="est-spec-heading">
									<?php estatein_theme_icon( 'bed' ); ?>
									<span class="est-spec-label">Bedrooms</span>
								</span>
								<span class="est-spec-value"><?php echo esc_html( sprintf( '%02d', $estatein_bedrooms ) ); ?></span>
							</li>
						<?php endif; ?>
						<?php if ( $estatein_bathrooms ) : ?>
							<li>
								<span class="est-spec-heading">
									<?php estatein_theme_icon( 'bath' ); ?>
									<span class="est-spec-label">Bathrooms</span>
								</span>
								<span class="est-spec-value"><?php echo esc_html( sprintf( '%02d', $estatein_bathrooms ) ); ?></span>
							</li>
						<?php endif; ?>
						<?php if ( $estatein_area ) : ?>
							<li>
								<span class="est-spec-heading">
									<?php estatein_theme_icon( 'building' ); ?>
									<span class="est-spec-label">Area</span>
								</span>
								<span class="est-spec-value"><?php echo esc_html( number_format( (float) $estatein_area ) ); ?> Square Feet</span>
							</li>
						<?php endif; ?>
					</ul>
				</div>
			</div>

			<div class="col-lg-6">
				<div class="est-info-card h-100">
					<h2 class="est-info-card-title">Key Features and Amenities</h2>
					<?php if ( ! empty( $estatein_amenities ) ) : ?>
						<ul class="est-amenities-list list-unstyled mb-0">
							<?php foreach ( $estatein_amenities as $estatein_amenity ) : ?>
								<li>
									<?php estatein_theme_icon( 'lightning' ); ?>
									<span><?php echo esc_html( $estatein_amenity ); ?></span>
								</li>
							<?php endforeach; ?>
						</ul>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</div>
</section>
