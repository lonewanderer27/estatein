<?php
/**
 * Property card component. Expects $args['property'] (a `property` post).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$estatein_property = $args['property'];
$estatein_id        = $estatein_property->ID;
$estatein_price     = get_post_meta( $estatein_id, 'price', true );
$estatein_bedrooms   = get_post_meta( $estatein_id, 'bedrooms', true );
$estatein_bathrooms  = get_post_meta( $estatein_id, 'bathrooms', true );
$estatein_type       = get_post_meta( $estatein_id, 'property_type', true );
?>
<div class="est-property-card">
	<a href="<?php echo esc_url( get_permalink( $estatein_property ) ); ?>" class="est-property-media">
		<?php if ( has_post_thumbnail( $estatein_property ) ) : ?>
			<?php echo get_the_post_thumbnail( $estatein_property, 'estatein-property-card', array( 'class' => 'est-property-img' ) ); ?>
		<?php else : ?>
			<span class="est-property-img est-property-img-placeholder" aria-hidden="true"><?php estatein_theme_icon( 'building' ); ?></span>
		<?php endif; ?>
	</a>

	<div class="est-property-body">
		<h3 class="est-property-title">
			<a href="<?php echo esc_url( get_permalink( $estatein_property ) ); ?>" class="text-decoration-none"><?php echo esc_html( $estatein_property->post_title ); ?></a>
		</h3>
		<p class="est-property-excerpt">
			<?php echo esc_html( wp_trim_words( get_the_excerpt( $estatein_property ), 14 ) ); ?>
			<a href="<?php echo esc_url( get_permalink( $estatein_property ) ); ?>" class="est-property-readmore">Read More</a>
		</p>

		<ul class="est-property-meta list-unstyled d-flex flex-wrap gap-2 mb-3">
			<?php if ( $estatein_bedrooms ) : ?>
				<li><?php estatein_theme_icon( 'bed' ); ?><?php echo esc_html( $estatein_bedrooms ); ?>-Bedroom</li>
			<?php endif; ?>
			<?php if ( $estatein_bathrooms ) : ?>
				<li><?php estatein_theme_icon( 'bath' ); ?><?php echo esc_html( $estatein_bathrooms ); ?>-Bathroom</li>
			<?php endif; ?>
			<?php if ( $estatein_type ) : ?>
				<li><?php estatein_theme_icon( 'building' ); ?><?php echo esc_html( $estatein_type ); ?></li>
			<?php endif; ?>
		</ul>

		<div class="d-flex align-items-center justify-content-between gap-3">
			<div>
				<span class="est-property-price-label d-block">Price</span>
				<span class="est-property-price">
					<?php echo $estatein_price ? '$' . esc_html( number_format( (float) $estatein_price ) ) : '—'; ?>
				</span>
			</div>
			<a href="<?php echo esc_url( get_permalink( $estatein_property ) ); ?>" class="btn btn-primary est-btn-primary">View Property Details</a>
		</div>
	</div>
</div>
