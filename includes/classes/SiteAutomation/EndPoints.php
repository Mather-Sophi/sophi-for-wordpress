<?php
/**
 * EndPoints for curated content
 *
 * @package SophiWP
 */

namespace SophiWP\SiteAutomation;

use WP_REST_Controller;
use WP_REST_Server;

/**
 * Class: EndPoints
 */
class EndPoints extends WP_REST_Controller {

	const SOPHI_NAMESPACE = 'sophi/v1';

	/**
	 * Class constructor.
	 *
	 */
	public function __construct() {
		add_action( 'rest_api_init', array( $this, 'register_routes' ) );
	}

	/**
	 * Called automatically on `rest_api_init()`.
	 */
	public function register_routes() {
		// GET /sophi/v1/get-posts route.
		register_rest_route(
			self::SOPHI_NAMESPACE,
			'/get-posts',
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_sophi_posts' ),
					'permission_callback' => '__return_true',
					'args'                => $this->get_collection_params(),
				),
			)
		);

		// POST /sophi/v1/update-posts route.
		register_rest_route(
			self::SOPHI_NAMESPACE,
			'/update-posts',
			array(
				array(
					'methods'             => WP_REST_Server::CREATABLE,
					'callback'            => array( $this, 'update_sophi_posts' ),
					'permission_callback' => '__return_true',
					'args'                => $this->update_collection_params(),
				),
			)
		);
	}

	/**
	 * Get the sophi posts.
	 *
	 * @since 1.3.0
	 *
	 * @param  WP_REST_Request $request The request.
	 * @return mixed
	 */
	public function get_sophi_posts( $request ) {
		// Request parameters.
		$attributes = $request->get_query_params();

		if ( empty( $attributes['pageName'] ) || empty( $attributes['widgetName'] ) ) {
			return '';
		}

		$page_name   = sanitize_title( $attributes['pageName'] );
		$widget_name = sanitize_title( $attributes['widgetName'] );

		/**
		 * Whether to bypass caching.
		 *
		 * @param {bool} $bypass_cache True or false.
		 * @param {string} $page Page name.
		 * @param {string} $widget Widget name.
		 *
		 * @return {bool} Whether to bypass cache.
		 * @since 1.1.1
		 * @hook sophi_bypass_curated_posts_cache
		 *
		 */
		$bypass_cache = apply_filters( 'sophi_bypass_curated_posts_cache', false, $page_name, $widget_name );

		$curated_posts = false;

		if ( ! $bypass_cache ) {
			$sophi_cached_response = new \WP_Query(
				[
					'post_name__in'          => [ "sophi-site-automation-data-{$page_name}-{$widget_name}" ],
					'post_type'              => 'sophi-response',
					'post_status'            => 'any',
					'posts_per_page'         => 1,
					'fields'                 => 'ids',
					'no_found_rows'          => true,
					'update_post_term_cache' => false
				]
			);

			if ( $sophi_cached_response->have_posts() ) {
				$last_update = get_post_meta( $sophi_cached_response->posts[0], 'sophi_site_automation_last_updated', true );

				if ( $last_update + 5 * MINUTE_IN_SECONDS > time() ) {
					$curated_posts = get_post_meta( $sophi_cached_response->posts[0], 'sophi_site_automation_data', true );
				}
			}
		}

		if ( $bypass_cache || ! $curated_posts ) {

			// phpcs:ignore WordPressVIPMinimum.Functions.RestrictedFunctions.get_posts_get_posts
			$curated_posts = get_posts(
				[
					'sophi_curated_page'   => $page_name,
					'sophi_curated_widget' => $widget_name,
				]
			);

			if ( empty( $curated_posts ) ) {
				return '';
			}

		}

		return $curated_posts;
	}

	/**
	 * Get the query params to get the sophi posts.
	 *
	 * @since 1.3.0
	 *
	 * @return array
	 */
	public function get_collection_params() {
		$params['pageName'] = array(
			'description'       => __( 'Name of the page.', 'sophi-wp' ),
			'required'          => true,
			'type'              => 'string',
			'sanitize_callback' => 'sanitize_text_field',
			'validate_callback' => 'rest_validate_request_arg',
		);

		$params['widgetName'] = array(
			'description'       => __( 'Name of the widget.', 'sophi-wp' ),
			'required'          => true,
			'type'              => 'string',
			'sanitize_callback' => 'sanitize_text_field',
			'validate_callback' => 'rest_validate_request_arg',
		);

		/**
		 * Filters the get sophi posts query params.
		 *
		 * @param array $params Query params.
		 */
		return apply_filters( 'sophi_get_posts_params', $params );
	}

	/**
	 * Update the sophi posts.
	 *
	 * @since 1.3.0
	 *
	 * @param  WP_REST_Request $request The request.
	 * @return mixed
	 */
	public function update_sophi_posts( $request ) {
		// Request parameters.
		$timeout = 3;
		$attributes = $request->get_query_params();
		$api_url = 'https://site-automation-api.ml.sophi.works/v1/hosts/sophi.10uplabs.dev/overrides';

		//		'Authorization' => 'Basic ' . base64_encode( $username . ':' . $password ),
		$api_token = '';

		$rule_type   = sanitize_title( $attributes['ruleType'] );
		$post_ID  = sanitize_title( $attributes['postID'] );
		$page_name   = sanitize_title( $attributes['pageName'] );
		$widget_name = sanitize_title( $attributes['widgetName'] );

		$body = array(
			"articleId"           => $post_ID,
			"expirationHourOfDay" => 2,
			"page"                => $page_name,
			"position"            => 3,
			"requestedUserName"   => "admin",
			"ruleType"            => "in",
			"widgetName"          => $widget_name,
		);
		$body = wp_json_encode( $body );
		$args = [
			'method'      => 'POST',
			'headers' => array(
				'Content-Type'  => 'application/json',
				'Authorization' => 'Bearer ' . $api_token,
				'Cache-Control' => 'no-cache',
			),
			'body'      =>  $body,
		];

		if( 'in' === $rule_type ) {
			if ( function_exists( 'vip_safe_wp_remote_get' ) ) {
				$result = vip_safe_wp_remote_get( $api_url, '', 3, $timeout, 20, $args );
			} else {
				$args['timeout'] = $timeout;
				$result = wp_remote_post( $api_url, $args ); // phpcs:ignore
			}

			if ( is_wp_error( $result ) ) {
				return $result;
			}

			if ( wp_remote_retrieve_response_code( $result ) !== 200 ) {
				return new \WP_Error( wp_remote_retrieve_response_code( $result ), $result['response']['message'] );
			}

			return json_decode( wp_remote_retrieve_body( $result ), true );
		}

		return false;
	}

	/**
	 * Get the query params to update the sophi posts.
	 *
	 * @since 1.3.0
	 *
	 * @return array
	 */
	public function update_collection_params() {
		$params['ruleType'] = array(
			'description'       => __( 'The rule of Override.', 'sophi-wp' ),
			'required'          => true,
			'type'              => 'string',
			'sanitize_callback' => 'sanitize_text_field',
			'validate_callback' => 'rest_validate_request_arg',
		);
		$params['postID'] = array(
			'description'       => __( 'ID of the post.', 'sophi-wp' ),
			'required'          => true,
			'type'              => 'int',
			'sanitize_callback' => 'absint',
			'validate_callback' => 'rest_validate_request_arg',
		);
		$params['pageName'] = array(
			'description'       => __( 'Name of the page.', 'sophi-wp' ),
			'required'          => true,
			'type'              => 'string',
			'sanitize_callback' => 'sanitize_text_field',
			'validate_callback' => 'rest_validate_request_arg',
		);
		$params['widgetName'] = array(
			'description'       => __( 'Name of the widget.', 'sophi-wp' ),
			'required'          => true,
			'type'              => 'string',
			'sanitize_callback' => 'sanitize_text_field',
			'validate_callback' => 'rest_validate_request_arg',
		);

		/**
		 * Filters the get sophi posts query params.
		 *
		 * @param array $params Query params.
		 */
		return apply_filters( 'sophi_get_posts_params', $params );
	}
}
