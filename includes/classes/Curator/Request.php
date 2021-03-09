<?php

namespace SophiWP\Curator;

use function SophiWP\Settings\get_sophi_settings;

class Request {
	private $auth;

	private $api_url;

	public function __construct( $auth ) {
		$this->auth    = $auth;
		$this->api_url = $this->prepare_api_url();
	}

	public function get( $page, $section ) {
		$request = wp_remote_get(
			$this->api_url,
			[
				'headers' => [
					'Content-Type'  => 'application/json',
					'Authorization' => 'Bearer ' . $this->auth->get_access_token(),
				],
				'bopy'    => [
					'page'   => $page,
					'widget' => $section,
				],
			]
		);

		if ( is_wp_error( $request ) || wp_remote_retrieve_response_code( $request ) != 200 ) {
			error_log( print_r( $request, true ) );
			return false;
		}

		return wp_remote_retrieve_body( $request );
	}

	private function prepare_api_url() {
		$curator_url = get_sophi_settings( 'sophi_curator_url' );
		$curator_url = untrailingslashit( $curator_url );

		return sprintf( '%1$s/curator', $curator_url );
	}
}
