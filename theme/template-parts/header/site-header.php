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
			<button type="button" class="header-search-pill" aria-label="<?php esc_attr_e( 'Open search', 'dankcave' ); ?>" data-search-open>
				<svg viewBox="0 0 20 20" width="18" height="18" aria-hidden="true" focusable="false">
					<circle cx="9" cy="9" r="6" fill="none" stroke="currentColor" stroke-width="1.8"></circle>
					<line x1="13.5" y1="13.5" x2="17.5" y2="17.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"></line>
				</svg>
			</button>
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

<div class="search-modal" id="search-modal" hidden aria-hidden="true">
	<button type="button" class="search-modal__close" aria-label="<?php esc_attr_e( 'Close search', 'dankcave' ); ?>" data-search-close>
		<svg viewBox="0 0 20 20" width="18" height="18" aria-hidden="true"><line x1="4" y1="4" x2="16" y2="16" stroke="currentColor" stroke-width="2" stroke-linecap="round"/><line x1="16" y1="4" x2="4" y2="16" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
	</button>
	<div class="search-modal__inner">
		<form class="search-modal__form" role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
			<label class="screen-reader-text" for="search-modal-input"><?php esc_html_e( 'Search the cave', 'dankcave' ); ?></label>
			<svg class="search-modal__icon" viewBox="0 0 20 20" width="26" height="26" aria-hidden="true">
				<circle cx="9" cy="9" r="6" fill="none" stroke="currentColor" stroke-width="1.8"></circle>
				<line x1="13.5" y1="13.5" x2="17.5" y2="17.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"></line>
			</svg>
			<input class="search-modal__input" id="search-modal-input" type="search" name="s" placeholder="<?php esc_attr_e( 'Search products, guides, categories…', 'dankcave' ); ?>" autocomplete="off" data-search-input>
			<button type="submit" class="search-modal__submit"><?php esc_html_e( 'Search', 'dankcave' ); ?></button>
		</form>

		<div class="search-modal__hint" data-search-hint>
			<span class="search-modal__hint-eyebrow"><?php esc_html_e( 'TRY', 'dankcave' ); ?></span>
			<div class="search-modal__suggestions">
				<a href="<?php echo esc_url( home_url( '/?s=bong' ) ); ?>">bong</a>
				<a href="<?php echo esc_url( home_url( '/?s=vape' ) ); ?>">vape</a>
				<a href="<?php echo esc_url( home_url( '/?s=rolling+papers' ) ); ?>">rolling papers</a>
				<a href="<?php echo esc_url( home_url( '/?s=dab+rig' ) ); ?>">dab rig</a>
				<a href="<?php echo esc_url( home_url( '/?s=grinder' ) ); ?>">grinder</a>
			</div>
		</div>

		<div class="search-modal__results" data-search-results hidden></div>
	</div>
</div>
