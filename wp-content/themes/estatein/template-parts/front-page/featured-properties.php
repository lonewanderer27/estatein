<?php
/**
 * Featured Properties — a properties-carousel of every `property` post,
 * rendered by template-parts/components/properties-carousel.php.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$estatein_properties = get_posts( array(
	'post_type'      => 'property',
	'posts_per_page' => -1,
	'orderby'        => 'date',
	'order'          => 'DESC',
) );

if ( empty( $estatein_properties ) ) {
	return;
}

$estatein_properties_url = get_post_type_archive_link( 'property' );
?>
<section class="est-section" id="estProperties">
	<div class="container">
		<div class="row align-items-end mb-4 gy-3">
			<div class="col-lg-8">
				<p class="est-eyebrow"><?php estatein_theme_icon( 'sparkle' ); ?><span class="est-eyebrow-dot"></span></p>
				<h2 class="est-section-title">Featured Properties</h2>
				<p class="est-section-subtitle">Explore our handpicked selection of featured properties. Each listing offers a glimpse into exceptional homes and investments available through Estatein. Click &ldquo;View Details&rdquo; for more information.</p>
			</div>
			<div class="col-lg-4 text-lg-end">
				<a href="<?php echo esc_url( $estatein_properties_url ? $estatein_properties_url : '#' ); ?>" class="btn est-btn-outline">View All Properties</a>
			</div>
		</div>

		<?php get_template_part( 'template-parts/components/properties-carousel', null, array( 'properties' => $estatein_properties, 'id_prefix' => 'estProperties' ) ); ?>
	</div>
</section>
