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
		'hero'                  => array( 'title' => __( 'Hero — Broken Headline',   'dankcave' ), 'description' => __( 'Big display type with CTA row. Best used at the very top of the home page.', 'dankcave' ), 'keywords' => array( 'hero', 'headline', 'landing' ) ),
		'editorial-band'        => array( 'title' => __( 'Editorial Band',           'dankcave' ), 'description' => __( 'Dark two-column band with image and story copy.', 'dankcave' ), 'keywords' => array( 'editorial', 'story', 'band' ) ),
		'product-row'           => array( 'title' => __( 'Product Row + See All',    'dankcave' ), 'description' => __( 'Product row using the WooCommerce shortcode with a titled section head.', 'dankcave' ), 'keywords' => array( 'products', 'shop', 'row' ) ),
		'blog-row'              => array( 'title' => __( 'Blog Card Row (3-up)',     'dankcave' ), 'description' => __( 'Three recent posts styled as blog cards. Uses the Query Loop block so it stays current.', 'dankcave' ), 'keywords' => array( 'blog', 'journal', 'query loop' ) ),
		'black-cta-band'        => array( 'title' => __( 'Black CTA Band',           'dankcave' ), 'description' => __( 'Full-width black band with headline and CTA. Good closer.', 'dankcave' ), 'keywords' => array( 'cta', 'band', 'closing' ) ),
		'stats-row'             => array( 'title' => __( 'Stats Row',                'dankcave' ), 'description' => __( 'Four-column stats row: shipping, verification, returns, tenure.', 'dankcave' ), 'keywords' => array( 'stats', 'trust', 'numbers' ) ),
		'newsletter-band'       => array( 'title' => __( 'Newsletter Subscribe',     'dankcave' ), 'description' => __( 'Dark newsletter band with MC4WP shortcode placeholder.', 'dankcave' ), 'keywords' => array( 'newsletter', 'subscribe', 'email' ) ),
		'section-header'        => array( 'title' => __( 'Section Header + Eyebrow', 'dankcave' ), 'description' => __( 'Reusable eyebrow + headline + link row for above any grid.', 'dankcave' ), 'keywords' => array( 'section', 'header', 'eyebrow' ) ),
		'content-hero'          => array( 'title' => __( 'Content Page Hero',        'dankcave' ), 'description' => __( 'Simple page hero for About, Contact, and legal pages.', 'dankcave' ), 'keywords' => array( 'about', 'page', 'hero' ) ),
		'about-hero'            => array( 'title' => __( 'About — Dark Radial Hero', 'dankcave' ), 'description' => __( 'Dark radial-gradient hero with gold eyebrow and big display headline. Opens the About page.', 'dankcave' ), 'keywords' => array( 'about', 'hero', 'dark' ) ),
		'commitment-split'      => array( 'title' => __( 'About — Commitment Split', 'dankcave' ), 'description' => __( 'Two-column: narrative copy on the left, 2×2 category tile grid on the right.', 'dankcave' ), 'keywords' => array( 'about', 'commitment', 'categories' ) ),
		'why-choose-cards'      => array( 'title' => __( 'About — Why Choose Cards','dankcave' ),  'description' => __( 'Three feature cards on white with wine-red accent bars.', 'dankcave' ), 'keywords' => array( 'about', 'features', 'why', 'cards' ) ),
		'category-showcase'     => array( 'title' => __( 'About — Category Showcase','dankcave' ), 'description' => __( 'Asymmetric 7-tile category mosaic with bulleted product-type callouts.', 'dankcave' ), 'keywords' => array( 'about', 'categories', 'showcase', 'grid' ) ),
		'customer-satisfaction' => array( 'title' => __( 'About — Satisfaction Band','dankcave' ), 'description' => __( 'Dark two-column band with narrative + CTA on the left and 2×2 stat cards on the right.', 'dankcave' ), 'keywords' => array( 'about', 'satisfaction', 'stats' ) ),
		'community-cta'         => array( 'title' => __( 'About — Community CTA',    'dankcave' ), 'description' => __( 'Centered closer with eyebrow, headline, intro and two buttons.', 'dankcave' ), 'keywords' => array( 'about', 'cta', 'community' ) ),
		'contact-split'         => array( 'title' => __( 'Contact — Form + FAQ',     'dankcave' ), 'description' => __( 'Two-column contact page: form on the left (uses Contact Form 7 shortcode), FAQ accordion on the right.', 'dankcave' ), 'keywords' => array( 'contact', 'form', 'faq' ) ),
		'page-privacy'          => array( 'title' => __( 'Legal — Privacy Policy',    'dankcave' ), 'description' => __( 'Full Privacy Policy page: display hero, auto-numbered sections, dark accent card. Content lives in blocks so Javid can edit each section from the editor.', 'dankcave' ), 'keywords' => array( 'legal', 'privacy', 'gdpr' ) ),
		'page-terms'            => array( 'title' => __( 'Legal — Terms of Service', 'dankcave' ),  'description' => __( 'Full Terms page: hero, 21+ warning card, surgeon-general warning cards, and 16 numbered sections. All blocks; auto-numbered via CSS counter.', 'dankcave' ), 'keywords' => array( 'legal', 'terms', 'tos' ) ),
		'page-shipping'         => array( 'title' => __( 'Legal — Shipping Policy',  'dankcave' ),  'description' => __( 'Full Shipping Policy page: hero, 6 numbered sections including a dark damage-claim callout.', 'dankcave' ), 'keywords' => array( 'legal', 'shipping', 'delivery' ) ),
		'page-returns'          => array( 'title' => __( 'Legal — Returns & Refunds','dankcave' ),  'description' => __( 'Full Returns page: hero, 7 numbered sections covering RMA process, damage claims, refund timing.', 'dankcave' ), 'keywords' => array( 'legal', 'returns', 'refunds' ) ),
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
