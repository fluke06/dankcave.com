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

	// Patterns to build (see CLAUDE.md § Scope):
	// 1.  hero              — Big display type + product cutout
	// 2.  product-grid-4    — Product cards, 4-up, pastel backgrounds
	// 3.  category-grid-5   — Category tiles, lifestyle photography
	// 4.  editorial-band    — Image + overlay text storytelling
	// 5.  product-row       — "Popular / Trending / New" row with See all →
	// 6.  blog-row          — 3-up blog card row
	// 7.  black-cta-band    — "No fuss. Just good gear." band
	// 8.  stats-row         — $50+, 100%, 30 day, 20+ yrs
	// 9.  newsletter-band   — Black band with MC4WP form
	// 10. section-header    — Eyebrow + headline used above each section
	// 11. content-hero      — Simple page hero (About/Contact/legal)
	//
	// Each pattern's block markup lives in patterns/<name>.php as a returned string.
	// Uncomment the loop below once individual pattern files exist.

	// $patterns = array(
	// 	'hero'            => __( 'Hero — Broken Headline',   'dankcave' ),
	// 	'product-grid-4'  => __( 'Product Grid (4-up)',      'dankcave' ),
	// 	'category-grid-5' => __( 'Category Grid (5-cell)',   'dankcave' ),
	// 	'editorial-band'  => __( 'Editorial Storytelling',   'dankcave' ),
	// 	'product-row'     => __( 'Product Row + See All',    'dankcave' ),
	// 	'blog-row'        => __( 'Blog Card Row (3-up)',     'dankcave' ),
	// 	'black-cta-band'  => __( 'Black CTA Band',           'dankcave' ),
	// 	'stats-row'       => __( 'Stats Row',                'dankcave' ),
	// 	'newsletter-band' => __( 'Newsletter Subscribe',     'dankcave' ),
	// 	'section-header'  => __( 'Section Header + Eyebrow', 'dankcave' ),
	// 	'content-hero'    => __( 'Content Page Hero',        'dankcave' ),
	// );
	// foreach ( $patterns as $slug => $title ) {
	// 	$file = DANKCAVE_DIR . 'patterns/' . $slug . '.php';
	// 	if ( ! file_exists( $file ) ) {
	// 		continue;
	// 	}
	// 	$content = require $file;
	// 	register_block_pattern(
	// 		'dankcave/' . $slug,
	// 		array(
	// 			'title'      => $title,
	// 			'categories' => array( 'dankcave' ),
	// 			'content'    => $content,
	// 		)
	// 	);
	// }
}
add_action( 'init', 'dankcave_register_patterns' );
