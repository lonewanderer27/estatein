<?php
/**
 * Properties archive listing — "Discover a World of Possibilities": builds
 * its own query from the $_GET params submitted by
 * template-parts/archive-property/hero.php (search + location/type/price/
 * size/build-year filters), then renders the result with the same
 * properties-carousel component used by the homepage's Featured Properties.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$estatein_archive_url   = get_post_type_archive_link( 'property' );
$estatein_search        = isset( $_GET['s'] ) ? sanitize_text_field( wp_unslash( $_GET['s'] ) ) : '';
$estatein_location_id   = isset( $_GET['location_id'] ) ? absint( $_GET['location_id'] ) : 0;
$estatein_property_type = isset( $_GET['property_type'] ) ? sanitize_text_field( wp_unslash( $_GET['property_type'] ) ) : '';
$estatein_price_range   = isset( $_GET['price_range'] ) ? sanitize_text_field( wp_unslash( $_GET['price_range'] ) ) : '';
$estatein_size_range    = isset( $_GET['size_range'] ) ? sanitize_text_field( wp_unslash( $_GET['size_range'] ) ) : '';
$estatein_year_range    = isset( $_GET['year_range'] ) ? sanitize_text_field( wp_unslash( $_GET['year_range'] ) ) : '';

$estatein_has_filters = (bool) ( $estatein_search || $estatein_location_id || $estatein_property_type || $estatein_price_range || $estatein_size_range || $estatein_year_range );

$estatein_meta_query = array();

if ( $estatein_location_id ) {
	$estatein_meta_query[] = array(
		'key'     => '_location_id',
		'value'   => $estatein_location_id,
		'compare' => '=',
	);
}

if ( $estatein_property_type ) {
	$estatein_meta_query[] = array(
		'key'     => 'property_type',
		'value'   => $estatein_property_type,
		'compare' => '=',
	);
}

foreach ( array(
	'price_range' => array( $estatein_price_range, 'price' ),
	'size_range'  => array( $estatein_size_range, 'area_sqft' ),
	'year_range'  => array( $estatein_year_range, 'build_year' ),
) as $estatein_range_field ) {
	list( $estatein_range_value, $estatein_meta_key ) = $estatein_range_field;

	if ( ! $estatein_range_value ) {
		continue;
	}

	list( $estatein_min, $estatein_max ) = estatein_parse_filter_range( $estatein_range_value );

	$estatein_meta_query[] = null === $estatein_max
		? array( 'key' => $estatein_meta_key, 'value' => $estatein_min, 'compare' => '>=', 'type' => 'NUMERIC' )
		: array( 'key' => $estatein_meta_key, 'value' => array( $estatein_min, $estatein_max ), 'compare' => 'BETWEEN', 'type' => 'NUMERIC' );
}

if ( count( $estatein_meta_query ) > 1 ) {
	$estatein_meta_query['relation'] = 'AND';
}

$estatein_query_args = array(
	'post_type'      => 'property',
	'posts_per_page' => -1,
	'orderby'        => 'date',
	'order'          => 'DESC',
);

if ( $estatein_search ) {
	$estatein_query_args['s'] = $estatein_search;
}

if ( $estatein_meta_query ) {
	$estatein_query_args['meta_query'] = $estatein_meta_query;
}

$estatein_properties = get_posts( $estatein_query_args );
?>
<section class="est-section" id="estPropertiesListing">
	<div class="container">
		<div class="row align-items-end mb-4 gy-3">
			<div class="col-lg-8">
				<p class="est-eyebrow"><?php estatein_theme_icon( 'sparkle' ); ?><span class="est-eyebrow-dot"></span></p>
				<h2 class="est-section-title">Discover a World of Possibilities</h2>
				<p class="est-section-subtitle">Our portfolio of properties is as diverse as your dreams. Explore the following categories to find the perfect property that resonates with your vision of home.</p>
			</div>
			<?php if ( $estatein_has_filters ) : ?>
				<div class="col-lg-4 text-lg-end">
					<a href="<?php echo esc_url( $estatein_archive_url ? $estatein_archive_url : '#' ); ?>" class="btn est-btn-outline">Clear Filters</a>
				</div>
			<?php endif; ?>
		</div>

		<?php if ( empty( $estatein_properties ) ) : ?>
			<p class="est-section-subtitle">No properties match your search. Try adjusting or clearing your filters.</p>
		<?php else : ?>
			<?php get_template_part( 'template-parts/components/properties-carousel', null, array( 'properties' => $estatein_properties, 'id_prefix' => 'estListing' ) ); ?>
		<?php endif; ?>
	</div>
</section>
