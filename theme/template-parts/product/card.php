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

	// "Add" behaviour depends on product type + stock.
	$add_url         = $product->add_to_cart_url();
	$product_type    = $product->get_type();
	$is_ajax_addable = ( 'simple' === $product_type ) && $product->is_purchasable() && $product->is_in_stock();
	$needs_options   = in_array( $product_type, array( 'variable', 'grouped' ), true );
	if ( $is_ajax_addable ) {
		$add_label = __( 'Add +', 'dankcave' );
	} elseif ( $needs_options ) {
		$add_label = __( 'Options →', 'dankcave' );
	} elseif ( ! $product->is_in_stock() ) {
		$add_label = __( 'Sold out', 'dankcave' );
	} else {
		$add_label = __( 'View →', 'dankcave' );
	}

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

$add_classes = 'product-card__add';
if ( $is_ajax_addable ) {
	$add_classes .= ' add_to_cart_button ajax_add_to_cart';
} elseif ( $needs_options ) {
	$add_classes .= ' product-card__add--needs-options';
} elseif ( ! $product->is_in_stock() ) {
	$add_classes .= ' product-card__add--sold-out';
}
?>
<article class="product-card" data-product-id="<?php echo esc_attr( $pid ); ?>">
	<?php if ( $badge ) : ?>
		<span class="product-card__badge"><?php echo esc_html( $badge ); ?></span>
	<?php endif; ?>

	<?php if ( $product ) : ?>
		<div class="product-card__hover-actions" aria-hidden="true">
			<button type="button" class="product-card__hover-btn dc-tooltip" data-dc-wishlist data-product-id="<?php echo esc_attr( $pid ); ?>" data-tooltip="<?php esc_attr_e( 'Add to wishlist', 'dankcave' ); ?>" aria-label="<?php echo esc_attr( sprintf( __( 'Add %s to wishlist', 'dankcave' ), $title ) ); ?>">
				<svg viewBox="0 0 20 20" width="16" height="16" aria-hidden="true">
					<path fill="none" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round" d="M10 17s-6.5-4.35-6.5-8.5A3.5 3.5 0 0 1 10 6a3.5 3.5 0 0 1 6.5 2.5C16.5 12.65 10 17 10 17z"/>
				</svg>
			</button>
			<button type="button" class="product-card__hover-btn dc-tooltip" data-dc-compare data-product-id="<?php echo esc_attr( $pid ); ?>" data-tooltip="<?php esc_attr_e( 'Add to compare', 'dankcave' ); ?>" aria-label="<?php echo esc_attr( sprintf( __( 'Compare %s', 'dankcave' ), $title ) ); ?>">
				<svg viewBox="0 0 20 20" width="16" height="16" aria-hidden="true">
					<path fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" d="M4 6h9m-2-3l3 3-3 3M16 14H7m2 3l-3-3 3-3"/>
				</svg>
			</button>
			<button type="button" class="product-card__hover-btn dc-tooltip" data-dc-quickview data-product-id="<?php echo esc_attr( $pid ); ?>" data-tooltip="<?php esc_attr_e( 'Quick view', 'dankcave' ); ?>" aria-label="<?php echo esc_attr( sprintf( __( 'Quick view %s', 'dankcave' ), $title ) ); ?>">
				<svg viewBox="0 0 20 20" width="16" height="16" aria-hidden="true">
					<path fill="none" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round" d="M1.5 10s3-5.5 8.5-5.5 8.5 5.5 8.5 5.5-3 5.5-8.5 5.5S1.5 10 1.5 10z"/>
					<circle cx="10" cy="10" r="2.5" fill="none" stroke="currentColor" stroke-width="1.6"/>
				</svg>
			</button>
		</div>
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
