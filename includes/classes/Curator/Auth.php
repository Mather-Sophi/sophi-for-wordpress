<?php
/**
 * Request and manage access_token for API calls.
 *
 * @package SophiWP
 */

namespace SophiWP\Curator;

use function SophiWP\Settings\get_sophi_settings;

/**
 * Class: Auth
 */
class Auth {
	/**
	 * API URL to get access_token.
	 *
	 * @var string $auth_url
	 */
	private $auth_url = 'https://login.sophi.io/oauth/token';

	/**
	 * Get cached access_token.
	 *
	 * @return string|\WP_Error
	 */
	public function get_access_token() {
		$access_token = get_transient( 'sophi_curator_access_token' );

		if ( $access_token ) {
			return $access_token;
		}

		return $this->request_access_token();
	}

	/**
	 * Request a new access_token.
	 *
	 * @return string|\WP_Error
	 */
	private function request_access_token() {
		$body    = [
			'client_id'     => get_sophi_settings( 'sophi_client_id' ),
			'client_secret' => get_sophi_settings( 'sophi_client_secret' ),
			'audience'      => 'https://curator-api.sophi.io',
			'grant_type'    => 'client_credentials',
		];
		$request = wp_remote_post(
			$this->auth_url,
			[
				'headers' => [ 'Content-Type' => 'application/json' ],
				'body'    => wp_json_encode( $body ),
			]
		);

		if ( is_wp_error( $request ) ) {
			return $request;
		}

		if ( 200 !== wp_remote_retrieve_response_code( $request ) ) {
			return new \WP_Error( $request['response']['code'], $request['response']['message'] );
		}

		$response = wp_remote_retrieve_body( $request );
		$response = json_decode( $response, true );

		set_transient( 'sophi_curator_access_token', $response['access_token'], $response['expires_in'] );

		return $response['access_token'];
	}
}
