<?php
/**
 * Template helper functions used across the theme. Kept small — helpers only,
 * not business logic.
 *
 * @package Dankcave
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Format a price with the WooCommerce currency symbol, if WC is available.
 */
function dankcave_price( $amount ) {
	if ( function_exists( 'wc_price' ) ) {
		return wc_price( $amount );
	}
	return '$' . number_format( (float) $amount, 2 );
}

/**
 * Reading-time helper for blog posts. Matches ToolPlaybook's pattern.
 */
function dankcave_reading_time( $post_id = null ) {
	$post = get_post( $post_id );
	if ( ! $post ) {
		return '';
	}
	$words   = str_word_count( wp_strip_all_tags( $post->post_content ) );
	$minutes = max( 1, (int) ceil( $words / 220 ) );
	return sprintf( _n( '%d min read', '%d min read', $minutes, 'dankcave' ), $minutes );
}
