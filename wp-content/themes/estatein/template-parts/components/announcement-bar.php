<?php
/**
 * Site-wide announcement bar: sparkle icon, the site's tagline, a "Learn
 * More" link to the property archive, and a dismiss button (handled by
 * assets/js/main.js).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$estatein_properties_url = get_post_type_archive_link( 'property' );
?>
<div class="est-announcement" id="estAnnouncement">
	<div class="container d-flex align-items-center justify-content-center position-relative">
		<p class="mb-0 small">
			<?php estatein_theme_icon( 'sparkle' ); ?>
			<?php bloginfo( 'description' ); ?>
			<a href="<?php echo esc_url( $estatein_properties_url ? $estatein_properties_url : '#' ); ?>" class="est-announcement-link">Learn More</a>
		</p>
		<button type="button" class="est-announcement-close" id="estAnnouncementClose" aria-label="Dismiss announcement">
			<?php estatein_theme_icon( 'close' ); ?>
		</button>
	</div>
</div>
