<?php
/**
 * Home section — "Popular & trending". 4-up product grid ordered by best-seller
 * (falls back to most-reviewed, then to newest).
 *
 * @package Dankcave
 */

$heading    = get_theme_mod( 'dankcave_pt_heading',    'Popular & trending' );
$link_label = get_theme_mod( 'dankcave_pt_link_label', 'Shop all →' );
$link_url   = get_theme_mod( 'dankcave_pt_link_url',   home_url( '/shop/' ) );

$products = array();
if ( function_exists( 'wc_get_products' ) ) {
	$products = wc_get_products( array(
		'limit'    => 4,
		'status'   => 'publish',
		'orderby'  => 'meta_value_num',
		'meta_key' => 'total_sales',
		'order'    => 'DESC',
	) );
	if ( count( $products ) < 4 ) {
		// Fallback: most-reviewed
		$products = wc_get_products( array(
			'limit'    => 4,
			'status'   => 'publish',
			'orderby'  => 'meta_value_num',
			'meta_key' => '_wc_review_count',
			'order'    => 'DESC',
		) );
	}
	if ( count( $products ) < 4 ) {
		$products = wc_get_products( array(
			'limit'   => 4,
			'status'  => 'publish',
			'orderby' => 'date',
			'order'   => 'DESC',
		) );
	}
}

$demo_cards = array(
	array( 'title' => 'Lookah Unicorn E-Rig',    'category' => 'Dab rig',    'price' => '$189', 'image' => DANKCAVE_URI . 'assets/images/hero-product-placeholder.png', 'rating' => 5, 'reviews' => 212 ),
	array( 'title' => 'Connect Glass Dab Rig',   'category' => 'Dab rig',    'price' => '$79',  'image' => DANKCAVE_URI . 'assets/images/hero-product-placeholder.png', 'rating' => 4, 'reviews' => 58 ),
	array( 'title' => 'Glycerin Beaker Bong',    'category' => 'Bong',       'price' => '$89',  'image' => DANKCAVE_URI . 'assets/images/hero-product-placeholder.png', 'rating' => 5, 'reviews' => 133 ),
	array( 'title' => 'Multivariant E-Vape',     'category' => 'Vaporizer',  'price' => '$199', 'image' => DANKCAVE_URI . 'assets/images/hero-product-placeholder.png', 'rating' => 5, 'reviews' => 98 ),
);
?>
<section class="section section--cream home-popular">
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
					<?php get_template_part( 'template-parts/product/card', null, array( 'demo' => $demo ) ); ?>
				<?php endforeach; ?>
			<?php endif; ?>
		</div>
	</div>
</section>
