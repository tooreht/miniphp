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
 *
 * Basic idea of middleware in PHP.
 * Based on https://github.com/symfony/HttpKernel/blob/master/HttpKernelInterface.php
 * 
 * Inspired by: 
 * - http://stackphp.com
 * - https://github.com/symfony/HttpKernel
 * - https://igor.io/2013/02/02/http-kernel-middlewares.html
 */

namespace mini\http;
use mini\http\Request;
use mini\http\Response;
/**
 * HttpKernelInterface handles a Request to convert it to a Response.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 *
 * @api
 */
interface HttpKernelInterface
{
	const MASTER_REQUEST = 1;
	const SUB_REQUEST = 2;
	/**
	 * Handles a Request to convert it to a Response.
	 *
	 * When $catch is true, the implementation must catch all exceptions
	 * and do its best to convert them to a Response instance.
	 *
	 * @param Request $request A Request instance
	 * @param int $type The type of the request
	 * (one of HttpKernelInterface::MASTER_REQUEST or HttpKernelInterface::SUB_REQUEST)
	 * @param bool $catch Whether to catch exceptions or not
	 *
	 * @return Response A Response instance
	 *
	 * @throws \Exception When an Exception occurs during processing
	 *
	 * @api
	 */
	public function handle(Request $request, $type = self::MASTER_REQUEST, $catch = true);
}