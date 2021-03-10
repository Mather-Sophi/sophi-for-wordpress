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
	private $auth_url = 'https://sophi-qa.auth0.com/oauth/token';

	/**
	 * Get cached access_token.
	 *
	 * @return string
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
	 * @return string
	 */
	private function request_access_token() {
		$request = wp_remote_post(
			$this->auth_url,
			[
				'headers' => [ 'Content-Type' => 'application/json' ],
				'bopy'    => [
					'client_id'     => get_sophi_settings( 'sophi_client_id' ),
					'client_secret' => get_sophi_settings( 'sophi_client_secret' ),
					'audience'      => 'https://curator.sophi.works.qa/',
					'grant_type'    => 'client_credentials',
				],
			]
		);

		// todo: error handling for failed cases
		if ( is_wp_error( $request ) || 200 !== wp_remote_retrieve_response_code( $request ) ) {
			error_log( print_r( $request, true ) );
			return false;
		}

		$response = wp_remote_retrieve_body( $request );

		if ( empty( $response['access_token'] ) ) {
			error_log( 'Failed to request access token: ' . print_r( $response, true ) );
			return false;
		}

		set_transient( 'sophi_curator_access_token', $response['access_token'], $response['expires_in'] );

		return $response['access_token'];
	}
}
