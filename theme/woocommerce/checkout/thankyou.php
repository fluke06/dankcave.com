<?php
/**
 * Thank-you / order-received page — Dankcave editorial version.
 *
 * @var WC_Order $order
 * @package Dankcave
 */

defined( 'ABSPATH' ) || exit;

if ( ! $order ) {
	echo '<div class="dc-thankyou dc-thankyou--empty"><h1>' . esc_html__( 'Order not found', 'dankcave' ) . '</h1><p>' . esc_html__( 'This order confirmation link is missing or has expired.', 'dankcave' ) . '</p></div>';
	return;
}

do_action( 'woocommerce_before_thankyou', $order->get_id() );

$first_name = $order->get_billing_first_name();
$order_id   = $order->get_order_number();
$order_date = wc_format_datetime( $order->get_date_created() );
$email      = $order->get_billing_email();
$total      = $order->get_formatted_order_total();
$payment    = $order->get_payment_method_title();
$is_failed  = $order->has_status( 'failed' );
$items      = $order->get_items();
?>

<section class="dc-thankyou<?php echo $is_failed ? ' dc-thankyou--failed' : ''; ?>">

	<?php if ( $is_failed ) : ?>

		<div class="dc-thankyou__inner">
			<span class="dc-thankyou__eyebrow"><?php esc_html_e( 'PAYMENT ISSUE', 'dankcave' ); ?></span>
			<h1 class="dc-thankyou__title"><?php esc_html_e( 'Payment could not be processed', 'dankcave' ); ?></h1>
			<p class="dc-thankyou__intro"><?php esc_html_e( 'Your bank declined the charge. Please try again or use a different payment method.', 'dankcave' ); ?></p>
			<div class="dc-thankyou__cta-row">
				<a class="dc-thankyou__cta dc-thankyou__cta--dark" href="<?php echo esc_url( $order->get_checkout_payment_url() ); ?>"><?php esc_html_e( 'Try again', 'dankcave' ); ?></a>
				<a class="dc-thankyou__cta dc-thankyou__cta--light" href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>"><?php esc_html_e( 'Go to account', 'dankcave' ); ?></a>
			</div>
		</div>

	<?php else : ?>

		<div class="dc-thankyou__hero">
			<div class="dc-thankyou__inner">
				<span class="dc-thankyou__eyebrow"><?php esc_html_e( 'ORDER CONFIRMED', 'dankcave' ); ?></span>
				<h1 class="dc-thankyou__title">
					<?php
					if ( $first_name ) {
						printf( esc_html__( 'Thanks, %s — your order is on its way.', 'dankcave' ), esc_html( $first_name ) );
					} else {
						esc_html_e( 'Thank you — your order is on its way.', 'dankcave' );
					}
					?>
				</h1>
				<p class="dc-thankyou__intro">
					<?php
					printf(
						/* translators: 1: order number, 2: email address */
						esc_html__( 'Order #%1$s is in the queue. We\'ve emailed a copy of the confirmation to %2$s.', 'dankcave' ),
						esc_html( $order_id ),
						esc_html( $email )
					);
					?>
				</p>
			</div>
		</div>

		<div class="dc-thankyou__inner">

			<dl class="dc-thankyou__overview">
				<div class="dc-thankyou__overview-cell">
					<dt><?php esc_html_e( 'Order number', 'dankcave' ); ?></dt>
					<dd>#<?php echo esc_html( $order_id ); ?></dd>
				</div>
				<div class="dc-thankyou__overview-cell">
					<dt><?php esc_html_e( 'Date', 'dankcave' ); ?></dt>
					<dd><?php echo esc_html( $order_date ); ?></dd>
				</div>
				<div class="dc-thankyou__overview-cell">
					<dt><?php esc_html_e( 'Email', 'dankcave' ); ?></dt>
					<dd><?php echo esc_html( $email ); ?></dd>
				</div>
				<div class="dc-thankyou__overview-cell">
					<dt><?php esc_html_e( 'Total', 'dankcave' ); ?></dt>
					<dd><?php echo wp_kses_post( $total ); ?></dd>
				</div>
				<?php if ( $payment ) : ?>
					<div class="dc-thankyou__overview-cell">
						<dt><?php esc_html_e( 'Payment', 'dankcave' ); ?></dt>
						<dd><?php echo esc_html( $payment ); ?></dd>
					</div>
				<?php endif; ?>
			</dl>

			<?php if ( ! empty( $items ) ) : ?>
				<section class="dc-thankyou__items" aria-labelledby="dc-thankyou-items-title">
					<h2 id="dc-thankyou-items-title" class="dc-thankyou__section-title"><?php esc_html_e( 'What you ordered', 'dankcave' ); ?></h2>
					<ul class="dc-thankyou__item-list">
						<?php foreach ( $items as $item_id => $item ) :
							$product   = $item->get_product();
							$thumb_id  = $product ? $product->get_image_id() : 0;
							$thumb_url = $thumb_id ? wp_get_attachment_image_url( $thumb_id, 'thumbnail' ) : wc_placeholder_img_src( 'thumbnail' );
							$link      = $product ? $product->get_permalink() : '#';
							$qty       = $item->get_quantity();
							$name      = wp_kses_post( apply_filters( 'woocommerce_order_item_name', $item->get_name(), $item, false ) );
							$total_h   = $order->get_formatted_line_subtotal( $item );
						?>
							<li class="dc-thankyou__item">
								<a class="dc-thankyou__item-thumb" href="<?php echo esc_url( $link ); ?>">
									<img src="<?php echo esc_url( $thumb_url ); ?>" alt="" width="72" height="72">
								</a>
								<div class="dc-thankyou__item-body">
									<a class="dc-thankyou__item-name" href="<?php echo esc_url( $link ); ?>"><?php echo $name; // phpcs:ignore ?></a>
									<span class="dc-thankyou__item-qty"><?php echo esc_html( sprintf( __( 'Qty %d', 'dankcave' ), $qty ) ); ?></span>
								</div>
								<span class="dc-thankyou__item-total"><?php echo wp_kses_post( $total_h ); ?></span>
							</li>
						<?php endforeach; ?>
					</ul>
				</section>
			<?php endif; ?>

			<div class="dc-thankyou__grid">

				<section class="dc-thankyou__block">
					<h3 class="dc-thankyou__block-title"><?php esc_html_e( 'Billing', 'dankcave' ); ?></h3>
					<address class="dc-thankyou__address">
						<?php echo wp_kses_post( $order->get_formatted_billing_address( '&mdash;' ) ); ?>
						<?php if ( $order->get_billing_phone() ) : ?>
							<div class="dc-thankyou__meta"><?php echo esc_html( $order->get_billing_phone() ); ?></div>
						<?php endif; ?>
						<?php if ( $order->get_billing_email() ) : ?>
							<div class="dc-thankyou__meta"><?php echo esc_html( $order->get_billing_email() ); ?></div>
						<?php endif; ?>
					</address>
				</section>

				<?php if ( $order->needs_shipping_address() ) : ?>
					<section class="dc-thankyou__block">
						<h3 class="dc-thankyou__block-title"><?php esc_html_e( 'Shipping to', 'dankcave' ); ?></h3>
						<address class="dc-thankyou__address">
							<?php echo wp_kses_post( $order->get_formatted_shipping_address( '&mdash;' ) ); ?>
						</address>
					</section>
				<?php endif; ?>

				<section class="dc-thankyou__block">
					<h3 class="dc-thankyou__block-title"><?php esc_html_e( 'What happens next', 'dankcave' ); ?></h3>
					<ol class="dc-thankyou__next">
						<li><?php esc_html_e( 'You\'ll get a shipping notification email once the order leaves the warehouse — usually within 1 business day.', 'dankcave' ); ?></li>
						<li><?php esc_html_e( 'Packages arrive in plain, discreet boxes. No branding on the outside.', 'dankcave' ); ?></li>
						<li><?php esc_html_e( 'An adult signature (21+) may be required. Have valid ID ready.', 'dankcave' ); ?></li>
					</ol>
				</section>

			</div>

			<div class="dc-thankyou__cta-row">
				<?php if ( is_user_logged_in() ) : ?>
					<a class="dc-thankyou__cta dc-thankyou__cta--dark" href="<?php echo esc_url( wc_get_endpoint_url( 'view-order', $order->get_id(), wc_get_page_permalink( 'myaccount' ) ) ); ?>"><?php esc_html_e( 'Track this order', 'dankcave' ); ?></a>
				<?php endif; ?>
				<a class="dc-thankyou__cta dc-thankyou__cta--light" href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>"><?php esc_html_e( 'Keep shopping', 'dankcave' ); ?></a>
			</div>

		</div>

	<?php endif; ?>

</section>

<?php
// Give plugins a chance to hook in (analytics, referrer, etc.).
do_action( 'woocommerce_thankyou_' . $order->get_payment_method(), $order->get_id() );
do_action( 'woocommerce_thankyou', $order->get_id() );
