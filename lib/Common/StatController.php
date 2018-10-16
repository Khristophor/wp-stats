<?php

namespace WordpressStats\Common;

use WordpressStats\Plugin;
use WordpressStats\Common\Stat;

/**
 * Custom REST routes to make data retrieval easier.
 *
 * Adds two custom routes under /wp-json/cw/v1/, one for counts
 * to return total number of posts, pages and users on the site
 * and one for leaders, to return top category, tag and post by
 * popularity.
 *
 * @package    WordpressStats
 * @subpackage WordpressStats/Common
 * @author     Chris Wiseman <khristophor@github.io>
 */
class StatController extends \WP_REST_Controller {

	/**
	 * The plugin's instance.
	 *
	 * @since  1.0.0
	 * @access private
	 * @var    Plugin $plugin This plugin's instance.
	 */
	private $plugin;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since 1.0.0
	 *
	 * @param Plugin $plugin This plugin's instance.
	 */
	public function __construct( Plugin $plugin ) {
		$this->plugin = $plugin;
	}

	/**
	* Register the routes for the objects of the controller.
	*/
	public function register_routes() {
		$version   = '1';
		$namespace = 'cw/v' . $version;
		\register_rest_route(
			$namespace,
			'/stats',
			array(
				array(
					'methods'  => \WP_REST_Server::READABLE,
					'callback' => array( $this, 'get_stats' ),
					'args'     => array(),
				),
			)
		);
	}

	/**
	* Get a collection of statistics about the current WordPress instance
	*
	* @param  WP_REST_Request $request Full data about the request.
	* @return WP_Error|WP_REST_Response
	*/
	public function get_stats( $request ) {
		try {
			$post_count       = new Stat( array( 'post' ), __( 'Posts', 'wordpress-stats' ), 'count' );
			$page_count       = new Stat( array( 'page' ), __( 'Pages', 'wordpress-stats' ), 'count' );
			$author_count     = new Stat( array( 'author' ), __( 'Authors', 'wordpress-stats' ), 'count' );
			$popular_category = new Stat( array( 'category' ), __( 'Category', 'wordpress-stats' ), 'popular' );
			$popular_tag      = new Stat( array( 'post_tag' ), __( 'Tag', 'wordpress-stats' ), 'popular' );
		} catch ( \Exception $e ) {
			return new WP_Error( 'invalid-stat', sprintf( __( 'Error: %s', 'wordpress-stats' ), $e ) );
		}

		$data = array(
			$post_count->get_statistic(),
			$page_count->get_statistic(),
			$author_count->get_statistic(),
			$popular_category->get_statistic(),
			$popular_tag->get_statistic(),
		);

		return new \WP_REST_Response( $data, 200 );
	}

}
