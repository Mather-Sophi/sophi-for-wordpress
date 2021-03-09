<?php

namespace SophiWP\Curator;

use function SophiWP\Settings\get_sophi_settings;

class Auth {
	private $auth_url = 'https://sophi-qa.auth0.com/oauth/token';

	public function get_access_token() {
		// todo: security concern for storing access token in database.
		$access_token = get_transient( 'sophi_curator_access_token' );

		if ( $access_token ) {
			return $access_token;
		}

		return $this->request_access_token();
	}

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

		if ( is_wp_error( $request ) || wp_remote_retrieve_response_code( $request ) != 200 ) {
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
