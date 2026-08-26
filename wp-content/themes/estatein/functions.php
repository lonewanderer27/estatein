<?php
/**
 * Estatein theme setup: enqueues, nav menus, thumbnail sizes, and the small
 * presentational fields the theme owns (property_type) on top of the
 * custom-post-types plugin's data model.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'ESTATEIN_VERSION', '0.1.0' );

function estatein_setup() {
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'custom-logo', array(
		'height'      => 40,
		'width'       => 140,
		'flex-height' => true,
		'flex-width'  => true,
	) );
	add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'script', 'style' ) );

	register_nav_menus( array(
		'primary' => __( 'Primary Menu', 'estatein' ),
	) );

	add_image_size( 'estatein-property-card', 640, 480, true );
	add_image_size( 'estatein-avatar', 96, 96, true );
}
add_action( 'after_setup_theme', 'estatein_setup' );

/**
 * Component stylesheets, one per template-parts/components or
 * template-parts/front-page file, loaded after base.css (tokens, body
 * defaults, and the shared section-header/button chrome).
 */
function estatein_component_stylesheets() {
	return array(
		'announcement-bar',
		'header',
		'hero',
		'property-search',
		'highlight-card',
		'property-card',
		'testimonial-card',
		'faq-card',
		'carousel',
		'cta',
		'footer',
		'property-gallery',
		'description-features',
		'inquire-form',
		'pricing',
		'pricing-card',
	);
}

function estatein_enqueue_assets() {
	wp_enqueue_style( 'bootstrap', get_template_directory_uri() . '/assets/vendor/bootstrap/css/bootstrap.min.css', array(), '5.3.8' );
	wp_enqueue_style( 'estatein-base', get_template_directory_uri() . '/assets/css/base.css', array( 'bootstrap' ), ESTATEIN_VERSION );

	$component_deps = array( 'estatein-base' );

	foreach ( estatein_component_stylesheets() as $component ) {
		$handle = 'estatein-' . $component;
		wp_enqueue_style( $handle, get_template_directory_uri() . '/assets/css/components/' . $component . '.css', array( 'estatein-base' ), ESTATEIN_VERSION );
		$component_deps[] = $handle;
	}

	wp_enqueue_style( 'estatein-style', get_stylesheet_uri(), $component_deps, ESTATEIN_VERSION );

	wp_enqueue_script( 'bootstrap-bundle', get_template_directory_uri() . '/assets/vendor/bootstrap/js/bootstrap.bundle.min.js', array(), '5.3.8', true );
	wp_enqueue_script( 'estatein-main', get_template_directory_uri() . '/assets/js/main.js', array( 'bootstrap-bundle' ), ESTATEIN_VERSION, true );
}
add_action( 'wp_enqueue_scripts', 'estatein_enqueue_assets' );

/**
 * "Property Type" side field (e.g. Villa, Apartment, Townhouse) — a
 * presentational field the theme owns for the card badge; the plugin's
 * `property` CPT doesn't define a type taxonomy or field.
 */
function estatein_register_property_type_field() {
	register_post_meta( 'property', 'property_type', array(
		'type'         => 'string',
		'single'       => true,
		'show_in_rest' => true,
	) );

	add_action( 'add_meta_boxes_property', function () {
		add_meta_box(
			'estatein_property_type',
			'Property Type',
			'estatein_render_property_type_meta_box',
			'property',
			'side'
		);
	} );

	add_action( 'save_post_property', 'estatein_save_property_type_meta_box' );
}
add_action( 'init', 'estatein_register_property_type_field' );

function estatein_render_property_type_meta_box( $post ) {
	wp_nonce_field( 'estatein_save_property_type', 'estatein_property_type_nonce' );
	?>
	<input
		type="text"
		class="widefat"
		name="estatein_property_type"
		placeholder="e.g. Villa, Apartment, Townhouse"
		value="<?php echo esc_attr( get_post_meta( $post->ID, 'property_type', true ) ); ?>"
	/>
	<?php
}

function estatein_save_property_type_meta_box( $post_id ) {
	if ( ! isset( $_POST['estatein_property_type_nonce'] )
		|| ! wp_verify_nonce( $_POST['estatein_property_type_nonce'], 'estatein_save_property_type' ) ) {
		return;
	}

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	if ( isset( $_POST['estatein_property_type'] ) ) {
		update_post_meta( $post_id, 'property_type', sanitize_text_field( $_POST['estatein_property_type'] ) );
	}
}

/**
 * Distinct `property_type` values across published properties, for the
 * archive filter bar and the property-request form's Property Type select.
 */
function estatein_get_property_type_options() {
	global $wpdb;

	$types = $wpdb->get_col(
		"SELECT DISTINCT pm.meta_value FROM {$wpdb->postmeta} pm
		INNER JOIN {$wpdb->posts} p ON p.ID = pm.post_id
		WHERE pm.meta_key = 'property_type'
		AND pm.meta_value != ''
		AND p.post_type = 'property'
		AND p.post_status = 'publish'
		ORDER BY pm.meta_value ASC"
	);

	return array_values( array_filter( $types ) );
}

/**
 * Bucketed range options for the archive filter bar (Pricing Range,
 * Property Size, Build Year) and the property-request form's Budget
 * select, so both draw from the same value/label pairs. Each value is
 * "min-max"; an empty max means open-ended (e.g. "1000000-" = $1M+).
 * estatein_parse_filter_range() below turns a value back into numbers.
 */
function estatein_property_filter_ranges( $field ) {
	$ranges = array(
		'price'      => array(
			'0-250000'      => 'Under $250,000',
			'250000-500000' => '$250,000 – $500,000',
			'500000-1000000' => '$500,000 – $1,000,000',
			'1000000-'      => '$1,000,000+',
		),
		'area_sqft'  => array(
			'0-1000'    => 'Under 1,000 sqft',
			'1000-2000' => '1,000 – 2,000 sqft',
			'2000-3500' => '2,000 – 3,500 sqft',
			'3500-'     => '3,500+ sqft',
		),
		'build_year' => array(
			'0-2000'    => 'Before 2000',
			'2000-2010' => '2000 – 2010',
			'2010-2020' => '2010 – 2020',
			'2020-'     => '2020 & Newer',
		),
	);

	return isset( $ranges[ $field ] ) ? $ranges[ $field ] : array();
}

/**
 * Splits a "min-max" range value (from estatein_property_filter_ranges())
 * into array( $min, $max ) floats, with $max null when open-ended.
 */
function estatein_parse_filter_range( $value ) {
	$parts = explode( '-', (string) $value, 2 );
	$min   = isset( $parts[0] ) && '' !== $parts[0] ? (float) $parts[0] : 0;
	$max   = isset( $parts[1] ) && '' !== $parts[1] ? (float) $parts[1] : null;

	return array( $min, $max );
}

/**
 * Builds a "Country, State" style label from the hierarchical `location`
 * CPT attached via the plugin's estatein_get_location(): parent title first,
 * then the location's own title (e.g. location "California" with parent
 * "USA" becomes "USA, California").
 */
function estatein_theme_location_label( $post_id ) {
	if ( ! function_exists( 'estatein_get_location' ) ) {
		return '';
	}

	$location = estatein_get_location( $post_id );

	if ( ! $location ) {
		return '';
	}

	if ( $location->post_parent ) {
		$parent = get_post( $location->post_parent );

		if ( $parent ) {
			return $parent->post_title . ', ' . $location->post_title;
		}
	}

	return $location->post_title;
}

/**
 * Property gallery images, sourced from the Gallery block embedded in the
 * property's content (the property CPT has no dedicated gallery meta field
 * — see custom-post-types/includes/property.php), falling back to the
 * featured image when no gallery block is present.
 */
function estatein_get_property_gallery_images( $post_id ) {
	$post = get_post( $post_id );

	if ( ! $post ) {
		return array();
	}

	$images = array();

	foreach ( parse_blocks( $post->post_content ) as $block ) {
		if ( 'core/gallery' !== $block['blockName'] || empty( $block['innerBlocks'] ) ) {
			continue;
		}

		foreach ( $block['innerBlocks'] as $inner_block ) {
			if ( 'core/image' !== $inner_block['blockName'] ) {
				continue;
			}

			$attachment_id = isset( $inner_block['attrs']['id'] ) ? (int) $inner_block['attrs']['id'] : 0;
			$url           = '';

			if ( $attachment_id ) {
				$url = wp_get_attachment_image_url( $attachment_id, 'large' );
			} elseif ( isset( $inner_block['attrs']['url'] ) ) {
				$url = $inner_block['attrs']['url'];
			}

			if ( $url ) {
				$images[] = $url;
			}
		}

		break;
	}

	if ( empty( $images ) && has_post_thumbnail( $post_id ) ) {
		$images[] = get_the_post_thumbnail_url( $post_id, 'large' );
	}

	return $images;
}

/**
 * Renders a 1-5 star rating as inline SVG stars.
 */
function estatein_theme_render_stars( $rating ) {
	$rating = max( 1, min( 5, (int) $rating ) );
	$star   = '<svg class="est-star" width="16" height="16" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 2.5l3.09 6.26 6.91 1-5 4.87 1.18 6.87L12 17.9l-6.18 3.6L7 14.63l-5-4.87 6.91-1L12 2.5z"/></svg>';

	echo '<span class="est-stars" role="img" aria-label="' . esc_attr( $rating . ' out of 5 stars' ) . '">';
	echo str_repeat( $star, $rating );
	echo '</span>';
}

/**
 * wp_nav_menu()'s default walker doesn't add Bootstrap's `nav-item` /
 * `nav-link` classes on its own — add them so the "primary" menu picks up
 * the navbar styling in assets/css/components/header.css.
 */
function estatein_nav_menu_css_class( $classes ) {
	$classes[] = 'nav-item';

	return $classes;
}
add_filter( 'nav_menu_css_class', 'estatein_nav_menu_css_class' );

function estatein_nav_menu_link_attributes( $atts ) {
	$atts['class'] = isset( $atts['class'] ) ? $atts['class'] . ' nav-link' : 'nav-link';

	return $atts;
}
add_filter( 'nav_menu_link_attributes', 'estatein_nav_menu_link_attributes' );

/**
 * Fallback markup for the "primary" nav menu when no menu has been
 * assigned yet in Appearance > Menus, so the header still renders.
 */
function estatein_default_primary_menu() {
	$items = array(
		'Home'       => home_url( '/' ),
		'About Us'   => '#',
		'Properties' => get_post_type_archive_link( 'property' ) ?: '#',
		'Services'   => '#',
	);
	?>
	<ul id="estNavbar" class="navbar-nav mx-lg-auto">
		<?php foreach ( $items as $label => $url ) : ?>
			<li class="nav-item">
				<a class="nav-link" href="<?php echo esc_url( $url ); ?>"><?php echo esc_html( $label ); ?></a>
			</li>
		<?php endforeach; ?>
	</ul>
	<?php
}

/**
 * Small inline SVG icon set used across the homepage components, avoiding a
 * separate icon font.
 */
function estatein_theme_icon( $name ) {
	$icons = array(
		'arrow-up-right' => '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M7 17L17 7M8 7h9v9"/></svg>',
		'arrow-left'     => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>',
		'arrow-right'    => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M12 5l7 7-7 7"/></svg>',
		'bed'            => '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 18v-6a2 2 0 012-2h14a2 2 0 012 2v6M3 18h18M3 18v2M21 18v2M3 12V9a2 2 0 012-2h4a2 2 0 012 2v3"/></svg>',
		'bath'           => '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 12h16v3a4 4 0 01-4 4H8a4 4 0 01-4-4v-3zM7 12V6a2 2 0 012-2 2 2 0 012 2M4 19v2M18 19v2"/></svg>',
		'building'       => '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 21V5a1 1 0 011-1h6a1 1 0 011 1v16M16 21V10a1 1 0 011-1h2a1 1 0 011 1v11M4 21h16M8 8h0M8 12h0M8 16h0"/></svg>',
		'home'           => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 11l9-7 9 7M5 10v10h14V10"/></svg>',
		'trend'          => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 17l5-5 4 4 8-8M14 8h6v6"/></svg>',
		'gear'           => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.7 1.7 0 00.34 1.87l.06.06a2 2 0 11-2.83 2.83l-.06-.06a1.7 1.7 0 00-1.87-.34 1.7 1.7 0 00-1 1.55V21a2 2 0 11-4 0v-.09a1.7 1.7 0 00-1-1.55 1.7 1.7 0 00-1.87.34l-.06.06a2 2 0 11-2.83-2.83l.06-.06A1.7 1.7 0 004.6 15a1.7 1.7 0 00-1.55-1H3a2 2 0 110-4h.09A1.7 1.7 0 004.6 9a1.7 1.7 0 00-.34-1.87l-.06-.06a2 2 0 112.83-2.83l.06.06A1.7 1.7 0 009 4.6a1.7 1.7 0 001-1.55V3a2 2 0 114 0v.09a1.7 1.7 0 001 1.55 1.7 1.7 0 001.87-.34l.06-.06a2 2 0 112.83 2.83l-.06.06A1.7 1.7 0 0019.4 9a1.7 1.7 0 001.55 1H21a2 2 0 110 4h-.09a1.7 1.7 0 00-1.55 1z"/></svg>',
		'sparkle'        => '<svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 2l1.8 6.2L20 10l-6.2 1.8L12 18l-1.8-6.2L4 10l6.2-1.8L12 2z"/></svg>',
		'lightning'      => '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M13 2L3 14h7l-1 8 10-12h-7l1-8z"/></svg>',
		'close'          => '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6L6 18M6 6l12 12"/></svg>',
		'send'           => '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 2L11 13M22 2l-7 20-4-9-9-4 20-7z"/></svg>',
		'mail'           => '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16v16H4V4zM4 6l8 7 8-7"/></svg>',
		'pin'            => '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 6-9 12-9 12s-9-6-9-12a9 9 0 1118 0z"/><circle cx="12" cy="10" r="3"/></svg>',
		'search'         => '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="7"/><path d="M21 21l-4.35-4.35"/></svg>',
		'dollar'         => '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 1v22M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/></svg>',
		'ruler'          => '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 16l5-5 3 3 4-4 3 3 3-3M3 16v5h18v-5"/></svg>',
		'calendar'       => '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>',
		'facebook'       => '<svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M22 12a10 10 0 10-11.6 9.9v-7H7.9V12h2.5V9.8c0-2.5 1.5-3.9 3.7-3.9 1.1 0 2.2.2 2.2.2v2.5h-1.3c-1.2 0-1.6.8-1.6 1.6V12h2.8l-.4 2.9h-2.4v7A10 10 0 0022 12z"/></svg>',
		'linkedin'       => '<svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M20.4 3H3.6A.6.6 0 003 3.6v16.8a.6.6 0 00.6.6h16.8a.6.6 0 00.6-.6V3.6a.6.6 0 00-.6-.6zM8.3 18.3H5.7V9.9h2.6v8.4zM7 8.8a1.5 1.5 0 110-3 1.5 1.5 0 010 3zm11.3 9.5h-2.6v-4.1c0-1-.4-1.7-1.3-1.7-.7 0-1.1.5-1.3 1-.1.2-.1.5-.1.7v4.1h-2.6s0-6.8 0-7.5h2.6v1.1c.3-.5 1-1.3 2.3-1.3 1.7 0 3 1.1 3 3.4v4.3z"/></svg>',
		'instagram'      => '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r="1" fill="currentColor" stroke="none"/></svg>',
	);

	if ( isset( $icons[ $name ] ) ) {
		echo $icons[ $name ]; // phpcs:ignore -- static inline SVG map, no user input.
	}
}
