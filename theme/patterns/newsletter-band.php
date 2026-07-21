<?php
/**
 * Pattern: dark newsletter band with MC4WP shortcode placeholder.
 *
 * @package Dankcave
 */

return <<<'BLOCKS'
<!-- wp:group {"className":"newsletter-band","align":"full","style":{"color":{"background":"#22242a","text":"#ffffff"}}} -->
<section class="wp-block-group alignfull newsletter-band has-text-color has-background" style="background:#22242a;color:#fff;padding:40px 48px;">
	<!-- wp:columns {"verticalAlignment":"center"} -->
	<div class="wp-block-columns are-vertically-aligned-center">
		<!-- wp:column {"verticalAlignment":"center","width":"55%"} -->
		<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:55%;">
			<!-- wp:heading {"level":2,"style":{"typography":{"fontSize":"26px","fontWeight":"800","letterSpacing":"-0.02em"},"color":{"text":"#ffffff"}}} -->
			<h2 class="wp-block-heading has-text-color" style="color:#fff;font-size:26px;font-weight:800;letter-spacing:-0.02em;margin:0 0 8px;">Vices, handled with care.</h2>
			<!-- /wp:heading -->
			<!-- wp:paragraph {"style":{"typography":{"fontSize":"14.5px","lineHeight":"1.6"}}} -->
			<p style="font-size:14.5px;line-height:1.6;color:#8a8e96;margin:0;">Drops, deals, and the occasional bad influence — in your inbox, 21+ only.</p>
			<!-- /wp:paragraph -->
		</div>
		<!-- /wp:column -->
		<!-- wp:column {"verticalAlignment":"center"} -->
		<div class="wp-block-column is-vertically-aligned-center">
			<!-- wp:shortcode -->
			[mc4wp_form id="0"]
			<!-- /wp:shortcode -->
			<!-- wp:paragraph {"style":{"typography":{"fontSize":"12px"}}} -->
			<p style="font-size:12px;color:#8a8e96;margin:12px 0 0;">Placeholder — replace MC4WP form ID after saving.</p>
			<!-- /wp:paragraph -->
		</div>
		<!-- /wp:column -->
	</div>
	<!-- /wp:columns -->
</section>
<!-- /wp:group -->
BLOCKS;
