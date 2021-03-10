<?php
/**
 * Curator services.
 *
 * @package SophiWP
 */

namespace SophiWP\Curator;

/**
 * Simple service container.
 */
class Services {
	/**
	 * Curator services.
	 *
	 * @var array $services.
	 */
	private $services;

	/**
	 * Register services needs for Curator.
	 */
	public function register() {
		$this->auth        = new Auth();
		$this->request     = new Request( $this->auth );
		$this->integration = new Integration( $this->request );
	}

	/**
	 * Get service from the container.
	 *
	 * @param string $name Service key.
	 *
	 * @return object
	 */
	public function __get( $name ) {
		return isset( $this->services[ $name ] ) ? $this->services[ $name ] : null;
	}

	/**
	 * Set a service.
	 *
	 * @param string $name Service key.
	 * @param object $service Service object to be set.
	 */
	public function __set( $name, $service ) {
		$this->services[ $name ] = $service;
	}
}
