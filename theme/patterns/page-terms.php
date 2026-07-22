<?php
/**
 * Pattern: Terms of Service — pure Gutenberg blocks. Built via a data array
 * + loop so the 16 sections stay maintainable. Each section becomes a
 * wp:group + wp:heading + one or more wp:paragraph / wp:list / callout
 * blocks.
 *
 * @package Dankcave
 */

return ( function () {
	// Each section: heading + array of "blocks" (each block is a spec that
	// renders to a paragraph / list / card / warning card).
	$sections = array(
		array(
			'h' => 'Legal use &amp; your responsibility',
			'b' => array(
				array( 'p', 'All products on this website are intended for legal use and may not be used, or discussed, in a manner that is illegal. As the consumer, it is your responsibility to know your local and state laws before making your purchase.' ),
				array( 'p', 'It is illegal to use drug-paraphernalia slang when referring to one of our tobacco pipes in the State of New Jersey. If you do, you will be denied service as required by law; any such communication will not be answered and will be deleted from our system without notice.' ),
				array( 'p', 'Any governmental employee, agency, or agent must identify themselves to the operators of DankCave upon entering the site and when ordering. DankCave.com reserves the right to request a scan of any customer&#8217;s photo ID for age verification before shipping an order.' ),
				array( 'p', 'All of our products are traditionally and solely intended for tobacco use or concentrated essential oils, by legal adults. You must be 21 years old to purchase anything from this website. Although this site may link to other sites, DankCave.com does not imply any approval, association, sponsorship, endorsement, or affiliation with linked sites. Your linking to any off-site pages is at your own risk.' ),
			),
		),
		array(
			'h' => 'US Code 21-863',
			'b' => array(
				array( 'p', 'US Code 21-863 refers to ALL materials used to manufacture tobacco accessories: metal, wooden, acrylic, glass, stone, plastic, or ceramic pipes. Pipes with carburetion devices (third holes) are considered paraphernalia regardless of material. Traditional accessories like pipes, cigar and cigarette holders and hookah/shisha pipes are not included in the list of contemporary accessories.' ),
				array( 'p', 'U.S. vs. Posters &amp; Things (Supreme Court, 1996) upholds this code as the objective standard by which paraphernalia is judged. All of our glass pipes conform to RTDA guidelines for bowl depth and width. By entering this site you acknowledge that it will only be construed and evaluated according to California law, and that materials here apply only to products offered in California. DankCave reserves the right to refuse service/goods.' ),
			),
		),
		array(
			'h' => 'Purchases of tobacco products &amp; electronic cigarettes',
			'b' => array(
				array( 'p', 'If you purchase any tobacco products from DankCave.com and you are under the legal age to purchase tobacco and/or tobacco accessories (including water pipes), you are subject to local prosecution and fines. Your legal guardians shall be solely liable for any damage resulting from any infringement of such laws.' ),
				array( 'p', 'You agree that any tobacco products, accessories, or supplies purchased by you will be used for lawful purposes only, and that it is legal in your country and/or state to purchase tobacco-related products. Use of these products is entirely at the risk and discretion of the user, and DankCave.com will not be responsible for any use or misuse.' ),
			),
		),
		array(
			'h' => 'Health warnings',
			'b' => array(
				array( 'warning', '<strong>SURGEON GENERAL&#8217;S WARNING:</strong> Smoking causes lung cancer, heart disease, emphysema, and may complicate pregnancy. Quitting smoking now greatly reduces serious risks to your health. Smoking by pregnant women may result in fetal injury, premature birth, and low birth weight. Cigarette smoke contains carbon monoxide.' ),
				array( 'p', 'If you purchase any vaporizers and/or electronic cigarettes, you agree they are not a medical device. Information on DankCave.com is not intended as a diagnosis, treatment, or cure. Some people may experience an allergic reaction or side effects. Please consult your physician prior to use, or if you experience adverse effects. These products are intended for aromatherapy, delivery of aromatic blends, or smokeless use of tobacco only.' ),
				array( 'warning', 'Electronic cigarettes are not for use by persons under legal smoking age. Keep out of reach of children and pets &mdash; if swallowed, they can present a choking hazard. Nicotine is addictive and can be toxic if inhaled or ingested. Do not use if pregnant or breastfeeding, or if at risk of heart disease, high blood pressure, or diabetes. These statements have not been evaluated by the FDA. WARNING: Electronic cigarettes with nicotine contain a chemical known to the State of California to cause birth defects or other reproductive harm.' ),
				array( 'p', 'Note: The items listed herein are intended for tobacco and/or legal smoking blends only. Please exit this site if you do not accept these terms, are not of legal age, or are in a country where use of this site is not permitted.' ),
			),
		),
		array(
			'h' => 'Shipping restrictions',
			'b' => array( array( 'p', 'DankCave.com does not offer shipping to third parties as gifts or otherwise. We are only able to ship product(s) to those who have purchased it and agreed to our terms and conditions. Therefore we will only ship to the credit card holder&#8217;s name.' ) ),
		),
		array(
			'h' => 'Returns',
			'b' => array(
				array( 'p', 'DankCave.com only accepts returns on unused products, and a return claim must be filed within fourteen days of the delivery date. We cannot accept returns, exchanges, or offer refunds for products that have been used &mdash; please inspect all items prior to use.' ),
				array( 'p', 'If you send an item back, package it the same way it arrived to ensure it does not break in transit. We recommend all return shipments purchase insurance covering the full order amount; if you do not and your shipment is damaged, we cannot offer a refund. Once inspected and approved for re-stocking, a full refund for the purchase price will be issued.' ),
				array( 'p', 'If you are returning due to preference (not a DankCave error) and received free shipping, you will receive a refund equal to the full purchase amount minus the cost of shipping. Shipping and handling fees are non-refundable, and the buyer is responsible for any return shipping fees.' ),
			),
		),
		array(
			'h' => 'Changes to these terms',
			'b' => array( array( 'p', 'DankCave.com may revise and update these Terms of Use at any time. Your continued usage of the Website after any changes will mean you accept those changes. Any aspect of the Website may be changed, supplemented, deleted, or updated without notice at the sole discretion of DankCave.com.' ) ),
		),
		array(
			'h' => 'Use of site materials',
			'b' => array( array( 'p', 'Permission is granted to electronically copy and print hard-copy portions of this site for the sole purpose of placing an order with or purchasing DankCave products. Any other use &mdash; including reproduction, distribution, display, or transmission of the content &mdash; is strictly prohibited unless authorized by DankCave. You agree not to change or delete any proprietary notices from downloaded materials.' ) ),
		),
		array(
			'h' => 'Warranty disclaimer',
			'b' => array( array( 'p', 'This site and the materials and products on this site are provided &#8220;as is&#8221; and without warranties of any kind, whether express or implied. To the fullest extent permissible by law, DankCave disclaims all warranties, including implied warranties of merchantability, fitness for a particular purpose, and non-infringement. DankCave does not warrant that functions will be uninterrupted or error-free, that defects will be corrected, or that the site or its server are free of viruses. Some states do not permit such limitations, so they may not apply to you.' ) ),
		),
		array(
			'h' => 'Limitation of liability',
			'b' => array( array( 'p', 'DankCave shall not be liable for any special or consequential damages that result from the use of, or inability to use, the materials on this site or the performance of the products, even if advised of the possibility of such damages. Applicable law may not allow such limitations, so they may not apply to you.' ) ),
		),
		array(
			'h' => 'Typographical errors',
			'b' => array( array( 'p', 'If a DankCave product is mistakenly listed at an incorrect price, DankCave reserves the right to refuse or cancel any orders placed for the product at that price, whether or not the order has been confirmed and your card charged. If your card has already been charged and your order is canceled, DankCave will issue a credit in the amount of the incorrect price.' ) ),
		),
		array(
			'h' => 'Termination',
			'b' => array( array( 'p', 'These terms apply to you upon your accessing the site and/or completing the registration or shopping process. They may be terminated by DankCave without notice at any time, for any reason. Provisions relating to Copyrights, Trademark, Disclaimer, Limitation of Liability, Indemnification, and Miscellaneous shall survive any termination.' ) ),
		),
		array(
			'h' => 'Notice',
			'b' => array( array( 'p', 'DankCave may deliver notice to you by e-mail, a general notice on the site, or another reliable method to the address you have provided.' ) ),
		),
		array(
			'h' => 'Miscellaneous',
			'b' => array( array( 'p', 'Your use of this site shall be governed by the laws of the State of California, U.S.A., without regard to choice-of-law provisions, and not by the 1980 U.N. Convention on contracts for the international sale of goods. Jurisdiction and venue for any legal proceeding shall be in the state or federal courts located in San Joaquin County, California. Any claim must be commenced within one (1) year after it arises. DankCave&#8217;s failure to enforce any provision is not a waiver. DankCave may assign its rights and duties under this Agreement to any party at any time without notice.' ) ),
		),
		array(
			'h' => 'Use of site',
			'b' => array( array( 'p', 'Harassment in any manner or form on the site &mdash; including via e-mail, chat, or use of obscene or abusive language &mdash; is strictly forbidden. Impersonation of others is prohibited. You may not upload, distribute, or publish content that is libelous, defamatory, obscene, threatening, invasive of privacy, abusive, illegal, or otherwise objectionable. You may not upload commercial content or use the site to solicit others to join any other commercial service or organization.' ) ),
		),
		array(
			'h' => 'Participation &amp; third-party links',
			'b' => array(
				array( 'p', 'DankCave cannot review all communications and materials posted by users and is not responsible for their content &mdash; it acts as a passive conduit. DankCave reserves the right to block or remove content it determines to be abusive, fraudulent, infringing, or otherwise unacceptable in its sole discretion.' ),
				array( 'p', 'You agree to indemnify, defend, and hold harmless DankCave and its Service Providers from all losses, expenses, damages and costs (including reasonable attorneys&#8217; fees) resulting from any violation of these terms or activity related to your account.' ),
				array( 'p', 'DankCave may link to sites operated by third parties. It has no control over these linked sites, which have separate privacy and data-collection practices. These links are for your convenience and you access them at your own risk.' ),
			),
		),
	);

	// Render each section as native block markup.
	$section_markup = '';
	foreach ( $sections as $s ) {
		$section_markup .= "\n\n<!-- wp:group {\"className\":\"dc-legal__section\"} -->\n<div class=\"wp-block-group dc-legal__section\">\n";
		$section_markup .= "<!-- wp:heading {\"level\":2,\"className\":\"dc-legal__h2\"} -->\n<h2 class=\"wp-block-heading dc-legal__h2\">" . $s['h'] . "</h2>\n<!-- /wp:heading -->\n";
		foreach ( $s['b'] as $block ) {
			list( $type, $text ) = $block;
			if ( 'warning' === $type ) {
				$section_markup .= "<!-- wp:group {\"className\":\"dc-legal__warning\"} -->\n<div class=\"wp-block-group dc-legal__warning\">\n<!-- wp:paragraph -->\n<p>" . $text . "</p>\n<!-- /wp:paragraph -->\n</div>\n<!-- /wp:group -->\n";
			} else {
				$section_markup .= "<!-- wp:paragraph {\"className\":\"dc-legal__p\"} -->\n<p class=\"dc-legal__p\">" . $text . "</p>\n<!-- /wp:paragraph -->\n";
			}
		}
		$section_markup .= "</div>\n<!-- /wp:group -->\n";
	}

	return '
<!-- wp:group {"className":"dc-legal","align":"full","tagName":"section"} -->
<section class="wp-block-group alignfull dc-legal">
<!-- wp:html -->
<div class="dc-legal__progress"><div class="dc-legal__progress-bar"></div></div>
<!-- /wp:html -->

<!-- wp:group {"className":"dc-legal__hero"} -->
<div class="wp-block-group dc-legal__hero">
<!-- wp:heading {"level":1,"className":"dc-legal__title"} -->
<h1 class="wp-block-heading dc-legal__title">The house<br><span class="dc-legal__title-accent">rules</span>.</h1>
<!-- /wp:heading -->
<!-- wp:paragraph {"className":"dc-legal__lede"} -->
<p class="dc-legal__lede">The full legal terms for using DankCave.com. You must be 21+ to enter.</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->

<!-- wp:group {"className":"dc-legal__body"} -->
<div class="wp-block-group dc-legal__body">

<!-- wp:group {"className":"dc-legal__card"} -->
<div class="wp-block-group dc-legal__card">
<!-- wp:paragraph -->
<p>If you are under the age of 21, please exit this site now.</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->

<!-- wp:paragraph {"className":"dc-legal__intro"} -->
<p class="dc-legal__intro">Please read the following terms and conditions before using this website. All users of this site agree that access to and use of this site are subject to the following terms and conditions and other applicable law. If you do not agree to these terms and conditions, please do not use this site.</p>
<!-- /wp:paragraph -->
<!-- wp:paragraph {"className":"dc-legal__intro"} -->
<p class="dc-legal__intro">These General Terms and Conditions (&#8220;Terms and Conditions&#8221;) are applicable to any use of the website www.dankcave.com (&#8220;Website&#8221;), to any services available on the Website, and to any and all offers, orders and agreements connected therewith (&#8220;Services&#8221;).</p>
<!-- /wp:paragraph -->
' . $section_markup . '

<!-- wp:group {"className":"dc-legal__section"} -->
<div class="wp-block-group dc-legal__section">
<!-- wp:group {"className":"dc-legal__card"} -->
<div class="wp-block-group dc-legal__card">
<!-- wp:paragraph -->
<p>Please exit this site if you do not agree to these conditions.</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->
</div>
<!-- /wp:group -->

</div>
<!-- /wp:group -->

</section>
<!-- /wp:group -->
';
} )();
