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
		parent::__construct( 'cw_wordpress_stats', 'WordPress Stats', $widget_options );

	}

	/**
	 * Call WordPress register_widget() function.
	 *
	 * @since 1.0.0
	 */
	public function register() {

		\register_widget( $this );

	}

}
