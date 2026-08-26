<?php
/**
 * Testimonial CPT: title = reviewer name, editor = quote, thumbnail =
 * reviewer avatar. Rating, Location, and Client Details are added via
 * their own meta boxes below (Location reuses the shared field from
 * includes/location.php).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function register_testimonial_post_type() {
	register_post_type( 'testimonial', array(
		'label'               => 'Testimonials',
		'labels'              => array(
			'name'          => 'Testimonials',
			'singular_name' => 'Testimonial',
			'add_new_item'  => 'Add New Testimonial',
			'edit_item'     => 'Edit Testimonial',
			'all_items'     => 'All Testimonials',
			'search_items'  => 'Search Testimonials',
		),
		'public'              => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'show_in_rest'        => true,
		'publicly_queryable'  => true,
		'has_archive'         => false,
		'hierarchical'        => false,
		'supports'            => array( 'title', 'editor', 'thumbnail', 'slug' ),
		'menu_icon'           => 'dashicons-testimonial',
	) );

	register_post_meta( 'testimonial', 'rating', array(
		'type'              => 'integer',
		'single'            => true,
		'show_in_rest'      => true,
		'default'           => 5,
		'sanitize_callback' => 'estatein_sanitize_rating',
	) );

	register_post_meta( 'testimonial', 'company', array(
		'type'         => 'string',
		'single'       => true,
		'show_in_rest' => true,
	) );

	register_post_meta( 'testimonial', 'domain', array(
		'type'         => 'string',
		'single'       => true,
		'show_in_rest' => true,
	) );

	register_post_meta( 'testimonial', 'category', array(
		'type'         => 'string',
		'single'       => true,
		'show_in_rest' => true,
	) );

	register_post_meta( 'testimonial', 'website', array(
		'type'              => 'string',
		'single'            => true,
		'show_in_rest'      => true,
		'sanitize_callback' => 'sanitize_url',
	) );

	register_post_meta( 'testimonial', 'client_since', array(
		'type'         => 'string',
		'single'       => true,
		'show_in_rest' => true,
	) );

	register_post_meta( 'testimonial', 'contact_person', array(
		'type'         => 'string',
		'single'       => true,
		'show_in_rest' => true,
	) );
}
add_action( 'init', 'register_testimonial_post_type' );

function estatein_sanitize_rating( $value ) {
	return max( 1, min( 5, (int) $value ) );
}

/**
 * "Rating" meta box (1-5) for the `testimonial` CPT.
 */
function estatein_register_testimonial_rating_field() {
	add_action( 'add_meta_boxes_testimonial', function () {
		add_meta_box(
			'estatein_testimonial_rating',
			'Rating',
			'estatein_render_testimonial_rating_meta_box',
			'testimonial',
			'side'
		);
	} );

	add_action( 'save_post_testimonial', 'estatein_save_testimonial_rating_meta_box' );
}
add_action( 'init', 'estatein_register_testimonial_rating_field' );

function estatein_render_testimonial_rating_meta_box( $post ) {
	wp_nonce_field( 'estatein_save_testimonial_rating', 'estatein_testimonial_rating_nonce' );

	$rating = (int) get_post_meta( $post->ID, 'rating', true );
	$rating = $rating ? $rating : 5;
	?>
	<select name="estatein_testimonial_rating" id="estatein_testimonial_rating">
		<?php for ( $i = 5; $i >= 1; $i-- ) : ?>
			<option value="<?php echo esc_attr( $i ); ?>" <?php selected( $rating, $i ); ?>>
				<?php echo esc_html( $i ); ?> star<?php echo ( 1 === $i ) ? '' : 's'; ?>
			</option>
		<?php endfor; ?>
	</select>
	<?php
}

function estatein_save_testimonial_rating_meta_box( $post_id ) {
	if ( ! isset( $_POST['estatein_testimonial_rating_nonce'] )
		|| ! wp_verify_nonce( $_POST['estatein_testimonial_rating_nonce'], 'estatein_save_testimonial_rating' ) ) {
		return;
	}

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	if ( isset( $_POST['estatein_testimonial_rating'] ) ) {
		update_post_meta( $post_id, 'rating', estatein_sanitize_rating( $_POST['estatein_testimonial_rating'] ) );
	}
}

/**
 * Reads the `rating` meta on $post_id, clamped to 1-5 (defaults to 5).
 */
function estatein_get_testimonial_rating( $post_id ) {
	$rating = get_post_meta( $post_id, 'rating', true );

	return $rating ? estatein_sanitize_rating( $rating ) : 5;
}

estatein_register_location_field( 'testimonial' );

/**
 * "Client Details" meta box (company, domain, category, website,
 * client_since) for the `testimonial` CPT — covers client-showcase style
 * testimonials ("ABC Corporation — Since 2019") alongside individual
 * reviewer testimonials.
 */
function estatein_register_testimonial_client_fields() {
	add_action( 'add_meta_boxes_testimonial', function () {
		add_meta_box(
			'estatein_testimonial_client',
			'Client Details',
			'estatein_render_testimonial_client_meta_box',
			'testimonial',
			'normal'
		);
	} );

	add_action( 'save_post_testimonial', 'estatein_save_testimonial_client_meta_box' );
}
add_action( 'init', 'estatein_register_testimonial_client_fields' );

function estatein_render_testimonial_client_meta_box( $post ) {
	wp_nonce_field( 'estatein_save_testimonial_client', 'estatein_testimonial_client_nonce' );

	$fields = array(
		'company'        => 'Company',
		'contact_person' => 'Contact Person Name',
		'domain'         => 'Domain',
		'category'       => 'Category',
		'website'        => 'Website (optional)',
		'client_since'   => 'Client Since',
	);
	?>
	<table class="form-table">
		<?php foreach ( $fields as $key => $label ) : ?>
			<tr>
				<th><label for="estatein_testimonial_<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $label ); ?></label></th>
				<td>
					<input
						type="text"
						class="regular-text"
						id="estatein_testimonial_<?php echo esc_attr( $key ); ?>"
						name="estatein_testimonial_<?php echo esc_attr( $key ); ?>"
						value="<?php echo esc_attr( get_post_meta( $post->ID, $key, true ) ); ?>"
					/>
				</td>
			</tr>
		<?php endforeach; ?>
	</table>
	<?php
}

function estatein_save_testimonial_client_meta_box( $post_id ) {
	if ( ! isset( $_POST['estatein_testimonial_client_nonce'] )
		|| ! wp_verify_nonce( $_POST['estatein_testimonial_client_nonce'], 'estatein_save_testimonial_client' ) ) {
		return;
	}

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	if ( isset( $_POST['estatein_testimonial_company'] ) ) {
		update_post_meta( $post_id, 'company', sanitize_text_field( $_POST['estatein_testimonial_company'] ) );
	}

	if ( isset( $_POST['estatein_testimonial_contact_person'] ) ) {
		update_post_meta( $post_id, 'contact_person', sanitize_text_field( $_POST['estatein_testimonial_contact_person'] ) );
	}

	if ( isset( $_POST['estatein_testimonial_domain'] ) ) {
		update_post_meta( $post_id, 'domain', sanitize_text_field( $_POST['estatein_testimonial_domain'] ) );
	}

	if ( isset( $_POST['estatein_testimonial_category'] ) ) {
		update_post_meta( $post_id, 'category', sanitize_text_field( $_POST['estatein_testimonial_category'] ) );
	}

	if ( isset( $_POST['estatein_testimonial_website'] ) ) {
		update_post_meta( $post_id, 'website', sanitize_url( $_POST['estatein_testimonial_website'] ) );
	}

	if ( isset( $_POST['estatein_testimonial_client_since'] ) ) {
		update_post_meta( $post_id, 'client_since', sanitize_text_field( $_POST['estatein_testimonial_client_since'] ) );
	}
}
