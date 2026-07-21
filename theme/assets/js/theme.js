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

	// TODO: Cart drawer open/close (when we build the mini-cart)
	// TODO: Product image gallery swap
	// TODO: Sticky header on scroll (if design calls for it)
} );
