<?php
/**
 * Single Property: gallery/header, description + amenities, inquire form,
 * pricing breakdown, then the shared FAQ and closing CTA sections.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

while ( have_posts() ) :
	the_post();

	$estatein_property = get_post();

	get_template_part( 'template-parts/single-property/gallery', null, array( 'property' => $estatein_property ) );
	get_template_part( 'template-parts/single-property/description-features', null, array( 'property' => $estatein_property ) );
	get_template_part( 'template-parts/single-property/inquire', null, array( 'property' => $estatein_property ) );
	get_template_part( 'template-parts/single-property/pricing', null, array( 'property' => $estatein_property ) );

endwhile;

get_template_part( 'template-parts/front-page/faq' );
get_template_part( 'template-parts/front-page/cta' );

get_footer();
