<?php
/**
 * Proceed to checkout button. Restyled to match design.
 *
 * @package Dankcave
 */

defined( 'ABSPATH' ) || exit;
?>
<a href="<?php echo esc_url( wc_get_checkout_url() ); ?>" class="checkout-button button alt wc-forward dc-summary-card__cta">
	<?php
	printf(
		'%s · %s',
		esc_html__( 'Checkout', 'dankcave' ),
		wp_kses_post( WC()->cart->get_total() )
	);
	?>
</a>
