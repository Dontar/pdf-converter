<?php

class SimpleRouter {

	private $url = "";

	private $method;

	private $callbacks = array();

	function __construct() {
		$this->method = $_SERVER['REQUEST_METHOD'];
		$this->url = parse_url($_SERVER['REQUEST_URI']);
	}

	/**
	 * Undocumented function
	 *
	 * @param string $route
	 * @param callable $callback
	 * @return void
	 */
	function on($route, $callback) {
		$this->callbacks[] = array($route, $callback);
	}

	function dispatch() {
		foreach ($this->callbacks as $value) {
			list($route, $callback) = $value;
			if (strpos($this->url['path'], $route) === 0) {
				$params = isset($this->url['query'])?parse_str($this->url['query']):array();
				$result = $callback($this->method, $params);
				if (is_string($result)) {
					echo $result;
				}
			}
		}
	}

}