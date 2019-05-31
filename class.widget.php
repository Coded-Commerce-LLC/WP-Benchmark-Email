<?php

// Exit If Accessed Directly
if( ! defined( 'ABSPATH' ) ) { exit; }

add_action( 'widgets_init', function() {
	register_widget( 'wpbme_widget' );
} );

// WP Widget Class
class wpbme_widget extends WP_Widget {

	public function __construct() {
		$widget_ops = array( 
			'classname' => 'wpbme_widget',
			'description' => 'Benchmark Signup Form',
		);
		parent::__construct( 'wpbme_widget', 'Benchmark Signup Form', $widget_ops );
	}

	public function widget( $args, $instance ) {
		$id = $instance['post_id'];
		$formdata = get_transient( 'wpbme_js_' . $instance['post_id'] );
		if( $formdata ) {
			echo $formdata->JSCode;
			return;
		}
		$form = wpbme_api::get_form_data( $id );
		set_transient( 'wpbme_js_' . $id, $form, 86400 );
		echo $form->JSCode;
	}

	public function form( $instance ) {

		// Query Existing Forms
		$forms = wpbme_api::get_forms();
		$options = '';
		foreach( $forms as $form ) {
			$selected = ( ! empty( $instance['post_id'] ) && $form->ID == $instance['post_id'] )
				? 'selected="selected"' : '';
			$options .= sprintf(
				'<option value="%s"%s>%s</option>',
				$form->ID,
				$selected,
				$form->Name
			);
		}

		// Handle No Existing Forms
		if( ! $options ) {
			echo sprintf(
				'<p>%s</p>',
				__( 'Please design a signup form first!', 'benchmark-email-lite' )
			);
		}

		// Assemble Dropdown
		$dropdown = sprintf(
			'<select id="%s" name="%s"><option value="">- %s -</option>%s</select>',
			esc_attr( $this->get_field_id( 'post_id' ) ),
			esc_attr( $this->get_field_name( 'post_id' ) ),
			__( 'Please Select', 'benchmark-email-lite' ),
			$options
		);
		echo sprintf(
			'<p><label for="%s">%s</label></p>',
			esc_attr( $this->get_field_id( 'post_id' ) ),
			$dropdown
		);

		// Manage Forms Button
		echo sprintf(
			'<p><a href="%s" class="button">%s</a></p>',
			admin_url( 'admin.php?page=wpbme_interface&tab=Listbuilder' ),
			__( 'Manage Signup Forms', 'benchmark-email-lite' )
		);
	}

	public function update( $new_instance, $old_instance ) {
		return $new_instance;
	}
}
