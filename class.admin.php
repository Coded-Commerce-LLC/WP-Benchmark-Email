<?php

// Exit If Accessed Directly
if( ! defined( 'ABSPATH' ) ) { exit; }

// TEST FUNCTION
/*
	add_action( 'admin_notices', function() {
		echo sprintf(
			'<div class="notice notice-info"><p>%s</p></div>',
			//wpbme_api::token_renew( '' )
			//wpbme_api::get_ap_token( '' )
			//wpbme_api::goto_ui( '', '/Contacts' )
			//wpbme_api::benchmark_query( 'Client/Authenticate', 'POST', [ 'Username' => 'seanconklin', 'Password' => '' ] )
		);
	} );
*/

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

// Admin Dashboard Items
add_action( 'wp_dashboard_setup', function() {

	// Ensure is_plugin_active() Exists
	if( ! function_exists( 'is_plugin_active' ) ) {
		include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
	}
	$messages = [];

	// Handle Sister Product Dismissal Request
	if( ! empty( $_REQUEST['wpbme_dismiss_sister'] ) && check_admin_referer( 'wpbme_dismiss_sister' ) ) {
		update_option( 'wpbme_sister_dismissed', current_time( 'timestamp') );
	}

	// Check Sister Product
	$wpbme_sister_dismissed = get_option( 'wpbme_sister_dismissed' );
	if(
		$wpbme_sister_dismissed < current_time( 'timestamp') - 86400 * 90
		&& ! is_plugin_active( 'woo-benchmark-email/woo-benchmark-email.php' )
		&& current_user_can( 'activate_plugins' )
	) {

		// Plugin Installed But Not Activated
		if( file_exists( WP_PLUGIN_DIR . '/woo-benchmark-email/woo-benchmark-email.php' ) ) {
			$messages[] = sprintf(
				'
					%s &nbsp; <strong style="font-size:1.2em;"><a href="%s">%s</a></strong>
					<a style="float:right;" href="%s">%s</a>
				',
				__( 'Activate our sister product Woo Benchmark Email to view campaign statistics.', 'benchmark-email-lite' ),
				wpbme_admin::get_sister_activate_link(),
				__( 'Activate Now', 'benchmark-email-lite' ),
				wpbme_admin::get_sister_dismiss_link(),
				__( 'dismiss for 90 days', 'benchmark-email-lite' )
			);

		// Plugin Not Installed
		} else {
			$messages[] = sprintf(
				'
					%s &nbsp; <strong style="font-size:1.2em;"><a href="%s">%s</a></strong>
					<a style="float:right;" href="%s">%s</a>
				',
				__( 'Install our sister product Woo Benchmark Email to view campaign statistics.', 'benchmark-email-lite' ),
				wpbme_admin::get_sister_install_link(),
				__( 'Install Now', 'benchmark-email-lite' ),
				wpbme_admin::get_sister_dismiss_link(),
				__( 'dismiss for 90 days', 'benchmark-email-lite' )
			);
		}
	}

	// Message If Plugin Isn't Configured
	if( empty( get_option( 'wpbme_key' ) ) ) {
		$messages[] = sprintf(
			'%s &nbsp; <strong style="font-size:1.2em;"><a href="admin.php?page=wc-settings&tab=wpbme">%s</a></strong>',
			__( 'Please configure your API Key to use Benchmark Email Lite.', 'benchmark-email-lite' ),
			__( 'Configure Now', 'benchmark-email-lite' )
		);
	}

	// Output Message
	if( $messages ) {
		foreach( $messages as $message ) {
			echo sprintf(
				'<div class="notice notice-info is-dismissible"><p>%s</p></div>',
				print_r( $message, true )
			);
		}
	}
} );

// Plugins Page Link To Settings
add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), function( $links ) {
	$settings = [
		'settings' => sprintf(
			'<a href="%s">%s</a>',
			admin_url( 'options-general.php?page=wpbme_settings' ),
			__( 'Settings', 'benchmark-email-lite' )
		),
	];
	return array_merge( $settings, $links );
} );

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
		[ 'wpbme_settings', 'page_settings' ]
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
		'widgets.php',
	);
} );

// Class For Namespacing Functions
class wpbme_admin {

	// Page Body For Benchmark UI
	static function page_interface() {
		$tab = empty( $_GET['tab'] ) ? '/Emails/Dashboard' : '/' . $_GET['tab'];
		$redirect_url = wpbme_api::authenticate_ui_redirect( $tab );
		if( ! $redirect_url ) {
			wp_redirect( admin_url( 'options-general.php?page=wpbme_settings' ) );
		}
		echo sprintf(
			'
				<div class="wrap">
					<h2>%s</h2>
					<br />
					<iframe src="%s" style="%s">Loading . . .</iframe>
				</div>
			',
			'Benchmark Email Interface',
			$redirect_url,
			'width: 100%; height: 1000px;'
		);
	}

	// Sister Install Link
	static function get_sister_install_link() {
		$action = 'install-plugin';
		$slug = 'woo-benchmark-email';
		return wp_nonce_url(
			add_query_arg(
				[ 'action' => $action, 'plugin' => $slug ],
				admin_url( 'update.php' )
			),
			$action . '_' . $slug
		);
	}

	// Sister Activate Link
	static function get_sister_activate_link( $action='activate' ) {
		$plugin = 'woo-benchmark-email/woo-benchmark-email.php';
		$_REQUEST['plugin'] = $plugin;
		return wp_nonce_url(
			add_query_arg(
				[ 'action' => $action, 'plugin' => $plugin, 'plugin_status' => 'all', 'paged' => '1&s' ],
				admin_url( 'plugins.php' )
			),
			$action . '-plugin_' . $plugin
		);
	}

	// Sister Dismiss Notice Link
	static function get_sister_dismiss_link() {
		$url = wp_nonce_url( 'index.php?wpbme_dismiss_sister=1', 'wpbme_dismiss_sister' );
		return $url;
	}

}
