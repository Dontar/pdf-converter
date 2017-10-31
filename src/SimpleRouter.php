<?php

class SimpleRouter {

	private $url = "";

	private $method;

	private $callbacks = array();

	private $headers;

	function __construct() {
		$this->method = $_SERVER['REQUEST_METHOD'];
		$this->url = parse_url($_SERVER['REQUEST_URI']);
		$this->headers = $this->getRequestHeaders();
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

	function getRequestHeaders() {
		$headers = array();
		foreach($_SERVER as $key => $value) {
			if (substr($key, 0, 5) <> 'HTTP_') {
				continue;
			}
			$header = str_replace(' ', '-', ucwords(str_replace('_', ' ', strtolower(substr($key, 5)))));
			$headers[$header] = $value;
		}
		if (isset($_SERVER['CONTENT_TYPE'])) {
			$headers["Content-Type"] = $_SERVER['CONTENT_TYPE'];
		}
		return $headers;
	}

	function dispatch() {
		$echoStream = function($data) {
			if (is_string($data)) {
				echo $data;
				return true;
			} else if (is_resource($data)) {
				while(!feof($data)) {
					echo fread($data, 8192);
				}
				fclose($data);
				return true;
			}
			return false;
		};

		foreach ($this->callbacks as $value) {
			list($route, $callback) = $value;
			if (strpos($this->url['path'], $route) === 0) {
				$params = isset($this->url['query'])?parse_str($this->url['query']):array();
				$result = $callback($this->method, $params, $this->headers);
				if (!$echoStream($result)) {
					if (is_array($result)) {
						list($headers, $data) = array_values($result);
						if (isset($headers)) {
							foreach ($headers as $value) {
								header($value);
							}
						}
						$echoStream($data);
					}
				}
			}
		}
	}
}