<?php
/**
 * Properties archive hero: title/subtitle, a search box, and a row of
 * range/select filters. Submits as a GET form back to the archive URL so
 * template-parts/archive-property/listing.php can read $_GET and filter
 * accordingly. Every field re-selects its current $_GET value so the form
 * round-trips after submit.
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

$estatein_property_types = estatein_get_property_type_options();
?>
<section class="est-section est-property-search" id="estPropertySearch">
	<div class="container">
		<h1 class="est-hero-title">Find Your Dream Property</h1>
		<p class="est-hero-subtitle">Welcome to Estatein, where your dream property awaits in every corner of our beautiful world. Explore our curated selection of properties, each offering a unique story and a chance to redefine your life. With categories to suit every dreamer, your journey starts here.</p>

		<form class="est-property-search-form" method="get" action="<?php echo esc_url( $estatein_archive_url ? $estatein_archive_url : home_url( '/' ) ); ?>">
			<div class="input-group est-property-search-bar mb-3">
				<span class="input-group-text est-property-search-icon"><?php estatein_theme_icon( 'search' ); ?></span>
				<label for="estPropertySearchInput" class="visually-hidden">Search For A Property</label>
				<input type="search" class="form-control" id="estPropertySearchInput" name="s" value="<?php echo esc_attr( $estatein_search ); ?>" placeholder="Search For A Property" />
				<button class="btn btn-primary est-btn-primary" type="submit"><?php estatein_theme_icon( 'search' ); ?> Find Property</button>
			</div>

			<div class="row g-2 est-property-search-filters">
				<div class="col-6 col-lg">
					<div class="est-filter-select">
						<span class="est-filter-icon"><?php estatein_theme_icon( 'pin' ); ?></span>
						<?php
						wp_dropdown_pages( array(
							'post_type'         => 'location',
							'name'              => 'location_id',
							'id'                => 'estFilterLocation',
							'class'             => 'form-select',
							'selected'          => $estatein_location_id,
							'show_option_none'  => 'Location',
							'option_none_value' => 0,
							'sort_column'       => 'menu_order, post_title',
						) );
						?>
					</div>
				</div>

				<div class="col-6 col-lg">
					<div class="est-filter-select">
						<span class="est-filter-icon"><?php estatein_theme_icon( 'building' ); ?></span>
						<select class="form-select" name="property_type" id="estFilterPropertyType">
							<option value="">Property Type</option>
							<?php foreach ( $estatein_property_types as $estatein_type ) : ?>
								<option value="<?php echo esc_attr( $estatein_type ); ?>" <?php selected( $estatein_property_type, $estatein_type ); ?>><?php echo esc_html( $estatein_type ); ?></option>
							<?php endforeach; ?>
						</select>
					</div>
				</div>

				<div class="col-6 col-lg">
					<div class="est-filter-select">
						<span class="est-filter-icon"><?php estatein_theme_icon( 'dollar' ); ?></span>
						<select class="form-select" name="price_range" id="estFilterPriceRange">
							<option value="">Pricing Range</option>
							<?php foreach ( estatein_property_filter_ranges( 'price' ) as $estatein_value => $estatein_label ) : ?>
								<option value="<?php echo esc_attr( $estatein_value ); ?>" <?php selected( $estatein_price_range, $estatein_value ); ?>><?php echo esc_html( $estatein_label ); ?></option>
							<?php endforeach; ?>
						</select>
					</div>
				</div>

				<div class="col-6 col-lg">
					<div class="est-filter-select">
						<span class="est-filter-icon"><?php estatein_theme_icon( 'ruler' ); ?></span>
						<select class="form-select" name="size_range" id="estFilterSizeRange">
							<option value="">Property Size</option>
							<?php foreach ( estatein_property_filter_ranges( 'area_sqft' ) as $estatein_value => $estatein_label ) : ?>
								<option value="<?php echo esc_attr( $estatein_value ); ?>" <?php selected( $estatein_size_range, $estatein_value ); ?>><?php echo esc_html( $estatein_label ); ?></option>
							<?php endforeach; ?>
						</select>
					</div>
				</div>

				<div class="col-6 col-lg">
					<div class="est-filter-select">
						<span class="est-filter-icon"><?php estatein_theme_icon( 'calendar' ); ?></span>
						<select class="form-select" name="year_range" id="estFilterYearRange">
							<option value="">Build Year</option>
							<?php foreach ( estatein_property_filter_ranges( 'build_year' ) as $estatein_value => $estatein_label ) : ?>
								<option value="<?php echo esc_attr( $estatein_value ); ?>" <?php selected( $estatein_year_range, $estatein_value ); ?>><?php echo esc_html( $estatein_label ); ?></option>
							<?php endforeach; ?>
						</select>
					</div>
				</div>
			</div>
		</form>
	</div>
</section>
