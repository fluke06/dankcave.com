<?php
/**
 * My Account → Orders list. Rendered as cards matching the dashboard style.
 *
 * @package Dankcave
 */

defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_before_account_orders', $has_orders );
?>
<?php if ( $has_orders ) : ?>
	<div class="dc-orders">
		<div class="dc-orders__list">
			<?php foreach ( $customer_orders->orders as $customer_order ) :
				$order = wc_get_order( $customer_order );
				if ( ! $order ) { continue; }

				$status       = $order->get_status();
				$status_label = wc_get_order_status_name( $status );
				$status_class = 'is-' . sanitize_html_class( $status );
				$item_count   = $order->get_item_count();
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
					<div class="dc-dash-order__actions">
						<?php foreach ( wc_get_account_orders_actions( $order ) as $key => $action ) : ?>
							<a class="dc-dash-order__view button <?php echo esc_attr( $key ); ?>" href="<?php echo esc_url( $action['url'] ); ?>"><?php echo esc_html( $action['name'] ); ?></a>
						<?php endforeach; ?>
					</div>
				</div>
			<?php endforeach; ?>
		</div>

		<?php do_action( 'woocommerce_before_account_orders_pagination' ); ?>

		<?php if ( 1 < $customer_orders->max_num_pages ) : ?>
			<div class="dc-orders__pagination woocommerce-pagination">
				<?php if ( 1 !== $current_page ) : ?>
					<a class="dc-orders__pagination-btn woocommerce-button button woocommerce-Button" href="<?php echo esc_url( wc_get_endpoint_url( 'orders', $current_page - 1 ) ); ?>"><?php esc_html_e( '← Previous', 'dankcave' ); ?></a>
				<?php endif; ?>
				<?php if ( intval( $customer_orders->max_num_pages ) !== $current_page ) : ?>
					<a class="dc-orders__pagination-btn woocommerce-button button woocommerce-Button" href="<?php echo esc_url( wc_get_endpoint_url( 'orders', $current_page + 1 ) ); ?>"><?php esc_html_e( 'Next →', 'dankcave' ); ?></a>
				<?php endif; ?>
			</div>
		<?php endif; ?>
	</div>
<?php else : ?>
	<div class="dc-dash-orders__empty">
		<?php esc_html_e( 'No orders yet.', 'dankcave' ); ?>
		<a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>"><?php esc_html_e( 'Start shopping →', 'dankcave' ); ?></a>
	</div>
<?php endif; ?>

<?php do_action( 'woocommerce_after_account_orders', $has_orders ); ?>
