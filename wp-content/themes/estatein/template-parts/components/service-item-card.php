<?php
/**
 * Small service card inside a services/category-section.php grid. Display
 * only, no link — expects $args['icon'] (an estatein_theme_icon() key),
 * $args['title'], $args['description'].
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$estatein_icon        = $args['icon'];
$estatein_title       = $args['title'];
$estatein_description = $args['description'];
?>
<div class="est-service-item-card h-100">
	<span class="est-service-item-icon-rings">
		<span class="est-service-item-icon"><?php estatein_theme_icon( $estatein_icon ); ?></span>
	</span>
	<h3 class="est-service-item-title"><?php echo esc_html( $estatein_title ); ?></h3>
	<p class="est-service-item-description mb-0"><?php echo esc_html( $estatein_description ); ?></p>
</div>
