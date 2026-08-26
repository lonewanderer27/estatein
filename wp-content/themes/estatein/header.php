<?php
/**
 * Shared document head + announcement bar + primary navbar.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$estatein_contact = function_exists( 'estatein_get_contact_info' ) ? estatein_get_contact_info() : array();
$estatein_contact_email = ! empty( $estatein_contact['email'] ) ? $estatein_contact['email'] : '';
?>
<!doctype html>
<html <?php language_attributes(); ?> data-bs-theme="dark">
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<?php get_template_part( 'template-parts/components/announcement-bar' ); ?>

<header class="est-header">
	<nav class="navbar navbar-expand-lg" data-bs-theme="dark">
		<div class="container">
			<a class="navbar-brand est-brand" href="<?php echo esc_url( home_url( '/' ) ); ?>">
				<span class="est-brand-mark" aria-hidden="true"></span>
				<?php bloginfo( 'name' ); ?>
			</a>

			<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#estNavbar" aria-controls="estNavbar" aria-expanded="false" aria-label="Toggle navigation">
				<span class="navbar-toggler-icon"></span>
			</button>

			<div class="collapse navbar-collapse" id="estNavbar">
				<?php
				wp_nav_menu( array(
					'theme_location' => 'primary',
					'container'      => false,
					'menu_class'     => 'navbar-nav mx-lg-auto',
					'fallback_cb'    => 'estatein_default_primary_menu',
					'items_wrap'     => '<ul id="%1$s" class="%2$s">%3$s</ul>',
				) );
				?>
				<a href="<?php echo esc_url( $estatein_contact_email ? 'mailto:' . $estatein_contact_email : '#' ); ?>" class="btn btn-primary est-btn-contact d-none d-lg-inline-flex">Contact Us</a>
			</div>
		</div>
	</nav>
</header>
