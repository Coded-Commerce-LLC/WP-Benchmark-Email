<?php

// Exit If Accessed Directly
if( ! defined( 'ABSPATH' ) ) { exit; }


// Admin Settings Link
add_action( 'admin_menu', function() {
	add_options_page(
		'Benchmark Email Lite',
		'Benchmark Email',
		'manage_options',
		'wpbme_page',
		[ 'wpbme_settings', 'wpbme_page' ]
	);
} );


// Setings Class
class wpbme_settings {

	// Renders WP Settings API Forms
	static function wpbme_page() {

		// Security
		if( ! current_user_can( 'manage_options' ) ) {
			wp_die( __( 'You do not have sufficient permissions to access this page.', 'benchmark-email-lite' ) );
		}

		// Apply Updates
		$updated = false;

		// Key Update
		if( isset( $_POST[ 'wpbme_key' ] ) ) {
			$wpbme_key = sanitize_text_field( $_POST[ 'wpbme_key' ] );
			update_option( 'wpbme_key', $wpbme_key );
			$updated = true;
		}

		// Tracker Disablement Update
		if( isset( $_POST[ 'wpbme_tracking_disable' ] ) && $_POST[ 'wpbme_tracking_disable' ] == 'yes' ) {
			update_option( 'wpbme_tracking_disable', 'yes' );
			$updated = true;
		} elseif( isset( $_POST[ 'wpbme_key' ] ) ) {
			delete_option( 'wpbme_tracking_disable' );
			$updated = true;
		}

		// Debug Update
		if( isset( $_POST[ 'wpbme_debug' ] ) && $_POST[ 'wpbme_debug' ] == 'yes' ) {
			update_option( 'wpbme_debug', 'yes' );
			$updated = true;
		} elseif( isset( $_POST[ 'wpbme_key' ] ) ) {
			delete_option( 'wpbme_debug' );
			$updated = true;
		}

		// Update Made
		if( $updated ) {
			wpbme_api::update_partner();
			?>
			<div class="updated">
				<p><strong><?php _e( 'Settings saved.', 'benchmark-email-lite' ); ?></strong></p>
			</div>
			<?php
		}

		// Get Settings
		$wpbme_debug = get_option( 'wpbme_debug' );
		$wpbme_key = get_option( 'wpbme_key' );
		$wpbme_tracking_disable = get_option( 'wpbme_tracking_disable' );

		// Show Form
		?>
		<div class="wrap">
			<h2><?php _e( 'Benchmark Email Lite Settings', 'benchmark-email-lite' ); ?></h2>
			<form name="wbme_settings_form" method="post" action="">
				<p>
					<label>
						<?php _e( 'API Key', 'benchmark-email-lite' ); ?><br />
						<input type="text" size="36" id="wpbme_key" name="wpbme_key" value="<?php echo $wpbme_key; ?>" />
						<a id="get_api_key" class="button" href="#">
							<?php _e( 'Get API Key', 'benchmark-email-lite' ); ?>
						</a>
					</label>
				</p>
				<p>
					<label>
						<?php $wpbme_tracking_disable = $wpbme_tracking_disable == 'yes' ? 'checked="checked"' : ''; ?>
						<input type="checkbox" id="wpbme_tracking_disable" name="wpbme_tracking_disable" value="yes" <?php echo $wpbme_tracking_disable; ?> />
						<?php _e( 'Disable visitor tracking?', 'benchmark-email-lite' ); ?><br />
					</label>
				</p>
				<p>
					<label>
						<?php $wpbme_debug = $wpbme_debug == 'yes' ? 'checked="checked"' : ''; ?>
						<input type="checkbox" id="wpbme_debug" name="wpbme_debug" value="yes" <?php echo $wpbme_debug; ?> />
						<?php _e( 'Enable debugging?', 'benchmark-email-lite' ); ?><br />
					</label>
				</p>
				<p class="submit">
					<input type="submit" name="Submit" class="button-primary"
						value="<?php esc_attr_e( 'Save Changes', 'benchmark-email-lite' ) ?>" />
				</p>
			</form>
		</div>
		<?php
	}
}
