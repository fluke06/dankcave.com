<?php
/**
 * Performance / Core Web Vitals — LCP, CLS, INP tuning.
 *
 * Focuses on cooperating with the plugins that would otherwise hurt LCP on
 * mobile: Smush lazy-load, Autoptimize, Jetpack, W3 Total Cache. We opt out
 * of lazy loading for the first image in each product/blog card grid and for
 * template-authored hero images (they're above the fold and are the LCP
 * element on their respective templates).
 *
 * @package Dankcave
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * Smush: don't lazy-load images with the .skip-lazy or .no-lazyload class.
 * Smush's filter passes ($skip, $src) but the exact filter name changed with
 * versions; register both to cover 3.x and 4.x.
 */
add_filter( 'wp_smush_should_skip_lazy_load', 'dankcave_perf_smush_skip', 10, 2 );
add_filter( 'smush_skip_image_from_lazy_load', 'dankcave_perf_smush_skip', 10, 2 );
function dankcave_perf_smush_skip( $skip, $img_html = '' ) {
	if ( is_string( $img_html ) && ( strpos( $img_html, 'skip-lazy' ) !== false || strpos( $img_html, 'no-lazyload' ) !== false || strpos( $img_html, 'fetchpriority="high"' ) !== false ) ) {
		return true;
	}
	return $skip;
}

/**
 * Autoptimize lazy load: same treatment via its noptimize / skip filters.
 */
add_filter( 'autoptimize_filter_imgopt_do_lazyload', '__return_true' );
add_filter( 'autoptimize_filter_imgopt_lazyload_exclude_array', 'dankcave_perf_lazy_exclude' );
function dankcave_perf_lazy_exclude( $exclusions ) {
	if ( ! is_array( $exclusions ) ) { $exclusions = array(); }
	$exclusions[] = 'skip-lazy';
	$exclusions[] = 'no-lazyload';
	$exclusions[] = 'fetchpriority="high"';
	return $exclusions;
}

/**
 * WordPress core lazy loading: opt out for images that already carry
 * fetchpriority=high or the skip-lazy class.
 */
add_filter( 'wp_img_tag_add_loading_attr', 'dankcave_perf_core_lazy', 10, 3 );
function dankcave_perf_core_lazy( $value, $image, $context ) {
	if ( is_string( $image ) && ( strpos( $image, 'skip-lazy' ) !== false || strpos( $image, 'fetchpriority="high"' ) !== false ) ) {
		return false;
	}
	return $value;
}

/**
 * Preload LCP images on the templates where we know what the LCP element is.
 * Puts a <link rel=preload as=image> in <head> so the browser can start the
 * fetch immediately (before HTML parser reaches the img tag).
 */
add_action( 'wp_head', 'dankcave_perf_preload_lcp', 4 );
function dankcave_perf_preload_lcp() {
	$candidates = array();

	if ( ( is_front_page() || is_home() ) && ! is_paged() ) {
		// Home hero product image
		$hero_img = get_theme_mod( 'dankcave_hero_image', DANKCAVE_URI . 'assets/images/hero-product-placeholder.png' );
		if ( $hero_img ) { $candidates[] = $hero_img; }
	}
	if ( is_singular( 'product' ) && function_exists( 'wc_get_product' ) ) {
		$product = wc_get_product( get_the_ID() );
		if ( $product && $product->get_image_id() ) {
			$img = wp_get_attachment_image_url( $product->get_image_id(), 'large' );
			if ( $img ) { $candidates[] = $img; }
		}
	}
	if ( is_singular( 'post' ) && has_post_thumbnail() ) {
		$img = wp_get_attachment_image_url( get_post_thumbnail_id(), 'large' );
		if ( $img ) { $candidates[] = $img; }
	}

	foreach ( $candidates as $img ) {
		printf(
			'<link rel="preload" as="image" href="%s" fetchpriority="high" />' . "\n",
			esc_url( $img )
		);
	}
}

/**
 * Add resource hints for the CDN / uploads host so the browser can open the
 * connection in parallel with HTML parsing (saves ~100-200ms on cold hits).
 */
add_filter( 'wp_resource_hints', 'dankcave_perf_resource_hints', 10, 2 );
function dankcave_perf_resource_hints( $hints, $relation ) {
	if ( 'preconnect' === $relation ) {
		$hints[] = array( 'href' => site_url(), 'crossorigin' );
	}
	return $hints;
}
