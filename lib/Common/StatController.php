<?php

namespace Khristophor\Wordpress_Stats\Common;

use Khristophor\Wordpress_Stats\Plugin;

class StatController extends \WP_REST_Controller {

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
		$version = '1';
		$namespace = 'cw/v' . $version;
		\register_rest_route(
			$namespace, '/counts', array(
				array(
					'methods'  => \WP_REST_Server::READABLE,
					'callback' => array( $this, 'get_counts' ),
					'args'     => array(),
				),
			)
		);
		\register_rest_route(
			$namespace, '/leaders', array(
				array(
					'methods'  => \WP_REST_Server::READABLE,
					'callback' => array( $this, 'get_leaders' ),
					'args'     => array(),
				),
			)
		);
	}

	/**
	* Get the counts for various objects within the current site
	*
	* @param  WP_REST_Request $request Full data about the request.
	* @return WP_Error|WP_REST_Response
	*/
	public function get_counts( $request ) {
		$data = array();

		$args = array(
			'no_found_rows' => true,
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,
			'fields' => 'ids',
			'posts_per_page' => 1000,
			'post_type' => 'post',
			'post_status' => 'publish',
		);
		$query = new \WP_Query( $args );
		$data['post_count'] = $query->post_count;

		$args['post_type'] = 'page';
		$query = new \WP_Query( $args );
		$data['page_count'] = $query->post_count;

		$users = count_users();
		$data['author_count'] = $users['avail_roles']['author'];

		return new \WP_REST_Response( $data, 200 );
	}

	/**
	* Get most popular items based on varying quantifiers
	*
	* Returns the most popular post by comment count, which category
	* and tag are attached to the largest number of posts, and which
	* author is most active based on edits/creates (pages and posts).
	*
	* @param  WP_REST_Request $request Full data about the request.
	* @return WP_Error|WP_REST_Response
	*/
	public function get_leaders() {
		$data = array();

		$args = array(
			'no_found_rows' => true,
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,
			'fields' => 'ids',
			'posts_per_page' => 1000,
			'post_type' => array( 'post', 'page' ),
			'post_status' => 'publish',
		);

		$category_names = array();
		$tag_names = array();

		$query = new \WP_Query( $args );
		if ( $query->have_posts() ) {
			foreach ( $query->posts as $post_id ) {
				$categories = wp_get_post_categories( $post_id, array(
					'fields' => 'names',
				) );
				if ( false === empty( $categories ) ) {
					$category_names = array_merge( $category_names, $categories );
				}

				$tags = wp_get_post_terms( $post_id, 'post_tag', array(
					'fields' => 'names',
				) );
				if ( false === empty( $tags ) ) {
					$tag_names = array_merge( $tag_names, $tags );
				}
			}

			$category_count = array_count_values( $category_names );
			arsort( $category_count );
			$data['category'] = $category_count;

			$tag_count = array_count_values( $tag_names );
			arsort( $tag_count );
			$data['tag'] = $tag_count;
		}

		unset( $args['fields'] );
		$args['posts_per_page'] = 1;
		$args['orderby'] = 'comment_count';

		$query = new \WP_Query( $args );
		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) {
				global $post;
				$query->the_post();
				$data['commented'] = array(
					'post_id' => $post->ID,
					'post_title' => $post->post_title,
					'comment_count' => $post->comment_count,
				);

			}
			wp_reset_postdata();
		}

		return new \WP_REST_Response( $data, 200 );
	}
}
