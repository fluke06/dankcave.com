<?php
/**
 * Reusable product card partial. Renders a single WooCommerce product tile with
 * pastel-tinted image well, eyebrow, title, star rating, price, and Add CTA.
 *
 * The card body is a stretched link — clicking anywhere on image/title/price
 * navigates to the product page. The "Add" button is a separate link/button
 * that adds simple products straight to the cart (via ?add-to-cart=ID) or,
 * for variable/grouped/external products, sends the user to the product page
 * for variation selection. This mirrors the WooCommerce default archive
 * behaviour while keeping our editorial card layout.
 *
 * Args (passed via get_template_part or set_query_var):
 *   product     - WC_Product instance (defaults to global $product)
 *   eyebrow     - override eyebrow text (defaults to primary category name)
 *   badge       - optional badge string ("NEW", "KIT", "SALE") shown top-left
 *   demo        - optional array of demo data (title, image, price, category)
 *                 rendered when no real product is passed — used when the shop
 *                 is still empty and we want the section to look complete.
 *
 * @package Dankcave
 */

$args    = wp_parse_args( $args ?? array(), array( 'product' => null, 'eyebrow' => '', 'badge' => '', 'demo' => null ) );
$product = $args['product'] ?: ( function_exists( 'wc_get_product' ) && ! empty( $GLOBALS['product'] ) ? $GLOBALS['product'] : null );
$demo    = $args['demo'];

if ( ! $product && ! $demo ) {
	return;
}

// Data extraction (real product) or fallback (demo)
if ( $product ) {
	$pid       = $product->get_id();
	$permalink = $product->get_permalink();
	$title     = $product->get_name();
	$price_html = $product->get_price_html();
	$image_url = wp_get_attachment_image_url( $product->get_image_id(), 'dc-product-card' );
	if ( ! $image_url ) {
		$image_url = wc_placeholder_img_src( 'dc-product-card' );
	}
	$avg_rating   = (float) $product->get_average_rating();
	$review_count = (int) $product->get_review_count();

	// "Add" behaviour depends on product type.
	// WC returns the right add-to-cart URL for simple / variable / grouped / external.
	$add_url = $product->add_to_cart_url();
	// Only simple, in-stock products get the fast "Add +" affordance.
	// Everything else routes the customer to the product page for variation selection.
	$is_ajax_addable = ( 'simple' === $product->get_type() ) && $product->is_purchasable() && $product->is_in_stock();
	$add_label = $is_ajax_addable ? __( 'Add +', 'dankcave' ) : __( 'Options →', 'dankcave' );

	// Eyebrow: prefer arg, then post meta, then primary category name
	$eyebrow = $args['eyebrow'];
	if ( ! $eyebrow ) {
		$eyebrow = get_post_meta( $pid, '_dankcave_card_eyebrow', true );
	}
	if ( ! $eyebrow ) {
		$terms = get_the_terms( $pid, 'product_cat' );
		if ( $terms && ! is_wp_error( $terms ) ) {
			$eyebrow = $terms[0]->name;
		}
	}
} else {
	// Demo card data
	$pid          = 0;
	$permalink    = '#';
	$title        = $demo['title']    ?? '';
	$price_html   = $demo['price']    ?? '';
	$image_url    = $demo['image']    ?? '';
	$avg_rating   = $demo['rating']   ?? 5;
	$review_count = $demo['reviews']  ?? 0;
	$eyebrow      = $args['eyebrow'] ?: ( $demo['category'] ?? '' );
	$add_url      = '#';
	$add_label    = __( 'Add', 'dankcave' );
	$is_ajax_addable = false;
}

$badge = $args['badge'] ?: ( $demo['badge'] ?? '' );

$add_classes = 'product-card__add' . ( $is_ajax_addable ? ' add_to_cart_button ajax_add_to_cart' : '' );
?>
<article class="product-card">
	<?php if ( $badge ) : ?>
		<span class="product-card__badge"><?php echo esc_html( $badge ); ?></span>
	<?php endif; ?>

	<a class="product-card__link" href="<?php echo esc_url( $permalink ); ?>" aria-label="<?php echo esc_attr( $title ); ?>">
		<div class="product-card__well">
			<?php if ( $image_url ) : ?>
				<img src="<?php echo esc_url( $image_url ); ?>" alt="" loading="lazy">
			<?php endif; ?>
		</div>
		<div class="product-card__body">
			<?php if ( $eyebrow ) : ?>
				<div class="product-card__eyebrow"><?php echo esc_html( strtoupper( $eyebrow ) ); ?></div>
			<?php endif; ?>
			<div class="product-card__title"><?php echo esc_html( $title ); ?></div>
			<?php if ( $review_count > 0 || ! $product ) : ?>
				<div class="product-card__rating" aria-label="<?php echo esc_attr( sprintf( __( '%s stars, %d reviews', 'dankcave' ), $avg_rating, $review_count ) ); ?>">
					<span class="product-card__stars" aria-hidden="true"><?php
						$full = (int) floor( $avg_rating );
						echo str_repeat( '★', $full ) . str_repeat( '☆', 5 - $full );
					?></span>
					<span class="product-card__reviews">(<?php echo esc_html( $review_count ); ?>)</span>
				</div>
			<?php endif; ?>
		</div>
	</a>

	<div class="product-card__foot">
		<span class="product-card__price"><?php echo wp_kses_post( $price_html ); ?></span>
		<?php if ( $product ) : ?>
			<a class="<?php echo esc_attr( $add_classes ); ?>"
				href="<?php echo esc_url( $add_url ); ?>"
				data-product_id="<?php echo esc_attr( $pid ); ?>"
				data-product_sku="<?php echo esc_attr( $product->get_sku() ); ?>"
				data-quantity="1"
				aria-label="<?php echo esc_attr( sprintf( __( 'Add %s to cart', 'dankcave' ), $title ) ); ?>"
				rel="nofollow">
				<?php echo esc_html( $add_label ); ?>
			</a>
		<?php else : ?>
			<span class="product-card__add"><?php esc_html_e( 'Add +', 'dankcave' ); ?></span>
		<?php endif; ?>
	</div>
</article>
