<?php
/**
 * Class StatTest
 *
 * @package    WordpressStats
 * @subpackage WordpressStats/Common
 * @author     Chris Wiseman <khristophor@github.io>
 */

use WordpressStats\Common\Stat;

/**
 * Unit tests for Stat class methods.
 */
class StatTest extends WP_UnitTestCase {

	/**
	 * Error path(s)
	 */

	/**
	 * @covers WordpressStats\Common\Stat::__construct()
	 */
	function test_should_throw_exception_with_invalid_type() {

		$this->expectException( \Exception::class );
		new Stat( array( 'post' ), 'Post', 'type' );

	}

	/**
	 * @covers WordpressStats\Common\Stat::__construct()
	 */
	function test_should_throw_exception_with_invalid_content_type() {

		$this->expectException( \Exception::class );
		new Stat( array( 'content' ), 'Post', 'count' );

	}

	/**
	 * Happy path(s)
	 */

	/**
	 * @covers WordpressStats\Common\Stat::build_args()
	 * @covers WordpressStats\Common\Stat::get_statistic()
	 * @covers WordpressStats\Common\Stat::set_value()
	 */
	function test_should_return_proper_count_statistic() {

		$this->factory->post->create_many( 25 );

		$content = array( 'post' );
		$label = 'Posts';
		$type = 'count';

		$count_posts = \wp_count_posts();
		$published_posts = $count_posts->publish;

		$expected = array(
			'label' => $label,
			'tagline' => '',
			'type' => $type,
			'url' => '',
			'value' => $published_posts,
		);

		$count_stat = new Stat( $content, $label, $type );

		$this->assertEquals( $expected, $count_stat->get_statistic() );

	}

	/**
	 * @covers WordpressStats\Common\Stat::build_args()
	 * @covers WordpressStats\Common\Stat::get_statistic()
	 * @covers WordpressStats\Common\Stat::set_value()
	 */
	function test_should_return_proper_popular_statistic() {

		$this->factory->category->create_many( 12 );

		$content = array( 'category' );
		$label = 'Category';
		$type = 'popular';

		$terms = \get_terms(
			array(
				'taxonomy' => $content[0],
				'order' => 'DESC',
				'orderby' => 'count',
				'number' => 1,
			)
		);

		$expected = array(
			'label' => $label,
			'tagline' => $terms[0]->count . ' posts tagged.',
			'type' => $type,
			'url' => \get_term_link( $terms[0] ),
			'value' => $terms[0]->name,
		);

		$popular_stat = new Stat( $content, $label, $type );

		$this->assertEquals( $expected, $popular_stat->get_statistic() );

	}

}
