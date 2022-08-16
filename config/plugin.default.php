<?php

return [
    'OAuthServer' => [
        'appController'         => 'Cake\Controller\Controller', // e.g. 'App\Controller\AppController',
        'indexRedirectDisabled' => false,
        'serviceDisabled'       => false, // all oauth endpoints except oauth/status will be HTTP 503'd when true
        'statusDisabled'        => false, // oauth/status endpoint will be HTTP 503'd when true
        'refreshTokensEnabled'  => true, // allows refresh tokens to be used set ttl using a different config below
        'privateKey'            => [
            'path'     => 'file://' . __DIR__ . '/private.example.key',
            'password' => null,
        ],
        'publicKey'             => [
            'path' => 'file://' . __DIR__ . '/public.example.key',
        ],
        'encryptionKey'         => 'lxZFUEsBCJ2Yb14IF2ygAHI5N4+ZAUXXaSeeJm6+twsUmIen', // see OAuthUtility shell or generate using base64_encode(random_bytes(32)),
        'ttl'                   => [
            \OAuthServer\Lib\Enum\Token::REFRESH_TOKEN        => 'P1D',
            \OAuthServer\Lib\Enum\Token::AUTHENTICATION_TOKEN => 'P1D',
            \OAuthServer\Lib\Enum\Token::ACCESS_TOKEN         => 'P1D',
        ],
        // maps required OAuth 2.0 repositories to table locator alias strings, defaults available in an enumeration object
        'repositories'          => [
            \OAuthServer\Lib\Enum\Repository::USER => 'Users',
        ],
        'grants'                => [
            \OAuthServer\Lib\Enum\GrantType::REFRESH_TOKEN,
            \OAuthServer\Lib\Enum\GrantType::CLIENT_CREDENTIALS,
            \OAuthServer\Lib\Enum\GrantType::AUTHORIZATION_CODE,
        ],
        'defaultScope'          => '',
    ],
];