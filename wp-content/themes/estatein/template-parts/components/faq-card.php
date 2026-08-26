<?php
/**
 * FAQ card component. Expects $args['faq'] (a `faq` post).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$estatein_faq = $args['faq'];
?>
<div class="est-faq-card">
	<h3 class="est-faq-question"><?php echo esc_html( $estatein_faq->post_title ); ?></h3>
	<p class="est-faq-excerpt"><?php echo esc_html( get_the_excerpt( $estatein_faq ) ); ?></p>
	<a href="<?php echo esc_url( get_permalink( $estatein_faq ) ); ?>" class="btn est-btn-outline est-btn-sm">Read More</a>
</div>
