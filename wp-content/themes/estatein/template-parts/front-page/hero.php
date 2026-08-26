<?php
/**
 * Hero section. Headline/subtitle/stats are placeholder copy for this
 * draft — none of it maps to an existing post type or setting yet.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$estatein_properties_url = get_post_type_archive_link( 'property' );
?>
<section class="est-hero" id="estHero">
	<div class="est-hero-media">
		<div class="est-hero-media-inner">
			<div class="est-hero-badge">
				<svg class="est-hero-badge-ring" viewBox="0 0 200 200" aria-hidden="true">
					<defs>
						<path id="estHeroBadgePath" d="M100,100 m0,-84 a84,84 0 1 1 -0.01,0 z" />
					</defs>
					<circle class="est-hero-badge-circle est-hero-badge-circle-outer" cx="100" cy="100" r="97" />
					<circle class="est-hero-badge-circle est-hero-badge-circle-inner" cx="100" cy="100" r="72" />
					<text class="est-hero-badge-text">
						<textPath href="#estHeroBadgePath" startOffset="0%">DISCOVER YOUR DREAM PROPERTY <tspan class="est-hero-badge-star">&#10022;</tspan> </textPath>
					</text>
				</svg>
				<span class="est-hero-badge-center">
					<?php estatein_theme_icon( 'arrow-up-right' ); ?>
				</span>
			</div>
			<div class="est-hero-image" role="img" aria-label="Modern residential towers"></div>
		</div>
	</div>

	<div class="container">
		<div class="row align-items-center gy-5">
			<div class="col-lg-6 est-hero-content">
				<h1 class="est-hero-title"><?php bloginfo( 'description' ); ?></h1>
				<p class="est-hero-subtitle">Your journey to finding the perfect property begins here. Explore our listings to find the home that matches your dreams.</p>

				<div class="d-grid gap-3 d-sm-flex flex-wrap mb-5">
					<a href="#estHighlights" class="btn est-btn-outline">Learn More</a>
					<a href="<?php echo esc_url( $estatein_properties_url ? $estatein_properties_url : '#' ); ?>" class="btn btn-primary est-btn-primary">Browse Properties</a>
				</div>

				<div class="row est-stats g-2 g-lg-3">
					<div class="col-4">
						<div class="est-stat">
							<span class="est-stat-number">200+</span>
							<span class="est-stat-label">Happy Customers</span>
						</div>
					</div>
					<div class="col-4">
						<div class="est-stat">
							<span class="est-stat-number">10k+</span>
							<span class="est-stat-label">Properties For Clients</span>
						</div>
					</div>
					<div class="col-4">
						<div class="est-stat">
							<span class="est-stat-number">16+</span>
							<span class="est-stat-label">Years of Experience</span>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
