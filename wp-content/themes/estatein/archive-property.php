<?php
/**
 * Properties archive: search/filter hero, the filtered listing, the
 * general property-request lead form, then the shared closing CTA.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

get_template_part( 'template-parts/archive-property/hero' );
get_template_part( 'template-parts/archive-property/listing' );
get_template_part( 'template-parts/archive-property/request' );
get_template_part( 'template-parts/front-page/cta' );

get_footer();
