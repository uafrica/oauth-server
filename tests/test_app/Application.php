<?php

namespace TestApp;

use Cake\Http\BaseApplication;
use Cake\Routing\Middleware\RoutingMiddleware;

class Application extends BaseApplication
{
    /**
     * {@inheritDoc}
     */
    public function middleware($middleware)
    {
        $middleware->add(new RoutingMiddleware($this));

        return $middleware;
    }
}
