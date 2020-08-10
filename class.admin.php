<?php

// Exit If Accessed Directly
if( ! defined( 'ABSPATH' ) ) { exit; }

// Plugins Page Link To Settings
add_filter(
	'plugin_action_links_benchmark-email-lite/benchmark-email-lite.php',
	function( $links ) {
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

	// Check Authentications
	$wpbme_key = get_option( 'wpbme_key' );
	$wpbme_temp_token = get_option( 'wpbme_temp_token' );

	// Menus When Not Connected
	if( ! $wpbme_key || ! $wpbme_temp_token ) {
		add_menu_page(
			'Benchmark', 'Benchmark', 'manage_options', 'wpbme_settings',
			[ 'wpbme_settings', 'page_settings' ], 'dashicons-email'
		);
	}

	// Menus When Connected
	else {
		add_menu_page(
			'Benchmark', 'Benchmark', 'manage_options', 'wpbme_interface',
			[ 'wpbme_admin', 'page_interface' ], 'dashicons-email'
		);
		add_submenu_page(
			'wpbme_interface', 'Interface', 'Interface', 'manage_options',
			'wpbme_interface', [ 'wpbme_admin', 'page_interface' ]
		);
		add_submenu_page(
			'wpbme_interface', 'Signup Form Widgets', 'Signup Form Widgets',
			'manage_options', 'widgets.php'
		);
		add_submenu_page(
			'wpbme_interface', 'Shortcodes', 'Shortcodes', 'manage_options',
			'wpbme_shortcodes', [ 'wpbme_admin', 'page_shortcodes' ]
		);
		add_submenu_page(
			'wpbme_interface', 'Settings', 'Settings', 'manage_options',
			'wpbme_settings', [ 'wpbme_settings', 'page_settings' ]
		);
	}

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

			// Successful Email Creation
			if( intval( $newemail ) > 1 ) {
				$tab = '/Emails/Details?e=' . $newemail;

			// Failed Email Creation
			} else {

				// Failed Due To From Address
				if( stristr( $newemail, 'Email Invalid' ) !== false ) {
					$tab = '/ConfirmedEmails';
					printf(
						'<div class="notice notice-error"><p>%s <strong>%s</strong></p></div>',
						__(
							'Please verify the email address you are signed into WordPress with'
							. ' using the interface below, then re-attempt creating your email.',
							'benchmark-email-lite'
						),
						$current_user->user_email
					);

				// Failed Due To Missing List
				} else if( stristr( $newemail, 'No Contact Lists' ) !== false ) {
					$tab = '/Contacts';
					printf(
						'<div class="notice notice-error"><p>%s <strong>%s</strong></p></div>',
						__( 'Missing contact list', 'benchmark-email-lite' ),
						__( 'Sample Contact List', 'benchmark-email-lite' )
					);

				// Other Error
				} else {
					$tab = '/Emails/Dashboard';
					printf(
						'<div class="notice notice-error"><p>%s</p></div>',
						__(
							'Error creating email campaign. Please contact support.',
							'benchmark-email-lite'
						)
					);
				}
			}
		}

		// Developer Analytics
		$tracker = ucwords( sanitize_title( ltrim( preg_replace( '/\?.*/', '', $tab ), '/' ) ) );
		wpbme_api::tracker( 'UI-' . $tracker );

		// Get Redirection URL
		$redirect_url = wpbme_api::authenticate_ui_redirect( $tab );

		// Maybe Get Pre Auth Redirect
		if( strchr( $tab, '?' ) ) {
			$redirect_url_auth = wpbme_api::authenticate_ui_redirect( '/Emails/Dashboard' );
			$redirect_script = sprintf(
				'
					bmeui_popup = window.open(
						\'%s\', \'bmeui\', \'width=1024,height=768,top=\' + top + \',left=\' + left
					);
					setTimeout( function() {
						bmeui_popup = window.open(
							\'%s\', \'bmeui\', \'width=1024,height=768,top=\' + top + \',left=\' + left
						);
					}, 3000 );
				',
				$redirect_url_auth, $redirect_url
			);
		} else {
			$redirect_script = sprintf(
				'
					bmeui_popup = window.open(
						\'%s\', \'bmeui\', \'width=1024,height=768,top=\' + top + \',left=\' + left
					);
				',
				$redirect_url
			);
		}

		// Output Body
		printf(
			'
				<div class="wrap">
					<h1>%s</h1>
					<br />
					<p><a class="button-primary" id="bmeui" href="#">%s &rarr;</a></p>
					<p>%s</p>
				</div>
				<script>
					var bmeui_popup = false;
					jQuery( document ).ready( function( $ ) {
						$( "#bmeui" ).click( function() {
							var left = ( window.screen.width / 2 ) - ( ( 1024 / 2 ) + 10 );
							var top = ( window.screen.height / 2 ) - ( ( 768 / 2 ) + 50 );
							%s
						} );
					} );
				</script>
			',
			__( 'Benchmark Email Interface', 'benchmark-email-lite' ),
			__( 'Open Benchmark Interface', 'benchmark-email-lite' ),
			__(
				'Please click the button to open the requested Benchmark interface in a secure pop-up window.',
				'benchmark-email-lite'
			),
			$redirect_script
		);
	}

	// Displays Shortcodes
	static function page_shortcodes() {
		wpbme_api::tracker( 'Shortcodes' );
		$forms = wpbme_api::get_forms();

		// Handle No Forms
		if( ! $forms ) {
			printf(
				'<p>%s</p>',
				__( 'Please design a signup form first!', 'benchmark-email-lite' )
			);
			return;
		}

		// Has Forms
		printf(
			'
				<br /><h1>%s</h1>
				<p>%s</p>
			',
			__( 'Shortcodes for Pages and Posts', 'benchmark-email-lite' ),
			__( 'Use these to place a signup form on specific pages or posts.', 'benchmark-email-lite' )
		);

		// Loop Forms
		foreach( $forms as $form ) {
			if( empty( $form->Name ) || empty( $form->ID ) ) { continue; }
			printf(
				'
					<p style="margin: 2em 0;">
						<h2>%s</h2>
						<code>[benchmark-email-lite form_id="%d"]</code>
					</p>
					<hr />
				',
				$form->Name,
				$form->ID
			);
		}

		// Manage Forms Button
		printf(
			'
				<p style="margin: 2em 0;">
					<a href="%s">%s</a><br /><br />
					<a href="%s">%s</a><br /><br />
					<a href="%s" class="button-primary">%s</a>
				</p>
			',
			admin_url( 'admin.php?page=wpbme_interface&tab=Signupform/FullEmbed/Details' ),
			__( 'Create an Embedded Form', 'benchmark-email-lite' ),
			admin_url( 'admin.php?page=wpbme_interface&tab=Signupform/Popup/Details' ),
			__( 'Create a Popup Form', 'benchmark-email-lite' ),
			admin_url( 'admin.php?page=wpbme_interface&tab=Listbuilder' ),
			__( 'Manage All Signup Forms', 'benchmark-email-lite' )
		);
	}
}
