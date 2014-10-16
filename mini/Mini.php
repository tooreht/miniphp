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

use mini\Environment;
use mini\Route;
use mini\http\HttpKernelInterface;
use mini\http\Response;
use mini\http\Request;
use mini\http\StackBuilder;
use mini\utils\Container;

class Mini implements HttpKernelInterface
{
	/**
	 * @var \mini\utils\Container
	 */
	public $container;

	public function __construct()
	{
		$this->container = new Container();
		
		// environment
		$this->container->singleton('environment', function ($c) {
			return Environment::getInstance();
		});

		// request
		$this->container->singleton('request', function ($c) {
			return new Request($c['environment']);
		});

		// response
		$this->container->singleton('response', function ($c) {
			return new Response($c['environment']);
		});

		// router
		$this->container->singleton('router', function ($c) {
			return new Router();
		});

		// middleware
		$this->container->singleton('middleware', function ($c) {
			return new StackBuilder();
		});
	}

	public function __get($name)
	{
		return $this->container[$name];
	}

	public function __set($name, $value)
	{
		$this->container[$name] = $value;
	}

	public function __isset($name)
	{
		return isset($this->container[$name]);
	}

	public function __unset($name)
	{
		unset($this->container[$name]);
	}

	public function map($args)
	{
		$pattern = array_shift($args);
		$callable = array_pop($args);

		$route = new Route($pattern, $callable);

		$this->router->map($route);

		foreach ($args as $md) {
			$route->middleware->push($md);
		}

		return $route;
	}

	/**
	 * GET request
	 */
	public function get()
	{
        if (func_num_args() < 2) {
            throw new \InvalidArgumentException("Missing argument(s) when calling get");
        }

		$args = func_get_args();
		return $this->map($args)->via(Request::METHOD_GET, Request::METHOD_HEAD);
	}

	/**
	 * POST request
	 */
	public function post()
	{
        if (func_num_args() < 2) {
            throw new \InvalidArgumentException("Missing argument(s) when calling post");
        }

		$args = func_get_args();
		return $this->map($args)->via(Request::METHOD_POST);
	}

	/**
	 * PUT request
	 */
	public function put()
	{
        if (func_num_args() < 2) {
            throw new \InvalidArgumentException("Missing argument(s) when calling put");
        }

		$args = func_get_args();
		return $this->map($args)->via(Request::METHOD_PUT);
	}

	/**
	 * PATCH request
	 */
	public function patch()
	{
        if (func_num_args() < 2) {
            throw new \InvalidArgumentException("Missing argument(s) when calling patch");
        }

		$args = func_get_args();
		return $this->map($args)->via(Request::METHOD_PATCH);
	}

	/**
	 * DELETE request
	 */
	public function delete()
	{
        if (func_num_args() < 2) {
            throw new \InvalidArgumentException("Missing argument(s) when calling delete");
        }

		$args = func_get_args();
		return $this->map($args)->via(Request::METHOD_DELETE);
	}

	/**
	 * Run app
	 */
	public function run()
	{
		$app = $this->middleware->resolve($this);
		$app->handle($this->request);
	}

    public function handle(Request $request, $type = HttpKernelInterface::MASTER_REQUEST, $catch = true)
    {
		$method = $request->getMethod();
		$resourceUri = $request->getResourceUri();
		$routes = $this->router->matchRoutes($method, $resourceUri);
		
		ob_start();
		if ($routes) {
			foreach ($routes as $route) {
				echo call_user_func_array($route->getCallable(), array_values($route->getParams()));
			}			
		}
		else
		{
			$this->response->notFound();
		}
		ob_end_flush();
    }
}
