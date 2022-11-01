<?php

namespace Liste_de_course\Login\Google;

/**
 * Class REST_API
 *
 * @package Vivant
 */
class REST_API {

	/**
	 * Version of the REST API.
	 */
	protected $version = 'v1';

	/**
	 * @var string
	 */
	protected $path = '';

	/**
	 * @var array
	 */
	protected $routes = array();

	/**
	 * @return array
	 */
	public function get_routes() {
		return $this->routes;
	}

	/**
	 * @param $route
	 * @param $config
	 */
	public function add_route( $route, $config ) {
		$this->routes[] = array(
			'route' => $this->version . '/' . $this->path . '/' . $route,
			'config' => $config
		);
	}
}