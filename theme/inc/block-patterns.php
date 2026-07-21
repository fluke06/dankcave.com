<?php
/**
 * Gutenberg block patterns — a "Dankcave" category in the block inserter
 * with prebuilt sections that reuse the theme's design tokens.
 *
 * See CLAUDE.md § Scope for the full pattern list.
 *
 * @package Dankcave
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function dankcave_register_pattern_category() {
	if ( ! function_exists( 'register_block_pattern_category' ) ) {
		return;
	}
	register_block_pattern_category(
		'dankcave',
		array(
			'label'       => __( 'Dankcave', 'dankcave' ),
			'description' => __( 'Prebuilt sections styled for the Dankcave theme. Insert to compose new pages using the site\'s visual language.', 'dankcave' ),
		)
	);
}
add_action( 'init', 'dankcave_register_pattern_category' );

function dankcave_register_patterns() {
	if ( ! function_exists( 'register_block_pattern' ) ) {
		return;
	}

	$patterns = array(
		'hero'            => array( 'title' => __( 'Hero — Broken Headline',   'dankcave' ), 'description' => __( 'Big display type with CTA row. Best used at the very top of the home page.', 'dankcave' ), 'keywords' => array( 'hero', 'headline', 'landing' ) ),
		'editorial-band'  => array( 'title' => __( 'Editorial Band',           'dankcave' ), 'description' => __( 'Dark two-column band with image and story copy.', 'dankcave' ), 'keywords' => array( 'editorial', 'story', 'band' ) ),
		'product-row'     => array( 'title' => __( 'Product Row + See All',    'dankcave' ), 'description' => __( 'Product row using the WooCommerce shortcode with a titled section head.', 'dankcave' ), 'keywords' => array( 'products', 'shop', 'row' ) ),
		'blog-row'        => array( 'title' => __( 'Blog Card Row (3-up)',     'dankcave' ), 'description' => __( 'Three recent posts styled as blog cards. Uses the Query Loop block so it stays current.', 'dankcave' ), 'keywords' => array( 'blog', 'journal', 'query loop' ) ),
		'black-cta-band'  => array( 'title' => __( 'Black CTA Band',           'dankcave' ), 'description' => __( 'Full-width black band with headline and CTA. Good closer.', 'dankcave' ), 'keywords' => array( 'cta', 'band', 'closing' ) ),
		'stats-row'       => array( 'title' => __( 'Stats Row',                'dankcave' ), 'description' => __( 'Four-column stats row: shipping, verification, returns, tenure.', 'dankcave' ), 'keywords' => array( 'stats', 'trust', 'numbers' ) ),
		'newsletter-band' => array( 'title' => __( 'Newsletter Subscribe',     'dankcave' ), 'description' => __( 'Dark newsletter band with MC4WP shortcode placeholder.', 'dankcave' ), 'keywords' => array( 'newsletter', 'subscribe', 'email' ) ),
		'section-header'  => array( 'title' => __( 'Section Header + Eyebrow', 'dankcave' ), 'description' => __( 'Reusable eyebrow + headline + link row for above any grid.', 'dankcave' ), 'keywords' => array( 'section', 'header', 'eyebrow' ) ),
		'content-hero'    => array( 'title' => __( 'Content Page Hero',        'dankcave' ), 'description' => __( 'Simple page hero for About, Contact, and legal pages.', 'dankcave' ), 'keywords' => array( 'about', 'page', 'hero' ) ),
	);

	foreach ( $patterns as $slug => $meta ) {
		$file = DANKCAVE_DIR . 'patterns/' . $slug . '.php';
		if ( ! file_exists( $file ) ) {
			continue;
		}
		$content = require $file;
		register_block_pattern(
			'dankcave/' . $slug,
			array(
				'title'       => $meta['title'],
				'description' => isset( $meta['description'] ) ? $meta['description'] : '',
				'keywords'    => isset( $meta['keywords'] ) ? $meta['keywords'] : array(),
				'categories'  => array( 'dankcave' ),
				'content'     => $content,
			)
		);
	}
}
add_action( 'init', 'dankcave_register_patterns' );
