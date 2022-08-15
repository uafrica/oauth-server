<?php

use Cake\Routing\Router;
use Cake\Routing\RouteBuilder;


Router::plugin('OAuthServer', ['path' => '/users'], function (RouteBuilder $routes) {
    $routes->connect('/login', ['controller' => 'Users', 'action' => 'login']);
});