<?php
/**
 * Inquiry CPT: stores "Inquire About [Property]" form submissions from the
 * single-property page for wp-admin review, and emails a copy to the
 * site's contact address via estatein_get_contact_info(). Not public and
 * has no archive — this is a private lead record, not a page.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function register_inquiry_post_type() {
	register_post_type( 'inquiry', array(
		'label'               => 'Inquiries',
		'labels'              => array(
			'name'          => 'Inquiries',
			'singular_name' => 'Inquiry',
			'all_items'     => 'All Inquiries',
			'search_items'  => 'Search Inquiries',
		),
		'public'              => false,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'show_in_rest'        => false,
		'publicly_queryable'  => false,
		'has_archive'         => false,
		'hierarchical'        => false,
		'supports'            => array( 'title' ),
		'menu_icon'           => 'dashicons-email-alt',
	) );

	foreach ( array( 'first_name', 'last_name', 'email', 'phone', 'message' ) as $key ) {
		register_post_meta( 'inquiry', $key, array(
			'type'         => 'string',
			'single'       => true,
			'show_in_rest' => false,
		) );
	}

	register_post_meta( 'inquiry', 'property_id', array(
		'type'         => 'integer',
		'single'       => true,
		'show_in_rest' => false,
	) );
}
add_action( 'init', 'register_inquiry_post_type' );

/**
 * Read-only "Inquiry Details" meta box. Inquiries are only ever created by
 * estatein_handle_inquiry_submission() below, never edited in wp-admin.
 */
function estatein_register_inquiry_details_field() {
	add_action( 'add_meta_boxes_inquiry', function () {
		add_meta_box(
			'estatein_inquiry_details',
			'Inquiry Details',
			'estatein_render_inquiry_details_meta_box',
			'inquiry',
			'normal'
		);
	} );
}
add_action( 'init', 'estatein_register_inquiry_details_field' );

function estatein_render_inquiry_details_meta_box( $post ) {
	$property_id = (int) get_post_meta( $post->ID, 'property_id', true );
	$property    = $property_id ? get_post( $property_id ) : null;

	$fields = array(
		'Name'     => trim( get_post_meta( $post->ID, 'first_name', true ) . ' ' . get_post_meta( $post->ID, 'last_name', true ) ),
		'Email'    => get_post_meta( $post->ID, 'email', true ),
		'Phone'    => get_post_meta( $post->ID, 'phone', true ),
		'Property' => $property ? $property->post_title : '',
		'Message'  => get_post_meta( $post->ID, 'message', true ),
	);

	foreach ( $fields as $label => $value ) {
		?>
		<p>
			<strong><?php echo esc_html( $label ); ?>:</strong>
			<?php echo esc_html( $value ? $value : '—' ); ?>
		</p>
		<?php
	}
}

/**
 * Handles the "Inquire About [Property]" form POST from
 * template-parts/single-property/inquire.php: stores the submission as an
 * `inquiry` post and emails a copy to the site's contact address.
 */
function estatein_handle_inquiry_submission() {
	if ( ! isset( $_POST['estatein_inquiry_nonce'] )
		|| ! wp_verify_nonce( $_POST['estatein_inquiry_nonce'], 'estatein_submit_inquiry' ) ) {
		wp_die( 'Security check failed.' );
	}

	$redirect = wp_get_referer() ? wp_get_referer() : home_url( '/' );

	$first_name  = isset( $_POST['first_name'] ) ? sanitize_text_field( $_POST['first_name'] ) : '';
	$last_name   = isset( $_POST['last_name'] ) ? sanitize_text_field( $_POST['last_name'] ) : '';
	$email       = isset( $_POST['email'] ) ? sanitize_email( $_POST['email'] ) : '';
	$phone       = isset( $_POST['phone'] ) ? sanitize_text_field( $_POST['phone'] ) : '';
	$message     = isset( $_POST['message'] ) ? sanitize_textarea_field( $_POST['message'] ) : '';
	$property_id = isset( $_POST['property_id'] ) ? absint( $_POST['property_id'] ) : 0;

	if ( ! $first_name || ! $last_name || ! is_email( $email ) ) {
		wp_safe_redirect( add_query_arg( 'estatein_inquiry', 'error', $redirect ) );
		exit;
	}

	$property = $property_id ? get_post( $property_id ) : null;
	$title    = $property
		? sprintf( '%s %s — %s', $first_name, $last_name, $property->post_title )
		: sprintf( '%s %s', $first_name, $last_name );

	$inquiry_id = wp_insert_post( array(
		'post_type'   => 'inquiry',
		'post_title'  => $title,
		'post_status' => 'publish',
	) );

	if ( $inquiry_id && ! is_wp_error( $inquiry_id ) ) {
		update_post_meta( $inquiry_id, 'first_name', $first_name );
		update_post_meta( $inquiry_id, 'last_name', $last_name );
		update_post_meta( $inquiry_id, 'email', $email );
		update_post_meta( $inquiry_id, 'phone', $phone );
		update_post_meta( $inquiry_id, 'message', $message );
		update_post_meta( $inquiry_id, 'property_id', $property_id );

		$contact = function_exists( 'estatein_get_contact_info' ) ? estatein_get_contact_info() : array();
		$to      = ! empty( $contact['email'] ) ? $contact['email'] : get_option( 'admin_email' );

		$body = sprintf(
			"New property inquiry from %s %s\n\nEmail: %s\nPhone: %s\nProperty: %s\n\nMessage:\n%s",
			$first_name,
			$last_name,
			$email,
			$phone ? $phone : '—',
			$property ? $property->post_title : '—',
			$message ? $message : '—'
		);

		wp_mail( $to, sprintf( 'New Inquiry: %s %s', $first_name, $last_name ), $body );
	}

	wp_safe_redirect( add_query_arg( 'estatein_inquiry', 'success', $redirect ) );
	exit;
}
add_action( 'admin_post_estatein_submit_inquiry', 'estatein_handle_inquiry_submission' );
add_action( 'admin_post_nopriv_estatein_submit_inquiry', 'estatein_handle_inquiry_submission' );
