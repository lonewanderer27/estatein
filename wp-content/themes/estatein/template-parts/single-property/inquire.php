<?php
/**
 * Inquire About [Property] — heading/copy on the left, a Bootstrap form
 * card on the right. Submits to admin-post.php
 * (estatein_handle_inquiry_submission() in the custom-post-types plugin).
 * Expects $args['property'].
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$estatein_property = $args['property'];
$estatein_id       = $estatein_property->ID;
$estatein_location = estatein_theme_location_label( $estatein_id );
?>
<section class="est-section" id="estInquire">
	<div class="container">
		<div class="row g-4">
			<div class="col-lg-4">
				<p class="est-eyebrow"><?php estatein_theme_icon( 'sparkle' ); ?><span class="est-eyebrow-dot"></span></p>
				<h2 class="est-section-title">Inquire About <?php echo esc_html( $estatein_property->post_title ); ?></h2>
				<p class="est-section-subtitle">Interested in this property? Fill out the form below, and our real estate experts will get back to you with more details and scheduling a viewing and answering any questions you may have.</p>
			</div>

			<div class="col-lg-8">
				<form class="est-inquire-form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
					<?php wp_nonce_field( 'estatein_submit_inquiry', 'estatein_inquiry_nonce' ); ?>
					<input type="hidden" name="action" value="estatein_submit_inquiry" />
					<input type="hidden" name="property_id" value="<?php echo esc_attr( $estatein_id ); ?>" />

					<div class="row g-3">
						<div class="col-md-6">
							<label class="form-label" for="estFirstName">First Name</label>
							<input type="text" class="form-control" id="estFirstName" name="first_name" placeholder="Enter First Name" required />
						</div>
						<div class="col-md-6">
							<label class="form-label" for="estLastName">Last Name</label>
							<input type="text" class="form-control" id="estLastName" name="last_name" placeholder="Enter Last Name" required />
						</div>
						<div class="col-md-6">
							<label class="form-label" for="estEmail">Email</label>
							<input type="email" class="form-control" id="estEmail" name="email" placeholder="Enter your Email" required />
						</div>
						<div class="col-md-6">
							<label class="form-label" for="estPhone">Phone</label>
							<input type="tel" class="form-control" id="estPhone" name="phone" placeholder="Enter Phone Number" />
						</div>
						<div class="col-12">
							<label class="form-label">Selected Property</label>
							<div class="est-selected-property d-flex align-items-center justify-content-between">
								<span><?php echo esc_html( $estatein_property->post_title ); ?><?php echo $estatein_location ? ', ' . esc_html( $estatein_location ) : ''; ?></span>
								<?php estatein_theme_icon( 'pin' ); ?>
							</div>
						</div>
						<div class="col-12">
							<label class="form-label" for="estMessage">Message</label>
							<textarea class="form-control" id="estMessage" name="message" rows="4" placeholder="Enter your Message here"></textarea>
						</div>
						<div class="col-12">
							<div class="form-check">
								<input class="form-check-input" type="checkbox" id="estAgreeTerms" required />
								<label class="form-check-label small" for="estAgreeTerms">I agree with <a href="#">Terms of Use</a> and <a href="#">Privacy Policy</a></label>
							</div>
						</div>
						<div class="col-12 text-end">
							<button type="submit" class="btn btn-primary est-btn-primary"><?php estatein_theme_icon( 'send' ); ?> Send Your Message</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</section>
