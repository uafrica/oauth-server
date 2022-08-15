<?php

namespace OAuthServer\Test\Fixture;

/**
 * @inheritDoc
 */
class AuthCodeScopesFixture extends AbstractMigrationsTestFixture
{
    /**
     * @inheritDoc
     */
    public $import = [
        'connection' => 'test_migrations',
        'model'      => 'OAuthServer.AuthCodeScopes',
    ];
}