<?php
/**
 * Pattern: Privacy Policy — pure Gutenberg blocks.
 *
 * @package Dankcave
 */

return '
<!-- wp:group {"className":"dc-legal","align":"full","tagName":"section"} -->
<section class="wp-block-group alignfull dc-legal">
<!-- wp:html -->
<div class="dc-legal__progress"><div class="dc-legal__progress-bar"></div></div>
<!-- /wp:html -->

<!-- wp:group {"className":"dc-legal__hero"} -->
<div class="wp-block-group dc-legal__hero">
<!-- wp:heading {"level":1,"className":"dc-legal__title"} -->
<h1 class="wp-block-heading dc-legal__title">Your data,<br>handled <span class="dc-legal__title-accent">right</span>.</h1>
<!-- /wp:heading -->
<!-- wp:paragraph {"className":"dc-legal__lede"} -->
<p class="dc-legal__lede">Owned and operated by DankCave. Here is exactly what we gather and how we use it.</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->

<!-- wp:group {"className":"dc-legal__body"} -->
<div class="wp-block-group dc-legal__body">

<!-- wp:paragraph {"className":"dc-legal__intro"} -->
<p class="dc-legal__intro">This site is owned and operated by DankCave. As an online business, ensuring the privacy of your information is a top priority for us. At DankCave, we want to make your online shopping experience both safe and satisfying.</p>
<!-- /wp:paragraph -->
<!-- wp:paragraph {"className":"dc-legal__intro"} -->
<p class="dc-legal__intro">Because we gather certain types of information about our users, we feel you should fully understand our policy and the terms and conditions surrounding the capture and use of that information. This privacy statement discloses what information we gather and how we use it.</p>
<!-- /wp:paragraph -->

<!-- wp:group {"className":"dc-legal__section"} -->
<div class="wp-block-group dc-legal__section">
<!-- wp:heading {"level":2,"className":"dc-legal__h2"} -->
<h2 class="wp-block-heading dc-legal__h2">Electronic newsletters policy</h2>
<!-- /wp:heading -->
<!-- wp:paragraph {"className":"dc-legal__p"} -->
<p class="dc-legal__p">We offer a free electronic newsletter to our customers. DankCave gathers the email addresses of users who make purchases on our site.</p>
<!-- /wp:paragraph -->
<!-- wp:paragraph {"className":"dc-legal__p"} -->
<p class="dc-legal__p">Users may remove themselves from this mailing list by following the link provided in every newsletter that points users to the subscription management page, which offers a simple one-click unsubscribe.</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->

<!-- wp:group {"className":"dc-legal__section"} -->
<div class="wp-block-group dc-legal__section">
<!-- wp:heading {"level":2,"className":"dc-legal__h2"} -->
<h2 class="wp-block-heading dc-legal__h2">Personal information gathered through online purchases</h2>
<!-- /wp:heading -->
<!-- wp:paragraph {"className":"dc-legal__p"} -->
<p class="dc-legal__p">When you place an order we collect:</p>
<!-- /wp:paragraph -->
<!-- wp:list {"className":"dc-legal__list"} -->
<ul class="dc-legal__list"><!-- wp:list-item --><li>First and last name</li><!-- /wp:list-item --><!-- wp:list-item --><li>Billing address</li><!-- /wp:list-item --><!-- wp:list-item --><li>Email address</li><!-- /wp:list-item --><!-- wp:list-item --><li>Credit card billing information &mdash; we <strong>never</strong> actually see your full card number, only the last 4 digits. Our system is 100% PCI compliant.</li><!-- /wp:list-item --></ul>
<!-- /wp:list -->
<!-- wp:group {"className":"dc-legal__info"} -->
<div class="wp-block-group dc-legal__info">
<!-- wp:paragraph -->
<p>All of your personal information is protected by high-quality security settings that include a secure socket layer (SSL) connection for a safe checkout, 256-bit encryption throughout the entire checkout process, and 100% PCI compliance.</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->
</div>
<!-- /wp:group -->

<!-- wp:group {"className":"dc-legal__section"} -->
<div class="wp-block-group dc-legal__section">
<!-- wp:heading {"level":2,"className":"dc-legal__h2"} -->
<h2 class="wp-block-heading dc-legal__h2">We never sell your data</h2>
<!-- /wp:heading -->
<!-- wp:group {"className":"dc-legal__card dc-legal__card--big"} -->
<div class="wp-block-group dc-legal__card dc-legal__card--big">
<!-- wp:paragraph -->
<p>Under <span class="dc-legal__card-accent">no circumstances</span> does DankCave rent, sell, or trade any information about an individual user to a third party that is gathered throughout the checkout process.</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->
</div>
<!-- /wp:group -->

<!-- wp:group {"className":"dc-legal__section"} -->
<div class="wp-block-group dc-legal__section">
<!-- wp:heading {"level":2,"className":"dc-legal__h2"} -->
<h2 class="wp-block-heading dc-legal__h2">Questions</h2>
<!-- /wp:heading -->
<!-- wp:paragraph {"className":"dc-legal__p"} -->
<p class="dc-legal__p">Questions about this policy? Email <a href="mailto:support@dankcave.com">support@dankcave.com</a> or write to DankCave, 3941 Holly Drive Suite J, Tracy, CA 95304.</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->

</div>
<!-- /wp:group -->

</section>
<!-- /wp:group -->
';
