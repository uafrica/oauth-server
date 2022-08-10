<?php

use Cake\Routing\Router;
use Cake\Routing\RouteBuilder;

Router::plugin('OAuthServer', ['path' => '/oauth'], function (RouteBuilder $routes) {
    $routes->connect('/', ['controller' => 'OAuth', 'action' => 'index']);
    $routes->connect('/authorize', ['controller' => 'OAuth', 'action' => 'authorize']);
    $routes->connect('/access_token', ['controller' => 'OAuth', 'action' => 'accessToken'], ['_ext' => ['json']]);
    $routes->connect('/status', ['controller' => 'OAuth', 'action' => 'status'], ['_ext' => ['json']]);
});