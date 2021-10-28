<?php
/**
 * Request and manage access_token for API calls.
 *
 * @package SophiWP
 */

namespace SophiWP\SiteAutomation;

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
		$access_token = get_transient( 'sophi_site_automation_access_token' );

		if ( $access_token ) {
			return $access_token;
		}

		return $this->refresh_access_token();
	}

	/**
	 * Refresh the access_token and save it to the database.
	 *
	 * @return string|\WP_Error
	 */
	public function refresh_access_token() {
		$response = $this->request_access_token(
			get_sophi_settings( 'client_id' ),
			get_sophi_settings( 'client_secret' )
		);

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		set_transient( 'sophi_site_automation_access_token', $response['access_token'], $response['expires_in'] );

		return $response['access_token'];
	}

	/**
	 * Request a new access token.
	 *
	 * @param string $client_id Client ID.
	 * @param string $client_secret Client Secret.
	 *
	 * @return array|\WP_Error
	 */
	public function request_access_token( $client_id, $client_secret ) {
		$body    = [
			'client_id'     => $client_id,
			'client_secret' => $client_secret,
			'audience'      => 'https://site-automation-api.ml.sophi.io',
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

		if ( 401 === wp_remote_retrieve_response_code( $request ) ) {
			return new \WP_Error( 401, __( 'Invalid credentials! Please confirm your client ID and secret then try again.', 'sophi-wp' ) );
		}

		if ( 200 !== wp_remote_retrieve_response_code( $request ) ) {
			return new \WP_Error( $request['response']['code'], $request['response']['message'] );
		}

		$response = wp_remote_retrieve_body( $request );
		$response = json_decode( $response, true );

		return $response;
	}
}
