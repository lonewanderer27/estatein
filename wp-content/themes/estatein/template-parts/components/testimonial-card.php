<?php
/**
 * Testimonial card component. Expects $args['testimonial'] (a
 * `testimonial` post).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$estatein_testimonial = $args['testimonial'];
$estatein_id           = $estatein_testimonial->ID;
$estatein_rating       = function_exists( 'estatein_get_testimonial_rating' ) ? estatein_get_testimonial_rating( $estatein_id ) : 5;
$estatein_location     = estatein_theme_location_label( $estatein_id );
$estatein_name         = get_post_meta( $estatein_id, 'contact_person', true ) ?: $estatein_testimonial->post_title;
$estatein_initial      = mb_substr( $estatein_name, 0, 1 );
?>
<div class="est-testimonial-card">
	<?php estatein_theme_render_stars( $estatein_rating ); ?>

	<h3 class="est-testimonial-title"><?php echo esc_html( $estatein_testimonial->post_title ); ?></h3>
	<p class="est-testimonial-quote"><?php echo esc_html( wp_trim_words( get_the_content( null, false, $estatein_testimonial ), 26 ) ); ?></p>

	<div class="est-testimonial-author d-flex align-items-center gap-2">
		<?php if ( has_post_thumbnail( $estatein_testimonial ) ) : ?>
			<?php echo get_the_post_thumbnail( $estatein_testimonial, 'estatein-avatar', array( 'class' => 'est-testimonial-avatar' ) ); ?>
		<?php else : ?>
			<span class="est-testimonial-avatar est-testimonial-avatar-fallback" aria-hidden="true"><?php echo esc_html( $estatein_initial ); ?></span>
		<?php endif; ?>

		<div>
			<span class="est-testimonial-name d-block"><?php echo esc_html( $estatein_name ); ?></span>
			<?php if ( $estatein_location ) : ?>
				<span class="est-testimonial-location d-block small"><?php echo esc_html( $estatein_location ); ?></span>
			<?php endif; ?>
		</div>
	</div>
</div>
