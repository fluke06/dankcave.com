<?php
/**
 * Pattern: blog row — 3 latest posts via the Query Loop block.
 *
 * @package Dankcave
 */

return <<<'BLOCKS'
<!-- wp:group {"className":"pattern-blog-row","align":"wide","style":{"spacing":{"padding":{"top":"56px","bottom":"56px","left":"48px","right":"48px"}}}} -->
<section class="wp-block-group alignwide pattern-blog-row" style="padding:56px 48px;">
	<!-- wp:group {"className":"section-head","layout":{"type":"flex","justifyContent":"space-between","flexWrap":"wrap"}} -->
	<div class="wp-block-group section-head">
		<!-- wp:heading {"level":2,"className":"section-head__title","style":{"typography":{"fontSize":"30px","fontWeight":"800","letterSpacing":"-0.02em"}}} -->
		<h2 class="wp-block-heading section-head__title" style="font-size:30px;font-weight:800;letter-spacing:-0.02em;margin:0;">From the journal</h2>
		<!-- /wp:heading -->
		<!-- wp:paragraph {"className":"section-head__link"} -->
		<p class="section-head__link"><a href="/blog/" style="color:#993331;font-weight:700;text-decoration:none;">Read all →</a></p>
		<!-- /wp:paragraph -->
	</div>
	<!-- /wp:group -->

	<!-- wp:query {"queryId":42,"query":{"perPage":3,"pages":0,"offset":0,"postType":"post","order":"desc","orderBy":"date","author":"","search":"","exclude":[],"sticky":"","inherit":false},"align":"wide","layout":{"type":"constrained"}} -->
	<div class="wp-block-query alignwide">
		<!-- wp:post-template {"layout":{"type":"grid","columnCount":3}} -->
			<!-- wp:group {"className":"blog-card","layout":{"type":"constrained"}} -->
			<div class="wp-block-group blog-card">
				<!-- wp:post-featured-image {"className":"blog-card__well","style":{"border":{"radius":"18px"}}} /-->
				<!-- wp:post-date {"format":"M j","className":"blog-card__date"} /-->
				<!-- wp:post-title {"level":3,"isLink":true,"className":"blog-card__title"} /-->
				<!-- wp:post-excerpt {"moreText":"Read article →","className":"blog-card__excerpt"} /-->
			</div>
			<!-- /wp:group -->
		<!-- /wp:post-template -->
	</div>
	<!-- /wp:query -->
</section>
<!-- /wp:group -->
BLOCKS;
