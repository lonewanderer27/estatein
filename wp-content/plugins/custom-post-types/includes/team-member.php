<?php
/**
 * Team Member CPT: first name, middle name, last name, and position —
 * same admin-managed pattern as location/testimonial/value.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function register_team_member_post_type() {
	register_post_type( 'team_member', array(
		'label'               => 'Team Members',
		'labels'              => array(
			'name'          => 'Team Members',
			'singular_name' => 'Team Member',
			'add_new_item'  => 'Add New Team Member',
			'edit_item'     => 'Edit Team Member',
			'all_items'     => 'All Team Members',
			'search_items'  => 'Search Team Members',
		),
		'public'              => false,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'show_in_rest'        => true,
		'publicly_queryable'  => false,
		'has_archive'         => false,
		'hierarchical'        => false,
		'supports'            => array( 'title', 'thumbnail' ),
		'menu_icon'           => 'dashicons-groups',
	) );

	foreach ( estatein_team_member_fields() as $key => $label ) {
		register_post_meta( 'team_member', $key, array(
			'type'         => 'string',
			'single'       => true,
			'show_in_rest' => true,
		) );
	}

	foreach ( array_keys( estatein_team_member_social_fields() ) as $key ) {
		register_post_meta( 'team_member', $key, array(
			'type'              => 'string',
			'single'            => true,
			'show_in_rest'      => true,
			'sanitize_callback' => 'sanitize_url',
		) );
	}
}
add_action( 'init', 'register_team_member_post_type' );

/**
 * Field map shared by the register, render, and save code below.
 */
function estatein_team_member_fields() {
	return array(
		'first_name'  => 'First Name',
		'middle_name' => 'Middle Name',
		'last_name'   => 'Last Name',
		'position'    => 'Position',
	);
}

function estatein_register_team_member_fields() {
	add_action( 'add_meta_boxes_team_member', function () {
		add_meta_box(
			'estatein_team_member_details',
			'Name & Position',
			'estatein_render_team_member_meta_box',
			'team_member',
			'normal'
		);
	} );

	add_action( 'save_post_team_member', 'estatein_save_team_member_meta_box' );
}
add_action( 'init', 'estatein_register_team_member_fields' );

function estatein_render_team_member_meta_box( $post ) {
	wp_nonce_field( 'estatein_save_team_member', 'estatein_team_member_nonce' );
	?>
	<table class="form-table">
		<?php foreach ( estatein_team_member_fields() as $key => $label ) : ?>
			<tr>
				<th><label for="estatein_team_member_<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $label ); ?></label></th>
				<td>
					<input
						type="text"
						class="regular-text"
						id="estatein_team_member_<?php echo esc_attr( $key ); ?>"
						name="estatein_team_member_<?php echo esc_attr( $key ); ?>"
						value="<?php echo esc_attr( get_post_meta( $post->ID, $key, true ) ); ?>"
					/>
				</td>
			</tr>
		<?php endforeach; ?>
	</table>
	<?php
}

function estatein_save_team_member_meta_box( $post_id ) {
	if ( ! isset( $_POST['estatein_team_member_nonce'] )
		|| ! wp_verify_nonce( $_POST['estatein_team_member_nonce'], 'estatein_save_team_member' ) ) {
		return;
	}

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	foreach ( estatein_team_member_fields() as $key => $label ) {
		if ( isset( $_POST[ 'estatein_team_member_' . $key ] ) ) {
			update_post_meta( $post_id, $key, sanitize_text_field( $_POST[ 'estatein_team_member_' . $key ] ) );
		}
	}
}

/**
 * Social link field map — same three platforms as Contact Info
 * (includes/contact-settings.php).
 */
function estatein_team_member_social_fields() {
	return array(
		'instagram_url' => 'Instagram URL',
		'linkedin_url'  => 'LinkedIn URL',
		'facebook_url'  => 'Facebook URL',
	);
}

function estatein_register_team_member_social_fields() {
	add_action( 'add_meta_boxes_team_member', function () {
		add_meta_box(
			'estatein_team_member_social',
			'Social Links',
			'estatein_render_team_member_social_meta_box',
			'team_member',
			'normal'
		);
	} );

	add_action( 'save_post_team_member', 'estatein_save_team_member_social_meta_box' );
}
add_action( 'init', 'estatein_register_team_member_social_fields' );

function estatein_render_team_member_social_meta_box( $post ) {
	wp_nonce_field( 'estatein_save_team_member_social', 'estatein_team_member_social_nonce' );
	?>
	<table class="form-table">
		<?php foreach ( estatein_team_member_social_fields() as $key => $label ) : ?>
			<tr>
				<th><label for="estatein_team_member_<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $label ); ?></label></th>
				<td>
					<input
						type="url"
						class="regular-text"
						id="estatein_team_member_<?php echo esc_attr( $key ); ?>"
						name="estatein_team_member_<?php echo esc_attr( $key ); ?>"
						value="<?php echo esc_attr( get_post_meta( $post->ID, $key, true ) ); ?>"
					/>
				</td>
			</tr>
		<?php endforeach; ?>
	</table>
	<?php
}

function estatein_save_team_member_social_meta_box( $post_id ) {
	if ( ! isset( $_POST['estatein_team_member_social_nonce'] )
		|| ! wp_verify_nonce( $_POST['estatein_team_member_social_nonce'], 'estatein_save_team_member_social' ) ) {
		return;
	}

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	foreach ( array_keys( estatein_team_member_social_fields() ) as $key ) {
		if ( isset( $_POST[ 'estatein_team_member_' . $key ] ) ) {
			update_post_meta( $post_id, $key, sanitize_url( $_POST[ 'estatein_team_member_' . $key ] ) );
		}
	}
}
