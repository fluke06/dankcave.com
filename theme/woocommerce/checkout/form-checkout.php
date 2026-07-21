<?php
/**
 * Checkout form — custom override matching design/Dankcave - Checkout.dc.html.
 *
 * Two-column: customer details cards on the left, sticky order review on the right.
 * We keep the outer <form class="checkout woocommerce-checkout"> and all the
 * WooCommerce action hooks so payment gateways (Authorize.net, Sezzle) inject
 * their fields in the expected slots.
 *
 * @package Dankcave
 */

defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_before_checkout_form', $checkout );

if ( ! $checkout->is_registration_enabled() && $checkout->is_registration_required() && ! is_user_logged_in() ) {
	echo esc_html( apply_filters( 'woocommerce_checkout_must_be_logged_in_message', __( 'You must be logged in to checkout.', 'woocommerce' ) ) );
	return;
}
?>

<div class="dc-checkout">

	<header class="dc-checkout__header">
		<h1 class="dc-checkout__title"><?php esc_html_e( 'Checkout', 'dankcave' ); ?></h1>
	</header>

	<div class="dc-checkout__steps" aria-hidden="true">
		<div class="dc-checkout-step is-active">
			<span class="dc-checkout-step__num">1</span>
			<span class="dc-checkout-step__label"><?php esc_html_e( 'Information', 'dankcave' ); ?></span>
		</div>
		<span class="dc-checkout-step__sep"></span>
		<div class="dc-checkout-step is-active">
			<span class="dc-checkout-step__num">2</span>
			<span class="dc-checkout-step__label"><?php esc_html_e( 'Shipping', 'dankcave' ); ?></span>
		</div>
		<span class="dc-checkout-step__sep"></span>
		<div class="dc-checkout-step">
			<span class="dc-checkout-step__num">3</span>
			<span class="dc-checkout-step__label"><?php esc_html_e( 'Payment', 'dankcave' ); ?></span>
		</div>
	</div>

	<form name="checkout" method="post" class="checkout woocommerce-checkout dc-checkout__form" action="<?php echo esc_url( wc_get_checkout_url() ); ?>" enctype="multipart/form-data">

		<div class="dc-checkout__grid">
			<div class="dc-checkout__main">
				<?php if ( $checkout->get_checkout_fields() ) : ?>
					<?php do_action( 'woocommerce_checkout_before_customer_details' ); ?>

					<div class="dc-checkout__customer" id="customer_details">
						<div class="dc-checkout-card dc-checkout-card--billing">
							<?php do_action( 'woocommerce_checkout_billing' ); ?>
						</div>
						<div class="dc-checkout-card dc-checkout-card--shipping">
							<?php do_action( 'woocommerce_checkout_shipping' ); ?>
						</div>
					</div>

					<?php do_action( 'woocommerce_checkout_after_customer_details' ); ?>
				<?php endif; ?>
			</div>

			<aside class="dc-checkout__aside" aria-label="<?php esc_attr_e( 'Order summary', 'dankcave' ); ?>">
				<?php do_action( 'woocommerce_checkout_before_order_review_heading' ); ?>

				<h2 id="order_review_heading" class="dc-checkout__aside-title"><?php esc_html_e( 'Order summary', 'dankcave' ); ?></h2>

				<?php do_action( 'woocommerce_checkout_before_order_review' ); ?>

				<div id="order_review" class="woocommerce-checkout-review-order dc-checkout__review">
					<?php do_action( 'woocommerce_checkout_order_review' ); ?>
				</div>

				<?php do_action( 'woocommerce_checkout_after_order_review' ); ?>
			</aside>
		</div>

	</form>

</div>

<?php do_action( 'woocommerce_after_checkout_form', $checkout ); ?>
