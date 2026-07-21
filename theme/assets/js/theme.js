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

	// PDP accordions: animate open/close with a max-height transition. The
	// native <details> element snaps instantly, so we override the toggle,
	// measure the body's scrollHeight, and interpolate.
	document.querySelectorAll( '.pdp-accordion' ).forEach( function ( acc ) {
		const summary = acc.querySelector( '.pdp-accordion__head' );
		const body    = acc.querySelector( '.pdp-accordion__body' );
		if ( ! summary || ! body ) { return; }

		body.style.overflow = 'hidden';
		body.style.transition = 'max-height .35s cubic-bezier(.2,.8,.2,1), opacity .25s ease, padding .3s ease';

		function open() {
			acc.open = true;
			body.style.maxHeight = body.scrollHeight + 'px';
			body.style.opacity = '1';
		}
		function close() {
			body.style.maxHeight = body.scrollHeight + 'px';
			// force reflow
			void body.offsetHeight;
			body.style.maxHeight = '0px';
			body.style.opacity = '0';
			body.addEventListener( 'transitionend', function once() {
				acc.open = false;
				body.removeEventListener( 'transitionend', once );
			} );
		}

		// Initial state — collapsed unless authored with `open`.
		if ( acc.hasAttribute( 'open' ) ) {
			body.style.maxHeight = 'none';
			body.style.opacity = '1';
		} else {
			body.style.maxHeight = '0px';
			body.style.opacity = '0';
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

	// TODO: Cart drawer open/close (when we build the mini-cart)
} );
