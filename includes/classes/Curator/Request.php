<?php
/**
 * Request curated content
 *
 * @package SophiWP
 */

namespace SophiWP\Curator;

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
	 * Curator API URL.
	 *
	 * @var string $api_url
	 */
	private $api_url;

	/**
	 * Page name for curator request.
	 *
	 * @var string $page
	 */
	private $page;

	/**
	 * Section name for curator request.
	 *
	 * @var string $section
	 */
	private $section;

	/**
	 * Status of latest curator request.
	 *
	 * @var array $status
	 */
	private $status;

	/**
	 * Class constructor.
	 *
	 * @param Auth $auth Authentication object.
	 */
	public function __construct( $auth ) {
		$this->auth    = $auth;
		$this->api_url = $this->prepare_api_url();
	}

	/**
	 * Get (cached) curated posts.
	 *
	 * @param string $page Page name.
	 * @param string $section Section name.
	 *
	 * @return array|null
	 */
	public function get( $page, $section ) {
		$this->page    = $page;
		$this->section = $section;

		$this->status = $this->get_status();
		$curator_data = get_option( "sophi_curator_data_{$page}_{$section}" );

		if ( ! empty( $this->status['success'] ) && $curator_data ) {
			return $curator_data;
		}

		$response = $this->request();

		if ( is_wp_error( $response ) ) {
			error_log( print_r( $response, true ) );

			$this->set_status(
				[
					'success' => false,
					'message' => $response->get_error_message(),
				]
			);

			$this->retry();

			if ( $curator_data ) {
				return $curator_data;
			} else {
				return [];
			}
		}

		$this->set_status( [ 'success' => true ] );
		return $this->process( $response );
	}

	/**
	 * Retry getting curated data if error occurred.
	 */
	private function retry() {
		// todo check if we need refresh access_token

		add_action(
			'sophi_retry_get_curated_data',
			[ $this, 'do_cron' ],
			10,
			2
		);

		$retry_time = $this->status['retry'] < 20 ? 5 * MINUTE_IN_SECONDS : HOUR_IN_SECONDS;

		wp_schedule_single_event( time() + $retry_time, 'sophi_retry_get_curated_data', [ $this->page, $this->section ] );
	}

	/**
	 * Cron call back.
	 *
	 * @param string $page Page name.
	 * @param string $section Section name.
	 */
	public function do_cron( $page, $section ) {
		$this->get( $page, $section );
	}

	/**
	 * Get request status from database.
	 *
	 * @return array
	 */
	private function get_status() {
		return get_transient( "sophi_curator_status_{$this->page}_{$this->section}" );
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
		set_transient( "sophi_curator_status_{$this->page}_{$this->section}", $data, $this->get_cache_duration() );
	}

	/**
	 * Get curated data from Sophi Curator API.
	 *
	 * return
	 */
	private function request() {
		$access_token = $this->auth->get_access_token();

		if ( is_wp_error( $access_token ) ) {
			return $access_token;
		}

		$args = [
			'headers' => [
				'Content-Type'  => 'application/json',
				'Authorization' => 'Bearer ' . $access_token,
			],
			'body'    => [
				'page'   => $this->page,
				'widget' => $this->section,
			],
		];

		if ( function_exists( 'vip_safe_wp_remote_get' ) ) {
			$request = vip_safe_wp_remote_get( $this->api_url, false, 3, 1, 20, $args );
		} else {
			$request = wp_remote_get( $this->api_url, $args );
		}

		if ( is_wp_error( $request ) ) {
			return $request;
		}

		if ( wp_remote_retrieve_response_code( $request ) != 200 ) {
			error_log( print_r( $request, true ) );
			return new \WP_Error( $request['response']['code'], $request['response']['message'] );
		}

		return json_decode( wp_remote_retrieve_body( $request ), true );
	}

	/**
	 * Process response from Sophi.
	 *
	 * @param array $response Response of Curator API.
	 *
	 * @return array
	 */
	private function process( $response ) {
		if ( ! $response ) {
			return [];
		}

		// todo: get id from response.

		update_option( "sophi_curator_data_{$this->page}_{$this->section}", $response );
		return $response;
	}

	/**
	 * Prepare curator API URL
	 *
	 * @return string
	 */
	private function prepare_api_url() {
		$curator_url = get_sophi_settings( 'sophi_curator_url' );
		$curator_url = untrailingslashit( $curator_url );

		return sprintf( '%1$s/curator', $curator_url );
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
