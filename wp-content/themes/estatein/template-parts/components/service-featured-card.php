<?php
/**
 * Featured callout card closing out a services/category-section.php grid.
 * Expects $args['title'] and $args['description']; optional $args['learn_more_url'].
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$estatein_title          = $args['title'];
$estatein_description    = $args['description'];
$estatein_learn_more_url = isset( $args['learn_more_url'] ) ? $args['learn_more_url'] : '#';
?>
<div class="est-service-featured-card h-100">
	<div class="d-flex align-items-center justify-content-between gap-3 mb-2">
		<h3 class="est-service-featured-title mb-0"><?php echo esc_html( $estatein_title ); ?></h3>
		<a href="<?php echo esc_url( $estatein_learn_more_url ); ?>" class="btn est-btn-outline est-btn-sm flex-shrink-0">Learn More</a>
	</div>
	<p class="est-service-featured-description mb-0"><?php echo esc_html( $estatein_description ); ?></p>
</div>
