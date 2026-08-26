( function () {
	'use strict';

	document.addEventListener( 'DOMContentLoaded', function () {
		var announcement = document.getElementById( 'estAnnouncement' );
		var closeBtn = document.getElementById( 'estAnnouncementClose' );

		if ( announcement && closeBtn ) {
			closeBtn.addEventListener( 'click', function () {
				announcement.classList.add( 'est-is-hidden' );
			} );
		}

		document.querySelectorAll( '.est-carousel' ).forEach( function ( carousel ) {
			var counter = carousel.parentElement.querySelector(
				'.est-carousel-counter[data-total]' +
					( carousel.classList.contains( 'd-lg-none' ) ? '.d-lg-none' : ':not(.d-lg-none)' )
			);

			if ( ! counter ) {
				return;
			}

			var total = parseInt( counter.getAttribute( 'data-total' ), 10 ) || 1;

			carousel.addEventListener( 'slide.bs.carousel', function ( event ) {
				var index = event.to + 1;
				counter.textContent = String( index ).padStart( 2, '0' ) + ' of ' + String( total ).padStart( 2, '0' );
			} );
		} );

		document.querySelectorAll( '.est-gallery-carousel' ).forEach( function ( carousel ) {
			var thumbs = carousel.querySelectorAll( '.est-gallery-thumbs button' );
			var dots = carousel.querySelectorAll( '.est-gallery-dot' );

			carousel.addEventListener( 'slide.bs.carousel', function ( event ) {
				thumbs.forEach( function ( thumb ) {
					var slideTo = parseInt( thumb.getAttribute( 'data-bs-slide-to' ), 10 );
					thumb.classList.toggle( 'active', slideTo === event.to );
				} );

				dots.forEach( function ( dot, index ) {
					dot.classList.toggle( 'active', index === event.to );
				} );
			} );
		} );
	} );
} )();
