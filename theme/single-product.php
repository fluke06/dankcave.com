<?php
/**
 * Single product page.
 *
 * Custom layout that mirrors design/Dankcave - Product.dc.html:
 * two-column body (sticky gallery + summary), reviews band, related grid.
 *
 * We still call woocommerce_template_single_add_to_cart() so third-party
 * integrations (Sezzle, Authorize.net add-ons) that hook
 * woocommerce_before_add_to_cart_button / after_add_to_cart_button keep working.
 *
 * @package Dankcave
 */

if ( ! function_exists( 'WC' ) ) {
	get_header();
	echo '<main class="wrap"><p>' . esc_html__( 'Product unavailable.', 'dankcave' ) . '</p></main>';
	get_footer();
	return;
}

get_header();

while ( have_posts() ) : the_post();
	$product = wc_get_product( get_the_ID() );
	if ( ! $product ) { continue; }
	$GLOBALS['product'] = $product;
	?>

	<div class="pdp">
		<div class="pdp__crumbs">
			<?php get_template_part( 'template-parts/shop/breadcrumb' ); ?>
		</div>

		<div class="pdp__body">
			<?php
			get_template_part( 'template-parts/single-product/gallery', null, array( 'product' => $product ) );
			get_template_part( 'template-parts/single-product/summary', null, array( 'product' => $product ) );
			?>
		</div>

		<?php get_template_part( 'template-parts/single-product/reviews', null, array( 'product' => $product ) ); ?>
		<?php get_template_part( 'template-parts/single-product/related', null, array( 'product' => $product ) ); ?>
	</div>

<?php endwhile;

get_footer();
