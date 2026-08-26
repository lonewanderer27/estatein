<?php
/**
 * Value CPT: title = value name, editor = description. Nothing else —
 * same admin-managed pattern as location/testimonial.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function register_value_post_type() {
	register_post_type( 'value', array(
		'label'               => 'Values',
		'labels'              => array(
			'name'          => 'Values',
			'singular_name' => 'Value',
			'add_new_item'  => 'Add New Value',
			'edit_item'     => 'Edit Value',
			'all_items'     => 'All Values',
			'search_items'  => 'Search Values',
		),
		'public'              => false,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'show_in_rest'        => true,
		'publicly_queryable'  => false,
		'has_archive'         => false,
		'hierarchical'        => false,
		'supports'            => array( 'title', 'editor' ),
		'menu_icon'           => 'dashicons-star-filled',
	) );
}
add_action( 'init', 'register_value_post_type' );
