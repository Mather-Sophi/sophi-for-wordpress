<?php

namespace SophiWP\Curator;

class Services {
	private $services;

	public function register() {
		$this->auth        = new Auth();
		$this->request     = new Request( $this->auth );
		$this->integration = new Integration( $this->request );
	}

	public function __get( $name ) {
		return isset( $this->services[ $name ] ) ? $this->services[ $name ] : null;
	}

	public function __set( $name, $service ) {
		$this->services[ $name ] = $service;
	}
}
