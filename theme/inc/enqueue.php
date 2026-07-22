<?php
/**
 * Front-end and editor asset enqueue.
 *
 * @package Dankcave
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function dankcave_enqueue_assets() {
	// Self-hosted fonts (only the ones actually used — trimmed from Google Fonts).
	wp_enqueue_style(
		'dankcave-fonts',
		DANKCAVE_URI . 'assets/css/fonts.css',
		array(),
		DANKCAVE_VERSION
	);

	// Main theme stylesheet.
	wp_enqueue_style(
		'dankcave',
		DANKCAVE_URI . 'assets/css/theme.css',
		array( 'dankcave-fonts' ),
		DANKCAVE_VERSION
	);

	// WooCommerce-specific styles, only load when WooCommerce is around.
	if ( class_exists( 'WooCommerce' ) ) {
		wp_enqueue_style(
			'dankcave-woocommerce',
			DANKCAVE_URI . 'assets/css/woocommerce.css',
			array( 'dankcave' ),
			DANKCAVE_VERSION
		);
	}

	// Theme JS — deferred, no jQuery dependency.
	wp_enqueue_script(
		'dankcave',
		DANKCAVE_URI . 'assets/js/theme.js',
		array(),
		DANKCAVE_VERSION,
		array( 'in_footer' => true, 'strategy' => 'defer' )
	);

	// WooCommerce JS.
	if ( class_exists( 'WooCommerce' ) ) {
		wp_enqueue_script(
			'dankcave-woocommerce',
			DANKCAVE_URI . 'assets/js/woocommerce.js',
			array( 'dankcave' ),
			DANKCAVE_VERSION,
			array( 'in_footer' => true, 'strategy' => 'defer' )
		);

		// Load WC's add-to-cart-variation script on EVERY page so the quickview
		// modal can wire up variation dropdowns for variable products (WC would
		// normally only load it on the single product template).
		wp_enqueue_script( 'wc-add-to-cart-variation' );
	}

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'dankcave_enqueue_assets' );

/**
 * Preload the fonts that render above the fold. Trims LCP.
 * Only preload fonts that appear in the hero / initial viewport:
 *   - Gabarito (variable) — dominant body + UI copy
 *   - Bricolage Grotesque 700 — display headings
 * Instrument Serif ships without preload (only used further down in editorial blocks).
 */
function dankcave_preload_fonts() {
	$fonts = array(
		'gabarito-variable.woff2',
		'bricolagegrotesque-700.woff2',
	);
	foreach ( $fonts as $f ) {
		$path = DANKCAVE_DIR . 'assets/fonts/' . $f;
		if ( ! file_exists( $path ) ) {
			continue;
		}
		printf(
			'<link rel="preload" as="font" type="font/woff2" crossorigin="anonymous" href="%s">' . "\n",
			esc_url( DANKCAVE_URI . 'assets/fonts/' . $f )
		);
	}
}
add_action( 'wp_head', 'dankcave_preload_fonts', 2 );

/**
 * Editor styles — scope the theme's design tokens into the Gutenberg editor
 * so patterns render correctly during page composition.
 */
function dankcave_editor_assets() {
	wp_enqueue_style(
		'dankcave-editor',
		DANKCAVE_URI . 'assets/css/editor.css',
		array(),
		DANKCAVE_VERSION
	);
}
add_action( 'enqueue_block_editor_assets', 'dankcave_editor_assets' );
