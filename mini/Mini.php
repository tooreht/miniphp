<?php

/**
* Mini - a micro PHP 5 framework
*
* @author tooreht <tooreht@gmail.com>
* @copyright 2014 tooreht
* @link https://github.com/tooreht/miniphp
* @license https://github.com/tooreht/miniphp/license
* @version 0.0.1
* @package Mini
*/

namespace mini;

class Mini
{
	public $container;

	public function __construct()
	{
		$this->container = new \mini\utils\Container();
		
		// environment
		$this->container->singleton('environment', function ($c) {
			return \mini\Environment::getInstance();
		});

		// request
		$this->container->singleton('request', function ($c) {
			return new \mini\http\Request($c['environment']);
		});

		// response
		$this->container->singleton('response', function ($c) {
			return new \mini\http\Response($c['environment']);
		});

		// router
		$this->container->singleton('router', function ($c) {
			return new \mini\Router();
		});
	}

	/**
	* Mini PSR-0 autoloader
	*/
	public static function autoload($className)
	{
		$root = dirname(dirname(__FILE__));

		$fileName = $root . DIRECTORY_SEPARATOR . str_replace('\\', DIRECTORY_SEPARATOR, $className) . '.php';

		if (file_exists($fileName)) {
			require_once $fileName;
		}
	}

	/**
	* Register Mini's PSR-0 autoloader
	*/
	public static function registerAutoloader()
	{
		spl_autoload_register(__NAMESPACE__ . "\\Mini::autoload");
	}

	public function addMiddleware($middleware = array())
	{
		foreach ($middleware as $mw)
		{
			$this->container->middleware[] = $mw;
		}
	}

	public function map($args)
	{
		$pattern = array_shift($args);
		$callable = array_pop($args);

		$route = new \mini\Route($pattern, $callable);

		$this->container->router->map($route);

		$this->addMiddleware($args);

		return $route;
	}

	/**
	 * GET request
	 */
	public function get()
	{
		$args = func_get_args();
		return $this->map($args)->via(\mini\http\Request::METHOD_GET, \mini\http\Request::METHOD_HEAD);
	}

	/**
	 * POST request
	 */
	public function post()
	{
		$args = func_get_args();
		return $this->map($args)->via(\mini\http\Request::METHOD_POST);
	}

	/**
	 * PUT request
	 */
	public function put()
	{
		$args = func_get_args();
		return $this->map($args)->via(\mini\http\Request::METHOD_PUT);
	}

	/**
	 * PATCH request
	 */
	public function patch()
	{
		$args = func_get_args();
		return $this->map($args)->via(\mini\http\Request::METHOD_PATCH);
	}

	/**
	 * DELETE request
	 */
	public function delete()
	{
		$args = func_get_args();
		return $this->map($args)->via(\mini\http\Request::METHOD_DELETE);
	}

	/**
	 * Run app
	 */
	public function run()
	{
		$method = $this->container->request->method();
		$resourceUri = $this->container->request->resourceUri();
		$routes = $this->container->router->matchRoutes($method, $resourceUri);

		ob_start();
		if (empty($routes))
		{
			$this->container->response->notFound();
		}
		else
		{
			foreach ($routes as $route) {
				$route->dispatch();
			}
		}
		ob_end_flush();
	}
}