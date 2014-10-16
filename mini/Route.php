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

use mini\http\StackBuilder;
use mini\utils\Container;

class Route
{
	/**
	 * @var \mini\utils\Container
	 */
	public $container;
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

	public function __construct($pattern, $callable)
	{
		$this->pattern = $pattern;
		$this->setCallable($callable);

		$this->container = new Container();

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

	public function via() {

		$args = func_get_args();
		$this->methods = array_merge($this->methods, $args);

		return $this;
	}

	public function getMethods()
	{
		return $this->methods;
	}

	public function getPattern()
	{
		return $this->pattern;
	}

	public function getParams()
	{
		return $this->params;
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

	public function conditions($conditions = array())
	{
		$this->conditions = $conditions;
	}

	public function supportsHttpMethod($method) {
		return in_array($method, $this->methods);
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
}
