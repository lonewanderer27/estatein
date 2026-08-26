<?php
/**
 * Homepage: hero, highlights, featured properties, testimonials, FAQ, CTA.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

get_template_part( 'template-parts/front-page/hero' );
get_template_part( 'template-parts/front-page/highlights' );
get_template_part( 'template-parts/front-page/featured-properties' );
get_template_part( 'template-parts/front-page/testimonials' );
get_template_part( 'template-parts/front-page/faq' );
get_template_part( 'template-parts/front-page/cta' );

get_footer();
