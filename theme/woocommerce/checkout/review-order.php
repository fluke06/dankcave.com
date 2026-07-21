<?php
/**
 * Order review sidebar on checkout. Mini product list + totals + Place Order.
 *
 * @package Dankcave
 */

defined( 'ABSPATH' ) || exit;
?>

<div class="dc-review">
	<ul class="dc-review__items woocommerce-checkout-review-order-table__products">
		<?php
		do_action( 'woocommerce_review_order_before_cart_contents' );

		foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) :
			$_product   = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
			$product_id = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );

			if ( ! ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_checkout_cart_item_visible', true, $cart_item, $cart_item_key ) ) ) {
				continue;
			}

			$pastels = array( '#f7e0ea', '#e2e6f1', '#efdcd8', '#d9ede6', '#efe7dd', '#f3e3d0', '#eee9e0', '#f1e6d6' );
			$well_bg = $pastels[ abs( crc32( (string) $product_id ) ) % count( $pastels ) ];

			$attrs = wc_get_formatted_cart_item_data( $cart_item );
			?>
			<li class="dc-review-item <?php echo esc_attr( apply_filters( 'woocommerce_cart_item_class', 'cart_item', $cart_item, $cart_item_key ) ); ?>">
				<div class="dc-review-item__thumb" style="background: <?php echo esc_attr( $well_bg ); ?>;">
					<?php echo $_product->get_image( 'woocommerce_thumbnail' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					<span class="dc-review-item__qty"><?php echo esc_html( $cart_item['quantity'] ); ?></span>
				</div>
				<div class="dc-review-item__info">
					<div class="dc-review-item__name"><?php echo wp_kses_post( apply_filters( 'woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key ) ); ?></div>
					<?php if ( $attrs ) : ?>
						<div class="dc-review-item__attrs"><?php echo wp_kses_post( $attrs ); ?></div>
					<?php else : ?>
						<div class="dc-review-item__attrs"><?php echo esc_html( sprintf( __( 'Qty %d', 'dankcave' ), $cart_item['quantity'] ) ); ?></div>
					<?php endif; ?>
				</div>
				<div class="dc-review-item__price">
					<?php echo apply_filters( 'woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal( $_product, $cart_item['quantity'] ), $cart_item, $cart_item_key ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</div>
			</li>
			<?php
		endforeach;

		do_action( 'woocommerce_review_order_after_cart_contents' );
		?>
	</ul>

	<div class="dc-review__totals">
		<div class="dc-review__row cart-subtotal">
			<span><?php esc_html_e( 'Subtotal', 'dankcave' ); ?></span>
			<span class="dc-review__val"><?php wc_cart_totals_subtotal_html(); ?></span>
		</div>

		<?php foreach ( WC()->cart->get_coupons() as $code => $coupon ) : ?>
			<div class="dc-review__row cart-discount coupon-<?php echo esc_attr( sanitize_title( $code ) ); ?>">
				<span><?php wc_cart_totals_coupon_label( $coupon ); ?></span>
				<span class="dc-review__val"><?php wc_cart_totals_coupon_html( $coupon ); ?></span>
			</div>
		<?php endforeach; ?>

		<?php
		/**
		 * Shipping method is rendered as its own labelled card in the customer
		 * details column above (dankcave_checkout_shipping_method_card). Keep
		 * the action hooks so payment gateways / analytics still fire, but
		 * suppress the inline table row here so the summary card doesn't show
		 * a duplicate radio list between subtotal and total.
		 */
		if ( WC()->cart->needs_shipping() && WC()->cart->show_shipping() ) :
			do_action( 'woocommerce_review_order_before_shipping' );
			do_action( 'woocommerce_review_order_after_shipping' );
			$chosen_methods = WC()->session->get( 'chosen_shipping_methods' );
			$chosen         = isset( $chosen_methods[0] ) ? $chosen_methods[0] : '';
			$rates          = WC()->shipping()->get_packages();
			$rate_label     = '';
			if ( ! empty( $rates ) && ! empty( $rates[0]['rates'][ $chosen ] ) ) {
				$rate           = $rates[0]['rates'][ $chosen ];
				$rate_label     = $rate->get_label();
				$rate_cost_html = wc_price( (float) $rate->get_cost() );
			}
		?>
			<div class="dc-review__row">
				<span><?php esc_html_e( 'Shipping', 'dankcave' ); ?></span>
				<span class="dc-review__val">
					<?php if ( $rate_label ) : ?>
						<?php echo esc_html( $rate_label ); ?> · <?php echo wp_kses_post( $rate_cost_html ); ?>
					<?php else : ?>
						<?php esc_html_e( 'Choose above', 'dankcave' ); ?>
					<?php endif; ?>
				</span>
			</div>
		<?php endif; ?>

		<?php foreach ( WC()->cart->get_fees() as $fee ) : ?>
			<div class="dc-review__row fee">
				<span><?php echo esc_html( $fee->name ); ?></span>
				<span class="dc-review__val"><?php wc_cart_totals_fee_html( $fee ); ?></span>
			</div>
		<?php endforeach; ?>

		<?php if ( wc_tax_enabled() && ! WC()->cart->display_prices_including_tax() ) : ?>
			<?php if ( 'itemized' === get_option( 'woocommerce_tax_total_display' ) ) : ?>
				<?php foreach ( WC()->cart->get_tax_totals() as $code => $tax ) : ?>
					<div class="dc-review__row tax-rate tax-rate-<?php echo esc_attr( sanitize_title( $code ) ); ?>">
						<span><?php echo esc_html( $tax->label ); ?></span>
						<span class="dc-review__val"><?php echo wp_kses_post( $tax->formatted_amount ); ?></span>
					</div>
				<?php endforeach; ?>
			<?php else : ?>
				<div class="dc-review__row tax-total">
					<span><?php echo esc_html( WC()->countries->tax_or_vat() ); ?></span>
					<span class="dc-review__val"><?php wc_cart_totals_taxes_total_html(); ?></span>
				</div>
			<?php endif; ?>
		<?php endif; ?>

		<?php do_action( 'woocommerce_review_order_before_order_total' ); ?>

		<div class="dc-review__row dc-review__row--total order-total">
			<span><?php esc_html_e( 'Total', 'dankcave' ); ?></span>
			<span class="dc-review__val"><?php wc_cart_totals_order_total_html(); ?></span>
		</div>

		<?php do_action( 'woocommerce_review_order_after_order_total' ); ?>
	</div>

	<?php
	/**
	 * Payment methods + place-order button are rendered by WooCommerce's default
	 * `checkout/payment.php` template which fires on `woocommerce_checkout_order_review`
	 * at priority 20 (right after this review-order at priority 10). Do NOT render
	 * them inline here — that produces duplicate payment method lists.
	 */
	?>

</div>
