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

namespace mini\http;

class Request
{
	const METHOD_HEAD = 'HEAD';
	const METHOD_GET = 'GET';
	const METHOD_POST = 'POST';
	const METHOD_PUT = 'PUT';
	const METHOD_PATCH = 'PATCH';
	const METHOD_DELETE = 'DELETE';
	const METHOD_OPTIONS = 'OPTIONS';
	const METHOD_OVERRIDE = '_METHOD';

	public function __construct(\mini\Environment $env)
	{
		$this->env = $env;
		// ... 
	}

	public function method()
	{
		return $this->env->get('REQUEST_METHOD');
	}

	public static function methods()
	{
		return array(
			self::METHOD_HEAD,
			self::METHOD_GET,
			self::METHOD_POST,
			self::METHOD_PUT,
			self::METHOD_PATCH,
			self::METHOD_DELETE,
			// self::METHOD_OPTIONS,
			// self::METHOD_OVERRIDE
		);
	}

	public function resourceUri()
	{
		return $this->env->get('PATH');
	}
}