<?php

namespace App;

use Cake\Error\Middleware\ErrorHandlerMiddleware;
use Cake\Http\BaseApplication;
use Cake\Routing\Middleware\RoutingMiddleware;

/**
 * @inheritDoc
 */
class Application extends BaseApplication
{
    /**
     * @inheritDoc
     */
    public function middleware($middlewareQueue)
    {
        return $middlewareQueue
            ->add(new ErrorHandlerMiddleware())
            ->add(new RoutingMiddleware($this));
    }
}
