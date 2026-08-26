<?php
/**
 * Site-wide contact info (email, phone, headquarters, social links) as a
 * single settings page rather than a post type — there's only ever one of
 * these, so there's nothing to repeat/list like location or testimonial.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function estatein_register_contact_settings() {
	register_setting( 'estatein_contact', 'estatein_contact_email', array( 'sanitize_callback' => 'sanitize_email' ) );
	register_setting( 'estatein_contact', 'estatein_contact_phone', array( 'sanitize_callback' => 'sanitize_text_field' ) );
	register_setting( 'estatein_contact', 'estatein_contact_headquarters_id', array( 'sanitize_callback' => 'absint' ) );
	register_setting( 'estatein_contact', 'estatein_contact_instagram_url', array( 'sanitize_callback' => 'sanitize_url' ) );
	register_setting( 'estatein_contact', 'estatein_contact_linkedin_url', array( 'sanitize_callback' => 'sanitize_url' ) );
	register_setting( 'estatein_contact', 'estatein_contact_facebook_url', array( 'sanitize_callback' => 'sanitize_url' ) );

	add_settings_section( 'estatein_contact_main', 'Contact Info', '__return_false', 'estatein-contact' );

	add_settings_field( 'estatein_contact_email', 'Email', 'estatein_render_contact_email_field', 'estatein-contact', 'estatein_contact_main' );
	add_settings_field( 'estatein_contact_phone', 'Phone', 'estatein_render_contact_phone_field', 'estatein-contact', 'estatein_contact_main' );
	add_settings_field( 'estatein_contact_headquarters_id', 'Main Headquarters', 'estatein_render_contact_headquarters_field', 'estatein-contact', 'estatein_contact_main' );
	add_settings_field( 'estatein_contact_instagram_url', 'Instagram URL', 'estatein_render_contact_instagram_field', 'estatein-contact', 'estatein_contact_main' );
	add_settings_field( 'estatein_contact_linkedin_url', 'LinkedIn URL', 'estatein_render_contact_linkedin_field', 'estatein-contact', 'estatein_contact_main' );
	add_settings_field( 'estatein_contact_facebook_url', 'Facebook URL', 'estatein_render_contact_facebook_field', 'estatein-contact', 'estatein_contact_main' );
}
add_action( 'admin_init', 'estatein_register_contact_settings' );

function estatein_register_contact_settings_page() {
	add_options_page( 'Contact Info', 'Contact Info', 'manage_options', 'estatein-contact', 'estatein_render_contact_settings_page' );
}
add_action( 'admin_menu', 'estatein_register_contact_settings_page' );

function estatein_render_contact_settings_page() {
	?>
	<div class="wrap">
		<h1>Contact Info</h1>
		<form method="post" action="options.php">
			<?php
			settings_fields( 'estatein_contact' );
			do_settings_sections( 'estatein-contact' );
			submit_button();
			?>
		</form>
	</div>
	<?php
}

function estatein_render_contact_email_field() {
	?>
	<input type="email" class="regular-text" name="estatein_contact_email" value="<?php echo esc_attr( get_option( 'estatein_contact_email' ) ); ?>" />
	<?php
}

function estatein_render_contact_phone_field() {
	?>
	<input type="text" class="regular-text" name="estatein_contact_phone" value="<?php echo esc_attr( get_option( 'estatein_contact_phone' ) ); ?>" />
	<?php
}

function estatein_render_contact_headquarters_field() {
	wp_dropdown_pages( array(
		'post_type'         => 'location',
		'name'              => 'estatein_contact_headquarters_id',
		'id'                => 'estatein_contact_headquarters_id',
		'selected'          => (int) get_option( 'estatein_contact_headquarters_id' ),
		'show_option_none'  => '— None —',
		'option_none_value' => 0,
		'sort_column'       => 'menu_order, post_title',
	) );
}

function estatein_render_contact_instagram_field() {
	?>
	<input type="url" class="regular-text" name="estatein_contact_instagram_url" value="<?php echo esc_attr( get_option( 'estatein_contact_instagram_url' ) ); ?>" />
	<?php
}

function estatein_render_contact_linkedin_field() {
	?>
	<input type="url" class="regular-text" name="estatein_contact_linkedin_url" value="<?php echo esc_attr( get_option( 'estatein_contact_linkedin_url' ) ); ?>" />
	<?php
}

function estatein_render_contact_facebook_field() {
	?>
	<input type="url" class="regular-text" name="estatein_contact_facebook_url" value="<?php echo esc_attr( get_option( 'estatein_contact_facebook_url' ) ); ?>" />
	<?php
}

/**
 * Reads all contact settings at once for templates, e.g.:
 *   $contact = estatein_get_contact_info();
 *   echo $contact['email'];
 *   echo $contact['headquarters'] ? $contact['headquarters']->post_title : '';
 */
function estatein_get_contact_info() {
	$headquarters_id = (int) get_option( 'estatein_contact_headquarters_id' );

	return array(
		'email'        => get_option( 'estatein_contact_email' ),
		'phone'        => get_option( 'estatein_contact_phone' ),
		'headquarters' => $headquarters_id ? get_post( $headquarters_id ) : null,
		'instagram'    => get_option( 'estatein_contact_instagram_url' ),
		'linkedin'     => get_option( 'estatein_contact_linkedin_url' ),
		'facebook'     => get_option( 'estatein_contact_facebook_url' ),
	);
}
