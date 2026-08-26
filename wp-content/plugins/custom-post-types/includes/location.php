<?php
/**
 * Location CPT: a shared, hierarchical reference (Country > State > City)
 * that other post types (e.g. property, testimonial) attach to via
 * estatein_register_location_field() instead of storing free-text locations.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function register_location_post_type() {
	register_post_type( 'location', array(
		'label'               => 'Locations',
		'labels'              => array(
			'name'          => 'Locations',
			'singular_name' => 'Location',
			'add_new_item'  => 'Add New Location',
			'edit_item'     => 'Edit Location',
			'all_items'     => 'All Locations',
			'search_items'  => 'Search Locations',
		),
		'public'              => false,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'show_in_rest'        => true,
		'publicly_queryable'  => false,
		'has_archive'         => false,
		'hierarchical'        => true,
		'supports'            => array( 'title', 'editor', 'thumbnail', 'page-attributes' ),
		'menu_icon'           => 'dashicons-location',
	) );

	register_post_meta( 'location', 'region_type', array(
		'type'         => 'string',
		'single'       => true,
		'show_in_rest' => true,
		'default'      => 'city',
	) );
}
add_action( 'init', 'register_location_post_type' );

/**
 * Adds a "Location" meta box to $post_type, letting an editor pick one
 * `location` post. The choice is stored as `_location_id` post meta.
 *
 * Call once per consuming post type, e.g.:
 *   estatein_register_location_field( 'property' );
 *   estatein_register_location_field( 'testimonial' );
 */
function estatein_register_location_field( $post_type ) {
	add_action( 'add_meta_boxes_' . $post_type, function () use ( $post_type ) {
		add_meta_box(
			'estatein_location',
			'Location',
			'estatein_render_location_meta_box',
			$post_type,
			'side'
		);
	} );

	add_action( 'save_post_' . $post_type, 'estatein_save_location_meta_box' );
}

function estatein_render_location_meta_box( $post ) {
	wp_nonce_field( 'estatein_save_location', 'estatein_location_nonce' );

	wp_dropdown_pages( array(
		'post_type'         => 'location',
		'name'              => 'estatein_location_id',
		'id'                => 'estatein_location_id',
		'selected'          => (int) get_post_meta( $post->ID, '_location_id', true ),
		'show_option_none'  => '— None —',
		'option_none_value' => 0,
		'sort_column'       => 'menu_order, post_title',
	) );
}

function estatein_save_location_meta_box( $post_id ) {
	if ( ! isset( $_POST['estatein_location_nonce'] )
		|| ! wp_verify_nonce( $_POST['estatein_location_nonce'], 'estatein_save_location' ) ) {
		return;
	}

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	if ( isset( $_POST['estatein_location_id'] ) ) {
		update_post_meta( $post_id, '_location_id', (int) $_POST['estatein_location_id'] );
	}
}

/**
 * Reads the `_location_id` meta on $post_id and returns that `location`
 * post, or null if none is set.
 */
function estatein_get_location( $post_id ) {
	$location_id = (int) get_post_meta( $post_id, '_location_id', true );

	if ( ! $location_id ) {
		return null;
	}

	$location = get_post( $location_id );

	return ( $location && 'location' === $location->post_type ) ? $location : null;
}
