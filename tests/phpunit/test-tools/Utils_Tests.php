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
}
