<?php
/**
 * My Account page. Two-column layout matching design/Dankcave - Account.dc.html.
 *
 * @package Dankcave
 */

defined( 'ABSPATH' ) || exit;

$current_user = wp_get_current_user();
$first_name   = $current_user->first_name ? $current_user->first_name : $current_user->display_name;
?>
<div class="dc-account">
	<header class="dc-account__header">
		<div class="dc-account__eyebrow"><?php esc_html_e( 'Welcome back', 'dankcave' ); ?></div>
		<h1 class="dc-account__title">
			<?php echo esc_html( sprintf( __( 'Hey, %s', 'dankcave' ), $first_name ) ); ?> <span class="dc-account__wave" aria-hidden="true">👋</span>
		</h1>
	</header>

	<div class="dc-account__layout">
		<?php do_action( 'woocommerce_account_navigation' ); ?>

		<div class="dc-account__content woocommerce-MyAccount-content">
			<?php
			/**
			 * Fires when the account endpoint content is being loaded.
			 */
			do_action( 'woocommerce_account_content' );
			?>
		</div>
	</div>
</div>
