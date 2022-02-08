<?php
/**
 * Site Automation Integration: Hook into WP_Query or block to replace WordPress content with Sophi curated.
 *
 * @package SophiWP
 */

namespace SophiWP\SiteAutomation;

use function SophiWP\Settings\get_sophi_settings;

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
		add_filter( 'found_posts', array( $this, 'found_posts' ), 10, 2 );
	}

	/**
	 * Change the found_posts variable on WP_Query.
	 *
	 * @param int      $found_posts Number of found posts
	 * @param WP_Query $query Query object
	 * @return int Found posts.
	 */
	public function found_posts( $found_posts, $query ) {
		if ( isset( $query->sophi_curated_post_list_success ) && $query->sophi_curated_post_list_success ) {
			return $query->num_posts;
		}

		return $found_posts;
	}

	/**
	 * Inject Sophi data to WP_Query.
	 *
	 * @param array|null $posts Return an array of post data to short-circuit WP's query,
	 *                          or null to allow WP to run its normal queries.
	 * @param \WP_Query  $query  The WP_Query instance (passed by reference).
	 */
	public function get_curated_posts( $posts, $query ) {
		$query_integration = get_sophi_settings( 'query_integration' );

		if ( 1 !== intval( $query_integration ) ) {
			return $posts;
		}

		$query_vars = $query->query_vars;

		if ( empty( $query_vars['sophi_curated_page'] ) || empty( $query_vars['sophi_curated_widget'] ) ) {
			return $posts;
		}

		$curated_response = $this->request->get( $query_vars['sophi_curated_page'], $query_vars['sophi_curated_widget'] );
		$request_status   = $this->request->get_status();

		if ( $request_status['success'] ) {
			$query->sophi_curated_post_list_success = true;
			$query->num_posts = count( $curated_response );

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

			/**
			 * The curated post list result that is injected to WP_Query.
			 *
			 * @since 1.0.9
			 * @hook sophi_curated_post_list
			 *
			 * @param {array} $new_posts Post list.
			 * @param {string} $query_vars['sophi_curated_page'] Sophi curated page param.
			 * @param {string} $query_vars['sophi_curated_widget'] Sophi curated widget param.
			 * @param {object} $query Original query.
			 *
			 * @return {integer} Posts per page limit.
			 */
			return apply_filters( 'sophi_curated_post_list', array_filter( $new_posts ), $query_vars['sophi_curated_page'], $query_vars['sophi_curated_widget'], $query );
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
