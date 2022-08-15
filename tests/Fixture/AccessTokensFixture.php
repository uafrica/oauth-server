<?php

namespace OAuthServer\Test\Fixture;

/**
 * @inheritDoc
 */
class AccessTokensFixture extends AbstractMigrationsTestFixture
{
    /**
     * @inheritDoc
     */
    public $import = [
        'connection' => 'test_migrations',
        'model'      => 'OAuthServer.AccessTokens',
    ];
}