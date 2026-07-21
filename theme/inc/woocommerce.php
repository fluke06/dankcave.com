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
 * Break the shipping methods out into their own card above payment, matching the design.
 * We suppress the default inline render (which sits between subtotal and total in the
 * order summary) and instead output it as a labelled card in the main column.
 */
add_action( 'woocommerce_checkout_after_customer_details', 'dankcave_checkout_shipping_method_card', 5 );
function dankcave_checkout_shipping_method_card() {
	if ( ! WC()->cart || ! WC()->cart->needs_shipping() ) { return; }
	$packages = WC()->shipping()->get_packages();
	if ( empty( $packages ) ) { return; }
	?>
	<div class="dc-checkout-card dc-checkout-card--shipping-method">
		<h3 class="dc-checkout-card__title"><?php esc_html_e( 'Shipping method', 'dankcave' ); ?></h3>
		<?php foreach ( $packages as $i => $package ) :
			$available = $package['rates'];
			$chosen    = isset( WC()->session->get( 'chosen_shipping_methods' )[ $i ] ) ? WC()->session->get( 'chosen_shipping_methods' )[ $i ] : null;
		?>
			<ul id="shipping_method" class="dc-shipping-methods">
				<?php foreach ( $available as $rate ) :
					$id      = 'shipping_method_' . $i . '_' . sanitize_title( $rate->id );
					$label   = wc_cart_totals_shipping_method_label( $rate );
				?>
					<li>
						<label for="<?php echo esc_attr( $id ); ?>" class="dc-shipping-method">
							<input type="radio" name="shipping_method[<?php echo esc_attr( $i ); ?>]" data-index="<?php echo esc_attr( $i ); ?>" id="<?php echo esc_attr( $id ); ?>" value="<?php echo esc_attr( $rate->id ); ?>" class="shipping_method" <?php checked( $rate->id, $chosen ); ?>>
							<span class="dc-shipping-method__label"><?php echo wp_kses_post( $label ); ?></span>
						</label>
					</li>
				<?php endforeach; ?>
			</ul>
		<?php endforeach; ?>
	</div>
	<?php
}
