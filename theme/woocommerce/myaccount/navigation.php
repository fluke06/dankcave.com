<?php
/**
 * Sidebar nav for My Account. Avatar card + link list.
 *
 * @package Dankcave
 */

defined( 'ABSPATH' ) || exit;

$current_user = wp_get_current_user();

$initials = '';
if ( $current_user->first_name || $current_user->last_name ) {
	$initials = strtoupper( substr( $current_user->first_name, 0, 1 ) . substr( $current_user->last_name, 0, 1 ) );
}
if ( ! $initials ) {
	$initials = strtoupper( substr( $current_user->display_name, 0, 2 ) );
}
if ( ! $initials ) {
	$initials = strtoupper( substr( $current_user->user_email, 0, 2 ) );
}

$display_name = $current_user->display_name;
$member_since = date_i18n( 'Y', strtotime( $current_user->user_registered ) );

$icons = array(
	'dashboard'       => '▦',
	'orders'          => '▤',
	'downloads'       => '⬇',
	'edit-address'    => '⌂',
	'payment-methods' => '▭',
	'edit-account'    => '◔',
	'customer-logout' => '⏻',
);
?>
<aside class="dc-account-nav" aria-label="<?php esc_attr_e( 'Account navigation', 'dankcave' ); ?>">
	<div class="dc-account-nav__profile">
		<div class="dc-account-nav__avatar" aria-hidden="true"><?php echo esc_html( $initials ); ?></div>
		<div class="dc-account-nav__ident">
			<div class="dc-account-nav__name"><?php echo esc_html( $display_name ); ?></div>
			<div class="dc-account-nav__since"><?php echo esc_html( sprintf( __( 'Member since %s', 'dankcave' ), $member_since ) ); ?></div>
		</div>
	</div>

	<nav class="dc-account-nav__links">
		<?php foreach ( wc_get_account_menu_items() as $endpoint => $label ) :
			$icon = $icons[ $endpoint ] ?? '·';
		?>
			<a class="dc-account-nav__link <?php echo esc_attr( wc_get_account_menu_item_classes( $endpoint ) ); ?>" href="<?php echo esc_url( wc_get_account_endpoint_url( $endpoint ) ); ?>">
				<span class="dc-account-nav__icon" aria-hidden="true"><?php echo esc_html( $icon ); ?></span>
				<span class="dc-account-nav__label"><?php echo esc_html( $label ); ?></span>
			</a>
		<?php endforeach; ?>
	</nav>
</aside>
