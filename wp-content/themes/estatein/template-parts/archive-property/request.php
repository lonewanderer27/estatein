<?php
/**
 * "Let's Make it Happen" — the properties archive's general lead form
 * (buyer preferences, not tied to one property). Submits to admin-post.php
 * (estatein_handle_property_request_submission() in the custom-post-types
 * plugin). Visually this is the single-property inquire form
 * (assets/css/components/inquire-form.css) plus a row of preference
 * selects and a preferred-contact-method pair, laid out full width instead
 * of alongside the heading.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$estatein_property_types = estatein_get_property_type_options();
$estatein_budget_ranges  = estatein_property_filter_ranges( 'price' );
$estatein_room_counts    = array( '1', '2', '3', '4+' );
?>
<section class="est-section" id="estPropertyRequest">
	<div class="container">
		<div class="mb-4">
			<p class="est-eyebrow"><?php estatein_theme_icon( 'sparkle' ); ?><span class="est-eyebrow-dot"></span></p>
			<h2 class="est-section-title">Let&rsquo;s Make it Happen</h2>
			<p class="est-section-subtitle">Ready to take the first step toward your dream property? Fill out the form below, and our real estate wizards will work their magic to find your perfect match. Don&rsquo;t wait; let&rsquo;s embark on this exciting journey together.</p>
		</div>

		<form class="est-inquire-form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
			<?php wp_nonce_field( 'estatein_submit_property_request', 'estatein_property_request_nonce' ); ?>
			<input type="hidden" name="action" value="estatein_submit_property_request" />

			<div class="row g-4">
				<div class="col-md-3">
					<label class="form-label" for="estReqFirstName">First Name</label>
					<input type="text" class="form-control" id="estReqFirstName" name="first_name" placeholder="Enter First Name" required />
				</div>
				<div class="col-md-3">
					<label class="form-label" for="estReqLastName">Last Name</label>
					<input type="text" class="form-control" id="estReqLastName" name="last_name" placeholder="Enter Last Name" required />
				</div>
				<div class="col-md-3">
					<label class="form-label" for="estReqEmail">Email</label>
					<input type="email" class="form-control" id="estReqEmail" name="email" placeholder="Enter your Email" required />
				</div>
				<div class="col-md-3">
					<label class="form-label" for="estReqPhone">Phone</label>
					<input type="tel" class="form-control" id="estReqPhone" name="phone" placeholder="Enter Phone Number" />
				</div>

				<div class="col-md-3">
					<label class="form-label" for="estReqLocation">Preferred Location</label>
					<?php
					wp_dropdown_pages( array(
						'post_type'         => 'location',
						'name'              => 'preferred_location_id',
						'id'                => 'estReqLocation',
						'class'             => 'form-select',
						'show_option_none'  => 'Select Location',
						'option_none_value' => 0,
						'sort_column'       => 'menu_order, post_title',
					) );
					?>
				</div>
				<div class="col-md-3">
					<label class="form-label" for="estReqPropertyType">Property Type</label>
					<select class="form-select" id="estReqPropertyType" name="property_type">
						<option value="">Select Property Type</option>
						<?php foreach ( $estatein_property_types as $estatein_type ) : ?>
							<option value="<?php echo esc_attr( $estatein_type ); ?>"><?php echo esc_html( $estatein_type ); ?></option>
						<?php endforeach; ?>
					</select>
				</div>
				<div class="col-md-3">
					<label class="form-label" for="estReqBathrooms">No. of Bathrooms</label>
					<select class="form-select" id="estReqBathrooms" name="bathrooms">
						<option value="">Select no. of Bathrooms</option>
						<?php foreach ( $estatein_room_counts as $estatein_count ) : ?>
							<option value="<?php echo esc_attr( $estatein_count ); ?>"><?php echo esc_html( $estatein_count ); ?></option>
						<?php endforeach; ?>
					</select>
				</div>
				<div class="col-md-3">
					<label class="form-label" for="estReqBedrooms">No. of Bedrooms</label>
					<select class="form-select" id="estReqBedrooms" name="bedrooms">
						<option value="">Select no. of Bedrooms</option>
						<?php foreach ( $estatein_room_counts as $estatein_count ) : ?>
							<option value="<?php echo esc_attr( $estatein_count ); ?>"><?php echo esc_html( $estatein_count ); ?></option>
						<?php endforeach; ?>
					</select>
				</div>

				<div class="col-md-6">
					<label class="form-label" for="estReqBudget">Budget</label>
					<select class="form-select" id="estReqBudget" name="budget">
						<option value="">Select Budget</option>
						<?php foreach ( $estatein_budget_ranges as $estatein_value => $estatein_label ) : ?>
							<option value="<?php echo esc_attr( $estatein_value ); ?>"><?php echo esc_html( $estatein_label ); ?></option>
						<?php endforeach; ?>
					</select>
				</div>
				<div class="col-md-6">
					<label class="form-label">Preferred Contact Method</label>
					<div class="row g-3">
						<div class="col-6">
							<label class="est-contact-method-pill">
								<input type="radio" name="contact_method" value="phone" checked />
								<span class="est-contact-method-icon"><?php estatein_theme_icon( 'phone' ); ?></span>
								<span class="est-contact-method-text">Enter Your Number</span>
								<span class="est-contact-method-dot" aria-hidden="true"></span>
							</label>
						</div>
						<div class="col-6">
							<label class="est-contact-method-pill">
								<input type="radio" name="contact_method" value="email" />
								<span class="est-contact-method-icon"><?php estatein_theme_icon( 'mail' ); ?></span>
								<span class="est-contact-method-text">Enter Your Email</span>
								<span class="est-contact-method-dot" aria-hidden="true"></span>
							</label>
						</div>
					</div>
				</div>

				<div class="col-12">
					<label class="form-label" for="estReqMessage">Message</label>
					<textarea class="form-control" id="estReqMessage" name="message" rows="4" placeholder="Enter your Message here"></textarea>
				</div>

				<div class="col-12 d-flex flex-wrap align-items-center justify-content-between gap-3">
					<div class="form-check mb-0">
						<input class="form-check-input" type="checkbox" id="estReqAgreeTerms" required />
						<label class="form-check-label small" for="estReqAgreeTerms">I agree with <a href="#">Terms of Use</a> and <a href="#">Privacy Policy</a></label>
					</div>
					<button type="submit" class="btn btn-primary est-btn-primary"><?php estatein_theme_icon( 'send' ); ?> Send Your Message</button>
				</div>
			</div>
		</form>
	</div>
</section>
