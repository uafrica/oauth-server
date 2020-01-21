<?php

use Cake\Routing\RouteBuilder;
use Cake\Routing\Router;

Router::plugin('OAuthServer', ['path' => '/oauth'], static function (RouteBuilder $routes) {
    $routes->connect(
        '/',
        [
            'controller' => 'OAuth',
            'action' => 'oauth',
        ]
    );
    $routes->connect(
        '/authorize',
        [
            'controller' => 'OAuth',
            'action' => 'authorize',
        ]
    );
    $routes->connect(
        '/access_token',
        [
            'controller' => 'OAuth',
            'action' => 'accessToken',
        ],
        [
            '_ext' => ['json'],
        ]
    );

    $routes->connect(
        '/jwks',
        [
            'controller' => 'OpenidConfiguration',
            'action' => 'jwks',
        ],
        [
            '_ext' => ['json'],
        ]
    );
});

Router::connect('/.well-known/openid-configuration', [
    'plugin' => 'OAuthServer',
    'controller' => 'OpenidConfiguration',
    'action' => 'view',
]);
