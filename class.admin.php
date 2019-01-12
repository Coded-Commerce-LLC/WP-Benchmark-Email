<?php

// Exit If Accessed Directly
if( ! defined( 'ABSPATH' ) ) { exit; }

// Administrative / Settings Class
class wpbme_admin {


	/**********************
		Admin Messaging
	**********************/

	// Plugin Action Links
	static function plugin_action_links( $links ) {
		$settings = [
			'settings' => sprintf(
				'<a href="%s">%s</a>',
				admin_url( 'options-general.php?page=wpbme_page' ),
				__( 'Settings', 'wp-benchmark-email' )
			),
		];
		return array_merge( $settings, $links );
	}

	// Admin Dashboard Notifications
	static function wp_dashboard_setup() {

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
					__( 'Activate our sister product Woo Benchmark Email to view campaign statistics.', 'wp-benchmark-email' ),
					wpbme_admin::get_sister_activate_link(),
					__( 'Activate Now', 'wp-benchmark-email' ),
					wpbme_admin::get_sister_dismiss_link(),
					__( 'dismiss for 90 days', 'wp-benchmark-email' )
				);

			// Plugin Not Installed
			} else {
				$messages[] = sprintf(
					'
						%s &nbsp; <strong style="font-size:1.2em;"><a href="%s">%s</a></strong>
						<a style="float:right;" href="%s">%s</a>
					',
					__( 'Install our sister product Woo Benchmark Email to view campaign statistics.', 'wp-benchmark-email' ),
					wpbme_admin::get_sister_install_link(),
					__( 'Install Now', 'wp-benchmark-email' ),
					wpbme_admin::get_sister_dismiss_link(),
					__( 'dismiss for 90 days', 'wp-benchmark-email' )
				);
			}
		}

		// Message If Plugin Isn't Configured
		if( empty( get_option( 'wpbme_key' ) ) ) {
			$messages[] = sprintf(
				'%s &nbsp; <strong style="font-size:1.2em;"><a href="admin.php?page=wc-settings&tab=wpbme">%s</a></strong>',
				__( 'Please configure your API Key to use WP Benchmark Email.', 'wp-benchmark-email' ),
				__( 'Configure Now', 'wp-benchmark-email' )
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
	}

	// Sister Install Link
	static function get_sister_install_link() {
		$action = 'install-plugin';
		$slug = 'woo-benchmark-email';
		return wp_nonce_url(
			add_query_arg(
				array( 'action' => $action, 'plugin' => $slug ),
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
				array( 'action' => $action, 'plugin' => $plugin, 'plugin_status' => 'all', 'paged' => '1&s' ),
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

	// AJAX Load Script
	static function admin_enqueue_scripts() {
		wp_enqueue_script( 'wpbme_admin', plugin_dir_url( __FILE__ ) . 'admin.js', [ 'jquery' ], null );
	}

}
