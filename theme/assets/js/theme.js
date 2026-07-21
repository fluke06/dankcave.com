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

	// TODO: Cart drawer open/close (when we build the mini-cart)
	// TODO: Sticky header on scroll (if design calls for it)
} );
