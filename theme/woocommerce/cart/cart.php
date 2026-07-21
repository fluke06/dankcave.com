<?php
/**
 * Cart page — custom override to match design/Dankcave - Cart.dc.html.
 *
 * Two-column layout: cart items on the left, sticky order summary on the
 * right. Preserves the DOM contract WooCommerce needs for cart mutations
 * (form action, remove links with nonces, input names, cart_item class).
 *
 * @package Dankcave
 */

defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_before_cart' ); ?>

<div class="dc-cart">

	<header class="dc-cart__header">
		<h1 class="dc-cart__title"><?php esc_html_e( 'Your bag', 'dankcave' ); ?></h1>
		<?php
		$cart      = WC()->cart;
		$threshold = (float) get_theme_mod( 'dankcave_free_ship_threshold', 50 );
		$subtotal  = (float) $cart->get_subtotal();
		if ( $subtotal <= 0 ) {
			$blurb = __( 'Your bag is empty.', 'dankcave' );
		} elseif ( $subtotal < $threshold ) {
			$remaining = $threshold - $subtotal;
			$blurb     = sprintf( __( 'Add %s more for free shipping.', 'dankcave' ), wc_price( $remaining ) );
		} else {
			$blurb = __( 'You have unlocked free shipping.', 'dankcave' );
		}
		?>
		<div class="dc-cart__blurb"><?php echo wp_kses_post( $blurb ); ?></div>
	</header>

	<form class="woocommerce-cart-form dc-cart__form" action="<?php echo esc_url( wc_get_cart_url() ); ?>" method="post">
		<?php do_action( 'woocommerce_before_cart_table' ); ?>

		<div class="dc-cart__grid">
			<div class="dc-cart__items">
				<?php do_action( 'woocommerce_before_cart_contents' ); ?>

				<?php
				foreach ( $cart->get_cart() as $cart_item_key => $cart_item ) :
					$_product   = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
					$product_id = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );

					if ( ! ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_cart_item_visible', true, $cart_item, $cart_item_key ) ) ) {
						continue;
					}

					$product_permalink = apply_filters( 'woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink( $cart_item ) : '', $cart_item, $cart_item_key );

					// Deterministic pastel well behind the item thumb.
					$pastels = array( '#f7e0ea', '#e2e6f1', '#efdcd8', '#d9ede6', '#efe7dd', '#f3e3d0', '#eee9e0', '#f1e6d6' );
					$well_bg = $pastels[ abs( crc32( (string) $product_id ) ) % count( $pastels ) ];

					$thumbnail  = apply_filters( 'woocommerce_cart_item_thumbnail', $_product->get_image( 'woocommerce_thumbnail' ), $cart_item, $cart_item_key );
					$name       = apply_filters( 'woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key );
					$price      = apply_filters( 'woocommerce_cart_item_price', WC()->cart->get_product_price( $_product ), $cart_item, $cart_item_key );
					$attrs      = wc_get_formatted_cart_item_data( $cart_item );

					?>
					<div class="dc-cart-line woocommerce-cart-form__cart-item cart_item <?php echo esc_attr( apply_filters( 'woocommerce_cart_item_class', '', $cart_item, $cart_item_key ) ); ?>">
						<a class="dc-cart-line__thumb" href="<?php echo esc_url( $product_permalink ); ?>" style="background: <?php echo esc_attr( $well_bg ); ?>;">
							<?php echo $thumbnail; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						</a>

						<div class="dc-cart-line__info">
							<div class="dc-cart-line__name">
								<?php if ( $product_permalink ) : ?>
									<a href="<?php echo esc_url( $product_permalink ); ?>"><?php echo wp_kses_post( $name ); ?></a>
								<?php else : ?>
									<?php echo wp_kses_post( $name ); ?>
								<?php endif; ?>
							</div>
							<?php if ( $attrs ) : ?>
								<div class="dc-cart-line__attrs"><?php echo wp_kses_post( $attrs ); ?></div>
							<?php endif; ?>
							<div class="dc-cart-line__price"><?php echo wp_kses_post( $price ); ?></div>
						</div>

						<div class="dc-cart-line__side">
							<div class="dc-cart-line__qty">
								<?php
								if ( $_product->is_sold_individually() ) {
									$product_quantity = sprintf( '1 <input type="hidden" name="cart[%s][qty]" value="1" />', $cart_item_key );
								} else {
									$product_quantity = woocommerce_quantity_input( array(
										'input_name'   => "cart[{$cart_item_key}][qty]",
										'input_value'  => $cart_item['quantity'],
										'max_value'    => $_product->get_max_purchase_quantity(),
										'min_value'    => '0',
										'product_name' => $_product->get_name(),
									), $_product, false );
								}
								echo apply_filters( 'woocommerce_cart_item_quantity', $product_quantity, $cart_item_key, $cart_item ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
								?>
							</div>
							<?php
							echo apply_filters( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
								'woocommerce_cart_item_remove_link',
								sprintf(
									'<a href="%s" class="dc-cart-line__remove remove" aria-label="%s" data-product_id="%s" data-product_sku="%s">%s</a>',
									esc_url( wc_get_cart_remove_url( $cart_item_key ) ),
									esc_attr( sprintf( __( 'Remove %s from cart', 'dankcave' ), wp_strip_all_tags( $name ) ) ),
									esc_attr( $product_id ),
									esc_attr( $_product->get_sku() ),
									esc_html__( 'Remove', 'dankcave' )
								),
								$cart_item_key
							);
							?>
						</div>
					</div>
				<?php endforeach; ?>

				<?php do_action( 'woocommerce_cart_contents' ); ?>

				<?php if ( 0 === count( $cart->get_cart() ) ) : ?>
					<div class="dc-cart-empty">
						<div class="dc-cart-empty__title"><?php esc_html_e( 'Your bag is empty', 'dankcave' ); ?></div>
						<a class="dc-cart-empty__link" href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>"><?php esc_html_e( 'Back to shop →', 'dankcave' ); ?></a>
					</div>
				<?php endif; ?>

				<div class="dc-cart__actions-row">
					<a class="dc-cart__continue" href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>"><?php esc_html_e( '← Continue shopping', 'dankcave' ); ?></a>

					<div class="dc-cart__update-wrap">
						<?php if ( wc_coupons_enabled() ) { ?>
							<div class="dc-cart__coupon">
								<label for="coupon_code" class="screen-reader-text"><?php esc_html_e( 'Coupon:', 'dankcave' ); ?></label>
								<input type="text" name="coupon_code" class="input-text dc-cart__coupon-input" id="coupon_code" value="" placeholder="<?php esc_attr_e( 'Discount code', 'dankcave' ); ?>" />
								<button type="submit" class="dc-cart__coupon-apply" name="apply_coupon" value="<?php esc_attr_e( 'Apply', 'dankcave' ); ?>"><?php esc_html_e( 'Apply', 'dankcave' ); ?></button>
							</div>
						<?php } ?>
						<button type="submit" class="dc-cart__update button" name="update_cart" value="<?php esc_attr_e( 'Update cart', 'dankcave' ); ?>"><?php esc_html_e( 'Update cart', 'dankcave' ); ?></button>
						<?php do_action( 'woocommerce_cart_actions' ); ?>
						<?php wp_nonce_field( 'woocommerce-cart', 'woocommerce-cart-nonce' ); ?>
					</div>
				</div>

				<?php do_action( 'woocommerce_after_cart_contents' ); ?>
			</div>

			<aside class="dc-cart__summary" aria-label="<?php esc_attr_e( 'Order summary', 'dankcave' ); ?>">
				<?php woocommerce_cart_totals(); ?>
			</aside>
		</div>

		<?php do_action( 'woocommerce_after_cart_table' ); ?>
	</form>

	<?php do_action( 'woocommerce_before_cart_collaterals' ); ?>
</div>

<?php do_action( 'woocommerce_after_cart' ); ?>
