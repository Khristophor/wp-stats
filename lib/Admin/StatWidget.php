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
		echo $args['before_widget'] . $args['before_title'] . esc_attr( $title ) . $args['after_title'];
		$stats = $this->collect_stats(); ?>
		<h5>Counts</h5>
		<p><strong>Posts:</strong> <?php echo esc_attr( $stats['post_count'] ); ?></p>
		<p><strong>Pages:</strong> <?php echo esc_attr( $stats['page_count'] ); ?></p>
		<p><strong>Authors:</strong> <?php echo esc_attr( $stats['author_count'] ); ?></p>
		<h5>Most Popular</h5>
		<p><strong>Category:</strong> <?php echo esc_attr( current( array_keys( $stats['category'] ) ) ); ?></p>
		<p class="aside"><?php echo esc_attr( current( array_values( $stats['category'] ) ) ); ?> posts tagged.</p>
		<p><strong>Tag:</strong> <?php echo esc_attr( current( array_keys( $stats['tag'] ) ) ); ?></p>
		<p class="aside"><?php echo esc_attr( current( array_values( $stats['tag'] ) ) ); ?> posts tagged.</p>
		<p><strong>Post:</strong> <a href="<?php echo get_permalink( $stats['commented']['post_id'] ); ?>"><?php echo esc_attr( $stats['commented']['post_title'] ); ?></a></p>
		<p class="aside"><?php echo esc_attr( $stats['commented']['comment_count'] ); ?> comments.</p>
		<?php echo $args['after_widget'];
	}

	/**
	 * Call custom REST endpoints registered to Common\StatController and collect
	 * statistics.
	 *
	 * @since 1.0.0
	 */
	protected function collect_stats() {

		$counts_endpoint = \get_home_url() . '/wp-json/cw/v1/counts';
		$leaders_endpoint = \get_home_url() . '/wp-json/cw/v1/leaders';

		$stats = array();

		$response = wp_remote_get( $counts_endpoint );
		if ( is_array( $response ) ) {
			$stats = array_merge( $stats, json_decode( $response['body'], true ) );
		}

		$response = wp_remote_get( $leaders_endpoint );
		if ( is_array( $response ) ) {
			$stats = array_merge( $stats, json_decode( $response['body'], true ) );
		}

		return $stats;

	}

}
