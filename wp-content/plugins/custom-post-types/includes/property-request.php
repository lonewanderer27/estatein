<?php
/**
 * Property Request CPT: stores "Let's Make it Happen" submissions from the
 * properties archive page for wp-admin review, and emails a copy to the
 * site's contact address via estatein_get_contact_info(). Unlike `inquiry`,
 * this isn't tied to one property — it's a buyer's general preferences
 * (preferred location, type, budget, etc). Not public and has no archive —
 * this is a private lead record, not a page.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function register_property_request_post_type() {
	register_post_type( 'property_request', array(
		'label'               => 'Property Requests',
		'labels'              => array(
			'name'          => 'Property Requests',
			'singular_name' => 'Property Request',
			'all_items'     => 'All Property Requests',
			'search_items'  => 'Search Property Requests',
		),
		'public'              => false,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'show_in_rest'        => false,
		'publicly_queryable'  => false,
		'has_archive'         => false,
		'hierarchical'        => false,
		'supports'            => array( 'title' ),
		'menu_icon'           => 'dashicons-email-alt2',
	) );

	foreach ( array( 'first_name', 'last_name', 'email', 'phone', 'property_type', 'bedrooms', 'bathrooms', 'budget', 'contact_method', 'message' ) as $key ) {
		register_post_meta( 'property_request', $key, array(
			'type'         => 'string',
			'single'       => true,
			'show_in_rest' => false,
		) );
	}

	register_post_meta( 'property_request', 'preferred_location_id', array(
		'type'         => 'integer',
		'single'       => true,
		'show_in_rest' => false,
	) );
}
add_action( 'init', 'register_property_request_post_type' );

/**
 * Read-only "Property Request Details" meta box. Requests are only ever
 * created by estatein_handle_property_request_submission() below, never
 * edited in wp-admin.
 */
function estatein_register_property_request_details_field() {
	add_action( 'add_meta_boxes_property_request', function () {
		add_meta_box(
			'estatein_property_request_details',
			'Property Request Details',
			'estatein_render_property_request_details_meta_box',
			'property_request',
			'normal'
		);
	} );
}
add_action( 'init', 'estatein_register_property_request_details_field' );

function estatein_render_property_request_details_meta_box( $post ) {
	$location_id = (int) get_post_meta( $post->ID, 'preferred_location_id', true );
	$location    = $location_id ? get_post( $location_id ) : null;
	$contact_method = get_post_meta( $post->ID, 'contact_method', true );

	$fields = array(
		'Name'                => trim( get_post_meta( $post->ID, 'first_name', true ) . ' ' . get_post_meta( $post->ID, 'last_name', true ) ),
		'Email'                => get_post_meta( $post->ID, 'email', true ),
		'Phone'                => get_post_meta( $post->ID, 'phone', true ),
		'Preferred Location'   => $location ? $location->post_title : '',
		'Property Type'        => get_post_meta( $post->ID, 'property_type', true ),
		'Bedrooms'             => get_post_meta( $post->ID, 'bedrooms', true ),
		'Bathrooms'            => get_post_meta( $post->ID, 'bathrooms', true ),
		'Budget'               => get_post_meta( $post->ID, 'budget', true ),
		'Preferred Contact'    => $contact_method ? ucfirst( $contact_method ) : '',
		'Message'              => get_post_meta( $post->ID, 'message', true ),
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
 * Handles the "Let's Make it Happen" form POST from
 * template-parts/archive-property/request.php: stores the submission as a
 * `property_request` post and emails a copy to the site's contact address.
 */
function estatein_handle_property_request_submission() {
	if ( ! isset( $_POST['estatein_property_request_nonce'] )
		|| ! wp_verify_nonce( $_POST['estatein_property_request_nonce'], 'estatein_submit_property_request' ) ) {
		wp_die( 'Security check failed.' );
	}

	$redirect = wp_get_referer() ? wp_get_referer() : home_url( '/' );

	$first_name       = isset( $_POST['first_name'] ) ? sanitize_text_field( $_POST['first_name'] ) : '';
	$last_name        = isset( $_POST['last_name'] ) ? sanitize_text_field( $_POST['last_name'] ) : '';
	$email            = isset( $_POST['email'] ) ? sanitize_email( $_POST['email'] ) : '';
	$phone            = isset( $_POST['phone'] ) ? sanitize_text_field( $_POST['phone'] ) : '';
	$preferred_location_id = isset( $_POST['preferred_location_id'] ) ? absint( $_POST['preferred_location_id'] ) : 0;
	$property_type    = isset( $_POST['property_type'] ) ? sanitize_text_field( $_POST['property_type'] ) : '';
	$bedrooms         = isset( $_POST['bedrooms'] ) ? sanitize_text_field( $_POST['bedrooms'] ) : '';
	$bathrooms        = isset( $_POST['bathrooms'] ) ? sanitize_text_field( $_POST['bathrooms'] ) : '';
	$budget           = isset( $_POST['budget'] ) ? sanitize_text_field( $_POST['budget'] ) : '';
	$contact_method   = isset( $_POST['contact_method'] ) && in_array( $_POST['contact_method'], array( 'phone', 'email' ), true )
		? $_POST['contact_method']
		: '';
	$message          = isset( $_POST['message'] ) ? sanitize_textarea_field( $_POST['message'] ) : '';

	if ( ! $first_name || ! $last_name || ! is_email( $email ) ) {
		wp_safe_redirect( add_query_arg( 'estatein_property_request', 'error', $redirect ) );
		exit;
	}

	$request_id = wp_insert_post( array(
		'post_type'   => 'property_request',
		'post_title'  => sprintf( '%s %s', $first_name, $last_name ),
		'post_status' => 'publish',
	) );

	if ( $request_id && ! is_wp_error( $request_id ) ) {
		update_post_meta( $request_id, 'first_name', $first_name );
		update_post_meta( $request_id, 'last_name', $last_name );
		update_post_meta( $request_id, 'email', $email );
		update_post_meta( $request_id, 'phone', $phone );
		update_post_meta( $request_id, 'preferred_location_id', $preferred_location_id );
		update_post_meta( $request_id, 'property_type', $property_type );
		update_post_meta( $request_id, 'bedrooms', $bedrooms );
		update_post_meta( $request_id, 'bathrooms', $bathrooms );
		update_post_meta( $request_id, 'budget', $budget );
		update_post_meta( $request_id, 'contact_method', $contact_method );
		update_post_meta( $request_id, 'message', $message );

		$location = $preferred_location_id ? get_post( $preferred_location_id ) : null;
		$contact  = function_exists( 'estatein_get_contact_info' ) ? estatein_get_contact_info() : array();
		$to       = ! empty( $contact['email'] ) ? $contact['email'] : get_option( 'admin_email' );

		$body = sprintf(
			"New property request from %s %s\n\nEmail: %s\nPhone: %s\nPreferred Location: %s\nProperty Type: %s\nBedrooms: %s\nBathrooms: %s\nBudget: %s\nPreferred Contact Method: %s\n\nMessage:\n%s",
			$first_name,
			$last_name,
			$email,
			$phone ? $phone : '—',
			$location ? $location->post_title : '—',
			$property_type ? $property_type : '—',
			$bedrooms ? $bedrooms : '—',
			$bathrooms ? $bathrooms : '—',
			$budget ? $budget : '—',
			$contact_method ? ucfirst( $contact_method ) : '—',
			$message ? $message : '—'
		);

		wp_mail( $to, sprintf( 'New Property Request: %s %s', $first_name, $last_name ), $body );
	}

	wp_safe_redirect( add_query_arg( 'estatein_property_request', 'success', $redirect ) );
	exit;
}
add_action( 'admin_post_estatein_submit_property_request', 'estatein_handle_property_request_submission' );
add_action( 'admin_post_nopriv_estatein_submit_property_request', 'estatein_handle_property_request_submission' );
