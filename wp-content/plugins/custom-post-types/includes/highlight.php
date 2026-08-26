<?php
/**
 * Highlight CPT: title = short heading, thumbnail = icon, link = where the
 * card points to. For the homepage's icon-card row ("Find Your Dream
 * Home", "Unlock Property Value", etc.) — same admin-managed pattern as
 * location/testimonial/value.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function register_highlight_post_type() {
	register_post_type( 'highlight', array(
		'label'               => 'Highlights',
		'labels'              => array(
			'name'          => 'Highlights',
			'singular_name' => 'Highlight',
			'add_new_item'  => 'Add New Highlight',
			'edit_item'     => 'Edit Highlight',
			'all_items'     => 'All Highlights',
			'search_items'  => 'Search Highlights',
		),
		'public'              => false,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'show_in_rest'        => true,
		'publicly_queryable'  => false,
		'has_archive'         => false,
		'hierarchical'        => false,
		'supports'            => array( 'title', 'thumbnail', 'page-attributes' ),
		'menu_icon'           => 'dashicons-star-filled',
	) );

	register_post_meta( 'highlight', 'link', array(
		'type'              => 'string',
		'single'            => true,
		'show_in_rest'      => true,
		'sanitize_callback' => 'sanitize_url',
	) );
}
add_action( 'init', 'register_highlight_post_type' );

function estatein_register_highlight_link_field() {
	add_action( 'add_meta_boxes_highlight', function () {
		add_meta_box(
			'estatein_highlight_link',
			'Link',
			'estatein_render_highlight_link_meta_box',
			'highlight',
			'side'
		);
	} );

	add_action( 'save_post_highlight', 'estatein_save_highlight_link_meta_box' );
}
add_action( 'init', 'estatein_register_highlight_link_field' );

function estatein_render_highlight_link_meta_box( $post ) {
	wp_nonce_field( 'estatein_save_highlight_link', 'estatein_highlight_link_nonce' );
	?>
	<input
		type="url"
		class="widefat"
		name="estatein_highlight_link"
		placeholder="https://"
		value="<?php echo esc_attr( get_post_meta( $post->ID, 'link', true ) ); ?>"
	/>
	<?php
}

function estatein_save_highlight_link_meta_box( $post_id ) {
	if ( ! isset( $_POST['estatein_highlight_link_nonce'] )
		|| ! wp_verify_nonce( $_POST['estatein_highlight_link_nonce'], 'estatein_save_highlight_link' ) ) {
		return;
	}

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	if ( isset( $_POST['estatein_highlight_link'] ) ) {
		update_post_meta( $post_id, 'link', sanitize_url( $_POST['estatein_highlight_link'] ) );
	}
}
