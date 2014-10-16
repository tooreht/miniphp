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

namespace mini\http;

use mini\Environment;

class Response
{
	public function __construct(Environment $env)
	{
		$this->env = $env;
		// ... 
	}

	public function notFound()
	{
		header("HTTP/1.1 404 Not Found");
	}
}