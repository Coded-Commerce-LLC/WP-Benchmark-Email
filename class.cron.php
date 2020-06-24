<?php

// Exit If Accessed Directly
if( ! defined( 'ABSPATH' ) ) { exit; }

// Renew Temporary Auth Token Twice Daily
add_action( 'init', function() {
	if ( ! wp_next_scheduled( 'wpbme_token_renew' ) ) {
		wp_schedule_event( time(), 'twicedaily', 'wpbme_token_renew' );
	}
	add_action( 'wpbme_token_renew', function() {
		wpbme_api::authenticate_ui_renew();
	} );
} );
