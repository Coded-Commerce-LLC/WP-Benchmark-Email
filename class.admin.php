<?php

// Exit If Accessed Directly
if( ! defined( 'ABSPATH' ) ) { exit; }

// Admin JavaScripts
add_action( 'admin_enqueue_scripts', function() {
	wp_enqueue_script( 'wpbme_admin', plugin_dir_url( __FILE__ ) . 'admin.js', [ 'jquery' ], null );
} );

// AJAX: Admin Action Handler
add_action( 'wp_ajax_wpbme_action', function() {
	if( empty( $_POST['sync'] ) ) { return; }
	switch( $_POST['sync'] ) {

		// API Key
		case 'get_api_key':
			if( empty( $_POST['user'] ) || empty( $_POST['pass'] ) ) { return; }
			$response = wpbme_api::authenticate( $_POST['user'], $_POST['pass'] );
			wp_send_json( $response );
	}
} );

// Plugins Page Link To Settings
add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), function( $links ) {
	$settings = [
		'settings' => sprintf(
			'<a href="%s">%s</a>',
			admin_url( 'admin.php?page=wpbme_settings' ),
			__( 'Settings', 'benchmark-email-lite' )
		),
	];
	return array_merge( $settings, $links );
} );

// Post To Campaign
add_filter( 'post_row_actions', function( $actions, $post ) {
	$actions['benchmark_p2c'] = sprintf(
		'<a href="%s">%s</a>',
		admin_url( 'admin.php?page=wpbme_interface&post=' . $post->ID ),
		__( 'Create Email Campaign', 'benchmark-email-lite' )
	);
	return $actions;
}, 10, 2 );

// Adds UI Controller Page
add_action( 'admin_menu', function() {
	add_menu_page(
		'Benchmark',
		'Benchmark',
		'manage_options',
		'wpbme_interface',
		[ 'wpbme_admin', 'page_interface' ],
		'dashicons-email'
	);
	add_submenu_page(
		'wpbme_interface',
		'Interface',
		'Interface',
		'manage_options',
		'wpbme_interface',
		[ 'wpbme_admin', 'page_interface' ]
	);
	add_submenu_page(
		'wpbme_interface',
		'Settings',
		'Settings',
		'manage_options',
		'wpbme_settings',
		[ 'wpbme_settings', 'page_settings' ]
	);
	add_submenu_page(
		'wpbme_interface',
		'Signup Form Widgets',
		'Signup Form Widgets',
		'manage_options',
		'widgets.php'
	);
	add_submenu_page(
		'wpbme_interface',
		'Shortcodes',
		'Shortcodes',
		'manage_options',
		'wpbme_shortcodes',
		[ 'wpbme_admin', 'page_shortcodes' ]
	);
} );

// Class For Namespacing Functions
class wpbme_admin {

	// Page Body For Benchmark UI
	static function page_interface() {
		$tab = empty( $_GET['tab'] ) ? '/Emails/Dashboard' : '/' . $_GET['tab'];

		// Handle P2C
		if( ! empty( $_GET['post'] ) && intval( $_GET['post'] ) ) {
			$current_user = wp_get_current_user();
			$post = get_post( $_GET['post'] );
			$content = $post->post_content;
			$content = apply_filters( 'the_content', $content );
			$newemail = wpbme_api::create_email(
				$post->post_title . ' ' . current_time( 'mysql' ),
				$post->post_title,
				$current_user->display_name,
				$current_user->user_email,
				$post->ID
			);
		}

		// Get Authenticated Redirect
		$redirect_url = wpbme_api::authenticate_ui_redirect( $tab );
		if( ! $redirect_url ) {
			wp_redirect( admin_url( 'admin.php?page=wpbme_settings' ) );
		}

		// Output
		echo sprintf(
			'
				<div class="wrap">
					<h2>%s</h2>
					<br />
					<p><a href="%s" target="BMEUI">%s</a></p>
					<iframe id="wpbme_interface" src="%s" style="%s">%s</iframe>
				</div>
			',
			'Benchmark Email Interface',
			$redirect_url,
			__( 'Click to use a new tab if the below fails to load properly in your browser.', 'benchmark-email-lite' ),
			$redirect_url,
			'width: 100%; height: 1000px;',
			__( 'Loading...', 'benchmark-email-lite' )
		);

		// Handle Email Campaign Redirection
		if( ! empty( $newemail->ID ) ) {
			echo sprintf(
				'
					<script type="text/javascript">
					jQuery( document ).ready( function( $ ) {
						$( "iframe#wpbme_interface" ).attr( "src", "%s" ); 
					} );
					</script>
				',
				wpbme_api::$url_ui . 'Emails/Edit?e=' . $newemail->ID
			);
		}
	}

	// Displays Shortcodes
	static function page_shortcodes() {
		$forms = wpbme_api::get_forms();

		// Handle No Forms
		if( ! $forms ) {
			echo sprintf(
				'<p>%s</p>',
				__( 'Please design a signup form first!', 'benchmark-email-lite' )
			);
			return;
		}

		// Has Forms
		echo sprintf(
			'
				<h2>%s</h2>
				<p>%s</p>
			',
			__( 'Shortcodes for Pages and Posts', 'benchmark-email-lite' ),
			__( 'Use these to place a signup form on specific pages or posts within their content bodies.', 'benchmark-email-lite' )
		);

		// Loop Forms
		foreach( $forms as $form ) {
			echo sprintf(
				'
					<p>
						<strong>%s</strong><br />
						<code>[benchmark-email-lite form_id="%d"]</code>
					</p>
				',
				$form->Name,
				$form->ID
			);
		}

		// Manage Forms Button
		echo sprintf(
			'<p><a href="%s" class="button-primary">%s</a></p>',
			admin_url( 'admin.php?page=wpbme_interface&tab=Listbuilder' ),
			__( 'Manage Signup Forms', 'benchmark-email-lite' )
		);
	}
}
