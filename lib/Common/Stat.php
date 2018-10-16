<?php

namespace WordpressStats\Common;

/**
 * Class to represent a single statistic.
 *
 * This object has a limited set of inputs and returns a standardized output.
 * Designed to be used as part of a REST API response to drive the Widget display.
 *
 * @package    WordpressStats
 * @subpackage WordpressStats/Common
 * @author     Chris Wiseman <khristophor@github.io>
 */
class Stat {

	/**
	 * The type of WordPress content to use for the statistic
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      array $content_type Array to identify the WordPress content type
	 */
	protected $content_type;

	/**
	 * The label to display for the statistic, i.e. Posts.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string $label String to identify the statistic
	 */
	protected $label;

	/**
	 * An optional tagline to display below the statistic, i.e. 52 posts tagged
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string $tagline String for optional statistic information
	 */
	protected $tagline;

	/**
	 * The type of statistic being displayed, either "Counts" or "Most Popular"
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string $type String to identify the type of statistic
	 */
	protected $type;

	/**
	 * The optional url to link to the WordPress object being referenced
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string $url String for optionally linking to a WordPress object, like a Category
	 */
	protected $url;

	/**
	 * The value to display for the statistic, i.e. a count or a category name.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string|int $value String or Integer value of the statistic
	 */
	protected $value;

	/**
	 * Create an instance of the statistic which will be used to display within the custom
	 * widget.
	 *
	 * @since 1.0.0
	 * @param array    $content_type   type of WordPress content
	 * @param string   $label          identify the statistic
	 * @param string   $type           type of statistic
	 */
	public function __construct( array $content_type, string $label, string $type ) {

		$valid_types = array( 'count', 'popular' );
		if ( false === in_array( $type, $valid_types, true ) ) {
			throw new \Exception( 'Type must be one of: ' . join( ', ', $valid_types ) );
		}

		$valid_content_types = array( 'post', 'page', 'author', 'category', 'post_tag' );
		if ( false === empty( array_diff( $content_type, $valid_content_types ) ) ) {
			throw new \Exception( 'Content type must be one or more of: ' . join( ', ', $valid_content_types ) );
		}

		$this->content_type = $content_type;
		$this->label        = $label;
		$this->type         = $type;

	}

	/**
	 * Construct a WordPress args array to be used by \WP_Query related functions
	 *
	 * @since  1.0.0
	 * @return array   $args   WordPress query arguments
	 */
	protected function build_args() {

		if ( 'count' === $this->type ) {
			return array(
				'no_found_rows'          => true,
				'update_post_meta_cache' => false,
				'update_post_term_cache' => false,
				'fields'                 => 'ids',
				'posts_per_page'         => 1000,
				'post_type'              => ( 1 === count( $this->content_type ) ? $this->content_type[0] : $this->content_type ),
				'post_status'            => 'publish',
			);
		} elseif ( 'popular' === $this->type ) {
			return array(
				'taxonomy' => ( 1 === count( $this->content_type ) ? $this->content_type[0] : $this->content_type ),
				'order'    => 'DESC',
				'orderby'  => 'count',
				'number'   => 1,
			);
		}

	}

	/**
	 * Set the internal $value property from query results
	 *
	 * Runs various WordPress queries based on $content_type and $type
	 * properties set in the __construct method.
	 *
	 * @since  1.0.0
	 * @return void
	 */
	protected function set_value() {

		if ( array( 'author' ) === $this->content_type ) {
			$users       = \count_users();
			$this->value = $users['avail_roles']['author'];
		} else {
			$args = $this->build_args();

			if ( 'count' === $this->type ) {
				$query       = new \WP_Query( $args );
				$this->value = $query->post_count;
			} elseif ( 'popular' === $this->type ) {
				$term = \get_terms( $args );
				if ( false === \is_wp_error( $term ) && is_array( $term ) ) {
					// Set additional properties unique to popular statistic
					$this->tagline = $term[0]->count . __( ' posts tagged.', 'wordpress-stats' );
					$this->url     = \get_term_link( $term[0] );
					$this->value   = $term[0]->name;
				}
			}
		}

	}

	/**
	 * Construct the array to be returned for the given statistic instance
	 *
	 * Returns an empty array if the $value property is not set.
	 *
	 * @since  1.0.0
	 * @return array   $stat   populated array to be used as a json response
	 */
	public function get_statistic() {

		$this->set_value();
		if ( false === isset( $this->value ) ) {
			return array();
		}

		return array(
			'label'   => $this->label,
			'tagline' => ( isset( $this->tagline ) ? $this->tagline : '' ),
			'type'    => $this->type,
			'url'     => ( isset( $this->url ) ? $this->url : '' ),
			'value'   => $this->value,
		);

	}

}
