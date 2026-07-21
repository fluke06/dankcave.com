<?php
/**
 * Home section — "Pick your poison". A 4-up product grid pulling the featured
 * vaporizers (or falling back to demo cards if the shop is empty).
 *
 * @package Dankcave
 */

$heading    = get_theme_mod( 'dankcave_pyp_heading',    'Pick your poison' );
$link_label = get_theme_mod( 'dankcave_pyp_link_label', 'All vaporizers →' );
$link_url   = get_theme_mod( 'dankcave_pyp_link_url',   home_url( '/shop/' ) );

// Query featured products first, fall back to any products if none featured.
$products = array();
if ( function_exists( 'wc_get_products' ) ) {
	$products = wc_get_products( array(
		'featured' => true,
		'limit'    => 4,
		'status'   => 'publish',
	) );
	if ( count( $products ) < 4 ) {
		$products = wc_get_products( array(
			'limit'   => 4,
			'status'  => 'publish',
			'orderby' => 'date',
			'order'   => 'DESC',
		) );
	}
}

// Demo cards if the shop is empty. Uses images shipped in design/uploads so we
// can preview the section without needing seeded products.
$demo_cards = array(
	array( 'title' => 'E-Vape One — Fuchsia',    'category' => 'The loud one',   'price' => '$199', 'image' => DANKCAVE_URI . 'assets/images/hero-product-placeholder.png', 'rating' => 5, 'reviews' => 98 ),
	array( 'title' => 'E-Vape Kit — Deep Blue',  'category' => 'After hours',    'price' => '$219', 'image' => DANKCAVE_URI . 'assets/images/hero-product-placeholder.png', 'rating' => 5, 'reviews' => 64, 'badge' => 'KIT' ),
	array( 'title' => 'E-Vape One — Oxblood',    'category' => 'The classic',    'price' => '$199', 'image' => DANKCAVE_URI . 'assets/images/hero-product-placeholder.png', 'rating' => 4, 'reviews' => 41 ),
	array( 'title' => 'E-Vape One — Seafoam',    'category' => 'Easy days',      'price' => '$199', 'image' => DANKCAVE_URI . 'assets/images/hero-product-placeholder.png', 'rating' => 5, 'reviews' => 77 ),
);
?>
<section class="section section--cream home-pyp">
	<div class="wrap">
		<div class="section-head">
			<h2 class="section-head__title"><?php echo esc_html( $heading ); ?></h2>
			<a class="section-head__link" href="<?php echo esc_url( $link_url ); ?>"><?php echo esc_html( $link_label ); ?></a>
		</div>
		<div class="product-grid product-grid--4">
			<?php if ( ! empty( $products ) ) : ?>
				<?php foreach ( $products as $product ) : ?>
					<?php get_template_part( 'template-parts/product/card', null, array( 'product' => $product ) ); ?>
				<?php endforeach; ?>
			<?php else : ?>
				<?php foreach ( $demo_cards as $demo ) : ?>
					<?php get_template_part( 'template-parts/product/card', null, array( 'demo' => $demo, 'badge' => $demo['badge'] ?? '' ) ); ?>
				<?php endforeach; ?>
			<?php endif; ?>
		</div>
	</div>
</section>
