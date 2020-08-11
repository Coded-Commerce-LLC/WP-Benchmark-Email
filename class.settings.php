<?php

// Exit If Accessed Directly
if( ! defined( 'ABSPATH' ) ) { exit; }

// Setings Class
class wpbme_settings {

	// Renders WP Settings API Forms
	static function page_settings() {
		wpbme_api::tracker( 'Settings' );

		// Security
		if( ! current_user_can( 'manage_options' ) ) {
			wp_die( __( 'You do not have sufficient permissions to access this page.', 'benchmark-email-lite' ) );
		}

		// Apply Updates
		$updated = false;

		// Auth Keys Update
		if( isset( $_POST[ 'wpbme_ap_token' ] ) ) {
			update_option( 'wpbme_ap_token', esc_attr( $_POST[ 'wpbme_ap_token' ] ) );
			$updated = true;
		}
		if( isset( $_POST[ 'wpbme_key' ] ) ) {
			update_option( 'wpbme_key', esc_attr( $_POST[ 'wpbme_key' ] ) );
			$updated = true;
		}
		if( isset( $_POST[ 'wpbme_temp_token' ] ) ) {
			update_option( 'wpbme_temp_token', esc_attr( $_POST[ 'wpbme_temp_token' ] ) );
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

		// Usage Disablement Update
		if( isset( $_POST[ 'wpbme_usage_disable' ] ) && $_POST[ 'wpbme_usage_disable' ] == 'yes' ) {
			update_option( 'wpbme_usage_disable', 'yes' );
			$updated = true;
		} elseif( isset( $_POST[ 'wpbme_key' ] ) ) {
			delete_option( 'wpbme_usage_disable' );
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
		?>

		<div class="wrap">

			<h1><?php _e( 'Benchmark settings', 'benchmark-email-lite' ); ?></h1>
			<br />

			<form name="wbme_settings_form" method="post" action="">
				<h2><?php _e( 'Benchmark connection', 'benchmark-email-lite' ); ?></h2>
				<p>
					<a href="https://ui.benchmarkemail.com/register?p=68907" target="_blank">
						<?php _e( 'Get a FREE Benchmark Email account!', 'benchmark-email-lite' ); ?>
					</a>
				</p>

				<?php

				// Maybe Run Authentication
				$auth_update = null;
				if( isset( $_POST['BME_USERNAME'] ) && isset( $_POST['BME_PASSWORD'] ) ) {
					$response = wpbme_api::authenticate(
						esc_attr( $_POST['BME_USERNAME'] ),
						esc_attr( $_POST['BME_PASSWORD'] )
					);
					if( $response && isset( $response['wpbme_key'] ) ) {
						update_option( 'wpbme_ap_token', $response['wpbme_ap_token'] );
						update_option( 'wpbme_key', $response['wpbme_key'] );
						update_option( 'wpbme_temp_token', $response['wpbme_temp_token'] );
						$updated = true;
						$auth_update = true;
					} else {
						$auth_update = false;
					}
				}
				if( ! get_option( 'wpbme_key' ) && empty( $_GET['wpbme_override'] ) ) {
					?>

					<?php if( $auth_update === false ) { ?>
						<div class="notice notice-error is-dismissible">
							<p><?php _e( 'The credential failed to authenticate.', 'benchmark-email-lite' ); ?></p>
						</div>
					<?php } elseif( $auth_update === true ) { ?>
						<div class="notice notice-success is-dismissible">
							<p><?php _e( 'Your login was successful and access keys have been saved.', 'benchmark-email-lite' ); ?></p>
						</div>
					<?php } ?>

					<p>
						<label style="display: block;">
							<?php _e( 'Benchmark Username', 'benchmark-email-lite' ); ?><br />
							<input type="text" name="BME_USERNAME" />
						</label>
					</p>
					<p>
						<label style="display: block;">
							<?php _e( 'Benchmark Password', 'benchmark-email-lite' ); ?><br />
							<input type="password" name="BME_PASSWORD" />
						</label>
					</p>
					<p class="submit">
						<input type="submit" name="Submit" class="button-primary"
							value="<?php esc_attr_e( 'Connect to Benchmark', 'benchmark-email-lite' ) ?>" />
					</p>
					<p>
						<a href="admin.php?page=wpbme_settings&wpbme_override=1">
							<?php _e( 'Or, click to enter keys manually', 'benchmark-email-lite' ); ?>
						</a>
					</p>

				<?php
				} else {

					// Display Update Made
					if( $updated ) {
						wpbme_api::update_partner();
						?>
						<div class="updated">
							<p><strong><?php _e( 'Settings saved.', 'benchmark-email-lite' ); ?></strong></p>
						</div>
						<?php
					}

					// Load Settings
					$wpbme_ap_token = get_option( 'wpbme_ap_token' );
					$wpbme_debug = get_option( 'wpbme_debug' );
					$wpbme_key = get_option( 'wpbme_key' );
					$wpbme_temp_token = get_option( 'wpbme_temp_token' );
					$wpbme_tracking_disable = get_option( 'wpbme_tracking_disable' );
					$wpbme_usage_disable = get_option( 'wpbme_usage_disable' );
					?>

					<?php if( ! get_option( 'wpbme_key' ) ) { ?>
					<p>
						<a href="admin.php?page=wpbme_settings">
							<?php _e( 'Authenticate with username and password', 'benchmark-email-lite' ); ?>
						</a>
					</p>
					<?php } ?>

					<p>
						<label style="display: block;">
							<?php _e( 'API Key', 'benchmark-email-lite' ); ?><br />
							<input type="text" size="36" id="wpbme_key" name="wpbme_key" value="<?php echo $wpbme_key; ?>" /><br />
							<em><?php _e( 'Authenticates communications with the Benchmark REST API.', 'benchmark-email-lite' ); ?></em>
						</label>
					</p>
					<p>
						<label style="display: block;">
							<?php _e( 'Authentication Token', 'benchmark-email-lite' ); ?><br />
							<input type="text" size="36" id="wpbme_temp_token" name="wpbme_temp_token" value="<?php echo $wpbme_temp_token; ?>" /><br />
							<em><?php _e( 'Authenticates your Benchmark Interface browser session.', 'benchmark-email-lite' ); ?></em>
						</label>
					</p>
					<p>
						<label style="display: block;">
							<?php _e( 'Automation Pro Token', 'benchmark-email-lite' ); ?><br />
							<input type="text" size="36" id="wpbme_ap_token" name="wpbme_ap_token" value="<?php echo $wpbme_ap_token; ?>" /><br />
							<em><?php _e( 'Authenticates front-end visitor tracker used by Automation Pro.', 'benchmark-email-lite' ); ?></em>
						</label>
					</p>
					<br />
					<hr />

					<h3><?php _e( 'Less common settings', 'benchmark-email-lite' ); ?></h3>
					<p>
						<label>
							<?php $wpbme_tracking_disable = $wpbme_tracking_disable == 'yes' ? 'checked="checked"' : ''; ?>
							<input type="checkbox" id="wpbme_tracking_disable" name="wpbme_tracking_disable" value="yes" <?php echo $wpbme_tracking_disable; ?> />
							<?php _e( 'Disable visitor tracking?', 'benchmark-email-lite' ); ?><br />
							<em><?php _e( 'Optionally disable the front-end visitor tracker used by Automation Pro conversion tracking.', 'benchmark-email-lite' ); ?></em>
						</label>
					</p>
					<p>
						<label>
							<?php $wpbme_usage_disable = $wpbme_usage_disable == 'yes' ? 'checked="checked"' : ''; ?>
							<input type="checkbox" id="wpbme_usage_disable" name="wpbme_usage_disable" value="yes" <?php echo $wpbme_usage_disable; ?> />
							<?php _e( 'Disable admin usage tracking?', 'benchmark-email-lite' ); ?><br />
							<em><?php _e( 'Optionally disable aggregate usage statistics for the developer of this plugin.', 'benchmark-email-lite' ); ?></em>
						</label>
					</p>

					<?php if( class_exists( 'WooCommerce' ) ) { ?>
					<p>
						<label>
							<?php $wpbme_debug = $wpbme_debug == 'yes' ? 'checked="checked"' : ''; ?>
							<input type="checkbox" id="wpbme_debug" name="wpbme_debug" value="yes" <?php echo $wpbme_debug; ?> />
							<?php _e( 'Enable debugging?', 'benchmark-email-lite' ); ?><br />
							<em><?php _e( 'Optionally enable logging of all API communications into WooCommerce, as available.', 'benchmark-email-lite' ); ?></em>
							<p>
								<a href="<?php echo admin_url( 'admin.php?page=wc-status&tab=logs' ); ?>">
									<?php _e( 'Logs are stored in WooCommerce', 'benchmark-email-lite' ); ?>
								</a>
							</p>
						</label>
					</p>
					<?php } ?>

					<br />
					<hr />
					<p class="submit">
						<input type="submit" name="Submit" class="button-primary"
							value="<?php esc_attr_e( 'Save Changes', 'benchmark-email-lite' ) ?>" />
					</p>

				<?php } ?>

			</form>

		</div>

		<?php
	}
}
