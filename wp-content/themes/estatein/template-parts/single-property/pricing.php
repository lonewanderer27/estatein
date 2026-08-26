<?php
/**
 * Comprehensive Pricing Details — listing price plus four pricing-card
 * breakdowns (Additional Fees, Monthly Costs, Total Initial Costs, Monthly
 * Expenses), each built from estatein_get_property_pricing(). Expects
 * $args['property'].
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$estatein_property = $args['property'];
$estatein_id       = $estatein_property->ID;
$estatein_price    = get_post_meta( $estatein_id, 'price', true );
$estatein_pricing  = function_exists( 'estatein_get_property_pricing' ) ? estatein_get_property_pricing( $estatein_id ) : array();

if ( empty( $estatein_pricing ) ) {
	return;
}

$estatein_down_payment_pct = '';

if ( $estatein_price && $estatein_pricing['down_payment_amount'] ) {
	$estatein_down_payment_pct = round( ( (float) $estatein_pricing['down_payment_amount'] / (float) $estatein_price ) * 100 ) . '% of listing price';
}
?>
<section class="est-section" id="estPricing">
	<div class="container">
		<div class="row mb-4">
			<div class="col-lg-8">
				<p class="est-eyebrow"><?php estatein_theme_icon( 'sparkle' ); ?><span class="est-eyebrow-dot"></span></p>
				<h2 class="est-section-title">Comprehensive Pricing Details</h2>
				<p class="est-section-subtitle">At Estatein, transparency is key. We want you to have a clear understanding of all costs associated with your property investment. Below, we break down the pricing for <?php echo esc_html( $estatein_property->post_title ); ?> to help you make an informed decision.</p>
			</div>
		</div>

		<div class="est-pricing-note d-flex gap-3 align-items-start mb-4">
			<span class="est-pricing-note-badge">Note</span>
			<p class="mb-0">The figures provided above are estimates and may vary depending on the property, location, and individual circumstances.</p>
		</div>

		<div class="row g-4 mb-4">
			<div class="col-lg-3">
				<span class="est-property-price-label d-block">Listing Price</span>
				<span class="est-pricing-listing-price d-block"><?php echo $estatein_price ? '$' . esc_html( number_format( (float) $estatein_price ) ) : '—'; ?></span>
			</div>
			<div class="col-lg-9">
				<?php
				get_template_part( 'template-parts/components/pricing-card', null, array(
					'title' => 'Additional Fees',
					'rows'  => array(
						array( 'label' => 'Property Transfer Tax', 'value' => $estatein_pricing['additional_fee_transfer_tax'], 'description' => 'Based on the sale price and local regulations' ),
						array( 'label' => 'Legal Fees', 'value' => $estatein_pricing['additional_fee_legal'], 'description' => 'Approximate cost for legal services, including title transfer' ),
						array( 'label' => 'Home Inspection', 'value' => $estatein_pricing['additional_fee_home_inspection'], 'description' => 'Recommended for due diligence' ),
						array( 'label' => 'Property Insurance', 'value' => $estatein_pricing['additional_fee_property_insurance'], 'description' => 'Annual cost for comprehensive property insurance' ),
						array( 'label' => 'Mortgage Fees', 'value' => $estatein_pricing['additional_fee_mortgage_note'] ? $estatein_pricing['additional_fee_mortgage_note'] : 'Varies', 'description' => 'If applicable, consult with your lender for specific details' ),
					),
				) );
				?>
			</div>
		</div>

		<div class="row g-4">
			<div class="col-12">
				<?php
				get_template_part( 'template-parts/components/pricing-card', null, array(
					'title' => 'Monthly Costs',
					'rows'  => array(
						array( 'label' => 'Property Taxes', 'value' => $estatein_pricing['monthly_property_taxes'], 'description' => 'Approximate monthly property tax based on the sale price and local rates' ),
						array( 'label' => 'Homeowners\' Association Fee', 'value' => $estatein_pricing['monthly_hoa_fee'], 'description' => 'Monthly fee for common area maintenance and security' ),
					),
				) );
				?>
			</div>
			<div class="col-12">
				<?php
				get_template_part( 'template-parts/components/pricing-card', null, array(
					'title' => 'Total Initial Costs',
					'rows'  => array(
						array( 'label' => 'Listing Price', 'value' => $estatein_price ),
						array( 'label' => 'Additional Fees', 'value' => $estatein_pricing['total_additional_fees'], 'description' => 'Property transfer tax, legal fees, inspection, insurance' ),
						array( 'label' => 'Down Payment', 'value' => $estatein_pricing['down_payment_amount'], 'description' => $estatein_down_payment_pct ),
						array( 'label' => 'Mortgage Amount', 'value' => $estatein_pricing['mortgage_amount'], 'description' => 'If applicable' ),
					),
				) );
				?>
			</div>
			<div class="col-12">
				<?php
				get_template_part( 'template-parts/components/pricing-card', null, array(
					'title' => 'Monthly Expenses',
					'rows'  => array(
						array( 'label' => 'Property Taxes', 'value' => $estatein_pricing['monthly_property_taxes'] ),
						array( 'label' => 'Homeowners\' Association Fee', 'value' => $estatein_pricing['monthly_hoa_fee'] ),
						array( 'label' => 'Mortgage Payment', 'value' => $estatein_pricing['monthly_mortgage_note'] ? $estatein_pricing['monthly_mortgage_note'] : 'Varies based on terms and interest rate' ),
						array( 'label' => 'Property Insurance', 'value' => $estatein_pricing['monthly_property_insurance'], 'description' => 'Approximate monthly cost' ),
					),
				) );
				?>
			</div>
		</div>
	</div>
</section>
