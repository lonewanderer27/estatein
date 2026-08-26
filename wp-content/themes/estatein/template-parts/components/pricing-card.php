<?php
/**
 * Pricing breakdown card. Expects $args['title'] and $args['rows'] (each a
 * ['label', 'value', 'description'?] array); optional $args['learn_more_url'].
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$estatein_title          = $args['title'];
$estatein_rows           = $args['rows'];
$estatein_learn_more_url = isset( $args['learn_more_url'] ) ? $args['learn_more_url'] : '#';
?>
<div class="est-pricing-card h-100">
	<div class="d-flex align-items-center justify-content-between mb-3">
		<h3 class="est-pricing-card-title mb-0"><?php echo esc_html( $estatein_title ); ?></h3>
		<a href="<?php echo esc_url( $estatein_learn_more_url ); ?>" class="btn est-btn-outline est-btn-sm">Learn More</a>
	</div>

	<?php foreach ( array_chunk( $estatein_rows, 2 ) as $estatein_group_index => $estatein_row_group ) : ?>
		<div class="row g-3 est-pricing-row-group<?php echo $estatein_group_index > 0 ? ' est-pricing-row-group-divider' : ''; ?>">
			<?php foreach ( $estatein_row_group as $estatein_row ) : ?>
				<div class="col-md-6">
					<span class="est-pricing-row-label d-block"><?php echo esc_html( $estatein_row['label'] ); ?></span>
					<div class="d-flex align-items-center flex-wrap gap-2">
						<span class="est-pricing-row-value">
							<?php
							$estatein_value = $estatein_row['value'];

							if ( is_numeric( $estatein_value ) && '' !== $estatein_value ) {
								echo '$' . esc_html( number_format( (float) $estatein_value ) );
							} elseif ( $estatein_value ) {
								echo esc_html( $estatein_value );
							} else {
								echo '—';
							}
							?>
						</span>
						<?php if ( ! empty( $estatein_row['description'] ) ) : ?>
							<span class="est-pricing-row-badge"><?php echo esc_html( $estatein_row['description'] ); ?></span>
						<?php endif; ?>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
	<?php endforeach; ?>
</div>
