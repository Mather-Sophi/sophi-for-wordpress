<?php
/**
 * Site Automation services.
 *
 * @package SophiWP
 */

namespace SophiWP\SiteAutomation;

/**
 * Simple service container.
 */
class Services {
	/**
	 * Site Automation services.
	 *
	 * @var array $services.
	 */
	private $services;

	/**
	 * Register services needs for Site Automation.
	 */
	public function register() {
		$this->request     = new Request();
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
