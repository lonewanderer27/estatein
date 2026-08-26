<?php
/**
 * Plugin Name: Custom Post Types
 * Description: Registers custom post types for this site.
 * Version: 1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once __DIR__ . '/includes/location.php';
require_once __DIR__ . '/includes/testimonial.php';
require_once __DIR__ . '/includes/faq.php';
require_once __DIR__ . '/includes/value.php';
require_once __DIR__ . '/includes/contact-settings.php';
require_once __DIR__ . '/includes/property.php';
require_once __DIR__ . '/includes/team-member.php';
require_once __DIR__ . '/includes/highlight.php';
require_once __DIR__ . '/includes/inquiry.php';
