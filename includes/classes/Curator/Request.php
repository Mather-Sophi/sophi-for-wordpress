<?php

namespace SophiWP\Curator;

use function SophiWP\Settings\get_sophi_settings;

class Request {
	private $auth;

	private $api_url;

	private $page;

	private $section;

	public function __construct( $auth ) {
		$this->auth    = $auth;
		$this->api_url = $this->prepare_api_url();
	}

	public function get( $page, $section ) {
		$this->page    = $page;
		$this->section = $page;

		$curator_status = $this->get_status();
		$curator_data   = get_option( "sophi_curator_data_{$page}_{$section}" );

		if ( $curator_status['success'] && $curator_data ) {
			return $curator_data;
		}

		$response = $this->request();

		if ( $response ) {
			$this->set_status( [ 'success' => true ] );
			return $this->process( $response );
		}

		$this->set_status( [ 'success' => false ] );
		$this->process( $response );
		$this->retry();

		if ( $curator_data ) {
			return $curator_data;
		}

		return [];
	}

	private function retry() {

	}

	private function get_status() {
		return get_transient( "sophi_curator_status_{$this->page}_{$this->section}" );
	}

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
			$prev_status   = $this->get_status();
			$data['retry'] = $prev_status['retry'] + 1;
		}

		set_transient( "sophi_curator_status_{$this->page}_{$this->section}", $data, $this->get_cache_duration() );
	}

	private function request() {
		$request = wp_remote_get(
			$this->api_url,
			[
				'headers' => [
					'Content-Type'  => 'application/json',
					'Authorization' => 'Bearer ' . $this->auth->get_access_token(),
				],
				'bopy'    => [
					'page'   => $this->page,
					'widget' => $this->section,
				],
			]
		);

		if ( is_wp_error( $request ) || wp_remote_retrieve_response_code( $request ) != 200 ) {
			error_log( print_r( $request, true ) );
			// todo: return error message here for logging.
			return false;
		}

		return wp_remote_retrieve_body( $request );
	}

	private function process( $response ) {
		if ( ! $response ) {
			return [];
		}

		// todo: get id from response.

		update_option( "sophi_curator_data_{$this->page}_{$this->section}", $response );
		return $response;
	}

	private function prepare_api_url() {
		$curator_url = get_sophi_settings( 'sophi_curator_url' );
		$curator_url = untrailingslashit( $curator_url );

		return sprintf( '%1$s/curator', $curator_url );
	}

	private function get_cache_duration() {
		return apply_filters( 'sophi_cache_duration', 5 * MINUTE_IN_SECONDS );
	}
}
