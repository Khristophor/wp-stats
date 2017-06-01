<?php

namespace Khristophor\Wordpress_Stats\Admin;

/**
 * Custom widget to make use of Common\StatController endpoints
 *
 * Handles instatiation, registration and display of custom widget.
 * Uses counts/ and leaders/ custom REST endpoints to provide data
 * to populate the widget.
 *
 * @package    WordpressStats
 * @subpackage WordpressStats/Admin
 * @author     Chris Wiseman <khristophor@github.io>
 */
class StatWidget extends \WP_Widget {

	/**
	 * Initialize the class, set its properties, initialize parent.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		$widget_options = array(
			'classname' => 'cw_wordpress_stats',
			'description' => __( 'This widget displays statistics about the WordPress site.', 'wordpress-stats' ),
		);
		parent::__construct( 'cw_wordpress_stats', __( 'WordPress Stats', 'wordpress-stats' ), $widget_options );

	}

	/**
	 * Call WordPress register_widget() function.
	 *
	 * @since 1.0.0
	 */
	public function register() {

		\register_widget( $this );

	}

	/**
	 * Add form to Widgets menu for basic settings.
	 *
	 * @since 1.0.0
	 *
	 * @param array $instance values associated with the current instance of the widget
	 */
	public function form( $instance ) {
		$title = ! empty( $instance['title'] ) ? $instance['title'] : ''; ?>
		<p>
		<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>">Title:</label>
		<input type="text" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" value="<?php echo esc_attr( $title ); ?>" />
		</p><?php
	}

	/**
	 * Build widget output.
	 *
	 * @since 1.0.0
	 *
	 * @param array $args set by the active theme when the sidebar region is registered
	 * @param array $instance values associated with the current instance of the widget
	 */
	public function widget( $args, $instance ) {
		$title = \apply_filters( 'widget_title', $instance['title'] );
		$blog_title = \get_bloginfo( 'name' );
		$tagline = \get_bloginfo( 'description' );
		echo esc_attr( $args['before_widget'] ) . esc_attr( $args['before_title'] ) . esc_attr( $title ) . esc_attr( $args['after_title'] ); ?>
		<p><strong>Site Name:</strong> <?php echo esc_attr( $blog_title ); ?></p>
		<p><strong>Tagline:</strong> <?php echo esc_attr( $tagline ); ?></p>
		<?php echo esc_attr( $args['after_widget'] );
	}

}
