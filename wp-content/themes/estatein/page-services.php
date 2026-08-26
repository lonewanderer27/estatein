<?php
/**
 * Services page (slug "services" — WordPress's template hierarchy matches
 * this file automatically once a Page with that slug exists).
 *
 * TODO: the three category arrays below (items + featured) are static
 * placeholder content. Replace with a query against a `service`/
 * `service-category` CPT once the plugin adds one — the only CPT that
 * exists today is `highlight`, reused as-is by services/hero.php.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$estatein_selling = array(
	'id'       => 'estServicesSelling',
	'title'    => 'Unlock Property Value',
	'subtitle' => 'Selling your property should be a rewarding experience, and at Estatein, we make sure it is. Our Property Selling Service is designed to maximize the value of your property, ensuring you get the best deal possible. Explore the categories below to see how we can help you at every step of your selling journey.',
	'items'    => array(
		array(
			'icon'        => 'chart-bar',
			'title'       => 'Valuation Mastery',
			'description' => 'Discover the true worth of your property with our expert valuation services.',
		),
		array(
			'icon'        => 'chart-pie',
			'title'       => 'Strategic Marketing',
			'description' => 'Selling a property requires more than just a listing; it demands a strategic marketing approach.',
		),
		array(
			'icon'        => 'shuffle',
			'title'       => 'Negotiation Wizardry',
			'description' => 'Negotiating the best deal is an art, and our negotiation experts are masters of it.',
		),
		array(
			'icon'        => 'message-circle',
			'title'       => 'Closing Success',
			'description' => 'A successful sale is not complete until the closing. We guide you through the intricate closing process.',
		),
	),
	'featured' => array(
		'title'       => 'Unlock the Value of Your Property Today',
		'description' => 'Ready to unlock the true value of your property? Explore our Property Selling Service categories and let us help you achieve the best deal possible for your valuable asset.',
	),
);

$estatein_management = array(
	'id'       => 'estServicesManagement',
	'title'    => 'Effortless Property Management',
	'subtitle' => 'Owning a property should be a pleasure, not a hassle. Estatein\'s Property Management Service takes the stress out of property ownership, offering comprehensive solutions tailored to your needs. Explore the categories below to see how we can make property management effortless for you.',
	'items'    => array(
		array(
			'icon'        => 'users',
			'title'       => 'Tenant Harmony',
			'description' => 'Our Tenant Management services ensure that your tenants have a smooth experience while reducing vacancies.',
		),
		array(
			'icon'        => 'wrench',
			'title'       => 'Maintenance Ease',
			'description' => 'Say goodbye to property maintenance headaches. We handle all aspects of property upkeep.',
		),
		array(
			'icon'        => 'dollar',
			'title'       => 'Financial Peace of Mind',
			'description' => 'Managing property finances can be complex. Our financial experts take care of rent collection and reporting.',
		),
		array(
			'icon'        => 'shield',
			'title'       => 'Legal Guardian',
			'description' => 'Stay compliant with property laws and regulations effortlessly.',
		),
	),
	'featured' => array(
		'title'       => 'Experience Effortless Property Management',
		'description' => 'Ready to experience hassle-free property management? Explore our Property Management Service categories and let us handle the complexities while you enjoy the benefits of property ownership.',
	),
);

$estatein_investment = array(
	'id'       => 'estServicesInvestment',
	'title'    => 'Smart Investments, Informed Decisions',
	'subtitle' => 'Building a real estate portfolio requires a strategic approach. Estatein\'s Investment Advisory Service empowers you to make smart investments and informed decisions.',
	'variant'  => 'grid-start',
	'items'    => array(
		array(
			'icon'        => 'trend',
			'title'       => 'Market Insight',
			'description' => 'Stay ahead of market trends with our expert Market Analysis. We provide in-depth insights into real estate market conditions.',
		),
		array(
			'icon'        => 'dollar',
			'title'       => 'ROI Assessment',
			'description' => 'Make investment decisions with confidence. Our ROI Assessment services evaluate the potential returns on your investments.',
		),
		array(
			'icon'        => 'gear',
			'title'       => 'Customized Strategies',
			'description' => 'Every investor is unique, and so are their goals. We develop Customized Investment Strategies tailored to your specific needs.',
		),
		array(
			'icon'        => 'layers',
			'title'       => 'Diversification Mastery',
			'description' => 'Diversify your real estate portfolio effectively. Our experts guide you in spreading your investments across various property types and locations.',
		),
	),
	'featured' => array(
		'title'       => 'Unlock Your Investment Potential',
		'description' => 'Ready to make smarter investment decisions? Explore our Investment Advisory Service categories and let our experts guide you toward informed, confident real estate investments.',
	),
);

get_header();
get_template_part( 'template-parts/services/hero' );
get_template_part( 'template-parts/services/category-section', null, $estatein_selling );
get_template_part( 'template-parts/services/category-section', null, $estatein_management );
get_template_part( 'template-parts/services/category-section', null, $estatein_investment );
get_template_part( 'template-parts/front-page/cta' );
get_footer();
