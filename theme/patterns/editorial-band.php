<?php
/**
 * Pattern: editorial band — dark section with image + text side by side.
 *
 * @package Dankcave
 */

return <<<'BLOCKS'
<!-- wp:group {"className":"pattern-editorial","align":"full","style":{"color":{"background":"#1c1714","text":"#f2ede8"}}} -->
<section class="wp-block-group alignfull pattern-editorial has-text-color has-background" style="background:#1c1714;color:#f2ede8;padding:80px 48px;">
	<!-- wp:columns {"verticalAlignment":"center"} -->
	<div class="wp-block-columns are-vertically-aligned-center">
		<!-- wp:column {"verticalAlignment":"center"} -->
		<div class="wp-block-column is-vertically-aligned-center">
			<!-- wp:paragraph {"style":{"typography":{"fontSize":"12px","fontWeight":"800","letterSpacing":"0.12em"}}} -->
			<p style="font-size:12px;font-weight:800;letter-spacing:0.12em;color:#f8c913;text-transform:uppercase;">EDITORIAL</p>
			<!-- /wp:paragraph -->
			<!-- wp:heading {"level":2,"style":{"typography":{"fontSize":"44px","fontWeight":"800","letterSpacing":"-0.03em","lineHeight":"1.05"},"color":{"text":"#f2ede8"}}} -->
			<h2 class="wp-block-heading has-text-color" style="color:#f2ede8;font-size:44px;font-weight:800;letter-spacing:-0.03em;line-height:1.05;margin:8px 0 16px;">Small tools. Big flavor.</h2>
			<!-- /wp:heading -->
			<!-- wp:paragraph {"style":{"typography":{"fontSize":"16px","lineHeight":"1.7"}}} -->
			<p style="font-size:16px;line-height:1.7;color:#b7ada3;max-width:44ch;">A one-button vape, a hand-blown rig, or a five-buck pack of papers — the good stuff, no shelf padding.</p>
			<!-- /wp:paragraph -->
			<!-- wp:buttons -->
			<div class="wp-block-buttons">
				<!-- wp:button {"style":{"color":{"background":"#993331","text":"#ffffff"},"border":{"radius":"14px"}}} -->
				<div class="wp-block-button"><a class="wp-block-button__link has-text-color has-background wp-element-button" style="background:#993331;color:#fff;border-radius:14px;font-weight:700;padding:14px 24px;" href="/shop/">Shop the essentials</a></div>
				<!-- /wp:button -->
			</div>
			<!-- /wp:buttons -->
		</div>
		<!-- /wp:column -->
		<!-- wp:column -->
		<div class="wp-block-column">
			<!-- wp:image {"sizeSlug":"large","className":"pattern-editorial__image","style":{"border":{"radius":"20px"}}} -->
			<figure class="wp-block-image size-large pattern-editorial__image" style="border-radius:20px;overflow:hidden;"><img alt="Editorial image" /></figure>
			<!-- /wp:image -->
		</div>
		<!-- /wp:column -->
	</div>
	<!-- /wp:columns -->
</section>
<!-- /wp:group -->
BLOCKS;
