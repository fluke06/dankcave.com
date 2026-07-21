<?php
/**
 * Cart totals sidebar. Restyled to match the design summary card.
 *
 * @package Dankcave
 */

defined( 'ABSPATH' ) || exit;

$checkout_url = wc_get_checkout_url();
$cart_total   = WC()->cart->get_total();
?>
<div class="dc-summary-card cart_totals <?php echo ( WC()->customer->has_calculated_shipping() ) ? 'calculated_shipping' : ''; ?>">
	<div class="dc-summary-card__title"><?php esc_html_e( 'Order summary', 'dankcave' ); ?></div>

	<div class="dc-summary-card__row cart-subtotal">
		<span><?php esc_html_e( 'Subtotal', 'dankcave' ); ?></span>
		<span class="dc-summary-card__val"><?php wc_cart_totals_subtotal_html(); ?></span>
	</div>

	<?php foreach ( WC()->cart->get_coupons() as $code => $coupon ) : ?>
		<div class="dc-summary-card__row cart-discount coupon-<?php echo esc_attr( sanitize_title( $code ) ); ?>">
			<span><?php wc_cart_totals_coupon_label( $coupon ); ?></span>
			<span class="dc-summary-card__val"><?php wc_cart_totals_coupon_html( $coupon ); ?></span>
		</div>
	<?php endforeach; ?>

	<?php if ( WC()->cart->needs_shipping() ) :
		$threshold = (float) get_theme_mod( 'dankcave_free_ship_threshold', 50 );
		$subtotal  = (float) WC()->cart->get_subtotal();
		$free      = ( $subtotal >= $threshold ) && $threshold > 0;
	?>
		<div class="dc-summary-card__row">
			<span><?php esc_html_e( 'Shipping', 'dankcave' ); ?></span>
			<span class="dc-summary-card__val">
				<?php echo $free ? esc_html__( 'FREE', 'dankcave' ) : esc_html__( 'Calculated at checkout', 'dankcave' ); ?>
			</span>
		</div>
	<?php endif; ?>

	<?php foreach ( WC()->cart->get_fees() as $fee ) : ?>
		<div class="dc-summary-card__row fee">
			<span><?php echo esc_html( $fee->name ); ?></span>
			<span class="dc-summary-card__val"><?php wc_cart_totals_fee_html( $fee ); ?></span>
		</div>
	<?php endforeach; ?>

	<?php if ( wc_tax_enabled() && ! WC()->cart->display_prices_including_tax() ) : ?>
		<?php $taxable_address = WC()->customer->get_taxable_address(); ?>
		<?php if ( 'itemized' === get_option( 'woocommerce_tax_total_display' ) ) : ?>
			<?php foreach ( WC()->cart->get_tax_totals() as $code => $tax ) : ?>
				<div class="dc-summary-card__row tax-rate tax-rate-<?php echo esc_attr( sanitize_title( $code ) ); ?>">
					<span><?php echo esc_html( $tax->label ); ?></span>
					<span class="dc-summary-card__val"><?php echo wp_kses_post( $tax->formatted_amount ); ?></span>
				</div>
			<?php endforeach; ?>
		<?php else : ?>
			<div class="dc-summary-card__row tax-total">
				<span><?php echo esc_html( WC()->countries->tax_or_vat() ); ?></span>
				<span class="dc-summary-card__val"><?php wc_cart_totals_taxes_total_html(); ?></span>
			</div>
		<?php endif; ?>
	<?php endif; ?>

	<div class="dc-summary-card__row dc-summary-card__row--total order-total">
		<span><?php esc_html_e( 'Total', 'dankcave' ); ?></span>
		<span class="dc-summary-card__val"><?php wc_cart_totals_order_total_html(); ?></span>
	</div>

	<?php do_action( 'woocommerce_cart_totals_after_order_total' ); ?>

	<div class="wc-proceed-to-checkout">
		<?php do_action( 'woocommerce_proceed_to_checkout' ); ?>
	</div>

	<?php do_action( 'woocommerce_after_cart_totals' ); ?>

	<div class="dc-summary-card__trust">
		<span>🔒 <?php esc_html_e( 'Secure', 'dankcave' ); ?></span>
		<span>Visa</span>
		<span>MC</span>
		<span>Amex</span>
	</div>
</div>
