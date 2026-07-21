<?php
/**
 * "Pairs well with" 4-up related grid. Uses WooCommerce's related-products
 * query (same category/tags, excludes current). Falls back to newest products.
 *
 * @package Dankcave
 */

$args    = wp_parse_args( $args ?? array(), array( 'product' => null ) );
$product = $args['product'] ?: ( $GLOBALS['product'] ?? null );
if ( ! $product ) { return; }

$related_ids = wc_get_related_products( $product->get_id(), 4 );

if ( empty( $related_ids ) ) {
	$fallback = wc_get_products( array(
		'limit'   => 4,
		'status'  => 'publish',
		'orderby' => 'date',
		'order'   => 'DESC',
		'exclude' => array( $product->get_id() ),
	) );
	$related_ids = array_map( function ( $p ) { return $p->get_id(); }, $fallback );
}

if ( empty( $related_ids ) ) {
	return;
}
?>
<section class="pdp-related">
	<div class="pdp-related__head">
		<h2 class="pdp-related__title"><?php esc_html_e( 'Pairs well with', 'dankcave' ); ?></h2>
		<a class="pdp-related__link" href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>"><?php esc_html_e( 'Shop all →', 'dankcave' ); ?></a>
	</div>

	<div class="product-grid product-grid--4">
		<?php foreach ( $related_ids as $rid ) :
			$related = wc_get_product( $rid );
			if ( ! $related ) { continue; }
			get_template_part( 'template-parts/product/card', null, array( 'product' => $related ) );
		endforeach; ?>
	</div>
</section>
