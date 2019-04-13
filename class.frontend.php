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
			'name' => __( 'Benchmark Signup Forms' ),
			'not_found' => __( 'No forms were found.' ),
			'singular_name' => __( 'Benchmark Signup Form' ),
			//$labels->add_new = 'Add News';
			//$labels->add_new_item = 'Add News';
			//$labels->edit_item = 'Edit News';
			//$labels->new_item = 'News';
			///$labels->view_item = 'View News';
			//$labels->search_items = 'Search News';
			//$labels->not_found_in_trash = 'No News found in Trash';
			//$labels->all_items = 'All News';
			//$labels->name_admin_bar = 'News';
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
