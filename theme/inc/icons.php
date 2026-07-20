<?php
/**
 * Custom SVG icon registry. Same pattern as ToolPlaybook — icons live in a PHP
 * array so we get zero HTTP requests + inheritable currentColor styling.
 *
 * Usage:  dankcave_the_icon( 'cart' );
 *
 * @package Dankcave
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function dankcave_icons() {
	return array(
		// Icons added as we build. Every SVG here should use stroke="currentColor"
		// or fill="currentColor" so it inherits the surrounding text color.
		// 'cart'  => '<svg viewBox="0 0 24 24"...></svg>',
		// 'search'=> '<svg viewBox="0 0 24 24"...></svg>',
	);
}

function dankcave_icon( $name ) {
	$icons = dankcave_icons();
	return isset( $icons[ $name ] ) ? $icons[ $name ] : '';
}

function dankcave_the_icon( $name ) {
	echo dankcave_icon( $name ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}
