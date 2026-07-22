<?php
/**
 * SEO — meta tags, Open Graph, Twitter Cards, canonical URL, JSON-LD schemas,
 * and LLM-friendly llms.txt endpoint. Everything a search engine or an LLM
 * needs to fully understand the shop.
 *
 * Only loads if Yoast / RankMath aren't already installed — those plugins have
 * their own pipelines and we don't want to double-emit tags.
 *
 * @package Dankcave
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * Return true if a major SEO plugin is active — we defer meta/OG/schema to it
 * but keep our own llms.txt endpoint and a fallback meta description when the
 * plugin doesn't emit one (Yoast in particular skips pages without a manually
 * authored description).
 */
function dankcave_seo_plugin_active() {
	if ( defined( 'WPSEO_VERSION' ) ) { return true; } // Yoast
	if ( defined( 'RANK_MATH_VERSION' ) ) { return true; }
	if ( defined( 'AIOSEO_FILE' ) ) { return true; }
	if ( class_exists( 'The_SEO_Framework\\Load' ) ) { return true; }
	return false;
}
function dankcave_seo_should_run() {
	return ! dankcave_seo_plugin_active();
}

/**
 * Print meta description + Open Graph + Twitter Cards + canonical URL.
 * Selects the right content for each template.
 */
add_action( 'wp_head', 'dankcave_seo_head', 2 );
function dankcave_seo_head() {
	if ( ! dankcave_seo_should_run() ) { return; }

	$site_name  = get_bloginfo( 'name' );
	$site_desc  = get_bloginfo( 'description' );
	$canonical  = home_url( add_query_arg( array(), $GLOBALS['wp']->request ) );
	$description = $site_desc;
	$title      = $site_name;
	$image      = '';
	$type       = 'website';

	if ( is_singular( 'product' ) && function_exists( 'wc_get_product' ) ) {
		$product     = wc_get_product( get_the_ID() );
		if ( $product ) {
			$title       = $product->get_name() . ' – ' . $site_name;
			$description = wp_strip_all_tags( $product->get_short_description() ?: $product->get_description() );
			$description = wp_trim_words( $description, 30, '' );
			$image       = wp_get_attachment_image_url( $product->get_image_id(), 'large' );
			$type        = 'product';
		}
		$canonical = get_permalink();
	} elseif ( is_singular( 'post' ) ) {
		$title       = get_the_title() . ' – ' . $site_name;
		$description = wp_strip_all_tags( get_the_excerpt() );
		$description = wp_trim_words( $description, 30, '' );
		$image       = get_the_post_thumbnail_url( get_the_ID(), 'large' );
		$type        = 'article';
		$canonical   = get_permalink();
	} elseif ( is_page() ) {
		$title       = get_the_title() . ' – ' . $site_name;
		$description = wp_strip_all_tags( get_the_excerpt() ?: get_the_content() );
		$description = wp_trim_words( $description, 30, '' );
		$canonical   = get_permalink();
	} elseif ( is_product_category() || is_product_tag() ) {
		$term        = get_queried_object();
		$title       = $term->name . ' – ' . $site_name;
		$description = wp_strip_all_tags( $term->description ?: sprintf( __( 'Shop %s at Dankcave — curated bongs, vaporizers and glass.', 'dankcave' ), $term->name ) );
		$image       = wp_get_attachment_image_url( get_term_meta( $term->term_id, 'thumbnail_id', true ), 'large' );
		$canonical   = get_term_link( $term );
	} elseif ( is_shop() ) {
		$title       = __( 'Shop', 'dankcave' ) . ' – ' . $site_name;
		$description = __( 'Every piece we stock in one place: bongs, dab rigs, vapes, and the small tools that make them sing.', 'dankcave' );
		$canonical   = wc_get_page_permalink( 'shop' );
	} elseif ( is_home() || is_front_page() ) {
		$title       = $site_name . ' – ' . ( $site_desc ?: 'Vices, handled with care.' );
		$description = __( 'Curated bongs, dab rigs, vapes, and rolling gear. Ships discreetly. Adults 21+.', 'dankcave' );
		$image       = get_theme_mod( 'dankcave_hero_image', DANKCAVE_URI . 'assets/images/hero-product-placeholder.png' );
	} elseif ( is_404() ) {
		$title       = __( 'Page not found', 'dankcave' ) . ' – ' . $site_name;
		$description = __( 'The page you followed does not exist.', 'dankcave' );
	} elseif ( is_search() ) {
		$title       = sprintf( __( 'Search results for "%s"', 'dankcave' ), get_search_query() ) . ' – ' . $site_name;
		$description = __( 'Search results.', 'dankcave' );
	}

	$description = trim( $description );
	if ( ! $image ) { $image = get_theme_mod( 'dankcave_share_image', DANKCAVE_URI . 'assets/images/hero-product-placeholder.png' ); }

	echo '<meta name="description" content="' . esc_attr( $description ) . '" />' . "\n";
	echo '<link rel="canonical" href="' . esc_url( $canonical ) . '" />' . "\n";

	// Open Graph
	echo '<meta property="og:type"        content="' . esc_attr( $type ) . '" />' . "\n";
	echo '<meta property="og:site_name"   content="' . esc_attr( $site_name ) . '" />' . "\n";
	echo '<meta property="og:title"       content="' . esc_attr( $title ) . '" />' . "\n";
	echo '<meta property="og:description" content="' . esc_attr( $description ) . '" />' . "\n";
	echo '<meta property="og:url"         content="' . esc_url( $canonical ) . '" />' . "\n";
	if ( $image ) {
		echo '<meta property="og:image"     content="' . esc_url( $image ) . '" />' . "\n";
	}
	echo '<meta property="og:locale"      content="' . esc_attr( str_replace( '-', '_', get_locale() ) ) . '" />' . "\n";

	// Twitter Cards
	echo '<meta name="twitter:card"        content="summary_large_image" />' . "\n";
	echo '<meta name="twitter:title"       content="' . esc_attr( $title ) . '" />' . "\n";
	echo '<meta name="twitter:description" content="' . esc_attr( $description ) . '" />' . "\n";
	if ( $image ) {
		echo '<meta name="twitter:image"     content="' . esc_url( $image ) . '" />' . "\n";
	}

	// Robots — allow indexing by default. Search results and 404s: noindex.
	if ( is_search() || is_404() ) {
		echo '<meta name="robots" content="noindex, follow" />' . "\n";
	}
}

/**
 * JSON-LD structured data. Organisation site-wide + template-specific schemas.
 * These get indexed as rich results (product, breadcrumb, article, sitelinks).
 */
add_action( 'wp_head', 'dankcave_seo_jsonld', 3 );
function dankcave_seo_jsonld() {
	if ( ! dankcave_seo_should_run() ) { return; }

	$graph = array();
	$site_url = home_url( '/' );

	// Site-wide Organization + WebSite + SearchAction
	$graph[] = array(
		'@type'       => 'Organization',
		'@id'         => $site_url . '#organization',
		'name'        => get_bloginfo( 'name' ),
		'url'         => $site_url,
		'logo'        => wp_get_attachment_image_url( get_theme_mod( 'custom_logo' ), 'full' ),
		'description' => get_bloginfo( 'description' ),
	);
	$graph[] = array(
		'@type'         => 'WebSite',
		'@id'           => $site_url . '#website',
		'url'           => $site_url,
		'name'          => get_bloginfo( 'name' ),
		'publisher'     => array( '@id' => $site_url . '#organization' ),
		'potentialAction' => array(
			'@type'       => 'SearchAction',
			'target'      => array(
				'@type'       => 'EntryPoint',
				'urlTemplate' => $site_url . '?s={search_term_string}',
			),
			'query-input' => 'required name=search_term_string',
		),
	);

	// Template-specific graphs
	if ( is_singular( 'product' ) && function_exists( 'wc_get_product' ) ) {
		$product = wc_get_product( get_the_ID() );
		if ( $product ) {
			$offers = array(
				'@type'         => 'Offer',
				'url'           => get_permalink(),
				'priceCurrency' => get_woocommerce_currency(),
				'price'         => $product->get_price(),
				'availability'  => $product->is_in_stock() ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock',
				'itemCondition' => 'https://schema.org/NewCondition',
			);
			$product_schema = array(
				'@type'       => 'Product',
				'@id'         => get_permalink() . '#product',
				'name'        => $product->get_name(),
				'description' => wp_strip_all_tags( $product->get_short_description() ?: $product->get_description() ),
				'sku'         => $product->get_sku() ?: (string) $product->get_id(),
				'image'       => wp_get_attachment_image_url( $product->get_image_id(), 'large' ) ?: '',
				'url'         => get_permalink(),
				'brand'       => array( '@type' => 'Brand', 'name' => get_bloginfo( 'name' ) ),
				'offers'      => $offers,
			);
			if ( $product->get_review_count() > 0 ) {
				$product_schema['aggregateRating'] = array(
					'@type'       => 'AggregateRating',
					'ratingValue' => (string) $product->get_average_rating(),
					'reviewCount' => (int) $product->get_review_count(),
				);
			}
			$graph[] = $product_schema;

			// Breadcrumbs on PDP
			$graph[] = dankcave_seo_breadcrumb_ld( $product );
		}
	} elseif ( is_singular( 'post' ) ) {
		$post_id = get_the_ID();
		$graph[] = array(
			'@type'         => 'Article',
			'@id'           => get_permalink() . '#article',
			'headline'      => get_the_title(),
			'description'   => wp_strip_all_tags( get_the_excerpt() ),
			'image'         => get_the_post_thumbnail_url( $post_id, 'large' ) ?: '',
			'datePublished' => get_the_date( 'c' ),
			'dateModified'  => get_the_modified_date( 'c' ),
			'author'        => array( '@type' => 'Person', 'name' => get_the_author() ),
			'publisher'     => array( '@id' => $site_url . '#organization' ),
			'mainEntityOfPage' => get_permalink(),
		);
	}

	$doc = array( '@context' => 'https://schema.org', '@graph' => $graph );
	echo '<script type="application/ld+json">' . wp_json_encode( $doc, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . '</script>' . "\n";
}

function dankcave_seo_breadcrumb_ld( $product ) {
	$items = array(
		array( 'name' => __( 'Home', 'dankcave' ), 'url' => home_url( '/' ) ),
		array( 'name' => __( 'Shop', 'dankcave' ), 'url' => wc_get_page_permalink( 'shop' ) ),
	);
	$terms = get_the_terms( $product->get_id(), 'product_cat' );
	if ( $terms && ! is_wp_error( $terms ) ) {
		$items[] = array( 'name' => $terms[0]->name, 'url' => get_term_link( $terms[0] ) );
	}
	$items[] = array( 'name' => $product->get_name(), 'url' => get_permalink( $product->get_id() ) );

	$list = array();
	foreach ( $items as $i => $it ) {
		$list[] = array(
			'@type'    => 'ListItem',
			'position' => $i + 1,
			'name'     => $it['name'],
			'item'     => $it['url'],
		);
	}
	return array(
		'@type'           => 'BreadcrumbList',
		'itemListElement' => $list,
	);
}

/**
 * Demote H1 tags inside the_content() to H2 so each page has exactly one H1
 * (the template-emitted title). Legacy content authored with H1 subheadings
 * would otherwise duplicate the primary heading and confuse crawlers.
 */
add_filter( 'the_content', 'dankcave_demote_content_h1', 20 );
function dankcave_demote_content_h1( $html ) {
	if ( is_admin() ) { return $html; }
	// WC pages (cart, checkout, my-account) DO need their content H1 to survive
	// because our page.php bails and doesn't emit its own H1.
	if ( function_exists( 'WC' ) && ( is_cart() || is_checkout() || is_account_page() ) ) {
		return $html;
	}
	// Pages using the full-bleed landing template render patterns as their
	// primary content and rely on the content H1 (About hero, legal-page hero,
	// etc.) as the sole H1 for the document.
	if ( is_singular( 'page' ) ) {
		$tpl = get_page_template_slug( get_the_ID() );
		if ( 'page-templates/landing.php' === $tpl ) {
			return $html;
		}
	}
	if ( is_singular( array( 'page', 'post', 'product' ) ) ) {
		$html = preg_replace( '#<h1(\b[^>]*)>#i', '<h2$1>', $html );
		$html = str_ireplace( '</h1>', '</h2>', $html );
	}
	return $html;
}

/**
 * Auto-fill meta description via Yoast's filter when the editor hasn't
 * authored one. Fixes empty meta descriptions on product / category / blog
 * pages without touching Yoast's other output.
 */
add_filter( 'wpseo_metadesc', 'dankcave_seo_fallback_metadesc', 10000 );
add_filter( 'wpseo_opengraph_desc', 'dankcave_seo_fallback_metadesc', 10000 );
add_filter( 'wpseo_twitter_description', 'dankcave_seo_fallback_metadesc', 10000 );
function dankcave_seo_fallback_metadesc( $desc ) {
	// Consider the description "empty" if Yoast is just handing us the raw
	// content of a WooCommerce shortcode page (e.g. [woocommerce_cart]) or a
	// stub too short to be useful.
	$looks_junk = ! empty( $desc ) && ( preg_match( '/^\s*\[[a-z_]+\][\s]*$/i', trim( $desc ) ) || strlen( trim( $desc ) ) < 50 );
	if ( ! empty( $desc ) && ! $looks_junk ) { return $desc; }

	$site_name = get_bloginfo( 'name' );
	// Front page must be checked before is_page() — otherwise an empty static
	// home page's content wins and returns nothing.
	if ( is_home() || is_front_page() ) {
		$desc = get_bloginfo( 'description' ) ?: __( 'Curated bongs, dab rigs, vapes, and rolling gear. Ships discreetly. Adults 21+.', 'dankcave' );
	}
	// WooCommerce shortcode pages MUST be checked before is_page() — otherwise
	// they match as generic pages and get their description set to the raw
	// shortcode text ([woocommerce_cart]).
	elseif ( function_exists( 'is_cart' ) && is_cart() ) {
		$desc = __( 'Review your bag and head to checkout. Free discreet shipping on qualifying orders. 30-day returns.', 'dankcave' );
	} elseif ( function_exists( 'is_checkout' ) && is_checkout() ) {
		$desc = __( 'Checkout securely. Encrypted, discreet packaging, statement shows DC Retail on your card.', 'dankcave' );
	} elseif ( function_exists( 'is_account_page' ) && is_account_page() ) {
		$desc = __( 'Sign in to your account or manage your orders, addresses, and preferences.', 'dankcave' );
	} elseif ( is_singular( 'product' ) && function_exists( 'wc_get_product' ) ) {
		$product = wc_get_product( get_the_ID() );
		if ( $product ) {
			$desc = wp_strip_all_tags( $product->get_short_description() ?: $product->get_description() );
		}
	} elseif ( is_singular( 'post' ) ) {
		$desc = wp_strip_all_tags( get_the_excerpt() );
	} elseif ( is_page() ) {
		$desc = wp_strip_all_tags( get_the_excerpt() ?: get_the_content() );
	} elseif ( is_product_category() || is_product_tag() ) {
		$term = get_queried_object();
		if ( $term ) {
			$desc = wp_strip_all_tags( $term->description ?: sprintf( __( 'Shop %s at %s — curated bongs, vaporizers and glass.', 'dankcave' ), $term->name, $site_name ) );
		}
	} elseif ( is_shop() ) {
		$desc = __( 'Every piece we stock in one place: bongs, dab rigs, vapes, and the small tools that make them sing.', 'dankcave' );
	} elseif ( is_404() ) {
		$desc = __( 'The page you followed does not exist. Head back to the shop or the journal.', 'dankcave' );
	}
	// Trim to ~155 characters for meta description best practice (avoids
	// Google truncating with ellipsis mid-sentence).
	$desc = trim( wp_strip_all_tags( $desc ) );
	if ( strlen( $desc ) > 155 ) {
		$desc = substr( $desc, 0, 152 );
		$desc = substr( $desc, 0, strrpos( $desc, ' ' ) ?: 152 ) . '…';
	}
	return $desc;
}

/**
 * Ensure a canonical URL on 404 so crawlers don't pass link equity to a
 * mistyped URL — point them back at the homepage.
 */
add_action( 'wp_head', 'dankcave_seo_404_canonical', 2 );
function dankcave_seo_404_canonical() {
	if ( ! is_404() ) { return; }
	if ( did_action( 'wp_head' ) < 1 ) { return; }
	echo '<link rel="canonical" href="' . esc_url( home_url( '/' ) ) . '" data-source="dankcave" />' . "\n";
}

/**
 * Backfill missing meta description, og:image, and twitter:image using output
 * buffering. Runs at wp_head priority 999 so Yoast has already rendered.
 * We only add the tag if the response doesn't already contain that meta name.
 */
// Buffer the entire page output so we can rewrite Yoast's junky descriptions
// that come from cart/checkout page content (the shortcode itself). Yoast's
// Indexable system stores per-post descriptions in the database and its
// wpseo_metadesc filter is only consulted for empty values, so we intercept
// the final HTML instead.
add_action( 'template_redirect', 'dankcave_seo_full_response_buffer', 0 );
function dankcave_seo_full_response_buffer() {
	if ( is_admin() || is_feed() || wp_doing_ajax() || wp_doing_cron() ) { return; }
	if ( 'GET' !== ( $_SERVER['REQUEST_METHOD'] ?? 'GET' ) ) { return; }
	// Compute + stash the fallback description now while WP query state is fresh.
	// The ob callback fires much later when is_cart() etc. may no longer work.
	global $dankcave_seo_cached_desc;
	$dankcave_seo_cached_desc = dankcave_seo_fallback_metadesc( '' );
	ob_start( 'dankcave_seo_rewrite_head' );
}
function dankcave_seo_rewrite_head( $html ) {
	if ( ! $html || false === strpos( $html, '<head' ) ) { return $html; }
	global $dankcave_seo_cached_desc;
	$fallback_desc = $dankcave_seo_cached_desc ?: '';

	$html = preg_replace_callback(
		'#<meta\s+(name|property)=(["\'])(description|og:description|twitter:description)\2\s+content=(["\'])([^"\']*)\4([^>]*)>#i',
		function ( $m ) use ( $fallback_desc ) {
			$attr = $m[1]; $name = $m[3]; $content = $m[5]; $tail = $m[6];
			$looks_junk = ( strlen( $content ) < 50 ) || preg_match( '/^\s*\[[a-z_]+\]\s*$/', trim( $content ) );
			if ( $looks_junk && $fallback_desc ) { $content = $fallback_desc; }
			if ( strlen( $content ) > 155 ) {
				$trim = substr( $content, 0, 152 );
				$content = substr( $trim, 0, strrpos( $trim, ' ' ) ?: 152 ) . '…';
			}
			return '<meta ' . $attr . '="' . $name . '" content="' . esc_attr( $content ) . '"' . $tail . '>';
		},
		$html
	);
	return $html;
}

// The head-level buffer keeps working for pages Yoast doesn't emit description
// tags for at all — it appends the missing tags before </head>.
add_action( 'wp_head', 'dankcave_seo_start_buffer', -1 );
add_action( 'wp_head', 'dankcave_seo_end_buffer',   9999 );
function dankcave_seo_start_buffer() { ob_start(); }
function dankcave_seo_end_buffer() {
	if ( ob_get_level() < 1 ) { return; }
	$head = ob_get_clean();
	if ( ! is_string( $head ) ) { echo $head; return; }

	// 1) Ensure a proper meta description: replace ugly ones (shortcodes,
	//    empty, too short) with our fallback; trim overly long ones to ~155.
	$fallback_desc = dankcave_seo_fallback_metadesc( '' );
	$has_desc = ( strpos( $head, 'name="description"' ) !== false || strpos( $head, "name='description'" ) !== false );
	if ( ! $has_desc ) {
		if ( $fallback_desc ) {
			$head .= '<meta name="description" content="' . esc_attr( $fallback_desc ) . '" data-source="dankcave" />' . "\n";
			// Also add og:description + twitter:description while we're here
			if ( strpos( $head, 'property="og:description"' ) === false ) {
				$head .= '<meta property="og:description" content="' . esc_attr( $fallback_desc ) . '" data-source="dankcave" />' . "\n";
			}
			if ( strpos( $head, 'name="twitter:description"' ) === false ) {
				$head .= '<meta name="twitter:description" content="' . esc_attr( $fallback_desc ) . '" data-source="dankcave" />' . "\n";
			}
		}
	} else {
		// Replace description content if it's junky (looks like a shortcode or is very short)
		// AND trim overly-long ones to ~155.
		$new_head = preg_replace_callback(
			'#<meta\s+(name|property)=(["\'])(description|og:description|twitter:description)\2\s+content=(["\'])([^"\']*)\4([^>]*)>#i',
			function ( $m ) use ( $fallback_desc ) {
				$attr    = $m[1];
				$name    = $m[3];
				$content = $m[5];
				$looks_junk = ( strlen( $content ) < 50 ) || preg_match( '/^\s*\[[a-z_]+\]\s*$/', $content );
				if ( $looks_junk && $fallback_desc ) { $content = $fallback_desc; }
				if ( strlen( $content ) > 155 ) {
					$trim = substr( $content, 0, 152 );
					$content = substr( $trim, 0, strrpos( $trim, ' ' ) ?: 152 ) . '…';
				}
				return '<meta ' . $attr . '="' . $name . '" content="' . esc_attr( $content ) . '"' . $m[6] . '>';
			},
			$head
		);
		if ( null !== $new_head ) { $head = $new_head; }
	}

	// 2) Fallback share image — hero / product image / logo
	$share_image = dankcave_seo_fallback_share_image( '' );
	if ( $share_image ) {
		if ( strpos( $head, 'property="og:image"' ) === false ) {
			$head .= '<meta property="og:image" content="' . esc_url( $share_image ) . '" data-source="dankcave" />' . "\n";
		}
		if ( strpos( $head, 'name="twitter:image"' ) === false ) {
			$head .= '<meta name="twitter:image" content="' . esc_url( $share_image ) . '" data-source="dankcave" />' . "\n";
		}
	}

	// 3) Ensure twitter:card is set (some templates skip it)
	if ( strpos( $head, 'name="twitter:card"' ) === false ) {
		$head .= '<meta name="twitter:card" content="summary_large_image" data-source="dankcave" />' . "\n";
	}

	echo $head; // phpcs:ignore
}

/**
 * Emit Product JSON-LD on single-product pages if Yoast isn't already (Yoast
 * Premium ships Product schema but only when "Enhanced product data" is on;
 * on plain installs the graph tops out at WebPage). This is the biggest
 * single SEO win for a WooCommerce store — enables Google Product rich cards.
 */
add_action( 'wp_head', 'dankcave_seo_product_schema', 5 );
function dankcave_seo_product_schema() {
	if ( ! is_singular( 'product' ) || ! function_exists( 'wc_get_product' ) ) { return; }
	$product = wc_get_product( get_the_ID() );
	if ( ! $product ) { return; }

	// Skip if Yoast already emits Product (avoid duplicate schema)
	global $wpseo_output_buffer_has_product_schema;
	if ( $wpseo_output_buffer_has_product_schema ) { return; }

	$offer = array(
		'@type'         => 'Offer',
		'url'           => get_permalink(),
		'priceCurrency' => get_woocommerce_currency(),
		'price'         => (string) $product->get_price(),
		'availability'  => $product->is_in_stock() ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock',
		'itemCondition' => 'https://schema.org/NewCondition',
		'priceValidUntil' => date( 'Y-m-d', strtotime( '+1 year' ) ),
	);
	$image = wp_get_attachment_image_url( $product->get_image_id(), 'large' );
	$data = array(
		'@context'    => 'https://schema.org',
		'@type'       => 'Product',
		'@id'         => get_permalink() . '#product',
		'name'        => $product->get_name(),
		'description' => wp_strip_all_tags( $product->get_short_description() ?: $product->get_description() ),
		'sku'         => $product->get_sku() ?: (string) $product->get_id(),
		'url'         => get_permalink(),
		'brand'       => array( '@type' => 'Brand', 'name' => get_bloginfo( 'name' ) ),
		'offers'      => $offer,
	);
	if ( $image ) { $data['image'] = $image; }
	if ( $product->get_review_count() > 0 ) {
		$data['aggregateRating'] = array(
			'@type'       => 'AggregateRating',
			'ratingValue' => (string) $product->get_average_rating(),
			'reviewCount' => (int) $product->get_review_count(),
		);
	}
	echo '<script type="application/ld+json" data-source="dankcave-product">' . wp_json_encode( $data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . '</script>' . "\n";
}

/**
 * Fallback og:image + twitter:image for pages that don't have one.
 * Yoast will use its site-wide fallback if configured, but many templates end
 * up without an image tag entirely.
 */
add_filter( 'wpseo_opengraph_image', 'dankcave_seo_fallback_share_image', 20 );
add_filter( 'wpseo_twitter_image', 'dankcave_seo_fallback_share_image', 20 );
function dankcave_seo_fallback_share_image( $image ) {
	if ( ! empty( $image ) ) { return $image; }

	// Product/post/page featured image
	if ( is_singular() && has_post_thumbnail() ) {
		$img = wp_get_attachment_image_url( get_post_thumbnail_id(), 'large' );
		if ( $img ) { return $img; }
	}
	// Custom theme mod (Customizer setting)
	$mod = get_theme_mod( 'dankcave_share_image' );
	if ( $mod ) { return $mod; }
	// Site logo as last resort
	$logo_id = get_theme_mod( 'custom_logo' );
	if ( $logo_id ) {
		$img = wp_get_attachment_image_url( $logo_id, 'full' );
		if ( $img ) { return $img; }
	}
	return $image;
}


/**
 * /llms.txt — the emerging standard for exposing site content to LLMs.
 * https://llmstxt.org — a text index of the site's most important sections.
 */
add_action( 'init', 'dankcave_register_llms_txt' );
function dankcave_register_llms_txt() {
	add_rewrite_rule( '^llms\.txt$', 'index.php?dankcave_llms=1', 'top' );
	add_rewrite_rule( '^llms-full\.txt$', 'index.php?dankcave_llms=full', 'top' );
	add_rewrite_tag( '%dankcave_llms%', '(1|full)' );
}
// Stop WP from redirecting /llms.txt → /llms.txt/ (canonical URL normalisation
// otherwise sends the request through a 301 that clobbers our text/plain header).
add_filter( 'redirect_canonical', 'dankcave_llms_skip_canonical', 10, 2 );
function dankcave_llms_skip_canonical( $redirect_url, $requested_url ) {
	if ( preg_match( '#/llms(?:-full)?\.txt/?$#', $requested_url ) ) { return false; }
	return $redirect_url;
}

add_action( 'template_redirect', 'dankcave_serve_llms_txt' );
function dankcave_serve_llms_txt() {
	$mode = get_query_var( 'dankcave_llms' );
	if ( ! $mode ) { return; }
	nocache_headers();
	header( 'Content-Type: text/plain; charset=UTF-8' );
	// Disable 404 status from an empty query
	status_header( 200 );

	$site_name = get_bloginfo( 'name' );
	$site_desc = get_bloginfo( 'description' );
	$home      = home_url( '/' );

	echo "# $site_name\n\n";
	echo "> $site_desc — curated bongs, dab rigs, vapes, and rolling gear. Adults 21+ only.\n\n";
	echo "## About\n\n";
	echo "$site_name is an independent smoke-shop specialising in glass, vapes, and small tools.\n";
	echo "Every piece is picked by hand; free discreet shipping over \$50; 30-day returns.\n\n";
	echo "## Key sections\n\n";
	echo "- [Shop all](" . wc_get_page_permalink( 'shop' ) . "): full product catalogue\n";
	if ( $blog_id = get_option( 'page_for_posts' ) ) {
		echo "- [Journal](" . get_permalink( $blog_id ) . "): guides, gear reviews, cannabis culture\n";
	}
	echo "- [Cart](" . wc_get_cart_url() . ")\n";
	echo "- [Checkout](" . wc_get_checkout_url() . ")\n\n";

	// Categories
	$cats = get_terms( array( 'taxonomy' => 'product_cat', 'hide_empty' => true, 'number' => 20, 'orderby' => 'count', 'order' => 'DESC' ) );
	if ( $cats && ! is_wp_error( $cats ) ) {
		echo "## Categories\n\n";
		foreach ( $cats as $c ) {
			echo "- [{$c->name}](" . get_term_link( $c ) . "): {$c->count} products\n";
		}
		echo "\n";
	}

	if ( 'full' === $mode ) {
		// Full mode enumerates every published product + post for deep LLM indexing.
		$products = wc_get_products( array( 'limit' => 500, 'status' => 'publish', 'orderby' => 'popularity' ) );
		echo "## Products (" . count( $products ) . ")\n\n";
		foreach ( $products as $p ) {
			$price = wp_strip_all_tags( $p->get_price_html() );
			echo "- [" . $p->get_name() . "](" . $p->get_permalink() . "): $price";
			$desc = wp_strip_all_tags( $p->get_short_description() ?: $p->get_description() );
			if ( $desc ) { echo ' — ' . wp_trim_words( $desc, 25, '…' ); }
			echo "\n";
		}
		echo "\n";

		$posts = get_posts( array( 'numberposts' => 100, 'post_type' => 'post' ) );
		if ( $posts ) {
			echo "## Journal (" . count( $posts ) . ")\n\n";
			foreach ( $posts as $post ) {
				echo "- [{$post->post_title}](" . get_permalink( $post ) . "): " . wp_trim_words( wp_strip_all_tags( $post->post_excerpt ?: $post->post_content ), 25, '…' ) . "\n";
			}
		}
	}
	exit;
}
