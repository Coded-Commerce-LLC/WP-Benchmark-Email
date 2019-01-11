
// Track Last Page
var wpbme_page = 1;

// DOM Loaded
jQuery( document ).ready( function( $ ) {

	// Handle API Key Click
	$( 'a#get_api_key' ).click( function() {

		// Prompt User And Pass
		var user = prompt(
			"Please enter your Benchmark Email username", ''
		);
		var pass = prompt(
			"Please enter your Benchmark Email password", ''
		);

		// Validate Input
		if( user === null || pass === null ) { return; }

		// Pass To AJAX Handler
		var data = {
			'action': 'wpbme_action',
			'sync': 'get_api_key',
			'user': user,
			'pass': pass
		};
		$( 'input#wpbme_key' ).val( 'Loading...' );
		$.post( ajaxurl, data, function( response ) {

			// Process Response
			if( response != '' ) {
				$( 'input#wpbme_key' ).val( response );
			}
		} );
	} );

} );
