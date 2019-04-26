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
			$response = wpbme_api::get_api_key( $_POST['user'], $_POST['pass'] );
			echo $response ? $response : __( 'Error - Please try again', 'benchmark-email-lite' );
			wp_die();
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

// CPT Page Settings Link
add_action( 'admin_notices', function() {
	$screen = get_current_screen();
	if( 'benchmark-form' == $screen->post_type && 'edit' == $screen->base ) {
		echo sprintf(
			'<div class="notice notice-info"><p><a href="%s">%s</a></p></div>',
			admin_url( 'options-general.php?page=wpbme_page' ),
			__( 'Benchmark Email Settings', 'benchmark-email-lite' )
		);
	}
} );

// Settings Page CPT Link
add_action( 'admin_notices', function() {
	$screen = get_current_screen();
	if( 'settings_page_wpbme_page' == $screen->id ) {
		echo sprintf(
			'<div class="notice notice-info"><p><a href="%s">%s</a></p></div>',
			admin_url( 'edit.php?post_type=benchmark-form' ),
			__( 'Benchmark Email Signup Forms', 'benchmark-email-lite' )
		);
	}
} );

// Plugin Page Settings Link
add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), function( $links ) {
	$settings = [
		'settings' => sprintf(
			'<a href="%s">%s</a>',
			admin_url( 'options-general.php?page=wpbme_page' ),
			__( 'Settings', 'benchmark-email-lite' )
		),
	];
	return array_merge( $settings, $links );
} );

// Sister Product Notices
class wpbme_admin {

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
