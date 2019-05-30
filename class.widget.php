<?php

add_action( 'widgets_init', function() {
	register_widget( 'wpbme_widget' );
} );


// WP Widget Class
class wpbme_widget extends WP_Widget {

	public function __construct() {
		$widget_ops = array( 
			'classname' => 'wpbme_widget',
			'description' => 'Benchmark Email Lite Signup Form',
		);
		parent::__construct( 'wpbme_widget', 'Benchmark Email Lite Signup Form', $widget_ops );
	}

	public function widget( $args, $instance ) {
		if( empty( $instance['post_id'] ) ) { return; }
		$content_post = get_post( $instance['post_id'] );
		echo apply_filters( 'the_content', $content_post->post_content );
	}

	public function form( $instance ) {
		$dropdown = wp_dropdown_pages( [
			'echo' => false,
			'id' => esc_attr( $this->get_field_id( 'post_id' ) ),
			'name' => esc_attr( $this->get_field_name( 'post_id' ) ),
			'post_type' => 'benchmark',
			'selected' => empty( $instance['post_id'] ) ? '' : $instance['post_id'],
			'show_option_none' => __( 'Please select', 'benchmark-email-lite' ),
		] );
		if( ! $dropdown ) {
			echo sprintf(
				'<p>%s <a href="%s">%s</a></p>',
				__( 'Please design a signup form first!', 'benchmark-email-lite' ),
				admin_url( 'admin.php?page=wpbme_interface&tab=Listbuilder' ),
				__( 'Click here to design a signup form.', 'benchmark-email-lite' )
			);
		}
		echo sprintf(
			'<p><label for="%s">%s</label></p>',
			esc_attr( $this->get_field_id( 'post_id' ) ),
			$dropdown
		);
	}

	public function update( $new_instance, $old_instance ) {
		return $new_instance;
	}
}
