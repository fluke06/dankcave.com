<?php
/**
 * Theme setup — WordPress + WooCommerce support declarations, image sizes,
 * menus. All of these run once on `after_setup_theme`.
 *
 * @package Dankcave
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function dankcave_setup() {
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'html5', array(
		'search-form',
		'comment-form',
		'comment-list',
		'gallery',
		'caption',
		'style',
		'script',
	) );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'align-wide' );
	add_theme_support( 'editor-styles' );
	add_editor_style( 'assets/css/editor.css' );

	// WooCommerce support + gallery features.
	add_theme_support( 'woocommerce', array(
		'thumbnail_image_width' => 480,
		'single_image_width'    => 900,
		'product_grid'          => array(
			'default_rows'    => 3,
			'min_rows'        => 1,
			'default_columns' => 4,
			'min_columns'     => 2,
			'max_columns'     => 6,
		),
	) );
	add_theme_support( 'wc-product-gallery-zoom' );
	add_theme_support( 'wc-product-gallery-lightbox' );
	add_theme_support( 'wc-product-gallery-slider' );

	// Custom image sizes tuned to the design mockups.
	add_image_size( 'dc-hero',            1600, 900,  true ); // Home hero product cutout
	add_image_size( 'dc-product-card',    720,  720,  true ); // Square product card on listings
	add_image_size( 'dc-category-tile',   640,  480,  true ); // Category lifestyle photo
	add_image_size( 'dc-blog-card',       720,  480,  true ); // Blog card thumb
	add_image_size( 'dc-blog-featured',   1200, 720,  true ); // Blog post hero
	add_image_size( 'dc-editorial',       1200, 800,  true ); // Editorial band image

	register_nav_menus( array(
		'primary'      => __( 'Primary Menu (header pill)',  'dankcave' ),
		'footer-legal' => __( 'Footer legal (Shipping / Returns / Privacy / Terms)', 'dankcave' ),
	) );

	// Enable native custom-logo support so Javid can upload a logo via the Customizer.
	add_theme_support( 'custom-logo', array(
		'height'      => 120,
		'width'       => 480,
		'flex-height' => true,
		'flex-width'  => true,
	) );
}
add_action( 'after_setup_theme', 'dankcave_setup' );

/**
 * Content-width for embedded media (video / iframe fallbacks).
 */
function dankcave_content_width() {
	$GLOBALS['content_width'] = 1200;
}
add_action( 'after_setup_theme', 'dankcave_content_width', 0 );
