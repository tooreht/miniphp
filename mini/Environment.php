<?php

/**
 * Mini - a micro PHP 5 framework
 *
 * @author tooreht <tooreht@gmail.com>
 * @copyright 2014 tooreht
 * @link http://www.miniframework.com
 * @license http://www.miniframework.com/license
 * @version 0.0.1
 * @package Mini
 */

namespace mini;

use mini\utils\Container;

class Environment
{
	protected $variables;

	protected static $instance;

	public static function getInstance($refresh=false)
	{
		if (is_null(self::$instance) || $refresh)
		{
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __clone() {}

	private function __construct()
	{
		$this->variables = new Container();
		$this->variables->set('REQUEST_METHOD', $_SERVER['REQUEST_METHOD']);
		$this->variables->set('REMOTE_ADDR', $_SERVER['REMOTE_ADDR']);
		$this->variables->set('SCRIPT_NAME', $_SERVER['SCRIPT_NAME']);
		$this->variables->set('REQUEST_URI', $_SERVER['REQUEST_URI']);
		$this->variables->set('QUERY_STRING', $_SERVER['QUERY_STRING']);
		$this->variables->set('SERVER_NAME', $_SERVER['SERVER_NAME']);
		$this->variables->set('SERVER_PORT', $_SERVER['SERVER_PORT']);

		// Is the application running under HTTPS or HTTP protocol?
		$this->variables->set('URL_SCHEME', empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] === 'off' ? 'http' : 'https');

		$this->variables->set('PATH_INFO', str_replace("index.php", "", $_SERVER['SCRIPT_NAME'], $count));

		$requestUri = $_SERVER['REQUEST_URI'];
		// append trailing slash if not present
		if (substr($requestUri, -1) !== '/')
		{
			$requestUri .= '/';
		}
		$this->variables->set('PATH', str_replace($this->variables->get('PATH_INFO'), "/", $requestUri, $count));
	}

	public function __get($name)
	{
		return $this->variables[$name];
	}

	public function __isset($name)
	{
		return isset($this->variables[$name]);
	}
}