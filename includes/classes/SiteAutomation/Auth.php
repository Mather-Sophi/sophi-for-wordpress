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
	 * Encryption Key.
	 */
	private $key;

	/**
	 * Encryption Salt.
	 */
	private $salt;

	/**
	 * Class constructor.
	 */
	public function __construct() {
		$this->environment = get_sophi_settings( 'environment' );

		// Get the required values.
		$this->key  = $this->get_default_key();
		$this->salt = $this->get_default_salt();
	}

	/**
	 * Get the Logged-in key for encryption.
	 * 
	 * @since 1.3.0
	 * 
	 * @return string Logged-in key or a predefined one.
	 */
	private function get_default_key() {
		if ( '' !== wp_salt( 'logged_in' ) ) {
			return wp_salt( 'logged_in' ) ;
		}

		// If this is reached, you're either not on a live site or have a serious security issue.
		return 'das-ist-kein-geheimer-schluessel';
	}

	/**
	 * Get the SALT key for encryption.
	 * 
	 * @since 1.3.0
	 * 
	 * @return string SALT key or a predefined one.
	 */
	private function get_default_salt() {
		if ( '' !== LOGGED_IN_SALT ) {
			return LOGGED_IN_SALT;
		}

		// If this is reached, you're either not on a live site or have a serious security issue.
		return 'das-ist-kein-geheimes-salz';
	}

	/**
	 * The encryption method.
	 * 
	 * @since 1.3.0
	 * 
	 * @param string $value the value to be encrypted.
	 * 
	 * @return string the encrypted value OR false on failure.
	 */
	public function encrypt( $value ) {
		if ( ! extension_loaded( 'openssl' ) ) {
			return $value;
		}

		$method = 'aes-256-ctr';
		$ivlen  = openssl_cipher_iv_length( $method );
		$iv     = openssl_random_pseudo_bytes( $ivlen );

		$raw_value = openssl_encrypt( $value . $this->salt, $method, $this->key, 0, $iv );
		if ( ! $raw_value ) {
			return false;
		}

		return base64_encode( $iv . $raw_value ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode
	}

	/**
	 * The decryption method.
	 * 
	 * @since 1.3.0
	 * 
	 * @param string $value the value to be decrypted.
	 * 
	 * @return string the decrypted value OR false on failure.
	 */
	public function decrypt( $raw_value ) {
		if ( ! extension_loaded( 'openssl' ) ) {
			return $raw_value;
		}

		$raw_value = base64_decode( $raw_value, true ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_decode

		$method = 'aes-256-ctr';
		$ivlen  = openssl_cipher_iv_length( $method );
		$iv     = substr( $raw_value, 0, $ivlen );

		$raw_value = substr( $raw_value, $ivlen );

		$value = openssl_decrypt( $raw_value, $method, $this->key, 0, $iv );
		if ( ! $value || substr( $value, - strlen( $this->salt ) ) !== $this->salt ) {
			return false;
		}

		return substr( $value, 0, - strlen( $this->salt ) );
	}

	/**
	 * Get cached access_token.
	 *
	 * @return string|\WP_Error
	 */
	public function get_access_token() {
		$access_token = $this->decrypt( get_transient( 'sophi_site_automation_access_token' ) );

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
		$response = $this->request_access_token();

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		set_transient( 'sophi_site_automation_access_token', $this->encrypt( $response['access_token'] ), $response['expires_in'] );

		return $response['access_token'];
	}

	/**
	 * Request a new access token.
	 *
	 * @return array|\WP_Error
	 */
	public function request_access_token() {
		$client_id     = get_sophi_settings( 'sophi_override_client_id' );
		$client_secret = get_sophi_settings( 'sophi_override_client_secret' );

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

		$result = wp_remote_post( $auth_url, $args );

		/** This filter is documented in includes/classes/SiteAutomation/Request.php */
		$result = apply_filters( 'sophi_request_result', $result, $args, $auth_url );

		if ( is_wp_error( $result ) ) {
			return $result;
		}

		if ( 401 === wp_remote_retrieve_response_code( $result ) ) {
			return new \WP_Error( 401, __( 'Invalid credentials! Please confirm your client ID and secret then try again.', 'sophi-wp' ) );
		}

		if ( 200 !== wp_remote_retrieve_response_code( $result ) ) {
			return new \WP_Error( $result['response']['code'], $result['response']['message'] );
		}

		$response = wp_remote_retrieve_body( $result );

		return json_decode( $response, true );
	}

	/**
	 * Set the environment to be used.
	 *
	 * @since 1.3.0
	 * @param string $environment Could be 'prod', 'stg' or 'dev'.
	 * @return void
	 */
	public function set_environment( $environment ) {
		$this->environment = $environment;
	}

	/**
	 * Get the API URL to get access_token
	 *
	 * @since 1.3.0
	 * @return string
	 */
	protected function get_auth_url() {
		return 'prod' === $this->environment ? 'https://sophi-prod.auth0.com/oauth/token' : 'https://sophi-works.auth0.com/oauth/token';
	}

	/**
	 * Get the audience parameter to get access_token
	 *
	 * @since 1.3.0
	 * @return string
	 */
	protected function get_audience() {
		return 'prod' === $this->environment ? 'https://api.sophi.io' : 'https://api.sophi.works';
	}
}
