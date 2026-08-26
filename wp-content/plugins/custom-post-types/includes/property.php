<?php
/**
 * Property CPT: title = property name, editor = description (gallery lives
 * here too via the native Gallery block), thumbnail = cover image. Price/
 * bed/bath/area, a structured pricing breakdown, amenities, and location
 * are added via their own meta boxes below.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function register_property_post_type() {
	register_post_type( 'property', array(
		'label'               => 'Properties',
		'labels'              => array(
			'name'          => 'Properties',
			'singular_name' => 'Property',
			'add_new_item'  => 'Add New Property',
			'edit_item'     => 'Edit Property',
			'all_items'     => 'All Properties',
			'search_items'  => 'Search Properties',
		),
		'public'              => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'show_in_rest'        => true,
		'has_archive'         => true,
		'hierarchical'        => false,
		'supports'            => array( 'title', 'editor', 'thumbnail' ),
		'menu_icon'           => 'dashicons-admin-home',
	) );

	register_post_meta( 'property', 'price', array(
		'type'         => 'number',
		'single'       => true,
		'show_in_rest' => true,
	) );

	register_post_meta( 'property', 'bedrooms', array(
		'type'         => 'integer',
		'single'       => true,
		'show_in_rest' => true,
	) );

	register_post_meta( 'property', 'bathrooms', array(
		'type'         => 'integer',
		'single'       => true,
		'show_in_rest' => true,
	) );

	register_post_meta( 'property', 'area_sqft', array(
		'type'         => 'integer',
		'single'       => true,
		'show_in_rest' => true,
	) );

	register_post_meta( 'property', 'amenities', array(
		'type'         => 'string',
		'single'       => true,
		'show_in_rest' => true,
	) );

	foreach ( estatein_property_pricing_fields() as $key => $field ) {
		register_post_meta( 'property', $key, array(
			'type'         => 'text' === $field['type'] ? 'string' : 'number',
			'single'       => true,
			'show_in_rest' => true,
		) );
	}
}
add_action( 'init', 'register_property_post_type' );

/**
 * "Property Details" meta box (side): price, bedrooms, bathrooms, area.
 */
function estatein_register_property_details_field() {
	add_action( 'add_meta_boxes_property', function () {
		add_meta_box(
			'estatein_property_details',
			'Property Details',
			'estatein_render_property_details_meta_box',
			'property',
			'side'
		);
	} );

	add_action( 'save_post_property', 'estatein_save_property_details_meta_box' );
}
add_action( 'init', 'estatein_register_property_details_field' );

function estatein_render_property_details_meta_box( $post ) {
	wp_nonce_field( 'estatein_save_property_details', 'estatein_property_details_nonce' );

	$fields = array(
		'price'     => 'Listing Price',
		'bedrooms'  => 'Bedrooms',
		'bathrooms' => 'Bathrooms',
		'area_sqft' => 'Area (sq ft)',
	);

	foreach ( $fields as $key => $label ) {
		?>
		<p>
			<label for="estatein_property_<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $label ); ?></label><br />
			<input
				type="number"
				class="widefat"
				id="estatein_property_<?php echo esc_attr( $key ); ?>"
				name="estatein_property_<?php echo esc_attr( $key ); ?>"
				value="<?php echo esc_attr( get_post_meta( $post->ID, $key, true ) ); ?>"
			/>
		</p>
		<?php
	}
}

function estatein_save_property_details_meta_box( $post_id ) {
	if ( ! isset( $_POST['estatein_property_details_nonce'] )
		|| ! wp_verify_nonce( $_POST['estatein_property_details_nonce'], 'estatein_save_property_details' ) ) {
		return;
	}

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	foreach ( array( 'price', 'bedrooms', 'bathrooms', 'area_sqft' ) as $key ) {
		if ( isset( $_POST[ 'estatein_property_' . $key ] ) ) {
			update_post_meta( $post_id, $key, sanitize_text_field( $_POST[ 'estatein_property_' . $key ] ) );
		}
	}
}

/**
 * Field map for the "Pricing Breakdown" meta box, shared by the register,
 * render, save, and estatein_get_property_pricing() below.
 */
function estatein_property_pricing_fields() {
	return array(
		'additional_fee_transfer_tax'      => array( 'label' => 'Additional Fees — Property Transfer Tax', 'type' => 'number' ),
		'additional_fee_legal'             => array( 'label' => 'Additional Fees — Legal Fees', 'type' => 'number' ),
		'additional_fee_home_inspection'   => array( 'label' => 'Additional Fees — Home Inspection', 'type' => 'number' ),
		'additional_fee_property_insurance'=> array( 'label' => 'Additional Fees — Property Insurance (annual)', 'type' => 'number' ),
		'additional_fee_mortgage_note'     => array( 'label' => 'Additional Fees — Mortgage Fees Note', 'type' => 'text' ),
		'monthly_property_taxes'           => array( 'label' => 'Monthly Costs — Property Taxes', 'type' => 'number' ),
		'monthly_hoa_fee'                  => array( 'label' => 'Monthly Costs — HOA Fee', 'type' => 'number' ),
		'monthly_property_insurance'       => array( 'label' => 'Monthly Costs — Property Insurance', 'type' => 'number' ),
		'monthly_mortgage_note'            => array( 'label' => 'Monthly Costs — Mortgage Payment Note', 'type' => 'text' ),
		'down_payment_amount'              => array( 'label' => 'Financing — Down Payment Amount', 'type' => 'number' ),
		'mortgage_amount'                  => array( 'label' => 'Financing — Mortgage Amount', 'type' => 'number' ),
	);
}

function estatein_register_property_pricing_field() {
	add_action( 'add_meta_boxes_property', function () {
		add_meta_box(
			'estatein_property_pricing',
			'Pricing Breakdown',
			'estatein_render_property_pricing_meta_box',
			'property',
			'normal'
		);
	} );

	add_action( 'save_post_property', 'estatein_save_property_pricing_meta_box' );
}
add_action( 'init', 'estatein_register_property_pricing_field' );

function estatein_render_property_pricing_meta_box( $post ) {
	wp_nonce_field( 'estatein_save_property_pricing', 'estatein_property_pricing_nonce' );
	?>
	<table class="form-table">
		<?php foreach ( estatein_property_pricing_fields() as $key => $field ) : ?>
			<tr>
				<th><label for="estatein_property_<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $field['label'] ); ?></label></th>
				<td>
					<input
						type="<?php echo ( 'number' === $field['type'] ) ? 'number' : 'text'; ?>"
						class="regular-text"
						id="estatein_property_<?php echo esc_attr( $key ); ?>"
						name="estatein_property_<?php echo esc_attr( $key ); ?>"
						value="<?php echo esc_attr( get_post_meta( $post->ID, $key, true ) ); ?>"
					/>
				</td>
			</tr>
		<?php endforeach; ?>
	</table>
	<?php
}

function estatein_save_property_pricing_meta_box( $post_id ) {
	if ( ! isset( $_POST['estatein_property_pricing_nonce'] )
		|| ! wp_verify_nonce( $_POST['estatein_property_pricing_nonce'], 'estatein_save_property_pricing' ) ) {
		return;
	}

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	foreach ( estatein_property_pricing_fields() as $key => $field ) {
		if ( ! isset( $_POST[ 'estatein_property_' . $key ] ) ) {
			continue;
		}

		update_post_meta( $post_id, $key, sanitize_text_field( $_POST[ 'estatein_property_' . $key ] ) );
	}
}

/**
 * Returns every pricing field for $post_id, plus a computed
 * `total_additional_fees` (sum of the four Additional Fees numbers) — the
 * one figure on the page that's an actual total rather than a restated
 * input.
 */
function estatein_get_property_pricing( $post_id ) {
	$pricing = array();

	foreach ( estatein_property_pricing_fields() as $key => $field ) {
		$pricing[ $key ] = get_post_meta( $post_id, $key, true );
	}

	$pricing['total_additional_fees'] =
		(float) $pricing['additional_fee_transfer_tax']
		+ (float) $pricing['additional_fee_legal']
		+ (float) $pricing['additional_fee_home_inspection']
		+ (float) $pricing['additional_fee_property_insurance'];

	return $pricing;
}

/**
 * "Amenities" meta box: one feature per line.
 */
function estatein_register_property_amenities_field() {
	add_action( 'add_meta_boxes_property', function () {
		add_meta_box(
			'estatein_property_amenities',
			'Amenities',
			'estatein_render_property_amenities_meta_box',
			'property',
			'normal'
		);
	} );

	add_action( 'save_post_property', 'estatein_save_property_amenities_meta_box' );
}
add_action( 'init', 'estatein_register_property_amenities_field' );

function estatein_render_property_amenities_meta_box( $post ) {
	wp_nonce_field( 'estatein_save_property_amenities', 'estatein_property_amenities_nonce' );
	?>
	<textarea
		class="widefat"
		rows="6"
		name="estatein_property_amenities"
		placeholder="One amenity per line"
	><?php echo esc_textarea( get_post_meta( $post->ID, 'amenities', true ) ); ?></textarea>
	<?php
}

function estatein_save_property_amenities_meta_box( $post_id ) {
	if ( ! isset( $_POST['estatein_property_amenities_nonce'] )
		|| ! wp_verify_nonce( $_POST['estatein_property_amenities_nonce'], 'estatein_save_property_amenities' ) ) {
		return;
	}

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	if ( isset( $_POST['estatein_property_amenities'] ) ) {
		update_post_meta( $post_id, 'amenities', sanitize_textarea_field( $_POST['estatein_property_amenities'] ) );
	}
}

/**
 * Splits the amenities textarea into a clean array, one entry per line.
 */
function estatein_get_property_amenities( $post_id ) {
	$raw = get_post_meta( $post_id, 'amenities', true );

	if ( ! $raw ) {
		return array();
	}

	$lines = array_map( 'trim', explode( "\n", $raw ) );

	return array_values( array_filter( $lines ) );
}

estatein_register_location_field( 'property' );
