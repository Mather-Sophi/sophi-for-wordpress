<?php
/**
 * Request curated content
 *
 * @package SophiWP
 */

namespace SophiWP\SiteAutomation;

use function SophiWP\Settings\get_sophi_settings;

/**
 * Class: Request
 */
class Request {

	/**
	 * Auth object which manages access_token.
	 *
	 * @var Auth $auth
	 */
	private $auth;

	/**
	 * Site Automation API URL.
	 *
	 * @var string $api_url
	 */
	private $api_url;

	/**
	 * Page name for Site Automation request.
	 *
	 * @var string $page
	 */
	private $page;

	/**
	 * Widget name for Site Automation request.
	 *
	 * @var string $widget
	 */
	private $widget;

	/**
	 * Status of latest Site Automation request.
	 *
	 * @var array $status
	 */
	private $status;

	/**
	 * Post ID for Site Automation request.
	 *
	 * @var string $post_id
	 */
	protected $post_id;

	/**
	 * Class constructor.
	 *
	 * @param Auth $auth Authentication object.
	 */
	public function __construct( $auth ) {
		$this->auth    = $auth;

		add_action(
			'sophi_retry_get_curated_data',
			[ $this, 'do_cron' ],
			10,
			2
		);
	}

	/**
	 * Get (cached) curated posts.
	 *
	 * @param string $page Page name.
	 * @param string $widget Widget name.
	 * @param float  $timeout The request timeout value.
	 *
	 * @return array|bool
	 */
	public function get( $page, $widget, $timeout = 3 ) {
		$this->page    = $page;
		$this->widget  = $widget;
		$this->api_url = $this->set_api_url( $page, $widget );
		$this->post_id = get_the_ID();

		$this->status         = $this->get_status();
		$site_automation_data = false;

		/**
		 * Whether to bypass caching.
		 *
		 * @since 1.0.9
		 * @hook sophi_bypass_get_cache
		 *
		 * @param {bool} $bypass_cache True or false.
		 * @param {bool} $page Page name.
		 * @param {bool} $widget Widget name.
		 *
		 * @return {bool} Whether to bypass cache.
		 */
		$bypass_cache = apply_filters( 'sophi_bypass_get_cache', false, $page, $widget );

		if ( ! $bypass_cache ) {
			$site_automation_data = get_post_meta( $this->post_id, "_sophi_site_automation_data_{$this->page}_{$this->widget}", true );
		}

		if ( $site_automation_data && ! empty( $this->status['success'] ) ) {
			return $site_automation_data;
		}

		$response = $this->request( $timeout );

		if ( is_wp_error( $response ) ) {
			$this->set_status(
				[
					'success' => false,
					'message' => $response->get_error_message(),
				]
			);

			$this->retry();

			// If we have stale data, use it.
			if ( $site_automation_data ) {
				return $site_automation_data;
			} else {
				return [];
			}
		}

		$this->set_status( [ 'success' => true ] );
		return $this->process( $response, $bypass_cache );
	}

	/**
	 * Retry getting curated data if error occurred.
	 */
	private function retry() {
		$retry_time = $this->status['retry'] < 20 ? 5 * MINUTE_IN_SECONDS : HOUR_IN_SECONDS;

		if ( $this->status['retry'] > 50 ) {
			return;
		}

		wp_schedule_single_event( time() + $retry_time, 'sophi_retry_get_curated_data', [ $this->page, $this->widget ] );
	}

	/**
	 * Cron call back.
	 *
	 * @param string $page Page name.
	 * @param string $widget Widget name.
	 */
	public function do_cron( $page, $widget ) {
		$this->get( $page, $widget, 3 );
	}

	/**
	 * Get request status from database.
	 *
	 * @return array
	 */
	public function get_status() {
		return get_transient( "sophi_site_automation_status_{$this->post_id}_{$this->page}_{$this->widget}" );
	}

	/**
	 * Set request status. Setting both database option and class attribute.
	 *
	 * @param array $data Status array.
	 */
	private function set_status( $data ) {
		$data = wp_parse_args(
			$data,
			[
				'success' => false,
				'retry'   => 0,
				'message' => '',
			]
		);

		if ( empty( $data['success'] ) ) {
			$data['retry'] = $this->status['retry'] + 1;
		} else {
			$data['retry'] = 0;
		}

		$this->status = $data;

		if ( ! empty( $this->post_id ) ) {
			set_transient( "sophi_site_automation_status_{$this->post_id}_{$this->page}_{$this->widget}", $data, $this->get_cache_duration() );
		}
	}

	/**
	 * Get curated data from Sophi Site Automation API.
	 *
	 * @param float $timeout The request timeout value.
	 *
	 * @return mixed WP_Error on failure or body request on success.
	 */
	private function request( $timeout ) {
		$access_token = $this->auth->get_access_token();

		if ( is_wp_error( $access_token ) ) {
			return $access_token;
		}

		$args = [
			'headers' => [
				'Content-Type'  => 'application/json',
				'Authorization' => 'Bearer ' . $access_token,
			],
		];

		/**
		 * Filters the arguments used in Sophi HTTP request.
		 *
		 * @since 1.0.14
		 * @hook sophi_request_args
		 *
		 * @param {array}   $args HTTP request arguments.
		 * @param {string}  $url  The request URL.
		 *
		 * @return {array} HTTP request arguments.
		 */
		$args = apply_filters( 'sophi_request_args', $args, $this->api_url );

		if ( function_exists( 'vip_safe_wp_remote_get' ) ) {
			$result = vip_safe_wp_remote_get( $this->api_url, '', 3, $timeout, 20, $args );
		} else {
			$args['timeout'] = $timeout;
			$result          = wp_remote_get( $this->api_url, $args ); // phpcs:ignore
		}

		/**
		 * Filters a Sophi HTTP request immediately after the response is received.
		 *
		 * @since 1.0.14
		 * @hook sophi_request_result
		 *
		 * @param {array|WP_Error}  $result Result of HTTP request.
		 * @param {array}           $args     HTTP request arguments.
		 * @param {string}          $url      The request URL.
		 *
		 * @return {array|WP_Error} Result of HTTP request.
		 */
		$result = apply_filters( 'sophi_request_result', $result, $args, $this->api_url );

		if ( is_wp_error( $result ) ) {
			return $result;
		}

		if ( wp_remote_retrieve_response_code( $result ) !== 200 ) {
			return new \WP_Error( wp_remote_retrieve_response_code( $result ), $result['response']['message'] );
		}

		return json_decode( wp_remote_retrieve_body( $result ), true );
	}

	/**
	 * Process response from Sophi.
	 *
	 * @param array $response Response of Site Automation API.
	 * @param bool  $bypass_cache Whether to bypass cache or not.
	 *
	 * @return array
	 */
	private function process( $response, $bypass_cache ) {
		if ( ! $response ) {
			return [];
		}


		$post = get_post();

		if ( ! $post || wp_is_post_revision( $post ) ) {
			return $response;
		}

		$meta_key   = "_sophi_site_automation_data_{$this->page}_{$this->widget}";
		$created_at = date_create( 'now', wp_timezone() );

		if ( $created_at && ! $bypass_cache ) {
			update_post_meta( $post->ID, $meta_key, $response );
			update_post_meta( $post->ID, $meta_key . '_created_at', $created_at->getTimestamp() );
		}

		return $response;
	}

	/**
	 * Prepare Site Automation API URL
	 *
	 * @param string $page Page name.
	 * @param string $widget Widget name.
	 *
	 * @return string
	 */
	private function set_api_url( $page, $widget ) {
		$site_automation_url = get_sophi_settings( 'site_automation_url' );
		$site_automation_url = untrailingslashit( $site_automation_url );

		$host = parse_url( get_home_url(), PHP_URL_HOST );

		return sprintf( '%1$s/curatedHosts/%2$s/curator?page=%3$s&widget=%4$s', $site_automation_url, $host, $page, $widget );
	}

	/**
	 * Filterable cache duration.
	 *
	 * @return int
	 */
	private function get_cache_duration() {
		return apply_filters( 'sophi_cache_duration', 5 * MINUTE_IN_SECONDS );
	}
}
