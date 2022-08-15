<?php

namespace OAuthServer\Test\Fixture;

/**
 * @inheritDoc
 */
class RefreshTokensFixture extends AbstractMigrationsTestFixture
{
    /**
     * @inheritDoc
     */
    public $import = [
        'connection' => 'test_migrations',
        'model'      => 'OAuthServer.RefreshTokens',
    ];
}