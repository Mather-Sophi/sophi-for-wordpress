<?php
/**
 * EndPoints for curated content
 *
 * @package SophiWP
 */

namespace SophiWP\SiteAutomation;

use WP_REST_Controller;
use WP_REST_Server;
use function SophiWP\Settings\get_sophi_settings;

/**
 * Class: EndPoints
 */
class EndPoints extends WP_REST_Controller {

	/**
	 * Sophi default namespace for REST endpoints.
	 */
	const SOPHI_NAMESPACE = 'sophi/v1';

	/**
	 * Auth object.
	 *
	 * @var Request $auth
	 */
	public $auth;

	/**
	 * Request object.
	 *
	 * @var Request $request
	 */
	public $request;

	/**
	 * Class constructor.
	 *
	 */
	public function __construct( $auth, $request ) {
		$this->auth    = $auth;
		$this->request = $request;

		add_action( 'rest_api_init', array( $this, 'register_routes' ) );
	}

	/**
	 * Called automatically on `rest_api_init()`.
	 */
	public function register_routes() {
		// GET /sophi/v1/get-posts route.
		register_rest_route(
			self::SOPHI_NAMESPACE,
			'/curator-posts',
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_curator_posts' ),
					'permission_callback' => '__return_true',
					'args'                => $this->get_curator_posts_params(),
				),
			)
		);

		// POST /sophi/v1/update-posts route.
		register_rest_route(
			self::SOPHI_NAMESPACE,
			'/override-post',
			array(
				array(
					'methods'             => WP_REST_Server::CREATABLE,
					'callback'            => array( $this, 'override_curator_post' ),
					'permission_callback' => '__return_true',
					'args'                => $this->override_curator_post_params(),
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
	public function get_curator_posts( $request ) {
		// Request parameters.
		$attributes = $request->get_query_params();

		$page_name              = isset( $attributes['pageName'] ) ? sanitize_title( $attributes['pageName'] ) : '';
		$override_post_ID       = isset( $attributes['overridePostID'] ) ? sanitize_title( $attributes['overridePostID'] ) : '';
		$widget_name            = isset( $attributes['widgetName'] ) ? sanitize_title( $attributes['widgetName'] ) : '';
		$display_featured_image = isset( $attributes['displayFeaturedImage'] ) ? sanitize_title( $attributes['displayFeaturedImage'] ) : '';
		$display_author         = isset( $attributes['displayAuthor'] ) ? sanitize_title( $attributes['displayAuthor'] ) : '';
		$display_post_date      = isset( $attributes['displayPostDate'] ) ? sanitize_title( $attributes['displayPostDate'] ) : '';
		$display_post_excerpt   = isset( $attributes['displayPostExcept'] ) ? sanitize_title( $attributes['displayPostExcept'] ) : '';

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
				'orderby'   => 'post__in'
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

		$post_data->postLink = get_the_permalink( $post_ID );
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
	public function get_curator_posts_params() {
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
	public function override_curator_post( $request ) {
		$current_user = wp_get_current_user();

		if ( $current_user->exists() ) {
			$user_email = $current_user->user_email;
		} else {
			return new \WP_Error( 401, __( 'Unauthorised user, please log in.', 'sophi-wp' ) );
		}

		// Get the auth token.
		$api_token = $this->auth->get_access_token();

		if ( is_wp_error( $api_token ) ) {
			return new \WP_Error( 401, __( 'Invalid API token, please try adding correct credentials on the settings page.', 'sophi-wp' ) );
		}

		// Request parameters.
		$timeout    = 3;
		$attributes = $request->get_query_params();

		$api_url = get_sophi_settings( 'sophi_override_url' );
		$host    = get_sophi_settings( 'host' );
		$api_url = $api_url . 'hosts/' . $host . '/overrides';

		$rule_type        = isset( $attributes['ruleType'] ) ? sanitize_title( $attributes['ruleType'] ) : '';
		$page_name        = isset( $attributes['pageName'] ) ? sanitize_title( $attributes['pageName'] ) : '';
		$override_post_ID = isset( $attributes['overridePostID'] ) ? sanitize_title( $attributes['overridePostID'] ) : '';
		$widget_name      = isset( $attributes['widgetName'] ) ? sanitize_title( $attributes['widgetName'] ) : '';
		$override_expiry  = isset( $attributes['widgetName'] ) ? sanitize_title( $attributes['overrideExpiry'] ) : 2;
		$position         = isset( $attributes['position'] ) ? sanitize_title( $attributes['position'] ) : 1;

		if ( empty( $widget_name ) && 'ban' !== $rule_type ) {
			return new \WP_Error( 401, __( 'Missing parameter: widgetName', 'sophi-wp' ) );
		}

		$body = array(
			"articleId"           => $override_post_ID,
			"expirationHourOfDay" => $override_expiry,
			"page"                => $page_name,
			"position"            => 'ban' === $rule_type || 'out' === $rule_type ? '' : $position,
			"requestedUserName"   => $user_email,
			"ruleType"            => 'ban' === $rule_type ? 'out' : $rule_type,
			"widgetName"          => 'ban' === $rule_type ? null : $widget_name,
		);
		$body = wp_json_encode( $body );
		$args = [
			'method'  => 'POST',
			'headers' => array(
				'Content-Type'  => 'application/json',
				'Authorization' => 'Bearer ' . $api_token,
				'Cache-Control' => 'no-cache',
			),
			'body'    => $body,
		];

		/**
		 * Filters the arguments being passed to the override api auth request.
		 *
		 * @param array $args Arguments.
		 * @param string $api_url Auth API URL.
		 *
		 * @since 1.3.0
		 *
		 */
		$args = apply_filters( 'sophi_override_request_args', $args, $api_url );

		if ( function_exists( 'vip_safe_wp_remote_get' ) ) {
			$result = vip_safe_wp_remote_get( $api_url, '', 3, $timeout, 20, $args );
		} else {
			$args['timeout'] = $timeout;
			$result          = wp_remote_post( $api_url, $args ); // phpcs:ignore
		}

		if ( is_wp_error( $result ) ) {
			return $result;
		}

		if ( wp_remote_retrieve_response_code( $result ) !== 200 ) {
			return new \WP_Error( wp_remote_retrieve_response_code( $result ), $result['response']['message'] );
		}

		if ( 200 === $result['response']['code'] ) {
			// Update the override entry in the database, so we don't have
			// to wait for API to update the details at front end.
			$override_post = [
				"overridePostID" => $override_post_ID,
				"position"       => $position,
				"ruleType"       => 'ban' === $rule_type ? 'out' : $rule_type,
			];
			$this->request->get( $page_name, $widget_name, 3, $override_post );
		}

		return json_decode( wp_remote_retrieve_body( $result ), true );
	}

	/**
	 * Get the query params to update the sophi posts.
	 *
	 * @since 1.3.0
	 *
	 * @return array
	 */
	public function override_curator_post_params() {
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
			'required'          => false,
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
