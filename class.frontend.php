<?php

// Exit If Accessed Directly
if( ! defined( 'ABSPATH' ) ) { exit; }

// Init: Define Custom Post Type For Signup Forms
add_action( 'init', function() {
	$args = [
		'capability_type' => 'page',
		'has_archive' => true,
		'hierarchical' => true,
		'labels' => [
			'menu_name' => __( 'Benchmark' ),
			'name' => __( 'Benchmark Email Signup Forms' ),
			'not_found' => __( 'No forms were found.' ),
			'singular_name' => __( 'Benchmark Email Signup Form' ),
			//$labels->add_new = 'Add Benchmark Form';
			//$labels->add_new_item = 'Add Benchmark Form';
			//$labels->edit_item = 'Edit Benchmark Form';
			//$labels->new_item = 'Benchmark Form';
			//$labels->view_item = 'View Benchmark Form';
			//$labels->search_items = 'Search Benchmark Form';
			//$labels->not_found_in_trash = 'No Benchmark Form found in Trash';
			//$labels->all_items = 'All Benchmark Form';
			//$labels->name_admin_bar = 'Benchmark Form';
		],
		'menu_icon' => 'dashicons-email',
		'public' => true,
		'query_var' => true,
		'rewrite' => [ 'slug' => 'benchmark-form' ],
		'show_ui' => true,
		'supports' => [
			'title',
			'editor',
			'revisions',
			'page-attributes',
		],
	];
	register_post_type( 'benchmark-form', $args );
} );

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


// Front End Plugin Logic
class wpbme_frontend {

	// Match a Contact List - Helper Function
	static function match_list( $list_slug ) {

		// Load Lists, If Not Already Loaded
		$lists = wpbme_api::get_lists();

		// Handle Error Retrieving Lists
		if( ! is_array( $lists ) ) { return false; }

		// Loop Contact Lists
		foreach( $lists as $list ) {

			// Skip Bad Result
			if( empty( $list->ID ) || empty( $list->Name ) ) { continue; }

			// Handle a Match
			if( $list->Name == wpbme_frontend::$list_names[$list_slug] ) {
				return $list->ID;
			}
		}

		// Add Missing Contact List
		return wpbme_api::add_list( wpbme_frontend::$list_names[$list_slug] );
	}
}
