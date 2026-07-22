// -----------------------------------------------------------------------------
// Dankcave theme JS. Vanilla, no jQuery.
// -----------------------------------------------------------------------------

document.addEventListener( 'DOMContentLoaded', function () {

	// Mobile hamburger toggle
	const toggle = document.querySelector( '.site-header__toggle' );
	const mobileNav = document.getElementById( 'primary-nav-mobile' );

	if ( toggle && mobileNav ) {
		toggle.addEventListener( 'click', function () {
			const isOpen = mobileNav.getAttribute( 'data-open' ) === 'true';
			const nextOpen = ! isOpen;
			mobileNav.setAttribute( 'data-open', String( nextOpen ) );
			mobileNav.hidden = ! nextOpen;
			toggle.setAttribute( 'aria-expanded', String( nextOpen ) );
			document.body.classList.toggle( 'nav-open', nextOpen );
		} );

		// Close the drawer when a link inside it is clicked (so mid-nav clicks feel snappy)
		mobileNav.addEventListener( 'click', function ( e ) {
			if ( e.target.closest( 'a' ) ) {
				mobileNav.setAttribute( 'data-open', 'false' );
				mobileNav.hidden = true;
				toggle.setAttribute( 'aria-expanded', 'false' );
				document.body.classList.remove( 'nav-open' );
			}
		} );

		// Close on Escape for keyboard users
		document.addEventListener( 'keydown', function ( e ) {
			if ( e.key === 'Escape' && mobileNav.getAttribute( 'data-open' ) === 'true' ) {
				mobileNav.setAttribute( 'data-open', 'false' );
				mobileNav.hidden = true;
				toggle.setAttribute( 'aria-expanded', 'false' );
				document.body.classList.remove( 'nav-open' );
				toggle.focus();
			}
		} );
	}

	// PDP: swap hero image when a thumbnail is clicked.
	const pdpHero = document.querySelector( '[data-pdp-hero]' );
	const pdpThumbs = document.querySelectorAll( '.pdp-gallery__thumb' );
	if ( pdpHero && pdpThumbs.length ) {
		pdpThumbs.forEach( function ( thumb ) {
			thumb.addEventListener( 'click', function () {
				const full = thumb.getAttribute( 'data-full' );
				if ( full ) {
					pdpHero.src = full;
				}
				pdpThumbs.forEach( function ( t ) { t.classList.remove( 'is-active' ); } );
				thumb.classList.add( 'is-active' );
			} );
		} );
	}

	// PDP: swap hero image when a variation is selected on variable products.
	// WooCommerce's variations JS emits `found_variation` on the form. Older
	// codebases dispatched a jQuery event only; we bind both jQuery (if present)
	// and a native listener so image swap works regardless.
	const variationsForm = document.querySelector( 'form.variations_form' );
	if ( variationsForm && pdpHero ) {
		const originalSrc = pdpHero.getAttribute( 'src' );
		function applyVariationImage( variation ) {
			if ( ! variation || ! variation.image ) { return; }
			const img = variation.image;
			const src = img.src || img.full_src || img.url;
			if ( src ) { pdpHero.src = src; }
		}
		function resetVariationImage() {
			if ( originalSrc ) { pdpHero.src = originalSrc; }
		}
		// Native — WC fires this via jQuery so we listen for both.
		variationsForm.addEventListener( 'found_variation', function ( e ) {
			applyVariationImage( e.detail || ( e.originalEvent && e.originalEvent.detail ) );
		} );
		variationsForm.addEventListener( 'reset_data', resetVariationImage );
		// jQuery fallback (WooCommerce ships jQuery via wp-includes on classic themes).
		if ( window.jQuery ) {
			window.jQuery( variationsForm )
				.on( 'found_variation', function ( _e, variation ) { applyVariationImage( variation ); } )
				.on( 'reset_data', resetVariationImage );
		}
	}

	// Landing scroll progress bar — thin yellow bar that fills as the user
	// scrolls. Cheap: one scroll listener that reads scrollHeight/clientHeight.
	// Emitted by page-templates/landing.php on long-form pages (legal pages).
	( function () {
		const bar = document.querySelector( '.dc-landing-progress__bar' );
		if ( ! bar ) return;
		const paint = function () {
			const h   = document.documentElement;
			const max = h.scrollHeight - h.clientHeight;
			bar.style.width = ( max > 0 ? Math.min( 100, ( h.scrollTop / max ) * 100 ) : 0 ) + '%';
		};
		window.addEventListener( 'scroll', paint, { passive: true } );
		paint();
	} )();

	// Accordions: animate open/close with a max-height transition. The native
	// <details> element snaps instantly, so we override the toggle and animate
	// a wrapper around the content. Applies to every <details> on the site —
	// PDP tabs, FAQ patterns, anything future. If a details already has an
	// explicit .*__body child (like PDP), we use that; otherwise we auto-wrap
	// everything after <summary> in a .dc-acc__body div so we have something
	// to size.
	document.querySelectorAll( 'details.pdp-accordion, details.pattern-contact__acc, details.wp-block-details' ).forEach( function ( acc ) {
		if ( acc.dataset.dcAccInit === '1' ) { return; }
		acc.dataset.dcAccInit = '1';

		const summary = acc.querySelector( 'summary, .pdp-accordion__head' );
		if ( ! summary ) { return; }

		let body = acc.querySelector( '.pdp-accordion__body' );
		if ( ! body ) {
			body = document.createElement( 'div' );
			body.className = 'dc-acc__body';
			// Move every sibling after <summary> into the wrapper.
			let next = summary.nextSibling;
			while ( next ) {
				const move = next;
				next = next.nextSibling;
				body.appendChild( move );
			}
			acc.appendChild( body );
		}

		body.style.overflow   = 'hidden';
		body.style.transition = 'max-height .35s cubic-bezier(.2,.8,.2,1), opacity .25s ease';

		function measure() { return body.scrollHeight; }

		function open() {
			acc.open = true;
			body.style.maxHeight = measure() + 'px';
			body.style.opacity   = '1';
			body.addEventListener( 'transitionend', function once( e ) {
				if ( e.propertyName !== 'max-height' ) { return; }
				body.style.maxHeight = 'none'; // allow content to grow naturally after opening
				body.removeEventListener( 'transitionend', once );
			} );
		}
		function close() {
			body.style.maxHeight = measure() + 'px';
			void body.offsetHeight; // force reflow so the transition kicks
			body.style.maxHeight = '0px';
			body.style.opacity   = '0';
			body.addEventListener( 'transitionend', function once( e ) {
				if ( e.propertyName !== 'max-height' ) { return; }
				acc.open = false;
				body.removeEventListener( 'transitionend', once );
			} );
		}

		// Initial state — collapsed unless authored with `open`.
		if ( acc.hasAttribute( 'open' ) ) {
			body.style.maxHeight = 'none';
			body.style.opacity   = '1';
		} else {
			body.style.maxHeight = '0px';
			body.style.opacity   = '0';
		}

		summary.addEventListener( 'click', function ( e ) {
			e.preventDefault();
			if ( acc.hasAttribute( 'open' ) ) { close(); } else { open(); }
		} );
	} );

	// Header search modal — full-screen overlay with a live REST product search.
	const searchOpen  = document.querySelector( '[data-search-open]' );
	const searchClose = document.querySelectorAll( '[data-search-close]' );
	const searchModal = document.getElementById( 'search-modal' );
	const searchInput = document.querySelector( '[data-search-input]' );
	const searchResults = document.querySelector( '[data-search-results]' );
	const searchHint  = document.querySelector( '[data-search-hint]' );

	function openSearch() {
		if ( ! searchModal ) return;
		searchModal.hidden = false;
		searchModal.setAttribute( 'aria-hidden', 'false' );
		requestAnimationFrame( () => searchModal.setAttribute( 'data-open', 'true' ) );
		document.body.classList.add( 'search-open' );
		setTimeout( () => searchInput && searchInput.focus(), 60 );
	}
	function closeSearch() {
		if ( ! searchModal ) return;
		searchModal.removeAttribute( 'data-open' );
		searchModal.setAttribute( 'aria-hidden', 'true' );
		setTimeout( () => { searchModal.hidden = true; }, 200 );
		document.body.classList.remove( 'search-open' );
	}

	if ( searchOpen && searchModal ) {
		searchOpen.addEventListener( 'click', openSearch );
	}
	searchClose.forEach( btn => btn.addEventListener( 'click', closeSearch ) );
	if ( searchModal ) {
		searchModal.addEventListener( 'click', function ( e ) {
			// Backdrop click closes; clicks on the inner card don't propagate through
			if ( e.target === searchModal ) closeSearch();
		} );
		document.addEventListener( 'keydown', function ( e ) {
			if ( e.key === 'Escape' && searchModal.getAttribute( 'data-open' ) === 'true' ) closeSearch();
		} );
	}

	// Live-search: debounce on input and hit the WP REST wp/v2/search endpoint.
	if ( searchInput && searchResults ) {
		let debounceHandle = null;
		let lastQuery = '';
		async function runSearch( q ) {
			if ( ! q || q.length < 2 ) {
				searchResults.hidden = true;
				searchResults.innerHTML = '';
				if ( searchHint ) searchHint.hidden = false;
				return;
			}
			if ( searchHint ) searchHint.hidden = true;
			try {
				const url = '/wp-json/wp/v2/search?type=post&subtype=product,post,page&per_page=8&search=' + encodeURIComponent( q );
				const r = await fetch( url, { credentials: 'same-origin' } );
				if ( ! r.ok ) { throw new Error( r.status ); }
				const data = await r.json();
				if ( q !== lastQuery ) return; // stale
				if ( ! data.length ) {
					searchResults.hidden = false;
					searchResults.innerHTML = '<div class="search-modal-item" style="justify-content:center;color:rgba(255,255,255,.55)">No matches for &ldquo;' + escapeHtml( q ) + '&rdquo;. Try a broader term.</div>';
					return;
				}
				searchResults.hidden = false;
				searchResults.innerHTML = data.map( function ( it ) {
					const meta = it.subtype === 'product' ? 'Product' : ( it.subtype === 'post' ? 'Article' : 'Page' );
					// WP REST returns titles already HTML-entity encoded, so use as-is.
					return '<a class="search-modal-item" href="' + escapeAttr( it.url ) + '">'
						+ '<div class="search-modal-item__info">'
						+ '<div class="search-modal-item__title">' + it.title + '</div>'
						+ '<div class="search-modal-item__meta">' + meta + '</div>'
						+ '</div></a>';
				} ).join( '' );
			} catch ( _err ) {
				searchResults.hidden = false;
				searchResults.innerHTML = '<div class="search-modal-item" style="justify-content:center;color:rgba(255,255,255,.55)">Search unavailable — press Enter to load results on the results page.</div>';
			}
		}
		function escapeHtml( s ) {
			return String( s ).replace( /[&<>"]/g, c => ( { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;' }[ c ] ) );
		}
		function escapeAttr( s ) { return escapeHtml( s ); }
		searchInput.addEventListener( 'input', function ( e ) {
			const q = e.target.value.trim();
			lastQuery = q;
			clearTimeout( debounceHandle );
			debounceHandle = setTimeout( () => runSearch( q ), 220 );
		} );
	}

	// Checkout inline toggles for login form + coupon form
	document.querySelectorAll( '[data-dc-toggle-login]' ).forEach( function ( link ) {
		link.addEventListener( 'click', function ( e ) {
			e.preventDefault();
			const target = document.querySelector( '[data-dc-inline-login]' );
			if ( ! target ) return;
			const isOpen = ! target.hasAttribute( 'hidden' );
			if ( isOpen ) {
				target.setAttribute( 'hidden', '' );
			} else {
				target.removeAttribute( 'hidden' );
				const input = target.querySelector( 'input[type="text"], input[type="email"]' );
				if ( input ) setTimeout( () => input.focus(), 20 );
			}
		} );
	} );
	document.querySelectorAll( '[data-dc-toggle-coupon]' ).forEach( function ( link ) {
		link.addEventListener( 'click', function ( e ) {
			e.preventDefault();
			const target = document.querySelector( '[data-dc-inline-coupon]' );
			if ( ! target ) return;
			const isOpen = ! target.hasAttribute( 'hidden' );
			if ( isOpen ) {
				target.setAttribute( 'hidden', '' );
			} else {
				target.removeAttribute( 'hidden' );
				const input = target.querySelector( 'input[type="text"]' );
				if ( input ) setTimeout( () => input.focus(), 20 );
			}
		} );
	} );

	// Product card hover actions — Wishlist (localStorage), Compare (defer),
	// Quick view (opens modal with fetched product HTML).
	const wishlistKey = 'dc-wishlist';
	function readWishlist() {
		try { return JSON.parse( localStorage.getItem( wishlistKey ) || '[]' ); } catch (_) { return []; }
	}
	function writeWishlist( ids ) {
		try { localStorage.setItem( wishlistKey, JSON.stringify( ids ) ); } catch (_) {}
	}
	function paintWishlistState() {
		const list = readWishlist();
		document.querySelectorAll( '[data-dc-wishlist]' ).forEach( btn => {
			const id = btn.getAttribute( 'data-product-id' );
			if ( list.includes( id ) ) btn.classList.add( 'is-active' );
			else btn.classList.remove( 'is-active' );
		} );
	}
	paintWishlistState();
	document.addEventListener( 'click', function ( e ) {
		const wish = e.target.closest( '[data-dc-wishlist]' );
		if ( wish ) {
			e.preventDefault();
			const id = wish.getAttribute( 'data-product-id' );
			const list = readWishlist();
			const idx = list.indexOf( id );
			if ( idx >= 0 ) list.splice( idx, 1 ); else list.push( id );
			writeWishlist( list );
			paintWishlistState();
			return;
		}
		const compare = e.target.closest( '[data-dc-compare]' );
		if ( compare ) {
			e.preventDefault();
			toggleCompare( compare.getAttribute( 'data-product-id' ) );
			return;
		}
	} );

	// -------------------------------------------------------------------------
	// Compare — localStorage backed, floating tray at the bottom, click to open
	// a side-by-side attribute modal.
	// -------------------------------------------------------------------------
	const compareKey  = 'dc-compare';
	const compareMax  = 4;
	const tray        = document.getElementById( 'dc-compare-tray' );
	const trayThumbs  = tray ? tray.querySelector( '[data-dc-compare-thumbs]' ) : null;
	const trayCount   = tray ? tray.querySelector( '[data-dc-compare-count]' ) : null;
	const compareModal = document.getElementById( 'dc-compare-modal' );
	const compareBody  = compareModal ? compareModal.querySelector( '[data-dc-compare-body]' ) : null;

	function readCompare() {
		try { return JSON.parse( localStorage.getItem( compareKey ) || '[]' ); } catch (_) { return []; }
	}
	function writeCompare( ids ) {
		try { localStorage.setItem( compareKey, JSON.stringify( ids ) ); } catch (_) {}
	}
	function paintCompareState() {
		const list = readCompare();
		document.querySelectorAll( '[data-dc-compare]' ).forEach( btn => {
			const id = btn.getAttribute( 'data-product-id' );
			if ( list.includes( id ) ) { btn.classList.add( 'is-active' ); btn.setAttribute( 'data-tooltip', 'In compare' ); }
			else { btn.classList.remove( 'is-active' ); btn.setAttribute( 'data-tooltip', 'Add to compare' ); }
		} );
		if ( trayCount ) trayCount.textContent = list.length;
		if ( ! tray ) return;
		if ( list.length === 0 ) {
			tray.removeAttribute( 'data-visible' );
			setTimeout( () => { tray.hidden = true; }, 200 );
			return;
		}
		tray.hidden = false;
		requestAnimationFrame( () => tray.setAttribute( 'data-visible', 'true' ) );
		// Fetch thumb HTML for the current list
		if ( trayThumbs ) {
			const url = ( window.dcAjax && dcAjax.url ? dcAjax.url : '/wp-admin/admin-ajax.php' ) +
				'?action=dankcave_compare_thumbs&ids=' + encodeURIComponent( list.join( ',' ) );
			fetch( url, { credentials: 'same-origin' } )
				.then( r => r.json() )
				.then( json => { if ( json && json.success ) trayThumbs.innerHTML = json.data.html; } )
				.catch( () => {} );
		}
	}
	function toggleCompare( id ) {
		const list = readCompare();
		const idx = list.indexOf( id );
		if ( idx >= 0 ) {
			list.splice( idx, 1 );
		} else {
			if ( list.length >= compareMax ) {
				alert( 'You can compare up to ' + compareMax + ' products at a time.' );
				return;
			}
			list.push( id );
		}
		writeCompare( list );
		paintCompareState();
	}
	paintCompareState();

	// Tray thumb remove
	if ( tray ) {
		tray.addEventListener( 'click', function ( e ) {
			const rm = e.target.closest( '.dc-compare-tray__thumb-remove' );
			if ( rm ) {
				toggleCompare( rm.getAttribute( 'data-product-id' ) );
				return;
			}
			const clr = e.target.closest( '[data-dc-compare-clear]' );
			if ( clr ) { writeCompare( [] ); paintCompareState(); return; }
			const open = e.target.closest( '[data-dc-compare-open]' );
			if ( open ) { openCompareModal(); return; }
		} );
	}

	function openCompareModal() {
		if ( ! compareModal || ! compareBody ) return;
		const list = readCompare();
		if ( ! list.length ) return;
		compareModal.hidden = false;
		compareModal.setAttribute( 'aria-hidden', 'false' );
		requestAnimationFrame( () => compareModal.setAttribute( 'data-open', 'true' ) );
		document.body.classList.add( 'dc-drawer-open' );
		compareBody.innerHTML = '<div class="dc-quickview__loading">Loading…</div>';
		const url = ( window.dcAjax && dcAjax.url ? dcAjax.url : '/wp-admin/admin-ajax.php' ) +
			'?action=dankcave_compare_table&ids=' + encodeURIComponent( list.join( ',' ) );
		fetch( url, { credentials: 'same-origin' } )
			.then( r => r.json() )
			.then( json => { if ( json && json.success ) compareBody.innerHTML = json.data.html; } )
			.catch( () => { compareBody.innerHTML = '<div class="dc-quickview__loading">Network error.</div>'; } );
	}
	function closeCompareModal() {
		if ( ! compareModal ) return;
		compareModal.removeAttribute( 'data-open' );
		compareModal.setAttribute( 'aria-hidden', 'true' );
		setTimeout( () => { compareModal.hidden = true; compareBody && ( compareBody.innerHTML = '' ); }, 300 );
		if ( ! document.getElementById( 'dc-cart-drawer' )?.getAttribute( 'data-open' ) && ! document.getElementById( 'dc-quickview' )?.getAttribute( 'data-open' ) ) {
			document.body.classList.remove( 'dc-drawer-open' );
		}
	}
	if ( compareModal ) {
		document.querySelectorAll( '[data-dc-compare-close]' ).forEach( el => el.addEventListener( 'click', closeCompareModal ) );
		// Backdrop click closes modal on mobile — big touch target.
		const backdrop = compareModal.querySelector( '.dc-compare-modal__backdrop' );
		if ( backdrop ) backdrop.addEventListener( 'click', closeCompareModal );
		document.addEventListener( 'keydown', function ( e ) {
			if ( e.key === 'Escape' && compareModal.getAttribute( 'data-open' ) === 'true' ) closeCompareModal();
		} );
		// Per-column remove — updates list, then either re-renders the table or
		// closes the modal if the list is empty.
		compareModal.addEventListener( 'click', function ( e ) {
			const rm = e.target.closest( '[data-dc-compare-remove]' );
			if ( ! rm ) return;
			e.preventDefault();
			const id = rm.getAttribute( 'data-dc-compare-remove' );
			const list = readCompare();
			const idx = list.indexOf( id );
			if ( idx >= 0 ) list.splice( idx, 1 );
			writeCompare( list );
			paintCompareState();
			if ( ! list.length ) { closeCompareModal(); return; }
			// Re-render body with the shortened list.
			const url = ( window.dcAjax && dcAjax.url ? dcAjax.url : '/wp-admin/admin-ajax.php' ) +
				'?action=dankcave_compare_table&ids=' + encodeURIComponent( list.join( ',' ) );
			compareBody.classList.add( 'is-refreshing' );
			fetch( url, { credentials: 'same-origin' } )
				.then( r => r.json() )
				.then( json => { if ( json && json.success ) compareBody.innerHTML = json.data.html; compareBody.classList.remove( 'is-refreshing' ); } )
				.catch( () => { compareBody.classList.remove( 'is-refreshing' ); } );
		} );
	}

	// Quick view modal — opens on eye click OR on the "Options →" button of
	// variable products (so users can pick a variation from the card).
	const qv       = document.getElementById( 'dc-quickview' );
	const qvBody   = qv ? qv.querySelector( '[data-dc-quickview-body]' ) : null;
	function openQuickView( productId ) {
		if ( ! qv || ! qvBody ) return;
		qv.hidden = false;
		qv.setAttribute( 'aria-hidden', 'false' );
		requestAnimationFrame( () => qv.setAttribute( 'data-open', 'true' ) );
		document.body.classList.add( 'dc-drawer-open' );
		qvBody.innerHTML = '<div class="dc-quickview__loading">Loading…</div>';
		const url = ( window.dcAjax && dcAjax.url ? dcAjax.url : '/wp-admin/admin-ajax.php' ) +
			'?action=dankcave_quickview&product_id=' + encodeURIComponent( productId );
		fetch( url, { credentials: 'same-origin' } )
			.then( r => r.json() )
			.then( json => {
				if ( ! json || ! json.success ) {
					qvBody.innerHTML = '<div class="dc-quickview__loading">Could not load this product.</div>';
					return;
				}
				qvBody.innerHTML = json.data.html;
				// Attach WC variations JS to the newly-injected form.
				if ( window.jQuery ) {
					const form = qvBody.querySelector( 'form.variations_form' );
					if ( form ) window.jQuery( form ).wc_variation_form();
				}
			} )
			.catch( () => { qvBody.innerHTML = '<div class="dc-quickview__loading">Network error.</div>'; } );
	}
	function closeQuickView() {
		if ( ! qv ) return;
		qv.removeAttribute( 'data-open' );
		qv.setAttribute( 'aria-hidden', 'true' );
		setTimeout( () => { qv.hidden = true; qvBody && ( qvBody.innerHTML = '' ); }, 300 );
		document.body.classList.remove( 'dc-drawer-open' );
	}
	if ( qv ) {
		document.querySelectorAll( '[data-dc-quickview-close]' ).forEach( el => el.addEventListener( 'click', closeQuickView ) );
		document.addEventListener( 'keydown', function ( e ) {
			if ( e.key === 'Escape' && qv.getAttribute( 'data-open' ) === 'true' ) closeQuickView();
		} );
	}
	document.addEventListener( 'click', function ( e ) {
		const qvBtn = e.target.closest( '[data-dc-quickview]' );
		if ( qvBtn ) {
			e.preventDefault();
			openQuickView( qvBtn.getAttribute( 'data-product-id' ) );
		}
	} );
	// Intercept "Options →" clicks on variable products so they open QV instead.
	document.addEventListener( 'click', function ( e ) {
		const opt = e.target.closest( '.product-card__add--needs-options' );
		if ( opt ) {
			e.preventDefault();
			const card = opt.closest( '.product-card' );
			const id = card ? card.getAttribute( 'data-product-id' ) : null;
			if ( id ) openQuickView( id );
		}
	} );

	// Cart drawer — slides in from the right on cart click or after AJAX add.
	const drawer = document.getElementById( 'dc-cart-drawer' );
	function openDrawer() {
		if ( ! drawer ) return;
		drawer.hidden = false;
		drawer.setAttribute( 'aria-hidden', 'false' );
		requestAnimationFrame( () => drawer.setAttribute( 'data-open', 'true' ) );
		document.body.classList.add( 'dc-drawer-open' );
	}
	function closeDrawer() {
		if ( ! drawer ) return;
		drawer.removeAttribute( 'data-open' );
		drawer.setAttribute( 'aria-hidden', 'true' );
		setTimeout( () => { drawer.hidden = true; }, 320 );
		document.body.classList.remove( 'dc-drawer-open' );
	}
	if ( drawer ) {
		document.querySelectorAll( '[data-dc-drawer-close]' ).forEach( el => el.addEventListener( 'click', closeDrawer ) );
		document.addEventListener( 'keydown', function ( e ) {
			if ( e.key === 'Escape' && drawer.getAttribute( 'data-open' ) === 'true' ) closeDrawer();
		} );
	}

	// Cart pill in header opens the drawer instead of navigating (unless
	// Cmd/Ctrl-clicked — allow normal open-in-tab behaviour).
	const cartPill = document.querySelector( '.cart-summary' );
	if ( cartPill && drawer ) {
		cartPill.addEventListener( 'click', function ( e ) {
			if ( e.metaKey || e.ctrlKey || e.shiftKey || e.button !== 0 ) return;
			e.preventDefault();
			openDrawer();
		} );
	}

	// After WooCommerce completes an AJAX add-to-cart, open the drawer.
	if ( window.jQuery ) {
		window.jQuery( document.body ).on( 'added_to_cart', function () { openDrawer(); } );
		// Removal from inside the drawer — hook a click on remove links and
		// call the wc-ajax=remove_from_cart endpoint so the drawer updates in place.
		document.addEventListener( 'click', function ( e ) {
			const remove = e.target.closest( '.dc-cart-drawer-item__remove' );
			if ( ! remove ) return;
			e.preventDefault();
			const href = remove.getAttribute( 'href' );
			// hit the URL then trigger a fragments refresh
			fetch( href, { credentials: 'same-origin' } ).then( () => {
				window.jQuery.ajax( {
					type: 'post',
					url: ( window.wc_cart_fragments_params && window.wc_cart_fragments_params.wc_ajax_url ) ? window.wc_cart_fragments_params.wc_ajax_url.replace( '%%endpoint%%', 'get_refreshed_fragments' ) : '/?wc-ajax=get_refreshed_fragments',
					success: function ( data ) {
						if ( data && data.fragments ) {
							window.jQuery.each( data.fragments, function ( key, value ) {
								window.jQuery( key ).replaceWith( value );
							} );
						}
					},
				} );
			} );
		} );
	}
} );
