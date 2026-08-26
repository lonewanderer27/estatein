<?php
/**
 * One "service category" section (Property Selling / Property Management /
 * Investment Advisory), reused across the services page. Expects $args:
 * id, title, subtitle, items (4x ['icon', 'title', 'description']),
 * featured (['title', 'description', 'learn_more_url'?]) — 3 item cards,
 * a 4th item, then a wide featured card.
 *
 * TODO: replace $args['items']/$args['featured'] with a query against a
 * `service`/`service-category` CPT once the plugin adds one — for now the
 * category data is a static array defined in page-services.php.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$estatein_id       = $args['id'];
$estatein_title    = $args['title'];
$estatein_subtitle = $args['subtitle'];
$estatein_items    = $args['items'];
$estatein_featured = $args['featured'];
?>
<section class="est-section" id="<?php echo esc_attr( $estatein_id ); ?>">
	<div class="container">
		<p class="est-eyebrow"><?php estatein_theme_icon( 'sparkle' ); ?><span class="est-eyebrow-dot"></span></p>
		<h2 class="est-section-title"><?php echo esc_html( $estatein_title ); ?></h2>
		<p class="est-section-subtitle mb-4 mb-lg-5"><?php echo esc_html( $estatein_subtitle ); ?></p>

		<div class="row g-3 g-lg-4">
			<?php foreach ( $estatein_items as $estatein_index => $estatein_item ) : ?>
				<div class="col-12 col-lg-4">
					<?php get_template_part( 'template-parts/components/service-item-card', null, $estatein_item ); ?>
				</div>
				<?php if ( 3 === $estatein_index ) : ?>
					<div class="col-12 col-lg-8">
						<?php get_template_part( 'template-parts/components/service-featured-card', null, $estatein_featured ); ?>
					</div>
				<?php endif; ?>
			<?php endforeach; ?>
		</div>
	</div>
</section>
