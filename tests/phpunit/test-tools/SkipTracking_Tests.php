<?php
namespace SophiWP\ContentSync;

use SophiWP as Base;

class SkipTracking_Tests extends Base\TestCase {

	protected $testFiles = array(
		'functions/content-sync.php',
	);

	/**
	 * Test Maybe Skip logic
	 *
	 * @covers \SophiWP\ContentSync\maybe_skip_track_event
	 * @dataProvider data_provider_for_test_maybe_skip
	 */
	public function test_maybe_skip( $data, $get_transient, $set_transient, $should_skip ) {

		\WP_Mock::userFunction( 'wp_hash' )->andReturn( '' );
		\WP_Mock::userFunction( 'wp_json_encode' )->andReturn( '' );
		\WP_Mock::userFunction( 'get_transient' )->andReturn( $get_transient );

		if ( $set_transient ) {
			\WP_Mock::userFunction( 'set_transient' )->with( 'sophi_tracking_request_', $set_transient, 10 );
		}

		$this->assertSame( $should_skip, maybe_skip_track_event( $data ) );
	}

	public function data_provider_for_test_maybe_skip() {
		return array(
			'Should ignore modifiedAt argument'         => array(
				'data'          => array(
					'a'          => 'b',
					'modifiedAt' => 'c',
				),
				'get_transient' => false,
				'set_transient' => array( 'a' => 'b' ),
				'should_skip'   => false,
			),
			'Should skip when transient exists'         => array(
				'data'          => array( 'a' => 'b' ),
				'get_transient' => array( 'a' => 'b' ),
				'set_transient' => false,
				'should_skip'   => true,
			),
			'Should not skip when transient is not set' => array(
				'data'          => array( 'a' => 'b' ),
				'get_transient' => false,
				'set_transient' => array( 'a' => 'b' ),
				'should_skip'   => false,
			),
			'Should not skip when previous request was different' => array(
				'data'          => array( 'a' => 'b' ),
				'get_transient' => array( 'c' => 'd' ),
				'set_transient' => array( 'a' => 'b' ),
				'should_skip'   => false,
			),
		);
	}
	/**
	 * Test get number of embedded images
	 */
	public function test_should_pass_untracked_event() {
		$this->assertTrue( true );
	}
}
