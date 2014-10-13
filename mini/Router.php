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

class Router
{
	protected $routes = array();

	public function __construct() {}

	public function map(\mini\Route $route)
	{
		$this->routes[] = $route;
	}

	public function matchRoutes($method, $resourceUri)
	{
		$matches = [];

		foreach ($this->routes as $route)
			if ($route->supportsHttpMethod($method) && $route->matches($resourceUri))
			{
				$matches[] = $route;
			}
		return $matches;
	}

}