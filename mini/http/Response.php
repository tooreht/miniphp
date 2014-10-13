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

class Response
{
	public function __construct(\mini\Environment $env)
	{
		$this->env = $env;
		// ... 
	}

	public function notFound()
	{
		header("HTTP/1.1 404 Not Found");
	}
}