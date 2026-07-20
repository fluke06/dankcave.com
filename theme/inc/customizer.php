<?php
/**
 * Customizer sections. Populated as templates are built and their
 * editable fields are identified.
 *
 * @package Dankcave
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function dankcave_customize_register( $wp_customize ) {
	// TODO: Register sections + settings as templates are built.
	// Common Customizer targets planned:
	//   - Header CTA copy
	//   - Newsletter band headline / subcopy / MC4WP shortcode
	//   - Stats row values ($50+, 100%, 30 day, 20+ yrs)
	//   - Editorial band headline / subcopy / image
	//   - Homepage featured category picks
	//   - Footer tagline + social links
}
add_action( 'customize_register', 'dankcave_customize_register' );
