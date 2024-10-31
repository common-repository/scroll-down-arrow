jQuery( document ).ready( function( $ ) {

	let duration_time = parseInt($('div').filter('[data-duration_time]').data('duration_time'), 10);
	let scrolling           = $( 'div' ).filter( '[data-scrolling]' ).data( 'scrolling' );
	let enable_duration    = $( 'div' ).filter( '[data-enable_duration]' ).data( 'enable_duration' );

	 if ( $( '#ep-arrow' ).length > 0 ){

		 // Display the Arrow only if the page loads from the top
		 if ( enable_duration && $( window ).scrollTop() ) {
			 $( '#ep-arrow' ).css( 'display', 'none' );
		 }
		 //Fade out after set duration
		 if ( enable_duration == true ) {
			 setTimeout( function() {

				 $( '#ep-arrow' ).fadeOut( 800 );

			 },   ( duration_time * 1000 ) );
		 }

		 if ( scrolling == true ) {
			 $( window ).scroll( function() {
				 if ( $( window ).scrollTop() > 0) {
					 $( '#ep-arrow' ).fadeOut( 1000 );
				 }
			 });
		 }
	}
});


