<?php
/**
 * Pattern: content page hero — clean top banner for About/Contact/legal.
 *
 * @package Dankcave
 */

return <<<'BLOCKS'
<!-- wp:group {"className":"pattern-content-hero","align":"full","style":{"color":{"background":"#f2f0ee"}}} -->
<section class="wp-block-group alignfull pattern-content-hero has-background" style="background:#f2f0ee;padding:80px 48px 40px;">
	<!-- wp:group {"layout":{"type":"constrained","contentSize":"820px"}} -->
	<div class="wp-block-group">
		<!-- wp:paragraph {"style":{"typography":{"fontSize":"12px","fontWeight":"800","letterSpacing":"0.12em"}}} -->
		<p style="font-size:12px;font-weight:800;letter-spacing:0.12em;color:#b08a3c;text-transform:uppercase;">ABOUT</p>
		<!-- /wp:paragraph -->
		<!-- wp:heading {"level":1,"style":{"typography":{"fontSize":"56px","fontWeight":"800","letterSpacing":"-0.03em","lineHeight":"1.02"}}} -->
		<h1 class="wp-block-heading" style="font-size:56px;font-weight:800;letter-spacing:-0.03em;line-height:1.02;margin:8px 0 16px;">Page heading goes here.</h1>
		<!-- /wp:heading -->
		<!-- wp:paragraph {"style":{"typography":{"fontSize":"17px","lineHeight":"1.65"}}} -->
		<p style="font-size:17px;line-height:1.65;color:#5c616a;max-width:52ch;">Short description that sets the tone for the rest of the page. Edit this text and remove the eyebrow if you do not need one.</p>
		<!-- /wp:paragraph -->
	</div>
	<!-- /wp:group -->
</section>
<!-- /wp:group -->
BLOCKS;
