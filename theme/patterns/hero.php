<?php
/**
 * Pattern: broken headline hero — big display type + CTA row.
 *
 * @package Dankcave
 */

return <<<'BLOCKS'
<!-- wp:group {"className":"pattern-hero","align":"full","layout":{"type":"constrained","contentSize":"1180px"}} -->
<section class="wp-block-group alignfull pattern-hero" style="padding:80px 48px 60px;background:#f2f0ee;">
	<!-- wp:paragraph {"className":"pattern-hero__eyebrow"} -->
	<p class="pattern-hero__eyebrow" style="font-size:12px;font-weight:800;letter-spacing:.12em;color:#b08a3c;text-transform:uppercase;margin-bottom:16px;">DANKCAVE · 21+</p>
	<!-- /wp:paragraph -->
	<!-- wp:heading {"level":1,"style":{"typography":{"fontSize":"88px","fontWeight":"800","letterSpacing":"-0.04em","lineHeight":"0.95"}}} -->
	<h1 class="wp-block-heading" style="font-size:88px;font-weight:800;letter-spacing:-0.04em;line-height:0.95;margin:0 0 24px;">Vices,<br>handled with care.</h1>
	<!-- /wp:heading -->
	<!-- wp:paragraph {"style":{"typography":{"fontSize":"18px","lineHeight":"1.6"}}} -->
	<p style="font-size:18px;line-height:1.6;color:#5c616a;max-width:44ch;margin-bottom:32px;">Bongs, rigs, vapes, and the small tools that make them sing. Curated by nerds, shipped discreetly.</p>
	<!-- /wp:paragraph -->
	<!-- wp:buttons {"layout":{"type":"flex","flexWrap":"wrap"}} -->
	<div class="wp-block-buttons">
		<!-- wp:button {"style":{"color":{"background":"#22242a","text":"#ffffff"},"border":{"radius":"14px"},"typography":{"fontWeight":"700"}}} -->
		<div class="wp-block-button"><a class="wp-block-button__link has-text-color has-background wp-element-button" style="border-radius:14px;background:#22242a;color:#fff;font-weight:700;padding:16px 28px;" href="/shop/">Shop everything</a></div>
		<!-- /wp:button -->
		<!-- wp:button {"style":{"color":{"background":"#ffffff","text":"#22242a"},"border":{"radius":"14px"},"typography":{"fontWeight":"700"}}} -->
		<div class="wp-block-button"><a class="wp-block-button__link has-text-color has-background wp-element-button" style="border-radius:14px;background:#fff;color:#22242a;font-weight:700;padding:16px 28px;border:1px solid #dcd7cd;" href="/blog/">Read the journal</a></div>
		<!-- /wp:button -->
	</div>
	<!-- /wp:buttons -->
</section>
<!-- /wp:group -->
BLOCKS;
