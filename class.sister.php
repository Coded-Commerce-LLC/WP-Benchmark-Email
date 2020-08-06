<?php

// Exit If Accessed Directly
if( ! defined( 'ABSPATH' ) ) { exit; }

// Admin Dashboard Items
add_action( 'admin_notices', function() {

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
				__( 'Activate our sister product Woo Benchmark Email to enable eCommerce tracking.', 'benchmark-email-lite' ),
				wpbme_sister::get_sister_activate_link(),
				__( 'Activate Now', 'benchmark-email-lite' ),
				wpbme_sister::get_sister_dismiss_link(),
				__( 'dismiss for 90 days', 'benchmark-email-lite' )
			);

		// Plugin Not Installed
		} else {
			$messages[] = sprintf(
				'
					%s &nbsp; <strong style="font-size:1.2em;"><a href="%s">%s</a></strong>
					<a style="float:right;" href="%s">%s</a>
				',
				__( 'Install our sister product Woo Benchmark Email to enable eCommerce tracking.', 'benchmark-email-lite' ),
				wpbme_sister::get_sister_install_link(),
				__( 'Install Now', 'benchmark-email-lite' ),
				wpbme_sister::get_sister_dismiss_link(),
				__( 'dismiss for 90 days', 'benchmark-email-lite' )
			);
		}
	}

	// Message If Plugin Isn't Configured
	if( empty( get_option( 'wpbme_key' ) ) ) {
		$messages[] = sprintf(
			'%s &nbsp; <strong style="font-size:1.2em;"><a href="admin.php?page=wpbme_settings">%s</a></strong>',
			__( 'Please connect to Benchmark to use the Benchmark Email Lite plugin.', 'benchmark-email-lite' ),
			__( 'Connect Now', 'benchmark-email-lite' )
		);
	}

	// Output Message
	if( $messages ) {
		foreach( $messages as $message ) {
			printf(
				'<div class="notice notice-info is-dismissible"><p>%s</p></div>',
				print_r( $message, true )
			);
		}
	}
} );

// Sister Plugin Class
class wpbme_sister {

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
