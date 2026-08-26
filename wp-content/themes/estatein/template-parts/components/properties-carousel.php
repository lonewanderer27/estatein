<?php
/**
 * Property carousel component: 3-per-slide on lg+ and 1-per-slide on mobile
 * (two parallel carousels, the standard Bootstrap pattern for a responsive
 * per-slide count), each card rendered by
 * template-parts/components/property-card.php. Expects $args['properties']
 * (a non-empty array of `property` posts); $args['id_prefix'] namespaces
 * the two carousel element ids when more than one instance could ever
 * appear in the same document (default "estProperties").
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$estatein_properties = $args['properties'];

if ( empty( $estatein_properties ) ) {
	return;
}

$estatein_id_prefix = ! empty( $args['id_prefix'] ) ? $args['id_prefix'] : 'estProperties';

$estatein_total        = count( $estatein_properties );
$estatein_desktop_rows = array_chunk( $estatein_properties, 3 );
$estatein_desktop_total_slides = count( $estatein_desktop_rows );

$estatein_desktop_arrow_class = 'est-carousel-arrow d-none ' . ( $estatein_desktop_total_slides > 1 ? 'd-lg-inline-flex' : '' );
$estatein_mobile_arrow_class  = 'est-carousel-arrow ' . ( $estatein_total > 1 ? 'd-lg-none' : 'd-none' );

$estatein_lg_id = $estatein_id_prefix . 'CarouselLg';
$estatein_sm_id = $estatein_id_prefix . 'CarouselSm';
?>
<!-- Desktop/tablet: 3 properties per slide -->
<div id="<?php echo esc_attr( $estatein_lg_id ); ?>" class="carousel slide est-carousel d-none d-lg-block" data-bs-touch="true">
	<div class="carousel-inner">
		<?php foreach ( $estatein_desktop_rows as $estatein_index => $estatein_row ) : ?>
			<div class="carousel-item <?php echo 0 === $estatein_index ? 'active' : ''; ?>">
				<div class="row g-4">
					<?php foreach ( $estatein_row as $estatein_property ) : ?>
						<div class="col-lg-4">
							<?php get_template_part( 'template-parts/components/property-card', null, array( 'property' => $estatein_property ) ); ?>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
		<?php endforeach; ?>
	</div>
</div>

<!-- Mobile: 1 property per slide -->
<div id="<?php echo esc_attr( $estatein_sm_id ); ?>" class="carousel slide est-carousel d-lg-none" data-bs-touch="true">
	<div class="carousel-inner">
		<?php foreach ( $estatein_properties as $estatein_index => $estatein_property ) : ?>
			<div class="carousel-item <?php echo 0 === $estatein_index ? 'active' : ''; ?>">
				<?php get_template_part( 'template-parts/components/property-card', null, array( 'property' => $estatein_property ) ); ?>
			</div>
		<?php endforeach; ?>
	</div>
</div>

<div class="est-carousel-controls">
	<span class="est-carousel-counter d-none d-lg-inline" data-total="<?php echo esc_attr( $estatein_desktop_total_slides ); ?>">01 of <?php echo esc_html( sprintf( '%02d', $estatein_desktop_total_slides ) ); ?></span>
	<span class="est-carousel-counter d-lg-none" data-total="<?php echo esc_attr( $estatein_total ); ?>">01 of <?php echo esc_html( sprintf( '%02d', $estatein_total ) ); ?></span>

	<div class="d-flex gap-2">
		<button class="<?php echo esc_attr( $estatein_desktop_arrow_class ); ?>" type="button" data-bs-target="#<?php echo esc_attr( $estatein_lg_id ); ?>" data-bs-slide="prev" aria-label="Previous"><?php estatein_theme_icon( 'arrow-left' ); ?></button>
		<button class="<?php echo esc_attr( $estatein_desktop_arrow_class ); ?>" type="button" data-bs-target="#<?php echo esc_attr( $estatein_lg_id ); ?>" data-bs-slide="next" aria-label="Next"><?php estatein_theme_icon( 'arrow-right' ); ?></button>

		<button class="<?php echo esc_attr( $estatein_mobile_arrow_class ); ?>" type="button" data-bs-target="#<?php echo esc_attr( $estatein_sm_id ); ?>" data-bs-slide="prev" aria-label="Previous"><?php estatein_theme_icon( 'arrow-left' ); ?></button>
		<button class="<?php echo esc_attr( $estatein_mobile_arrow_class ); ?>" type="button" data-bs-target="#<?php echo esc_attr( $estatein_sm_id ); ?>" data-bs-slide="next" aria-label="Next"><?php estatein_theme_icon( 'arrow-right' ); ?></button>
	</div>
</div>
