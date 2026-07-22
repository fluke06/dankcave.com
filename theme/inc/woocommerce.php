<?php
/**
 * WooCommerce-specific hooks and template adjustments.
 * Populated as the shop templates are built.
 *
 * @package Dankcave
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WooCommerce' ) ) {
	return;
}

/**
 * Remove default WooCommerce wrappers — the theme provides its own wrap markup
 * so Woo templates render inside our layout, not Woo's default sidebar-and-wrap.
 */
remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10 );
remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10 );
remove_action( 'woocommerce_sidebar', 'woocommerce_get_sidebar', 10 );

function dankcave_woocommerce_wrapper_start() {
	echo '<main class="wc-main"><div class="wrap">';
}
function dankcave_woocommerce_wrapper_end() {
	echo '</div></main>';
}
add_action( 'woocommerce_before_main_content', 'dankcave_woocommerce_wrapper_start', 10 );
add_action( 'woocommerce_after_main_content', 'dankcave_woocommerce_wrapper_end', 10 );

/**
 * Reshape the checkout fields to match design/Dankcave - Checkout.dc.html:
 *   - Drop company_name (both billing + shipping) — not asked for in design
 *   - Move email to the TOP of billing so the card leads with Contact
 *   - Give email + phone their own row (not the tail of the address block)
 *   - Reorder so name comes first, then address, then city/state/zip, then phone
 */
add_filter( 'woocommerce_checkout_fields', 'dankcave_shape_checkout_fields', 20 );
function dankcave_shape_checkout_fields( $fields ) {
	unset( $fields['billing']['billing_company'] );
	unset( $fields['shipping']['shipping_company'] );

	// Placeholder text mirrors the design mockup so empty fields hint their content.
	$placeholders = array(
		'billing_email'      => __( 'you@email.com', 'dankcave' ),
		'billing_first_name' => __( 'Jane', 'dankcave' ),
		'billing_last_name'  => __( 'Doe', 'dankcave' ),
		'billing_address_1'  => __( '3941 Holly Drive', 'dankcave' ),
		'billing_address_2'  => __( 'Apt, suite (optional)', 'dankcave' ),
		'billing_city'       => __( 'Tracy', 'dankcave' ),
		'billing_postcode'   => __( '95304', 'dankcave' ),
		'billing_phone'      => __( '(209) 555-0142', 'dankcave' ),
		'shipping_first_name' => __( 'Jane', 'dankcave' ),
		'shipping_last_name'  => __( 'Doe', 'dankcave' ),
		'shipping_address_1'  => __( '3941 Holly Drive', 'dankcave' ),
		'shipping_address_2'  => __( 'Apt, suite (optional)', 'dankcave' ),
		'shipping_city'       => __( 'Tracy', 'dankcave' ),
		'shipping_postcode'   => __( '95304', 'dankcave' ),
		'shipping_phone'      => __( '(209) 555-0142', 'dankcave' ),
	);
	foreach ( $placeholders as $key => $ph ) {
		$section = 0 === strpos( $key, 'billing_' ) ? 'billing' : 'shipping';
		if ( isset( $fields[ $section ][ $key ] ) ) {
			$fields[ $section ][ $key ]['placeholder'] = $ph;
		}
	}

	// Email leads the Contact card, priority 4 so it's before everything else.
	if ( isset( $fields['billing']['billing_email'] ) ) {
		$fields['billing']['billing_email']['priority'] = 4;
		$fields['billing']['billing_email']['class']    = array( 'form-row-wide', 'dc-contact-email' );
		$fields['billing']['billing_email']['label']    = __( 'Email address', 'dankcave' );
	}
	// Everything else keeps design's ordering.
	$order = array(
		'billing_first_name' => 10,
		'billing_last_name'  => 20,
		'billing_country'    => 30,
		'billing_address_1'  => 40,
		'billing_address_2'  => 50,
		'billing_city'       => 60,
		'billing_state'      => 70,
		'billing_postcode'   => 80,
		'billing_phone'      => 90,
	);
	foreach ( $order as $key => $prio ) {
		if ( isset( $fields['billing'][ $key ] ) ) {
			$fields['billing'][ $key ]['priority'] = $prio;
		}
	}
	return $fields;
}

/**
 * Rename the "Billing details" card heading to "Contact & shipping" to match design.
 */
add_filter( 'gettext', 'dankcave_relabel_billing_heading', 20, 3 );
function dankcave_relabel_billing_heading( $translated, $original, $domain ) {
	if ( 'woocommerce' !== $domain ) { return $translated; }
	if ( 'Billing details' === $original )  return __( 'Contact & shipping', 'dankcave' );
	if ( 'Billing &amp; Shipping' === $original ) return __( 'Contact & shipping', 'dankcave' );
	return $translated;
}

/**
 * Replace the default "Returning customer? Click here to login" notice with a
 * subtle inline link at the top-right of the Contact card. Same for the
 * coupon toggle — surface it as a small link near the order review sidebar
 * instead of a full-width toast.
 */
remove_action( 'woocommerce_before_checkout_form', 'woocommerce_checkout_login_form', 10 );
remove_action( 'woocommerce_before_checkout_form', 'woocommerce_checkout_coupon_form', 10 );

// Log in link inline at the top of the Contact card
add_action( 'woocommerce_before_checkout_billing_form', 'dankcave_checkout_inline_login', 5 );
function dankcave_checkout_inline_login( $checkout ) {
	if ( is_user_logged_in() ) { return; }
	?>
	<div class="dc-checkout-inline-actions">
		<span class="dc-checkout-inline-actions__prompt"><?php esc_html_e( 'Have an account?', 'dankcave' ); ?></span>
		<a href="#" class="dc-checkout-inline-actions__link" data-dc-toggle-login><?php esc_html_e( 'Log in', 'dankcave' ); ?></a>
	</div>
	<div class="dc-checkout-inline-login" hidden data-dc-inline-login>
		<?php woocommerce_login_form( array( 'redirect' => wc_get_checkout_url(), 'hidden' => true ) ); ?>
	</div>
	<?php
}

// -----------------------------------------------------------------------------
// Quick-view AJAX endpoint. Returns an HTML fragment for the modal so the
// same summary component works for simple + variable products (variable-form
// JS attaches automatically via WC's variations_form init).
// -----------------------------------------------------------------------------
add_action( 'wp_ajax_dankcave_quickview',        'dankcave_ajax_quickview' );
add_action( 'wp_ajax_nopriv_dankcave_quickview', 'dankcave_ajax_quickview' );
function dankcave_ajax_quickview() {
	$product_id = isset( $_GET['product_id'] ) ? (int) $_GET['product_id'] : 0;
	$product    = $product_id ? wc_get_product( $product_id ) : null;
	if ( ! $product ) {
		wp_send_json_error( array( 'message' => 'Product not found' ), 404 );
	}
	// Set global product so WC's template functions target this product.
	$GLOBALS['product'] = $product;
	$GLOBALS['post']    = get_post( $product_id );
	setup_postdata( $GLOBALS['post'] );

	$image_id  = $product->get_image_id();
	$image_url = $image_id ? wp_get_attachment_image_url( $image_id, 'large' ) : wc_placeholder_img_src( 'large' );
	$categories = wc_get_product_category_list( $product_id, ', ' );
	$categories_text = wp_strip_all_tags( $categories );

	ob_start();
	?>
	<div class="dc-quickview__grid">
		<div class="dc-quickview__media">
			<img src="<?php echo esc_url( $image_url ); ?>" alt="<?php echo esc_attr( $product->get_name() ); ?>">
		</div>
		<div class="dc-quickview__summary">
			<?php if ( $categories_text ) : ?>
				<div class="dc-quickview__eyebrow"><?php echo esc_html( strtoupper( $categories_text ) ); ?></div>
			<?php endif; ?>
			<h2 class="dc-quickview__title"><?php echo esc_html( $product->get_name() ); ?></h2>
			<div class="dc-quickview__price"><?php echo $product->get_price_html(); // phpcs:ignore ?></div>
			<?php if ( $product->get_short_description() ) : ?>
				<div class="dc-quickview__short"><?php echo apply_filters( 'woocommerce_short_description', $product->get_short_description() ); // phpcs:ignore ?></div>
			<?php endif; ?>
			<div class="dc-quickview__cart">
				<?php woocommerce_template_single_add_to_cart(); ?>
			</div>
			<a class="dc-quickview__view-full" href="<?php echo esc_url( $product->get_permalink() ); ?>"><?php esc_html_e( 'View full details →', 'dankcave' ); ?></a>
		</div>
	</div>
	<?php
	wp_reset_postdata();
	$html = ob_get_clean();
	wp_send_json_success( array( 'html' => $html ) );
}

// Pass the ajax URL to the frontend JS
add_action( 'wp_enqueue_scripts', 'dankcave_localize_quickview', 20 );
function dankcave_localize_quickview() {
	wp_localize_script( 'dankcave', 'dcAjax', array(
		'url'   => admin_url( 'admin-ajax.php' ),
		'nonce' => wp_create_nonce( 'dc-quickview' ),
	) );
}

// -----------------------------------------------------------------------------
// Compare — minimal product-comparison endpoint. Given a list of product IDs
// (from localStorage on the frontend), return thumbs for the floating tray or
// a side-by-side attribute table for the modal.
// -----------------------------------------------------------------------------
add_action( 'wp_ajax_dankcave_compare_thumbs',        'dankcave_ajax_compare_thumbs' );
add_action( 'wp_ajax_nopriv_dankcave_compare_thumbs', 'dankcave_ajax_compare_thumbs' );
function dankcave_ajax_compare_thumbs() {
	$ids = isset( $_GET['ids'] ) ? array_map( 'intval', explode( ',', wp_unslash( $_GET['ids'] ) ) ) : array();
	$ids = array_filter( $ids );
	ob_start();
	foreach ( $ids as $pid ) {
		$product = wc_get_product( $pid );
		if ( ! $product ) { continue; }
		$img = wp_get_attachment_image_url( $product->get_image_id(), 'woocommerce_thumbnail' );
		if ( ! $img ) { $img = wc_placeholder_img_src( 'woocommerce_thumbnail' ); }
		?>
		<div class="dc-compare-tray__thumb" data-product-id="<?php echo esc_attr( $pid ); ?>" title="<?php echo esc_attr( $product->get_name() ); ?>">
			<img src="<?php echo esc_url( $img ); ?>" alt="">
			<button type="button" class="dc-compare-tray__thumb-remove" data-product-id="<?php echo esc_attr( $pid ); ?>" aria-label="<?php esc_attr_e( 'Remove from compare', 'dankcave' ); ?>">×</button>
		</div>
		<?php
	}
	wp_send_json_success( array( 'html' => ob_get_clean() ) );
}

add_action( 'wp_ajax_dankcave_compare_table',        'dankcave_ajax_compare_table' );
add_action( 'wp_ajax_nopriv_dankcave_compare_table', 'dankcave_ajax_compare_table' );
function dankcave_ajax_compare_table() {
	$ids = isset( $_GET['ids'] ) ? array_map( 'intval', explode( ',', wp_unslash( $_GET['ids'] ) ) ) : array();
	$products = array();
	foreach ( array_filter( $ids ) as $pid ) {
		$p = wc_get_product( $pid );
		if ( $p ) { $products[] = $p; }
	}
	if ( empty( $products ) ) {
		ob_start();
		?>
		<div class="dc-compare-empty">
			<div class="dc-compare-empty__icon" aria-hidden="true">
				<svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18M3 12h18M3 18h18"/></svg>
			</div>
			<h3 class="dc-compare-empty__title"><?php esc_html_e( 'Nothing to compare yet', 'dankcave' ); ?></h3>
			<p class="dc-compare-empty__text"><?php esc_html_e( 'Tap the compare icon on any product card to line them up side-by-side here.', 'dankcave' ); ?></p>
			<a class="dc-compare-empty__cta" href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>"><?php esc_html_e( 'Browse products', 'dankcave' ); ?></a>
		</div>
		<?php
		wp_send_json_success( array( 'html' => ob_get_clean() ) );
	}

	// Collect the union of visible attributes across all products so we can
	// build a consistent set of rows.
	$attr_keys = array();
	foreach ( $products as $p ) {
		foreach ( $p->get_attributes() as $slug => $attribute ) {
			if ( ! is_a( $attribute, 'WC_Product_Attribute' ) || ! $attribute->get_visible() ) { continue; }
			$attr_keys[ $slug ] = wc_attribute_label( $attribute->get_name(), $p );
		}
	}

	// Find the lowest price among in-stock products so we can flag the best value.
	$prices = array();
	foreach ( $products as $p ) {
		$prices[ $p->get_id() ] = (float) wc_get_price_to_display( $p );
	}
	$min_price = $prices ? min( $prices ) : 0;

	ob_start();
	?>
	<div class="dc-compare-table-wrap" role="region" aria-label="<?php esc_attr_e( 'Product comparison', 'dankcave' ); ?>">
		<table class="dc-compare-table" data-cols="<?php echo esc_attr( count( $products ) ); ?>">
			<thead>
				<tr>
					<th class="dc-compare-table__label" scope="col"></th>
					<?php foreach ( $products as $p ) : ?>
						<th class="dc-compare-table__head" scope="col" data-product-id="<?php echo esc_attr( $p->get_id() ); ?>">
							<button type="button" class="dc-compare-table__remove" data-dc-compare-remove="<?php echo esc_attr( $p->get_id() ); ?>" aria-label="<?php echo esc_attr( sprintf( __( 'Remove %s from comparison', 'dankcave' ), $p->get_name() ) ); ?>">
								<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
							</button>
							<a href="<?php echo esc_url( $p->get_permalink() ); ?>" class="dc-compare-table__product">
								<div class="dc-compare-table__thumb">
									<img src="<?php echo esc_url( wp_get_attachment_image_url( $p->get_image_id(), 'medium' ) ?: wc_placeholder_img_src( 'medium' ) ); ?>" alt="" loading="lazy">
								</div>
								<div class="dc-compare-table__name"><?php echo esc_html( $p->get_name() ); ?></div>
							</a>
						</th>
					<?php endforeach; ?>
				</tr>
			</thead>
			<tbody>
				<tr>
					<th class="dc-compare-table__label" scope="row"><?php esc_html_e( 'Price', 'dankcave' ); ?></th>
					<?php foreach ( $products as $p ) :
						$is_best = ( count( $products ) > 1 && $prices[ $p->get_id() ] > 0 && $prices[ $p->get_id() ] <= $min_price );
					?>
						<td data-label="<?php esc_attr_e( 'Price', 'dankcave' ); ?>">
							<span class="dc-compare-table__price<?php echo $is_best ? ' is-best' : ''; ?>">
								<?php echo $p->get_price_html(); // phpcs:ignore ?>
							</span>
							<?php if ( $is_best ) : ?>
								<span class="dc-compare-table__badge dc-compare-table__badge--best"><?php esc_html_e( 'Best price', 'dankcave' ); ?></span>
							<?php endif; ?>
						</td>
					<?php endforeach; ?>
				</tr>
				<tr>
					<th class="dc-compare-table__label" scope="row"><?php esc_html_e( 'Stock', 'dankcave' ); ?></th>
					<?php foreach ( $products as $p ) :
						$in_stock = $p->is_in_stock();
					?>
						<td data-label="<?php esc_attr_e( 'Stock', 'dankcave' ); ?>">
							<span class="dc-compare-table__chip dc-compare-table__chip--<?php echo $in_stock ? 'ok' : 'out'; ?>"><?php echo esc_html( $in_stock ? __( 'In stock', 'dankcave' ) : __( 'Sold out', 'dankcave' ) ); ?></span>
						</td>
					<?php endforeach; ?>
				</tr>
				<tr>
					<th class="dc-compare-table__label" scope="row"><?php esc_html_e( 'Rating', 'dankcave' ); ?></th>
					<?php foreach ( $products as $p ) :
						$avg = (float) $p->get_average_rating();
						$count = (int) $p->get_review_count();
					?>
						<td data-label="<?php esc_attr_e( 'Rating', 'dankcave' ); ?>">
							<?php if ( $count ) : ?>
								<span class="dc-compare-table__rating"><?php echo esc_html( number_format_i18n( $avg, 1 ) ); ?> <span class="dc-compare-table__star" aria-hidden="true">★</span> <span class="dc-compare-table__count">(<?php echo esc_html( $count ); ?>)</span></span>
							<?php else : ?>
								<span class="dc-compare-table__empty">—</span>
							<?php endif; ?>
						</td>
					<?php endforeach; ?>
				</tr>
				<?php foreach ( $attr_keys as $slug => $label ) : ?>
					<tr>
						<th class="dc-compare-table__label" scope="row"><?php echo esc_html( $label ); ?></th>
						<?php foreach ( $products as $p ) :
							$val = '—';
							foreach ( $p->get_attributes() as $s => $a ) {
								if ( $s !== $slug ) { continue; }
								if ( $a->is_taxonomy() ) {
									$terms = wc_get_product_terms( $p->get_id(), $a->get_name(), array( 'fields' => 'names' ) );
									$val = $terms ? implode( ', ', $terms ) : '—';
								} else {
									$val = implode( ', ', $a->get_options() ) ?: '—';
								}
							}
						?>
							<td data-label="<?php echo esc_attr( $label ); ?>">
								<?php echo '—' === $val ? '<span class="dc-compare-table__empty">—</span>' : esc_html( $val ); ?>
							</td>
						<?php endforeach; ?>
					</tr>
				<?php endforeach; ?>
				<tr class="dc-compare-table__row-cta">
					<th class="dc-compare-table__label" scope="row"></th>
					<?php foreach ( $products as $p ) : ?>
						<td data-label="">
							<a class="dc-compare-table__cta" href="<?php echo esc_url( $p->add_to_cart_url() ); ?>" data-quantity="1" data-product_id="<?php echo esc_attr( $p->get_id() ); ?>" rel="nofollow"><?php echo esc_html( $p->add_to_cart_text() ); ?></a>
						</td>
					<?php endforeach; ?>
				</tr>
			</tbody>
		</table>
	</div>
	<?php
	wp_send_json_success( array( 'html' => ob_get_clean() ) );
}

// -----------------------------------------------------------------------------
// Cart drawer (right-side slide-in). Renders its own contents via helper fns so
// the same markup can be re-rendered inside woocommerce_add_to_cart_fragments
// after an AJAX add-to-cart.
// -----------------------------------------------------------------------------

function dankcave_render_cart_drawer_contents() {
	if ( ! function_exists( 'WC' ) || ! WC()->cart ) { return; }
	$items = WC()->cart->get_cart();
	if ( empty( $items ) ) {
		?>
		<div class="dc-cart-drawer__empty">
			<p><?php esc_html_e( 'Your bag is empty.', 'dankcave' ); ?></p>
			<a class="dc-cart-drawer__empty-cta" href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>"><?php esc_html_e( 'Start shopping', 'dankcave' ); ?></a>
		</div>
		<?php
		return;
	}
	?>
	<ul class="dc-cart-drawer__items">
		<?php foreach ( $items as $key => $item ) :
			$product = apply_filters( 'woocommerce_cart_item_product', $item['data'], $item, $key );
			if ( ! $product || ! $product->exists() ) { continue; }
			$name      = apply_filters( 'woocommerce_cart_item_name', $product->get_name(), $item, $key );
			$permalink = $product->is_visible() ? $product->get_permalink( $item ) : '';
			$attrs     = wc_get_formatted_cart_item_data( $item );
			$remove_url = wc_get_cart_remove_url( $key );
			?>
			<li class="dc-cart-drawer-item">
				<div class="dc-cart-drawer-item__thumb">
					<div class="dc-cart-drawer-item__thumb-media">
						<?php echo $product->get_image( 'woocommerce_thumbnail' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</div>
					<span class="dc-cart-drawer-item__qty"><?php echo (int) $item['quantity']; ?></span>
				</div>
				<div class="dc-cart-drawer-item__info">
					<div class="dc-cart-drawer-item__name">
						<?php if ( $permalink ) : ?>
							<a href="<?php echo esc_url( $permalink ); ?>"><?php echo wp_kses_post( $name ); ?></a>
						<?php else : ?>
							<?php echo wp_kses_post( $name ); ?>
						<?php endif; ?>
					</div>
					<?php if ( $attrs ) : ?>
						<div class="dc-cart-drawer-item__attrs"><?php echo wp_kses_post( $attrs ); ?></div>
					<?php endif; ?>
					<div class="dc-cart-drawer-item__meta">
						<span class="dc-cart-drawer-item__price"><?php echo wp_kses_post( WC()->cart->get_product_subtotal( $product, $item['quantity'] ) ); ?></span>
						<a class="dc-cart-drawer-item__remove" href="<?php echo esc_url( $remove_url ); ?>" data-product_id="<?php echo esc_attr( $product->get_id() ); ?>" aria-label="<?php echo esc_attr( sprintf( __( 'Remove %s from cart', 'dankcave' ), wp_strip_all_tags( $name ) ) ); ?>"><?php esc_html_e( 'Remove', 'dankcave' ); ?></a>
					</div>
				</div>
			</li>
		<?php endforeach; ?>
	</ul>
	<?php
}

function dankcave_render_cart_drawer_footer() {
	if ( ! function_exists( 'WC' ) || ! WC()->cart || 0 === WC()->cart->get_cart_contents_count() ) { return; }
	$subtotal = WC()->cart->get_cart_subtotal();
	?>
	<div class="dc-cart-drawer__subtotal">
		<span><?php esc_html_e( 'Subtotal', 'dankcave' ); ?></span>
		<span class="dc-cart-drawer__subtotal-val"><?php echo wp_kses_post( $subtotal ); ?></span>
	</div>
	<a class="dc-cart-drawer__cta dc-cart-drawer__cta--primary" href="<?php echo esc_url( wc_get_checkout_url() ); ?>"><?php esc_html_e( 'Checkout', 'dankcave' ); ?></a>
	<a class="dc-cart-drawer__cta dc-cart-drawer__cta--secondary" href="<?php echo esc_url( wc_get_cart_url() ); ?>"><?php esc_html_e( 'View cart', 'dankcave' ); ?></a>
	<?php
}

/**
 * Push updated drawer HTML into the AJAX fragments response so the drawer
 * refreshes automatically after adding to cart / removing without a page reload.
 */
add_filter( 'woocommerce_add_to_cart_fragments', 'dankcave_cart_drawer_fragments' );
function dankcave_cart_drawer_fragments( $fragments ) {
	ob_start();
	dankcave_render_cart_drawer_contents();
	$fragments['div[data-dc-drawer-contents]'] = '<div class="dc-cart-drawer__contents" data-dc-drawer-contents>' . ob_get_clean() . '</div>';

	ob_start();
	dankcave_render_cart_drawer_footer();
	$fragments['footer[data-dc-drawer-foot]'] = '<footer class="dc-cart-drawer__foot" data-dc-drawer-foot>' . ob_get_clean() . '</footer>';

	$count = WC()->cart->get_cart_contents_count();
	$fragments['span[data-dc-drawer-count]'] = '<span class="dc-cart-drawer__count" data-dc-drawer-count>' . (int) $count . '</span>';
	$fragments['span[data-cart-count]']      = '<span class="cart-summary__count" data-cart-count>' . (int) $count . '</span>';
	return $fragments;
}

// Coupon field inline in the order review sidebar
add_action( 'woocommerce_review_order_after_order_total', 'dankcave_checkout_inline_coupon', 20 );
function dankcave_checkout_inline_coupon() {
	if ( ! wc_coupons_enabled() ) { return; }
	?>
	<div class="dc-review__coupon">
		<div class="dc-review__coupon-toggle">
			<span><?php esc_html_e( 'Have a coupon?', 'dankcave' ); ?></span>
			<a href="#" data-dc-toggle-coupon><?php esc_html_e( 'Enter code', 'dankcave' ); ?></a>
		</div>
		<form class="dc-review__coupon-form" method="post" hidden data-dc-inline-coupon>
			<input type="text" name="coupon_code" class="dc-review__coupon-input" placeholder="<?php esc_attr_e( 'Discount code', 'dankcave' ); ?>" value="">
			<button type="submit" class="dc-review__coupon-apply" name="apply_coupon" value="1"><?php esc_html_e( 'Apply', 'dankcave' ); ?></button>
		</form>
	</div>
	<?php
}

/**
 * Original Dankcave site had no visible shipping-method choice at checkout — only
 * a single flat rate. We drop the dedicated shipping card and let WC auto-pick the
 * first available rate in the background. Review-order template also skips the
 * inline shipping row.
 */
