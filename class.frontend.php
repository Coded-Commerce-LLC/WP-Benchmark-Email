<?php

// Exit If Accessed Directly
if( ! defined( 'ABSPATH' ) ) { exit; }

// Enqueue Scripts: Frontend JS
add_action( 'wp_enqueue_scripts', function() {
	if( ! function_exists( 'is_checkout' ) || ! is_checkout() ) { return; }
	wp_enqueue_script( 'wpbme_frontend', plugin_dir_url( __FILE__ ) . 'frontend.js', [ 'jquery' ], null );
	wp_localize_script( 'wpbme_frontend', 'wpbme_ajax_object', [ 'ajax_url' => admin_url( 'admin-ajax.php' ) ] );
} );

// I18N
add_action( 'plugins_loaded', function() {
	load_plugin_textdomain( 'benchmark-email-lite', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
} );

// JB Tracker
add_action( 'wp_footer', function() {

	// Handle Disabled
	$tracking_disabled = get_option( 'wpbme_tracking_disable' );
	if( $tracking_disabled == 'yes' ) { return; }

	// Handle Disconnected
	$wpbme_ap_token = get_option( 'wpbme_ap_token' );
	if( ! $wpbme_ap_token ) { return; }

	// Output Tracker JS
	echo sprintf(
		'
		<script type="text/javascript">
		var _paq = _paq || [];
		( function() {
			if( window.apScriptInserted ) { return; }
			_paq.push( [ "clientToken", "%s" ] );
			var d = document, g = d.createElement( "script" ), s = d.getElementsByTagName( "script" )[0];
			g.type = "text/javascript";
			g.async = true;
			g.defer = true;
			g.src = "https://prod.benchmarkemail.com/tracker.bundle.js";
			s.parentNode.insertBefore( g, s );
			window.apScriptInserted = true;
		} )();
		</script>
		', $wpbme_ap_token
	);
} );
