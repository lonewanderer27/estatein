<?php
/**
 * Highlight card component. Expects $args['highlight'] (a `highlight` post).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$estatein_highlight = $args['highlight'];
$estatein_link      = get_post_meta( $estatein_highlight->ID, 'link', true );
?>
<div class="est-highlight-card">
	<?php if ( $estatein_link ) : ?>
		<a href="<?php echo esc_url( $estatein_link ); ?>" class="est-highlight-arrow" aria-label="<?php echo esc_attr( $estatein_highlight->post_title ); ?>">
			<?php estatein_theme_icon( 'arrow-up-right' ); ?>
		</a>
	<?php endif; ?>

	<span class="est-highlight-icon-rings">
		<?php if ( has_post_thumbnail( $estatein_highlight ) ) : ?>
			<span class="est-highlight-icon"><?php echo get_the_post_thumbnail( $estatein_highlight, 'thumbnail', array( 'class' => 'est-highlight-icon-img' ) ); ?></span>
		<?php else : ?>
			<span class="est-highlight-icon"><?php estatein_theme_icon( 'home' ); ?></span>
		<?php endif; ?>
	</span>

	<h3 class="est-highlight-title"><?php echo esc_html( $estatein_highlight->post_title ); ?></h3>
</div>
