<?php
/**
 * Pattern: Contact split — left column contact form (Contact Form 7 shortcode), right column FAQ accordion.
 * The FAQ uses core/details blocks so it stays fully editable without theme JS.
 *
 * @package Dankcave
 */

return <<<'BLOCKS'
<!-- wp:group {"className":"pattern-contact","align":"full"} -->
<section class="wp-block-group alignfull pattern-contact">
	<div class="pattern-contact__inner">
		<div class="pattern-contact__form">
			<h1 class="pattern-contact__title">Contact with an expert</h1>
			<p class="pattern-contact__intro">Questions about an order, a product, or just want a recommendation? Real humans in Tracy, CA &mdash; we usually reply within a day.</p>
			<!-- wp:shortcode -->
			[contact-form-7 id="17658" title="Contact form 1"]
			<!-- /wp:shortcode -->
			<div class="pattern-contact__meta">
				<span><strong>Email</strong> &middot; support@dankcave.com</span>
				<span><strong>Phone</strong> &middot; (209) 555-0142</span>
			</div>
		</div>
		<div class="pattern-contact__faq">
			<h2 class="pattern-contact__faq-title">Frequently asked questions</h2>
			<!-- wp:details {"showContent":true,"className":"pattern-contact__acc"} -->
			<details class="wp-block-details pattern-contact__acc" open>
				<summary>My order hasn&#8217;t arrived yet. Where is it?</summary>
				<!-- wp:paragraph -->
				<p>Track your order any time from <a href="/my-account/">your account</a> or the tracking link in your shipping email. If it is past the estimated window, email us and we will chase the carrier.</p>
				<!-- /wp:paragraph -->
			</details>
			<!-- /wp:details -->
			<!-- wp:details {"className":"pattern-contact__acc"} -->
			<details class="wp-block-details pattern-contact__acc">
				<summary>Do you deliver on public holidays?</summary>
				<!-- wp:paragraph -->
				<p>Carriers do not run on major public holidays, so those days are not counted in delivery estimates. Orders placed on a holiday ship the next business day.</p>
				<!-- /wp:paragraph -->
			</details>
			<!-- /wp:details -->
			<!-- wp:details {"className":"pattern-contact__acc"} -->
			<details class="wp-block-details pattern-contact__acc">
				<summary>Do you deliver to my postcode?</summary>
				<!-- wp:paragraph -->
				<p>We ship to all 50 U.S. states. Enter your ZIP at checkout to see available options and estimated arrival.</p>
				<!-- /wp:paragraph -->
			</details>
			<!-- /wp:details -->
			<!-- wp:details {"className":"pattern-contact__acc"} -->
			<details class="wp-block-details pattern-contact__acc">
				<summary>Is next-day delivery available on all orders?</summary>
				<!-- wp:paragraph -->
				<p>Express (2-day) is available on most in-stock items. Choose it at checkout &mdash; cutoff is 2pm PT for same-day dispatch.</p>
				<!-- /wp:paragraph -->
			</details>
			<!-- /wp:details -->
			<!-- wp:details {"className":"pattern-contact__acc"} -->
			<details class="wp-block-details pattern-contact__acc">
				<summary>Do I need to be there to sign for delivery?</summary>
				<!-- wp:paragraph -->
				<p>An adult signature (21+) may be required. Have a valid government-issued ID ready.</p>
				<!-- /wp:paragraph -->
			</details>
			<!-- /wp:details -->
		</div>
	</div>
</section>
<!-- /wp:group -->
BLOCKS;
