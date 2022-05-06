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
	 * Class constructor.
	 */
	public function __construct() {
		$this->environment = get_sophi_settings( 'environment' );
	}

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
		$body = [
			'client_id'     => $client_id,
			'client_secret' => $client_secret,
			'audience'      => $this->get_audience(),
			'grant_type'    => 'client_credentials',
		];
		$args = [
			'headers' => [ 'Content-Type' => 'application/json' ],
			'body'    => wp_json_encode( $body ),
		];

		$auth_url = $this->get_auth_url();

		/** This filter is documented in includes/classes/SiteAutomation/Request.php */
		$args = apply_filters( 'sophi_request_args', $args, $auth_url );

		$request = wp_remote_post( $auth_url, $args );

		/** This filter is documented in includes/classes/SiteAutomation/Request.php */
		$request = apply_filters( 'sophi_request_result', $request, $args, $auth_url );

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

	/**
	 * Set the environment to be used.
	 *
	 * @since 1.0.8
	 * @param string $environment Could be 'prod', 'stg' or 'dev'
	 * @return void
	 */
	public function set_environment( $environment ) {
		$this->environment = $environment;
	}

	/**
	 * Get the API URL to get access_token
	 *
	 * @since 1.0.8
	 * @return string
	 */
	protected function get_auth_url() {
		return 'prod' === $this->environment ? 'https://sophi-prod.auth0.com/oauth/token' : 'https://sophi-works.auth0.com/oauth/token';
	}

	/**
	 * Get the audience parameter to get access_token
	 *
	 * @since 1.0.8
	 * @return string
	 */
	protected function get_audience() {
		return 'prod' === $this->environment ? 'https://api.sophi.io' : 'https://api.sophi.works';
	}
}
