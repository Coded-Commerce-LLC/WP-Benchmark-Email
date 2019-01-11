<?php

// Exit If Accessed Directly
if( ! defined( 'ABSPATH' ) ) { exit; }

// Front End Plugin Logic
class wpbme_frontend {


	// Load Translations
	static function plugins_loaded() {
		load_plugin_textdomain( 'wp-benchmark-email', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	}


	// AJAX Load Script
	static function wp_enqueue_scripts() {
		if( ! function_exists( 'is_checkout' ) || ! is_checkout() ) { return; }
		wp_enqueue_script( 'wpbme_frontend', plugin_dir_url( __FILE__ ) . 'frontend.js', [ 'jquery' ], null );
		wp_localize_script( 'wpbme_frontend', 'wpbme_ajax_object', [ 'ajax_url' => admin_url( 'admin-ajax.php' ) ] );
	}


	// AJAX Routing
	static function wp_ajax__wpbme_action() {

		// Sync Action Is Requested
		if( empty( $_POST['sync'] ) ) { return; }
		switch( $_POST['sync'] ) {

			// API Key
			case 'get_api_key':
				if( empty( $_POST['user'] ) || empty( $_POST['pass'] ) ) { return; }
				$response = wpbme_api::get_api_key( $_POST['user'], $_POST['pass'] );
				echo $response ? $response : __( 'Error - Please try again', 'wp-benchmark-email' );
				wp_die();

		}

	}


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
