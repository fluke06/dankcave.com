<?php
/**
 * Detach registered Dankcave patterns embedded in the About + Contact posts.
 * Replaces every `<!-- wp:pattern {"slug":"dankcave/..."} /-->` reference with
 * the pattern's resolved block markup, so the content becomes native editable
 * blocks in Gutenberg.
 */

$pages = array(
	4400 => 'About',   // /about-us/
	4439 => 'Contact', // /contact-us/
);

$registry = WP_Block_Patterns_Registry::get_instance();

foreach ( $pages as $post_id => $label ) {
	$post = get_post( $post_id );
	if ( ! $post ) { echo "MISS $label ($post_id)\n"; continue; }

	$before = $post->post_content;

	// Match self-closing pattern references: <!-- wp:pattern {"slug":"dankcave/xxx"} /-->
	$after = preg_replace_callback(
		'/<!--\s*wp:pattern\s+(\{[^}]+\})\s*\/-->/',
		function ( $m ) use ( $registry ) {
			$attrs = json_decode( $m[1], true );
			if ( empty( $attrs['slug'] ) ) { return $m[0]; }
			$pattern = $registry->get_registered( $attrs['slug'] );
			if ( ! $pattern || empty( $pattern['content'] ) ) { return $m[0]; }
			return $pattern['content'];
		},
		$before
	);

	$before_refs = substr_count( $before, '<!-- wp:pattern' );
	$after_refs  = substr_count( $after, '<!-- wp:pattern' );

	wp_update_post( array(
		'ID'           => $post_id,
		'post_content' => $after,
	) );

	echo "$label ($post_id): $before_refs pattern refs → $after_refs after detach (bytes " . strlen( $before ) . " → " . strlen( $after ) . ")\n";
}
