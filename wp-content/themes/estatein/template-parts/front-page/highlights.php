<?php
/**
 * Highlights row — 4 icon cards sourced from the `highlight` CPT, each
 * rendered by template-parts/components/highlight-card.php.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$estatein_highlights = get_posts( array(
	'post_type'      => 'highlight',
	'posts_per_page' => 4,
	'orderby'        => 'menu_order',
	'order'          => 'ASC',
) );

if ( empty( $estatein_highlights ) ) {
	return;
}
?>
<section class="est-highlights" id="estHighlights">
	<div class="container">
		<div class="row g-3 g-lg-4">
			<?php foreach ( $estatein_highlights as $estatein_highlight ) : ?>
				<div class="col-6 col-lg-3">
					<?php get_template_part( 'template-parts/components/highlight-card', null, array( 'highlight' => $estatein_highlight ) ); ?>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>
