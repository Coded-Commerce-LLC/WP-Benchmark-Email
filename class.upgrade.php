<?php

// Exit If Accessed Directly
if( ! defined( 'ABSPATH' ) ) { exit; }

// Display Upgrade Notice
add_action( 'in_plugin_update_message-benchmark-email-lite/benchmark-email-lite.php', function( $data, $response ) {
	if( isset( $data['upgrade_notice'] ) ) {
		sprintf( '<div class="update-message">%s</div>', wpautop( $data['upgrade_notice'] ) );
	}
}, 10, 2 );

// WP Initialization Front And Back Ends
add_action( 'init', function() {

	// Check DB Version
	$db_version = get_option( 'wpbme_db_version' );
	if( $db_version == '3.0' ) { return; }

	// Transfer Primary API Key From v2.x
	$new_key = get_option( 'wpbme_key' );
	if( ! $new_key ) {
		$old_key = get_option( 'benchmark-email-lite_group' );
		if( ! empty( $old_key[1][0] ) ) {
			update_option( 'wpbme_key', $old_key[1][0] );
			$new_key = $old_key[1][0];
		}
	}

	// Transfer Primary API Key From Woo Plugin
	if( ! $new_key ) {
		$old_key = get_option( 'bmew_key' );
		if( ! empty( $old_key ) ) {
			update_option( 'wpbme_key', $old_key );
		}
	}

	// Developer Affiliation
	wpbme_api::update_partner();

	// Get Temp Auth Tokens
	wpbme_api::authenticate_ui_renew();

	// Transfer All Widgets From v2.x
	$old_widgets = get_option( 'widget_benchmarkemaillite_widget' );
	$new_widgets = get_option( 'widget_wpbme_widget' );
	if( is_array( $old_widgets ) ) {
		foreach( $old_widgets as $widget_id => $data ) {
			if( ! intval( $widget_id ) ) { continue; }

			// Get Form ID Or List ID
			$listdata = empty( $data['list'] ) ? '' : $data['list'];
			list( $api_key, $list_name, $list_id ) = explode( '|', $listdata );

			// Determine Is Form Or List?
			$form_id = '';
			$forms = wpbme_api::get_forms();
			foreach( $forms as $form ) {
				if( $form->ID == $list_id ) {
					$form_id = $list_id;
				}
			}

			// Maybe Make New Signup Form
			if( ! $form_id ) {
				$button = empty( $data['button'] ) ? '' : $data['button'];
				$description = empty( $data['description'] ) ? '' : $data['description'];
				$fields = [];
				if( is_array( $data['fields'] ) ) {
					foreach( $data['fields'] as $i => $fieldname ) {
						$fields[] = [
							'label' => empty( $data['fields_labels'][$i] )
								? '' : $data['fields_labels'][$i],
							'name' => $fieldname,
							'required' => empty( $data['fields_required'][$i] )
								? '0' : intval( $data['fields_required'][$i] ),
						];
					}
				}
				$form_name = sprintf(
					'%s (%s %s)',
					$list_name,
					__( 'Migrated', 'benchmark-email-lite' ),
					current_time( 'mysql' )
				);
				$form_id = wpbme_api::create_form(
					$list_id, $list_name, $form_name, $description, $button, $fields
				);
			}

			// Add New Widget Version
			$new_widgets[$widget_id] = [
				'post_id' => $form_id,
				'title' => empty( $data['title'] ) ? '' : $data['title'],
			];
			update_option( 'widget_wpbme_widget', $new_widgets );

			// Old Widget Removal -- Disabling For Now!
			//unset( $old_widgets[$widget_id] );
			//update_option( 'widget_benchmarkemaillite_widget', $old_widgets );

			// Update The Active Widgets
			$active_widgets = get_option( 'sidebars_widgets' );
			foreach( $active_widgets as $sidebar => $widgets ) {
				if( ! is_array( $widgets ) ) { continue; }
				foreach( $widgets as $i => $widget ) {
					if( $widget == sprintf( 'benchmarkemaillite_widget-%d', $widget_id ) ) {

						// Using `$active_widgets[$sidebar][$i]` Would Replace Instead Of Add New!
						$active_widgets[$sidebar][] = sprintf( 'wpbme_widget-%d', $widget_id );
					}
				}
			}
			update_option( 'sidebars_widgets', $active_widgets );

			// Transfer All Shortcodes From v2.x
			update_option( 'wpbme_legacy_widget_' . $widget_id, $form_id );
		}
	}

	// Save DB Version
	update_option( 'wpbme_db_version', '3.0' );

} );
