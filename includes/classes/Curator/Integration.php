<?php
/**
 * Curator Integration: Hook into WP_Query or block to replace WordPress content with Sophi curated.
 *
 * @package SophiWP
 */

namespace SophiWP\Curator;

/**
 * Class: Integration.
 */
class Integration {
	/**
	 * Request object.
	 *
	 * @var Request $request
	 */
	private $request;

	/**
	 * Class constructor.
	 *
	 * @param Request $request Request object.
	 */
	public function __construct( $request ) {
		$this->request = $request;

		add_filter( 'posts_pre_query', 'get_curated_posts', 10, 2 );
	}

	/**
	 * Inject Sophi data to WP_Query.
	 *
	 * @param array|null $posts Return an array of post data to short-circuit WP's query,
	 *                          or null to allow WP to run its normal queries.
	 * @param \WP_Query  $query  The WP_Query instance (passed by reference).
	 */
	public function get_curated_posts( $posts, $query ) {
		$query_vars = $query->query_vars;

		if ( empty( $query_vars['sophi_integrate'] ) ) {
			return $posts;
		}

		return $posts;
	}
}
