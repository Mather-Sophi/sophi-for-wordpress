<?php
namespace SophiWP\Utils;

use SophiWP as Base;

class Core_Tests extends Base\TestCase {

	protected $testFiles = array(
		'functions/utils.php',
	);

	/**
	 * Test get number of embedded images
	 *
	 * @dataProvider data_provider_embedded_images
	 */
	public function test_get_number_of_embedded_images( $html, $count ) {
		// wp_kses expected to be called once if HTML is not empty.
		\WP_Mock::passthruFunction( 'wp_kses', array( 'times' => $count === false ? 0 : 1 ) );

		$this->assertEquals( $count, get_number_of_embedded_images( $html ) );
	}

	public function data_provider_embedded_images() {
		return array(
			array(
				'html'  => '',
				'count' => false,
			),
			array(
				'html'  => 'Some Content',
				'count' => 0,
			),
			array(
				'html'  => 'Some <img src="file.jpg"> Content',
				'count' => 1,
			),
			array(
				'html'  => '<img src="file.jpg"> Some <img src="file.jpg"> Content',
				'count' => 2,
			),
			array(
				'html'  => '<strong>Some <img src="file.jpg"> Content</strong>',
				'count' => 1,
			),
		);
	}

	public function test_get_primary_category__default_category__yoast_deactivated() {
		$term_array = array(
			(object) array(
				'term_id' => 10,
				'name'    => 'Uncategorized',
			)
		);

		\WP_Mock::userFunction( 'wp_get_post_terms', array(
			'times'  => 1,
			'args'   => array( null, 'category' ),
			'return' => $term_array,
		) );

		$term_name = get_primary_category();

		$this->assertEquals( $term_name, 'Uncategorized' );
	}

	public function test_get_primary_category__multiple_categories__yoast_deactivated() {
		$term_array = array(
			(object) array(
				'term_id' => 10,
				'name'    => 'Science',
			),
			(object) array(
				'term_id' => 10,
				'name'    => 'Uncategorized',
			)
		);

		\WP_Mock::userFunction( 'wp_get_post_terms', array(
			'times'  => 1,
			'args'   => array( null, 'category' ),
			'return' => $term_array,
		) );

		$term_name = get_primary_category();

		$this->assertEquals( $term_name, 'Science' );
	}

	public function test_get_primary_category__default_category__yoast_activated() {
		\WP_Mock::userFunction( 'yoast_get_primary_term_id', array(
			'times'  => 1,
			'args'   => array( 'category', null ),
			'return' => 123,
		) );

		\WP_Mock::userFunction( 'get_term', array(
			'times'  => 1,
			'args'   => array( 123 ),
			'return' => (object) array(
				'term_id' => 123,
				'name'    => 'Uncategorized',
			),
		) );

		$term_name = get_primary_category();

		$this->assertEquals( $term_name, 'Uncategorized' );
	}
}
