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

		$page_name              = sanitize_title( $attributes['pageName'] );
		$override_post_ID       = sanitize_title( $attributes['overridePostID'] );
		$widget_name            = sanitize_title( $attributes['widgetName'] );
		$display_featured_image = sanitize_title( $attributes['displayFeaturedImage'] );
		$display_author         = sanitize_title( $attributes['displayAuthor'] );
		$display_post_date      = sanitize_title( $attributes['displayPostDate'] );
		$display_post_excerpt   = sanitize_title( $attributes['displayPostExcept'] );

		$rules = [
			'display_featured_image' => $display_featured_image,
			'display_author'         => $display_author,
			'display_post_date'      => $display_post_date,
			'display_post_excerpt'   => $display_post_excerpt,
		];

		// If override ID exists, only return the post data.
		// This is required only when updating (add/replace) the innerBlocks.
		if ( ! empty( $override_post_ID ) ) {
			$curated_posts     = [];
			$post_data         = get_post( $override_post_ID );
			$post_data_updated = $this->get_post_details( $override_post_ID, $rules );
			$curated_posts[]   = (object) array_merge( (array) $post_data, (array) $post_data_updated );

			return $curated_posts;
		}

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

		} else {
			$curated_posts = get_posts( [
				'post__in'  => $curated_posts,
				'post_type' => 'post',
			] );
		}

		$rules = [
			'display_featured_image' => $display_featured_image,
			'display_author'         => $display_author,
			'display_post_date'      => $display_post_date,
			'display_post_excerpt'   => $display_post_excerpt,
		];
		foreach ( $curated_posts as $index => $curated_post ) {
			$post_data_updated       = $this->get_post_details( $curated_post->ID, $rules );
			$curated_posts[ $index ] = (object) array_merge( (array) $curated_post, (array) $post_data_updated );
		}

		return $curated_posts;
	}

	/**
	 * Get the post data with prepared items to be rendered to the inner blocks.
	 *
	 * @param int   $post_ID Post ID.
	 * @param array $rules array of the parent block settings.
	 *
	 * @return \stdClass Prepared post data.
	 */
	public function get_post_details( $post_ID, $rules ) {
		$post_data = new \stdClass();

		$post_data->postLink = get_post_permalink( $post_ID );
		if ( $rules['display_featured_image'] ) {
			$post_data->featuredImage = get_the_post_thumbnail( $post_ID );
		}
		if ( $rules['display_author'] ) {
			$author_id             = get_post_field( 'post_author', $post_ID );
			$author_display_name   = get_the_author_meta( 'display_name', $author_id );
			$byline                = sprintf( __( 'by %s', 'sophi-wp' ), $author_display_name );
			$post_data->postAuthor = $byline;
		}
		if ( $rules['display_post_date'] ) {
			$post_data->postDate  = get_the_date( '', $post_ID );
			$post_data->postDateC = get_the_date( 'c', $post_ID );
		}
		if ( $rules['display_post_excerpt'] ) {
			$post_data->post_excerpt = wp_kses_post( get_the_excerpt( $post_ID ) );
		}

		return $post_data;
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
		$current_user = wp_get_current_user();

		if ( $current_user->exists() ) {
			$user_email = $current_user->user_email;
		} else {
			return __( 'Unauthorised user, please log in.', 'sophi-wp' );
		}

		// @TODO: Create auth token periodically.
		$api_token = get_option( 'auth_token' );

		// @TODO: Get details from the settings page.
		// Request parameters.
		$timeout = 3;
		$attributes = $request->get_query_params();
		$api_url = 'https://site-automation-api.ml.sophi.works/v1/hosts/sophi.10uplabs.dev/overrides';

		$rule_type        = sanitize_title( $attributes['ruleType'] );
		$override_post_ID = sanitize_title( $attributes['overridePostID'] );
		$override_expiry  = sanitize_title( $attributes['overrideExpiry'] );
		$position         = sanitize_title( $attributes['position'] );
		$page_name        = sanitize_title( $attributes['pageName'] );
		$widget_name      = sanitize_title( $attributes['widgetName'] );

		// @TODO: Update the cache in the database so we don't have to wait for API to update the details.

		$body = array(
			"articleId"           => $override_post_ID,
			"expirationHourOfDay" => $override_expiry,
			"page"                => $page_name,
			"position"            => $position,
			"requestedUserName"   => $user_email,
			"ruleType"            => $rule_type,
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
		$params['overridePostID'] = array(
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
