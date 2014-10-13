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

class Route
{
	protected $methods = array();
	protected $pattern;
	protected $callable;
	protected $params = array();

	/**
	* @var array value array of URL parameter names
	*/
	protected $paramNames = array();

	/**
	* @var array key array of URL parameter names with + at the end
	*/
	protected $paramNamesPath = array();
	protected $name;
	protected $conditions = array();
	protected $caseSensitive = false;
	protected $middleware = array();

	public function __construct($pattern, $callable)
	{
		$this->pattern = $pattern;
		$this->setCallable($callable);
	}

	public function via() {

		$args = func_get_args();
		$this->methods = array_merge($this->methods, $args);

		return $this;
	}

	public function methods()
	{
		return $this->methods;
	}

	public function pattern()
	{
		return $this->pattern;
	}

	public function getCallable()
	{
		return $this->callable;
	}

	/**
	* Set route callable
	* @param mixed $callable
	* @throws \InvalidArgumentException If argument is not callable
	*/
	public function setCallable($callable)
	{
		$matches = array();
		if (is_string($callable) && preg_match('!^([^\:]+)\:([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)$!', $callable, $matches)) {
			$class = $matches[1];
			$method = $matches[2];
			$callable = function() use ($class, $method) {
				static $obj = null;
				if ($obj === null) {
					$obj = new $class;
				}
				return call_user_func_array(array($obj, $method), func_get_args());
			};
		}
		if (!is_callable($callable)) {
			throw new \InvalidArgumentException('Route callable must be callable');
		}
		$this->callable = $callable;
	}

	public function supportsHttpMethod($method) {
		return in_array($method, $this->methods);
	}

	public function conditions($conditions = array())
	{
		$this->conditions = $conditions;
	}

	public function matches($resourceUri)
	{
		// var_dump($this->pattern);
		// var_dump($resourceUri);

		// Convert URL params into regex patterns, construct a regex for this route, init params
		$patternAsRegex = preg_replace_callback(
			'#:([\w]+)\+?#',
			array($this, 'matchesCallback'),
			str_replace(')', ')?', (string)$this->pattern)
		);
		$regex = '#^' . $patternAsRegex . '/$#';
		if ($this->caseSensitive === false) {
			$regex .= 'i';
		}
		// Cache URL params' names and values if this route matches the current HTTP request
		if (!preg_match($regex, $resourceUri, $paramValues)) {
			// echo 'failed' . $regex;
			return false;
		}

		// echo 'success' . $regex;
		// echo $regex;
		// var_dump($paramValues);
		// var_dump($this->paramNames);
		// var_dump($this->paramNamesPath);

		foreach ($this->paramNames as $paramName)
		{
			if (isset($paramValues[$paramName]))
			{
				if (isset($this->paramNamesPath[$paramName]))
				{
					$this->params[$paramName] = explode('/', urldecode($paramValues[$paramName]));
				}
				else
				{
					$this->params[$paramName] = urldecode($paramValues[$paramName]);
				}
			}
		}

		// echo '***';
		// var_dump($this->params);
		// echo '***';

		return true;
	}

	public function matchesCallback($match)
	{
		// var_dump($match);

		$this->paramNames[] = $match[1];

		if (isset($this->conditions[$match[1]]))
		{
			return '(?P<' . $match[1] . '>'. $this->conditions[$match[1]] . ')';
		}

		if (substr($match[0], -1) === '+') {
			$this->paramNamesPath[$match[1]] = 1;
			return '(?P<' . $match[1] . '>.+)';
		}

		return '(?P<' . $match[1] . '>[^/]+)';
	}

	public function dispatch()
	{
		foreach ($this->middleware as $mw) {
			call_user_func_array($mw, array($this));
		}

		$return = call_user_func_array($this->getCallable(), array_values($this->params));
		return !($return === false);
	}

	/**
	* Get middleware
	* @return array[Callable]
	*/
	public function getMiddleware()
	{
		return $this->middleware;
	}

	/**
	* Set middleware
	*
	* This method allows middleware to be assigned to a specific Route.
	* If the method argument `is_callable` (including callable arrays!),
	* we directly append the argument to `$this->middleware`. Else, we
	* assume the argument is an array of callables and merge the array
	* with `$this->middleware`. Each middleware is checked for is_callable()
	* and an InvalidArgumentException is thrown immediately if it isn't.
	*
	* @param Callable|array[Callable]
	* @return \Slim\Route
	* @throws \InvalidArgumentException If argument is not callable or not an array of callables.
	*/
	public function setMiddleware($middleware)
	{
		if (is_callable($middleware)) {
			$this->middleware[] = $middleware;
		} else if (is_array($middleware)) {
			foreach ($middleware as $callable) {
				if (!is_callable($callable)) {
					throw new \InvalidArgumentException('All Route middleware must be callable');
				}
			}
			$this->middleware = array_merge($this->middleware, $middleware);
		} else {
			throw new \InvalidArgumentException('Route middleware must be callable or an array of callables');
		}
		return $this;
	}
}