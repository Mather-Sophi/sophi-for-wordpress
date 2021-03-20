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

		add_filter( 'posts_pre_query', [ $this, 'get_curated_posts' ], 10, 2 );
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

		if ( empty( $query_vars['sophi_curated_page'] ) || empty( $query_vars['sophi_curated_widget'] ) ) {
			return $posts;
		}

		$curated_response = $this->request->get( $query_vars['sophi_curated_page'], $query_vars['sophi_curated_widget'] );

		// Determine how we should format the results  based on the fields parameter.
		$fields = $query->get( 'fields', '' );

		switch ( $fields ) {
			case 'ids':
				$new_posts = $this->format_hits_as_ids( $curated_response );
				break;

			case 'id=>parent':
				$new_posts = $this->format_hits_as_id_parents( $curated_response );
				break;

			default:
				$new_posts = $this->format_hits_as_posts( $curated_response );
				break;
		}

		if ( ! empty( $new_posts ) ) {
			return $new_posts;
		}

		return $posts;
	}

	/**
	 * Format results as an array of ID.
	 *
	 * @param array $data Response from Sophi.
	 *
	 * @return array
	 */
	private function format_hits_as_ids( $data ) {
		return array_map(
			function( $id ) {
				return intval( $id );
			},
			$data
		);
	}

	/**
	 * Format the results as objects containing id and parent id.
	 *
	 * @param array $data Response from Sophi.
	 *
	 * @return array
	 */
	private function format_hits_as_id_parents( $data ) {
		return array_map(
			function( $id ) {
				$post              = new \stdClass();
				$post->ID          = intval( $id );
				$post->post_parent = wp_get_post_parent_id( intval( $id ) );

				return $post;
			},
			$data
		);
	}

	/**
	 * Format the results as post objects.
	 *
	 * @param array $data Response from Sophi.
	 *
	 * @return array
	 */
	private function format_hits_as_posts( $data ) {
		return array_map(
			function( $id ) {
				$post = get_post( intval( $id ) );
				if ( is_a( $post, 'WP_Post' ) ) {
					return $post;
				}
			},
			$data
		);
	}
}
