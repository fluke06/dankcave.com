<?php
/**
 * My Account dashboard. Stats grid + recent orders + address + account details.
 *
 * @package Dankcave
 */

defined( 'ABSPATH' ) || exit;

$current_user = wp_get_current_user();
$customer_id  = $current_user->ID;

$order_count = wc_get_customer_order_count( $customer_id );
$spend       = wc_get_customer_total_spent( $customer_id );

$recent_orders = wc_get_orders( array(
	'customer_id' => $customer_id,
	'limit'       => 3,
	'orderby'     => 'date',
	'order'       => 'DESC',
) );

$address       = get_user_meta( $customer_id, 'billing_address_1', true );
$address_city  = get_user_meta( $customer_id, 'billing_city', true );
$address_state = get_user_meta( $customer_id, 'billing_state', true );
$address_zip   = get_user_meta( $customer_id, 'billing_postcode', true );
$address_phone = get_user_meta( $customer_id, 'billing_phone', true );
$first_name    = $current_user->first_name ? $current_user->first_name : $current_user->display_name;
$last_name     = $current_user->last_name;
?>
<div class="dc-dash">

	<div class="dc-dash__stats">
		<div class="dc-dash-stat">
			<div class="dc-dash-stat__val"><?php echo esc_html( $order_count ); ?></div>
			<div class="dc-dash-stat__label"><?php esc_html_e( 'Orders placed', 'dankcave' ); ?></div>
		</div>
		<div class="dc-dash-stat">
			<div class="dc-dash-stat__val"><?php echo wp_kses_post( wc_price( $spend ) ); ?></div>
			<div class="dc-dash-stat__label"><?php esc_html_e( 'Lifetime spend', 'dankcave' ); ?></div>
		</div>
		<div class="dc-dash-stat">
			<div class="dc-dash-stat__val"><?php echo esc_html( (int) floor( $spend / 5 ) ); ?></div>
			<div class="dc-dash-stat__label"><?php esc_html_e( 'Cave points', 'dankcave' ); ?></div>
		</div>
	</div>

	<section class="dc-dash-orders">
		<div class="dc-dash-orders__head">
			<h2 class="dc-dash-orders__title"><?php esc_html_e( 'Recent orders', 'dankcave' ); ?></h2>
			<a class="dc-dash-orders__link" href="<?php echo esc_url( wc_get_account_endpoint_url( 'orders' ) ); ?>"><?php esc_html_e( 'View all →', 'dankcave' ); ?></a>
		</div>

		<?php if ( ! empty( $recent_orders ) ) : ?>
			<div class="dc-dash-orders__list">
				<?php foreach ( $recent_orders as $order ) :
					$status       = $order->get_status();
					$status_label = wc_get_order_status_name( $status );
					$status_class = 'is-' . sanitize_html_class( $status );
					$item_count   = $order->get_item_count();
					$view_url     = $order->get_view_order_url();
				?>
					<div class="dc-dash-order">
						<div class="dc-dash-order__info">
							<div class="dc-dash-order__id-row">
								<span class="dc-dash-order__id">#<?php echo esc_html( $order->get_order_number() ); ?></span>
								<span class="dc-dash-order__status <?php echo esc_attr( $status_class ); ?>"><?php echo esc_html( $status_label ); ?></span>
							</div>
							<div class="dc-dash-order__meta">
								<?php
								echo esc_html( wc_format_datetime( $order->get_date_created(), 'M j, Y' ) );
								echo ' · ';
								echo esc_html( sprintf( _n( '%d item', '%d items', $item_count, 'dankcave' ), $item_count ) );
								echo ' · ';
								echo wp_kses_post( $order->get_formatted_order_total() );
								?>
							</div>
						</div>
						<a class="dc-dash-order__view" href="<?php echo esc_url( $view_url ); ?>"><?php esc_html_e( 'View', 'dankcave' ); ?></a>
					</div>
				<?php endforeach; ?>
			</div>
		<?php else : ?>
			<div class="dc-dash-orders__empty">
				<?php esc_html_e( 'You have not placed an order yet.', 'dankcave' ); ?>
				<a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>"><?php esc_html_e( 'Start shopping →', 'dankcave' ); ?></a>
			</div>
		<?php endif; ?>
	</section>

	<div class="dc-dash-panels">
		<div class="dc-dash-panel">
			<div class="dc-dash-panel__head">
				<h3 class="dc-dash-panel__title"><?php esc_html_e( 'Default address', 'dankcave' ); ?></h3>
				<a class="dc-dash-panel__edit" href="<?php echo esc_url( wc_get_endpoint_url( 'edit-address' ) ); ?>"><?php esc_html_e( 'Edit', 'dankcave' ); ?></a>
			</div>
			<div class="dc-dash-panel__body">
				<?php if ( $address ) : ?>
					<?php echo esc_html( trim( $first_name . ' ' . $last_name ) ); ?><br>
					<?php echo esc_html( $address ); ?><br>
					<?php echo esc_html( trim( $address_city . ', ' . $address_state . ' ' . $address_zip, ', ' ) ); ?><br>
					<?php echo esc_html( $address_phone ); ?>
				<?php else : ?>
					<?php esc_html_e( 'No address on file yet.', 'dankcave' ); ?>
					<br><a href="<?php echo esc_url( wc_get_endpoint_url( 'edit-address' ) ); ?>"><?php esc_html_e( 'Add one →', 'dankcave' ); ?></a>
				<?php endif; ?>
			</div>
		</div>

		<div class="dc-dash-panel">
			<div class="dc-dash-panel__head">
				<h3 class="dc-dash-panel__title"><?php esc_html_e( 'Account details', 'dankcave' ); ?></h3>
				<a class="dc-dash-panel__edit" href="<?php echo esc_url( wc_get_endpoint_url( 'edit-account' ) ); ?>"><?php esc_html_e( 'Edit', 'dankcave' ); ?></a>
			</div>
			<div class="dc-dash-panel__body">
				<?php echo esc_html( $current_user->user_email ); ?><br>
				<?php esc_html_e( 'Password', 'dankcave' ); ?> ••••••••<br>
				<?php esc_html_e( 'Age verified', 'dankcave' ); ?>: <?php esc_html_e( 'Yes (21+)', 'dankcave' ); ?>
			</div>
		</div>
	</div>

	<?php do_action( 'woocommerce_account_dashboard' ); ?>
	<?php do_action( 'woocommerce_before_my_account' ); ?>
	<?php do_action( 'woocommerce_after_my_account' ); ?>
</div>
