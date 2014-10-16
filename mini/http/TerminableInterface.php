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
 * Based on https://github.com/symfony/HttpKernel/blob/master/TerminableInterface.php
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
 * Terminable extends the Kernel request/response cycle with dispatching a post
 * response event after sending the response and before shutting down the kernel.
 *
 * @author Jordi Boggiano <j.boggiano@seld.be>
 * @author Pierre Minnieur <pierre.minnieur@sensiolabs.de>
 *
 * @api
 */
interface TerminableInterface
{
    /**
     * Terminates a request/response cycle.
     *
     * Should be called after sending the response and before shutting down the kernel.
     *
     * @param Request  $request  A Request instance
     * @param Response $response A Response instance
     *
     * @api
     */
    public function terminate(Request $request, Response $response);
}
