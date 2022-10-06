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
	 * Class constructor.
	 *
	 */
	public function __construct() {
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
	 * @param array  $override_post We pass the post data that needs be overridden.
	 *
	 * @return array|bool
	 */
	public function get( $page, $widget, $timeout = 3, $override_post = array() ) {
		$this->page    = $page;
		$this->widget  = $widget;
		$this->api_url = $this->set_api_url( $page, $widget );

		$this->status         = $this->get_status();
		$site_automation_data = false;
		$post_id = false;

		$override_in_action = 0 !== count( $override_post );

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
		$bypass_cache = $override_in_action ? false : apply_filters( 'sophi_bypass_get_cache', false, $page, $widget );

		if ( ! $bypass_cache ) {
			$query = new \WP_Query(
				[
					'post_name__in'          => [ "sophi-site-automation-data-{$page}-{$widget}" ],
					'post_type'              => 'sophi-response',
					'posts_per_page'         => 1,
					'fields'                 => 'ids',
					'post_status'            => 'any',
					'no_found_rows'          => true,
					'update_post_term_cache' => false
				]
			);

			if ( $query->have_posts() ) {
				$post_id = $query->posts[0];
				$last_update = get_post_meta( $post_id, 'sophi_site_automation_last_updated', true );

				if ( $last_update + 5 * MINUTE_IN_SECONDS > time() || $override_in_action ) {
					$site_automation_data = get_post_meta( $post_id, 'sophi_site_automation_data', true );
				}
			}
		}

		if ( $site_automation_data && ! empty( $this->status['success'] ) && ! $override_in_action ) {
			return $site_automation_data;
		}

		// If override data is received, inject it into the database, and skip the actual call to API.
		if( $override_in_action && is_array( $site_automation_data ) ) {
			if( 'in' === $override_post['ruleType'] ) {
				array_splice( $site_automation_data, $override_post['position'], 0, $override_post['overridePostID'] );
			}

			$response = $site_automation_data;
		} else {
			$response = $this->request( $timeout );
		}

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
		return $this->process( $response, $bypass_cache, $post_id );
	}

	/**
	 * Get the Override API token. Re-generate if expired.
	 *
	 * @return string|\WP_Error API token or WP Error.
	 */
	public function get_the_override_token() {
		$api_token = get_transient( 'sophi_override_auth_token' );

		if( ! $api_token ) {
			$timeout = 3;

			$api_url       = get_sophi_settings( 'sophi_override_auth_url' );
			$client_id     = get_sophi_settings( 'sophi_override_client_id' );
			$client_secret = get_sophi_settings( 'sophi_override_client_secret' );
			$audience      = get_sophi_settings( 'sophi_override_audience' );
			$grant_type    = get_sophi_settings( 'sophi_override_grant_type' );

			$body = array(
				"client_id"     => $client_id,
				"client_secret" => $client_secret,
				"audience"      => $audience,
				"grant_type"    => $grant_type,
			);
			$body = wp_json_encode( $body );
			$args = [
				'method'  => 'POST',
				'timeout' => 3,
				'headers' => array(
					'Content-Type'  => 'application/json',
					'Cache-Control' => 'no-cache',
				),
				'body'    => $body,
			];

			/**
			 * Filters the arguments being passed to the override api auth request.
			 *
			 * @since 1.3.0
			 *
			 * @param array  $args    Arguments.
			 * @param string $api_url Auth API URL.
			 */
			$args = apply_filters( 'sophi_override_auth_request_args', $args, $api_url );

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

			$response = json_decode( wp_remote_retrieve_body( $result ) );

			$api_token = $response->access_token;
			set_transient( 'sophi_override_auth_token', $api_token, DAY_IN_SECONDS );
		}

		return $api_token;
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
		return get_transient( "sophi_site_automation_status_{$this->page}_{$this->widget}" );
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
			$data['retry'] = is_array( $this->status ) ? $this->status['retry'] + 1 : 1;
		} else {
			$data['retry'] = 0;
		}

		$this->status = $data;
		set_transient( "sophi_site_automation_status_{$this->page}_{$this->widget}", $data, $this->get_cache_duration() );
	}

	/**
	 * Get curated data from Sophi Site Automation API.
	 *
	 * @param float $timeout The request timeout value.
	 *
	 * @return mixed WP_Error on failure or body request on success.
	 */
	private function request( $timeout ) {

		$args = [
			'headers' => [
				'Content-Type'  => 'application/json',
				'Cache-Control' => 'no-cache',
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
	 * @param array    $response Response of Site Automation API.
	 * @param bool     $bypass_cache Whether to bypass cache or not.
	 * @param int|bool $post_id The post id to update the post meta with response or false.
	 *
	 * @return array
	 */
	private function process( $response, $bypass_cache, $post_id ) {
		if ( ! $response ) {
			return [];
		}

		if ( ! $bypass_cache ) {
			if ( ! $post_id ) {
				$post_id = wp_insert_post(
					[
						'post_type'  => 'sophi-response',
						'post_title' => "sophi-site-automation-data-{$this->page}-{$this->widget}",
						'post_name'  => "sophi-site-automation-data-{$this->page}-{$this->widget}",
					]
				);
			}
			update_post_meta( $post_id, 'sophi_site_automation_data', $response );
			update_post_meta( $post_id, 'sophi_site_automation_last_updated', time() );
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
	private function set_api_url( $page = '', $widget = '' ) {
		$site_automation_url = get_sophi_settings( 'site_automation_url' );
		$site_automation_url = untrailingslashit( $site_automation_url );
		$host                = get_sophi_settings( 'host' );
		$tenant_id           = get_sophi_settings( 'tenant_id' );

		$url = sprintf(
			'%1$s/%2$s/hosts/%3$s/pages/%4$s',
			$site_automation_url,
			$tenant_id,
			$host,
			$page
		);

		if ( ! empty ( $widget ) ) {
			$url = sprintf(
				'%1$s/widgets/%2$s',
				$url,
				$widget
			);
		}

		return $url . '.json';
	}

	/**
	 * Filterable cache duration.
	 *
	 * @return int
	 */
	private function get_cache_duration() {
		/**
		 * Filter Sophi cache duration. Defaults to five minutes.
		 *
		 * @since 1.0.0
		 * @hook sophi_cache_duration
		 *
		 * @param {int} $cache_duration Cache duration in seconds. Default 300.
		 *
		 * @return {int} Cache duration in seconds.
		 */
		return apply_filters( 'sophi_cache_duration', 5 * MINUTE_IN_SECONDS );
	}
}
