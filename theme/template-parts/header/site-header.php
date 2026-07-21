<?php
/**
 * Site header: logo, pill primary nav, cart summary. Floats over hero on home,
 * sits on cream background on all other templates.
 *
 * @package Dankcave
 */

$cart_count = 0;
if ( function_exists( 'WC' ) && WC()->cart ) {
	$cart_count = (int) WC()->cart->get_cart_contents_count();
}
$cart_url = function_exists( 'wc_get_cart_url' ) ? wc_get_cart_url() : home_url( '/cart/' );
?>
<header class="site-header" role="banner">
	<div class="site-header__inner">
		<?php if ( has_custom_logo() ) : ?>
			<div class="site-brand site-brand--logo"><?php the_custom_logo(); ?></div>
		<?php else : ?>
			<a class="site-brand" href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>">
				<span class="site-brand__text"><?php bloginfo( 'name' ); ?></span>
			</a>
		<?php endif; ?>

		<nav class="primary-nav" aria-label="<?php esc_attr_e( 'Primary', 'dankcave' ); ?>">
			<?php
			wp_nav_menu( array(
				'theme_location' => 'primary',
				'container'      => false,
				'menu_class'     => 'primary-nav__list',
				'fallback_cb'    => function () {
					echo '<ul class="primary-nav__list">';
					printf( '<li><a href="%s">%s</a></li>', esc_url( home_url( '/shop/' ) ),  esc_html__( 'Shop', 'dankcave' ) );
					printf( '<li><a href="%s">%s</a></li>', esc_url( home_url( '/blog/' ) ),  esc_html__( 'Journal', 'dankcave' ) );
					printf( '<li><a href="%s">%s</a></li>', esc_url( home_url( '/about/' ) ), esc_html__( 'About', 'dankcave' ) );
					echo '</ul>';
				},
				'depth'          => 1,
			) );
			?>
		</nav>

		<div class="site-header__actions">
			<a class="header-search-pill" href="<?php echo esc_url( home_url( '/?s=' ) ); ?>" aria-label="<?php esc_attr_e( 'Search', 'dankcave' ); ?>">
				<svg viewBox="0 0 20 20" width="18" height="18" aria-hidden="true" focusable="false">
					<circle cx="9" cy="9" r="6" fill="none" stroke="currentColor" stroke-width="1.8"></circle>
					<line x1="13.5" y1="13.5" x2="17.5" y2="17.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"></line>
				</svg>
			</a>
			<a class="cart-summary" href="<?php echo esc_url( $cart_url ); ?>" aria-label="<?php esc_attr_e( 'Cart', 'dankcave' ); ?>">
				<span class="cart-summary__label"><?php esc_html_e( 'Bag', 'dankcave' ); ?></span>
				<span class="cart-summary__sep" aria-hidden="true">·</span>
				<span class="cart-summary__count" data-cart-count><?php echo esc_html( $cart_count ); ?></span>
			</a>
		</div>

		<button class="site-header__toggle" type="button" aria-controls="primary-nav-mobile" aria-expanded="false">
			<span class="visually-hidden"><?php esc_html_e( 'Menu', 'dankcave' ); ?></span>
			<span class="site-header__toggle-bar"></span>
			<span class="site-header__toggle-bar"></span>
			<span class="site-header__toggle-bar"></span>
		</button>
	</div>

	<div class="primary-nav-mobile" id="primary-nav-mobile" hidden>
		<?php
		wp_nav_menu( array(
			'theme_location' => 'primary',
			'container'      => false,
			'menu_class'     => 'primary-nav-mobile__list',
			'fallback_cb'    => function () {
				echo '<ul class="primary-nav-mobile__list">';
				printf( '<li><a href="%s">%s</a></li>', esc_url( home_url( '/shop/' ) ),      esc_html__( 'Shop', 'dankcave' ) );
				printf( '<li><a href="%s">%s</a></li>', esc_url( home_url( '/collections/' ) ), esc_html__( 'Collections', 'dankcave' ) );
				printf( '<li><a href="%s">%s</a></li>', esc_url( home_url( '/blog/' ) ),      esc_html__( 'Journal', 'dankcave' ) );
				printf( '<li><a href="%s">%s</a></li>', esc_url( home_url( '/about/' ) ),     esc_html__( 'About', 'dankcave' ) );
				echo '</ul>';
			},
			'depth'          => 1,
		) );
		?>
	</div>
</header>
