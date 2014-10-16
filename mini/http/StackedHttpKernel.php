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

use mini\http\HttpKernelInterface;
use mini\http\TerminableInterface;
use mini\http\Request;
use mini\http\Response;

class StackedHttpKernel implements HttpKernelInterface, TerminableInterface
{
    private $app;
    private $middlewares = array();

    public function __construct(HttpKernelInterface $app, array $middlewares)
    {
        $this->app = $app;
        $this->middlewares = $middlewares;
    }

    public function handle(Request $request, $type = HttpKernelInterface::MASTER_REQUEST, $catch = true)
    {
        return $this->app->handle($request, $type, $catch);
    }

    public function terminate(Request $request, Response $response)
    {
        foreach ($this->middlewares as $kernel) {
            if ($kernel instanceof TerminableInterface) {
                $kernel->terminate($request, $response);
            }
        }
    }
}
