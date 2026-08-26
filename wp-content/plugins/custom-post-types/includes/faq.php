<?php
/**
 * FAQ CPT: title = question, editor = full answer, excerpt = short teaser
 * text for card-style listings. Public (unlike location/testimonial) so
 * each FAQ has its own permalink for a "Read More" link to point to.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function register_faq_post_type() {
	register_post_type( 'faq', array(
		'label'               => 'FAQs',
		'labels'              => array(
			'name'          => 'FAQs',
			'singular_name' => 'FAQ',
			'add_new_item'  => 'Add New FAQ',
			'edit_item'     => 'Edit FAQ',
			'all_items'     => 'All FAQs',
			'search_items'  => 'Search FAQs',
		),
		'public'              => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'show_in_rest'        => true,
		'has_archive'         => false,
		'hierarchical'        => false,
		'supports'            => array( 'title', 'editor', 'excerpt', 'page-attributes' ),
		'menu_icon'           => 'dashicons-editor-help',
	) );
}
add_action( 'init', 'register_faq_post_type' );

/**
 * Fetches `faq` posts ordered by their manual "Order" field. Pass
 * $args to override defaults, e.g. array( 'posts_per_page' => 3 ) for a
 * homepage teaser.
 */
function estatein_get_faqs( $args = array() ) {
	$defaults = array(
		'post_type'      => 'faq',
		'orderby'        => 'menu_order',
		'order'          => 'ASC',
		'posts_per_page' => -1,
	);

	return get_posts( wp_parse_args( $args, $defaults ) );
}

/**
 * If a FAQ is saved with no Excerpt, fills it in with the first 20 words
 * of the answer so the field isn't left blank in wp-admin. Leaves a
 * manually written excerpt untouched.
 */
function estatein_autofill_faq_excerpt( $post_id, $post ) {
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	if ( wp_is_post_revision( $post_id ) || wp_is_post_autosave( $post_id ) ) {
		return;
	}

	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	if ( ! empty( $post->post_excerpt ) ) {
		return;
	}

	$excerpt = wp_trim_words( wp_strip_all_tags( $post->post_content ), 20 );

	if ( '' === $excerpt ) {
		return;
	}

	remove_action( 'save_post_faq', 'estatein_autofill_faq_excerpt' );
	wp_update_post( array(
		'ID'           => $post_id,
		'post_excerpt' => $excerpt,
	) );
	add_action( 'save_post_faq', 'estatein_autofill_faq_excerpt', 10, 2 );
}
add_action( 'save_post_faq', 'estatein_autofill_faq_excerpt', 10, 2 );
