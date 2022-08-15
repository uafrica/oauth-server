<?php

namespace OAuthServer\Test\Fixture;

/**
 * @inheritDoc
 */
class AccessTokenScopesFixture extends AbstractMigrationsTestFixture
{
    /**
     * @inheritDoc
     */
    public $import = [
        'connection' => 'test_migrations',
        'model'      => 'OAuthServer.AccessTokenScopes',
    ];
}