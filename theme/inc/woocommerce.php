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
