<?php
/**
 * Home section — "New products". Newest 4 WooCommerce products, each with a
 * yellow "NEW" badge on the card.
 *
 * @package Dankcave
 */

$heading    = get_theme_mod( 'dankcave_new_heading',    'New products' );
$link_label = get_theme_mod( 'dankcave_new_link_label', 'Shop all →' );
$link_url   = get_theme_mod( 'dankcave_new_link_url',   home_url( '/shop/' ) );

$products = array();
if ( function_exists( 'wc_get_products' ) ) {
	$products = wc_get_products( array(
		'limit'   => 4,
		'status'  => 'publish',
		'orderby' => 'date',
		'order'   => 'DESC',
	) );
}

$demo_cards = array(
	array( 'title' => 'Chrome Skull Bong',        'category' => 'Bong',      'price' => '$149', 'image' => DANKCAVE_URI . 'assets/images/hero-product-placeholder.png', 'rating' => 5, 'reviews' => 24, 'badge' => 'NEW' ),
	array( 'title' => 'Beehive Nano Rig',         'category' => 'Dab rig',   'price' => '$129', 'image' => DANKCAVE_URI . 'assets/images/hero-product-placeholder.png', 'rating' => 4, 'reviews' => 12, 'badge' => 'NEW' ),
	array( 'title' => 'Dragon Sphere Mini Bong',  'category' => 'Bong',      'price' => '$95',  'image' => DANKCAVE_URI . 'assets/images/hero-product-placeholder.png', 'rating' => 5, 'reviews' => 31, 'badge' => 'NEW' ),
	array( 'title' => 'E-Vape Kit — Deep Blue',   'category' => 'Vaporizer', 'price' => '$219', 'image' => DANKCAVE_URI . 'assets/images/hero-product-placeholder.png', 'rating' => 5, 'reviews' => 64, 'badge' => 'NEW' ),
);
?>
<section class="section section--cream home-new">
	<div class="wrap">
		<div class="section-head">
			<h2 class="section-head__title"><?php echo esc_html( $heading ); ?></h2>
			<a class="section-head__link" href="<?php echo esc_url( $link_url ); ?>"><?php echo esc_html( $link_label ); ?></a>
		</div>
		<div class="product-grid product-grid--4">
			<?php if ( ! empty( $products ) ) : ?>
				<?php foreach ( $products as $product ) : ?>
					<?php get_template_part( 'template-parts/product/card', null, array( 'product' => $product, 'badge' => 'NEW' ) ); ?>
				<?php endforeach; ?>
			<?php else : ?>
				<?php foreach ( $demo_cards as $demo ) : ?>
					<?php get_template_part( 'template-parts/product/card', null, array( 'demo' => $demo, 'badge' => $demo['badge'] ) ); ?>
				<?php endforeach; ?>
			<?php endif; ?>
		</div>
	</div>
</section>
