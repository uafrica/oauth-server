<?php

return [
    'OAuthServer' => [
        'indexMode'     => \OAuthServer\Lib\Enum\IndexMode::DISABLED,
        'refreshTokens' => true,
        'privateKey'    => [
            'path'     => 'file://' . __DIR__ . '/private.example.key',
            'password' => null,
        ],
        'publicKey'     => [
            'path' => 'file://' . __DIR__ . '/public.example.key',
        ],
        'encryptionKey' => 'lxZFUEsBCJ2Yb14IF2ygAHI5N4+ZAUXXaSeeJm6+twsUmIen', // See OAuthUtility shell or generate using base64_encode(random_bytes(32)),
        'ttl'           => [
            \OAuthServer\Lib\Enum\Token::REFRESH_TOKEN        => 'P1D',
            \OAuthServer\Lib\Enum\Token::AUTHENTICATION_TOKEN => 'P1D',
            \OAuthServer\Lib\Enum\Token::ACCESS_TOKEN         => 'P1D',
        ],
        // maps required OAuth 2.0 repositories to table locator alias strings, defaults available in enumeration object
        'repositories'  => [
            \OAuthServer\Lib\Enum\Repository::ACCESS_TOKEN => 'OAuthServer.AccessTokens',
        ],
        'grants'        => [
            \OAuthServer\Lib\Enum\GrantType::REFRESH_TOKEN,
            \OAuthServer\Lib\Enum\GrantType::CLIENT_CREDENTIALS,
            \OAuthServer\Lib\Enum\GrantType::AUTHORIZATION_CODE,
        ],
        'defaultScope'  => '',
    ],
];