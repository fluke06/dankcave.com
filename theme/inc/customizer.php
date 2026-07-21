<?php
/**
 * Customizer sections. Wire editable copy + assets per section as templates land.
 *
 * @package Dankcave
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function dankcave_customize_register( $wp_customize ) {
	// -------------------- Home Hero --------------------
	$wp_customize->add_section( 'dankcave_hero', array(
		'title'       => __( 'Home hero', 'dankcave' ),
		'description' => __( 'Big display type, product image, callout card, and dual-tone CTA at the top of the home page.', 'dankcave' ),
		'priority'    => 30,
	) );

	$fields = array(
		'dankcave_hero_left_line1'    => array( 'label' => __( 'Left headline, line 1', 'dankcave' ),        'default' => 'Sess',    'type' => 'text' ),
		'dankcave_hero_left_line2'    => array( 'label' => __( 'Left headline, line 2', 'dankcave' ),        'default' => 'ions',    'type' => 'text' ),
		'dankcave_hero_right_word'    => array( 'label' => __( 'Right accent word (wine red)', 'dankcave' ), 'default' => 'higher.', 'type' => 'text' ),
		'dankcave_hero_callout_title' => array( 'label' => __( 'Callout card title', 'dankcave' ),           'default' => 'E-Vape One', 'type' => 'text' ),
		'dankcave_hero_callout_body'  => array( 'label' => __( 'Callout card body (line breaks supported)', 'dankcave' ), 'default' => "Ceramic-coil vapor.\nOne-button sessions.", 'type' => 'textarea' ),
		'dankcave_hero_image_alt'     => array( 'label' => __( 'Hero product image alt text', 'dankcave' ),  'default' => 'E-Vape One', 'type' => 'text' ),
		'dankcave_hero_cta_label'     => array( 'label' => __( 'CTA button label', 'dankcave' ),             'default' => 'Discover collection', 'type' => 'text' ),
		'dankcave_hero_cta_url'       => array( 'label' => __( 'CTA button link', 'dankcave' ),              'default' => home_url( '/shop/' ), 'type' => 'url' ),
		'dankcave_hero_lede'          => array( 'label' => __( 'Lede paragraph under CTA', 'dankcave' ),     'default' => 'Curated vapes and glass that blur the line between gear and ritual.', 'type' => 'textarea' ),
	);
	foreach ( $fields as $id => $args ) {
		$wp_customize->add_setting( $id, array(
			'default'           => $args['default'],
			'sanitize_callback' => 'textarea' === $args['type'] ? 'sanitize_textarea_field' : ( 'url' === $args['type'] ? 'esc_url_raw' : 'sanitize_text_field' ),
			'transport'         => 'refresh',
		) );
		$wp_customize->add_control( $id, array(
			'label'   => $args['label'],
			'section' => 'dankcave_hero',
			'type'    => $args['type'],
		) );
	}

	// Hero image (native image control)
	$wp_customize->add_setting( 'dankcave_hero_image', array(
		'default'           => DANKCAVE_URI . 'assets/images/hero-product-placeholder.png',
		'sanitize_callback' => 'esc_url_raw',
		'transport'         => 'refresh',
	) );
	$wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'dankcave_hero_image', array(
		'label'       => __( 'Hero product image', 'dankcave' ),
		'description' => __( 'PNG with transparent background works best. Sits center-right in the hero, rotated 18°.', 'dankcave' ),
		'section'     => 'dankcave_hero',
	) ) );

	// -------------------- Home editorial band --------------------
	$wp_customize->add_section( 'dankcave_editorial', array(
		'title'       => __( 'Home editorial band', 'dankcave' ),
		'description' => __( 'Video-backed dark band with pill badge, big heading, subcopy, and CTA on the left.', 'dankcave' ),
		'priority'    => 34,
	) );
	$editorial_fields = array(
		'dankcave_editorial_badge'     => array( 'label' => __( 'Pill badge (small caps)', 'dankcave' ),   'default' => 'Since 2006', 'type' => 'text' ),
		'dankcave_editorial_heading_1' => array( 'label' => __( 'Heading, line 1', 'dankcave' ),          'default' => 'Twenty years', 'type' => 'text' ),
		'dankcave_editorial_heading_2' => array( 'label' => __( 'Heading, line 2', 'dankcave' ),          'default' => 'at the torch.', 'type' => 'text' ),
		'dankcave_editorial_body'      => array( 'label' => __( 'Body paragraph', 'dankcave' ),           'default' => "We started as borosilicate glassblowers. Every piece we stock today is something we'd keep on our own shelf.", 'type' => 'textarea' ),
		'dankcave_editorial_cta_label' => array( 'label' => __( 'CTA button label', 'dankcave' ),         'default' => 'Read our story', 'type' => 'text' ),
		'dankcave_editorial_cta_url'   => array( 'label' => __( 'CTA button link', 'dankcave' ),          'default' => home_url( '/about/' ), 'type' => 'url' ),
		'dankcave_editorial_video_url' => array( 'label' => __( 'Background video URL (mp4)', 'dankcave' ), 'default' => DANKCAVE_URI . 'assets/videos/editorial-band-placeholder.mp4', 'type' => 'url' ),
		'dankcave_editorial_poster'    => array( 'label' => __( 'Fallback poster image URL', 'dankcave' ), 'default' => '', 'type' => 'url' ),
	);
	foreach ( $editorial_fields as $id => $args ) {
		$wp_customize->add_setting( $id, array(
			'default'           => $args['default'],
			'sanitize_callback' => 'textarea' === $args['type'] ? 'sanitize_textarea_field' : ( 'url' === $args['type'] ? 'esc_url_raw' : 'sanitize_text_field' ),
			'transport'         => 'refresh',
		) );
		$wp_customize->add_control( $id, array(
			'label'   => $args['label'],
			'section' => 'dankcave_editorial',
			'type'    => $args['type'],
		) );
	}

	// -------------------- Newsletter (footer band) --------------------
	$wp_customize->add_section( 'dankcave_newsletter', array(
		'title'    => __( 'Footer newsletter', 'dankcave' ),
		'priority' => 40,
	) );
	$news_fields = array(
		'dankcave_newsletter_heading'  => array( 'label' => __( 'Heading', 'dankcave' ),  'default' => 'Vices, handled with care.', 'type' => 'text' ),
		'dankcave_newsletter_subcopy'  => array( 'label' => __( 'Subcopy', 'dankcave' ),  'default' => "Drops, deals, and the occasional bad influence — in your inbox, 21+ only.", 'type' => 'textarea' ),
		'dankcave_newsletter_shortcode'=> array( 'label' => __( 'MC4WP form shortcode (e.g. [mc4wp_form id="123"])', 'dankcave' ), 'default' => '', 'type' => 'text' ),
	);
	foreach ( $news_fields as $id => $args ) {
		$wp_customize->add_setting( $id, array(
			'default'           => $args['default'],
			'sanitize_callback' => 'textarea' === $args['type'] ? 'sanitize_textarea_field' : 'wp_kses_post',
			'transport'         => 'refresh',
		) );
		$wp_customize->add_control( $id, array(
			'label'       => $args['label'],
			'section'     => 'dankcave_newsletter',
			'type'        => $args['type'],
			'description' => isset( $args['description'] ) ? $args['description'] : '',
		) );
	}

	// -------------------- Footer copyright --------------------
	$wp_customize->add_section( 'dankcave_footer', array(
		'title'    => __( 'Footer legal bar', 'dankcave' ),
		'priority' => 45,
	) );
	$wp_customize->add_setting( 'dankcave_footer_copyright', array(
		'default'           => '',
		'sanitize_callback' => 'sanitize_text_field',
		'transport'         => 'refresh',
	) );
	$wp_customize->add_control( 'dankcave_footer_copyright', array(
		'label'       => __( 'Copyright line (leave empty for default: © {year} {site name} · Adults 21+ · Tracy, CA)', 'dankcave' ),
		'section'     => 'dankcave_footer',
		'type'        => 'text',
	) );
}
add_action( 'customize_register', 'dankcave_customize_register' );
