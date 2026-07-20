<?php
/**
 * Site header — opening HTML, <head>, and site chrome (top nav).
 * TODO: Build the actual header markup per design/*.dc.html.
 *
 * @package Dankcave
 */
?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="https://gmpg.org/xfn/11">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
	<?php wp_body_open(); ?>

	<header class="site-header">
		<div class="wrap">
			<a class="site-brand" href="<?php echo esc_url( home_url( '/' ) ); ?>">
				<?php bloginfo( 'name' ); ?>
			</a>
			<nav class="primary-nav" aria-label="<?php esc_attr_e( 'Primary', 'dankcave' ); ?>">
				<?php
				wp_nav_menu( array(
					'theme_location' => 'primary',
					'container'      => false,
					'menu_class'     => 'primary-nav__list',
					'fallback_cb'    => false,
					'depth'          => 2,
				) );
				?>
			</nav>
		</div>
	</header>

	<div class="site-content">
