<?php

// Exit If Accessed Directly
if( ! defined( 'ABSPATH' ) ) { exit; }

class wpbme_settings {

	// Admin Menu
	static function admin_menu() {
		add_options_page(
			'WP Benchmark Email',
			'Benchmark Email',
			'manage_options',
			'wpbme_page',
			[ 'wpbme_settings', 'wpbme_page' ]
		);
	}

	// Renders WP Settings API Forms
	static function wpbme_page() {

		// Security
		if( ! current_user_can( 'manage_options' ) ) {
			wp_die( __( 'You do not have sufficient permissions to access this page.', 'wp-benchmark-email' ) );
		}

		// Apply Updates
		$updated = false;
		if( isset( $_POST[ 'wpbme_key' ] ) ) {
			$wpbme_key = sanitize_text_field( $_POST[ 'wpbme_key' ] );
			update_option( 'wpbme_key', $wpbme_key );
			$updated = true;
		}
		if( isset( $_POST[ 'wpbme_debug' ] ) ) {
			$wpbme_debug = sanitize_text_field( $_POST[ 'wpbme_debug' ] );
			update_option( 'wpbme_debug', $wpbme_debug );
			$updated = true;
		}
		if( $updated ) {
			?>
			<div class="updated">
				<p><strong><?php _e( 'Settings saved.', 'wp-benchmark-email' ); ?></strong></p>
			</div>
			<?php
		}

		// Get Settings
		$wpbme_key = get_option( 'wpbme_key' );
		$wpbme_debug = get_option( 'wpbme_debug' );

		// Show Form
		?>
		<div class="wrap">
			<h2><?php _e( 'WP Benchmark Email Settings', 'wp-benchmark-email' ); ?></h2>
			<form name="wbme_settings_form" method="post" action="">
				<p>
					<label>
						<?php _e( 'API Key', 'wp-benchmark-email' ); ?><br />
						<input type="text" id="wpbme_key" name="wpbme_key" value="<?php echo $wpbme_key; ?>" />
						<a id="get_api_key" class="button" href="#">
							<?php _e( 'Get API Key', 'wp-benchmark-email' ); ?>
						</a>
					</label>
				</p>
				<p>
					<label>
						<?php _e( 'Enable debugging?', 'wp-benchmark-email' ); ?><br />
						<?php $wpbme_debug = $wpbme_debug == 'yes' ? 'checked="checked"' : ''; ?>
						<input type="checkbox" id="wpbme_debug" name="wpbme_debug" value="yes" <?php echo $wpbme_debug; ?> />
					</label>
				</p>
				<p class="submit">
					<input type="submit" name="Submit" class="button-primary"
						value="<?php esc_attr_e( 'Save Changes', 'wp-benchmark-email' ) ?>" />
				</p>
			</form>
		</div>
		<?php
	}
}
