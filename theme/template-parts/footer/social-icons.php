<?php
/**
 * Social icons row — reads customizer URLs and prints an <a> with an inline
 * SVG icon for each populated network. Skips anything blank so unused
 * networks don't render.
 *
 * @package Dankcave
 */

$networks = array(
	'instagram' => array(
		'label'   => __( 'Instagram', 'dankcave' ),
		'default' => 'https://www.instagram.com/dankcaveshop/',
		'svg'     => '<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="3" width="18" height="18" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r=".9" fill="currentColor" stroke="none"/></svg>',
	),
	'facebook'  => array(
		'label'   => __( 'Facebook', 'dankcave' ),
		'default' => 'https://www.facebook.com/dankcave',
		'svg'     => '<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M14 9V7.5A1.5 1.5 0 0 1 15.5 6H17V3h-2.5A4 4 0 0 0 10.5 7v2H8v3h2.5v9h3.5v-9H16.5l.5-3H14z"/></svg>',
	),
	'tiktok'    => array(
		'label'   => __( 'TikTok', 'dankcave' ),
		'default' => '',
		'svg'     => '<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M15 3v10.2a3.3 3.3 0 1 1-3.3-3.3"/><path d="M15 3a5 5 0 0 0 5 5"/></svg>',
	),
	'twitter'   => array(
		'label'   => __( 'X (Twitter)', 'dankcave' ),
		'default' => '',
		'svg'     => '<svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor" aria-hidden="true"><path d="M17.5 3h2.9l-6.4 7.3L21.5 21h-5.9l-4.6-6-5.3 6H2.8l6.9-7.9L2 3h6l4.1 5.4L17.5 3zm-1 16.2h1.6L7.5 4.7H5.8l10.7 14.5z"/></svg>',
	),
	'youtube'   => array(
		'label'   => __( 'YouTube', 'dankcave' ),
		'default' => '',
		'svg'     => '<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="6" width="18" height="12" rx="3"/><path d="M10.5 9.75v4.5l4-2.25-4-2.25z" fill="currentColor" stroke="none"/></svg>',
	),
);

$any = false;
foreach ( $networks as $key => $meta ) {
	$url = trim( (string) get_theme_mod( 'dankcave_social_' . $key, $meta['default'] ) );
	if ( ! $url ) { continue; }
	if ( ! $any ) {
		echo '<ul class="dc-social" aria-label="' . esc_attr__( 'Social links', 'dankcave' ) . '">';
		$any = true;
	}
	printf(
		'<li><a class="dc-social__link" href="%s" target="_blank" rel="noopener" aria-label="%s">%s</a></li>',
		esc_url( $url ),
		esc_attr( $meta['label'] ),
		$meta['svg'] // phpcs:ignore
	);
}
if ( $any ) { echo '</ul>'; }
