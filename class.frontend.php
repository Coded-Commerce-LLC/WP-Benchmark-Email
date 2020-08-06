<?php

// Exit If Accessed Directly
if( ! defined( 'ABSPATH' ) ) { exit; }

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
	printf(
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

// Shortcode
add_shortcode( 'benchmark-email-lite', function( $atts ) {
	$form_id = isset( $atts['form_id'] ) ? intval( $atts['form_id'] ) : '';
	if( $form_id ) {
		return wpbme_frontend::get_signup_form( $form_id );
	}
	$widget_id = isset( $atts['widget_id'] ) ? intval( $atts['widget_id'] ) : '';
	if( $widget_id ) {
		$form_id = get_option( 'wpbme_legacy_widget_' . $widget_id );
		if( $form_id ) {
			return wpbme_frontend::get_signup_form( $form_id );
		}
	}
} );

// Front End Class
class wpbme_frontend {

	// Renders Signup Form
	static function get_signup_form( $form_id ) {
		$formdata = get_transient( 'wpbme_js_' . $form_id );
		if( $formdata ) {
			return $formdata->JSCode;
		}
		$form = wpbme_api::get_form_data( $form_id );
		set_transient( 'wpbme_js_' . $form_id, $form, 86400 );
		return $form->JSCode;
	}
}
