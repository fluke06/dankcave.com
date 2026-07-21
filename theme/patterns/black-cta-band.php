<?php
/**
 * Pattern: black CTA band — "No fuss. Just good gear." style.
 *
 * @package Dankcave
 */

return <<<'BLOCKS'
<!-- wp:group {"className":"pattern-cta","align":"full","style":{"color":{"background":"#22242a","text":"#ffffff"}}} -->
<section class="wp-block-group alignfull pattern-cta has-text-color has-background" style="background:#22242a;color:#fff;padding:64px 48px;text-align:center;">
	<!-- wp:paragraph {"style":{"typography":{"fontSize":"12px","fontWeight":"800","letterSpacing":"0.14em"}}} -->
	<p style="font-size:12px;font-weight:800;letter-spacing:0.14em;color:#f8c913;text-transform:uppercase;">NO FUSS</p>
	<!-- /wp:paragraph -->
	<!-- wp:heading {"level":2,"style":{"typography":{"fontSize":"48px","fontWeight":"800","letterSpacing":"-0.03em","lineHeight":"1.1"},"color":{"text":"#ffffff"}}} -->
	<h2 class="wp-block-heading has-text-color" style="color:#fff;font-size:48px;font-weight:800;letter-spacing:-0.03em;line-height:1.1;margin:6px auto 22px;max-width:24ch;">No fuss. Just good gear.</h2>
	<!-- /wp:heading -->
	<!-- wp:buttons {"layout":{"type":"flex","justifyContent":"center"}} -->
	<div class="wp-block-buttons">
		<!-- wp:button {"style":{"color":{"background":"#f8c913","text":"#22242a"},"border":{"radius":"14px"}}} -->
		<div class="wp-block-button"><a class="wp-block-button__link has-text-color has-background wp-element-button" style="background:#f8c913;color:#22242a;border-radius:14px;font-weight:700;padding:16px 28px;" href="/shop/">Take me shopping</a></div>
		<!-- /wp:button -->
	</div>
	<!-- /wp:buttons -->
</section>
<!-- /wp:group -->
BLOCKS;
